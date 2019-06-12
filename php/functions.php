add_action( 'init', 'get_user_info' );

function get_user_info(){
  $current_user = wp_get_current_user(); 

  if ( !($current_user instanceof WP_User) ) 
    return; 
  if (isset($current_user)) {
      //error_log('in get_user_info '.$current_user->user_login);
      $GLOBALS['my_user_login'] = $current_user->user_login;
      $GLOBALS['my_user_email'] = $current_user->user_email;
  }
  return $current_user;
}

function contactform7_before_send_mail( $form_to_DB ) {
    global $wpdb;
    $user_login = $GLOBALS['my_user_login'];
    $form_to_DB = WPCF7_Submission::get_instance();
    if ( $form_to_DB ) 
        $formData = $form_to_DB->get_posted_data();
    $form_name = $formData['formname'];
    error_log($form_name);
    switch ($form_name) {
        case 'hr-staff-creation-form':
            perfman_new_hr_staff_creation_form($formData);
            break;

        case 'perfman_staff_checkdetails':
            perfman_staff_checkdetails_form($formData);
            break;
        
        case 'perfman_staff_add_target':
            perfman_staff_add_target($formData);
            break;
            
        default:
            perfman_log_error($formData);
    }
}

function perfman_staff_add_target($formData) {

    global $wpdb;

    $username = $formData['username'];
    $year = $formData['targetyear'];
    error_log('year: ' . $year);
    $objective = $formData['objective'];
    $source = $formData['source'];
    $target = $formData['target'];
    $stretch_target = $formData['stretch_target'];
    $training = $formData['training'];
    $results = $formData['results'];
    $weight = $formData['weight'];

    $wpdb->insert( $wpdb->prefix . 'perfman_staff_performance_plan', 
        array(
            'staff_user_name' => $username,
            'year' => $year,
            'objective' => $objective,
            'source' => $source,
            'target' => $target,
            'stretch_target' => $stretch_target,
            'training' => $training,
            'results' => $results,
            'weight' => $weight,
            'aud_inserted' => date("Y-m-d H:i:s"),
            'aud_inserted_by' => $GLOBALS['my_user_login']                         
        )
    );

    $manager_email = perfmon_get_manager_email($username);
    error_log('manager email for ' . $username . ' : ' . $manager_email);

    $subject = 'Performance Management review targets for ' . $username;
    $body = 'Please review staff targets for  <a href="http://begeekings.com/manager-review-target/">' . $username .'</a>';
    //error_log($body);
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    perfman_send_email($manager_email, $subject, $body);
    //wp_mail( $manager_email, $subject, $body, $headers );
}

function perfmon_get_manager_email($username) {
    global $wpdb;
    $sql_query = "SELECT manager_email FROM wp_perfman_staff WHERE user_name = '" . $username."'";
    error_log($sql_query);
    $manager_email = $wpdb->get_var( $sql_query);
    return $manager_email;
}

function perfman_new_hr_staff_creation_form($formData) {

    global $wpdb;

    $staff_email = $formData['staffemail'];
    $staff_name = $formData['staffname'];
    $nickname = $formData['nickname'];
    $surname = $formData['surname'];
    $personnel_number = $formData['personnelnumber'];
    $business_unit = $formData['businessunit'];
    $job_title = $formData['jobtitle'];
    $level = $formData['level'];
    $manager_name = $formData['managername'];
    $manager_email = $formData['manageremail'];
    //$manager = get_user_by_email($manager_email);

    $newusername = str_replace(' ', '', $staff_name.$surname);
    $newuserpassword = wp_generate_password( 5, false );

    $wpdb->insert( $wpdb->prefix . 'perfman_staff', 
        array(
            'user_name' => $newusername,
            'email' => $staff_email,
            'name' => $staff_name,
            'nickname' => $nickname,
            'surname' => $surname,
            'date_added' => date("Y-m-d H:i:s"),
            'personnel_number' => $personnel_number,
            'business_unit' => $business_unit,
            'job_title' => $job_title,
            'manager_name' => $manager_name,
            'manager_email' => $manager_email,
            'aud_inserted' => date("Y-m-d H:i:s"),
            'aud_inserted_by' => $GLOBALS['my_user_login']                         
        )
    );

    
    wp_create_user( $newusername, $newuserpassword, $staff_email );
    
   
    $subject = 'Performance Management';
    $body = 'Please complete your performance review by <a href="http://begeekings.com/perfman-staff-checkdetails/?username=' . htmlentities($newusername) . '&staffname=' . htmlentities($staff_name) . '&nickname=' . htmlentities($nickname) .'&surname=' . htmlentities($surname) .'&staffemail=' . htmlentities($staff_email) .'&personnelnumber=' . htmlentities($personnel_number) .'&businessunit=' . htmlentities($business_unit) .'&jobtitle=' . htmlentities($job_title) .'&managername=' . htmlentities($manager_name) .'&manageremail=' . htmlentities($manager_email) .'">clicking this link</a><br/><br/>Please login to the Performance Management site with user ' . $newusername . ' and password ' . $newuserpassword;
    //error_log($body);
    $headers = array('Content-Type: text/html; charset=UTF-8');
     
    perfman_send_email($staff_email, $subject, $body);
    //wp_mail( $staff_email, $subject, $body, $headers );
}

function perfman_staff_checkdetails_form($formData) {

    global $wpdb;

    $username = $formData['username'];
    $staff_email = $formData['staffemail'];
    $staff_name = $formData['staffname'];
    $nickname = $formData['nickname'];
    $surname = $formData['surname'];
    $personnel_number = $formData['personnelnumber'];
    $business_unit = $formData['businessunit'];
    $job_title = $formData['jobtitle'];
    $manager_name = $formData['managername'];
    $manager_email = $formData['manageremail'];

    $wpdb->update( $wpdb->prefix . 'perfman_staff', 
        array(
            'email' => $staff_email,
            'name' => $staff_name,
            'nickname' => $nickname,
            'surname' => $surname,
            'date_added' => date("Y-m-d H:i:s"),
            'personnel_number' => $personnel_number,
            'business_unit' => $business_unit,
            'job_title' => $job_title,
            'manager_name' => $manager_name,
            'manager_user_name' => $manager_email,
            'aud_updated' => date("Y-m-d H:i:s"),
            'aud_updated_by' => $GLOBALS['my_user_login']                         
        ),
        array(
            'user_name' => $username
        )
    );
}

function perfman_log_error($formData) {
    global $wpdb;
    $wpdb->insert( $wpdb->prefix . 'perfman_log', 
		    array( 
                          'log_date'  => date("Y-m-d H:i:s"),
                          'log_level' => 2,
                          'log_message' => "Could not find table for form " . $formData['formname'],
                          'aud_inserted' => date("Y-m-d H:i:s"),
                          'aud_inserted_by' => $GLOBALS['my_user_login']                         
			)
		);
}

function perfman_send_email($send_to, $subject, $body) {
    global $wpdb;

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $send_to, $subject, $body, $headers );

    $wpdb->insert( $wpdb->prefix . 'perfman_email', 
		    array( 
                          'date_sent'  => date("Y-m-d H:i:s"),
                          'sent_to' => $send_to,
                          'message_subject' => $subject,
                          'message_body' => $body                    
			)
		);
}

remove_all_filters ('wpcf7_before_send_mail');
add_action( 'wpcf7_before_send_mail', 'contactform7_before_send_mail' );