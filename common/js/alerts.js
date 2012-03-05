var editor;

function editA(id)
{
	if (editor && !confirm("Confermi la chiusura dell'editor? Facendo così perderai tutte le modifiche!!")) return;
    element=document.getElementById( "alert"+id );
	if (!editor) {
    	if (id!="0")  {
        	element.value=$( "#testo"+id ).html();
        	$( "#testo"+id ).hide();
        	$( "#titolo"+id ).hide();
        	$("#title"+id).show();
        	$("#title"+id).val($( "#titolo"+id ).text());
    	}
    	else $("#title0").show();
		var config = {
				skin : 'office2003'
			};
		editor = CKEDITOR.replace( element, config );
	}else{
		if (id!='0') $( "#testo"+id ).show();
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
    title=$("#title"+id2).val()
    if (id2!='0') {
    	$( '#testo'+id2 ).html(testo2);
    	$('#titolo'+id2).text(title);
	}
    $("#title"+id2).hide();
    $('#titolo'+id2).show();
	// Destroy the editor.
	editor.destroy();
	editor = null;
    $( "#"+editor1.name ).hide();
    $( '#testo'+id2 ).show();
    if (id2!='0') action2="modalert"; else action2="addalert";
    ev.request("admin/alerts/"+action2, "post", {ajax:"true",id :id2, text: testo2,"title":title}, function (data) {if (data.data) location.reload();});
}
function del(id2)
{
    $("#alert"+id2).html("");
    $("#testo"+id2).html("");
    $("#conenent"+id2).html("");
    ev.request("admin/alerts/delalert", "post", {ajax:"1",id :id2});
}
