<?php
	include "functions.php";

	$customer = legacy_sql_query("SELECT * FROM customers WHERE id = ?", [$_POST["customer"]]);

	//Pack creditcard information
	$data = array(
		'vendor' => uniqid(),
		'trans' => uniqid(),
		//'vendor' => 'VE001-99',
		//'trans' => '907-987654321-296',
		'cc' =>  $_POST['cc'],
		'name' => $customer["name"], 
		'exp' => '12/2024', 
		'amount' => $_POST['price']);

	//Encode creditcard information
	$options = array(
		'http' => array(
			'header' => array('Content-type: application/json', 'Accept: application/json'),
			'method' => 'POST',
			'content'=> json_encode($data)
		)
	);

	//Test creditcard
	$context  = stream_context_create($options);
	$result = json_decode(file_get_contents('http://blitz.cs.niu.edu/CreditCard/', false, $context), true);
	
	//print_r($result);
	
	//If errors occured, inform the user
	if(array_key_exists('errors', $result))
	{
		echo "Sorry, your transaction failed.<br>Reason: ".$result["errors"][0];
	}
	
	//Otherwise, continue with transaction
	else
	{
		//Get a list of parts in the cart 
		$order_id = get_order_id($_POST["customer"]);
		$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$order_id]);
		
		//Update quantity of parts in warehouse
		foreach($order_parts as $order_part)
			sql_update("UPDATE warehouse_parts SET quantity = quantity-? WHERE part_num=?", [$order_part["quantity"], $order_part["part_num"]]);
		
		//Update the status of the order
		sql_update("UPDATE orders SET order_status='Paid' WHERE order_id=?", [$order_id]);
		
		//Send email to client
		send_email($_POST["email"], "Transaction Complete", "Thank you for your purchase. We're processing your order and it will arrive soon");
		
		echo "Success";
	}
	
	//Return to catalog
	echo "<form action='".$url."/browse_catalog.php' method='POST' id='complete'>
			<input type='hidden' name='customer' value='".$_POST["customer"]."'>
		  </form>";
		 
?>
<script type="text/javascript">
	setTimeout(function ()
	{
		document.getElementById('complete').submit();
	}, 3000);
</script>
