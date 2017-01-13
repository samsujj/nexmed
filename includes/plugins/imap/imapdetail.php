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


global $AI;

$userid = $AI->user->userID;
$maildata = array('email'=>'dev007@nexmedsolutions.com','password'=>'P@ss0987');

$data = $AI->db->GetAll("SELECT * FROM user_mails WHERE userID = " . (int) $userid);

if(isset($data[0])){
    $password = base64_decode(base64_decode($data[0]['password']));

    $maildata =  array('email'=>$data[0]['email'],'password'=>$password);
}


$mailbox = 'galaxy.apogeehost.com';
$username = @$maildata['email'];
$password = @$maildata['password'];
$encryption = Imap::ENCRYPT_TLS; // or ImapClient::ENCRYPT_SSL or ImapClient::ENCRYPT_TLS or null

// open connection

try{
    $imap = new Imap($mailbox, $username, $password, $encryption);

}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL;
    die();
}

$cururl = 'imapdetail?id='.$_GET['id'];


$imap->selectFolder('INBOX');

$emails = $imap->getMessages();

$messageheader=$imap->getMessageHeader(@$_GET['id']);
$msgbody=$imap->getBody(@$_GET['id']);

$stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX.Sent", $username, $password);
$header=imap_fetchstructure($stream,@$_GET['id']);

$attchaments=$imap->getAttachment(@$_GET['id'],0);



//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/uploads/email_attach/'.$attchaments['name'], $attchaments['content']);

//$fp = fopen($attchaments['name'], 'w');




$stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX", $username, $password);
$structure = imap_fetchstructure($stream, @$_GET['id']);

$j=0;

$attachs = array();

if(isset($structure->parts) && count($structure->parts)) {



    for ($i = 0; $i < count($structure->parts); $i++) {
        if (isset($structure->parts[$i]->disposition) && strtoupper($structure->parts[$i]->disposition) == 'ATTACHMENT') {

            $attachs[$j] = array(
                'is_attachment' => false,
                'filename' => '',
                'name' => '',
                'attachment' => '',
                'size'=>0);

            if($structure->parts[$i]->bytes) {
                $attachs[$j]['size'] = number_format($structure->parts[$i]->bytes/1024,2);
            }

            if($structure->parts[$i]->ifdparameters) {
                foreach($structure->parts[$i]->dparameters as $object) {
                    if(strtolower($object->attribute) == 'filename') {
                        $attachs[$j]['is_attachment'] = true;
                        $attachs[$j]['filename'] = $object->value;
                    }
                }
            }

            if($structure->parts[$i]->ifparameters) {
                foreach($structure->parts[$i]->parameters as $object) {
                    if(strtolower($object->attribute) == 'name') {
                        $attachs[$j]['is_attachment'] = true;
                        $attachs[$j]['name'] = $object->value;
                    }
                }
            }

            if($attachs[$j]['is_attachment']) {
                $attachs[$j]['attachment'] = imap_fetchbody($stream, @$_GET['id'], $i+1);
                if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                    $attachs[$j]['attachment'] = base64_decode($attachs[$j]['attachment']);
                }elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                    $attachs[$j]['attachment'] = quoted_printable_decode($attachs[$j]['attachment']);
                }
            }

            $j++;
        }
    }
}


if(isset($_GET['mode']) && isset($_GET['attach_id'])){
    if($_GET['mode'] == 'download'){
        if(isset($attachs[$_GET['attach_id']])){
            $filename = $_SERVER['DOCUMENT_ROOT'].'/uploads/email_attach/'.rand().'_'.time().$attachs[$_GET['attach_id']]['name'];
            file_put_contents($filename, $attachs[$_GET['attach_id']]['attachment']);

            if(file_exists($filename)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($filename));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filename));
                ob_clean();
                flush();
                readfile($filename);

                @unlink($filename);

                exit;
            }
        }
    }
}


$count=0;
$attchmentstr='';


if(count($attachs)){
    foreach($attachs as $key=>$row){
        $attchmentstr .='<li>
                            <span class="mailbox-attachment-icon">
                                                          <i class="fa fa-file-pdf-o"></i></span>

                                        <div class="mailbox-attachment-info">
                                            <a href="'.$cururl.'&mode=download&attach_id='.$key.'" class="mailbox-attachment-name"><span>Attachment</span> <i class="glyphicon glyphicon-paperclip"></i> '.$row['filename'].'</a>
                                            <span class="mailbox-attachment-size">
                                                             '.$row['size'].' KB
                                                              <a href="'.$cururl.'&mode=download&attach_id='.$key.'" class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-cloud-download"></i></a>
                                                            </span>
                                        </div>
                                    </li>';
    }
}


$imap->selectFolder('INBOX.Sent');

// count messages in current folder
$overallMessages = $imap->countMessages();


global $AI;
$AI->skin->css('includes/plugins/imap/style.css');
?>

<div class="mailinbox">
    <div class="mailinboxblock">
        <div class="mailinboxheader">
            <h2><span>Mail</span> Inbox</h2>
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
                                    <span class="mailbox-read-time pull-right"><?php echo date('F d,Y h:i A',$messageheader->udate);?></span>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding" style="margin-top: 15px;">
                                <!--<div class="mailbox-controls with-border">
                                    <div class="pull-left readmailheadercontrol">
                                        <button type="button" class="btn replybtn"><i class="glyphicon glyphicon-arrow-left"></i> Forward</button>

                                        <button type="button" class="btn forwardbtn"><i class="glyphicon glyphicon-arrow-right"></i> Forward</button>
                                        <button type="button" class="btn trashbtn"><i class="glyphicon glyphicon-trash"></i> Trash</button>
                                    </div>
                                </div>-->
                                <div class="mailbox-read-info">
                                    <h5 class="form-control"><span class="span1">From</span> <span class="span2"> <?php echo $messageheader->from[0]->personal." < ".$messageheader->from[0]->mailbox."@".$messageheader->from[0]->host." >"; ?> </span></h5>
                                    <h5 class="form-control"><span class="span1">Subject</span> <span class="span2"><?php echo $messageheader->subject ; ?></span> </h5>
                                </div>
                                <!-- /.mailbox-read-info -->

                                <!-- /.mailbox-controls -->
                                <div class="mailbox-read-message">
                                    <?php echo $msgbody['body']; ?>
                                </div>
                                <!-- /.mailbox-read-message -->
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <ul class="mailbox-attachments clearfix hide">

                                    <?php echo $attchmentstr; ?>

                                </ul>
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