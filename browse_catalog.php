<?php
	include "functions.php";

	//Get parts to browse
	$results = legacy_sql_query("SELECT * FROM parts");

	//Display parts
	foreach($results as $result)
	{
		echo $result["number"]." ".$result["description"]." ".$result["price"]." ".$result["weight"]." ".$result["pictureURL"]."<br>";	
	}

?>
