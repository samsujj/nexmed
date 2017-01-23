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

$send_from = $username;

$data = $AI->db->GetAll("SELECT * FROM users WHERE account_type = 'Representatives' ");
echo "<pre>";
//print_r(count($data));
//print_r($data);
$i=0;
foreach ($data as $val) {
    echo $val['username'];
    echo "<br>";
    //$emailid=explode('@',$val['email']);
    echo $emailid=$val['email']."@nexmedsolutions.com";
    echo "<br/>";
    //$AI->db->query("update users set email= '$emailid' where userID= " . (int)$val['userID']);
    $data = $AI->db->GetAll("SELECT * FROM user_mails WHERE userID = " . (int)$val['userID']);

    if (isset($data[0])) {
        $password = base64_decode(base64_decode($data[0]['password']));

        $maildata = array('email' => $data[0]['email'], 'pass' => $password);

        echo "<br/>";
        //echo $i++;
        //echo "</pre>";
//exit;


        $vars = array();
        $vars['email'] = $maildata['email'];
        $vars['pass'] = $maildata['pass'];
        //$vars = $maildata;


        $defaults = array();
//$defaults['email_subject'] = 'Default Email Subject';
//$defaults['email_msg'] = 'Hello [[name]], this is the default content of your email.';

        $se = new C_system_emails($email_name);
//$se->set_from($send_from);
        $send_to = $val['email'];
        $se->set_defaults_array($defaults);
        $se->set_vars_array($vars);
//$headers[0]= 'From: ' . $send_from . "\r\n";
//$headers[1]= "X-Sender: $send_from < ".$send_from." >\n";
        $myheaders = array('Bcc' => 'debasiskar007@gmail.com');
       // if (!$se->send($send_to,$myheaders)) {
            //echo 47;exit;
        //}
    }
}

echo "</pre>";
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