<?php
	//Reports all errors when un-commented
	error_reporting(E_ALL & E_STRICT);
	ini_set('display_errors', '1');
	ini_set('log_errors', '0');
	ini_set('error_log', './');
	/**/
	
	//Start session
	session_start();

	//User field for sql and directory calls
	$_SESSION["user"] = "z1977114";
	
	//Test Customer, NOT FINAL!
	$_SESSION["customer_id"] = 1;

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
	 */
	function legacy_sql_query($query)
	{
		//Establish connection to legacy database
		try
		{
			//If the PDO has already been generated, use it
			if (isset($_SESSION["legacyPDO"]))
				$pdo = $_SESSION["legacyPDO"];
			
			//Otherwise, make a new PDO
			else
			{
				$dsn = "mysql:host=blitz.cs.niu.edu;port=3306;dbname=csci467";
				$pdo = new PDO($dsn, "student", "student");
				$_SESSION["legacyPDO"] = $pdo;
			}

			//Return JSON object
			return $pdo->query($query);
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
		//Establish connection to database
		try
		{
			//If the PDO has already been generated, use it
			if (isset($_SESSION["PDO"]))
				$pdo = $_SESSION["PDO"];
			
			//Otherwise, make a new PDO
			else
			{
				$dsn = "mysql:host=courses;port=3306;dbname=".$_SESSION["user"];
				$pdo = new PDO($dsn, $_SESSION["user"], "2001Jul07");
				$_SESSION["PDO"] = $pdo;
			}
			
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
			//Return selected object
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
			//Prepare and execute insert command
			$insert = connection()->prepare($query);
			$insert->execute($data);
	}
	
	/*	Handles UPDATE statements for database
	 *	$query represents an SQL Query. Only UPDATE statements should be used
	 *	$data represents an array of values to search and update, not optional
	 */
	function sql_update($query, $data)
	{
			//Update entry
			$update = connection()->prepare($query);
			$update->execute($data);
	}
?>
