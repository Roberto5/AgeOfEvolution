/**
 * 
 */

var editor;

function editA(id)
{
	if (editor && !confirm("Confermi la chiusura dell'editor? Facendo così perderai tutte le modifiche!!")) return;
    element=document.getElementById( "reply"+id );
	if (!editor) {
    	if (id!="0")  {
        	element.value=$( "#risposta"+id ).html();
        	$( "#risposta"+id ).hide();
        	$( "#domanda"+id ).hide();
        	$("#question"+id).show();
        	$("#question"+id).val($( "#domanda"+id ).text());
    	}
    	else $("#question0").show();
		var config = {
				skin : 'office2003'
			};
		editor = CKEDITOR.replace( element, config );
	}else{
		if (id!='0') $( "#risposta"+id ).show();
		// Destroy the editor.
		editor.destroy();
		editor = null;
		$(element).hide();
	}
}

function removeEditor(editor1)
{
	if ( !editor )
		return;
	// Retrieve the editor contents. In an Ajax application, this data would be
	// sent to the server or used in any other way.
    id2=editor1.name.substr(5,1);
    testo2=editor.getData();
    title=$("#question"+id2).val()
    if (id2!='0') {
    	$( '#risposta'+id2 ).html(testo2);
    	$('#domanda'+id2).text(title);
	}
    $("#question"+id2).hide();
    $('#domanda'+id2).show();
	// Destroy the editor.
	editor.destroy();
	editor = null;
    $( "#"+editor1.name ).hide();
    $( '#risposta'+id2 ).show();
    if (id2!='0') action2="modquest"; else action2="addquest";
    ev.request("admin/faq/"+action2, "post", {ajax:"true",id :id2, reply: testo2,"question":title}, function (data) {if (data.data) location.reload();});
}
function del(id2)
{
    $("#reply"+id2).html("");
    $("#risposta"+id2).html("");
    $("#conenent"+id2).html("");
    ev.request("admin/faq/delquest", "post", {ajax:"1",id :id2});
}