<?php

// Séparateur de dossier
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$s = "\\";
} else {
	$s = "/";
}

if (!isset($_GET['m']))
	error(1);

if (!isset($_POST['html']) || !isset($_POST['css']) || !isset($_POST['js']))
	error(3);

if ($_POST['html'] == "" && $_POST['css'] == "" & $_POST['js'] == "")
	error("Your work is empty!");

switch ($_GET['m']) {
	case "create":
		
		$app_id = rand_str(8);
		$subpath = "files$s".substr($app_id, 0, 1).$s.substr($app_id, 1, 1).$s;
		$path = $subpath.substr($app_id, 2).".0";
		if (!is_dir($subpath)) {
			mkdir($subpath, 0777, true);
		}
		file_put_contents("$path.html", $_POST['html']);
		file_put_contents("$path.css", $_POST['css']);
		file_put_contents("$path.js", $_POST['js']);
		print $app_id;
		exit;
		
	break;
	case "update":
	
		if (!isset($_POST['app']) || !ctype_alnum($_POST['app']))
			error(5);
		
		// Récupère la dernier version
		$last_version = 0;
		$path = "files$s".substr($_POST['app'], 0, 1).$s.substr($_POST['app'], 1, 1).$s.substr($_POST['app'], 2).".";
		while (file_exists($path.strval($last_version).".html")) {
			$last_version++;
		}
		
		// Si non trouvé
		if ($last_version == 0)
			error(4);
		
		// Sinon on enregistre
		file_put_contents("$path$last_version.html", $_POST['html']);
		file_put_contents("$path$last_version.css", $_POST['css']);
		file_put_contents("$path$last_version.js", $_POST['js']);
		print $last_version;
		exit;
		
	break;
	default:
		error(2);
}

function error($code) {
	print "error: $code";
	exit;
}

// Retourne une chaine de caractère au hasard
function rand_str($length = 16, $chars = '0123456789abcdef') {
	$chars_length = (strlen($chars) - 1);
	$string = $chars{rand(0, $chars_length)};
	for ($i = 1; $i < $length; $i = strlen($string)) {
		$r = $chars{rand(0, $chars_length)};
		if ($r != $string{$i - 1}) $string .=  $r;
	}
	return $string;
}