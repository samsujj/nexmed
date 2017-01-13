<?php


//set and submit landing page 2nd step

global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));
require_once(ai_cascadepath('includes/plugins/pop3/api.php'));
require_once ai_cascadepath('includes/modules/user_mails/includes/class.te_user_mails.php');

$landing_page = new C_landing_pages('Add Representatives');
$landing_page->next_step = 'repmanager?te_class=user_management&te_mode=table&te_asearch=true&te_qsearch=true&te_qkeywords=&te_qsearchMode=all&userID=&access_level=&username=&first_name=&last_name=&company=&email=&phone=&first_login=&first_login_datetime_month=&first_login_datetime_day=&first_login_datetime_year=&first_login_datetime_hour=&first_login_datetime_minute=&first_login_datetime_second=&last_login=&last_login_datetime_month=&last_login_datetime_day=&last_login_datetime_year=&last_login_datetime_hour=&last_login_datetime_minute=&last_login_datetime_second=&login_counter=&admin_notes=&account_type=Representatives&parent=&btnSearch=Search';
$landing_page->pp_create_campaign = false;

$landing_page->css_error_class = 'lp_error';


//add validation rule

$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
//$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('company', 'is_length', 3,'Invalid  Company');
$landing_page->add_validator('bill_city', 'is_length', 3,'Invalid  City');
$landing_page->add_validator('bill_address_line_1', 'is_length', 3,'Invalid Address');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');


if(util_is_POST()) {
    $landing_page->validate();

    $err = $AI->user->validate_password($_POST['password']);


    if (!empty($_POST['username'])){
        $err_arr = array();
        if(strlen($_POST['username'])<3) {
            $err_arr[] ='Username must be at least 3 characters.';
        }
        if(preg_match('/[^0-9A-Za-z-]/',$_POST['username'])) {
            $err_arr[] ='Username must only contain letters, numbers, and dashes.';
        }
        if(substr($_POST['username'],0,1)=='-' || substr($_POST['username'],-1)=='-') {
            $err_arr[] ='Username must not start or end with dash.';
        }

        if(count($err_arr) == 0){
            $lookup_userID = db_lookup_scalar("SELECT userID FROM users WHERE username = '" . db_in( $_POST['username'] ) . "';");
            if( is_numeric($lookup_userID)  )
            {
                $err_arr[] = 'Sorry, that username has already been taken, please choose another.';
            }
        }
    }

    if($landing_page->has_errors()) { $landing_page->display_errors(); }
    elseif (count($err_arr) > 0){
        $js[]="jonbox_alert('".implode('<br>',$err_arr)."');";
        if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
    }
    elseif($err !== true){
        $js[]="jonbox_alert('".$err."');";
        if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
    }
    else {

        //save user as Representatives
        $landing_page->save_user('Representatives');

        /****************Add mail Table[start]*********************/
        $te_user_mails = new C_te_user_mails();
        $te_user_mails->insert_data(array('userID'=>@$landing_page->session['created_user'],'email'=>$_POST['username'].'@nexmedsolutions.com','password'=>$_POST['password']));

        /****************Add mail Table[end]********************88*/


        require_once( ai_cascadepath('includes/plugins/system_emails/class.system_emails.php') );
        $email_name = 'repsignup';
        $send_to = $_POST['email'];
        $send_from = 'ben@apogeeinvent.com';

        $vars = array();
        $vars['uname'] = $_POST['username'];
        $vars['pass'] = $_POST['password'];

        $defaults = array();
        //$defaults['email_subject'] = 'Default Email Subject';
        //$defaults['email_msg'] = 'Hello [[name]], this is the default content of your email.';

        $se = new C_system_emails($email_name);
        $se->set_from($send_from);
        $se->set_defaults_array($defaults);
        $se->set_vars_array($vars);
        if(!$se->send($send_to))
        {
            //echo 47;exit;
        }


        $cpanelusr = 'nexmed';
        $cpanelpass = 'l0PS8AyMm0aB';
        $xmlapi = new xmlapi('galaxy.apogeehost.com');
        $xmlapi->set_port( 2083 );
        $xmlapi->password_auth($cpanelusr,$cpanelpass);
        $xmlapi->set_debug(0); //output actions in the error log 1 for true and 0 false
        $result = $xmlapi->api1_query($cpanelusr, 'Email', 'addpop', array($_POST['username'].'@nexmedsolutions.com',$_POST['password'],'unlimited','nexmedsolutions.com'));
        $x=imap_mail('debasiskar007@gmail.com', 'test 23', 'test body', $_POST['username'].'@nexmedsolutions.com');
        //var_dump($x);
        //exit;

        $landing_page->goto_next_step();
    }
}

$landing_page->refill_form();






?>

<form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">

    <!--<form method="post" name="form" onSubmit="return validate(this)">-->

    <input type="hidden" name="form_time" value="<?= date('Y-m-d H:i:s') ?>" />
    <p>
        <label for="first_name">user Name:</label>
        <input name="username" type="text" id="username" />
    </p>
    <p>
        <label for="last_name">password</label>
        <input name="password" type="password" id="password" />

    </p>
    <p>
        <label for="first_name">First Name:</label>
        <input name="first_name" type="text" id="first_name" />
    </p>
    <p>
        <label for="last_name">Last Name</label>
        <input name="last_name" type="text" id="last_name" />

    </p>
    <p>
        <label for="last_name">Company</label>
        <input name="company" type="text" id="company" />

    </p>
    <p>
        <label for="last_name">Email</label>
        <input name="email" type="text" id="email" />

    </p>

    <p>
        <label for="bill_address_line_1">Address</label>
        <input name="bill_address_line_1" type="text" id="bill_address_line_1" value="" />
    </p>
    <p>
        <label for="bill_city">City</label>
        <input name="bill_city" type="text" id="bill_city" value="" />
    </p>


    <!--
        <p>
    <label for="">Province/Other</label>

    <input type="hidden" name="question[40]" value="Other state"  />
<input type="text" name="answer[40]" value="" onChange="this.form.state.selectedIndex=1"  />
        </p>
    -->
    <input type="submit" value="Submit">
</form>