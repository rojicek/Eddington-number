
<!DOCTYPE html>
<html> 


<head> 

 <style>
    .chartWithOverlay {
           position: relative;
           width: 800px;
    }
    .overlay {
           width: 800px;
           height: 60px;
           position: absolute;    
           left: 0px;
           top: 260px;
    
    }
 </style>

<title>Eddington number</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="ed.css">


    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
         
    </head>

<body>
<?php

//echo "JE TO ROZBITE!!<p>";

include 'ensupport.php';



session_start();
$mileCoef = 1.609344;
$planAhead = 15;
$token =  $_SESSION['stravatoken'];
//echo "token: " .    $token . "<br>";

if (is_null($token)) 
{
       echo "<p>To learn your <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> &nbsp; <a href=\"auth.php\"><img src=\"LogInWithStrava.png\" width=\"159\" height=\"31\" align=\"middle\" alt=\"Log in with Strava\"></a></p>";
       echo "This link will ask you to grant permission to <b>read</b> some data about your rides from Strava through their interface.<br> This permission can be revoked at any time in Strava settings. I will never have access to your Strava credentials" ;         
}   
 
 else
 {//login
//jsem prihlaseny, muzu nacist aktivity 
 

 $url = "https://www.strava.com/api/v3/athlete?access_token=" . $token;
 $result = file_get_contents($url);
 $athlete = json_decode ($result, true);
  
 //get athlete info 
 $atljmeno =  $athlete["firstname"];// . " " . $athlete["lastname"];
 $atlpic =  $athlete["profile_medium"];
 
if    ($atlpic != "avatar/athlete/medium.png")
{
 echo "<img src = \"" .  $atlpic . "\" align = \"middle\">&nbsp;&nbsp;&nbsp;&nbsp;"; 
 }
 else
 {
   echo "&nbsp;&nbsp;&nbsp;&nbsp;"; 
 }
 
 echo  "Hi " .   $atljmeno . " - here's your stats:<p>";
 
 $emailbody =  $athlete["firstname"] . " " . $athlete["lastname"] . "\r\n";
 $emailbody = $emailbody . "used at: " . date('d.m.Y H:i:s', time()). "\r\n";;  
   
  
 
 //get rides
 
 $page = 1;
 

 while (true)
 {
 $url = "https://www.strava.com/api/v3/activities?per_page=200&page=".$page."&access_token=" . $token;
 $result = file_get_contents($url);
 $rides = json_decode ($result, true);
 
 //echo $url . "<p>";
 
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
      
      /* 
       //last year
       if (($rideDate >= $predrokem )   && (strtolower($oneride["type"]) == 'ride'))
       {
        $lastYearRides[] = new Ride("2000-01-01", floor($oneride["distance"] / 1000));
        $lastYearRidesStatute[] = new Ride("2000-01-01", floor($oneride["distance"] /1000 / $mileCoef));
              
      } 
        */
      //all time
      if ((strtolower($oneride["type"]) == 'ride'))
       {  
        
        $cleanName = preg_replace("/[^a-zA-Z0-9ěščřžýáíéůúĚŠČŘŽÝÁÍÉŮÚè_.,;@#%~\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\-\s\\\\]+/", " ", $oneride["name"]); 
        $revolutions = floor($oneride["average_cadence"] * $oneride["moving_time"] / 60.0);
                    
        $lifetimeRides[] = new Ride("2000-01-01", floor($oneride["distance"] / 1000), $cleanName, $revolutions);
        //$lifetimeRidesStatute[] = new Ride("2000-01-01", floor($oneride["distance"] / 1000 / $mileCoef));
      } 
      
  }
  
  $page=$page + 1;
}
 
    
  /*
   $lifetimeRides = "";
  
  //VASEK 
  $lifetimeRides[] = new Ride("2000-01-01", 22);
  $lifetimeRides[] = new Ride("2000-01-01", 23);
  $lifetimeRides[] = new Ride("2000-01-01", 25);  
  $lifetimeRides[] = new Ride("2000-01-01", 25);
  
  $lifetimeRides[] = new Ride("2000-01-01", 26);
  $lifetimeRides[] = new Ride("2000-01-01", 27);
  $lifetimeRides[] = new Ride("2000-01-01", 27);
  $lifetimeRides[] = new Ride("2000-01-01", 35);
  $lifetimeRides[] = new Ride("2000-01-01", 36);
  
  $lifetimeRides[] = new Ride("2000-01-01", 38);
  $lifetimeRides[] = new Ride("2000-01-01", 40);
  
    $lifetimeRides[] = new Ride("2000-01-01", 41);
  $lifetimeRides[] = new Ride("2000-01-01", 41);
  
  $lifetimeRides[] = new Ride("2000-01-01", 44);
    $lifetimeRides[] = new Ride("2000-01-01", 44);
    $lifetimeRides[] = new Ride("2000-01-01", 50);
    
     $lifetimeRides[] = new Ride("2000-01-01", 54);
    $lifetimeRides[] = new Ride("2000-01-01", 62);
    $lifetimeRides[] = new Ride("2000-01-01", 69);
    
    
     $lifetimeRides[] = new Ride("2000-01-01", 73);
    $lifetimeRides[] = new Ride("2000-01-01", 74);
    $lifetimeRides[] = new Ride("2000-01-01", 82);
    $lifetimeRides[] = new Ride("2000-01-01", 84);
    $lifetimeRides[] = new Ride("2000-01-01", 120);
   
   //VASEK KONEC
    */
    /*
       $lifetimeRides = "";
   //MARO 
    $lifetimeRides[] = new Ride("2000-01-01", 4);
    $lifetimeRides[] = new Ride("2000-01-01", 4);
    $lifetimeRides[] = new Ride("2000-01-01", 5);
    $lifetimeRides[] = new Ride("2000-01-01", 5);
    $lifetimeRides[] = new Ride("2000-01-01", 5);
    $lifetimeRides[] = new Ride("2000-01-01", 5);
    $lifetimeRides[] = new Ride("2000-01-01", 6);
    $lifetimeRides[] = new Ride("2000-01-01", 6);
    $lifetimeRides[] = new Ride("2000-01-01", 6);
    $lifetimeRides[] = new Ride("2000-01-01", 7);
    $lifetimeRides[] = new Ride("2000-01-01", 7);
    $lifetimeRides[] = new Ride("2000-01-01", 7);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 9);
    $lifetimeRides[] = new Ride("2000-01-01", 10);
    $lifetimeRides[] = new Ride("2000-01-01", 10);
    $lifetimeRides[] = new Ride("2000-01-01", 12);
    $lifetimeRides[] = new Ride("2000-01-01", 13);
    $lifetimeRides[] = new Ride("2000-01-01", 13);
    $lifetimeRides[] = new Ride("2000-01-01", 13);
    $lifetimeRides[] = new Ride("2000-01-01", 13);
    $lifetimeRides[] = new Ride("2000-01-01", 14);
    $lifetimeRides[] = new Ride("2000-01-01", 23);    
    $lifetimeRides[] = new Ride("2000-01-01", 24);
    $lifetimeRides[] = new Ride("2000-01-01", 25);
    $lifetimeRides[] = new Ride("2000-01-01", 25);
    $lifetimeRides[] = new Ride("2000-01-01", 25);    
    $lifetimeRides[] = new Ride("2000-01-01", 25);
    $lifetimeRides[] = new Ride("2000-01-01", 26);    
    $lifetimeRides[] = new Ride("2000-01-01", 26);
    $lifetimeRides[] = new Ride("2000-01-01", 27);
    $lifetimeRides[] = new Ride("2000-01-01", 27);    
    $lifetimeRides[] = new Ride("2000-01-01", 27);
    $lifetimeRides[] = new Ride("2000-01-01", 27);    
    $lifetimeRides[] = new Ride("2000-01-01", 27);
    $lifetimeRides[] = new Ride("2000-01-01", 28);
    $lifetimeRides[] = new Ride("2000-01-01", 32);
    $lifetimeRides[] = new Ride("2000-01-01", 33);
    //$lifetimeRides[] = new Ride("2000-01-01", 23);
    //$lifetimeRides[] = new Ride("2000-01-01", 24);
   // $lifetimeRides[] = new Ride("2000-01-01", 24);
   // $lifetimeRides[] = new Ride("2000-01-01", 25);
  //  $lifetimeRides[] = new Ride("2000-01-01", 25);
 //   $lifetimeRides[] = new Ride("2000-01-01", 25);
  //  $lifetimeRides[] = new Ride("2000-01-01", 27);
  //  $lifetimeRides[] = new Ride("2000-01-01", 28);


     */
       
     
   //debug - all print
   usort($lifetimeRides, array("Ride", "CompareRides")); 
   $emailbody = $emailbody . "Vsechny jizdy\r\n";               
   $pocetJizd = sizeof  ($lifetimeRides); 
   for ($ix = 0; $ix <$pocetJizd; $ix++)
   {
   // echo "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $lifetimeRides[$ix]->distance . " km<br>";
     $emailbody = $emailbody . "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $lifetimeRides[$ix]->distance . " km : ".$lifetimeRides[$ix]->revolutions . " rev.\r\n";
  }
    $emailbody = $emailbody . "konec vsech jizd\r\n\r\n";
  /*  
    
   usort($lastYearRides, array("Ride", "CompareRides")); 
   $emailbody = $emailbody . "Last year jizdy\r\n";               
   $pocetJizd = sizeof  ($lastYearRides); 
   for ($ix = 0; $ix <$pocetJizd; $ix++)
  //  echo "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $lastYearRides[$ix]->distance . " km<br>";
    $emailbody = $emailbody . "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $lastYearRides[$ix]->distance . " km\r\n";
    $emailbody = $emailbody . "konec last year jizd\r\n";
    */
   //echo "-----------------------<p>";
   //end of debug
     
   
   
   
  /*
 $edn =  GetEddingtonNumber($lastYearRides);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lastYearRides, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lastYearRides, $planEN2);
 
 echo  "Your last year metric Eddington number is <b>". $edn . "</b>. <br>";
 echo "<font size=-1>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times in last year as of today.</i><br>"  ;
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . "km or more to reach " . $planEN1 . "!<br>";
  echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . "km or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 
  $emailbody = $emailbody . "last year metric = " .  $edn . "\r\n";
 
 $edn =  GetEddingtonNumber($lastYearRidesStatute);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lastYearRidesStatute, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lastYearRidesStatute, $planEN2);
 
 echo  "Your last year real Eddington number is <b>". $edn . "</b>. <br>";
 echo "<font size=-1>";
 echo "<i>That means you rode " . $edn . " miles or more at least " . $edn . " times in last year as of today.</i><br>"  ; 
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . " miles or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . " miles or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 
 $emailbody = $emailbody . "last year real = " .  $edn . "\r\n";
  */
 $edn =  GetEddingtonNumber($lifetimeRides);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2;
 $missingRides1 = PlanEddingtonNumber  ($lifetimeRides, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lifetimeRides, $planEN2);
 
 echo  "Your lifetime metric Eddington number is <b>". $edn . "</b>. <br>";
 echo "<font size=-1>";
 echo "<i>That means you've ridden " . $edn . "km or more at least " . $edn . " times since you started recording on Strava.</i><br>"  ; 
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . "km or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . "km or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
 $emailbody = $emailbody . "lifetime metric = " .  $edn . "\r\n";
 $ednGrafStart =   $edn;
 /* 
 $edn =  GetEddingtonNumber($lifetimeRidesStatute);
 $planEN1 = $edn + 1;
 $planEN2 = $edn + 2; 
 $missingRides1 = PlanEddingtonNumber  ($lifetimeRidesStatute, $planEN1);
 $missingRides2 = PlanEddingtonNumber  ($lifetimeRidesStatute, $planEN2);
 
 echo  "Your lifetime realEddington number is <b>". $edn . "</b>. <br>";
 echo "<font size=-1>";
 echo "<i>That means you rode " . $edn . " miles or more at least " . $edn . " times since you started recording on Strava.</i><br>"  ;
 echo "Ride " . $missingRides1 . " ride(s) of " . $planEN1 . " miles or more to reach " . $planEN1 . "!<br>";
 echo "Ride " . $missingRides2 . " ride(s) of " . $planEN2 . " miles or more to reach " . $planEN2 . "!<br>";
 echo "</font><p>";
 
  $emailbody = $emailbody . "lifetime real = " .  $edn . "\r\n";
    */
  //graf
  
    //pridam graf pro lifetime EDN
  //$grafData = [];
  
  
   $emailbody = $emailbody . "\r\nplany:\r\n";
  for ($i = $ednGrafStart; $i <= $ednGrafStart+$planAhead; $i++)
  {
    
     $gedn =  PlanEddingtonNumber  ($lifetimeRides, $i);
     $dist[$i-$ednGrafStart] = $i;
     $edgTogo[$i-$ednGrafStart] = $gedn;
     $emailbody = $emailbody .   "For " . $dist[$i-$ednGrafStart] . " ride "  . $edgTogo[$i-$ednGrafStart] . "\r\n";
    // echo " for " . $dist[$i-$ednGrafStart] . " ride "  . $edgTogo[$i-$ednGrafStart] . "<br>";
    
  }
    

   
 
 ?>     
     <script type="text/javascript">
    
    
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
      
       //steps
        var dataSteps = google.visualization.arrayToDataTable([
          ['Distance',  'Rides to go' ],
          <?php
          
           for ($i = $ednGrafStart; $i <= $ednGrafStart+$planAhead; $i++)
           {
            echo "['" . $dist[$i-$ednGrafStart] . "', " .  $edgTogo[$i-$ednGrafStart] . "],";            
            }
           
          ?>
          
        ]);

        var optionsSteps = {
          title: 'Your lifetime Eddington number plans',
          titlePosition: 'in',
          vAxis: {title: 'Rides to go'},
          hAxis: {title: 'kilometers'},
          isStacked: true,
          legend: { position: "none" }, 
           animation: {
            duration: 1000,
            startup: true, 
            easing: "out", 
            },       
        };

        var chartSteps = new google.visualization.SteppedAreaChart(document.getElementById('chart_div_steps'));
        chartSteps.draw(dataSteps, optionsSteps);
      
       //histogram
       
          var dataHist = google.visualization.arrayToDataTable([
          ['Ride name', 'Length'],
            <?php
              
               for ($ix = 0; $ix < sizeof ($lifetimeRides); $ix++)
               {
              // echo "['" . Ride_. $ix . "', " .   12 . "],"; 
                echo "['" . $lifetimeRides[$ix]->name . "', " .   $lifetimeRides[$ix]->distance . "],"; 
               }   
               ?>
              ]);
               
          var optionsHist = {
          title: 'Distribution of your rides',
          legend: { position: 'none' },
           histogram: { bucketSize: 10 } ,
          hAxis: {title: 'Rides lenght [km]'},
          vAxis: {title: 'Rides ridden'},
           animation: {
            duration: 1000,
            startup: true , 
             easing: "out",
            },
        };

        var chartHist = new google.visualization.Histogram(document.getElementById('chart_div_hist'));
        chartHist.draw(dataHist, optionsHist);
                                                                            
    //donut/cadence
     var dataCadence = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
           <?php
                $kmOnly = 0;
                $totalRev = 0;
               for ($ix = 0; $ix < sizeof ($lifetimeRides); $ix++)
               {  
                 if  ($lifetimeRides[$ix]->revolutions > 0)
                 {          
                echo "['" . $lifetimeRides[$ix]->name . "', " .   $lifetimeRides[$ix]->revolutions . "],";
                $totalRev =  $totalRev + $lifetimeRides[$ix]->revolutions;
                }
                else
                { //nemam otacky, jen pridam km
                $kmOnly = $kmOnly + $lifetimeRides[$ix]->distance;
                } 
               } 
               
               if  ($totalRev ==0)
               { //aby se nakreslil graf
                  echo "['no data',1]";
              }
                 
               ?>       
        ]);

        var optionsCadence = {         
          pieHole: 0.6,
          legend: 'none',
          title: 'Your crank revolutions',
        };

        var chartCadence = new google.visualization.PieChart(document.getElementById('chart_div_cadence'));
        chartCadence.draw(dataCadence, optionsCadence);  
      
      }
      
      
    </script>                


  
 
 
 <div id="chart_div_steps" style="width: 800px; height: 600px;"></div>
 <div id="chart_div_hist" style="width: 800px; height: 600px;"></div>
 
 <div class="chartWithOverlay">
    <div id="chart_div_cadence" style="width: 800px; height: 600px;"></div>
   <div class="overlay">
      <div style="font-family:'Verdana'; font-style: bold;  font-size: 32px;">
        <center><?php echo $totalRev; ?></center>        
      </div>
      <div style="font-family:'Verdana'; font-size: 12px;">
      <center>crank revolutions</center>        
      <center><font style="font-size:10px;font-style: italic;">+ <?php echo $kmOnly;?>km<br>without cadence sensor</font></center>
      </div>
    </div>  
    </div>
 
  <?php
 
 //echo "<center> <b>Your lifetime Eddington number plans</b> </center>"; 
 //echo  "<div id=\"chart_div_steps\" style=\"width: 800px; height: 600px;\"></div>";
 //echo  "<div id=\"chart_div_hist\" style=\"width: 800px; height: 600px;\"></div>";


    

 //echo "<script>  javascript:drawChart(20); </script>";
 
 echo "<font size=-1>";
 echo "This is just to please your obsessive side so many athletes have these days. To learn what Eddington number is, look ";
 echo "<a href=\"http://en.wikipedia.org/wiki/Arthur_Eddington#Eddington_number_for_cycling\">here</a> or ";
 echo "<a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">here</a>.";
  echo "</font><p>";
 
 
 //spam: send email

$headers = 'From: strava@rojicek.cz' . "\r\n" .
    'Reply-To: jiri@rojicek.cz' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail("jiri@rojicek.cz", "Eddington number calc", $emailbody, $headers);
 
 
 
 
   }//konec if autorizace
?>
          </body>
          