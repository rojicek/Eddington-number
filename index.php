
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

<title>Strava stats</title>
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
$authToken =  $_SESSION['stravatoken'];



//try to reload array from session unless reload forced

if (($_GET["force"]=="yes")  || 1)
  {
    //echo "force reload<br>";
    for ($ixA = 0; $ixA < $_SESSION['numberOfActivities']; $ixA++)
        {unset($_SESSION['activity'. $ixA]);}
    
    unset($_SESSION['numberOfActivities']);
  }

if (isset($_SESSION['numberOfActivities']))
{
 //echo "ctu ze session<br>";
 for ($ixA = 0; $ixA < $_SESSION['numberOfActivities']; $ixA++)
     {$allActivities[$ixA]  =    $_SESSION['activity'. $ixA];}
 } //isset

 
//echo "prvni: " .  $prvniActivities[0]->name . "<br>";
//echo "pocet aktivit na zacatku - size: " . sizeof  ($allActivities) . "<br>";
//echo "pocet aktivit na zacatku -count: " . count  ($allActivities) . "<br>";
//echo "prvni act na zacatku: >". $allActivities[0]->name . "<<br>";

if (is_null($authToken)) 
{
       echo "<p>To learn your cycling stats including <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> &nbsp; <a href=\"auth.php\"><img src=\"LogInWithStrava.png\" width=\"159\" height=\"31\" align=\"middle\" alt=\"Log in with Strava\"></a></p>";
       echo "This link will ask you to grant permission to <b>read</b> some data about your rides from Strava through their interface.<br> This permission can be revoked at any time in Strava settings. I will never have access to your Strava credentials" ;         
}   

 else
 {//login
//jsem prihlaseny, muzu nacist aktivity 
 

 $url = "https://www.strava.com/api/v3/athlete?access_token=" . $authToken;
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
 
 //email jen dole
 //$emailbody =  $athlete["firstname"] . " " . $athlete["lastname"] . "\r\n";
 //$emailbody = $emailbody . "used at: " . date('d.m.Y H:i:s', time()). "\r\n";;  
   


 //get activities
   
 $page = 1;
 
 
 //todo: asi bych nemel nacitat aktivity, kdyz uz je mam v poli 
  
 if (!isset($_SESSION['numberOfActivities']))
 { //nenacetl jsem ze session  
 while (true) 
 {
  
   
  $url = "https://www.strava.com/api/v3/activities?per_page=200&page=".$page."&access_token=" . $authToken;
  $result = file_get_contents($url);
  $activities = json_decode ($result, true);
 
  //echo $url . "<p>";
 
 if (empty($activities))
 {
   break; //zadne dalsi jizdy
 }
  

 //can be more pages (max 200 rides per page by Strava)                
 foreach ($activities as $oneactivity)
  {
        //echo ".";    
        $cleanName = preg_replace("/[^a-zA-Z0-9ěščřžýáíéůúĚŠČŘŽÝÁÍÉŮÚè_.,;@#%~\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\-\s\\\\]+/", " ", $oneactivity["name"]); 
        $revolutions = floor($oneactivity["average_cadence"] * $oneactivity["moving_time"] / 60.0);
        //$datum = new DateTime($oneactivity["start_date_local"], new DateTimeZone($oneactivity["timezone"]));
                
        $timezone = trim(strstr($oneactivity["timezone"], " "));                                       
        $datum = new DateTime($oneactivity["start_date_local"], new DateTimeZone($timezone));
                
              
      //ok  echo "date: " . $oneactivity["start_date_local"] . ' @ ' .  $oneactivity["timezone"] . " = ";
      //ok echo $datum->format('Y-m-d H:i:s') .  "--" . $datum->format('Y') ."<br>";        
                                                                                
             
        $allActivities[] = new Activity($datum, $timezone, $oneactivity["type"], $oneactivity["distance"], $cleanName, $oneactivity["moving_time"], $oneactivity["total_elevation_gain"], $revolutions);

        
  }
  
  $page=$page + 1;
}
} //nenacetl jsem ze session 
 
 // echo "pocet aktivit dole " . sizeof  ($allActivities) . "<br>";
   
 // echo  $allActivities[0]->date->format('Y-m-d H:i:s') ."<br>"; 
 // echo  $allActivities[0]->timezone . "<br>";
 // echo  $allActivities[0]->type . "<br>";  
  
 
 
 //store activities into session variable
 $_SESSION['numberOfActivities'] =  sizeof  ($allActivities); 
 for ($ixA = 0; $ixA < sizeof  ($allActivities); $ixA++) 
 { //all activities
      $_SESSION['activity' . $ixA ] =  $allActivities[$ixA];
      $ixA = $ixA + 1;
 }//all activities
 
 
 //priprav menu (combo)
 //$comboYears = array("all", "year", "custom");
 //$comboYearsLabels = array("All time", "Last year", "Custom");
 
 
 
 $comboYears[0][0] = "all";
 $comboYears[0][1] = "All time";
 $comboYears[1][0] = "year";
 $comboYears[1][1] = "Last 365 days";
 $comboYearsItemsNbr = 2;
 $comboActivities = array("Ride", "Run"); //na zbytek kaslu
 $comboUnits = array("Metric", "Imperial"); //na zbytek kaslu
 
 
  
      foreach ($allActivities as $oneActivity)
      {
        
        $yr4menu = $oneActivity->date->format('Y') ;
        $alreadyInCombo = 0;
        
        foreach($comboYears as $tmp)
        {
          if(in_array($yr4menu, $tmp))
          {
              $alreadyInCombo = 1;
          }
        }
 
        
        //if (!in_array($yr4menu,$comboYears))  //if not yet in array
        if (!$alreadyInCombo)
         {
           $comboYears[$comboYearsItemsNbr][0]  =  $yr4menu;
           $comboYears[$comboYearsItemsNbr][1]  =  $yr4menu;
           $comboYearsItemsNbr = $comboYearsItemsNbr + 1;
           
           //array_push ($comboYears, $yr4menu) ;
           //array_push ($comboYearsLabels, $yr4menu) ;
        }
            
      }
 
 /* neni podporovano
 $comboYears[$comboYearsItemsNbr][0] = "custom";
 $comboYears[$comboYearsItemsNbr][1] = "Custom";
 $comboYearsItemsNbr = $comboYearsItemsNbr + 1;
 */
 
  for ($i=0;$i<$comboYearsItemsNbr; $i++)
  {
   // echo  $comboYears[$i][0] . " - " . $comboYears[$i][1] . "<br>";
  }
   
//defaultni hodnoty

 if ($_GET["time"]== "")
      $selectedTime = "all";
    else
      $selectedTime = $_GET["time"];

    if ($_GET["type"]== "")
      $selectedType = "ride";
    else
      $selectedType = $_GET["type"];

    if ($_GET["unit"]== "")
      $selectedUnit = "metric";
    else
      $selectedUnit = $_GET["unit"];
      
   //echo "selected"


 //for ($i=0;$i<$comboYearsItemsNbr; $i++)
 //echo "podminka:" . strtolower($selectedTime) . " == " .  strtolower($comboYears[$i][0]) . "<br>";

    
 ?>

Choose time, activity and your preferred units: 
<form name="selects" id="formSelects" action="" method="GET">


<select name="time" id="time">
<?php 
//foreach ($comboYears as $oneYear)
 for ($i=0;$i<$comboYearsItemsNbr; $i++)
 { 
  
  echo "<option value=\"". strtolower ($comboYears[$i][0]) . "\"";
  if (strtolower($selectedTime)==strtolower($comboYears[$i][0])){echo "selected=\"selected\"";}
  echo "> ".$comboYears[$i][1] ."</option>";
  }

?>

</select>

<select name="type" id="type">
<?php 
foreach ($comboActivities as $oneAct)
 { 
  echo "<option value=\"". strtolower ($oneAct) . "\"";
  if (strtolower($selectedType)==strtolower($oneAct)){echo "selected=\"selected\"";}
  echo "> ".$oneAct."</option>";
  }

?>
</select>
 
<select name="unit" id="unit">
<?php 
foreach ($comboUnits as $oneUnit)
 { 
  echo "<option value=\"". strtolower ($oneUnit) . "\"";
  if (strtolower($selectedUnit)==strtolower($oneUnit)){echo "selected=\"selected\"";}
  echo "> ".$oneUnit."</option>";
  }

?>
</select>
 

<input type="checkbox" name="force" value="yes"> Force data reload from Strava<br>
<input type="submit"  value="Refresh stats">
</form>

    <?php
            
  
    //loop over all activities and find those match
    unset($selectedActivities);
    $calcNumberOfActivities = 0;
    $calcTotalElevation = 0;
    $calcTotalDistance = 0;
    $calcMovingTime = 0;
    $calcRevolution = 0;
    $calcDistForNoCadence = 0;
    
    //jen pro prevod na stopy pro elevation gain pri imperial units
     $elevKoef = 1;
     if  (strtolower($selectedUnit) == "imperial")
              $elevKoef = 5.28;
    
   // echo "pocet vsech aktivit:" . sizeof  ($allActivities) . "<br>";
    foreach ($allActivities as $act)
     {       
       $timeMatch = 0; //make it separate
       if  (strtolower($selectedTime) == "all")
          {$timeMatch = 1;}
      
      $activityYear = $act->date->format('Y');
      if  (strtolower($selectedTime) == strtolower($activityYear))
          {$timeMatch = 1;}
       
       $yearAgo =  strtotime('-1 year');
       if ((strtolower($selectedTime) == "year") &&  ($act->date->format("U") >= $yearAgo))
            {$timeMatch = 1;}
                     
      // echo   "act time = "  . $act->date->format("Y-m-d H:i:s") . " ---- year ago=" . date("Y-m-d H:i:s", $yearAgo) . " MATCH = " . $timeMatch . "<br>"; 
                           
     
       //add as many filters as appropriate
    
       if  ((strtolower($act->type) == strtolower($selectedType)) && ($timeMatch == 1))      
       {        
                
        if  (strtolower($selectedUnit) == "imperial")
               {
                $dist = $act->distance / 1000 / $mileCoef;    //in miles
                $elev = $act->elevation_gain / $mileCoef;     //in ( miles) 
               
               }
            else
               {
                 $dist = $act->distance / 1000; //in km
                 $elev = $act->elevation_gain; //in meters                 
               }
        //echo "act " . $act->name ."; "  . $act->date->format("Y-m-d H:i:s") . "; dist:" . $dist . " elev: " . $elev . "<br>";
        
        $selectedActivities[] = new Activity($act->datum,  $act->timezone, $act->type, floor($dist), $act->name, $act->moving_time, $elev, $act->revolutions);
        $calcNumberOfActivities = $calcNumberOfActivities + 1;        
        $calcTotalElevation = $calcTotalElevation +  $elev;
        $calcTotalDistance = $calcTotalDistance +  $dist;
        $calcMovingTime = $calcMovingTime +  $act->moving_time;
        if   ($act->revolutions > 0)
          {$calcRevolution = $calcRevolution +   $act->revolutions;}
          else
          {$calcDistForNoCadence = $calcDistForNoCadence + $dist; }
        
       }
     }//foreach 
    
     //spravne labely
     $distUOM = "km";
     $elevUOM = "m";
     if  (strtolower($selectedUnit) == "imperial")
       {
        $distUOM = "miles";
        $elevUOM = "ft";
       }
      
      $actLabel = "Run";
      $bucketSize = 3; //pro beh
      if  (strtolower($selectedType) == "ride")
       {
        $actLabel = "Ride";  
        $bucketSize = 10; //pro kolo      
       } 
       
      
     
    
    //pokusy
     
     $edn =  GetEddingtonNumber($selectedActivities);
     $planEN1 = $edn + 1;
     $planEN2 = $edn + 2;     
     $missingRides1 = PlanEddingtonNumber  ($selectedActivities, $planEN1);
     $missingRides2 = PlanEddingtonNumber  ($selectedActivities, $planEN2);


     $calcMovingTimeDays =  floor($calcMovingTime / 86400);
     $calcMovingTimeHours =   floor(($calcMovingTime - $calcMovingTimeDays * 86400)/3600); 
     $calcMovingTimeMins =   floor(($calcMovingTime - $calcMovingTimeDays * 86400 - $calcMovingTimeHours * 3600)/60);
      $avgElevationGrade = 0;
     if ($calcTotalDistance > 0)
      {$avgElevationGrade = $calcTotalElevation / $calcTotalDistance / 10; }
         
    
             
     
                              
     
    

    ?>


  
    <table>
   
    <tr>
    <td colspan="2" class ="tucne">
     Your stats:    
    </td>
    </tr>
    
    <tr>
    <td>
    Total number of <?php echo strtolower($actLabel) . "s"; ?>
    </td>
    <td>
    <?php echo $calcNumberOfActivities;?>
    </td>
    </tr>

    <tr>
    <td>
    Total moving time:
    </td>
    <td>
    <?php echo $calcMovingTimeDays . "d ". $calcMovingTimeHours.  "hr " . $calcMovingTimeMins . "min";?>
    </td>
    </tr>   
    
    <tr>
    <td>
    Total number distance:
    </td>
    <td>
    <?php echo round($calcTotalDistance,1) . $distUOM ;?>
    </td>
    </tr>
    
    <tr>
    <td>
    Total number elevation gain:
    </td>
    <td>
    <?php echo round($calcTotalElevation * $elevKoef,1) . $elevUOM ;?>
    </td>
    </tr>
    
    <tr>
    <td>
    Average elevation gain grade:
    </td>
    <td>
    <?php echo round ($avgElevationGrade,2) . "%";?>
    </td>
    </tr>  
 
   <?php
    if  (strtolower($selectedType) == "ride") 
    { //"rides only"
    ?>       
     <tr>
     <td>
     Total crank revolutions:
     </td>
     <td>
     <?php echo  $calcRevolution . " + extra ". round($calcDistForNoCadence,1) . $distUOM . " without cadence sensor <br>"; ?>
    </td>
    </tr>  
    <?php
    } //konec "rides only"
    ?>
    
    <tr>
    <td colspan="2">
    <hr>
    </td>  
    </tr> 
    
    <tr>
    <td>
    Your Eddington number:
    </td>
    <td>
    <?php echo $edn;?>
    </td>
    </tr> 
    
    <tr>
    <td colspan="2">
     <?php
     $sfx = "";
     if  ($missingRides1 > 1) {$sfx = "s";}
      echo $actLabel . " " . $missingRides1 . " " . strtolower($actLabel) . $sfx . " of " . $planEN1 . $distUOM. " or more to reach " .  $planEN1 . "!";
     ?> 
    </td>  
    </tr>
    
    <tr>
    <td colspan="2">
     <?php
     $sfx = "";
     if  ($missingRides2 > 1) {$sfx = "s";}
      echo $actLabel . " " . $missingRides2 . " " . strtolower($actLabel) . $sfx . " of " . $planEN2 . $distUOM. " or more to reach " .  $planEN2 . "!";
     ?> 
    </td>  
    </tr>  
    
    </table>


    <!--pridam nejake grafy -->
      
    
    <?php
    
    //usporadam podle velikost
    usort($rides, array("Activity", "CompareDistances"));
    
     $ednGrafStart =   $edn; //zacatek
     for ($i = $ednGrafStart; $i <= $ednGrafStart+$planAhead; $i++)
      {
    
        $gedn =  PlanEddingtonNumber  ($selectedActivities, $i);
        $distTogo[$i-$ednGrafStart] = $i;
        $edgTogo[$i-$ednGrafStart] = $gedn;
        
     }

    ?>
    
     <script type="text/javascript">
    
    
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
      
       //EDN steps
        var dataSteps = google.visualization.arrayToDataTable([
          ['Distance',
          <?php echo "'" . $actLabel . "s to go'"   ?>
            ],
          <?php
          
           for ($i = $ednGrafStart; $i <= $ednGrafStart+$planAhead; $i++)
           {
            echo "['" . $distTogo[$i-$ednGrafStart] . "', " .  $edgTogo[$i-$ednGrafStart] . "],";            
            }
           
          ?>
          
        ]);

        var optionsSteps = {
          title: 'Your Eddington number plans',
          titlePosition: 'in',
          vAxis: {title: <?php echo "'" . $actLabel . "s to go'"   ?>},
          hAxis: {title: <?php echo "'Distance [" . $distUOM . "]'"; ?>},
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
      
       //konec EDN steps
         //histogram
       
          var dataHist = google.visualization.arrayToDataTable([
          ['Ride name', 'Length'],
            <?php
              
               for ($ix = 0; $ix < sizeof ($selectedActivities); $ix++)
               {
              // echo "['" . Ride_. $ix . "', " .   12 . "],"; 
                echo "['" . $selectedActivities[$ix]->name . "', " .   $selectedActivities[$ix]->distance . "],"; 
               }   
               ?>
              ]);
               
          var optionsHist = {
          title: <?php echo "'Distribution of your " . strtolower($actLabel) . "s'";?>,
          legend: { position: 'none' },
           histogram: { bucketSize: <?php echo $bucketSize; ?>} ,
          hAxis: {title: <?php echo "'Distance [" . $distUOM . "]'"; ?>},
          vAxis: {title: <?php echo "'Number of " . strtolower($actLabel) . "s'";?>},
           animation: {
            duration: 1000,
            startup: true , 
             easing: "out",
            },
        };

        var chartHist = new google.visualization.Histogram(document.getElementById('chart_div_hist'));
        chartHist.draw(dataHist, optionsHist);
        //konec histogramu

        //kadence
                                                                                 
    //donut/cadence
     var dataCadence = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
           <?php
                $kmOnly = 0;
                $totalRev = 0;
               for ($ix = 0; $ix < sizeof ($selectedActivities); $ix++)
               {  
                 if  ($selectedActivities[$ix]->revolutions > 0)
                 {          
                echo "['" . $selectedActivities[$ix]->name . "', " .   $selectedActivities[$ix]->revolutions . "],";
                $totalRev =  $totalRev + $selectedActivities[$ix]->revolutions;
                }
                else
                { //nemam otacky, jen pridam km
                $kmOnly = $kmOnly + $selectedActivities[$ix]->distance;
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

        //konec kadence
       
       
       } //drawchar
        
           //EDN schody konec
          </script>   
     
      <div id="chart_div_steps" style="width: 800px; height: 600px;"></div>  
       <div id="chart_div_hist" style="width: 800px; height: 600px;"></div>  
   
    <?php
    if  (strtolower($selectedType) == "ride") 
    { //"rides only"
    ?>     
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
    } //rides only
    ?>

    <?php
    $emailbody =  $athlete["firstname"] . " " . $athlete["lastname"] . "\r\n";
    $emailbody = $emailbody . "used at: " . date('d.m.Y H:i:s', time()) . "\r\n";
    
    $emailbody = $emailbody .  "Time:" .  $selectedTime . "\r\n";
    $emailbody = $emailbody .  "Type:" .  $selectedType . "\r\n";
    $emailbody = $emailbody .  "Units:" .  $selectedUnit . "\r\n";
    $emailbody = $emailbody .  "EDN:" .  $edn . "\r\n";
    
    
    for ($ix = 0; $ix < sizeof ($selectedActivities); $ix++)
          {
                  $emailbody = $emailbody .  " - " . $selectedActivities[$ix]->name . " - " .   $selectedActivities[$ix]->distance . $distUOM . "\r\n"; 
            }   
    
    $headers = 'From: strava@rojicek.cz' . "\r\n" .
    'Reply-To: jiri@rojicek.cz' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    mail("jiri@rojicek.cz", "Strava2", $emailbody, $headers);
 
    } //login if
    
    ?>
      
</body>
</html>