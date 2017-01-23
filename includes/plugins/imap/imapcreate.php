<?php
/**
 * Created by PhpStorm.
 * User: Kaushik
 * Date: 1/11/2017
 * Time: 6:46 PM
 */

require_once( ai_cascadepath('includes/plugins/imap/imap.php') );
require_once( ai_cascadepath('includes/plugins/system_emails/class.system_emails.php') );
require_once( ai_cascadepath('includes/core/classes/email.php') );
require_once( ai_cascadepath( 'includes/core/upload/class.upload.php' ) );

require_once "ImapClient/ImapClientException.php";
require_once "ImapClient/ImapConnect.php";
require_once "ImapClient/ImapClient.php";

use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

global $AI;


$userid = $AI->user->userID;
$maildata = array('email'=>'dev007@nexmedsolutions.com','password'=>'P@ss0987','name'=>'Debasis Kar');

$data = $AI->db->GetAll("SELECT m.*,u.first_name,u.last_name FROM user_mails m INNER JOIN users u ON u.userID = m.userID WHERE u.userID = " . (int) $userid);

if(isset($data[0])){
    $password = base64_decode(base64_decode($data[0]['password']));

    $maildata =  array('email'=>$data[0]['email'],'password'=>$password,'name'=>$data[0]['first_name']." ".$data[0]['last_name']);
}

$mailbox = 'galaxy.apogeehost.com';
$username = @$maildata['email'];
$password = @$maildata['password'];
$encryption = Imap::ENCRYPT_TLS; // or ImapClient::ENCRYPT_SSL or ImapClient::ENCRYPT_TLS or null

try{
    $imap = new Imap($mailbox, $username, $password, $encryption);

}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL;
    die();
}


$toaddr = '';
$subject = '';
$pmailbody = '';

if(isset($_GET['type']) && isset($_GET['id'])){
    if($_GET['type'] == 'replyIn' || $_GET['type'] == 'forwardIn'){
        $mailid = $_GET['id'];
        $stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX", $username, $password);
        $uid = imap_uid($stream,$_GET['id']);

        $imap->selectFolder('INBOX');
        $messageheader=$imap->getMessageHeader($uid);
        $pmailbody=$imap->getBody($uid);
        $pmailbody=trim($pmailbody['body']);

        if($_GET['type'] == 'replyIn'){

            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress)){
                $bodybefore = 'On '.date('Y-m-d H:i',$messageheader->udate).', '.$messageheader->fromaddress.' wrote:<br>';
            }

            $pmailbody = $bodybefore.$pmailbody;

            if(isset($messageheader->reply_to)){
                $reply = $messageheader->reply_to;
                if(isset($reply[0])){
                    $toaddr = $reply[0]->mailbox."@".$reply[0]->host;
                }
            }

            $subject = $messageheader->subject;
            if(!empty($subject)){
                if(substr($subject, 0, 3) != 'Re:'){
                    $subject = 'Re: '.$subject;
                }
            }
        }

        if($_GET['type'] == 'forwardIn'){
            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress) && isset($messageheader->toaddress) && isset($messageheader->subject)){
                $bodybefore .= '-------- Original Message --------<br>';
                $bodybefore .= 'Subject: '.$messageheader->subject.'<br>';
                $bodybefore .= 'Date: '.date('Y-m-d H:i',$messageheader->udate).'<br>';
                $bodybefore .= 'From: '.$messageheader->fromaddress.'<br>';
                $bodybefore .= 'To: '.$messageheader->toaddress.'<br><br>';
            }

            $pmailbody = $bodybefore.$pmailbody;
            $subject = $messageheader->subject;
            $subject = 'Fwd: '.$subject;



        }

    }
    if($_GET['type'] == 'replySent' || $_GET['type'] == 'forwardSent'){
        $mailid = $_GET['id'];
        $stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX.Sent", $username, $password);
        $uid = imap_uid($stream,$_GET['id']);

        $imap->selectFolder('INBOX.Sent');
        $messageheader=$imap->getMessageHeader($uid);
        $pmailbody=$imap->getBody($uid);
        $pmailbody=trim($pmailbody['body']);


        if($_GET['type'] == 'replySent'){
            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress)){
                $bodybefore = 'On '.date('Y-m-d H:i',$messageheader->udate).', '.$messageheader->fromaddress.' wrote:<br>';
            }

            $pmailbody = $bodybefore.$pmailbody;

            if(isset($messageheader->reply_to)){
                $reply = $messageheader->reply_to;
                if(isset($reply[0])){
                    $toaddr = $reply[0]->mailbox."@".$reply[0]->host;
                }
            }

            $subject = $messageheader->subject;
            if(!empty($subject)){
                if(substr($subject, 0, 3) != 'Re:'){
                    $subject = 'Re: '.$subject;
                }
            }
        }

        if($_GET['type'] == 'forwardSent'){
            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress) && isset($messageheader->toaddress) && isset($messageheader->subject)){
                $bodybefore .= '-------- Original Message --------<br>';
                $bodybefore .= 'Subject: '.$messageheader->subject.'<br>';
                $bodybefore .= 'Date: '.date('Y-m-d H:i',$messageheader->udate).'<br>';
                $bodybefore .= 'From: '.$messageheader->fromaddress.'<br>';
                $bodybefore .= 'To: '.$messageheader->toaddress.'<br><br>';
            }

            $pmailbody = $bodybefore.$pmailbody;
            $subject = $messageheader->subject;
            $subject = 'Fwd: '.$subject;
        }

    }
    if($_GET['type'] == 'replyTr' || $_GET['type'] == 'forwardTr'){
        $mailid = $_GET['id'];
        $stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX.Trash", $username, $password);
        $uid = imap_uid($stream,$_GET['id']);

        $imap->selectFolder('INBOX.Trash');
        $messageheader=$imap->getMessageHeader($uid);
        $pmailbody=$imap->getBody($uid);
        $pmailbody=trim($pmailbody['body']);

        if($_GET['type'] == 'replyTr'){

            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress)){
                $bodybefore = 'On '.date('Y-m-d H:i',$messageheader->udate).', '.$messageheader->fromaddress.' wrote:<br>';
            }

            $pmailbody = $bodybefore.$pmailbody;

            if(isset($messageheader->reply_to)){
                $reply = $messageheader->reply_to;
                if(isset($reply[0])){
                    $toaddr = $reply[0]->mailbox."@".$reply[0]->host;
                }
            }

            $subject = $messageheader->subject;
            if(!empty($subject)){
                if(substr($subject, 0, 3) != 'Re:'){
                    $subject = 'Re: '.$subject;
                }
            }
        }

        if($_GET['type'] == 'forwardTr'){
            $bodybefore = '';
            if(isset($messageheader->udate) && isset($messageheader->fromaddress) && isset($messageheader->toaddress) && isset($messageheader->subject)){
                $bodybefore .= '-------- Original Message --------<br>';
                $bodybefore .= 'Subject: '.$messageheader->subject.'<br>';
                $bodybefore .= 'Date: '.date('Y-m-d H:i',$messageheader->udate).'<br>';
                $bodybefore .= 'From: '.$messageheader->fromaddress.'<br>';
                $bodybefore .= 'To: '.$messageheader->toaddress.'<br><br>';
            }

            $pmailbody = $bodybefore.$pmailbody;
            $subject = $messageheader->subject;
            $subject = 'Fwd: '.$subject;



        }

    }

    if($_GET['type'] == 'draft'){
        $mailid = $_GET['id'];

        $stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX.Drafts", $username, $password);
        $uid = imap_uid($stream,$_GET['id']);

        $imap->selectFolder('INBOX.Drafts');
        $messageheader=$imap->getMessageHeader($uid);
        $pmailbody=$imap->getBody($uid);
        $pmailbody=trim($pmailbody['body']);

        $subject = $messageheader->subject;
        $toaddr = $messageheader->toaddress;

    }

}


$stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX", $username, $password);


if(util_is_POST()) {

    $mailbody = $_POST['body'];
    $targetarr=array('"\"');
    $replacearr=array('"');
    //$mailbody=str_replace('"\"','"',$mailbody);
   // $mailbody = htmlentities($mailbody);
    $mailbody = stripslashes($mailbody);



  //  echo ($mailbody);
   // exit;

    $boundary = "------=".md5(uniqid(rand()));

    $msg1 = '';
    $msg2 = '';
    $msg3 = '';

    $header = "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    $header .= "\r\n";




    $msg1 .= "--$boundary\r\n";
    $msg1 .= "Content-Type: text/html;\r\n\tcharset=\"utf-8\"\r\n";
    $msg1 .= "Content-Transfer-Encoding: 8bit \r\n";
    $msg1 .= "\r\n\r\n" ;
    $msg1 .= html_entity_decode($mailbody)."\r\n";
    $msg1 .= "\r\n\r\n";
    $msg3 .= "--$boundary--\r\n";



    if(isset($_POST['ai_upload_add'])){
        foreach($_POST['ai_upload_add'] as $row){
            $file2 = $row;
            $file_arr = explode('|',$file2);
            $file= $_SERVER['DOCUMENT_ROOT'].'/uploads/files/'.$file_arr[0].'/'.$file_arr[1];

            $filename=$file_arr[1];
            $ouv=fopen ("$file", "rb");$lir=fread ($ouv, filesize ("$file"));fclose
            ($ouv);
            $attachment = chunk_split(base64_encode($lir));

            $msg2 .= "--$boundary\r\n";
            $msg2 .= "Content-Transfer-Encoding: base64\r\n";
            $msg2 .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
            $msg2 .= "\r\n";
            $msg2 .= $attachment . "\r\n";
            $msg2 .= "\r\n\r\n";
        }
    }


    //imap_append($stream,"{galaxy.apogeehost.com}INBOX.Sent","From: ".$maildata['email']."\r\n"."To: samsujj@gmail.com\r\n"."Subject: This is the subject\r\n"."$header\r\n"."$msg1\r\n"."$msg2\r\n"."$msg3\r\n");


    $toaddr = $_POST['toaddr'];
    $subject = $_POST['subject'];
    $pmailbody = $mailbody;

    if(isset($_POST['ai_upload_add'])){

        $mailbody .= '<br><br><br>';

        foreach($_POST['ai_upload_add'] as $row){
            $file = $row;
            $file_arr = explode('|',$file);
            $mailbody .= '<a href="http://mars.apogeehost.com/~nexmed/uploads/files/'.$file_arr[0].'/'.$file_arr[1].'">'.$file_arr[1].'</a><br>';
        }
    }


    $email_name = 'imapsent';
    $send_to = $_POST['toaddr'];
    $send_from = 'test@test.com';


    $default_vars = array
    (
        'email_msg' => $mailbody,
        'email_subject' => $_POST['subject'],
        'title' => $email_name
    );

    if($_POST['subtype'] == 'drafts'){
        imap_append($stream, "{galaxy.apogeehost.com}INBOX.Drafts"
            , "From: ".$maildata['email']."\r\n"."To: ".$send_to."\r\n"."Subject: ".$_POST['subject']."\r\n"."$header\r\n"."$msg1\r\n"."$msg2\r\n"."$msg3\r\n");
        imap_close ($stream);
        util_redirect('imapinbox');
    }elseif ($_POST['subtype'] == 'send'){
        $sys_email = new C_system_emails($email_name);

        $sys_email->set_from($maildata['email']);
        $sys_email->set_from_name($maildata['name']);

        $sys_email->encode_vars=false;
        $sys_email->set_vars_array(array());
        $sys_email->set_defaults_array($default_vars);

        if($sys_email->send($send_to)){

            imap_append($stream, "{galaxy.apogeehost.com}INBOX.Sent"
                , "From: ".$maildata['email']."\r\n"."To: ".$send_to."\r\n"."Subject: ".$_POST['subject']."\r\n"."$header\r\n"."$msg1\r\n"."$msg2\r\n"."$msg3\r\n");
            imap_close ($stream);


            util_redirect('imapinbox');
        }

        if($sys_email->has_errors())
        {
            print_r($sys_email->get_errors());
        }
    }else{
        util_redirect('imapinbox');
    }

}



$imap->selectFolder('INBOX');

$emails = $imap->getMessages();

$imap->selectFolder('INBOX.Sent');
$overallMessages = $imap->countMessages();

$imap->selectFolder('INBOX.Trash');
$trashcount = $imap->countMessages();

$imap->selectFolder('INBOX.Drafts');
$draftscount = $imap->countMessages();

$AI->skin->css('includes/plugins/imap/style.css');
//$AI->skin->js('includes/plugins/tinymce/tinymce.min.js');

?>

<script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>

<script>
    function getval() {

        alert(tinyMCE.get('compose-textarea').getContent());

    }


    $(function(){
        $('#compose-textarea').html('<?php echo $pmailbody;?>');

        tinymce.init({
            selector: 'textarea#compose-textarea',
            height: 500,
            //width:100%,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools code toc'
            ],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor link | code'
        });

        //tinyMCE.activeEditor.setContent(xcxz);
        //tinyMCE.get('compose-textarea')..setContent('<strong>Some contents</strong>');
    })

</script>


<div class="mailinbox">
    <div class="mailinboxblock">
    <div class="mailinboxheader">
        <div class="maillogodiv"></div>

        <div class="clearfix"></div>
    </div>
    <div class="mailinboxwrapper">
        <!-- Main content -->
        <!-- Main content -->
        <section class="content">
            <div class="row row-eq-height">
                <!-- /mailleft.col -->
                <div class="col-md-2 col-sm-3 col-xs-12 mailinboxleft">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Folders</h3>
                            <!--<div class="box-tools">
                                <button type="button" class="btn btn-box-tool" class="navbar-toggle" data-toggle="collapse"  data-target="#navbar-collapse-1"><span class="glyphicon glyphicon-minus"></span>
                                </button>
                            </div>-->
                        </div>
                        <div class="box-body no-padding navbar-collapse" id="navbar-collapse-1">
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="/~nexmed/imapinbox"><span class="glyphicon glyphicon-inbox"></span>Inbox <span class="label label-green pull-right"><?php echo count($emails) ; ?></span></a></li>
                                <li><a href="/~nexmed/imapdrafts"><span class="glyphicon glyphicon-pencil"></span> Drafts<span class="label label-red pull-right"><?php echo $draftscount; ?></span></a></li>
                                <li><a href="/~nexmed/imapsentbox"><span class="glyphicon glyphicon-envelope"></span> Sent Mail <span class="label label-red pull-right"><?php echo $overallMessages; ?></span></a></li>
                                <li><a href="/~nexmed/imaptrash"><span class="glyphicon glyphicon-trash"></span> Trash<span class="label label-red pull-right"><?php echo $trashcount; ?></span></a></li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- /mailleft.col -->
                <!-- /mailright.col -->
                <div class="col-md-10 col-sm-9 col-xs-12 mailinboxright">
                    <div class="new_form_header">
                    <h2><span>Mail</span> Inbox</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="box box-primary writemailinboxwrapper">
                        <div class="box-header with-border">
                            <h3 class="box-title">New Message</h3>
                        </div>
                        <!-- /.box-header -->
                        <form name="landing_page" id="landing_page_chk" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <input name="toaddr" class="form-control" placeholder="Message to:" style="padding:0;" value="<?php echo $toaddr;?>">
                            </div>
                            <div class="form-group">
                                <input name="subject" class="form-control" placeholder="Message subject:" style="padding:0;" value="<?php echo $subject;?>">
                            </div>
                            <div class="form-group">
                                <!--<textarea name="body[0]" id="compose-textarea" class="form-control">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like</textarea>-->


                                <textarea name="body" id="compose-textarea" class="form-control"></textarea>

                                <?php //echo $AI->get_dynamic_area(  'body555',  'name', $AI->get_lang(),false,  true, '100%',  '300',  true );?>

                            </div>
                            <?php

                            $upload = new C_upload('','');
                            $upload->run();

                            ?>

                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-left">
                                <button type="submit" name="subtype" class="btn btnsend" value="send">Send</button>
                                <button type="submit" name="subtype" class="btn btndraft" value="drafts">Draft</button>
                                <!--<button type="reset" class="btn btndiscard" onclick="getval()">Discard</button>-->
                            </div>
                        </form>
                            <!--<div class="pull-right">
                                <div class="form-group">
                                    <div class="btn btn-default btn-file">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        <input type="file" name="attachment">
                                    </div>
                                    <div class="btn btn-default btn-file">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="file" name="attachment">
                                    </div>
                                    <div class="btn btn-default btn-file">
                                        <span class="glyphicon glyphicon-facetime-video"></span>
                                        <input type="file" name="attachment">
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /. box -->
                </div>
                <!-- /mailright.col -->
            </div>
        </section>
    </div>
</div>
</div>