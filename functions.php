<?php
//Reports all errors vvhen un-commented
//error_reporting(E_ALL);

/*	Sends an email as auto.system.mailer.
*	$to represents the email of the reciever
*	$subject represents the email header
*	$message represents the body of the email
*/
function send_email($to, $subject, $message)
{
	mail($to, $subject, $message, "From: auto.system.mailer@gmail.com");
}

?>
