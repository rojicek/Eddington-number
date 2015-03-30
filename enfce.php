<?php
//calcula
function GetEN($distances)
   {
   $pocetJizd = sizeof ($distances);
   $edn = 0;
  
   if (!sort($distances, SORT_NUMERIC ))
   {
    //sort failed!
     return null;
     }
   
  for ($ix = 0; $ix <$pocetJizd; $ix++)
   {     
      $stejnychAdelsich = ($pocetJizd - $ix);
      
      if ($stejnychAdelsich>=$distances[$ix])         
         $edn = $distances[$ix];
        else
         break;      
   }

    return $edn;
  }
?>