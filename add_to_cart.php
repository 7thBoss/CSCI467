<?php
	include "functions.php";

	//Get order_id
	$order_id = get_order_id($_POST["customer"]);

	//Create order if the order does not exist
	if (!is_numeric($order_id))
	{
		sql_insert("INSERT INTO orders (customer_id, order_status) VALUES(?, 'Selected')", [$_POST["customer"]]);
		$order_id = get_order_id($_POST["customer"]);
	}
	
	//If the quantity is blank, treat it as 1
	if ($_POST["quantity"] == "") $_POST["quantity"] = 1;
	
	//Insert part into order_parts
	$values = [$_POST["quantity"], $_POST["part_num"], $order_id];
	
	//If the part doesn't exist, create a new entry
	if (empty(sql_select("SELECT * FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $order_id])))
		sql_insert("INSERT INTO order_parts (quantity, part_num, order_id) VALUES(?, ?, ?)", $values);
	//If the part exists, update the quantity
	else
		sql_update("UPDATE order_parts SET quantity = quantity+? WHERE part_num=? AND order_id=?", $values);

	//Return to catalog
	echo "<form action='".$url."/browse_catalog.php' method='POST' id='complete'>
			<input type='hidden' name='customer' value='".$_POST["customer"]."'>
		  </form>";
		 
?>
<script type="text/javascript">
    document.getElementById('complete').submit();
</script>

