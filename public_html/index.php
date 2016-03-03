<?php
// http://commons.wikimedia.org/w/thumb.php?w=48&f=Harding%20Icefield%201.jpg

if (array_key_exists('f', $_GET))
  $f = str_replace(' ', '_', ucfirst($_GET['f']));
else
  $f = "";

if ($f == "")
{
  echo "Supply a filename!";
  exit;
}

// download and vips scaling stages
if (array_key_exists('stage', $_GET))
  $stage = intval($_GET['stage']);
else
  $stage = 0;

// compute cache file names
$m = 'cache/' . md5($f);
$c = $m . '.jpg';
$p = $m . '.tif';
$t = $m . '.part.tif';

$output = '';
$req = 'index.php?f=' . urlencode($f);

// better not cache this!
header('Cache-control: no-cache,no-store,must-revalidate');

$fetch_file = false;
if (is_readable($c))
{
  // get cache modification time
  $ctime = strftime("%Y%m%d%H%M%S", filectime($c));

  // connect to database
  $ts_pw = posix_getpwuid(posix_getuid());
  $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
  $db = mysqli_connect("p:commonswiki.labsdb", $ts_mycnf['user'], $ts_mycnf['password'], "commonswiki_p");
  unset($ts_mycnf, $ts_pw);

  // get last upload date from database
  $sql = sprintf("SELECT img_timestamp FROM image WHERE img_name = '%s'", mysqli_real_escape_string($db, $f));
  $res = mysqli_query($db, $sql);

  if (mysqli_num_rows($res) == 1)
  {
    $row = mysqli_fetch_array($res);
    // we need to re-fetch the file if the last upload reported by the database is NEWER than the file we have cached
    $fetch_file =  $row['img_timestamp'] > $ctime;
  }
}
else
  $fetch_file = true;

// first make sure we have the original image
if ($fetch_file)
{
  // either not cached before, or cached version too old
  ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');

  if ($stage == 0)
  {
    $stage = 1;
    include('template_loaderframe.inc');
    exit;
  }

  // url of the image (may need escaping!)
  $url = "http://commons.wikimedia.org/wiki/Special:Filepath/" . urlencode($f);

  // read first couple of byte to verify file type
  $image = file_get_contents($url, false, null, 0, 100);

  // need to detect JFIF magic
  if (substr($image,6,4) == 'JFIF' || substr($image,6,4) == 'Exif' || substr($image,6,9) == 'Photoshop')
  {
    umask(022);
    $in_handle = fopen($url, "rb");
    if (!$in_handle)
    {
      // TODO: output error
      exit;
    }
    $out_handle = fopen($c, 'wb');

    while (!feof($in_handle)) 
    {
      $image = fread( $in_handle, 8192 );
      fwrite($out_handle, $image);
      echo ".<br/>";
    }
    fclose($in_handle);
    fclose($out_handle);

    include('template_done.inc');
    exit;
  }
  else
  {
    $stage = 3;
    include('template_done.inc');
    echo $image;
    exit;
  }
}

// now make sure we have a pyramidal tiled tif of it
if (!is_readable($p) || $ctime > strftime("%Y%m%d%H%M%S", filectime($p)))
{
  if (!is_readable($t) || $ctime > strftime("%Y%m%d%H%M%S", filectime($t)))
  {
    // VIPS command to build a pyramidal TIFF
    $cmd = '/usr/bin/vips im_vips2tiff '.$c.' '.$t.':jpeg:75,tile:256x256,pyramid';

    // show spinner
    if ($stage == 0)
    {
      $stage = 2;
      include('template_loaderframe.inc');
      exit;
    }

    // run VIPS command
    putenv ('TMPDIR=/data/project/zoomviewer/var/tmp');
    $output = shell_exec( $cmd.'  2>&1');
    rename($t, $p);
  }

  include('template_done.inc');
  exit;
}

//"working on better credit line :-)";
$credit = $f;

// output the requested viewer flash/JS
if(array_key_exists('flash', $_GET) && $_GET['flash'] == "no")
  include( 'template_javascript.inc' );
else
  include( 'template_flash.inc' );
?>

