<?php

 session_start();
 $redirectUrl = "http://" . $_SERVER['SERVER_NAME']  . $_SERVER['REQUEST_URI'];
 //smaz vsechno za .php (nesmi byt jinde v retezci, ale to je asi v pohode))
$findme   = '.php';
$pos = strpos($redirectUrl, $findme);
$redirectUrlR = substr($redirectUrl, 0, $pos+4);  //4 = len(.php)

 echo   $redirectUrlR . "<br>";
 

            
            if (!isset($_GET['code']) || (empty($_GET['code'])))
             { 
             
               $url1 = "https://www.strava.com/oauth/authorize?client_id=5316&response_type=code&redirect_uri=" . $redirectUrlR;              
              // echo    $url1 . "<br>";
               header('Location: '.$url1);
            
               //die();                                     
             }
             $code =  $_GET['code'];
           
             
             //musim vymenit za token            
             $post_data = array(
				'client_id' => '5316',
				'client_secret' => '7df6c2d7fff268641275096646a5f37e82ce4d1f',
				'code' => $_GET['code']
			);
      
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($post_data),
				),
			);
      
      	$context  = stream_context_create($options);
        $result = file_get_contents('https://www.strava.com/oauth/token', false, $context);
        
        
        $token = json_decode($result)->access_token;
			  if($token != null)
        {				                                         				   
           $_SESSION['stravatoken'] =  $token;
            header('Location: index.php');
            die(); 
			 } 	
        
         
  
    

?>

