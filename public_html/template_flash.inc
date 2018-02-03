<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/swfobject/2.2/swfobject.js"></script>
        	<style type="text/css">
        		html, body { background-color: #000; height: 100%; overflow: hidden; margin: 0; padding: 0; }
        		body { font-family: Helvetica, Arial, sans-serif; font-weight: bold; color: #ccc; }
        		#container { width: 100%; height: 100%; text-align: center; }
			#fallback { display: none; }
        	</style>
	</head>
	<body>
		<div id="container">
			<a id="fallback">Flash version not supported. Click here for HTML5 viewer.</a>
			<span id="nojs">ZoomViewer needs either JavaScript or Flash enabled.</span>
		</div>

		<script type="text/javascript" defer>
			var jsurl = document.location + '&flash=no';

			function callback(e)
			{
				if (!e.success) document.location = jsurl;
			}

			var fallback = document.getElementById('fallback');
			fallback.href = jsurl;
			fallback.style.display = 'block';
			document.getElementById('nojs').style.display = 'none';

			var server = "iipsrv.fcgi";
			var image = "<?= htmlspecialchars($p) ?>";
			var credit = "<?= htmlspecialchars($credit) ?>";
			var flashvars = {
				server: server,
				image: image,
				credit: credit
			}
			var params = {
				scale: "noscale",
				bgcolor: "#000000",
				allowfullscreen: "true",
				allowscriptaccess: "always"
			}
			var result = swfobject.embedSWF("IIPZoom.swf", "container", "100%", "100%", "9.0.0","expressInstall.swf", flashvars, params, {}, callback);
		</script>
	</body>
</html>

