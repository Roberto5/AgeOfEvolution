function timeStampToString(time)
{
    time=parseInt(time);
    h="0";min="0";sec="0";
    if (time<60) sec=time;
    if ((time<3600)&&(time>60)) {
        min=parseInt(time/60);
        sec=time-min*60;
    }
    if (time>3600) {
        h=parseInt(time/3600);
        time2=time-h*3600;
        if (time2>60) {
            min=parseInt(time2/60);
            sec=time2-min*60;
        }
        else sec=time2;
    }
    if (h<10) h="0"+h;
    if (min<10) min="0"+min;
    if (sec<10) sec="0"+sec;
    return h+":"+min+":"+sec;
}
function in_array (needle, haystack, argStrict) {
    // Checks if the given value exists in the array  
    // 
    // version: 1006.1915
    // discuss at: http://phpjs.org/functions/in_array    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: vlado houba
    // +   input by: Billy
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: true
    // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
    // *     returns 2: false
    // *     example 3: in_array(1, ['1', '2', '3']);
    // *     returns 3: true    // *     example 3: in_array(1, ['1', '2', '3'], false);
    // *     returns 3: true
    // *     example 4: in_array(1, ['1', '2', '3'], true);
    // *     returns 4: false
    var key = '', strict = !!argStrict; 
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {                return true;
            }
        }
    }
     return false;
}

function seltt(oggetto)
{
	$('input[type=checkbox]').attr('checked',oggetto.checked);
}
