<?php
session_start();
$server = !empty($_SESSION['server']) ? $_SESSION['server'] : 'production';
if (isset($_POST['change'])) {
  $server = ($server == 'production') ? 'stage' : 'production';
  $_SESSION['server'] = $server;
}

require_once 'key.php';
require_once 'nprbrowser.class.inc';
require_once 'krumo/class.krumo.php';

// TODO: preg match for NPR API story ID
// preg_match("/[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab]{1}[0-9a-f]{3}-[0-9a-f]{12}/", $_GET['id'])
if (!empty($_GET['id'])) {
  header("Location: view.php?id=" . $_GET['id']);
  die();
}

$programs = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3004), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $programs[$title] = $item->id;
}

$topics = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3002), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $topics[$title] = $item->id;
}

$blogs = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3013), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $blogs[$title] = $item->id;
}

$bios = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3007), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $bios[$title] = $item->id;
}

$genres = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3018), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $genres[$title] = $item->id;
}

$artists = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3009), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $artists[$title] = $item->id;
}

$columns = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3003), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $columns[$title] = $item->id;
}

$serieses = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('id' => 3006), 'list');
foreach($call->results as $item) {
  $title = $item->title->{'$text'};
  $serieses[$title] = $item->id;
}

$stations = [];
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
$call->pull(array('size' => 5000), 'v2/stations');
foreach($call->results as $item) {
  $title = $item->title;
  $stations[$title] = $item->org_id;
}
ksort($stations);

$params = array(
  'limit',
  'topic',
  'program',
  'blog',
  'bio',
  'genre',
  'artist',
  'column',
  'series',
  'station',
  'text',
  'id',
);

$new_params = array();
$redirect = FALSE;
foreach ($params as $param) {
  if (isset($_GET[$param]) && strlen((trim($_GET[$param]))) > 0) {
    $new_params[$param] = $_GET[$param];
  }
  elseif (isset($_GET[$param])) {
    $redirect = TRUE;
  }
}
if ($redirect && !empty($_GET)) {
  header("Location: index.php?" . http_build_query($new_params));
  die();  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>NPR Browser</title>
  <meta name="description" content="">
  <meta name="author" content="">



  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="/favicon2.ico" />

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>-->
  
  <link rel="stylesheet" href="css/chosen.css">
  
  <script src="js/jquery-2.1.4.min.js" type="text/javascript"></script>
  <script src="js/chosen.jquery.js" type="text/javascript"></script>
  
  <script type="text/javascript">
    $(document).ready(function(){
	  $(".chosen").chosen();
    });
  </script>  
  
<!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link rel="stylesheet" href="css/normalize.css">-->
  <!--<link rel="stylesheet" href="css/skeleton.css">-->
  <!--<link rel="stylesheet" href="css/style.css">-->
  
  <link rel="stylesheet" href="css/pure-min.css">
  
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="css/grids-responsive-min.css">
<!--<![endif]-->  
  
  <style>
    .pure-g > div {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .l-box {
        padding: 1em;
    }
    
    /*
When setting the primary font stack, apply it to the Pure grid units along
with `html`, `button`, `input`, `select`, and `textarea`. Pure Grids use
specific font stacks to ensure the greatest OS/browser compatibility.
*/
html, button, input, select, textarea,
.pure-g [class *= "pure-u"] {
    /* Set your content font stack here: */
    font-family: Arial, Helvetica, sans-serif;
}
  </style>

</head>

<body>
<div id="container" class="pure-g">
<div class="pure-u-1-1 l-box">
<?php

$page = (!empty($_GET['page'])) ? (int) $_GET['page'] : 1;
$program = (!empty($_GET['program']) && in_array($_GET['program'], $programs)) ? $_GET['program'] : '';
$topic = (!empty($_GET['topic']) && in_array($_GET['topic'], $topics)) ? $_GET['topic'] : '';
$blog = (!empty($_GET['blog']) && in_array($_GET['blog'], $blogs)) ? $_GET['blog'] : '';
$bio = (!empty($_GET['bio']) && in_array($_GET['bio'], $bios)) ? $_GET['bio'] : '';
$genre = (!empty($_GET['genre']) && in_array($_GET['genre'], $genres)) ? $_GET['genre'] : '';
$artist = (!empty($_GET['artist']) && in_array($_GET['artist'], $artists)) ? $_GET['artist'] : '';
$column = (!empty($_GET['column']) && in_array($_GET['column'], $columns)) ? $_GET['column'] : '';
$series = (!empty($_GET['series']) && in_array($_GET['series'], $serieses)) ? $_GET['series'] : '';
$station = (!empty($_GET['station']) && in_array($_GET['station'], $stations)) ? $_GET['station'] : '';

$text = (!empty($_GET['text'])) ? htmlspecialchars($_GET['text'], ENT_QUOTES, 'UTF-8') : '';

$options = array();
$options['numResults'] = 10;
$options['startNum'] = (10 * ($page-1)) + 1;
$options['program'] = (!empty($_GET['program']) && in_array($_GET['program'], $programs)) ? $_GET['program'] : NULL;
$options['topic'] = (!empty($_GET['topic']) && in_array($_GET['topic'], $topics)) ? $_GET['topic'] : NULL;
$options['blog'] = (!empty($_GET['blog']) && in_array($_GET['blog'], $blogs)) ? $_GET['blog'] : NULL;
$options['bio'] = (!empty($_GET['bio']) && in_array($_GET['bio'], $bios)) ? $_GET['bio'] : NULL;
$options['genre'] = (!empty($_GET['genre']) && in_array($_GET['genre'], $genres)) ? $_GET['genre'] : NULL;
$options['artist'] = (!empty($_GET['artist']) && in_array($_GET['artist'], $artists)) ? $_GET['artist'] : NULL;
$options['column'] = (!empty($_GET['column']) && in_array($_GET['column'], $columns)) ? $_GET['column'] : NULL;
$options['series'] = (!empty($_GET['series']) && in_array($_GET['series'], $serieses)) ? $_GET['series'] : NULL;
$options['station'] = (!empty($_GET['station']) && in_array($_GET['station'], $stations)) ? $_GET['station'] : NULL;
if (!empty($_GET['text'])) {
  $options['text'] = $_GET['text'];
}

?>

<div class="pure-g">
  <div class="pure-u-1-1 1-box">
  <form action="index.php" method="post">
  <input class="pure-button" type="submit" name='change' value="<?php print $server; ?>" />
  </form>
</div>


<div class="pure-u-1-1 1-box">

<form action="index.php" method="get" class="pure-form pure-form-stacked">

    <fieldset>
        <legend>NPR Query</legend>

<div class="pure-g">

  <div class="pure-u-1 pure-u-md-1-3">
    <select name="program" class="pure-u-23-24 chosen">
      <option value=''>Program:</option>
      <?php foreach($programs as $k => $v) {
        $selected = ($v == $program) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
  </div>  
    
  <div class="pure-u-1 pure-u-md-1-3">  
    <select name="topic" class="pure-u-23-24 chosen">
      <option value="">Topic:</option>
      <?php foreach($topics as $k => $v) {
        $selected = ($v == $topic) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
   </div>
   
    <div class="pure-u-1 pure-u-md-1-3"> 
    <select name="blog" class="pure-u-23-24 chosen">
      <option value="">Blog:</option>
      <?php foreach($blogs as $k => $v) {
        $selected = ($v == $blog) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
     <select name="bio" class="pure-u-23-24 chosen">
      <option value="">Bio:</option>
      <?php foreach($bios as $k => $v) {
        $selected = ($v == $bio) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
     <select name="genre" class="pure-u-23-24 chosen">
      <option value="">Genre:</option>
      <?php foreach($genres as $k => $v) {
        $selected = ($v == $genre) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
     <select name="artist" class="pure-u-23-24 chosen">
      <option value="">Artist:</option>
      <?php foreach($artists as $k => $v) {
        $selected = ($v == $artist) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
     <select name="column" class="pure-u-23-24 chosen">
      <option value="">Column:</option>
      <?php foreach($columns as $k => $v) {
        $selected = ($v == $column) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
    <select name="series" class="pure-u-23-24 chosen">
      <option value="">Series:</option>
      <?php foreach($serieses as $k => $v) {
        $selected = ($v == $series) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
    <select name="station" class="pure-u-23-24 chosen">
      <option value="">Station:</option>
      <?php foreach($stations as $k => $v) {
        $selected = ($v == $station) ? 'selected' : '';
        print "<option $selected value='$v'>$k</option>";
      } ?>
    </select>   
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3"> 
    <input name="text" type="text" placeholder="Seach text" value="" class="pure-u-23-24">
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3">
    <input name="id" type="text" placeholder="Enter an NPR ID" value="" class="pure-u-23-24 ">
    </div>
    
    </div>
    </fieldset>

    <input class="button-primary" type="submit" value="GO" />
</form>
</div>
</div>
<?php
$call = new NPRCall($creds[$server]['host'], $creds[$server]['key'], FALSE);
$call->pull($options);
print "<div class='row'>" . krumo($call->query) . "</div>";
?>
<div class="pure-u-1-1 1-box">
<table class="pure-table"><thead><tr><th>TITLE</th><th>ID</th><!--<th>VIEW</th><th>EDIT</th><th>DELETE</th>--><th>PUBLISHED</th></tr></thead><tbody>
<?php
if (!empty($call->results)) {
  foreach($call->results as $story) {
    $date = $story->pubDate->{'$text'};
    print "<tr><td><a href='view.php?server={$server}&id={$story->id}'>{$story->title->{'$text'}}</a></td><td>{$story->id}</td>";
    print "<td>$date</td>";
    print "</tr>";
  }
}
else {
  print "<tr><td colspan='4'>Sorry, no results for this query.</td></tr>";
}
?>
</tbody>
</table>
<?php
$pages = count($call->results);
if ($pages > 1): ?>
  <div id ="pager">
  <?php
  $nav_params = $new_params;
  unset($nav_params['page']);
  $base = '/index.php';
  $query = '?' . http_build_query($nav_params) . '&page=';
  if ($page > 1) print '<a class="pure-button" href="' . $base . '">First</a>';
  if ($page > 1) print '<a class="pure-button" href="' . $base . $query . (int) ($page-1) . '">←PREV</a>';
  if ($page < $pages) print '<a class="pure-button 1-box" href="' . $base. $query . (int) ($page+1) . '"> NEXT→</a>';
  if ($page != $pages) print '<a class="pure-button 1-box" href="' . $base. $query . $pages . '">Last</a>';
  ?>
  </div>
<?php endif; ?>


</div>
</div>
</div>
  </body>
</html>
