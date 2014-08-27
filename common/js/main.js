function initbutton() {
	// add button
	$("button:not(.img):not(.server):not(.close):not(.edit):not(.add):not(.delete),input[type=submit]:not(.edit):not(.add):not(.delete)").button();
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
    //$("button.img").button({text:false});
}
$(document).ready(function(){
		initbutton();
	    $("#loginForm").submit(function(e) {
		    ev.login();
		    e.preventDefault();
		    return false;
	    });
// windows
		$("#credits").dialog({autoOpen: false});
		$("#account").dialog({autoOpen: false,width:350});
		$("ul#news").liScroll();
		$("[title]:not(.context-menu div):not(.notooltip)").tooltip();
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
		var reportmenu={
				read:{
					name:'segna come letti',
					callback:function() {
						ev.request(module+"/report/read/all/1","post",{ajax:1});
					},
					icon:'ui-icon ui-icon-check'
				}
		};
		var messagemenu={
				read:{
					name:'segna come letti',
					callback:function() {
		       			ev.request(module+"/message/read/all/1","post",{ajax:1});
					},
					icon:'ui-icon ui-icon-check'
		       	}
		};
		$.contextMenu({
			selector:'[rel=link-Report]',
			items:reportmenu
		});
		$.contextMenu({
			selector:'[rel=link-MESSAGES]',
			items:messagemenu
		});
		$.contextMenu({
			selector:'[rel=link-Debug]',
			items:debugmenu
		});
		//@todo aggiungere timeout per rinfrescare il banner
		$('#hidad a').click(function() {$('#banner').hide();$('#hidad').prev().remove();});
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