$(document).ready(function(){
		// add button
		$("button:not(.server):not(.close):not(.edit):not(.add):not(.delete),input[type=submit]:not(.edit):not(.add):not(.delete)").button();
		$("button.edit").button({icons: {
	        primary: "ui-icon-wrench"
	    },
	    text: false
	    });
		$("button.close").button({icons: {
	        primary: "ui-icon-circle-close"
	    },
	    text: false
	    });
		$("button.add").button({icons: {
	        primary: "ui-icon-plus"
	    },
	    text: false
	    });
		$("button.delete").button({icons: {
	        	primary: "ui-icon-close"
	    	},
	    		text: false
	    });;
	    $("#loginForm").submit(function(e) {
		    ev.login();
		    e.preventDefault();
		    return false;
	    });
// windows
		$("#credits").dialog({autoOpen: false});
		$("#account").dialog({autoOpen: false,width:350});
		$("ul#news").liScroll();
		$("[title]:not(.context-menu div)").tooltip({
			   offset: [-10, 0],
			   delay:1,
			   predelay:400
			}).dynamic({ bottom: { direction: 'down'} });
		counter();
		$('input:submit,input:button,button:not(.img)').button();
		
		setTimeout(ev.showalert,3000);
		// *************** game
		$( "#sharer" ).accordion({
			active: false ,
			collapsible: true
		});
		$( "#troopers" ).accordion({
			active: false ,
			collapsible: true
		});
		$( "#vlist" ).sortable({
			stop:function(event,ui) {
				list=$(this).sortable("toArray");
				ev.request(module+'/index/sort','post',{ajax:1,'list':list});	
			}
		});
		var debugmenu=[
		       		{'riempi magazzini':{
		       				onclick:function(){
		       					ev.request(module+"/debug/fill","post",{ajax:1});
		       		    	}
		       			}
		       		},
		       		{'aggiorna produzione':{
		       				onclick:function (){
		       					ev.request(module+"/debug/aggprod","post",{ajax:1});
		       				}
		       			}
		       		},
		       		{'completa evento <?php echo $select;?> <button>OK</button>':function(menuItem,cmenu,e) {
		       				$t=$(e.target);
		       				if ($t.is('button')) {
		       					$event=$(menuItem).find('select');
		       					ev.request(module+"/debug/complete","post",{ajax:1,ev:$event.val(),vid:'this'})
		       		            return true;
		       		        }
		       		        else return false;
		       			}
		       		},
		       		{'aggiungi 100 abitanti':{
		       				onclick:function (){
		       					ev.request(module+"/debug/addpop","post",{ajax:1});
		       				}
		       			}
		       		}
		];
		var reportmenu=[{'segna come letti':{
				onclick:function () {
					ev.request(module+"/report/read/all/1","post",{ajax:1});
				}
			}
		}];
		var messagemenu=[{'segna come letti':{
		           	onclick:function () {
		       			ev.request(module+"/message/read/all/1","post",{ajax:1});
		       		}
		       	}
		       	}];
		$('[rel=link-Report]').contextMenu(reportmenu,{theme:'human'});
		$('[rel=link-MESSAGES]').contextMenu(messagemenu,{theme:'human'});
		$('[rel=link-Debug]').contextMenu(debugmenu,{theme:'human'});
});
function togleraid(value)
{
    if (value == 2) {
        $('#raid_round').show();
    }
    else {
        $('#raid_round').hide();
    }
}