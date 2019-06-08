<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
   exit;
}

$user_email = $_POST["subscription-email"];
if(IsNullOrEmptyString($user_email)){
	die('email is empty(var)');
}
if(!IsValidEmail($user_email)){
	die('email not valid');
}

$count = 0;
$timeout_secs = 10; //number of seconds of timeout
$got_lock = true;

$my_file = 'newsletter-subscription.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file); //implicitly creates file

while (!flock($handle, LOCK_EX | LOCK_NB, $wouldblock)) {
    if ($wouldblock && $count++ < $timeout_secs) {
        sleep(1);
    } else {
        $got_lock = false;
        break;
    }
}
if ($got_lock) {
	$to_write = date("M,d,Y h:i:s A"). "\t" . $user_email . "\n";
	fwrite($handle, $to_write);
	fclose($handle);
}

function IsNullOrEmptyString($str){
    return (!isset($str) || trim($str) === '');
}
function IsValidEmail($email){
	$regex = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';
	if (!preg_match($regex, $email))
	{
 		return FALSE;
	}
	else
	{
 		return TRUE;
	}
}
?>