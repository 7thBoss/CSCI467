<?php
	//include functions.php
	
	
	
	//Pack creditcard information
	$data = array(
		'vendor' => 'VE001-99',
		'trans' => '907-987654321-296',
		'cc' => '6011 1234 4321 1234',
		'name' => 'Jane Doe', 
		'exp' => '12/2024', 
		'amount' => '654.32');

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
	$result = file_get_contents('http://blitz.cs.niu.edu/CreditCard/', false, $context);
	
	echo($result);
?>
