<?php

/*
 THIS IS FOR THE MAIN ACM MENU (AND NEW TOP NAV)

 Other menus:
   badge menu is in includes/draw.badge_menu.php
   classic admin dashboard is in array.php
*/

global $AI;

$my_profile_url = 'myprofile';
$blog_url = 'blog';
$perm = new C_permissions('dashboard'); // Don't use $this->perm because this file is also included by C_top_bar()
$webdev = $perm->get('webdev');
$admin = ($webdev || $AI->get_access_group_perm('Admin Only'));

$lm_perm = new C_permissions('lead_management');
$um_perm = new C_permissions('user_management');


$sub_domain = C_dashboard::get_subdomain_jump();
if ( $sub_domain === true )
{
	$my_profile_url = './';
}
elseif ( $sub_domain !== false )
{
	$my_profile_url = $sub_domain;
	$blog_url = $sub_domain . 'blog';
}

// Get the once-per-request cache if any
if ( isset($AI->defined_menus) )
{
	$this->main_menu = $AI->defined_menus['main_menu'];
	$this->panel_menus = $AI->defined_menus['panel_menus'];
	$this->top_panel_array = $AI->defined_menus['top_panel_array'];
	$this->dashboard_panel_array = $AI->defined_menus['dashboard_panel_array'];
	return; // Stop the rest of this file's logic
}

$this->main_menu = array();
$this->panel_menus = array();


/*********************
 TOP NAV MENU
**********************/
$this->top_panel_array = array(
	'home' => array('stat'=>1, 'id'=>'home','txt'=>'Home', 'href'=>'./', 'sort'=>PHP_INT_MAX-1, 'perm_key'=>'viewop_home'),
	'dashboard' => array('stat'=>1, 'id'=>'dashboard','txt'=>'Dashboard', 'href'=>'dashboard', 'sort'=>1700,'perm_key'=>'viewop_dashboard'),
	'attract' => array('stat'=>1, 'id'=>'attract','txt'=>'Attract', 'sort'=>1500, 'perm_key'=>'viewop_attract'),
	'communicate' => array('stat'=>1, 'id'=>'communicate','txt'=>'Communicate', 'sort'=>1400,'perm_key'=>'viewop_communicate'),
	'manage' => array('stat'=>1, 'id'=>'manage','txt'=>'Manage', 'sort'=>1300,'perm_key'=>'viewop_manage'),
	'success' => array('stat'=>1, 'id'=>'success','txt'=>'Success', 'sort'=>1100,'perm_key'=>'viewop_success'),
	'admin' => array('stat'=>1, 'id'=>'admin', 'txt'=>'ADMIN', 'sort'=>-99999998,'perm_key'=>'viewop_admin'),
	'search' => array('stat'=>1, 'id'=>'tbsearchbutton','txt'=>'`', 'href'=>'#', 'sort'=>-99999999, 'perm_key'=>'viewop_admin'),

	//'edit' => array('stat'=>1, 'id'=>'editmode', 'href'=>'#', 'txt'=>'Edit', 'sort'=>-99999999,'perm_key'=>'viewop_edit'),
);


//enforce perms for primary menu items (those provided here in core, we don't enforce on custom site entries):
/* $hid=array();
	 foreach($this->top_panel_array as $id=>$arr) {
	 if(isset($arr['txt']) && !$perm->get('viewop_'.$id)) { $hid[$id]=1; $this->top_panel_array[$id]['stat']=0; }
	 } */

//remove unecessary spaces
if( @$hid['dashboard'] || (@$hid['attract'] && @$hid['communicate'] && @$hid['manage']) ) unset($this->top_panel_array['sp1']);
if( @$hid['success']  || (@$hid['dashboard'] && @$hid['attract'] && @$hid['communicate'] && @$hid['manage'])) unset($this->top_panel_array['sp2']);

// Hook-based menu-item aggregation
$hook_ret = aimod_run_hook('hook_panel_top_menu_add');
foreach ( $hook_ret as $ret )
{
	if ( is_array($ret) && count($ret) > 0 )
	{
		foreach ( $ret as $k => $v )
		{
			$this->top_panel_array[$k] = $v;
		}
	}
}

// PHP: ORDER BY sort DESC, txt ASC
$sort = array();
$btxt = array();
foreach ( $this->top_panel_array as $k => $v )
{
	$sort[$k] = empty($v['sort']) ? 10.0 : (float) $v['sort'];
	$btxt[$k] = empty($v['txt' ]) ? ''   : trim(strtolower($v['txt']));
}
array_multisort($sort, SORT_DESC, $btxt, SORT_ASC, $this->top_panel_array); // http://us1.php.net/manual/en/function.array-multisort.php

//THE MAIN NAV OPTIONS
$this->dashboard_panel_array = array(
	array('id'=>'attract',     'txt'=>'Attract',     'icon'=>'images/menu_tree/group_48.png'),
	array('id'=>'communicate', 'txt'=>'Communicate', 'icon'=>'images/menu_tree/dollar_signs.png'),
	array('id'=>'manage',      'txt'=>'Manage',      'icon'=>'images/menu_tree/file_48.png'),
);





/*********************
 LOAD THE PANEL MENUS
**********************/

//'GET LEADS' PANEL

//ATTRACT
$this->panel_menus['attract'] = array();
$this->panel_menus['attract']['my_urls']=array( 'stat'=>(int)util_mod_enabled('share_links'), 'href'=>'share_links', 'btxt'=>'Share Links', 'img'=>'images/menu_tree/mail_reload_48.png', 'desc'=>'This page lets you easily grab and share the links for your personal web pages. Share these links in as many places as you can to get more leads!', 'sort'=>130);
$this->panel_menus['attract']['invite_contacts']=array( 'stat'=>1, 'href'=>'lead_management?invite_contacts=1', 'btxt'=>'Import Contacts', 'img'=>'images/menu_tree/group_reload_48.png', 'desc'=>'Add and organize new entries into your Contact Manager for follow up and marketing.', 'sort'=>120);
$this->panel_menus['attract']['blog']=array( 'stat'=>1, 'href'=>$blog_url, 'btxt'=>'Write Blog', 'img'=>'images/menu_tree/pencil_48.png', 'desc'=>'Start blogging on your own personal blog. Become an expert in your industry - it\'s a great way to market your business.', 'sort'=>110);
$this->panel_menus['attract']['update_homepage']=array( 'stat'=>(int)util_mod_enabled('user_profiles'), 'href'=>$my_profile_url, 'btxt'=>'Update Homepage', 'img'=>'images/menu_tree/house_save_48.png', 'desc'=>'Keep your personal website homepage up-to-date with the latest offers and opportunities!', 'sort'=>100);

//COMMUNICATE

$this->panel_menus['communicate'] = array();
$this->panel_menus['communicate']['lead_management'] = array( 'stat'=>1, 'href'=>'lead_management', 'btxt'=>'Contact Manager', 'img'=>'images/menu_tree/group_ok_48.png', 'desc'=>'Manage your contacts with this CRM that allows you to add, import, edit, search, sort, and email your contacts. Schedule follow ups and keep track of \'temperature\'.  All in one easy-to-access place!', 'sort'=>140);
$this->panel_menus['communicate']['success_line'] = array( 'stat'=>1, 'href'=>'success-line', 'btxt'=>'Success Line', 'img'=>'images/menu_tree/ranks.gif', 'desc'=>'The Success Line will use a fear-of-loss focused drip email marketing campaign to continually encourage your contacts to sign up. The Success Line sends your contacts emails that give them specific deadlines to sign up or they will lose out on a pre-built downline.', 'sort'=>130);
$this->panel_menus['communicate']['broadcast_email'] = array( 'stat'=>1, 'href'=>'lead_management?broadcast_email=1', 'btxt'=>'Broadcast Email', 'img'=>'images/menu_tree/mail_next_48.png', 'desc'=>'Use this tool to send an email to many of your contacts all at once. Broadcast Emails are pre-approved marketing emails that may contain variables so each email is customized for its recipient.', 'sort'=>120);
  if($lm_perm->get('broadcast')==false) $this->panel_menus['communicate']['broadcast_email']['stat']=0;

// Special Logic to determine which access groups should see this
if ( isset($AI->MODS_INDEX['drip_manager']['raw_settings']) )
{
	$account_types_array = @unserialize($AI->MODS_INDEX['drip_manager']['raw_settings']);
	if(isset($account_types_array['account_types']) && in_array($AI->user->account_type,$account_types_array['account_types']))
	{
		$this->panel_menus['communicate']['drip_manager'] = array( 'stat'=>1, 'href'=>'drip_manager', 'btxt'=>'Drip Manager', 'img'=>'images/menu_tree/mail_clock_48.png', 'desc'=>'Easily build Drip Emails to automatically follow up with your contacts. Keep your offering top-of-mind with personalized emails that send on a schedule based on the Landing Page your contact filled out.', 'sort'=>110);
	}
}

$this->panel_menus['communicate']['phone_burner'] = array( 'stat'=>1, 'href'=>'lead_management?phone_burner=1', 'btxt'=>'PhoneBurner', 'img'=>'images/menu_tree/telephone_info_48.png', 'desc'=>'With the PhoneBurner built in auto dialing tool you can move from one call to another quickly. The notes you take in PhoneBurner will be automatically transferred back into your Contact Manager at the end of the dialing session.', 'sort'=>100);
if(!util_mod_enabled('success_line')) unset($this->panel_menus['communicate']['success_line']);
if(!util_mod_enabled('phone_burner')) unset($this->panel_menus['communicate']['phone_burner']);

//MANAGE

$this->panel_menus['manage'] = array();
$this->panel_menus['manage']['reports'] = array( 'stat'=>1, 'href'=>'reports', 'btxt'=>'Reports', 'img'=>'images/menu_tree/shopping_cart_48.png', 'desc'=>'Measure the success of your efforts with intuitive graphical reports.', 'sort'=>110);
$this->panel_menus['manage']['postal_events'] = array( 'stat'=>0, 'href'=>'postal_events', 'btxt'=>'Email Events', 'img'=>'images/menu_tree/mail_info_48.png', 'desc'=>'View a log of email events from PostalParrot.', 'sort'=>100);
if ( util_mod_enabled('comp_plan') && $AI->get_access_group_perm('Administrators') )
{
	$this->panel_menus['manage']['downline_activity'] = array
		( 'stat' => 1
		, 'href' => 'downline-activity'
		, 'btxt' => 'Downline Activity'
		, 'img'  => 'images/menu_tree/group_level_48.png'
		, 'desc' => 'View an individual\'s business and sales activity'
		);
}

//USER_MANAGEMENT FOR MANAGER DOWNLINES (IF USER HAS 'limit_control_by_decendants' PERM)
 //(managers would also need user_management page & table mode)
if($um_perm->get('limit_control_by_decendants')) {
	$this->panel_menus['manage']['user_management'] = array
			( 'stat' => 1
			, 'href' => 'user_management'
			, 'btxt' => 'User Management'
			, 'img' => 'images/menu_tree/group_write_48.png'
			, 'desc' => 'Manage Users'
			);
}

//SUCCESS

$this->panel_menus['success'] = array();
$events_module = aimod_get_module('events');
$training_module = aimod_get_module('training');
$training_stat = ( util_mod_enabled('training') && $AI->perm->get('Page - Training') ? true : false );
$this->panel_menus['success']['success'] = array( 'stat'=>$training_stat, 'href'=>$training_module->training_page_name, 'btxt'=>'Success Education', 'img'=>'images/menu_tree/education_48.png', 'desc' => 'Success Education', 'sort'=>120);
$this->panel_menus['success']['events']	= array( 'stat'=>$events_module->calendar_enabled, 'href'=>'events', 'btxt'=>'Event Calendar', 'img'=>'includes/modules/events_programs/images/calendar.png', 'desc'=>'Events calendar', 'sort'=>110);
$this->panel_menus['success']['documents'] = array('stat'=>util_mod_enabled('documents'), 'href'=>'documents', 'btxt'=>'Document Library', 'img'=>'images/menu_tree/document_48.png', 'desc' => 'Your documents library', 'sort'=>100);



//USER ACCOUNT

$this->panel_menus['user_account'] = array();
//$this->panel_menus['user_account']['user_profile'] = array( 'stat'=>1, 'href'=>(util_mod_enabled('user_profiles')? 'profile?te_class=user_profiles&te_mode=update&te_key=' . $AI->user->userID . '&back_hop=myprofile':'account_settings?te_class=user_management&te_mode=update'), 'btxt'=>'Account Settings', 'img'=>'images/menu_tree/admin_48.png', 'desc'=>'Edit your public profile');
$this->panel_menus['user_account']['user_profile'] = array( 'stat'=>1, 'href'=>'my_account_edit', 'btxt'=>'Account Settings', 'img'=>'images/menu_tree/admin_48.png', 'desc'=>'Edit your public profile');



if($AI->user->account_type != 'Representatives' && $AI->user->account_type != 'Approved Reps'){
    $this->panel_menus['user_account']['billing_profiles'] = array( 'stat'=>util_mod_enabled('billing_profiles'), 'href'=>'my-billing-profile', 'btxt'=>'Billing Profile', 'img'=>'images/menu_tree/credit_card.png', 'desc'=>'Edit your billing information');
}


//draw either 'membership' or 'scheduled_purchase' management
if(util_mod_enabled('memberships')) {

    if($AI->user->account_type != 'Representatives' && $AI->user->account_type != 'Approved Reps'){
        $this->panel_menus['user_account']['membership'] = array( 'stat'=>1, 'href'=>'my_membership', 'btxt'=>'Membership', 'img'=>'images/menu_tree/admin_clock_48.png', 'desc'=>'Manage your membership');
    }



	if(util_mod_enabled('genealogy')) { // Included scheduled purchases anyway with Genealogy (aka Autoship)
        if($AI->user->account_type != 'Representatives' && $AI->user->account_type != 'Approved Reps'){
            $this->panel_menus['user_account']['scheduled_purchases'] = array( 'stat'=>1, 'href'=>'my_scheduled_purchases', 'btxt'=>'Scheduled Purchases', 'img'=>'images/menu_tree/shopping_cart_clock_48.png', 'desc'=>'Scheduled Purchases');
        }

	}
}
else if(util_mod_enabled('schedule_purchases')) {
    if($AI->user->account_type != 'Representatives' && $AI->user->account_type != 'Approved Reps'){
        $this->panel_menus['user_account']['scheduled_purchases'] = array( 'stat'=>1, 'href'=>'my_scheduled_purchases', 'btxt'=>'Scheduled Purchases', 'img'=>'images/menu_tree/admin_clock_48.png', 'desc'=>'Scheduled Purchases');
    }
}

if(util_mod_enabled('external_connections'))
{
	$this->panel_menus['user_account']['external_connections'] = array('stat'=>1,'btxt'=>'External Connections','href'=>'external_connections','img'=>'images/menu_tree/world_config_48.png','desc'=>'Manage connectios to other sites.');
}



//'sort' IS 'DESC' on categories
//HIGH SORT VALUE MEANS HIGHER PRIORITY (LISTED FIRST)
// (just like existing menu sorting logic)

$this->panel_menus['admin'] = array();
$this->panel_menus['admin']['__TAGS__'] = array // magic menu that dictates tag/category perms (use 'stat') and order (array order)
	( 'users'         => array('stat' => true   , 'btxt' => 'Users',					'sort'=>95)
	, 'sales'         => array('stat' => true   , 'btxt' => 'Sales',					'sort'=>90)
	, 'communication' => array('stat' => true   , 'btxt' => 'Communication',	'sort'=>85)
	, 'reports'       => array('stat' => true   , 'btxt' => 'Reports',				'sort'=>80)
	, 'pages'         => array('stat' => true   , 'btxt' => 'Pages',          'sort'=>78)
	, 'permissions'   => array('stat' => $webdev, 'btxt' => 'Permissions',		'sort'=>75)
	, 'settings'      => array('stat' => true   , 'btxt' => 'Settings',				'sort'=>70)
	, 'dev'           => array('stat' => $webdev, 'btxt' => 'Developer Menu',	'sort'=> 5)
	);

$this->panel_menus['admin']['settings'] = array
	( 'stat' => true
	, 'href' => 'settings?edit'
	, 'btxt' => 'Website Settings'
	, 'img'  => 'images/menu_tree/preferences-system.svg'
	, 'desc' => 'Configure global website settings and options including website title, keywords, default email addresses, etc.'
	, 'tags' => 'settings,dev'
	, 'sort' => 9000099 // (float/double) [Default: 10.0] Sort/priority/weight: Larger index equals higher placement on list
	);
$this->panel_menus['admin']['sitestat'] = array
	( 'stat' => true
	, 'href' => 'sitestat'
	, 'btxt' => 'Site Status'
	, 'img'  => 'images/menu_tree/health_48.png'
	, 'desc' => 'Review details surroudning general site health.'
	, 'tags' => 'dev'
	, 'sort' => 9000098
	);
$this->panel_menus['admin']['ai_info'] = array
	( 'stat' => true
	, 'href' => 'ai_info'
	, 'btxt' => 'AI Site Info'
	, 'img'  => 'images/menu_tree/world_info_48.png'
	, 'desc' => 'Simple Site Info page'
	, 'tags' => 'dev'
	, 'sort' => 9000097
	);



$this->panel_menus['admin']['system_emails'] = array
	( 'stat' => true
	, 'href' => 'system_emails'
	, 'btxt' => 'System Emails'
	, 'img'  => 'images/menu_tree/mail_save_48.png'
	, 'desc' => 'View and edit system-wide email templates and messages.'
	, 'tags' => 'settings, communication'
	);


$this->panel_menus['admin']['lead_management'] = array
	( 'stat' => true
	, 'href' => 'lead_management'
	, 'btxt' => 'CRM Leads'
	, 'img'  => 'images/menu_tree/group_ok_48.png'
	, 'desc' => 'Manage your contacts with this CRM that allows you to add, import, edit, search, sort, and email your contacts. Schedule follow ups and keep track of \'temperature\'.  All in one easy-to-access place!'
	, 'tags' => 'communication'
	);


if(util_mod_enabled('contact_service_fulfillment')) {
$this->panel_menus['admin']['contact_service_fulfillment'] = array
	( 'stat' => true
	, 'href' => 'contact_service_fulfillment'
	, 'btxt' => 'Contact Services Admin'
	, 'img'  => 'images/menu_tree/shopping_cart_ok_48.png'
	, 'desc' => ''
	, 'tags' => 'communication, reports'
	);
}

$this->panel_menus['admin']['blogs_moderate'] = array
	( 'stat' => true
	, 'href' => 'blogs_moderate'
	, 'btxt' => 'Blog Approval'
	, 'img'  => 'images/menu_tree/book_ok_48.png'
	, 'desc' => 'Moderate all blog postings.'
	, 'tags' => 'settings'
	);
$this->panel_menus['admin']['blogs_comments'] = array
	( 'stat' => true
	, 'href' => 'blog_comments'
	, 'btxt' => 'Blog Comments'
	, 'img'  => 'images/menu_tree/book_ok_48.png'
	, 'desc' => 'Aprove blog comments.'
	, 'tags' => 'settings'
	);
$this->panel_menus['admin']['blogs'] = array
	( 'stat' => true
	, 'href' => 'blogs'
	, 'btxt' => 'Blog Settings'
	, 'img'  => 'images/menu_tree/book_save_48.png'
	, 'desc' => ''
	, 'tags' => 'settings'
	);

$dynamic_page_id = (int) db_lookup_scalar("SELECT pageID FROM ai_dynamicpages WHERE pagename='".AI_PAGE_NAME."' LIMIT 1");

$this->panel_menus['admin']['autoseo'] = array
( 'stat' => true
		, 'href' => 'autoseo?ai_skin=full_page&cmd=main&pn='.AI_PAGE_NAME.'&qs='.urlencode($AI->skin->get_url_query()).'&page_path='.AI_PAGE_NAME.'&dynamicpage_id='.$dynamic_page_id
		, 'rel'  => 'jonbox'
		, 'btxt' => 'Autoseo'
		, 'img'  => 'images/menu_tree/world_config_48.png'
		, 'desc' => ''
		, 'tags' => 'settings'
);
$this->panel_menus['admin']['module_manager'] = array
	( 'stat' => true
	, 'href' => 'module_manager'
	, 'btxt' => 'Module Manager'
	, 'img'  => 'images/menu_tree/settings.svg'
	, 'desc' => 'Enable and disable modules and add flexibility to your system.  Manage and configure settings for each module.'
	, 'tags' => 'settings,dev'
	);

$pgid = uv($AI->skin->vars,'pageID');
if($pgid>0) {
$this->panel_menus['admin']['edit_curr_page'] = array
	( 'stat' => true
	, 'href' => 'ai_dynamicpages?te_class=ai_dynamicpages&te_mode=update&te_key='.$pgid
	, 'btxt' => 'Edit Page Settings'
	, 'img'  => 'images/menu_tree/windows_window_write_48.png'
	, 'desc' => ''
	, 'tags' => 'pages'
	, 'sort' => 20
	);
}
$this->panel_menus['admin']['add_page'] = array
	( 'stat' => true
	, 'href' => 'ai_dynamicpages?te_class=ai_dynamicpages&te_mode=insert&set_skinname='.$AI->skin->vars['theme']
	, 'btxt' => 'Add Page'
	, 'img'  => 'images/menu_tree/windows_window_add_48.png'
	, 'desc' => ''
	, 'tags' => 'pages'
	, 'sort' => 15
	);
$this->panel_menus['admin']['page_manager'] = array
	( 'stat' => true
	, 'href' => 'ai_dynamicpages'
	, 'btxt' => 'Page Manager'
	, 'img'  => 'images/menu_tree/windows_config_48.png'
	, 'desc' => 'Manage the pages on this site.'
	, 'tags' => 'pages'
	, 'sort' => 10
	);
$this->panel_menus['admin']['theme_manager'] = array
	( 'stat' => true
	, 'href' => 'themes'
	, 'btxt' => 'Theme Manager'
	, 'img'  => 'images/menu_tree/system_config_48.png'
	, 'desc' => 'Manage site themes, default page content, etc.'
	, 'tags' => 'pages'
	, 'sort' => 5
	);


$this->panel_menus['admin']['account_types'] = array
	( 'stat' => true
	, 'href' => 'account_types'
	, 'btxt' => 'Account Types'
	, 'img'  => 'images/menu_tree/user_next_48.png'
	, 'desc' => 'Manage the available account types in this system and the permissions for each.'
	, 'tags' => 'permissions'
	);
$this->panel_menus['admin']['dashboard_config'] = array
	( 'stat' => $webdev
	, 'href' => 'dashboard_config'
	, 'btxt' => 'Account Dashboards'
	, 'img'  => 'images/menu_tree/stadistics_config_48.png'
	, 'desc' => 'Manage Dashboard configurations for each account_type.'
	, 'tags' => 'permissions'
	);
$this->panel_menus['admin']['acodes'] = array
	( 'stat' => true
	, 'href' => 'acodes'
	, 'btxt' => 'Access Codes'
	, 'img'  => 'images/menu_tree/folder_lock_48.png'
	, 'desc' => 'Manage access codes that protect certain pages and/or features, allowing access to those who have purchased or received the access codes.'
	, 'tags' => 'permissions'
	);
$this->panel_menus['admin']['access_groups'] = array
	( 'stat' => true
	, 'href' => 'access_group_manager'
	, 'btxt' => 'Access Groups'
	, 'img'  => 'images/menu_tree/folder_next_48.png'
	, 'desc' => 'Manage your access groups.  Access Groups organize Permission Groups into single categories.'
	, 'tags' => 'permissions'
	);
$this->panel_menus['admin']['permission_manager'] = array
	( 'stat' => true
	, 'href' => 'permission_manager'
	, 'btxt' => 'Permission Manager'
	, 'img'  => 'images/menu_tree/37.png'
	, 'desc' => 'A comprehensive list of all manageable permissions.'
	, 'tags' => 'permissions'
	);
$this->panel_menus['admin']['classic_permissions'] = array
	( 'stat' => true
	, 'rel'  => 'jonbox'
	, 'href' => 'permissions?ai_skin=full_page&ajax_cmd=draw&target_element_id=jonbox_permissions&permissions_requested=' . urlencode(serialize($AI->get_permissions_requested()))
	, 'btxt' => 'Classic Permissions'
	, 'img'  => 'images/menu_tree/group_remove_48.png'
	, 'desc' => 'The classic permission pop-up manager.'
	, 'tags' => 'permissions'
	, 'id'   => 'classic_permissions_handle'
	);
$this->panel_menus['admin']['ai_archive'] = array
	( 'stat' => $webdev
	, 'href' => 'ai_archive'
	, 'btxt' => 'AI Archive'
	, 'img'  => 'images/menu_tree/trash_48.png'
	, 'desc' => 'Restore deleted table edit items'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['sql'] = array
	( 'stat' => true
	, 'href' => 'sql'
	, 'btxt' => 'SQL'
	, 'img'  => 'images/menu_tree/utilities-terminal.svg'
	, 'desc' => 'Perform direct queries to this system\'s SQL server.<br><br>View results and output directly on the webpage in different formats even with an option to export via CSV.'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['pma'] = array
	( 'stat' => true
	, 'href' => 'PMA-launch'
	, 'btxt' => 'phpMyAdmin'
	, 'img'  => 'images/menu_tree/data3.svg'
	, 'desc' => 'Launch phpMyAdmin'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['ai_dynamicpages'] = array
	( 'stat' => true
	, 'href' => 'ai_dynamicpages'
	, 'btxt' => 'Dynamic Pages'
	, 'img'  => 'images/menu_tree/dynamic-areas.svg'
	, 'desc' => 'Manage all the system\'s dynamic pages, update file references, page URLs, and page permissions.'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['generate_te'] = array
	( 'stat' => true
	, 'href' => 'generate_te'
	, 'btxt' => 'Generate TE'
	, 'img'  => 'images/menu_tree/windows_add_48.png'
	, 'desc' => 'TableEdit Generator'
	, 'tags' => 'dev'
	);

$this->panel_menus['admin']['ai_errors'] = array
	( 'stat' => true
	, 'href' => 'ai_errors'
	, 'btxt' => 'Error Log: AI'
	, 'img'  => 'images/menu_tree/dialog-warning.svg'
	, 'desc' => 'View all AI errors logged by $AI->error, $AI->silent_error, and/or AI_Exception.'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['php_errors'] = array
	( 'stat' => true
	, 'href' => 'php_errors'
	, 'btxt' => 'Error Log: PHP'
	, 'img'  => 'images/menu_tree/dialog-warning-php.svg'
	, 'desc' => 'View all PHP triggered errors.'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['orders_log'] = array
	( 'stat' => true
	, 'href' => 'orders_log'
	, 'btxt' => 'Orders Log'
	, 'img'  => 'images/menu_tree/shopping_cart_clock_48.png'
	, 'desc' => 'View full orders log'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['ai_debugger'] = array
	( 'stat' => $webdev
	, 'href' => AI_PHP_SELF . '?ai_debugger=' . ($AI->get_setting('debugger') ? 'off' : 'on')
	, 'btxt' => 'Debugger ' . ($AI->get_setting('debugger') ? '((ON))' : '(off)')
	, 'img'  => 'images/menu_tree/dialog-information' . ($AI->get_setting('debugger') ? '_red' : '') . '.svg'
	, 'desc' => ($AI->get_setting('debugger') ? 'The Debugger is currently turned on. Click to turn it off.' : 'The Debugger  is currently turned off. Click to turn it on.')
	, 'tags' => 'dev'
	, 'sort' => -999
	);
if ( $AI->user->has_substituted_user() )
{
	$this->panel_menus['admin']['ai_substitute_userID'] = array
		( 'stat' => true
		, 'href' => AI_PHP_SELF . '?ai_substitute_userID=' . (int) $AI->user->orig_userID
		, 'btxt' => 'Return to My Account'
		, 'img'  => 'images/menu_tree/admin_reload_48.png'
		, 'desc' => 'Return to your original administrator account.'
		, 'tags' => ''
		, 'sort' => -1000
		);
}
//$AI->skin->css('includes/plugins/translator/translation_manager.css');
//$AI->skin->js('includes/plugins/translator/translation_manager.js');
$this->panel_menus['admin']['translation_manager'] = array
	( 'stat' => true
	, 'rel'  => 'jonbox'
	, 'href' => 'translation_manager?page_name=' . urlencode(AI_PAGE_NAME)
	, 'btxt' => 'Translate Current Page'
	, 'img'  => 'images/menu_tree/translate.svg'
	, 'desc' => 'Translate select text and phrases found within the current page.<br><br>You could also change terms in the default language only on special text and phrases.'
	, 'tags' => 'settings'
	);
$this->panel_menus['admin']['maintenance'] = array
	( 'stat' => true
	, 'href' => 'maintenance'
	, 'btxt' => 'Maintenance Scheduler'
	, 'img'  => 'images/menu_tree/Appointment.svg'
	, 'desc' => 'Manage scripts to be run on the maintenance cronjob, adjusting event times, scaling event frequencies, and troubleshooting unrun/missed events.'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['email_log'] = array
	( 'stat' => true
	, 'href' => 'email_log'
	, 'btxt' => 'Email Log'
	, 'img'  => 'images/menu_tree/mail_zoom_48.png'
	, 'desc' => 'Sift through outgoing email messages sent directly by the system.'
	, 'tags' => 'reports'
	);
$this->panel_menus['admin']['version_admin'] = array
	( 'stat' => true
	, 'href' => 'version_admin'
	, 'btxt' => 'Version Admin'
	, 'img'  => 'images/menu_tree/settings.svg'
	, 'desc' => 'Manage legacy versioning files for plugins'
	, 'tags' => 'dev'
	);
$this->panel_menus['admin']['multiple_domains'] = array
	( 'stat' => true
	, 'href' => 'multiple_domains'
	, 'btxt' => 'Multiple Domains'
	, 'img'  => 'images/menu_tree/Internet-web-browser.svg'
	, 'desc' => 'Manage multiple domains that point to this system.  This includes lookups for subdomains and wildcard subdomains.'
	, 'tags' => 'dev'
	);


// Hook-based menu-item aggregation
$hook_ret = aimod_run_hook('hook_panel_menus_add');

foreach ( $hook_ret as $ret )
{
	if ( is_array($ret) && count($ret) > 0 )
	{
		foreach ( $ret as $k => $v )
		{
			if ( is_array($v) && count($v) > 0 )
			{
				foreach ( $v as $kk => $vv )
				{
					$this->panel_menus[$k][$kk] = $vv;
				}
			}
		}
	}
}

// Admin menu backward compatibility: pick up old admin buttons drawn for the control panel
// Those that have an href already used by the standard panel menu will be ignored
// Assumption: The href used matches the admin menu's array key
$hook_ret = aimod_run_hook('hook_control_panel_icons_administration_modules');
$hook_ret = array();

foreach ( $hook_ret as $module_name => $data )
{
	if ( isset($this->panel_menus['admin'][@$data['href']]) )
	{
		continue;
	}
	$this->panel_menus['admin'][@$data['title']] = array
		( 'stat' => true
		, 'href' => @$data['href']
		, 'btxt' => @$data['title']
		, 'img'  => @$data['icon']
		, 'desc' => @$data['desc']
		, 'tags' => ''
		, 'sort' => 10
		);
}

if ( $AI->user->has_substituted_user() )
{
	$this->panel_menus['user_account']['substitute'] = array( 'stat'=>1, 'href'=>'dashboard?ai_substitute_userID=' . (int) $AI->user->orig_userID, 'btxt'=>'Return to My Account', 'img'=>'images/menu_tree/admin_reload_48.png', 'desc'=>'Switch back to original user account.', 'admin_only'=>true );
}

$this->panel_menus['user_account']['logoff'] = array( 'stat'=>1, 'href'=>'logoff', 'btxt'=>'Logoff', 'img'=>'images/menu_tree/door_close_48.png', 'desc'=>'Logoff' );


/*********************
 CALL FOR OVERRIDES
**********************/
if( ($path=ai_cascadepath('includes/plugins/dashboard/array_menu_modify.php'))!='' ) require($path);

/**********************
LOAD CUSTOM MENU ITEMS FROM DATABASE
***********************/

require_once( ai_cascadepath("includes/plugins/top_bar/class.top_bar_customizer.php"));

$customizer = new C_top_bar_customizer($this->panel_menus,$this->top_panel_array);
$customizer->modify_arrays();
$this->panel_menus = $customizer->panel_menus;
$this->top_panel_array = $customizer->top_panel_array;


/*********************
 CLEAN UP MENU, CONDENSE DATA THAT HAVE EMPTY stat(us)
**********************/
foreach ( $this->panel_menus as $top_level => $menu_items )
{
	foreach ( $menu_items as $menu_key => $menu_data )
	{
		if ( strpos($menu_key, '__') === 0 ) { continue; } // Ignore magic menus

		if ( empty($menu_data['stat']) )
		{
			unset($this->panel_menus[$top_level][$menu_key]);
		}
	}
}

/*********************
 IN-REQUEST CACHES
**********************/
// Cache into $AI object; This file gets called multiple times and thus this logic can run multiple times unnecessarily
// The $AI->defined_menus property is checked at the top of the file
$AI->defined_menus['main_menu'] = $this->main_menu;
$AI->defined_menus['panel_menus'] = $this->panel_menus;
$AI->defined_menus['top_panel_array'] = $this->top_panel_array;
$AI->defined_menus['dashboard_panel_array'] = $this->dashboard_panel_array;
