<?php
	include "functions.php";

	//By default, return to the catalog
	$return = "<form action='".$url."/browse_catalog.php' method='POST' id='complete'></form>";

	//Pack creditcard information
	$data = array(
		'vendor' => uniqid(),
		'trans' => uniqid(),
		'cc' =>  $_POST['cc'],
		'name' => $_POST["name"], 
		'exp' => $_POST["exp"], 
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
	
	//If errors occured, inform the user
	if(array_key_exists('errors', $result))
	{
		//Print error
		echo "Sorry, your transaction failed.<br>Reason: ".$result["errors"][0];
		
		//Return to the checkout instead
		$return = "<form action='".$url."/checkout.php' method='POST' id='complete'>
					  <input type='hidden' name='order_id' value='".$_POST["order_id"]."'>
				   </form>";
	}
	
	//Otherwise, continue with transaction
	else
	{
		//Get a list of parts in the cart 
		$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$_POST["order_id"]]);
		
		//Update quantity of parts in warehouse
		foreach($order_parts as $order_part)
			sql_update("UPDATE warehouse_parts SET quantity = quantity-? WHERE part_num=?", [$order_part["quantity"], $order_part["part_num"]]);
		
		//Update the status of the order
		sql_update("UPDATE orders SET order_status='Paid', order_date=?, customer_name=?, email=?, address=? WHERE order_id=?", [date("Y-m-d H:i:s"), $_POST["name"], $_POST["email"], $_POST["address"], $_POST["order_id"]]);
		
		//Send email to client
		send_email($_POST["email"], "Transaction Complete", "Thank you for your purchase. We're processing your order and it will arrive soon");
		
		echo "Success";
	}
	
	//Return to catalog
	echo $return;
		 
?>
<script type="text/javascript">
	setTimeout(function ()
	{
		document.getElementById('complete').submit();
	}, 3000);
</script>
