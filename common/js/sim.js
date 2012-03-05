$(function() {
	var $troops = $("#troops");

	$("li", $troops).draggable({
		cancel : "a.ui-icon",
		revert : "invalid",
		helper : "clone",
		cursor : "move"
	});
	$("#atk li").draggable({
		cancel : "a.ui-icon",
		revert : "invalid",
		cursor : "move"
	});
	$("#def li").draggable({
		cancel : "a.ui-icon",
		revert : "invalid",
		cursor : "move"
	});

	$("#atk").droppable({
		accept : "#troops > li",
		activeClass : "ui-state-highlight",
		drop : function(event, ui) {
			checkin(ui.draggable, '#' + event.target.id);
		}
	});

	$("#def").droppable({
		accept : "#troops > li",
		activeClass : "ui-state-highlight",
		drop : function(event, ui) {
			checkin(ui.draggable, '#' + event.target.id);
		}
	});

	$troops.droppable({
		accept : "#atk li, #def li",
		activeClass : "custom-state-active",
		drop : function(event, ui) {
			checkout(ui.draggable);
		}
	});

	function checkin($item, $team) {
		id=$item.find('input.id_troop').val();
		var $list = $("ul", $($team)).length ? $("ul", $($team))
				: $("<ul class='troops ui-helper-reset'/>")
						.appendTo($($team));
		li=$($team+' ul>li');
		bool=true;
		if (li.length) {
			for(i=0;(i<li.length)&&bool;i++) {
				lid=li.eq(i).find('input.id_troop').val();
				if (lid==id) bool=false;
			}
		}
		if (bool) {
		//@TODO volendo si  può sostituire con un dialog
		nr=parseInt(prompt("inserire il numero di truppe",0));
		
		
		
		$clone=$item.clone();
		$('<input type="hidden" class="input_troops" name="' +$team.substr(1)+id + '" value="'+nr+'" />').appendTo($clone);
		$clone.find('h5').text($item.find('h5').text()+"("+nr+")");
		$clone.draggable({
			cancel : "a.ui-icon",
			revert : "invalid",
			
			cursor : "move"
		});
		$clone.appendTo($list);
		}
	}

	function checkout($item) {
		$item.remove();
	}
});

function addtroops(id, nr, ogg, gruppo) {
	ogg.find('#header-nr').html('(' + nr + ')');
	ogg.find('input.nr_troops').val(nr);
	ogg.find('input.gruppo').val(gruppo);
	switch (gruppo) {
	case '#atk':
		if (parseInt($('input[name=a' + id + ']').val()) == 0) {
			$('input[name=atk' + id + ']').val(nr);
		} else {
			$('input[name=atk' + id + ']')
					.val(
							parseInt($('input[name=atk' + id + ']').val())
									+ parseInt(nr));
		}
		break;
	case '#def':
		if (parseInt($('input[name=def' + id + ']').val()) == 0) {
			$('input[name=def' + id + ']').val(nr);
		} else {
			$('input[name=def' + id + ']')
					.val(
							parseInt($('input[name=def' + id + ']').val())
									+ parseInt(nr));
		}
		break;
	default:
		alert('Errore, non sappiamo in quale gruppo mettere le truppe');
		break;
	}
}

function deltroops(id, nr, gruppo) {
	switch (gruppo) {
	case '#atk':
		if (parseInt($('input[name=atk' + id + ']').val()) <= nr) {
			$('input[name=atk' + id + ']').val(0);
		} else {
			$('input[name=atk' + id + ']')
					.val(
							parseInt($('input[name=atk' + id + ']').val())
									- parseInt(nr));
		}
		break;
	case '#def':
		if (parseInt($('input[name=def' + id + ']').val()) <= nr) {
			$('input[name=def' + id + ']').val(0);
		} else {
			$('input[name=def' + id + ']')
					.val(
							parseInt($('input[name=def' + id + ']').val())
									- parseInt(nr));
		}
		break;
	default:
		alert('Errore, non sappiamo in quale gruppo togliere le truppe');
		break;
	}
}