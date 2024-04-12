<?php
	include "functions.php";
	
	//Test insert
	sql_insert("INSERT INTO orders (customer_id, order_status) VALUES(?, 'Selected')", [1]);


	//Test select
	$results = sql_select("SELECT * FROM orders");
	
	foreach($results as $result)
	{
		echo $result["order_id"]." ".$result["customer_id"]." ".$result["order_status"]."<br>";
	}
	
	echo "<br>";
	
	
	//Test select with parameters
	$results = sql_select("SELECT * FROM orders WHERE order_id = ? AND order_status = ?", [2, "Selected"]);
	
	foreach($results as $result)
	{
		echo $result["order_id"]." ".$result["customer_id"]." ".$result["order_status"]."<br>";
	}
	
	echo "<br>";
	
	
	//Test update
	sql_update("UPDATE orders SET order_status = ? WHERE order_id = ?", ["Paid", 1]);
	$results = sql_select("SELECT * FROM orders");
	
	foreach($results as $result)
	{
		echo $result["order_id"]." ".$result["customer_id"]." ".$result["order_status"]."<br>";
	}
?>
