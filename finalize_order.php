<?php
	include "functions.php";
	
	//sql_insert("INSERT INTO orders (customer_id, order_status) VALUES(?, ?)", array(1, "Selected"));
	
	echo "Hello<br>";
	
	/*
	$dsn = "mysql:host=courses;dbname=z1977114";
	$pdo = new PDO($dsn, "z1977114", "2001Jul07");
	$results = $pdo->query("SELECT * FROM orders");
	/**/
	
	$requests = sql_select("SELECT * FROM orders");
	
	foreach($results as $result)
	{
		echo $result["order_id"]." ".$result["customer_id"]." ".$result["order_status"]."<br>";
	}
	
	echo "Hello Again";
	
	/*/Pack creditcard information
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
	/**/
?>
