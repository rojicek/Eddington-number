<!DOCTYPE html>
<html> 
<head> 

<title>Eddington number</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="ed.css">



    </head>

<body>
<?php

//echo "JE TO ROZBITE!!<p>";

include 'ensupport.php';


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
 

 $url = "https://www.strava.com/api/v3/athlete?access_token=" . $token;
 $result = file_get_contents($url);
 $athlete = json_decode ($result, true);
    
  
 
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
 

 //create array fof lifes (all live / last year)
 //metric and statute -> 4 arrays
 //can be more pages (max 200 rides per page by Strava)

 foreach ($rides as $oneride)
  {
     
       $rideDate =  strtotime($oneride["start_date_local"]);
       
       //last year
       if (($rideDate >= $predrokem )   && (strtolower($oneride["type"]) == 'ride'))
       {
        $lastYearRides[] = new Ride("2000-01-01", $oneride["distance"]);
        $lastYearRidesStatute[] = new Ride("2000-01-01", $oneride["distance"] / $mileCoef);
              
      } 
      
      //all time
      if ((strtolower($oneride["type"]) == 'ride'))
       {                 
        $lifetimeRides[] = new Ride("2000-01-01", $oneride["distance"]);
        $lifetimeRidesStatute[] = new Ride("2000-01-01", $oneride["distance"] / $mileCoef);
      } 
      
  }
  
  $page=$page + 1;
}
 
 
 /*
  $lastYearRides[] = new Ride("2000-01-01", 10589);
  $lastYearRides[] = new Ride("2000-01-01", 15589);
  $lastYearRides[] = new Ride("2000-01-01", 13589);
  $lastYearRides[] = new Ride("2000-01-01", 22589);
  $lastYearRides[] = new Ride("2000-01-01", 55589);
  $lastYearRides[] = new Ride("2000-01-01", 40589);
  $lastYearRides[] = new Ride("2000-01-01", 31589);
  $lastYearRides[] = new Ride("2000-01-01", 28589);
  $lastYearRides[] = new Ride("2000-01-01", 17589);
  $lastYearRides[] = new Ride("2000-01-01", 5589);
  $lastYearRides[] = new Ride("2000-01-01", 9589);
  $lastYearRides[] = new Ride("2000-01-01", 19589);
  $lastYearRides[] = new Ride("2000-01-01", 39589);
      
   $lastYearRides[] = new Ride("2000-01-01", 11589);
      $lastYearRides[] = new Ride("2000-01-01", 31589);
    */
   //debug - all print
   //	 usort($lifetimeRides, array("Ride", "CompareRides"));               
   //$pocetJizd = sizeof  ($lifetimeRides); 
   //for ($ix = 0; $ix <$pocetJizd; $ix++)
    //echo "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $lifetimeRides[$ix]->distanceKM . " km<br>";
   //echo "-----------------------<p>";
   //end of debug
     
   
   
   
  
 $edn =  GetEddingtonNumber($lastYearRides);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lastYearRides, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lastYearRides, $planEN2);
 
 echo  "Your last year metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times in last year as of today.</i><br>"  ;
 echo "<font size=-1>";
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . "km or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . "km or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 
 
 
 $edn =  GetEddingtonNumber($lastYearRidesStatute);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lastYearRidesStatute, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lastYearRidesStatute, $planEN2);
 
 echo  "Your last year statute <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . " miles or more at least " . $edn . " times in last year as of today.</i><br>"  ;
 echo "<font size=-1>";
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . " miles or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . " miles or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 
 
 $edn =  GetEddingtonNumber($lifetimeRides);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lifetimeRides, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lifetimeRides, $planEN2);
 
 echo  "Your lifetime metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times since you started recording on Strava.</i><br>"  ;
 echo "<font size=-1>";
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . "km or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . "km or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
  
 $edn =  GetEddingtonNumber($lifetimeRidesStatute);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2; 
 $missingRides1 = PlanEddingtonNumber  ($lifetimeRidesStatute, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lifetimeRidesStatute, $planEN2);
 
 echo  "Your lifetime statute <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . " miles or more at least " . $edn . " times since you started recording on Strava.</i><br>"  ;
echo "<font size=-1>";
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . " miles or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . " miles or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 
 
 
   }//konec if autorizace
?>
          </body>
