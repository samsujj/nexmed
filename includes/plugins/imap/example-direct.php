<?php



namespace program;

require_once "ImapClient/ImapClientException.php";
require_once "ImapClient/ImapConnect.php";
require_once "ImapClient/ImapClient.php";

use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;




$mailbox = 'galaxy.apogeehost.com';
$username = 'dev007@nexmedsolutions.com';
$password = 'P@ss0987';
$encryption = Imap::ENCRYPT_TLS; // or ImapClient::ENCRYPT_SSL or ImapClient::ENCRYPT_TLS or null

// open connection

try{
    $imap = new Imap($mailbox, $username, $password, $encryption);

    /*
     * Or use advanced connect option like this
     *
    $imap = new ImapClient([
        'flags' => [
            'service' => ImapConnect::SERVICE_IMAP,
            'encrypt' => ImapConnect::ENCRYPT_SSL,
            'validateCertificates' => ImapConnect::VALIDATE_CERT,
        ],
        'mailbox' => [
            'remote_system_name' => 'imap.server.ru',
            'port' => 431,
        ],
        'connect' => [
            'username' => 'user',
            'password' => 'pass'
        ]
    ]);
    */

}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL;
    die();
}

// get all folders as array of strings
$folders = $imap->getFolders();
foreach($folders as $folder)
{
    print_r($folder);

}
$imap->selectFolder('INBOX');

// count messages in current folder
echo $overallMessages = $imap->countMessages();
echo $unreadMessages = $imap->countUnreadMessages();
$emails = $imap->getMessages();
echo "<br/>";
//var_dump($emails);
foreach ($emails as $key=>$email){

    print_r($key);
    echo "<br/>";
    print_r($email);
    echo "===============";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";

    echo "id==".$email['id'];
    echo "<br/>";
    $messageheader=$imap->getMessageHeader($email['id']);
    print_r($messageheader->from[0]->mailbox);
    print_r($messageheader->from[0]->host);
    echo "<pre>";
    print_r($messageheader);
    echo "<br/>";
    echo "</pre>";
    echo "<br/>";
    foreach ($email as $k=>$content){
        var_dump($content);
        echo "<br/>";
        var_dump($k);
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";

    }
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
}

echo "<br/>";
echo "<br/>";
echo "<br/>";
echo "Sent Box !!";
echo "<br/>";
echo "<br/>";


$imap->selectFolder('INBOX.Sent');

// count messages in current folder
echo $overallMessages = $imap->countMessages();
echo $unreadMessages = $imap->countUnreadMessages();
$emails = $imap->getMessages();
echo "<br/>";

foreach ($emails as $key=>$email){

    print_r($key);
    echo "<br/>";
    print_r($email);
    echo "===============";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";

    $messageheader=$imap->getMessageHeader($email['id']);
    print_r($messageheader->from[0]->mailbox);
    print_r($messageheader->from[0]->host);
    echo "<pre>";
    print_r($messageheader);
    echo "</pre>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";

}



$dmy=date("d-M-Y H:i:s");
$msg2='';
$dmy.= " +0100"; // Had to do this bit manually as server and me are in different timezones
$stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX.Sent", $username, $password);
$boundary = "------=".md5(uniqid(rand()));
$header = "MIME-Version: 1.0\r\n";
$header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
$header .= "\r\n";
//$file="../path_to/filename.pdf";
//$filename="filename.pdf";
//$ouv=fopen ("$file", "rb");$lir=fread ($ouv, filesize ("$file"));fclose
//($ouv);
//$attachment = chunk_split(base64_encode($lir));
$msg2 .= "$boundary\r\n";
$msg2 .= "Content-Transfer-Encoding: base64\r\n";
//$msg2 .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
$msg2 .= "\r\n";
$msg2 .= 'This is Message !!' . "\r\n";
$msg2 .= "\r\n\r\n";
$msg3 = "$boundary\r\n";
//imap_mail (  'debasiskar007@gmail.com' ,  'This is the subject\r\n' ,  "$msg2\r\n"."$msg3\r\n");
//imap_append($stream,"{galaxy.apogeehost.com/novalidate-cert}INBOX.Sent","From: $username \r\n"."To: debasiskar007@gmail.com\r\n"."Date: $dmy\r\n"."Subject: This is the subject\r\n"."$header\r\n"."$msg2\r\n"."$msg3\r\n");
$boundary = "------=".md5(uniqid(rand()));
imap_append($stream, "{galaxy.apogeehost.com}INBOX.Sent"
    , "From:  $username \r\n"
    . "To: debasiskar007@gmail.com\r\n"
    . "Subject: test\r\n"
    ."\r\n"
    ." $msg2 \r\n"
);
imap_close ($stream);



// select folder Inbox
/*$imap->selectFolder('INBOX');

// count messages in current folder
$overallMessages = $imap->countMessages();
$unreadMessages = $imap->countUnreadMessages();

// fetch all messages in the current folder
$emails = $imap->getMessages();
var_dump($emails);

// add new folder for archive
$imap->addFolder('archive');

// move the first email to archive
$imap->moveMessage($emails[0]['uid'], 'archive');

// delete second message
$imap->deleteMessage($emails[1]['uid']);*/
