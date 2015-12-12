<?php

if (array_key_exists('f', $_GET))
  $f = str_replace(' ', '_', ucfirst($_GET['f']));
else
{
  echo "Supply a filename!";
  exit;
}

// redirect to iipsrv with the cache file
header('Access-Control-Allow-Origin: *');
header('Location: https://tools.wmflabs.org/zoomviewer/proxy.php?iiif=' . $f . '/info.json');
?>
