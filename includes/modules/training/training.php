<?
global $AI;

require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_training_categories.php' ) );
require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_training_lessons.php' ) );
require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_training_completes.php' ) );
require_once( ai_cascadepath( 'includes/modules/training/graduation_manager/includes/class.te_graduation_manager.php' ) );
require_once( ai_cascadepath( 'includes/modules/training/graduation_tracking/includes/class.te_graduation_tracking.php' ) );
require_once( ai_cascadepath( 'includes/modules/training/manage_quizzes/includes/class.te_manage_quizzes.php' ) );
require_once( ai_cascadepath( 'includes/modules/training/quiz_history/includes/class.te_quiz_history.php' ) );
require_once( ai_cascadepath( 'includes/modules/training/quiz_question_manager/includes/class.te_quiz_question_manager.php' ) );

$training_module = aimod_get_module('training');
$url_base = $training_module->training_page_name;
if($AI->skin->vars['skinname']!='full_page')
{
	$AI->skin->css('includes/plugins/lead_management/lead_management.css');
	$AI->skin->css('includes/modules/training/training.css');
	$AI->skin->css('includes/modules/training/training.local.css');//File for local site css changes to training
	$AI->skin->js('includes/modules/training/training.js');
	echo '<div id="training_vars" data-page_name="'.$url_base.'"> </div>';
}


$module_info = aimod_get_module('training');

// Default Variables
$lesson_display = 1;

$te_mode    = util_GET('te_mode', 'table');
$te_key     = util_GET('te_key');
$te_class   = util_GET('te_class');
$ai_query_1 = util_GET('ai_query', null, 1);
$ai_query_2 = util_GET('ai_query', null, 2);
$ai_query_3 = util_GET('ai_query', null, 3);


if(($ai_query_2 != NULL || $ai_query_3 != NULL) || $te_class != NULL)
{
	if($AI->skin->vars['skinname']!='full_page')
	{
		echo '<a href="'.$url_base.'">Back To Main Page</a>';
	}
}


// We don't want this to go to anonymous users, however, this is a temporary solution
$access_groups = $AI->get_user_access_groups($AI->user->userID);
if($AI->user->isLoggedIn()) {
	$access_group_text = " access_group IN ('Everyone','All Logged In','" . implode("','", array_map('db_in', $access_groups)) . "') ";
} else {
	$access_group_text = " access_group IN ('Everyone','" . implode("','", array_map('db_in', $access_groups)) . "') ";
}



if(($complete_id =util_GET('mark_complete',false)) !== false)
{
	$complete_id = intval($complete_id);
	$quiz_id = (int) db_lookup_scalar("SELECT quiz_id FROM quiz WHERE training_id =".$complete_id,false);

	//they must have completed a quiz to continue
	if($quiz_id)
	{
		$res = db_query('SELECT qs.quiz_id, qs.number_correct, qs.userID, qs.total_questions, qs.perc_required FROM quiz_submissions AS qs WHERE qs.quiz_id='.$quiz_id.' AND qs.userID='.$AI->user->userID);

		$complete = false;
		while($res && ($row = db_fetch_assoc($res)))
		{
			if($row['total_questions'] == 0)continue;//to pervent devide by zero errors on older sites
			$percentage = ($row['number_correct']/$row['total_questions'])*100;
			if($percentage >= $row['perc_required'])
			{
				$complete = true;
				break;
			}
		}
		if(!$complete)die('ajax_run_script|jonbox_alert("This quiz can not be marked complete, You did not score high enough on the quiz);');
	}
	
	$te_training_completes = new C_te_training_completes();
	$te_training_completes->writable_db_field['lesson_id'] = true;
	$te_training_completes->writable_db_field['userID'] = true;
	$te_training_completes->db['lesson_id'] = $complete_id;
	$te_training_completes->db['userID'] = $AI->user->userID;
	$te_training_completes->insert();

	//Jason Moniz 1/28/2014
	//ADD in graduation handler trigger start
	$te_graduation_manager = new C_te_graduation_manager();
	$message = $te_graduation_manager->trigger_graduation($AI->user->userID, $complete_id);

	//Add in graduation handler trigger end
	if($message == false)
	{
		echo 'ajax_run_script|$.noop();';
	}
	else
	{
		echo 'ajax_run_script|jonbox_info("'.$message.'");';
	}
	return;
}


if(($ai_query_1 == "manage" && $ai_query_2 == "categories") || $te_class == "training_categories") {
	$te_training_categories = new C_te_training_categories();
	$te_training_categories->run_TableEdit();

} else if(($ai_query_1 == "reporting" && $ai_query_2 == "downline_stats" )){
	require_once(ai_cascadepath('includes/modules/training/downline_reporting.php'));
} else if(($ai_query_1 == "manage" && $ai_query_2 == "lessons") || $te_class == "training_lessons") {
	$te_training_lessons = new C_te_training_lessons();
	$te_training_lessons->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "completes") || $te_class == "training_completes") {
	$te_training_completes = new C_te_training_completes();
	$te_training_completes->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "graduation") || $te_class == "graduation_manager") {
	$te_graduation_manager = new C_te_graduation_manager();
	$te_graduation_manager->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "graduation_tracking") || $te_class == "graduation_tracking") {
	$te_graduation_tracking = new C_te_graduation_tracking();
	$te_graduation_tracking->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "quizzes") || $te_class == "manage_quizzes") {
	$te_manage_quizzes = new C_te_manage_quizzes();
	$te_manage_quizzes->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "history") || $te_class == "quiz_history" ) {
	$te_quiz_history = new C_te_quiz_history();
	$te_quiz_history->run_TableEdit();
} else if(($ai_query_1 == "manage" && $ai_query_2 == "questions") || $te_class == "quiz_question_manager") {

	$te_manage_quizzes = new C_te_manage_quizzes();
	$te_manage_quizzes->run_TableEdit();
	/*
	 Removed By Jason 1/13/2014 we want this table edit to be a sub table edit of quiz history
		$te_quiz_question_manager = new C_te_quiz_question_manager();
		$te_quiz_question_manager->run_TableEdit();
		*/
} else {

	if($ai_query_1 == "category") {

		$selected_category = $ai_query_2;
		if ( !empty($ai_query_3) ) {
			$selected_lesson = (int) $ai_query_3;
		}
	} else {

		/*
			$selected_category = db_lookup_scalar("SELECT id FROM training_categories WHERE " . $access_group_text . " ORDER BY sort_order");
			if(empty($selected_category)) {
			$selected_category = db_lookup_scalar("SELECT id FROM training_categories ORDER BY sort_order");
			}
			*/
		$selected_category = 0;
		$selected_lesson = 0;
	}


	//FETCH THE LESSONS
	if ( isset($_GET['get_lesson_id']) ) {
		$training_lessons = $AI->db->getAll("SELECT * FROM training_lessons WHERE category_id = '" . db_in($selected_category) . "' AND " . $access_group_text . " AND id=" . (int)$selected_lesson,'id');
	} else {
		$training_lessons = $AI->db->getAll("SELECT * FROM training_lessons WHERE category_id = '" . db_in($selected_category) . "' AND " . $access_group_text . " ORDER BY sort_order ASC, id ASC",'id');
	}

	//BUILD CHILDREN ARRAY
	$tl_children=array(); $tl_next=array(); $last_id=0;
	if(!empty($training_lessons)) {
		foreach($training_lessons as $id=>$t) {
			//TODO: Double check that it is alright to just remove this part
			//				if(!is_array($tl_children[intval($t['prerequisite_id']).''])) $tl_children[intval($t['prerequisite_id']).'']=array();

			//if prerequesite id is not in the lesson we set prerequisite to 0
			$prereq_in_lesson = (int) db_lookup_scalar("SELECT COUNT(*) FROM training_lessons WHERE category_id = '" . db_in($selected_category) . "' AND id=".(int)$t['prerequisite_id']);
			if($prereq_in_lesson)
			{
				$tl_children[intval($t['prerequisite_id']).''][]=$id;
			}
			else
			{
				$tl_children[0][]=$id;
			}
			//			if($last_id) $tl_next[$last_id]=$id;
			$last_id=$id;
		}
	}


	//FIX ANY ORDERING ISSUES
	if ( !isset($_GET['get_lesson_id']) ) {
		reorder_lessons($training_lessons,$tl_children);
	}

	foreach($training_lessons AS $id=>$t)
	{
		if($t['prerequisite_id']!=0)
		{
			$tl_next[$t['prerequisite_id']]= $id;
		}
	}



	//FETCH COMPLETED COURSES
	$completes = $AI->db->getAll("SELECT lesson_id FROM training_completes WHERE userID = '" . (int) $AI->user->userID . "' ",'lesson_id');
	//FETCH CATEGORY NAME:
	$category_name = db_lookup_scalar("SELECT name FROM training_categories WHERE id = " . (int) $selected_category);

	// Use selected lesson as the active lesson (IF valid, i.e. there are no prerequisites left)
	if ( !empty($selected_lesson) && isset($training_lessons[$selected_lesson]) ) {
		$req_id = $training_lessons[$selected_lesson]['prerequisite_id'];
		if($req_id==0 || isset($completes[$req_id])) $active_lesson = $selected_lesson;
	}
	// ELSE Get the next open lesson, if all completed, get the first
	if ( empty($active_lesson) ) {
		foreach($training_lessons as $id=>$tl) { if(!isset($completes[$id])) { $active_lesson=$id; break; } }
		if(empty($active_lesson)) $active_lesson=@array_shift(array_keys($training_lessons));
	}


	if ( !isset($_GET['get_lesson_id']) ) {
		echo "<div class=\"training_container\">\n";
		echo "<div class=\"row\">\n";
		echo "<div style=\"padding: 10px 10px 0 10px;\" class=\"col-sm-12\">\n";
		echo "<h2 id=\"page-header\">".tt('Success Education')."</h2>\n";
		//echo "<p>Browse through the step-by-step training below to learn more about the marketing system and your opportunity, as well as exclusive sales training.</p>\n";
		echo $AI->get_dynamic_area('training_module_header_text');
		echo "</div>\n";
		echo "</div>\n";


		echo "<div class=\"row\">\n";


		echo "<div class=\"training_left_side col-md-8 col-xs-12 col-sm-12\">\n";
	}

	if(empty($training_lessons)) {
		if ( isset($_GET['get_lesson_id']) ) {
			echo "There are no lessons in this category...";
		} else {
			if ( !empty($selected_category) ) {
				echo "There are no lessons in this category...";
			} else {
				echo '<hr>';
				echo $AI->get_dynamic_area('training_module_root_category_intro');
			}

			require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_training_lessons.php' ) );
			$te_training_lessons = new C_te_training_lessons();
			if( $te_training_lessons->te_permit['insert'] )
			{
				echo "<a class=\"te_button te_new_button\" href=\"$url_base?te_class=training_lessons&te_mode=insert\" title=\"New\"><span class=\"te_button te_new_button\">Click here to create a new lesson</span></a>\n";
			}
		}
	} else {

		if(!empty($category_name))
		{
			if ( !isset($_GET['get_lesson_id']) ) {
				echo "<div style=\"padding: 0 10px;\">\n";
				echo "<h3>" . h($category_name) . "</h3>\n";
				echo "</div>\n";
			}
		}
		//$user_acodes = ($AI->user->userID>0)? $AI->get_user_access_codes($AI->user->userID):array();

		if ( !isset($_GET['get_lesson_id']) ) {
			echo "<div class=\"training_lessons row\">\n";
		}



		foreach($training_lessons as $tlid=>$tl) {
			/*
				START COPY PASTE
			 */
			$quiz_id = (int) db_lookup_scalar("SELECT quiz_id FROM quiz WHERE training_id = ".$tl['id']);
			$percent_needed = (int) db_lookup_scalar("SELECT completion_percent FROM quiz WHERE training_id = ".$tl['id']);
			//MOVED FOR CHANGE
			$number_of_questions = (int)db_lookup_scalar("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=$quiz_id");
			$taken_quiz_flag = (int)db_lookup_scalar("SELECT COUNT(*)FROM quiz_submissions WHERE quiz_id=$quiz_id AND userID=".$AI->user->userID);
			$number_correct = (int) db_lookup_scalar("SELECT number_correct FROM quiz_submissions WHERE quiz_id=$quiz_id AND userID=".$AI->user->userID." ORDER BY submission_id DESC LIMIT 1");

			/*
			 END COPY PASTE
			 */
			$lock_style = '';
			$training_lessons_style = $training_lessons_block_active = "";

			$lesson_complete = (int) db_lookup_scalar("SELECT id FROM training_completes WHERE lesson_id = '" . db_in($tl['id']) . "' AND userID = '" . (int) $AI->user->userID . "' ");

			if(!isset($tl_next[$tlid]))
			{

				$next_lesson = (int)db_lookup_scalar("SELECT id FROM training_lessons WHERE prerequisite_id = ".(int)$tl['id']." ORDER BY id ASC LIMIT 1");
				if($next_lesson==0)
				{
					$next_lesson = (int)db_lookup_scalar("SELECT id FROM training_lessons WHERE sort_order > ".(int)$tl['sort_order']." AND category_id=".(int)$tl['category_id']." ORDER BY sort_order ASC LIMIT 1");
					if($next_lesson==0)
					{
						$next_lesson = (int)db_lookup_scalar("SELECT id FROM training_lessons WHERE id > ".(int)$tl['id']." AND category_id=".(int)$tl['category_id']." ORDER BY id ASC LIMIT 1");
					}
				}
			}
			else
			{
				$next_lesson = @$tl_next[$tlid]; //next($training_lessons); prev($training_lessons);// db_lookup_scalar("SELECT id FROM training_lessons WHERE category_id = '" . db_in($selected_category) . "' AND " . $access_group_text . " AND id > '" . db_in($tl['id']) . "' ORDER BY id ");
			}

			$prerequisite = (int) $tl['prerequisite_id'];
			$prerequisite_complete = isset($completes[$prerequisite]);// (int) db_lookup_scalar("SELECT id FROM training_completes WHERE lesson_id = '" . db_in($tl['prerequisite_id']) . "' AND userID = '" . (int) $AI->user->userID . "' ");
			if($active_lesson == $tl['id']) {
				$training_lessons_style = "training_lessons_active";
				$training_lessons_block_active = "training_lessons_block_active";
			}

			$acode_error = '';
			$tl['acode'] = strtolower($tl['acode']);

			if(@$tl['acode']!='' && !$AI->get_active_status_for_acode($tl['acode'],$AI->user->userID)) {
				//They don't have access to this lesson yet
				$acode_error = $AI->get_dynamic_area($tl['acode_fail_msg'], array('edit'=>false));
				if($acode_error!='') $acode_error = $AI->get_dynamic_area($tl['acode_fail_msg']);
				else $acode_error = "You don't have access to this Lesson!";
			}

			if ( !isset($_GET['get_lesson_id']) ) {
				echo "<a href=\"javascript:void(0)\" id=\"lesson_" . (int) $tl['id'] . "_link\" class=\"lesson_link training_lessons_block " . $training_lessons_block_active . "\" data-id=\"" . (int) $tl['id'] . "\" data-category=\"" . (int) $selected_category . "\">\n";
				//if lesson is complete display a checkmark image else put a lock there indicating that users cannot view next lesson
				// this is for the title portion of the lesson bar
				//TODO: get top portion to display based on quiz data results as well
				if($lesson_complete) {
					echo "<img src=\"includes/modules/training/images/checkmark.png\" class=\"training_checkmark\" />\n";
				} else if ($prerequisite && !$prerequisite_complete) {
					echo "<img src=\"includes/modules/training/images/lock.png\" class=\"training_lock\" />\n";
				}
				echo "<div>" . h($tl['name']) . "</div>\n";
				echo "</a>\n";

				if ($prerequisite && !$prerequisite_complete) {
					$training_lessons_style = "training_lesson_locked";
				}

				echo "<div class=\"training_lessons_editor " . $training_lessons_style . "\" id=\"lesson_" . (int) $tl['id'] . "\">\n";

				if ($prerequisite && !$prerequisite_complete) {
					echo "<div class=\"training_lessons_lock_message\">\n";
					$locked_lesson_name = db_lookup_scalar("SELECT name FROM training_lessons WHERE id = '" . $tl['prerequisite_id'] . "' ");
					echo "This lesson cannot be completed until the lesson \"" . h($locked_lesson_name) . "\" has been completed.\n";
					echo "</div>\n";
					$lock_style = "training_lesson_lock_hidden";
				}
			}

			echo "<div class=\"training_lessons_lock " . $lock_style . "\">\n";

			if(!empty($acode_error)) echo '<img src="images/menu_tree/error_icon.png" class="training_lock" width=30 style="float:left;">' . $acode_error;
			else {

				if ($prerequisite && !$prerequisite_complete) {
					echo "<div class=\"training_lessons_lock_message\">\n";
					$locked_lesson_name = db_lookup_scalar("SELECT name FROM training_lessons WHERE id = '" . $tl['prerequisite_id'] . "' ");
					echo "This lesson cannot be completed until the lesson \"" . h($locked_lesson_name) . "\" has been completed.\n";
					echo "</div>\n";
					$lock_style = "training_lesson_lock_hidden";
				}
				else if ( $training_lessons_block_active != "" ) {
					echo $AI->get_dynamic_area('training_lessons_' . $tl['id']);
				} else {
					echo "Lesson Loading...";
				}
			}

			/**
			 * PASTE START
			 */
			if(!$lesson_complete && empty($acode_error)) {

				if (!$prerequisite || $prerequisite_complete) {
					if($quiz_id>0)
					{
						if($number_of_questions != 0)
						{
							$percentage = ($number_correct/$number_of_questions)*100;
						}
						else
						{
							$percentage = 0;
						}


						if($percentage < $percent_needed )
						{
							if($taken_quiz_flag==0)
							{
								echo "<div id=\"quiz_progression_fail\" >Must Take Quiz To Complete Lesson</div>\n";
							}
							else
							{
								echo "<div id=\"quiz_progression_fail\" >".number_format($percentage,0)."% Correct</div>\n";
							}
							echo "<a href='take_quiz.php?quiz_id=".$quiz_id."&url=".$url_base."&next_lesson=".$next_lesson."&unit=".$tl['id']."' rel=\"jonbox\" class=\"training_mark_complete take_quiz\">Take Quiz</a>\n";
						}
						else
						{
							$AI->skin->js_onload('training.mark_complete("'. $tl['id'] .'", "'.$next_lesson.'", "'.$url_base.'")');
						}
					}
					else
					{
						echo "<a href=\"#\" onclick=\"training.mark_complete('".$tl['id']."','".(int)$next_lesson."', '".$url_base."'); return false;\"  class=\"training_mark_complete\">Mark Lesson Complete</a>\n";
					}
				}
			}

			/**
			 * PASTE END
			 */

			/*
			 * TODO: This is a the original code modifying paste code  above to work with quiz system
			 if(!$lesson_complete && empty($acode_error)) {
				echo "<a href=\"javascript:void(0)\" onclick=\"training.mark_complete('" . (int) $tl['id'] . "','" . h($next_lesson) . "')\" class=\"training_mark_complete\">Mark Lesson Complete</a>\n";
				}
				*/
			echo "</div>";

			if ( !isset($_GET['get_lesson_id']) ) {
				echo "</div>\n";
			}

			$lesson_display++;
		}

		if ( !isset($_GET['get_lesson_id']) ) {
			echo "</div>\n";
		} else {
			exit;
		}
	}

	if ( !isset($_GET['get_lesson_id']) ) {
		echo "</div>\n";

		//echo "<br clear=\"all\">\n";

		/*echo "<script type=\"text/javascript\">\n";
			echo "var speed = 1000;\n";
			for($x=1;$x<=(db_lookup_scalar("SELECT max(id) FROM training_lessons"));$x++) {
			echo "$('#lesson_" . $x . "_link').click(function() {\n";
			echo "if (!$(this).hasClass(\"training_lessons_block_active\") ) {\n";
			echo "$('#lesson_" . $x . "_link').addClass('training_lessons_block_active');\n";
			echo "$('.training_lessons_editor').slideUp(speed);\n";
			echo "$('#lesson_" . $x . "').slideDown(speed, function() {\n";
			echo "$('.training_lessons_block').removeClass('training_lessons_block_active');\n";
			echo "$('#lesson_" . $x . "_link').addClass('training_lessons_block_active');\n";
			echo "$(";
			echo "});\n";
			echo "}\n";
			echo "});\n";
			}
			echo "function mark_complete(unit,next) {\n";
			echo "$('#lesson_' + unit + '_link').prepend('<img src=\"includes/modules/training/images/checkmark.png\" class=\"training_checkmark\" />');\n";
			echo "$('#lesson_' + unit + '_link .training_lock').remove();\n";
			echo "$('#lesson_' + unit + ' .training_mark_complete').remove();\n";
			echo "ajax_get_request('training/manage/completes?ai_skin=full_page&mark_complete=' + unit, ajax_handler_default );\n";
			echo "if(next != '') {\n";
			echo "$('#lesson_' + next + ' .training_lessons_lock_message').remove();\n";
			echo "$('#lesson_' + next + ' .training_lessons_lock').show();\n";
			echo "$('#lesson_' + next).removeClass('training_lesson_locked');\n";
			echo "$('#lesson_' + next + '_link').addClass('training_lessons_block_active');\n";
			echo "$('.training_lessons_editor').slideUp(speed);\n";
			echo "$('#lesson_' + next).slideDown(speed, function() {\n";
			echo "$('#lesson_' + next + '_link .training_lock').remove();\n";
			echo "$('.training_lessons_block').removeClass('training_lessons_block_active');\n";
			echo "$('#lesson_' + next + '_link').addClass('training_lessons_block_active');\n";
			echo "$('.training_lessons_editor').removeClass('training_lessons_active');\n";
			echo "$('#lesson_' + next).addClass('training_lessons_active');\n";
			echo "});\n";
			echo "}\n";
			echo "}\n";
			echo "</script>\n";*/
	}

	echo '<div class="col-md-1"></div>';//Empty div for proper spacing

	//Start right side menu
	aimod_run_hook('hook_education_level_meter');

	echo "<br>\n";
	echo "<table id=\"lead_control_buttons\" cellspacing=\"0\" border=\"0\">\n";


	$training_categories = $AI->db->getAll("SELECT id, thumbnail, name FROM training_categories WHERE " . $access_group_text . " ORDER BY sort_order ASC, id ASC");
	$siteHasCategories = false;
	if(empty($training_categories)) {


		require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_training_categories.php' ) );
		$te_training_categories = new C_te_training_categories();
		if( $te_training_categories->te_permit['insert'] )
		{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<div class=\"button_action_panel_headers\">Categories</div>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>\n";
			echo "There are no categories...";
			echo "<a class=\"te_button te_new_button\" href=\"$url_base?te_class=training_categories&te_mode=insert\" title=\"New\"><span class=\"te_button te_new_button\">Click here to create a new category</span></a>\n";
		}
	} else {
		$siteHasCategories = true;
		echo "<tr>\n";
		echo "<td>\n";
		echo "<div class=\"button_action_panel_headers\">Categories</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";


		echo "<div class=\"training_modules\">\n";
		echo "<ul class=\"no_bullets teambuilder_right_menu\">\n";
		foreach($training_categories as $tc) {
			echo "<li>";
			$count = (int) db_lookup_scalar("SELECT COUNT(*) FROM training_lessons WHERE category_id = " . (int) $tc['id'] . " AND id NOT IN (SELECT lesson_id FROM training_completes WHERE userID = " . (int) $AI->user->userID . ")");
			if($count > 0) {
				echo "<sup class=\"badge\">" . (int) $count . "</sup>\n";
			}
			echo "<a class=\"icon_button";
			if($tc['id'] == $selected_category) {
				echo " category_active";
			}
			echo "\" href=\"$url_base/category/" . (int) $tc['id'] . "\"><img src=\"" . h($tc['thumbnail'] == '' ? 'images/menu_tree/book_48.png' : 'image.php?w=32&h=32&imgurl='.urlencode($tc['thumbnail'])) . "\"><span title=\"" . h($tc['name']) . "\">" . h($tc['name']) . "</span></a></li>\n";
		}
		echo "</ul>";
		echo "</div>\n";
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";

	//Display a menu
	if($AI->user->isLoggedIn() && $AI->get_access_group_perm('Admin Only')) {
		echo "<div class=\"button_action_panel_headers\">Administration</div>\n";

		echo "<ul class=\"no_bullets teambuilder_right_menu\">\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base\"><img src=\"images/menu_tree/book_48.png\"><span>Training Module</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/categories\"><img src=\"images/menu_tree/book_48.png\"><span>Manage Categories</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/lessons\"><img src=\"images/menu_tree/book_48.png\"><span>Manage Lessons</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/graduation\"><img src=\"images/menu_tree/book_48.png\"><span>Manage Graduation</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/quizzes\"><img src=\"images/menu_tree/book_48.png\"><span>Manage Quizzes</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/history\"><img src=\"images/menu_tree/book_48.png\"><span>Quiz History</span></a></li>\n";
		// Removed by Jason 1/13/2014 we should not be showing this button and only allowing people to manage questions through the quiz table edit itself
		//echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"training/manage/questions\"><img src=\"images/menu_tree/book_48.png\"><span>Quiz Questions</span></a></li>\n";
		echo "<li><sup class=\"admin\"></sup><a class=\"icon_button\" href=\"$url_base/manage/completes\"><img src=\"images/menu_tree/book_48.png\"><span>View Completions</span></a></li>\n";
		echo "</ul>";
	}


	if($siteHasCategories && util_is_site_type('titanmlm'))
	{
		echo "<div class=\"button_action_panel_headers\">".tt('Distributor')."</div>\n";
		echo '<ul class="no_bullets teambuilder_right_menu">';
		echo "<li><a class=\"icon_button\" href=\"$url_base/reporting/downline_stats\"><img src=\"images/menu_tree/book_48.png\"><span>".tt('Downline Reporting')."</span></a></li>\n";
		echo '</ul>';
	}

	//Jason Moniz 7/14/2014 this work to hide graduation tools for now but we may need to come up with a better solution when we have more than one OTHER tool category
	if($module_info->show_graduation_tracking )
	{
		echo "<div class=\"button_action_panel_headers\">Other Tools</div>\n";
		echo '<ul class="no_bullets teambuilder_right_menu">';
		echo "<li><a class=\"icon_button\" href=\"$url_base/manage/graduation_tracking\"><img src=\"images/menu_tree/book_48.png\"><span>Graduation Tracking</span></a></li>\n";
		echo '</ul>';
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
	echo "</div>\n";


}



function reorder_lessons(&$lessons,&$children,$req_id=0) {
	//STARTING:
	static $order; if($order==null) $order = array();
	//RUNNING
	if(isset($children[$req_id])) {
		foreach($children[$req_id] as $id) {
			$order[]=$id;
			if(isset($children[$id])) reorder_lessons($lessons,$children,$id);
		}
	}
	//FINISHED

	if($req_id==0) {
		$new_lessons = array();
		foreach($order as $i=>$id) $new_lessons[$id]=$lessons[$id]; //if($lessons[$id]['sort_order']!=$i) db_query("UPDATE training_lessons SET sort_order=$i WHERE id=$id");
		$lessons=$new_lessons;
	}
}
?>
