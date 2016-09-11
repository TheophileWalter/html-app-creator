<?php

// Séparateur de dossier
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$s = "\\";
} else {
	$s = "/";
}

function countlines($str) {
	return substr_count( $str, "\n" );
}

// Initial number of lines in file before script starts
$lines = 17;

// Get the datas
$def_html = "";
$def_css = "";
$def_js = "";
$app_version = "0";
$loaded = false;

if (isset($_GET['fullscreen']) && isset($_GET['app']) && ctype_alnum($_GET['app'])) {
	if (isset($_GET['version']) && ctype_digit($_GET['version']))
		$app_version = $_GET['version'];
	$path = "files$s".substr($_GET['app'], 0, 1).$s.substr($_GET['app'], 1, 1).$s.substr($_GET['app'], 2).".".$app_version;
	if (file_exists($path.".html")) {
		$def_html = file_get_contents($path.".html");
		$def_css = file_get_contents($path.".css");
		$def_js = file_get_contents($path.".js");
		$loaded = true;
	}
}

if (!$loaded) {
	$def_html = $_POST['html'];
	$def_css = $_POST['css'];
	$def_js = $_POST['js'];
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>HAC Render</title>
	<style>
<?php
print $def_css;
$lines += countlines($def_css)+countlines($def_html);
?>
</style>
</head>
<body>
<script type="text/javascript">
	function hacCkeckInIframe () { try { return window.self !== window.top; } catch (e) { return true; } } if (!hacCkeckInIframe()) { location.href = "index.php"; }
	(function(){ var hacOldLog = console.log; console.log = function (message) { parent.postMessage(message, "*"); hacOldLog.apply(console, arguments);};})();
	(function(){ var hacOldError = console.error; console.error = function (message) { parent.postMessage("__hac_error__:\n" + message, "*"); hacOldError.apply(console, arguments);};})();
	window.onerror = function(msg, url, linenumber) { parent.postMessage("__hac_error__:\n" + msg + '\nLine Number: ' + (parseInt(linenumber)-<?=$lines?>), "*"); return true;}
	console.clear = function () {parent.postMessage("__hac_system__:clear_console", "*");};
</script>
<?php
print $def_html;
?>
<script type="text/javascript">
<?php
print $def_js;
?>
</script>
</body>
</html>