<?php
//Reports all errors when un-commented
error_reporting(E_ALL & E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '0');
ini_set('error_log', './');
/**/

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
	try
	{
		//Establish connection to legacy database
		$dsn = "mysql:host=blitz.cs.niu.edu;port=3306;dbname=csci467";
		$pdo = new PDO($dsn, "student", "student");
		
		//Return JSON object
		return $pdo->query($query);
	}
	//Catch database errors
	catch(PDOexception $e)
	{
		echo "Connection to database failed: " . $e->getMessage();
	}
}

?>