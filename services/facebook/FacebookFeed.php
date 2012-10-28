<?php 

   $app_id = "422551301126797";
   $app_secret = "74c2dcc8e4ac0a39151c505d11352e70";
   $my_url = "http://localhost/test.php";

   session_start();

   $code = $_REQUEST["code"];

  if(empty($code)) {
     $_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
     $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
       . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'] . "&scope=user_birthday,read_stream";

      
     echo("<script> top.location.href='" . $dialog_url . "'</script>");
   }
   if($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
     // state variable matches
     $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
       . "&client_secret=" . $app_secret . "&code=" . $code;

     $response = file_get_contents($token_url);
     $params = null;
     parse_str($response, $params);

     $_SESSION['access_token'] = $params['access_token'];

     $graph_url = "https://graph.facebook.com/me?access_token=" 
       . $params['access_token'];

     $user = json_decode(file_get_contents($graph_url));
     echo("Hello " . $user->name . " " . $user->id . " " . $user->birthday . " " . $user->read_stream) ;
     

     $url_test = "https://graph.facebook.com/me/home?access_token=" . $_SESSION['access_token'];
     $news_feed = json_decode(file_get_contents($url_test));
     print_r( $news_feed );
   }
   else {
     echo("The state does not match. You may be a victim of CSRF.");
   }

 ?>