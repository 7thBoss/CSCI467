<?php
	include "functions.php";

	//Pack creditcard information
	$data = array(
		'vendor' => 'VE001-99',
		'trans' => '907-987654321-296',
		'cc' =>  $_POST['cc'],
		'name' => $_POST['name'], 
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
	
	print_r($result);
	
	//If errors occured, inform the user
	if(array_key_exists('errors', $result))
	{
		echo "Sorry, your transaction failed.<br>Reason: ".$result["errors"][0];
	}
	
	//Otherwise, continue with transaction
	else
	{
		echo "Success";
		
		//Update the status of the order
		sql_update("UPDATE orders SET order_status='Paid' WHERE customer_id=? AND order_status='Selected'", [$_SESSION["customer_id"]]);
		
		//Send email to client
		send_email($_POST["email"], "Transaction Complete", "Thank you for your purchace, your package will be arriving soon");
	}
?>
