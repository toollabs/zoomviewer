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
$md5 = md5($f);
$m = 'cache/' . $md5;
$c = $m . '.jpg';
$p = $m . '.tif';

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
  $cmd = 'jsub -mem 2048m -l release=trusty -N ' . escapeshellarg('zoom_' . $md5) . ' -once ./multires.sh ' . escapeshellarg($md5) . ' ' . escapeshellarg(urlencode($f));
  shell_exec( $cmd.'  2>&1');
}

// now make sure we have a pyramidal tiled tif of it
if (!is_readable($p))
{
  include('template_pending.inc');
  exit;
}
else
{
  //"working on better credit line :-)";
  $credit = $f;

  // output the requested viewer flash/JS
  if(array_key_exists('flash', $_GET) && $_GET['flash'] == "no")
    include( 'template_javascript.inc' );
  else
    include( 'template_flash.inc' );
}

?>

