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
$imap->selectFolder('INBOX.Sent');
$emaillist = $imap->getMessages();

// count messages in current folder
$overallMessages = $imap->countMessages();
global $AI;
$AI->skin->css('includes/plugins/imap/style.css');
?>


<div class="mailinbox">
    <div class="mailinboxblock">
        <div class="mailinboxheader">
            <h2><span>INBOX</span> Inbox</h2>
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
                            <div class="box-body no-padding collapse navbar-collapse" id="navbar-collapse-1">
                                <ul class="nav nav-pills nav-stacked">
                                    <li class="active">
                                        <a href="/~nexmed/imapinbox"><span class="glyphicon glyphicon-inbox"></span> Inbox
                                            <span class="label label-green pull-right"><?php echo count($emails); ?></span></a></li>

                                   <!-- <li><a href="#"><span class="glyphicon glyphicon-star"></span> Starred <span class="label label-yellow pull-right"></span></a></li>-->

                                    <!--<li><a href="#"><span class="glyphicon glyphicon-bookmark"></span> Important</a></li>-->

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
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                    <div class="has-feedback">
                                        <input type="text" class="form-control input-sm" placeholder="Search Mail">
                                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                    </div>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <div class="mailbox-controls">
                                    <!-- Check all button -->
                                    <button type="button" class="btn btn-default btn-sm btninputtype"><input type="checkbox"><span class="glyphicon glyphicon-vector-path-square"></span>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm btnwritemail"><span class="glyphicon glyphicon-plus"></span> write mail</button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm btndelete"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                                        <!---<button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>--->
                                    </div>
                                    <!-- /.btn-group -->

                                    <div class="pull-right">
                                        <button type="button" class="btn btn-default btn-sm btnrefresh"><span class="glyphicon glyphicon-refresh"></span></button>
                                        <button type="button" class="btn btn-default btn-sm btnall">All <span class="glyphicon glyphicon-triangle-bottom"></span></button>
                                        <!--1-50/200
                                        <div class="btn-group">
                                          <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                          <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                        </div>-->
                                    </div>

                                    <!-- /.btn-group -->
                                    <!-- /.pull-right -->
                                </div>
                                <div class="table-responsive mailbox-messages">
                                    <table class="table table-hover table-striped">
                                        <tbody>
                                        <?php
                                        foreach ($emaillist as $key=>$email) {
                                            ?>

                                            <tr class='clickable-row'
                                                data-href='http://probiddealer.influxiq.com/#/writemail'>
                                                <td><input type="checkbox"></td>
                                                <td class="mailbox-star"><a href="#"><span
                                                            class="glyphicon glyphicon-star text-yellow"></span></a>
                                                </td>
                                                <td class="mailbox-name"><a href="/~nexmed/imapsentdetail?id=<?php echo $email['id'] ; ?>"><b><?php echo $email['from'] ; ?></b></a>
                                                </td>
                                                <td class="mailbox-subject">
                                                   <!-- <b class="imptxt">important</b> -->
                                                    <a href="/~nexmed/imapsentdetail?id=<?php echo $email['id'] ; ?>"><?php echo $email['subject'] ; ?> </a>
                                                </td>
                                                <td class="mailbox-attachment"></td>
                                                <td class="mailbox-date"><?php echo
                                                    date('m/d/Y H:i:s', $email['udate']); ; ?></td>
                                            </tr>
                                            <?php
                                        }

                                        ?>


                                        </tbody>
                                    </table>
                                    <!-- /.table -->
                                </div>
                                <!-- /.mail-box-messages -->
                            </div>
                            <!-- /.box-body -->
                            <!--<div class="box-footer no-padding">
                             <div class="mailbox-controls">
                               <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                               </button>
                               <div class="btn-group">
                                 <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                 <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                                 <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                               </div>
                               <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                               <div class="pull-right">
                                 1-50/200
                                 <div class="btn-group">
                                   <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                   <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                 </div>
                               </div>
                             </div>
                           </div>-->
                        </div>
                        <!-- /. box -->
                    </div>
                    <!-- /mailright.col -->
                </div>
            </section>
        </div>
    </div>
</div>
