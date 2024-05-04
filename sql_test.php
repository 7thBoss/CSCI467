<?php
	include "functions.php";
	
	//Test legacy select
	$results = legacy_sql_query("SELECT * FROM parts LIMIT 5");
	
	foreach($results as $result)
		echo $result["description"]." ".$result["price"]." ".$result["weight"]."<br>";
	echo "<br>";
	
	//Test legacy select with options
	$results = legacy_sql_query("SELECT * FROM parts WHERE number = ?", [1]);
	
	foreach($results as $result)
		echo $result["description"]." ".$result["price"]." ".$result["weight"]."<br>";
	echo "<br>";

	//Test insert
	sql_insert("INSERT INTO orders (order_status, order_date) VALUES('Selected', ?)", [date("Y-m-d H:i:s")]);

	//Test select
	$results = sql_select("SELECT * FROM orders");
	
	foreach($results as $result)
		echo $result["order_id"]." ".$result["order_status"]." ".$result["order_date"]."<br>";
	echo "<br>";
	
	
	//Test select with parameters
	$results = sql_select("SELECT * FROM orders WHERE order_id = ? AND order_status = ?", [2, "Selected"]);
	
	foreach($results as $result)
		echo $result["order_id"]." ".$result["order_status"]." ".$result["order_date"]."<br>";
	echo "<br>";
	
	
	//Test update
	sql_update("UPDATE orders SET order_status = ? WHERE order_id = ?", ["Paid", 1]);
	$results = sql_select("SELECT * FROM orders");
	
	foreach($results as $result)
		echo $result["order_id"]." ".$result["customer_id"]." ".$result["order_status"]."<br>";
?>
