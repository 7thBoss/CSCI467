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

	//Remove quantity 
	sql_update("UPDATE order_parts SET quantity = quantity-? WHERE part_num=? AND order_id=?", [$_POST["quantity"], $_POST["part_num"], $order_id]);
	
	//If the part is completely removed, remove it from the list
	if (sql_select("SELECT quantity FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $order_id])[0][0] == 0)
	{
		sql_delete("DELETE FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $order_id]);
		
		//If the order no longer has items, remove it and return the customer to the catalog
		if(sql_select("SELECT COUNT(*) FROM order_parts WHERE order_id=?", [$order_id])[0][0] == 0)
		{
			sql_delete("DELETE FROM orders WHERE order_id=?", [$order_id]);
			
			//Return to catalog
			echo "<form action='".$url."/browse_catalog.php' method='POST' id='complete'>
					<input type='hidden' name='customer' value='".$_POST["customer"]."'>
				  </form>
				  
				  <script type='text/javascript'>
					document.getElementById('complete').submit();
				  </script>";
		}
	}

	//Return to checkout
	echo "<form action='".$url."/checkout.php' method='POST' id='complete'>
			<input type='hidden' name='customer' value='".$_POST["customer"]."'>
		  </form>";
		  
?>
<script type="text/javascript">
    document.getElementById('complete').submit();
</script>
