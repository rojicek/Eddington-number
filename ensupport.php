<?php
 
//Ride class
  
class Ride {
	public $date;
	public $distance;
  public $distanceKM;
	
	function Ride($date, $distance) 
	{
		$this->date = $date;
		$this->distance = $distance;
    $this->distanceKM = floor ($distance/1000); 
	}   
	static function CompareRides($r1, $r2)
	{
		return ($r1->distance - $r2->distance);
	}
}

 
 
function GetEddingtonNumber($rides)
{
   //minedn pro hledani dalsi jizdy - edn musi byt vetsi nez tohle cislo
	 usort($rides, array("Ride", "CompareRides"));
   
   
   //debug   
   /*
   $pocetJizd = sizeof  ($rides); 
   for ($ix = 0; $ix <$pocetJizd; $ix++)
    echo "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $rides[$ix]->distanceKM . " km<br>";
   echo "-----------------------<p>";
   */  
    //konec debug
	   
     
   //abych poznal chybu
  $edn = -1;  
  $pocetJizd = sizeof  ($rides); 
 
           
 //debug
// for ($i = 0; $i < $pocetJizd; $i++) 
 //echo "i=" . ($i+1) . " (". ($pocetJizd - $i) . ") " .  $rides[$i]->distanceKM . " km<br>";
 //echo "<p>";
                       	 
           
  //najdu skutecne EDN
  for ($i = 0; $i< $pocetJizd; $i++)
  {  
   $stejnychAdelsich =  $pocetJizd - $i;  
   //echo "budu porovnavat jestli " .$stejnychAdelsich .  ">=" . $rides[$i]->distanceKM .   "<br>";
    if ($stejnychAdelsich >= $rides[$i]->distanceKM) 
         {                             
           $edn = $rides[$i]->distanceKM; //aktualni EDN 
      //     echo "ride# " . ($i+1) . " (". $stejnychAdelsich . ") " .  $rides[$i]->distanceKM . " km -> EDN= ".$edn."<br>";            
        }
        else
        {
         if ($edn == -1)
          $edn = $stejnychAdelsich; //mam vsechny jizdy delsi nez pocet jizd: EDN urcuje pocet jizd
         break;
        }
  }
  
  if ($edn < 0)   //nemam zadne jizdy
      $edn = 0;
      
  return $edn;
}     


 function PlanEddingtonNumber($rides, $planEDN)
{
   //sice tridim opakovane, ale pokud uz je pole setridene, tak to asi bude velmi rychle
	 usort($rides, array("Ride", "CompareRides"));
   
   $missingRides = $planEDN; //max pokud nemam jeste nic  
   $pocetJizd = sizeof  ($rides); 
 
 //hledam pocet jizd pro planovane EDN
   for ($i = 0; $i< $pocetJizd; $i++)
   { 
      $stejnychAdelsich =  $pocetJizd - $i;  
      
      //echo "ride# " . ($i+1) . " (". $stejnychAdelsich . ") " .  $rides[$i]->distanceKM . " km <br>";
      //hledam prvni jizdu, ktera je stejna nebo delsi nez plan
      if  ($rides[$i]->distanceKM >= $planEDN)      
      {
       //muzu tu byt jen jednou!             
       $missingRides =  $planEDN -  $stejnychAdelsich;
      // echo "I AM IN <br>";
       //echo "missing rides =".$missingRides." (" . $planEDN . "-" . $stejnychAdelsich .") <br>";
       break;
      }
   }
   
   
   if ($missingRides < 0)
      $missingRides = 0;
   
   return   $missingRides;
      }
      

  
?>