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
	 usort($rides, array("Ride", "CompareRides"));
   
   
/*   //debug
   $pocetJizd = sizeof  ($rides); 
   for ($ix = 0; $ix <$pocetJizd; $ix++)
    echo "ride=" . ($ix+1) . " (". ($pocetJizd - $ix) . ") " .  $rides[$ix]->distanceKM . " km<br>";
   echo "<p>";  
    //konec debug
	*/
 
    
	$i = sizeof($rides);  
 // echo "rides = " . $i . "<br>";
     
	foreach ($rides as $ride) 
	{    
   // echo "ride=" . $ride->distanceKM . " km; i=" . $i . "<br>"; 
		if ($ride->distanceKM < $i)     			            
      $i--; 
      else
      break;                
	}
  
//	  echo "i = " . $i . "<br>";  
 //   echo "<p>"; 
    
  //debug
  $nextEDdistance = -1;
  $nextEDcount = -1;
  
  return array ($i, $nextEDdistance, $nextEDcount);
}
  
?>