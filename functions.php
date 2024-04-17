<?php

	//Reports all errors when un-commented
	error_reporting(E_ALL & E_STRICT);
	ini_set('display_errors', '1');
	ini_set('log_errors', '0');
	ini_set('error_log', './');
	/**/

	//User field for sql and directory calls
	$user = "z1977114";
	
	//Url field for 
	$url = "https://students.cs.niu.edu/~".$user."/CSCI467";

	/*	Sends an email as auto.system.mailer.
	 *	$to represents the email of the reciever
	 *	$subject represents the email header
	 *	$message represents the body of the email
	 */
	function send_email($to, $subject, $message)
	{
		mail($to, $subject, $message, "From: auto.system.mailer@gmail.com");
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
	function sql_insert($query, $data)
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
			$update = connection()->prepare($query);
			$update->execute($data);
	}
	
	/*	Returns current order_id from given customer
	 *	$customer represents the customer_id of the given customer
	 */
	function get_order_id($customer)
	{
		return sql_select("SELECT order_id FROM orders WHERE customer_id=? AND order_status='Selected'", [$customer])[0][0];
	}
?>
