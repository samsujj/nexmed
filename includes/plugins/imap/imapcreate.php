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


$stream=@imap_open("{galaxy.apogeehost.com/novalidate-cert}INBOX", $username, $password);


if(util_is_POST()) {
    $mailbody = $_POST['body'][0];


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


    $sys_email = new C_system_emails($email_name);

    $sys_email->set_from($maildata['email']);

    $sys_email->encode_vars=false;
    $sys_email->set_vars_array(array());
    $sys_email->set_defaults_array($default_vars);

    if($sys_email->send($send_to)){

        imap_append($stream, "{galaxy.apogeehost.com}INBOX.Sent"
            , "From:  ".$maildata['email']." \r\n"
            . "To: ".$send_to."\r\n"
            . "Subject: ".$_POST['subject']."\r\n"
            ."\r\n"
            ." $mailbody \r\n"
        );
        imap_close ($stream);


        util_redirect('imapinbox');
    }

    if($sys_email->has_errors())
    {
        print_r($sys_email->get_errors());
    }
}



$imap->selectFolder('INBOX');

$emails = $imap->getMessages();

$imap->selectFolder('INBOX.Sent');
$overallMessages = $imap->countMessages();


$AI->skin->css('includes/plugins/imap/style.css');

?>




<div class="mailinbox">
    <div class="mailinboxblock">
    <div class="mailinboxheader">
        <h2><span>Mail</span> Inbox</h2>
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
                <div class="col-md-10 col-sm-9 col-xs-12 mailinboxright">
                    <div class="box box-primary writemailinboxwrapper">
                        <div class="box-header with-border">
                            <h3 class="box-title">New Message</h3>
                        </div>
                        <!-- /.box-header -->
                        <form name="landing_page" id="landing_page_chk" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <input name="toaddr" class="form-control" placeholder="Message to:">
                            </div>
                            <div class="form-group">
                                <input name="subject" class="form-control" placeholder="Message subject:">
                            </div>
                            <div class="form-group">
                                <!---<textarea name="body" id="compose-textarea" class="form-control">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like</textarea>-->


                                <?php echo $AI->get_dynamic_area(  'body',  'name', $AI->get_lang(),true,  true, '100%',  '300',  true);?>

                            </div>
                            <?php

                            $upload = new C_upload('','');
                            $upload->run();

                            ?>

                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-left">
                                <button type="submit" class="btn btnsend">Send</button>
                                <button type="button" class="btn btndraft">Draft</button>
                                <button type="reset" class="btn btndiscard">Discard</button>
                            </div>
                        </form>
                            <div class="pull-right">
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
                            </div>
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