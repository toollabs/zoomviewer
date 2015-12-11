<?php

if (array_key_exists('f', $_GET))
  $f = str_replace(' ', '_', ucfirst($_GET['f']));
else
  $f = "";

if( $f == "" )
{
  echo "Supply a filename!";
  exit;
}

// compute cache file names
$m = 'cache/' . md5($f);
$c = $m . '.jpg';
$p = $m . '.tif';
$t = $m . '.part.tif';

// first make sure we have the original image
if( !is_readable($c) )
{
  // either not cached before, or cached version too old
  ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');

  // url of the image (may need escaping!)
  $url = "http://commons.wikimedia.org/wiki/Special:Filepath/" . urlencode($f);

  // read first couple of byte to verify file type
  $image = file_get_contents( $url, false, null, 0, 100 );

  // need to detect JFIF magic
  if( substr($image,6,4) == 'JFIF' || substr($image,6,4) == 'Exif' || substr($image,6,9) == 'Photoshop')
  {
    umask( 022 );
    $in_handle = fopen( $url, "rb");
    if( !$in_handle )
    {
      // error: Cannot download image from commons
      exit;
    }

    $out_handle = fopen( $c, 'wb' );

    while( !feof( $in_handle ) ) 
    {
      $image = fread( $in_handle, 8192 );
      fwrite( $out_handle, $image );
    }

    fclose( $in_handle );
    fclose( $out_handle );
  }
  else
  {
    // error: Cannot download image from commons or wrong format
    exit;
  }
}

// now make sure we have a pyramidal tiled tif of it
if( !is_readable($p) )
{
  if( !is_readable($t) )
  {
    $cmd = '/usr/bin/vips im_vips2tiff '.$c.' '.$t.':jpeg:75,tile:256x256,pyramid';
    putenv ('TMPDIR=/data/project/zoomviewer/var/tmp');
    $output = shell_exec( $cmd.'  2>&1');
    rename( $t, $p );
  }
  else
  {
    // encountered cace condition. The script should wait for $t to vanish
    exit;
  }
}

// redirect to iipsrv with the cache file
header('Access-Control-Allow-Origin: *');
header('Location: https://tools.wmflabs.org/zoomviewer/iipsrv.fcgi/?iiif=' . $p . '/info.json');
?>
