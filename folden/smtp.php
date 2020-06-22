<?php
require_once 'Mail.php';

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

set_include_path("." . PATH_SEPARATOR . ($UserDir = dirname($_SERVER['DOCUMENT_ROOT'])) . "/pear/php" . PATH_SEPARATOR . get_include_path());
require_once "Mail.php";

// $host = "ssl://smtp.dreamhost.com";
// $username = "youremail@example.com";
// $password = "your email password";
// $port = "465";
// $to = "address_form_will_send_TO@example.com";
// $email_from = "youremail@example.com";
// $email_subject = "Subject Line Here: " ;
// $email_body = "whatever you like" ;
// $email_address = "reply-to@example.com";

// $headers = array ('From' => $email_from, 'To' => $to, 'Subject' => $email_subject, 'Reply-To' => $email_address);
// $smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
// $mail = $smtp->send($to, $headers, $email_body);

$host = "ssl://smtp.dreamhost.com";
$username = "AKIAVVHU6V3GJERMER6A";
$password = "BCEXSVmdNZLvymWsmCl3xyBSN7gPjnszdhDwPRMDyMZ0";
$port = "465";
$to = "email-smtp.us-east-1.amazonaws.com";
$email_from = "noreply@dfx5.com";

$email_subject = "Subject Line Here: " ;
$email_body = "whatever you like" ;
$email_address = "reply-to@example.com";

$headers = array ('From' => $email_from, 'To' => $to, 'Subject' => $email_subject, 'Reply-To' => $email_address);
$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
$mail = $smtp->send($to, $headers, $email_body);


if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
} else {
echo("<p>Message successfully sent!</p>");
}
?>