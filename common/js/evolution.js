/**
 * stringhe di traduzione translation string
 */
var ita = {
	village1 : 'occupato da',
	village2 : 'alleato con',
	village3 : 'popolazione',
	village4 : 'bonus',
	village5 : 'invia truppe',
	village6 : 'invia mercanti',
	build : 'costruisci',
	sea : 'mare',
	valley : 'valle neutrale',
	select : 'Seleziona'
};
var ev = {
	focus : {},
	// init var
	revision : REVISION,
	lang : ita,
	classbuilding : [],
	max : [ 200, 200 ],
	age : 0,
	civ : {},
	totpos : 0,
	debug : true,
	wid : 0,
	flagLoader : false,
	flagicon : new Array(),
	cache : new Array(),
	token : new Array(),
	// village function
	flagName : true,
	// alerts function
	alertstak : null,
	alertindex : 0,
	// help function
	helpCache : new Array(),
	helpOpen : false,
	ondrag : false,
	// main function
	refresh :function () {
		this.request(module+'/index/refresh','post',{ajax:1});
	},
	request : function(target, Rtype, params, callback) {
		if (Rtype == "zend") {
			for ( var key in params)
				target += "/" + key + "/" + params[key];
			params = null;
		}
		this.flagLoader = true;
		setTimeout(function() {
			ev.loader();
		}, 1000);
		$.ajax({
			url : path + "/" + target,
			data : params,
			type : Rtype,
			dataType : "json",
			success : function(data, stato) {
				ev.flagLoader = false;
				ev.loader();
				// open windows
				if (data.html) {
					cb = null;
					if (data.html.close)
						eval('cb=function(){' + data.html.close + '();};');
					if (data.html.y)
						h = data.html.y;
					else
						h = 600;
					if (data.html.x)
						w = data.html.x;
					else
						w = 800;
					button = data.html.button ? {
						'ok' : function() {
							$(this).dialog('close');
						}
					} : null;
					data.wid = ev.windows({
						x : w,
						y : h
					}, "center", data.html, data.html.mod, false, cb, button);
				}
				// update
				if (data.update) {
					if (data.update.ids) {
						for ( var key in data.update.ids)
							$("#" + key).html(data.update.ids[key]);
						resetinit();
						resetres();
					}
					if (data.update.master) {
						src = $("#master").attr("src");
						// mastera_n.png
						src = src.substr(0, 8);
						$("#master").attr("src", src + data.update.master + '.png');
					}
					if (data.update.disable) {
						for ( var key in data.update.disable)
							$("#" + key).attr("disabled", data.update.disable[key]);
					}
					if (data.update.attr) {
						for ( var key in data.update.attr)
							for ( var k in data.update.attr[key]) {
								v = data.update.attr[key][k];
								if ((k == 'src') || (k == 'href'))
									v = path + v;
								if (k == 'title') {
									api = $('#' + key).data('tooltip');
									$tip = api ? api.getTip() : false;
									if ($tip)
										$tip.text(v);
									else {
										$('#' + key).data('title', v);
									}
								} else
									$('#' + key).attr(k, v);
							}
					}
					if (data.update.token) {
						for ( var key in data.update.token)
							ev.token[key] = data.update.token[key];
					}
					if (data.update.dispB) {
						for ( var key in data.update.dispB) {
							value = data.update.dispB[key];
							if (value)
								$("#cv" + ev.focus.id + " #" + key).show();
							else
								$("#cv" + ev.focus.id + " #" + key).hide();
						}
					}
					if (data.update.building) {
						// $('div.building.pos3 img')
						b = data.update.building;
						// $('div.building').addClass("empty");
						for (i = 0; i < ev.totpos; i++) {
							if (b[i]) {
								place = $('#cv' + ev.focus.id + ' div.building.pos' + i);
								place.attr("class","building pos"+i+" "+b[i].type);
								dat = place.data('events');
								/*if ((dat) && (!dat.contextmenu)) {
									place.contextMenu(ev.menubuilding, {
										theme : 'human'
									});
								} else
									place.contextMenu(ev.menubuilding, {
										theme : 'human'
									});*/
								api = place.data('tooltip');
								$tip = api ? api.getTip() : false;
								if ($tip)
									$tip.text(b[i].title);
								else {
									place.attr('title', b[i].title);
								}
							} else {
								$obj = $('#cv' + ev.focus.id + ' div.building.pos' + i);
								$obj.removeClass();
								$obj.addClass('building empty pos' + i);
								api = $obj.data('tooltip');
								$tip = api ? api.getTip() : false;
								if (!$tip && !api)
									$obj.attr('title', ev.lang.build);
									
								else {
									api.show();api.hide();
									api.getTip().text(ev.lang.build);
								}
							}
						}
						$("[title]:not(.context-menu div):not(.notooltip)").tooltip({
							offset : [ -10, 0 ],
							delay : 1,
							predelay : 400
						}).dynamic({
							bottom : {
								direction : 'down'
							}
						});
						$('.village_view div:not(.empty):not(.pos0):not(.pos1):not(.pos2):not(.pos3)').each(function(){
							try {
								$(this).droppable('destroy');
							}catch(e)
							{
								
							}
						});
					}
					ev_array = [ '', 'inAttack', 'outAttack', 'marketM', 'inReinf', 'outReinf', 'reinf' ];
					$('#evpan img').removeClass().addClass('icon');
					$('#ev1').addClass('attackbn');
					$('#ev2').addClass('attackbn');
					$('#ev3').addClass('marketMbn');
					$('#ev4').addClass('rinfobn');
					$('#ev5').addClass('rinfobn');
					$('#ev6').addClass('rinfobn');
					for (i = 1; i <= 6; i++) {
						str = $('#info' + i + ' div:eq(0)').text();
						str = str.replace(/\d+/, '0');
						$('#info' + i + ' div:eq(0)').text(str);
						$('#info' + i + ' div:eq(1)').html(' ');
					}
					if (data.update.event) {
						e = data.update.event;
						for ( var i in e) {
							$('#ev' + i).removeClass().addClass('icon ' + ev_array[i]);
							str = $('#info' + i + ' div:eq(0)').text();
							str = str.replace(/\d+/, e[i].n);
							$('#info' + i + ' div:eq(0)').text(str);
							$('#info' + i + ' div:eq(1)').html(e[i].content);
						}
						// resetinit();
					}
					if (data.update.focus) {
						$('.village_list').css("font-weight", "normal");
						$('#v' + data.update.focus.id).css("font-weight", "bold");
						ev.focus = data.update.focus;
					}
				}
				if (data.javascript) {
					/*
					 * $script=$(data.javascript); $("head").append($script);
					 */
					var s = document.createElement("script");
					s.type = "text/javascript";
					s.text = data.javascript;
					document.getElementsByTagName("head")[0].appendChild(s);
				}
				if (callback)
					callback(data, this);
			},
			error : function(request, state, error) {
				ev.flagLoader = false;
				ev.loader();
				content = {
					title : "ERROR",
					text : '<span style="float: left; clear:both; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>',
					error : true
				};
				switch (request.status) {
				case 500:
					content.text += "internal error";
					break;
				case 404:
					content.text += "page not found";
					break;
				default:
					content.text = error;
				}
				content.text = '<div>' + content.text + '<div>';
				wid = ev.windows({
					x : 300,
					y : 'auto'
				}, "center", content, true, true);
			}
		});
	},
	iconstak : new Array(),
	windows : function(size, pos, content, mod, alerts, close, button) {
		if (typeof (content.text) != 'string')
			content.text = content.text.toString();
		var i;
		if (alerts) {
			if ((alerts.n - alerts.i) > 1) {
				button = {
					next : function() {
						$(this).dialog("close");
						ev.showalert();
					}
				};
				content.title += " (" + (alerts.i * 1 + 1) + "/" + alerts.n + ")";
			}
		}
		if (content.id) {
			$open = $('.ev-windows');
			for (i = 0; i < $open.length; i++) {
				wid = $open.eq(i).attr('id');
				if (wid.match(/^cv/) && !ev.flagicon[wid]) {
					temp = {
						icon : ev.togglewin,
						id : "minus" + wid
					};
					temp.icon();
				}
			}
			id = "cv" + content.id;
		} else {
			this.wid++;
			id = 'windows' + this.wid;
		}
		content.text = content.text.replace("{wid}", this.wid);
		$win = $('#' + id);
		if ($win.length) {
			$win.html(content.text);
			if (ev.flagicon[id]) {
				temp = {
					icon : ev.togglewin,
					id : "minus" + id
				};
				temp.icon();
			}
			$win.dialog("moveToTop");
		} else {
			d_class = "";
			if (content.error) {
				d_class += " ui-state-error";
			}
			$("body").append(
					'<div class="ev-windows ui-widget-content" id="' + id + '" title="' + content.title + '"><p>' + content.text + '</p></div>');

			$("#" + id).dialog({
				dialogClass : d_class,
				stack : ".ev-windows",
				height : size.y,
				width : size.x,
				resizable : false,
				position : pos,
				close : function(event, ui) {
					aid = content.aid;
					if (aid > 0)
						ev.request("alert/read", "post", {
							id : aid
						});
					id = this.id;
					ev[content.title + "open"] = false;
					if (ev.flagicon[id])
						ev.iconstak[ev.flagicon[id]] = false;
					if (close)
						close();
					if (!content.notdestroy)
						$(this).remove();
				},
				/*
				 * focus : function (event , ui) { id=this.id; if
				 * (id.match(/^cv/))
				 * ev.request(module+'/index/refresh?vid='+id.substring(2 ) ,
				 * 'post',{ajax:1}); },
				 */
				modal : mod,
				buttons : button
			});
			$container = $('#' + id).parent();
			if (content.error) {
				$container.find('div:eq(0)').addClass('ui-state-error').prepend('<span class="ui-icon ui-icon-alert" style="float:left;"></span> ');
				$container.find('div.ui-dialog-buttonpane').removeClass('ui-widget-content');
			}
			$container
					.find('a[role="button"]')
					.before(
							'<a href="#" class="ui-corner-all" id="minus'
									+ id
									+ '" style="float:right;margin-top: 2px; margin-right: 10px;"><span class="ui-icon ui-icon-minusthick">minimize</span></a>');
			$("#minus" + id).mouseover(function() {
				id = this.id.substr(5);
				$("#" + this.id).addClass("ui-state-hover");
			});
			$("#minus" + id).mouseout(function() {
				$("#" + this.id).removeClass("ui-state-hover");
			});
			$("#minus" + id).click(ev.togglewin);
			$("[title]:not(.context-menu div):not(.notooltip)").tooltip({
				offset : [ -10, 0 ],
				delay : 1,
				predelay : 400
			}).dynamic({
				bottom : {
					direction : 'down'
				}
			});
			ev.flagicon[id] = false;
			this[content.title + "open"] = true;
		}
		return this.wid;
	},
	togglewin : function() {
		id = this.id.substr(5);
		if (ev.cache.width == undefined) {
			ev.cache.width = [];
		}
		if (ev.flagicon[id]) {// amplio la finestra
			$('#' + id).parent().css("width", ev.cache.width[id]);
			$("#" + id).dialog("option", "draggable", true);
			$("#" + id).show();
			$("#" + id).dialog("option", "position", 'center');
			ev.iconstak[ev.flagicon[id]] = false;
			ev.flagicon[id] = false;
			if (id.match(/^cv/)) {
				$open = $('.ev-windows:not(#' + id + ')');
				for (i = 0; i < $open.length; i++) {
					wid = $open.eq(i).attr('id');
					if (wid.match(/^cv/) && !ev.flagicon[wid]) {
						temp = {
							icon : ev.togglewin,
							id : "minus" + wid
						};
						temp.icon();
					}
				}
				ev.request(module + '/index/refresh?vid=' + id.substring(2), 'post', {
					ajax : 1
				});
			}
		} else {// riduco ad icona
			$("#" + id).dialog("option", "draggable", false);
			$w = $('#' + id);
			$win = $w.parent();
			ev.cache.width[id] = $win.css("width");
			$win.css("width", 200);
			$win.css('height', 'auto');
			$w.hide();
			for (i = 1; ev.iconstak[i]; i++)
				;
			if (i > 6)
				p = i % 6;
			else
				p = i;
			$w.dialog("option", "position", [ 206 * (p - 1), 'bottom' ]);
			ev.iconstak[i] = true;
			ev.flagicon[id] = i;
		}
	},
	loader : function() {
		if (ev.flagLoader) {
			$("#load").show();
		} else {
			$("#load").hide();
		}
	},
	parsedata : function(data) {
		for ( var key in data) {
			if (typeof (data[key]) == "string")
				this[key] = jQuery.parseJSON(data[key]);
			else {
				for ( var k in data[key])
					this[key][k] = jQuery.parseJSON(data[key][k]);
			}
		}
	},
	login : function() {
		form = $('#loginForm');
		var data = {
			username : form.find('#username').val(),
			password : form.find('#password').val()
		};
		ev.request('login', 'post', data/*
										 * , function(rep) { // if (rep.type==1)
										 * ;//ev.user=rep.data.user; }
										 */);
		return false;
	},
	logout : function() {
		ev.request('login/logout', 'post', null, function() {
			location.reload();
		});
	},
	// registrazione civiltà
	village : {
		template : '',
		init : function() {
			$.ajax({
				url : path + '/' + module + '/village',
				success : function(data) {
					ev.village.template = data;
				}
			});
		},
		changeName : function(vid) {
			if (ev.flagName) {
				ev.flagName = false;
				n = $("#nameVillage" + vid).text();
				$("#nameVillage" + vid).html(
						'<form style="display:inline;" id="nameform"><input id="nameInput" size="10" value="' + n + '" /></form>');
				$("#nameform").submit(function() {
					n = $("#nameInput").val();
					$("#nameVillage" + vid).text(n);
					data = {
						id : vid,
						name : n,
						ajax : 1
					};
					ev.request(module + "/profile/changenamevillage", "post", data, function(data) {
						if (data.data == true)
							$(document).trigger("quest", 1);
					});
					ev.flagName = true;
				});
			}
		},
		open : function(vid) {
			c = ev.map.getCoordFromId(vid);
			ev.map.centre = [ c.x, c.y ];
			ev.map.shift();
			content = {};
			content.text = this.template.replace(/\{vid\}/gi, vid);
			content.title = ev.map.village[vid].name;
			content.text = content.text.replace(/\{name\}/gi, content.title);
			ev.focus.id = vid;
			ev.windows({
				x : 1000,
				y : 740
			}, 'centre', content);
			$.contextMenu({
				items:ev.menubuilding,
				selector:'.building:not(.empty)',
				theme : 'human'
			});
			$("div.drag").draggable({
				revert : "invalid",
				helper : "clone",
				cursor : "move"
			});
			$("div.empty").droppable({
				accept : ".drag",
				hoverClass: "draghover",
				drop : function(event, ui) {
					$item = ui.draggable;
					c = $item.attr("class");
					$item.fadeOut(function() {
						$(event.target).removeClass("empty");
						$(event.target).addClass(c);
						$('img', event.target).attr('title', $('img', $item).attr('title'));
					});
					// $item.appendTo($('#pannel'));
					m = $(event.target).attr("class").match(/pos(\d+)/);
					t = $item.find(".Btype").val();
					ev.request(module + '/building/build/type/' + t + '/pos/' + m[1] /*+ '/tokenB/' + ev.token.tokenB*/, 'post', {
						ajax : 1
					});
				}
			});
			$('#nameVillage'+vid).dblclick(function(){ev.village.changeName(vid);});
			ev.request('s1/village/focus', 'post', {'vid':vid,ajax:1});
		}
	},
	createciv : function() {
		ev.request(module + "/index/createciv", "post", {
			server : module,
			name : $("#name").val(),
			agg : $("#agg").val(),
			sector : $('input[name="sector"]:checked').val(),
			cx : $('#cx').val(),
			cy : $('#cy').val()
		});
	},
	subscrive : function(cid) {
		ev.request(module + "/index/subscrive", "post", {
			id : cid
		});
	},
	SearchCiv : function(p) {
		civ_name = $("#civ_name").val();
		s = $("#start").val();
		ev.request(module + "/index/searchciv", "post", {
			name : civ_name,
			start : s,
			page : p
		});
	},
	showalert : function() {
		if (ev.alertstak[ev.alertindex]) {
			content = ev.alertstak[ev.alertindex];
			al = {
				n : ev.alertstak.length,
				i : ev.alertindex
			};
			ev.windows({
				x : 700,
				y : 600
			}, "center", content, true, al);
			ev.alertindex++;
		}
	},
	help : function(page) {
		if (this.helpCache[page]) {
			if (page != "index")
				$("#help").html(this.helpCache[page]);
			else
				$("#windows" + this.helpCache[page]).dialog("open");
		} else {
			if ((!this.helpOpen) || (page != "index")) {
				this.request("help/" + page + "/age/" + ev.age, "post", {
					ajax : 1
				}, function(data) {
					if (page != "index")
						ev.helpCache[page] = data.update.ids.help;
					else
						ev.helpCache[page] = data.wid;
				});
			}
		}
	},
	// map object
	map : {
		pos : {
			top : 0,
			left : 0
		},
		prev : {
			top : 0,
			left : 0
		},
		village : new Array(),
		size : [ 24, 18, 50 ],
		move : false,
		timeout : 300,
		zoom : 0,
		centre : [ 0, 0 ],
		focus : {},
		data : [],
		limit : [],
		esclude : [ 0, 1, 2, 3, 4, 24, 25, 26, 27, 28, 48, 49, 50, 51, 52 ],
		goRender : 0,
		init : function() {
			$.ajax({
				url : path + "/common/images/map/" + module + ".json",
				dataType : "json",
				success : function(data) {
					ev.map.data = data.layers[0].data;
					ev.map.goRender++;
					ev.map.render();
				}
			});
			$.ajax({
				url : path + "/" + module + "/map",
				dataType : "json",
				success : function(data) {
					ev.map.village = data;
					ev.map.goRender++;
					ev.map.render();
				}
			});
		},
		render : function() {
			if (this.goRender > 1) {
				this.shift();
				this.goRender = 0;
			}
		},
		getCoordFromId : function(id) {
			id = parseInt(id);
			x = id % ev.max[0] - parseInt(ev.max[0] / 2);
			y = parseInt(ev.max[1] / 2) - parseInt(id / ev.max[0]);
			return {
				'x' : x,
				'y' : y
			};
		},
		getIdFromCoord : function(x, y) {
			id = ev.max[0] * (parseInt(ev.max[1] / 2) - y) + x * 1 + parseInt(ev.max[0] / 2);
			return id;
		},
		hide_village_info : function() {
			// ev.map.canhide=true;
			location.hash = location.hash.substring(0, location.hash.length - 1);
		},
		load_detail : function() {
			text = location.hash;
			i = text.indexOf("|");
			x = text.substr(1, i - 1);
			y = text.substr(i + 1);
			ev.map.get_village_info(x + ":" + y);
		},
		arrow : function(direction) {
			if ((direction == "continue") && this.move) {
				direction = ev.map.move;
			} else {
				ev.map.timeout = 300;
			}

			switch (direction) {
			case 'up':
				ev.map.centre[1]++;
				this.move = direction;
				break;
			case 'down':
				ev.map.centre[1]--;
				this.move = direction;
				break;
			case 'right':
				ev.map.centre[0]++;
				this.move = direction;
				break;
			case 'left':
				ev.map.centre[0]--;
				this.move = direction;
				break;
			case 'stop':
				ev.map.move = false;
				break;
			}

			if (this.move) {
				setTimeout(function() {
					ev.map.arrow('continue');
				}, ev.map.timeout);
				if (ev.map.timeout > 50)
					ev.map.timeout -= 50;
				ev.map.shift();
			}

		},
		get_village_info : function(i, j,coords) {

			if (ev.ondrag) {
				ev.ondrag = false;
				return false;
			}
			if (coords) {
				x=i;y=j;
			}
			else {
				c = this.getCoord(i, j);
				x = c.x;
				y = c.y;
			}
			
			id = this.getIdFromCoord(x, y);
			location.hash = "#" + x + "|" + y + "@";
			if (ev.map.village[id]) {
				cid = ev.map.village[id].civ_id;
				civ_name = ev.map.village[id].civ_name;
				civ_ally = ev.map.village[id].civ_ally;
				ally = ev.map.village[id].ally;
				busy_pop = ev.map.village[id].busy_pop;
				prod1_bonus = ev.map.village[id].prod1_bonus;
				prod2_bonus = ev.map.village[id].prod2_bonus;
				prod3_bonus = ev.map.village[id].prod3_bonus;
				name = ev.map.village[id].name;
			} else {
				cid = 0;
				civ_name = '-';
				civ_ally = '-';
				ally = '-';
				busy_pop = 0;
				prod1_bonus = '-';
				prod2_bonus = '-';
				prod3_bonus = '-';
				name = ev.lang.valley;
			}
			if ((this.village[id]) && (this.village[id].civ_id == ev.civ.civ_id)) {
				ev.village.open(id);
			} else {
				text = '<div>' + ev.lang.village1 + ' <a class="civ" href="#civ' + cid + '" onclick="ev.request(module+\'/profile/index/cid/' + cid
						+ '\',\'post\',{ajax:1});">' + civ_name + '</a><div>';
				text += '<div>' + ev.lang.village2 + ' <a class="ally" href="#ally' + civ_ally + '">' + ally + '</a></div>';
				text += '<div>' + ev.lang.village3 + ': ' + busy_pop + '</div>';
				text += '<div>' + ev.lang.village4 + ': ' + prod1_bonus + '% ' + prod2_bonus + '% ' + prod3_bonus + '%</div>';
				if (ev.civ.civ_id) {// @todo add more option
					text += '<div><a href="#sendTroop' + id + '" onclick="ev.request(\'' + module
							+ '/movements/send\', \'post\', {type:\'attack\',ajax:1,vid:' + id + '});">' + ev.lang.village5 + '</a></div>';
				} else {
					if (!ev.map.village[id])
						text += '<div><button onclick="$(\'input[name=sector]:eq(5)\').attr(\'checked\',true);$(\'#cx\').val(ev.map.focus.x);$(\'#cy\').val(ev.map.focus.y);$(\'#modalwindows\').dialog(\'open\')">'
								+ ev.lang.select + '</button></div>';
				}
				ev.windows({
					h : 300,
					w : 400
				}, "center", {
					title : name + '(' + x + '|' + y + ')',
					'text' : text
				}, false, false, ev.map.hide_village_info);
				this.focus = c;
			}

		},
		getCoord : function(i, j) {
			l = this.centre[0] - Math.round(this.size[0] / 2);
			t = this.centre[1] - Math.round(this.size[1] / 2);
			return {
				x : l + i,
				y : t - j - 2 + parseInt(this.size[1])
			};
		},
		details : function(i, j, n) {
			c = this.getCoord(i, j);
			x = c.x;
			y = c.y;
			id = this.getIdFromCoord(x, y);
			if (this.village[id]) {
				$("#village_name").html(this.village[id].name + ' (' + x + '|' + y + ')');
				$("#village_player").html(this.village[id].civ_name);
				$("#village_ally").html(this.village[id].ally);
				$("#village_bonus").html(
						this.village[id].prod1_bonus + "% " + this.village[id].prod2_bonus + "% " + this.village[id].prod3_bonus + "%");

			} else {
				$("#village_name").html(ev.lang['valley'] + ' (' + x + '|' + y + ')');
				$("#village_player").html("");
				$("#village_ally").html('');
				$("#village_bonus").html('');
			}
			bool = false;
			// console.log(n);
			for ( var k in this.esclude) {
				if (n == this.esclude[k]) {
					bool = true;
					break;
				}
			}
			if (bool)
				$("#map_details").css('top', '200px').show();
			else
				$("#map_details").css('top', '0').show();
		},
		hide_map_details : function() {
			// if (ev.map.canhide)
			$("#map_details").hide();
		},
		shift : function() {// area 56
			t = new Date();
			before = t.getTime();
			map = $('.map');
			for ( var j = 0, n = 0; j < (ev.map.size[1]); j++) {
				for ( var i = 0; i < ev.map.size[0]; i++, n++) {
					c = ev.map.getCoord(i, j);
					id = ev.map.getIdFromCoord(c.x, c.y);
					prev_class = map.eq(n).attr('class');
					zoom = prev_class.match(/zoom-\d+/g);
					if ((Math.abs(c.x) < (ev.max[0] / 2)) && (Math.abs(c.y) < (ev.max[1] / 2))) {
						map.eq(n).attr('class', 'map ' + zoom + ' area-' + (ev.map.data[id] - 1));
						village = map.eq(n).children();
						village.attr('class', zoom);
						own = village.children();
						own.attr('class', zoom);
						if (this.village[id]) {
							village.addClass('area-26');
							if (ev.civ.civ_id == this.village[id].civ_id)
								own.addClass('area-23');
							else {
								// @todo add ally or enemy own
								own.addClass('map-null');
							}
						} else {
							village.addClass('map-null');
							own.addClass('map-null');
						}
					} else
						// @todo creare una mappa sferica create a spheric map
						map.eq(n).attr('class', 'map ' + zoom + ' area-56');
					// own area-23
				}
			}
			location.hash = this.centre[0] + '|' + this.centre[1];
			t = new Date();
			console.log('map shift exsecution time: ', (t.getTime() - before), " ms ");
		}
	},
	troops : {
		speed : 0,
		init : function(id) {
			var $troops = $("#troops");
			$("li", $troops).draggable({
				cancel : "input",
				revert : "invalid",
				helper : "clone",
				cursor : "move"
			});
			$("#" + id).droppable({
				accept : "#troops > li.ui-widget-content",
				activeClass : "ui-state-highlight",
				drop : function(event, ui) {
					checkin(ui.draggable);
				}
			});
			$troops.droppable({
				accept : "#" + id + " li.ui-widget-content",
				activeClass : "custom-state-active",
				drop : function(event, ui) {
					checkout(ui.draggable);
				}
			});
			function checkin($item) {
				$item.fadeOut(function() {
					var $list = $("ul", $('#' + id)).length ? $("ul", $('#' + id)) : $("<ul class='troops ui-helper-reset'/>").appendTo($('#' + id));
					ev.troops.addtroops($item.find('input.id_troop').val());
					$item.appendTo($list).fadeIn();
				});
			}
			function checkout($item) {
				$item.fadeOut(function() {
					$item.appendTo($troops).fadeIn();
					ev.troops.deltroops($item.find('input.id_troop').val());
				});
			}
		},
		insert_village : function(obj) {
			qobj = $(obj).find("option");
			value = qobj.eq(obj.selectedIndex).attr("title");
			if (value) {
				coords = value.split('|');
				$('input#x').val(coords[0]);
				$('input#y').val(coords[1]);
			} else {
				$('input#x').val(0);
				$('input#y').val(0);
			}
			this.getcoord();
		},
		addtroops : function(id) {
			$('input[name=t' + id + ']').show();
			tot = $('input[name=tot' + id + ']').val();
			$('input[name=t' + id + ']').val(tot);
			spd = $('input[name=vel_troop' + id + ']').val();
			if ((spd < this.speed) || (this.speed == 0))
				this.speed = spd;
			this.aggtime();
		},
		aggtime : function() {
			cx = $('input#x').val();
			cy = $('input#y').val();
			distx = (ev.focus.x - cx) * (ev.focus.x - cx);
			disty = (ev.focus.y - cy) * (ev.focus.y - cy);
			dist = Math.sqrt(distx * 1 + disty * 1);
			if (ev.troops.speed == 0)
				time = 0;
			else
				time = parseInt(dist / this.speed * 3600);
			$("#time").text(timeStampToString(time));
			t = time * 1000;
			$("#ETA").text(t);
		},
		deltroops : function(id) {
			$('input[name=t' + id + ']').val(0);
			$('input[name=t' + id + ']').hide();
			spd = $('input[name=vel_troop' + id + ']').val();
			if (spd == this.speed) {
				this.speed = 0;
				i = 0;
				while (i < totTroop) {
					if ($('input[name=t' + i + ']').val() > 0) {
						spd = $('input[name=vel_troop' + i + ']').val();
						if ((spd < this.speed) || (this.speed == 0))
							this.speed = spd;
					}
					i++;
				}
			}
			this.aggtime();
		},
		getcoord : function() {
			cx = $('input#x').val();
			cy = $('input#y').val();
			this.aggtime();
			ev.request(module + "/movements/gettime", "post", {
				x : cx,
				y : cy
			});
		},
		sendajax : function() {
			data = {
				ajax : 1,
				type : $('input[name=type]:checked').val(),
				x : $('input[name=x]').val(),
				y : $('input[name=y]').val(),
				round : $('input[name=round]').val(),
				tokenMov : $('input[name=tokenMov]').val()
			};
			$t = $('#atk input');
			for (i = 0; i < $t.length; i++) {
				if ($t.eq(i).attr("name"))
					data[$t.eq(i).attr("name")] = $t.eq(i).val();
			}
			ev.request(module + '/movements/dosend', 'post', data);
		}
	},
	quest : {
		flagquest : true,
		questcache : null,
		questn : 0,
		state : 0,
		questhandler : function(event, n) {
			if (n >= ev.quest.questn)
				ev.quest.showquest();
		},
		showquest : function() {
			ev.request(module + "/quest", "post", {
				n : ev.quest.questn,
				state : ev.quest.state
			}, function(data) {
				if (!data.data)
					ev.quest.showcache();
				else {
					ev.quest.questcache = data.html;
					ev.quest.questn = data.data.n;
					ev.quest.state = data.data.state;
				}
			});
		},
		showcache : function() {
			ev.windows({
				x : 800,
				y : 600
			}, "center", ev.quest.questcache, true, false, ev.quest.read);
		},
		read : function() {
			ev.request(module + "/quest/read", "post");
		}
	},
	choose_god : function(id) {
		if (confirm("Confermi questa scelta ?")) {
			ev.request(module + '/quest/changegod', 'post', {
				master : id
			});
		}
	},
	market : {
		sellres : function() {
			res = document.sell.res.value;
			rap = document.sell.rap.value;
			res2 = parseInt(res * rap);
			$("#view").text("ricavo : " + res2);
		},
		getTimeM : function(id) {
			ev.request(module + "/market/time", "post", {
				vid : id
			});
		}
	},
	changeimg : function(ogg) {
		$(ogg).css('background', 'url(' + path + '/common/images/troops/t' + ogg.value + '.gif) no-repeat');
	},
	colony : function(menuItem, menu) {
		coord = $(this).attr('alt');
		i = coord.indexOf(':');
		x = coord.substr(0, i);
		y = coord.substr(i + 1);
		ev.request(module + '/movements/colony', 'zend', {
			'id' : ev.map.village[x][y].id
		});
	},
	support : function(menuItem, menu) {
		coord = $(this).data('coords');
		x = coord.x;
		y = coord.y;
		ev.request(module + "/movements/send", "post", {
			type : "sup",
			ajax : 1,
			vid : ev.map.village[x][y].id
		});
	},
	atk : function(menuItem, menu) {
		coord = $(this).data('coords');
		x = coord.x;
		y = coord.y;
		ev.request(module + "/movements/send", "post", {
			type : "attack",
			ajax : 1,
			vid : ev.map.village[x][y].id
		});
	},
	raid : function(menuItem, menu) {
		coord = $(this).data('coords');
		x = coord.x;
		y = coord.y;
		ev.request(module + "/movements/send", "post", {
			type : "raid",
			ajax : 1,
			vid : ev.map.village[x][y].id
		});
	},
	marketMap : function() {
		coord = $(this).data('coords');
		x = coord.x;
		y = coord.y;
	},
	hideopt : function(value) {
		if (value == 3) {
			$('#opt2').show();
			$('#opt1').hide();
		} else {
			$('#opt1').show();
			$('#opt2').hide();
		}
	},
	disableinput : function(bool) {
		if (bool)
			$('.opt2').removeAttr("disabled");
		else
			$('.opt2').attr("disabled", true);
	},
	bugreport : function() {
		this.windows({
			x : 400,
			y : "auto"
		}, "centre", ev.bugcontent);
	},
	bugcontent : {
		title : "",
		text : ""
	},
	tag : new Array(),
	addtag : function(tag) {
		tag = tag.replace(" ", "");
		bool = false;
		for (i = 0; (i < this.tag.length) && (!bool); i++)
			if (tag == this.tag[i])
				bool = true;
		if (bool)
			ev.removetag(tag);
		else
			ev.tag.push(tag);
		$('#tag').val(' ');
		$('#itag').text(ev.tag.join(','));
	},
	removetag : function(tag) {
		tag = tag.replace(" ", "");
		t = new Array();
		for (i = 0; i < this.tag.length; i++)
			if (tag != this.tag[i])
				t.push(this.tag[i]);
		this.tag = t;
	},
	sendbug : function() {
		data = new Object;
		data.type = $('input[name="type"]:checked').val();
		data.category = $('#category').val();
		data.tag = ev.tag.join(',');
		data.description = $('#description').val();
		data.screen = $('#link').val();
		this.request('track', "post", data, function(data) {
			if (data.data != "") {
				$('#report').html(data.data);
				$('#report').addClass('ui-state-error');
				$('#report').removeClass('ui-state-highlight');
			} else {
				$('#report').html('succes!!');
				$('#report').removeClass('ui-state-error');
				$('#report').addClass('ui-state-highlight');
			}
		});
	},
	research : function(id, token) {
		this.request(module + '/research', "post", {
			type : id,
			tokenRe : this.token.tokenRe
		});
	},
	top : function(page) {
		ev.request(module + "/top/index/ref/1/page/" + page, "post", {
			ajax : 1
		});
	},
	build : {
		pop:0,
		flagpop:false,
		upgrade : function(obj) {
			c = obj.$trigger.attr("class");
			m = c.match(/pos([\d]{1,2})/);
			ev.request(module + "/building/upgrade/pos/" + m[1], "post", {
				ajax : "true"
			});
		},
		destroy : function(obj) {
			c = obj.$trigger.attr("class");
			m = c.match(/pos([\d]{1,2})/);
			ev.request(module + "/building/destroy/tokenB/" + ev.token.tokenB + "/pos/" + m[1], "post", {
				ajax : "true"
			});
		},
		deleteQueue : function(eid) {
			ev.request(module + "/index/delqueue/", "post", {
				id : eid,
				ajax : "true"
			});
		}
	},
	report : {
		page : 1,
		delreport : function() {
			$sel = $('#reportmodule input[type=checkbox]:checked');
			ids = new Array();
			for (i = 0; i < $sel.length; i++) {
				ids[i] = $sel.eq(i).val();
			}
			ev.request(module + '/report/delete', 'post', {
				ajax : 1,
				'ids' : ids
			});
			ev.report.report(ev.report.page);
			return false;
		},
		readreport : function() {
			$sel = $('#reportmodule input[type=checkbox]:checked');
			ids = new Array();
			for (i = 0; i < $sel.length; i++) {
				ids[i] = $sel.eq(i).val();
			}
			ev.request(module + '/report/read', 'post', {
				ajax : 1,
				'ids' : ids
			});
			ev.report.report(ev.report.page);
			return false;
		},
		report : function(page) {
			ev.report.page = page;
			ev.request(module + '/report/index/ref/1/page/' + page, 'post', {
				ajax : 1
			});
		}
	},
	message : {
		page : 1,
		delmess : function() {
			$sel = $('#messmodule input[type=checkbox]:checked');
			ids = new Array();
			for (i = 0; i < $sel.length; i++) {
				ids[i] = $sel.eq(i).val();
			}
			ev.request(module + '/message/delete', 'post', {
				ajax : 1,
				'ids' : ids
			});
			ev.message.mess(ev.message.page);
			return false;
		},
		readmess : function() {
			$sel = $('#messmodule input[type=checkbox]:checked');
			ids = new Array();
			for (i = 0; i < $sel.length; i++) {
				ids[i] = $sel.eq(i).val();
			}
			ev.request(module + '/message/read', 'post', {
				ajax : 1,
				'ids' : ids
			});
			ev.message.mess(ev.message.page);
			return false;
		},
		mess : function(page) {
			ev.message.page = page;
			ev.request(module + '/message/index/ref/1/page/' + page, 'post', {
				ajax : 1
			});
		},
		send : function(action) {
			ogg = $("#ogg").val();
			dest = $("#civ").val();
			text = $("#mess textarea").val();
			ev.request(action, 'post', {
				ajax : 1,
				destinatario : dest,
				oggetto : ogg,
				messaggio : text
			});
		}
	},
	statserver : function(server) {
		$('server').removeClass('offline');
		for (i = 0; server[i]; i++) {
			this.request(server[i] + '/stats', 'post', {
				ajax : 1
			}, function(reponse) {
				// $('#n_civ' + data.server).text(data.N_civ);
				if (reponse.data.offline)
					$('#button' + reponse.data.server).addClass('offline');

			});
			$.ajax({
				url : path + '/' + server[i] + '/processing',
				timeout : 5000
			});
		}
	}
};
