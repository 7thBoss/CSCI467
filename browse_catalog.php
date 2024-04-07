<?php
include "functions.php";

$results = legacy_sql_query("SELECT * FROM parts");

foreach($results as $result)
{
	echo $result["number"]."\n";	
}

?>
