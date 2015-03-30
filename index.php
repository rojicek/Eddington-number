<!DOCTYPE html>
<html> 
<head> 

<title>Eddington number</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="ed.css">



    </head>

<body>
<?php

session_start();
$mileCoef = 1.609344;
$token =  $_SESSION['stravatoken'];
//echo "token: " .    $token . "<br>";

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
        $distancesStatute[$ix] = $distances[$ix] / $mileCoef;     
        $ix = $ix + 1;
      } 
      
      //all time
      if ((strtolower($oneride["type"]) == 'ride'))
       {                 
        $distancesLife[$ixLife] =  $oneride["distance"];
        $distancesLifeStatute[$ixLife] = $distancesLife[$ixLife] / $mileCoef;        
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
 $razeniStatute = sort($distancesStatute);
 
 
 $razeniLife = sort($distancesLife);
 $razeniLifeStatute = sort($distancesLifeStatute);
 
 
 //debug
 //testovani algoritmu na rucnich datech
 /*
 $distances = array (1000,2000,4458,5548,10555,11523,12545,44545,23454,33414,23447,22556,1556,12458,2545,52454,7542,13545,4545,85245,12254,8876,13547, 44857, 55887, 44587);
 $pocetJizd = sizeof ($distances);
 $razeni = sort($distances);
 */
 /*
 for ($ix = 0; $ix <$pocetJizd; $ix++)
 {
  echo "trip " . ($ix+1) . " - (" .($pocetJizd - $ix) . ") dist: " . floor ($distances[$ix] / 1000) . "<br>";
 }
 */
 //debug
 
 
 
 //  echo  "##########################################################<br>";
 //yearly metric
   for ($ix = 0; $ix <$pocetJizd; $ix++)
 {
      $distances[$ix] = floor ($distances[$ix] / 1000);
      $stejnychAdelsich = ($pocetJizd - $ix);
      
      if ($stejnychAdelsich>=$distances[$ix])         
         $edn = $distances[$ix];
        else
         break;      
 }

  //yearly statute - asi by to slo spojit, protoze metricke EN bude mensi
   for ($ix = 0; $ix <$pocetJizd; $ix++)
 {
      $distancesStatute[$ix] = floor ($distancesStatute[$ix] / 1000);
      $stejnychAdelsichStatute = ($pocetJizd - $ix);
      
      if ($stejnychAdelsichStatute>=$distancesStatute[$ix])
         $ednStatute = $distancesStatute[$ix];
        else
         break;      
 }


// echo "pocet jizd life:" . $pocetJizdLife . "<br>"; 

 //life time
    for ($ixLife = 0; $ixLife <$pocetJizdLife; $ixLife++)
 {
      $distancesLife[$ixLife] = floor ($distancesLife[$ixLife] / 1000);
      $stejnychAdelsichLife = ($pocetJizdLife - $ixLife);
      
     // echo "ride #". $ixLife . " left=" .   $stejnychAdelsichLife . " delka=" . $distancesLife[$ixLife] . "<br>" ;
      
      if ($stejnychAdelsichLife>=$distancesLife[$ixLife])
         $ednLife = $distancesLife[$ixLife];
        else
         break;      
         
 }
 
  //life time  statute
    for ($ixLife = 0; $ixLife <$pocetJizdLife; $ixLife++)
 {
      $distancesLifeStatute[$ixLife] = floor ($distancesLifeStatute[$ixLife] / 1000);
      
      $stejnychAdelsichLifeStatute = ($pocetJizdLife - $ixLife);
         
      
      if ($stejnychAdelsichLifeStatute>=$distancesLifeStatute[$ixLife])
         $ednLifeStatute = $distancesLifeStatute[$ixLife];
        else
         break;      
         
 }
 
 
 echo  "Your last year metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times in last year as of today.</i><p>"  ;

  echo  "Your last year real <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednStatute . "</b>. <br>";
 echo "<i>That means you rode " . $ednStatute . " miles or more at least " . $ednStatute . " times in last year as of today.</i><p>"  ;


 echo  "Your lifetime metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednLife . "</b>. <br>";
 echo "<i>That means you rode " . $ednLife . "km or more at least " . $ednLife . " times since your started recording on Strava.</i><p>"  ;
 
  echo  "Your lifetime real <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednLifeStatute . "</b>. <br>";
 echo "<i>That means you rode " . $ednLifeStatute . " miles or more at least " . $ednLifeStatute . " times since your started recording on Strava.</i><p>"  ;
 
 
   }//konec if autorizace
?>
          </body>
