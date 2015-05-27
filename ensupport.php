<?php
 
//Ride class
  
class Ride {
	public $date;
	public $distance;
  public $name;
	
	function Ride($date, $distance, $name, $revolutions) 
	{
		$this->date = $date;
		$this->distance = $distance;
    $this->name = $name;
    $this->revolutions = $revolutions;
    
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
   
    
     
   //abych poznal chybu
  $edn = 0;  
  $pocetJizd = sizeof  ($rides); 
//  echo "pocet jizd =  " .   $pocetJizd . "<br>";
 
 //zadna jizda
     if ($pocetJizd == 0)
     { //zadna jizda
   //  echo "zadna jizda<p>";
        return 0; 
     }
 //pokud je pocet jizd mensi nez minimalni, tak pocet jizd je edn
    if  ($pocetJizd <= $rides[0]->distance )
    {
    //  echo "malo jizd: pocet=".$pocetJizd. "<= min jizda:" . $rides[0]->distance . "<p>";
      return   $pocetJizd;
    }
           
 //debug
 /*
 for ($i = 0; $i < $pocetJizd; $i++) 
 echo "i=" . ($i+1) . " (". ($pocetJizd - $i) . ") " .  $rides[$i]->distance . " km<br>";
 echo "<p>";
   */ 
    // echo "posledni=" . $rides[$pocetJizd-1]->distance . "<p>";

         
  //najdu skutecne EDN
  for ($i = 0; $i< $pocetJizd; $i++)
  {  
   $stejnychAdelsich =  $pocetJizd - $i;  
 //  echo "budu porovnavat jestli " .$stejnychAdelsich .  ">=" . $rides[$i]->distance .   " (act EDN=". $edn .")<br>";
    if ($stejnychAdelsich >= $rides[$i]->distance) 
         {                             
            $edn = $rides[$i]->distance; //aktualni EDN 
        //    echo "ride# " . ($i+1) . " (". $stejnychAdelsich . ") " .  $rides[$i]->distance . " km -> EDN= ".$edn."<br>";            
        }
        else
        {
        //POTREBUJI
        //sikora bug: dalsi jizda uz nejde pouzit, ale predchozim kvalifikuji na vyssi edn
        
        $pocetDalsich =  $pocetJizd - $i;
        if ($edn < $pocetDalsich)
        {
               // echo "beru pocet dalsich " .    $pocetDalsich . "<br>";
                $edn =    $pocetDalsich;
                
          }
            
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
      
      //echo "ride# " . ($i+1) . " (". $stejnychAdelsich . ") " .  $rides[$i]->distance . " km/miles (plan=".$planEDN.")<br>";
      //hledam prvni jizdu, ktera je stejna nebo delsi nez plan
      if  ($rides[$i]->distance >= $planEDN)      
      {
       //muzu tu byt jen jednou!             
       $missingRides =  $planEDN -  $stejnychAdelsich ;
     //  echo "I AM IN <br>";
      // echo "missing rides =".$missingRides." (" . $planEDN . "-" . $stejnychAdelsich .") <br>";
       break;
      }
   }
   
    //dulezite kdyz pouziji mensi/stejny plan nez edn
   if ($missingRides < 0)
      $missingRides = 0;
   
   return   $missingRides;
      }
      

  
?>