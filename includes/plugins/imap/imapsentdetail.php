<?php
/**
 * Created by PhpStorm.
 * User: iftekar
 * Date: 11/1/17
 * Time: 5:23 PM
 */
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
//$folders = $imap->getFolders();
//foreach($folders as $folder)
//{
//    print_r($folder);
//
//}
$imap->selectFolder('INBOX');

// count messages in current folder
$overallMessages = $imap->countMessages();
$unreadMessages = $imap->countUnreadMessages();
$emails = $imap->getMessages();
//echo "<br/>";
//var_dump($emails);
foreach ($emails as $key=>$email){

    /*print_r($key);
    echo "<br/>";
    print_r($email);
    echo "===============";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";*/

    //echo "id==".$email['id'];
    //echo "from==".$email['from'];
    //echo "<br/>";
    $messageheader=$imap->getMessageHeader($email['id']);
    /* $messageheader=$imap->getMessageHeader($email['id']);
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
     echo "<br/>";*/
}

//print_r($messageheader);
//echo "<br/>";
//print_r($msgbody);
/*echo "<pre>";
print_r($msgbody);
//print_r($messageheader->from[0]->host);
echo "</pre>";*/
$imap->selectFolder('INBOX.Sent');
$messageheader=$imap->getMessageHeader(@$_GET['id']);
/*echo "<pre>";
print_r($messageheader);
//print_r($messageheader->from[0]->host);
echo "</pre>";*/
$msgbody=$imap->getBody(@$_GET['id']);
/*echo "<pre>";
print_r(imap_base64($msgbody['body']));
echo "</pre>";
echo*/
$pos = strpos($msgbody['body'], 'base64');
$msgbody['body']=substr($msgbody['body'],$pos+6);

// count messages in current folder
$overallMessages = $imap->countMessages();

global $AI;
$AI->skin->css('includes/plugins/imap/style.css');
?>

<div class="mailinbox">
    <div class="mailinboxblock">
        <div class="mailinboxheader">
            <h2><span>Mail</span> </h2>
        </div>
        <div class="mailinboxwrapper">
            <!-- Main content -->
            <section class="content">
                <div class="row row-eq-height">
                    <!-- /mailleft.col -->
                    <div class="col-md-2 col-sm-3 col-xs-12 mailinboxleft">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">Folders</h3>
                                <div class="box-tools">
                                    <button type="button" class="btn btn-box-tool" class="navbar-toggle" data-toggle="collapse"  data-target="#navbar-collapse-1"><span class="glyphicon glyphicon-minus"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body no-padding navbar-collapse" id="navbar-collapse-1">
                                <ul class="nav nav-pills nav-stacked">
                                    <li class="active"><a href="/~nexmed/imapinbox"><span class="glyphicon glyphicon-inbox"></span>Inbox <span class="label label-green pull-right"><?php echo count($emails) ; ?></span></a></li>
                                    <!--<li><a href="#"><span class="glyphicon glyphicon-star"></span> Starred <span class="label label-yellow pull-right">12</span></a></li>
                                    <li><a href="#"><span class="glyphicon glyphicon-bookmark"></span> Important</a></li>-->
                                    <li><a href="/~nexmed/imapsentbox"><span class="glyphicon glyphicon-envelope"></span> Sent Mail <span class="label label-red pull-right"><?php echo $overallMessages; ?></span></a></li>
                                    <!--<li><a href="#"><span class="glyphicon glyphicon-pencil"></span> Drafts</a></li>
                                    <li><a href="#">More <span class="glyphicon glyphicon-chevron-down"></span> </a></li>-->
                                </ul>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                    <!-- /mailleft.col -->
                    <!-- /mailright.col -->
                    <div class="col-md-10 col-sm-9 col-xs-12 mailinboxright readmailinboxouterwrapper">
                        <div class="box box-primary readmailinboxwrapper">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                    <span class="mailbox-read-time pull-right">January 17, 2014 at 04:45 AM</span>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <div class="mailbox-controls with-border">
                                    <div class="pull-left readmailheadercontrol">
                                        <button type="button" class="btn replybtn"><i class="glyphicon glyphicon-arrow-left"></i> Forward</button>

                                        <button type="button" class="btn forwardbtn"><i class="glyphicon glyphicon-arrow-right"></i> Forward</button>
                                        <button type="button" class="btn trashbtn"><i class="glyphicon glyphicon-trash"></i> Trash</button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <div class="mailbox-read-info">
                                    <h5 class="form-control"><span class="span1">From</span> <span class="span2"> <?php echo $messageheader->toaddress; ?> </span></h5>
                                    <h5 class="form-control"><span class="span1">Subject</span> <span class="span2"><?php echo $messageheader->subject ; ?></span> </h5>
                                </div>
                                <!-- /.mailbox-read-info -->

                                <!-- /.mailbox-controls -->
                                <div class="mailbox-read-message">
                                    <?php echo $imap->convertToUtf8($msgbody['body']); ?>
                                </div>
                                <!-- /.mailbox-read-message -->
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <!--<ul class="mailbox-attachments clearfix hide">
                                    <li>
                                                      <span class="mailbox-attachment-icon">
                                                          <i class="fa fa-file-pdf-o"></i></span>

                                        <div class="mailbox-attachment-info">
                                            <a href="#" class="mailbox-attachment-name"><span>Attachment</span> <i class="glyphicon glyphicon-paperclip"></i> Oct2016-report.pdf</a>
                                            <span class="mailbox-attachment-size">
                                                              1,245 KB
                                                              <a href="#" class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-cloud-download"></i></a>
                                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="mailbox-attachment-icon"><i class="fa fa-file-word-o"></i></span>

                                        <div class="mailbox-attachment-info">
                                            <a href="#" class="mailbox-attachment-name"><span>Attachment</span> <i class="glyphicon glyphicon-paperclip"></i> Test description.docx</a>
                                            <span class="mailbox-attachment-size">
                                                              1,245 KB
                                                              <a href="#" class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-cloud-download"></i></a>
                                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="mailbox-attachment-icon has-img"><img src="../../../images/adminlogo.png" alt="Attachment"></span>

                                        <div class="mailbox-attachment-info">
                                            <a href="#" class="mailbox-attachment-name"><span>Attachment</span><i class="glyphicon glyphicon-picture"></i> photo1.png</a>
                                            <span class="mailbox-attachment-size">
                                                              2.67 MB
                                                              <a href="#" class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-cloud-download"></i></a>
                                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="mailbox-attachment-icon has-img"><img src="../../../images/adminlogo.png" alt="Attachment"></span>

                                        <div class="mailbox-attachment-info">
                                            <a href="#" class="mailbox-attachment-name"><span>Attachment</span><i class="glyphicon glyphicon-picture"></i> photo2.png</a>
                                            <span class="mailbox-attachment-size">
                                                              1.9 MB
                                                              <a href="#" class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-cloud-download"></i></a>
                                                            </span>
                                        </div>
                                    </li>
                                </ul>-->
                            </div>
                        </div>
                        <!-- /. box -->
                    </div>
                    <!-- /mailright.col -->
                </div>
            </section>
        </div>
    </div>
</div>