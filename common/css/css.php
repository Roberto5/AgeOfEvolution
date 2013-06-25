<?php
if (! empty($_SERVER["HTTP_ACCEPT_ENCODING"]) &&
 strpos("gzip", $_SERVER["HTTP_ACCEPT_ENCODING"]) === NULL) {} else {
    ob_start("ob_gzhandler");
}
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: must-revalidate');
$expire_offset = 1814400; // set to a reaonable interval, say 3600 (1 hr)
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire_offset) . ' GMT');
function css_compress ($buffer)
{
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  '), '', 
    $buffer); // remove tabs, spaces, newlines, etc.
    $buffer = str_replace('{ ', '{', $buffer); // remove unnecessary spaces.
    $buffer = str_replace(' }', '}', $buffer);
    $buffer = str_replace('; ', ';', $buffer);
    $buffer = str_replace(', ', ',', $buffer);
    $buffer = str_replace(' {', '{', $buffer);
    $buffer = str_replace('} ', '}', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(' ,', ',', $buffer);
    $buffer = str_replace(' ;', ';', $buffer);
    return $buffer;
}

function dump_css_cache ($filename,$key=array())
{
    $cwd = getcwd() . DIRECTORY_SEPARATOR;
    $stat = stat($filename);
    $current_cache = $cwd . '.' . $filename . '.' . $stat['size'] . '-' .
     $stat['mtime'] . '.cache';
    // the cache exists - just dump it
    if (is_file($current_cache)) {
        $cache_contents = file_get_contents($current_cache);
    } else {
        // remove any old, lingering caches for this file
        $dead_files = glob($cwd . '.' . $filename . '.*.cache', 
        GLOB_NOESCAPE);
        if ($dead_files)
            foreach ($dead_files as $dead_file)
                @unlink($dead_file);
        if (! function_exists('file_put_contents')) {
            function file_put_contents ($filename, $contents)
            {
                $handle = fopen($filename, 'w');
                fwrite($handle, $contents);
                fclose($handle);
            }
        }
        $cache_contents = css_compress(file_get_contents($filename));
        @file_put_contents($current_cache, $cache_contents);
    }
    foreach ($key as $k => $v) {
    	$cache_contents=str_replace($k, $v, $cache_contents);
    }
    return array('text' => $cache_contents, 'mtime' => $stat['mtime']);
}

$css=array(
//		'jquery.contextmenu.css',
		'lightbox.css',
		'jquery-ui.css',
		'scroll.css',
		'style.css'
);
if ($_GET['l'] == 'g') {
    $css[] = 'game.css';
    if ((is_numeric($_GET['s'])) && ($_GET['s'] > 0) && ($_GET['s'] < 7)) {
        $css[] = "style_" . $_GET['s'] . ".css";
        $css[] = "jquery-ui-" . $_GET['s'] . ".css";
    } else {
        $css[] = "style_" . rand(1, 6) . ".css";
    }
} else {
    $css[] = 'home.css';
    $css[] = "jquery-ui-1.8.11.custom.css";
}
$key=array('NORMAL'=>'#e6e6e6',
		'HOVER'=>'#dadada',
		'ACTIVE'=>'#eee',
		'INPUT_TEXT'=>'#000',
		'INPUT_BG'=>'#fff',
		'BACKGROUND2'=>'#aaa',
		'BACKGROUND'=>'#eee',
		'COLOR'=>'#000',
		'BORDER'=>'#001'
);
$mtime = 0;
foreach ($css as $value) {
	$r = dump_css_cache($value,$key);
	$display .= $r['text'];
	if ($mtime < $r['mtime']) $mtime = $r['mtime'];
}
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
echo $display;
?>