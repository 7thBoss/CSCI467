<?php
	include "functions.php";
	
	//If the quantity is blank, treat it as 1
	if ($_POST["quantity"] == "") $_POST["quantity"] = 1;

	//By default, return to the checkout
	$return = "checkout";

	//Remove quantity 
	sql_update("UPDATE order_parts SET quantity = quantity-? WHERE part_num=? AND order_id=?", [$_POST["quantity"], $_POST["part_num"], $_POST["order_id"]]);
	
	//If the part is completely removed, remove it from the list
	if (sql_select("SELECT quantity FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $_POST["order_id"]])[0][0] == 0)
	{
		sql_delete("DELETE FROM order_parts WHERE part_num=? AND order_id=?", [$_POST["part_num"], $_POST["order_id"]]);
		
		//If the order no longer has items, return to the catalog
		if(sql_select("SELECT COUNT(*) FROM order_parts WHERE order_id=?", [$_POST["order_id"]])[0][0] == 0)
			$return = "browse_catalog";
	}

	//Return from script
	echo "<form action='".$url."/".$return.".php' method='POST' id='complete'>
			<input type='hidden' name='order_id' value='".$_POST["order_id"]."'>
		  </form>";
		  
?>
<script type="text/javascript">
    document.getElementById('complete').submit();
</script>
