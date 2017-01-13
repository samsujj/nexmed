<?php
/**
 * Created by PhpStorm.
 * User: iftekar
 * Date: 11/1/17
 * Time: 2:27 PM
 */

$mailbox = 'galaxy.apogeehost.com';
$username = 'dev007@nexmedsolutions.com';
$password = 'P@ss0987';
//$encryption = Imap::ENCRYPT_TLS; // or ImapClient::ENCRYPT_SSL or ImapClient::ENCRYPT_TLS or null

// open connection


global $AI;
require_once( ai_cascadepath('includes/plugins/system_emails/class.system_emails.php') );
$email_name = 'repsignup';
$send_to = 'debasiskar007@gmail.com';
$send_from = $username;

$vars = array();
$vars['uname'] = $username;
$vars['pass'] = $username;

$defaults = array();
//$defaults['email_subject'] = 'Default Email Subject';
//$defaults['email_msg'] = 'Hello [[name]], this is the default content of your email.';

$se = new C_system_emails($email_name);
$se->set_from($send_from);
$se->set_defaults_array($defaults);
$se->set_vars_array($vars);
//$headers[0]= 'From: ' . $send_from . "\r\n";
//$headers[1]= "X-Sender: $send_from < ".$send_from." >\n";
/*if(!$se->send($send_to,array('Reply-To', $username)))
{
    //echo 47;exit;
}*/


//require 'PHPMailerAutoload.php';
/*require_once( ai_cascadepath('includes/plugins/imap/smtp.php') );
require_once( ai_cascadepath('includes/plugins/imap/phpmailer.php') );
require_once( ai_cascadepath('includes/plugins/imap/PHPMailerAutoload.php') );

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'galaxy.apogeehost.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'dev007@nexmedsolutions.com';                 // SMTP username
$mail->Password = 'P@ss0987';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->setFrom($username, 'Mailer');
$mail->addAddress('debasiskar007@gmail.com');     // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}*/