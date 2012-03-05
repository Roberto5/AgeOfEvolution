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
    sea : 'mare'
};
var ev = {
    focus : {},
    // init var
    revision : 67,
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
    // main function
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
	$
		.ajax({
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
				eval('cb=function(){' + data.html.close
					+ '();};');
			    if (data.html.y)
				h = data.html.y;
			    else
				h = 600;
			    if (data.html.x)
				w = data.html.x;
			    else
				w = 800;
			    data.wid = ev.windows({
				x : w,
				y : h
			    }, "center", data.html, data.html.mod, false, cb);
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
				$("#master").attr("src",
					src + data.update.master + '.png');
			    }
			    if (data.update.disable) {
				for ( var key in data.update.disable)
				    $("#" + key).attr("disabled",
					    data.update.disable[key]);
			    }
			    if (data.update.attr) {
				for ( var key in data.update.attr)
				    for ( var k in data.update.attr[key]) {
					v = data.update.attr[key][k];
					if ((k == 'src') || (k == 'href'))
					    v = path + v;
					if (k == 'title') {
					    $tip = $('#' + key).data('tooltip')
						    .getTip();
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
					$("#cv" + ev.focus.id + " #" + key)
						.show();
				    else
					$("#cv" + ev.focus.id + " #" + key)
						.hide();
				}
			    }
			    if (data.update.building) {
				// $('div.building.pos3 img')
				b = data.update.building;
				// $('div.building').addClass("empty");
				for (i=0; i < ev.totpos; i++) {
				    if (b[i]) {
					$(
						'#cv' + ev.focus.id
							+ ' div.building.pos'
							+ i).removeClass(
						"empty");
					$(
						'#cv' + ev.focus.id
							+ ' div.building.pos'
							+ i)
						.addClass(b[i].type);
					dat = $('.pos' + i).data('events');
					if (dat) {
					    if (!dat.contextmenu)
						$('.pos' + i).contextMenu(
							ev.menubuilding, {
							    theme : 'human'
							});
					}
					api = $(
						'#cv' + ev.focus.id
							+ ' div.building.pos'
							+ i + ' img').data(
						'tooltip');
					$tip = api ? api.getTip() : false;
					if ($tip)
					    $tip.text(b[i].title);
					else {
					    $(
						    '#cv'
							    + ev.focus.id
							    + ' div.building.pos'
							    + i + ' img').data(
						    'title', b[i].title);
					}
				    } else {
					$obj = $('#cv' + ev.focus.id
						+ ' div.building.pos' + i)
					$obj.removeClass();
					$obj.addClass('building empty pos' + i);
					api = $obj.data('tooltip');
					$tip = api ? api.getTip() : false;
					if ($tip)
					    $tip.text(ev.lang.buid);
					else {
					    $obj.data('title', ev.lang.buid);
					}
				    }
				}
				$("[title]:not(.context-menu div)").tooltip({
				    offset : [ -10, 0 ],
				    delay : 1,
				    predelay : 400
				}).dynamic({
				    bottom : {
					direction : 'down'
				    }
				});
			    }
			    ev_array = [ '', 'inAttack', 'outAttack',
				    'marketM', 'inReinf', 'outReinf', 'reinf' ];
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
				    $('#ev' + i).removeClass().addClass(
					    'icon ' + ev_array[i]);
				    str = $('#info' + i + ' div:eq(0)').text();
				    str = str.replace(/\d+/, e[i].n);
				    $('#info' + i + ' div:eq(0)').text(str);
				    $('#info' + i + ' div:eq(1)').html(
					    e[i].content);
				}
				// resetinit();
			    }
			    if (data.update.focus) {
				$('.village_list').css("font-weight", "normal");
				$('#v' + data.update.focus.id).css(
					"font-weight", "bold");
				ev.focus = data.update.focus;
			    }
			}
			if (data.javascript) {
			    /*
			     * $script=$(data.javascript);
			     * $("head").append($script);
			     */
			    var s = document.createElement("script");
			    s.type = "text/javascript";
			    s.text = data.javascript;
			    document.getElementsByTagName("head")[0]
				    .appendChild(s);
			}
			if (callback)
			    callback(data);
		    },
		    error : function(request, state, error) {
			ev.flagLoader = false;
			ev.loader();
			content = {
			    title : "ERROR",
			    text : '<span style="float: left; clear:both; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>'
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
			wid = ev.windows({
			    x : 300,
			    y : 200
			}, "center", content, true, true);
			$(
				'div[aria-labelledby="ui-dialog-title-windows'
					+ wid + '"]').removeClass(
				"ui-widget-content");
			$(
				'div[aria-labelledby="ui-dialog-title-windows'
					+ wid + '"]')
				.addClass("ui-state-error");
		    }
		});
    },
    iconstak : new Array(),
    windows : function(size, pos, content, mod, alerts, close) {
	button = null;
	if (alerts) {
	    if ((alerts.n - alerts.i) > 1) {
		button = {
		    next : function() {
			$(this).dialog("close");
			ev.showalert();
		    }
		};
		content.title += " (" + (alerts.i * 1 + 1) + "/" + alerts.n
			+ ")";
	    } else
		button = null;
	}
	if (content.id) {
	    $open = $('.ev-windows');
	    for (i = 0; i < $open.length; i++) {
		wid = $open.eq(i).attr('id');
		if (wid.match(/^cv/) && !ev.flagicon[wid]) {
		    temp = {
			icon : ev.togglewin,
			id : "minus" + wid
		    }
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
		}
		temp.icon();
	    }
	    $win.dialog("moveToTop");
	} else {
	    $("body").append(
		    '<div class="ev-windows ui-widget-content" id="' + id
			    + '" title="' + content.title + '"><p>'
			    + content.text + '</p></div>');
	    $("#" + id).dialog({
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
	    $(
		    'div[aria-labelledby="ui-dialog-title-' + id
			    + '"]  a[role="button"]')
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
	    $("[title]:not(.context-menu div)").tooltip({
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
	if (ev.flagicon[id]) {// amplio la finestra
	    $('div[aria-labelledby="ui-dialog-title-' + id + '"]').css("width",
		    ev.cache.width);
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
			}
			temp.icon();
		    }
		}
		ev.request(module + '/index/refresh?vid=' + id.substring(2),
			'post', {
			    ajax : 1
			});
	    }
	} else {// riduco ad icona
	    $("#" + id).dialog("option", "draggable", false);
	    ev.cache.width = $(
		    'div[aria-labelledby="ui-dialog-title-' + id + '"]').css(
		    "width");
	    $('div[aria-labelledby="ui-dialog-title-' + id + '"]').css("width",
		    200);
	    $('div[aria-labelledby="ui-dialog-title-' + id + '"]').css(
		    'height', 'auto');
	    $("#" + id).hide();
	    for (i = 1; ev.iconstak[i]; i++)
		;
	    if (i > 6)
		p = i % 6;
	    else
		p = i;
	    $("#" + id).dialog("option", "position",
		    [ 206 * (p - 1), 'bottom' ]);
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
    // controller input
    ctrlpass : function() {
	pass1 = $(".password").eq(0).val();
	pass2 = $(".password").eq(1).val();
	if ((pass1 != pass2) || (pass1.length <= 8)) {
	    $(".password").css("border", "2px solid red");
	} else
	    $(".password").css("border", "2px solid green");
    },
    ctrlemail : function(obj) {
	// @todo aggingere chiamata ajax
	if (obj.value
		.match(/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/gi))
	    $("#" + obj.id).css("border", "2px solid green");
	else {
	    $("#" + obj.id).css("border", "2px solid red");
	}
    },
    ctrlnum : function(obj) {
	if (obj.value == "0-")
	    obj.value = "-";
	if (obj.value != "-") {
	    obj.value = obj.value.replace("k", "000");
	    obj.value = obj.value.replace(/[^0-9\-]/gi, "");
	    str = obj.value;
	    if (str.indexOf("-", 1) > 0) {
		obj.value = obj.value.replace(/-$/gi, "");
	    }
	    if (obj.value == "")
		obj.value = 0;
	    obj.value = obj.value * 1;
	}
	if (obj.value == "NaN")
	    obj.value = 0;
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
    // village function
    flagName : true,
    changeNameVillage : function(vid) {
	if (this.flagName) {
	    this.flagName = false;
	    n = $("#nameVillage" + vid).text();
	    $("#nameVillage" + vid)
		    .html(
			    '<form style="display:inline;" id="nameform"><input id="nameInput" size="10" value="'
				    + n + '" /></form>');
	    $("#nameform").submit(
		    function() {
			n = $("#nameInput").val();
			$("#nameVillage" + vid).text(n);
			data = {
			    id : vid,
			    name : n,
			    ajax : 1
			};
			ev.request(module + "/profile/changenamevillage",
				"post", data, function(data) {
				    if (data.data == true)
					$(document).trigger("quest", 1);
				});
			ev.flagName = true;
		    });
	    /*
	     * $("#nameInput").keydown(function(e) { e.stopPropagation(); });
	     */
	}
    },
    // registrazione civiltà
    createciv : function() {
	ev.request(module + "/index/createciv", "post", {
	    server : module,
	    name : $("#name").val(),
	    agg : $("#agg").val(),
	    sector : $('input[name$="sector"]').val()
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
    // alerts function
    alertstak : null,
    alertindex : 0,
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
    // help function
    helpCache : new Array(),
    helpOpen : false,
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
} ,
// map object
map : {
	village : new Array(),
	size : [ 24, 18, 50 ],
	zoom : 0,
	centre : [ 0, 0 ],
	init : [],
	sizeHelper: [3600,2700],
	TtoY : function (t) {
	    return t/ev.map.size[2]+(ev.map.init[1])+ev.map.size[1];
	},
	YtoT :function (y) {
	    return (y - (ev.map.init[1]) - ev.map.size[1]) * ev.map.size[2];
	},
	LtoX : function (l){
	    return l/(-ev.map.size[2])+(ev.map.init[0])-ev.map.size[0];
	},
	XtoL :function (x) {
	    return (x - (ev.map.init[0]) + ev.map.size[0]) * (-ev.map.size[2]);
	},
	// ricarica la prossima mappa e sposta
	position : function(x, y) {
	    // 
	    if ((Math.abs(x) <= ev.max[0]) && (Math.abs(y) <= ev.max[1])) {
	    	if ((x!=ev.map.centre[0])||(y!=ev.map.centre[1])) {l = ev.map.XtoL(x);
	    		t = ev.map.YtoT(y);
	    		$('#map').css("left", l).css("top", t);
	    	}
	    	i=x-ev.map.init[0];
	    	j=y-ev.map.init[1];
	    	if ((Math.abs(j)>ev.map.size[1]-6)||(Math.abs(i)>ev.map.size[0]-6)) {
	    		/*$('#map img').attr('src',path+'/'+module+'/village/map/zoom/'+ev.map.zoom+'/x/'+x+'/y/'+y);
	    		$('#minimap').attr('src',path+'/'+module+'/village/map/zoom/'+ev.map.zoom+'/x/'+x+'/y/'+y);
	    		alert('preload');
	    		/*ev.flagLoader = true;
	    		setTimeout(function() {
	    		    ev.loader();
	    		}, 1000);
	    		//$(document).smartpreload({images:[path+'/'+module+'/village/map/zoom/'+ev.map.zoom+'/x/'+x+'/y/'+y],oneachimageload:function(src) {
	    			$('#map img').attr('src',src);*
	    			ev.map.init=[x,y];
	    			l=-ev.map.sizeHelper[0]/3;
	    			t=-ev.map.sizeHelper[1]/3;
	    			$('#map').css("left", l).css("top", t);
	    			/*ev.flagLoader=false;
	    			ev.loader();
	    		}});*/
	    	}
	    }//*/
	},
	//sposta la mappa 
	changetable : function(x, y) {
	    l = (x - (ev.map.init[0]) + ev.map.size[0]) * (-ev.map.size[2]);
	    t = (y - (ev.map.init[1]) - ev.map.size[1]) * ev.map.size[2];
	    $('#map').css("left", l).css("top", t);
	    ev.map.centre = [ x, y ];
	    location.hash = "#" + x + "|" + y;
	},
	hide_village_info : function() {
	    // ev.map.canhide=true;
	    location.hash = location.hash
		    .substring(0, location.hash.length - 1);
	},
	load_detail : function() {
	    text = location.hash;
	    i = text.indexOf("|");
	    x = text.substr(1, i - 1);
	    y = text.substr(i + 1);
	    ev.map.get_village_info(x + ":" + y);
	},
	arrow : function(direction) {
	    switch (direction) {
	    case 'up':
		x = ev.map.centre[0];
		y = ev.map.centre[1] * 1 + 1 * 1;
		break;
	    case 'down':
		x = ev.map.centre[0];
		y = ev.map.centre[1] * 1 - 1 * 1;
		break;
	    case 'right':
		x = ev.map.centre[0] * 1 + 1 * 1;
		y = ev.map.centre[1];
		break;
	    case 'left':
		x = ev.map.centre[0] * 1 - 1 * 1;
		y = ev.map.centre[1];
		break;
	    }
	    this.position(x, y);
	},
	get_village_info : function(coord) {
	    // ev.map.details(coord);
	    i = coord.indexOf('|');
	    x = coord.substr(0, i);
	    y = coord.substr(i + 1);
	    location.hash = "#" + x + "|" + y + "@";
	    try {
		village = ev.map.village[x][y]
	    } catch (e) {
		ev.map.position(x, y, function() {
		    ev.map.get_village_info(coord);
		});
		return;
	    }
	    if (ev.map.village[x][y].civ_id == ev.civ.civ_id) {
		ev.request(module + '/index/village/?vid='
			+ ev.map.village[x][y].id, "post", {
		    ajax : 1
		});
	    } else {
		text = '<div>' + ev.lang.village1
			+ ' <a class="civ" href="#civ'
			+ ev.map.village[x][y].civ_id
			+ '" onclick="ev.request(module+\'/profile/index/cid/'
			+ ev.map.village[x][y].civ_id
			+ '\',\'post\',{ajax:1});">'
			+ ev.map.village[x][y].civ_name + '</a><div>';
		text += '<div>' + ev.lang.village2
			+ ' <a class="ally" href="#ally'
			+ ev.map.village[x][y].civ_ally + '">'
			+ ev.map.village[x][y].ally + '</a></div>';
		text += '<div>' + ev.lang.village3 + ': '
			+ ev.map.village[x][y].busy_pop + '</div>';
		text += '<div>' + ev.lang.village4 + ': '
			+ ev.map.village[x][y].prod1_bonus + '% '
			+ ev.map.village[x][y].prod2_bonus + '% '
			+ ev.map.village[x][y].prod3_bonus + '%</div>';
		text += '<div><a href="#sendTroop'
			+ ev.map.village[x][y].id
			+ '" onclick="ev.request(\''
			+ module
			+ '/movements/send\', \'post\', {type:\'attack\',ajax:1,vid:'
			+ ev.map.village[x][y].id + '});">' + ev.lang.village5
			+ '</a></div>';
		ev.windows({
		    h : 300,
		    w : 400
		}, "center",
			{
			    title : ev.map.village[x][y].name + '(' + x + '|'
				    + y + ')',
			    'text' : text
			}, false, false, ev.map.hide_village_info);
	    }
	},
	details : function(coord) {
	    i = coord.indexOf('|');
	    x = coord.substr(0, i);
	    y = coord.substr(i + 1);
	    $("#village_name").html(
		    this.village[x][y].name + ' (' + x + '|' + y + ')');
	    $("#village_player").html(this.village[x][y].civ_name);
	    $("#village_ally").html(this.village[x][y].ally);
	    $("#village_bonus").html(
		    this.village[x][y].prod1_bonus + "% "
			    + this.village[x][y].prod2_bonus + "% "
			    + this.village[x][y].prod3_bonus + "%");
	    $("#map_details").show();
	},
	hide_map_details : function() {
	    // if (ev.map.canhide)
	    $("#map_details").hide();
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
		    var $list = $("ul", $('#' + id)).length ? $("ul", $('#'
			    + id)) : $("<ul class='troops ui-helper-reset'/>")
			    .appendTo($('#' + id));
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
	$(ogg).css(
		'background',
		'url(' + path + '/common/images/troops/t' + ogg.value
			+ '.gif) no-repeat');
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
	coord = $(this).attr('alt');
	i = coord.indexOf(':');
	x = coord.substr(0, i);
	y = coord.substr(i + 1);
	ev.request(module + "/movements/send", "post", {
	    type : "sup",
	    ajax : 1,
	    vid : ev.map.village[x][y].id
	});
    },
    atk : function(menuItem, menu) {
	coord = $(this).attr('alt');
	i = coord.indexOf(':');
	x = coord.substr(0, i);
	y = coord.substr(i + 1);
	ev.request(module + "/movements/send", "post", {
	    type : "attack",
	    ajax : 1,
	    vid : ev.map.village[x][y].id
	});
    },
    raid : function(menuItem, menu) {
	coord = $(this).attr('alt');
	i = coord.indexOf(':');
	x = coord.substr(0, i);
	y = coord.substr(i + 1);
	ev.request(module + "/movements/send", "post", {
	    type : "raid",
	    ajax : 1,
	    vid : ev.map.village[x][y].id
	});
    },
    marketMap : function() {
	coord = $(this).attr('alt');
	i = coord.indexOf(':');
	x = coord.substr(0, i);
	y = coord.substr(i + 1);
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
	    y : 600
	}, "centre", ev.bugcontent);
    },
    bugcontent : {
	title : "",
	text : ""
    },
    tag : new Array(),
    addtag : function(tag) {
	tag = tag.replace(" ", "");
	bool = false
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
	upgrade : function() {
	    c = $(this).attr("class");
	    m = c.match(/pos([\d]{1,2})/);
	    ev.request(module + "/building/upgrade/tokenB/" + ev.token.tokenB
		    + "/pos/" + m[1], "post", {
		ajax : "true"
	    });
	},
	destroy : function() {
	    c = $(this).attr("class");
	    m = c.match(/pos([\d]{1,2})/);
	    ev.request(module + "/building/destroy/tokenB/" + ev.token.tokenB
		    + "/pos/" + m[1], "post", {
		ajax : "true"
	    });
	},
	deleteQueue : function(eid) {
	    ev.request(module + "/index/delqueue/tokenB/" + ev.token.tokenB,
		    "post", {
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
    statserver:function(server) {
    	for(i=0;server[i];i++) {
    		this.request(server[i]+'/stats','post',{ajax:1},function(data){
    			$('#n_civ'+data.server).text(data.N_civ);
    			if (data.offline) $('#button'+data.server).addClass('offline');
    			$.ajax({url:path+'/'+data.server+'/processing',timeout:5000});
    		});
    	}
    }
};
