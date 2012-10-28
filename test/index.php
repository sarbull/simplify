<?php include "gapi.php"?>
<!doctype html>
<html>
<head>
<link rel='stylesheet' href='style.css' /></head>
<body>
<header><h1>Google+ Sample App</h1></header>
<div class="box">




<?php if(isset($personMarkup)): ?>
<div class="me"><?php print $personMarkup ?></div>
<?php endif ?>

<?php if(isset($activityMarkup)): ?>
<div class="activities">

<!-- Your Activities: --> <?php //print $activityMarkup; ?>

<?php
foreach ($activities['items'] as $item){

?>
<?php echo "<img src=\"" . $item['actor']['image']['url'] . "\">";?><br/>

<?php echo $item['actor']['displayName'];?> via Google+<br/>
<?php echo $item['object']['content'];?><br/><br/>

<?php
}

?>
</div>
<?php endif ?>

<?php
  if(isset($authUrl)) {
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
  } else {
   print "<a class='logout' href='?logout'>Logout</a>";
  }
?>

<?php // print_r($activities);?>
</div>


</body>
</html>
