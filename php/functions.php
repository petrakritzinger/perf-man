function contactform7_before_send_mail( $form_to_DB ) {
    global $wpdb;
    $user_login = $GLOBALS['my_user_login'];
    $form_to_DB = WPCF7_Submission::get_instance();
    if ( $form_to_DB ) 
        $formData = $form_to_DB->get_posted_data();
    $form_name = $formData['form-name'];

    switch ($form_name) {
        case 'hr-staff-creation-form':
            perfman_new_hr_staff_creation_form($formData);
            break;
            
        default:
            perfman_log_error($formData);
    }
}

add_action( 'init', 'get_user_info' );

function get_user_info(){
  $current_user = wp_get_current_user(); 

  if ( !($current_user instanceof WP_User) ) 
    return; 
  if (isset($current_user)) {
      error_log('in get_user_info '.$current_user->user_login);
      $GLOBALS['my_user_login'] = $current_user->user_login;
      $GLOBALS['my_user_email'] = $current_user->user_email;
  }
  return $current_user;
}

function perfman_new_hr_staff_creation_form($formData) {

     global $wpdb;
     
    $staff_email = $formData['staff-email'];
    $staff_name = $formData['staff-name'];
    $nickname = $formData['nickname'];
    $surname = $formData['surname'];
    $personnel_number = $formData['personnel-number'];
    $business_unit = $formData['business-unit'];
    $job_title = $formData['job-title'];
    $level = $formData['level'];
    $manager_name = $formData['manager-name'];
    $manager_email = $formData['manager-email'];
    //$manager = get_user_by_email($manager_email);
    

    $wpdb->insert( $wpdb->prefix . 'perfman_staff', 
            array(
                //'user_name' => $current_user->user_login,
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
                'aud_inserted' => date("Y-m-d H:i:s"),
                'aud_inserted_by' => $GLOBALS['my_user_login']                         
            )
        );
}

function perfman_log_error($formData) {

    $wpdb->insert( $wpdb->prefix . 'perfman_log', 
		    array( 
                          'log_date'  => date("Y-m-d H:i:s"),
                          'log_level' => 2,
                          'log_message' => "Could not find table for form " . $formData['form-name'],
                          'aud_inserted' => date("Y-m-d H:i:s"),
                          'aud_inserted_by' => $GLOBALS['my_user_login']                         
			)
		);
}

remove_all_filters ('wpcf7_before_send_mail');
add_action( 'wpcf7_before_send_mail', 'contactform7_before_send_mail' );