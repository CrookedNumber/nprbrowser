<?php
session_start();
$server = !empty($_SESSION['server']) ? $_SESSION['server'] : 'production';
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>NPR BROWSER</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>-->
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
<div><a class="pure-button" href="/">HOME</a></div>
<?php

require_once 'key.php';
require_once 'nprbrowser.class.inc';
require_once 'krumo/class.krumo.php';

if (!empty($_GET['id'])) {
  $call = new NPRCall($creds[$server]['host'], $creds[$server]['key']);
  $options = array('id' => $_GET['id']);
  $call->pull($options);

  $story = $call->results[0];
  $title = htmlspecialchars($story->title->{'$text'}, ENT_QUOTES, 'UTF-8');

  print "<h2>$title</h2>";
  print "<h3>ID: {$story->id}</h3>";
  /*if ($doc->hasEnclosure()) {
    if ($doc->profile == 'image') {
      print "<a href=\"{$doc->getHREF()}\"><image width=\"300\" src=\"{$doc->getHREF()}\"></a>";
    }
    else if ($doc->profile == 'audio') {
      print "<audio controls src=\"{$doc->getHREF()}\"></audio>";
    }
    else {
      print "<{$doc->profile} src=\"{$doc->getHREF()}\"></{$doc->profile}>";
    }
  }*/
  print "<h4>NPRCall Object</h4>";
  krumo($call);
  
  print "<h4>Story Object</h4>";
  krumo($story);
}
?>
</div>
</div>
</body>
</html>