<?php


 

            //echo "zacatek auth<br>";
            // if(isset($_GET['code']) and !is_null($_GET['code']))
            //if   (is_null($_GET['code']))
            if (!isset($_GET['code']) || (empty($_GET['code'])))
             { 
             //nemam ani code
            //   echo "nemam code<br>";
               $url1 = "https://www.strava.com/oauth/authorize?client_id=5316&response_type=code&redirect_uri=http://www.rojicek.cz/strava/auth.php";
              // $result = file_get_contents($url1, false, $context);
               //$result = file_get_contents($url1, false); //odskocim stejne pryc
               header('Location: '.$url1);
              // echo "get content<br>";
               die();                                     
             }
             $code =  $_GET['code'];
           //  echo "mam code: "  . $code . "<br>";            
             
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
				   //header('Location: '.$redirurl); //mam vsechno?
           header('Location: http://www.rojicek.cz/strava/index.php?ses='.$token);
           die(); 
			 } 	
        
         
  
  
  //echo "strava token: " . $_SESSION['stravatoken'];
  //redir na uvodni stranku
  //header('Location: http://www.rojicek.cz/strava/index.php');
//    die(); 
  
  
  //jen testy
  	//header('Location: https://www.strava.com/api/v3/athletes/227615/stats?id=1741883&access_token='.$_SESSION['stravatoken']);
//       die();      

?>

