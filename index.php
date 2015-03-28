<!DOCTYPE html>
<html> 
<head> 

<title>Eddington number</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="ed.css">



    </head>

<body>
<?php

$token =  $_GET['ses'];

//if (!isset($_GET['ses']) || (empty($_GET['ses'])))
if (is_null($token)) 
{
       echo "<p>To learn your <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> &nbsp; <a href=\"auth.php\"><img src=\"LogInWithStrava.png\" width=\"159\" height=\"31\" align=\"middle\" alt=\"Log in with Strava\"></a></p>";          
}   
 
 else
 {//login
//jsem prihlaseny, muzu nacist aktivity 
 

 //echo "main token: ". $_GET['ses'] . " <br>";
 
 $url = "https://www.strava.com/api/v3/athlete?access_token=" . $token;
 $result = file_get_contents($url);
 $athlete = json_decode ($result, true);

// echo $result;
// echo "<p>";
// echo "id: " . $athlete["id"];
// echo "<p>";
 
 //get rides
 
 $page = 1;
 
 while (true)
 {
 $url = "https://www.strava.com/api/v3/activities?per_page=200&page=".$page."&access_token=" . $token;
 $result = file_get_contents($url);
 $rides = json_decode ($result, true);
 
//$dnesek = strtotime(date("Y-m-d"));
$predrokem = strtotime(date("Y-m-d", strtotime(date("Y-m-d") . " - 1 year")));
 
 if (empty($rides))
 {
 // echo "<p>opoustim smycku<p>";
  break; //zadne dalsi jizdy
  }
  
  //$distances=array();
  $ix = 0;
  $ixLife = 0;
 foreach ($rides as $oneride)
  {
     
       $rideDate =  strtotime($oneride["start_date_local"]);
       
       //last year
       if (($rideDate >= $predrokem )   && (strtolower($oneride["type"]) == 'ride'))
       {
        $distances[$ix] =  $oneride["distance"];        
        $ix = $ix + 1;
      } 
      
      //all time
      if ((strtolower($oneride["type"]) == 'ride'))
       {                 
        $distancesLife[$ixLife] =  $oneride["distance"];        
        $ixLife = $ixLife + 1;
      } 
      
  }
  
  $page=$page + 1;
}
 
  $pocetJizd = $ix;
  $pocetJizdLife = $ixLife;
  
  $edn = 0;
  $ednLife = 0;
  
 
 $razeni = sort($distances);
 $razeniLife = sort($distancesLife);
 
 //  echo  "##########################################################<br>";
   for ($ix = 0; $ix <$pocetJizd; $ix++)
 {
      $distances[$ix] = floor ($distances[$ix] / 1000);
      $stejnychAdelsich = ($pocetJizd - $ix);
      
      if ($stejnychAdelsich>=$distances[$ix])
         $edn = $stejnychAdelsich;
        else
         break;      
 }

//echo "pocet jizd life:" . $pocetJizdLife . "<br>"; 
 //life time

    for ($ixLife = 0; $ixLife <$pocetJizdLife; $ixLife++)
 {
      $distancesLife[$ixLife] = floor ($distancesLife[$ixLife] / 1000);
      $stejnychAdelsichLife = ($pocetJizdLife - $ixLife);
      
     // echo "ride #". $ixLife . " left=" .   $stejnychAdelsichLife . " delka=" . $distancesLife[$ixLife] . "<br>" ;
      
      if ($stejnychAdelsichLife>=$distancesLife[$ixLife])
         $ednLife = $stejnychAdelsichLife;
        else
         break;      
         
 }
 
 
 echo  "Your last year metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times in last year as of today.</i><p>"  ;

 echo  "Your lifetime metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednLife . "</b>. <br>";
 echo "<i>That means you rode " . $ednLife . "km or more at least " . $ednLife . " times since your started recording on Strava.</i><p>"  ;
 
    //  echo "<p>ASI JE TO BLBE :)<p>"  ;
  //echo "lonske datum " . $predrokem;
  //echo "<p>";
  // echo "konec";
   }//konec if autorizace
?>
          </body>
