<?php

include 'functions.php';

$parts = legacy_sql_query('SELECT number FROM parts', []);

$part_ctr = 0;
$value = 0;  // value to set order to
while (gettype($parts[$part_ctr]['number']) != gettype($empty))
{
    echo $parts[$part_ctr]['number'];
    //echo '</br>';
    $part = sql_select('SELECT part_num FROM warehouse_parts where part_num = ?',[$parts[$part_ctr]['number']]);
    echo '    Looking for part  ';
    
    if(gettype($part[0]['part_num']) == gettype($empty))
  
    {
        echo ' part not found : Inserting Part</br>';
        sql_insert('INSERT INTO warehouse_parts (part_num, quantity) VALUES (?, ?)',[$parts[$part_ctr]['number'],$value]);
    }  
    else
    {
        echo 'part found: Part not inserted</br>';
    }
    $part_ctr = $part_ctr + 1;
    //echo $parts[$part_ctr]['number'];
}

echo 'done';
//


//First query parts for list of parts

// then check to see if part is in database
//   if is not, insert in with 0 for onhand and quantity for inventory values
// Nest, increment counter
 
/* Depending on design, end here, so nothing is desplayed
//       code would need to be added to another page to run with that page
//              would not be best as would run anytime that page is loaded in, ie refreshed or accessed
                but would have the most updated information about stock
                  could be on the recevings page
//  or would desplay a message with the number of parts now in the database
//    and a button to direct to new page
         would not run unless page is loading in by specificaly chousing page 
             so not running every time and wasting time
         but would need to be manualy run 
            so if new parts are added to parts legacydb someone would need to run this to add them causing delays
            if revecing gets new parts that have not been added 
*/
?>