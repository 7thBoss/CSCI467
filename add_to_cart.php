<?php
	include "functions.php";
	
	//Get order_id
	$order_id = sql_select("SELECT order_id FROM orders WHERE customer_id=? AND order_status='Selected'", [$_SESSION["customer_id"]]);
		
	//Create order if the order does not exist
	if (empty($order_id))
	{
		sql_insert("INSERT INTO orders (customer_id, order_status) VALUES(?, 'Selected')", [$_SESSION["customer_id"]]);
		$order_id = sql_select("SELECT order_id FROM orders WHERE customer_id=? AND order_status='Selected'", [$_SESSION["customer_id"]]);
	}

	print_r($_POST);

	//Add part to order
	if ($_POST["quantity"] == "") $_POST["quantity"] = 1;
	
	//Insert part into order_parts
	$values = [$_POST["quantity"], $_POST["part_num"], $order_id[0]['order_id']];
	if (empty(sql_select("SELECT * FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $order_id[0]['order_id']])))
		sql_insert("INSERT INTO order_parts (quantity, part_num, order_id) VALUES(?, ?, ?)", $values);
	//if it exists, update the quantity
	else
		sql_update("UPDATE order_parts SET quantity = quantity+? WHERE part_num=? AND order_id=?", $values);

	//Return to browse_catalog.php
	header("Location:https://students.cs.niu.edu/~z1977114/CSCI467/browse_catalog.php");
?>
