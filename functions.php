<?php
	//Reports all errors when un-commented
	error_reporting(E_ALL & E_STRICT);
	ini_set('display_errors', '1');
	ini_set('log_errors', '0');
	ini_set('error_log', './');
	/**/

	//User field for sql and directory calls
	$user = "z1977114";
	
	//Url field for the main directory
	$url = "https://students.cs.niu.edu/~".$user."/CSCI467";

	/*	Sends an email as auto.system.mailer.
	 *	$to represents the email of the reciever
	 *	$subject represents the email header
	 *	$message represents the body of the email
	 */
	function send_email($to, $subject, $message)
	{
		//Get parameters and prepare them
		$data = ['to' => $to, 'subject' => $subject, 'html' => $message];
		
		$curl = curl_init();

		//Send email
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.mailgun.net/v3/sandbox0d6f068e979b45f3848e5ca327631ff1.mailgun.org/messages?from=auto.system.mailer%40gmail.com&'.http_build_query($data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic YXBpOmMwMDczZGJhNWI1ZWY0Y2E1YzlmNzE0NzkzMDEyNjc4LTg2MjIwZTZhLWEyNmI5YTlj'
			),
		));

		curl_exec($curl);

		curl_close($curl);
	}

	/*	Creates connection to legacy database and queries database
	 *	$query represents an SQL Query. Only SELECT statements should be used
	 *	$data represents an array of values to search by
	 */
	function legacy_sql_query($query, $data = [])
	{
		try
		{
			//If the legacy PDO isn't set, make a new one
			if (!isset($GLOBALS["legacy_pdo"]))
			{
				$dsn = "mysql:host=blitz.cs.niu.edu;port=3306;dbname=csci467";
				$pdo = new PDO($dsn, "student", "student");
				$GLOBALS["legacy_pdo"] = $pdo;
			}
			//Otherwise, use the created one
			else
				$pdo = $GLOBALS["legacy_pdo"];
				
			//Return JSON object
			$select = $pdo->prepare($query);
			$select->execute($data);
			return $select->fetchAll();
		}
		
		//Catch database errors
		catch(PDOexception $e)
		{
			echo "Connection to database failed: " . $e->getMessage();
		}
	}
	
	//Establish connection to database
	function connection()
	{
		try
		{
			//If the PDO isn't set, make a new one
			if (!isset($GLOBALS["pdo"]))
			{
				$dsn = "mysql:host=courses;port=3306;dbname=".$GLOBALS["user"];
				$pdo = new PDO($dsn, $GLOBALS["user"], "2001Jul07");
				$GLOBALS["pdo"] = $pdo;
			}
			//Otherwise, use the created one
			else
				$pdo = $GLOBALS["pdo"];
				
			return $pdo;
		}
		
		//Catch database errors
		catch(PDOexception $e)
		{
			echo "Connection to database failed: " . $e->getMessage();
		}
	}
	
	/*	Handles SELECT statements for database
	 *	$query represents an SQL Query. Only SELECT statements should be used
	 *	$data represents an array of values to search by
	 */
	function sql_select($query, $data = [])
	{
			$select = connection()->prepare($query);
			$select->execute($data);
			return $select->fetchAll();
	}
	
	/*	Handles INSERT statements for database
	 *	$query represents an SQL Query. Only INSERT statements should be used
	 *	$data represents an array of values to insert, not optional
	 */
	function sql_insert($query, $data = [])
	{
			$insert = connection()->prepare($query);
			$insert->execute($data);
	}
	
	/*	Handles UPDATE statements for database
	 *	$query represents an SQL Query. Only UPDATE statements should be used
	 *	$data represents an array of values to search and update, not optional
	 */
	function sql_update($query, $data)
	{
			$update = connection()->prepare($query);
			$update->execute($data);
	}
	
	/*	Handles DELETE statements for database
	 *	$query represents an SQL Query. Only DELETE statements should be used
	 *	$data represents an array of values to search and update, not optional
	 */
	function sql_delete($query, $data)
	{
			$delete = connection()->prepare($query);
			$delete->execute($data);
	}
	
	/*	Returns the shipping and handling cost by the weight of a given order
	 *	$weight represents total weight of the order
	 */
	function get_shipping_cost_by_weight($weight)
	{
		$price = sql_select("SELECT price FROM shipping_cost WHERE ? > min_weight AND ? < max_weight", [$weight, $weight]);
		
		if ($price)
			return $price[0][0];
		else
			return 1.00;
	}
	
	/*	Returns the total price of a given order
	 *	$order_id represents the order_id of a given order
	 */
	function total_price($order_id)
	{
		//Initialize total price
		$total_price = 0;
		
		//Get a list of parts in the cart 
		$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$order_id]);
		
		//Search the legacy database for the matchining part
		foreach($order_parts as $order_part)
			$total_price += legacy_sql_query("SELECT price FROM parts WHERE number=?", [$order_part["part_num"]])[0][0] * $order_part["quantity"];
				
		return $total_price;
	}
	
	/*	Returns the total weight of a given order
	 *	$order_id represents the order_id of a given order
	 */
	function total_weight($order_id)
	{
		//Initialize total weight
		$total_weight = 0;
		
		//Get a list of parts in the cart 
		$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$order_id]);
		
		//Search the legacy database for the matchining part
		foreach($order_parts as $order_part)
			$total_weight += legacy_sql_query("SELECT weight FROM parts WHERE number=?", [$order_part["part_num"]])[0][0] * $order_part["quantity"];
				
		return $total_weight;
	}
?>
