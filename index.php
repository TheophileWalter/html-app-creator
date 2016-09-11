<?php

$fullscreen = false;
if (isset($_GET['fullscreen'])) {
	$fullscreen = true;
	$app_id = $_GET['app'];
	if (isset($_GET['version']) && ctype_digit($_GET['version']))
		$app_version = $_GET['version'];
	else
		$app_version = "0";
} else {

	$def_html = "";
	$def_css = "";
	$def_js = "";
	$app_id = "";
	$app_version = "0";

	if (isset($_GET['app']) && ctype_alnum($_GET['app'])) {
		if (isset($_GET['version']) && ctype_digit($_GET['version']))
			$app_version = $_GET['version'];
		$path = "files/".substr($_GET['app'], 0, 1)."/".substr($_GET['app'], 1, 1)."/".substr($_GET['app'], 2).".".$app_version;
		if (file_exists($path.".html")) {
			$def_html = htmlspecialchars(file_get_contents($path.".html"));
			$def_css = htmlspecialchars(file_get_contents($path.".css"));
			$def_js = htmlspecialchars(file_get_contents($path.".js"));
			$app_id = $_GET['app'];
		}
	}

}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>HTML App Creator - Walter.tw</title>
	<link rel="icon" href="/images/favn.ico" />
	<link rel="stylesheet" type="text/css" href="style-v2.css" />
</head>
<body>

	<div id="head">
		<h1 unselectable="on" onselectstart="return false;" ondragstart="return false;" <?=($fullscreen ? "style=\"cursor:pointer;\" onclick=\"javascript:location.href='/html-app-creator/';\"" : "")?>>HTML App Creator</h1>
	</div>
	
	<div id="controls">
<?php if (!$fullscreen) { ?>		<img src="img/run.svg" class="control-button right-space" alt="Run" title="Run" onclick="javascript:run();" />
		<img src="img/save.svg" class="control-button right-space" alt="Save" title="Save" onclick="javascript:save(false);" />
		<a href="/cloud" target="_blank"><img src="img/upload.svg" class="control-button right-space" alt="Upload File" title="Upload File" /></a>
		<img src="img/fullscreen.svg" class="control-button right-space" alt="Fullscreen" title="Fullscreen" onclick="javascript:save(true);" />
		<a href="about.txt" target="_blank"><img src="img/about.svg" class="control-button right-space" alt="About" title="About" /></a>
<?php } else { ?>		<img src="img/edit.svg" class="control-button" alt="Edit" title="Edit" onclick="javascript:location.href='?app=<?=htmlspecialchars($app_id)?>&amp;version=<?=htmlspecialchars($app_version)?>';" />
<?php } ?>
	</div>

<?php if (!$fullscreen) { ?>	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<pre id="html-editor"><?=$def_html?></pre>
			</td><td>
				<pre id="css-editor"><?=$def_css?></pre>
			</td>
		</tr><tr>
			<td>
				<pre id="js-editor"><?=$def_js?></pre>
				<pre id="js-console">JavaScript Console</pre>
			</td><td>
			<?php } ?>	<iframe src="render.php<?=($fullscreen ? "?fullscreen&amp;app=$app_id&amp;version=$app_version" : "")?>" id="render-iframe" name="render-iframe-name" border="0" <?=($fullscreen ? " style=\"width:100vw; height:94vh;\"" : "")?>></iframe>
<?php if (!$fullscreen) { ?>			</td>
		</tr>
	</table>
	
	<!-- Form to post datas -->
	<form method="post" action="render.php" target="render-iframe-name" class="no-display" id="render-form">
		<input type="hidden" class="no-display" name="html" id="html-input" value="" />
		<input type="hidden" class="no-display" name="css" id="css-input" value="" />
		<input type="hidden" class="no-display" name="js" id="js-input" value="" />
	</form>
	
	<script src="src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<?php } ?>
	<script type="text/javascript">
		function _0xDEAD () { try { return window.self !== window.top; } catch (e) { return true; } } if (_0xDEAD()) { document.body.style.display = "none"; history.go(-1); }
<?php if (!$fullscreen) { ?>	
	
		// Current application
		var currentAppId = "<?=$app_id?>";
		var htmlEditor = ace.edit("html-editor");
		htmlEditor.setTheme("ace/theme/chrome");
		htmlEditor.session.setMode("ace/mode/html");
		
		// Disable missing doctype warning for HTML
		var session = htmlEditor.getSession();
		session.on("changeAnnotation", function() {
			var annotations = session.getAnnotations()||[], i = len = annotations.length;
			while (i--) {
				if(/DOCTYPE|doctype/.test(annotations[i].text)) {
					annotations.splice(i, 1);
				}
			}
			if(len > annotations.length) {
				session.setAnnotations(annotations);
			}
		});
		
		// Placeholder
		function updateHTML() {
			var shouldShow = !htmlEditor.session.getValue().length;
			var node = htmlEditor.renderer.emptyMessageNode;
			if (!shouldShow && node) {
				htmlEditor.renderer.scroller.removeChild(htmlEditor.renderer.emptyMessageNode);
				htmlEditor.renderer.emptyMessageNode = null;
			} else if (shouldShow && !node) {
				node = htmlEditor.renderer.emptyMessageNode = document.createElement("div");
				node.textContent = "<!-- HTML -->"
				node.className = "ace_invisible ace_emptyMessage"
				node.style.padding = "0 9px"
				htmlEditor.renderer.scroller.appendChild(node);
			}
		}
		htmlEditor.on("input", updateHTML);
		setTimeout(updateHTML, 100);
		
		// CSS Editor
		var cssEditor = ace.edit("css-editor");
		cssEditor.setTheme("ace/theme/chrome");
		cssEditor.session.setMode("ace/mode/css");
		
		// Placeholder
		function updateCSS() {
			var shouldShow = !cssEditor.session.getValue().length;
			var node = cssEditor.renderer.emptyMessageNode;
			if (!shouldShow && node) {
				cssEditor.renderer.scroller.removeChild(cssEditor.renderer.emptyMessageNode);
				cssEditor.renderer.emptyMessageNode = null;
			} else if (shouldShow && !node) {
				node = cssEditor.renderer.emptyMessageNode = document.createElement("div");
				node.textContent = "/* CSS */"
				node.className = "ace_invisible ace_emptyMessage"
				node.style.padding = "0 9px"
				cssEditor.renderer.scroller.appendChild(node);
			}
		}
		cssEditor.on("input", updateCSS);
		setTimeout(updateCSS, 100);
		
		// JavaScript Editor
		var jsEditor = ace.edit("js-editor");
		jsEditor.setTheme("ace/theme/chrome");
		jsEditor.session.setMode("ace/mode/javascript");
		
		// Placeholder
		function updateJS() {
			var shouldShow = !jsEditor.session.getValue().length;
			var node = jsEditor.renderer.emptyMessageNode;
			if (!shouldShow && node) {
				jsEditor.renderer.scroller.removeChild(jsEditor.renderer.emptyMessageNode);
				jsEditor.renderer.emptyMessageNode = null;
			} else if (shouldShow && !node) {
				node = jsEditor.renderer.emptyMessageNode = document.createElement("div");
				node.textContent = "// JavaScript"
				node.className = "ace_invisible ace_emptyMessage"
				node.style.padding = "0 9px"
				jsEditor.renderer.scroller.appendChild(node);
			}
		}
		jsEditor.on("input", updateJS);
		setTimeout(updateJS, 100);
		
		// Create IE + others compatible event handler
		var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
		var eventer = window[eventMethod];
		var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

		// Listen to message from child window
		eventer(messageEvent,function(e) {
			var el = document.getElementById("js-console");
			if (e.data.toString().startsWith("__hac_system__:")) {
				switch (e.data.toString().substring(15)) {
					case "clear_console":
						el.innerHTML = "";
						break;
				}
			} else if (e.data.toString().startsWith("__hac_error__:")) {
				el.innerHTML += "<span style=\"color:white;background-color:#AA0000;\">" + e.data.toString().substring(14) + "</span>\n\n";
				el.scrollTop = el.scrollHeight;
			} else {
				el.innerHTML += e.data.toString() + "\n";
				el.scrollTop = el.scrollHeight;
			}
		},false);
		
		function run() {
			document.getElementById("html-input").value = htmlEditor.getValue();
			document.getElementById("css-input").value = cssEditor.getValue();
			document.getElementById("js-input").value = jsEditor.getValue();
			document.getElementById("render-form").submit();
			document.getElementById("js-console").innerHTML = "JavaScript Console\n\n";
		}
		
		function save(openFullsceen) {
			if (currentAppId == "") {
				// Create new app
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						if (!xhttp.responseText.startsWith("error:")) {
							currentAppId = xhttp.responseText;
							window.history.pushState("", "", "/html-app-creator/?app=" + currentAppId);
							if (openFullsceen) {
								fullScreen();
							}
						} else {
							alert("Error while saving your app!\n" + xhttp.responseText);
						}
					}
				};
				xhttp.open("POST", "save.php?m=create", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("html=" + encodeURIComponent(htmlEditor.getValue()) + "&css=" + encodeURIComponent(cssEditor.getValue()) + "&js=" + encodeURIComponent(jsEditor.getValue()));
			} else {
				// Update
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						if (!xhttp.responseText.startsWith("error:")) {
							window.history.pushState("", "", "/html-app-creator/?app=" + currentAppId + "&version=" + xhttp.responseText);
							if (openFullsceen) {
								fullScreen();
							}
						} else {
							alert("Error while saving your app!\n" + xhttp.responseText);
						}
					}
				};
				xhttp.open("POST", "save.php?m=update", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("html=" + encodeURIComponent(htmlEditor.getValue()) + "&css=" + encodeURIComponent(cssEditor.getValue()) + "&js=" + encodeURIComponent(jsEditor.getValue()) + "&app=" + encodeURIComponent(currentAppId));
			}
		}
		
		function fullScreen() {
			var win = window.open(location.href + '&fullscreen', '_blank');
			if (win) {
				//Browser has allowed it to be opened
				win.focus();
			} else {
				//Browser has blocked it
				alert('Please allow popups for this website');
			}
		}
		
		// Run the app
		run();
		
		// If the app does not exists
		if (currentAppId == "" && location.href.indexOf("?") != -1)
			window.history.pushState("", "", "/html-app-creator/");
		
<?php } ?>
	</script>
</body>
</html>