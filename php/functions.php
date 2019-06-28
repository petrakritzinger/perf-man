remove_action('virtue_page_title_container', 'virtue_page_title', 20);

add_shortcode('my-login-form', 'my_login_form_func');
function my_login_form_func($atts){
    if (!is_user_logged_in())
        return wp_login_form(array('echo'=> false));
}

function give_profile_name($atts){
    $user=wp_get_current_user();
    $name=$user->user_firstname; 
    return $name;
}

add_shortcode('profile_name', 'give_profile_name');


add_action( 'init', 'get_user_info' );

function get_user_info(){
    try {
        $current_user = wp_get_current_user(); 

        if ( !($current_user instanceof WP_User) ) 
            return; 
        if (isset($current_user)) {
            //error_log('in get_user_info '.$current_user->user_login);
            $GLOBALS['my_user_login'] = $current_user->user_login;
            $GLOBALS['my_user_email'] = $current_user->user_email;
        }
        return $current_user;
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function contactform7_before_send_mail( $form_to_DB ) {
    try {
        global $wpdb;
        //error_log("hello world!");
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

            case 'manager_review_target':
                perfman_manager_review_target($formData);
                break;

            case 'perfman_staff_update_target':
                perfman_staff_update_target($formData);
                break;

            case 'perfman_manager_update_target':
                perfman_manager_update_target($formData);
                break;

            case 'perfman_staff_term1_review':
                perfman_staff_term1_review($formData);
                break;
            
            case 'perfman_staff_term2_review':
                perfman_staff_term2_review($formData);
                break;
            
            case 'perfman_manager_approve_term1_review':
                perfman_manager_approve_term1_review($formData);
                break;
            
            case 'perfman_manager_update_term1_review':
                perfman_manager_update_term1_review($formData);
                break;

            case 'perfman_manager_approve_term2_review':
                perfman_manager_approve_term2_review($formData);
                break;
            
            case 'perfman_manager_update_term2_review':
                perfman_manager_update_term2_review($formData);
                break;
                
            default:
                perfman_log_error("Could not find table for form " . $formData['formname']);
        }
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_update_term2_review($formData) {
    try {
        global $wpdb;

        $manager_email = $formData['manager_email'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];
        $target_level = $formData['target_level'];
        $results = $formData['results'];

        $value = 0;

        switch ($target_level) {
            case 'Below Target':
                $value = 0;
                break;
            case 'Baseline Target':
                $value = 1;
                break;
            case 'Stretch Target':
                $value = 2;
                break;
            case 'Above Stretch':
                $value = 3;
                break;
            default:
                $value = 0;
        }

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'results' => $results,
                'target2_level' => $target_level,
                'target2_value' => $value,
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']  
            ),
            array(
                'manager_email' => $manager_email,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_approve_term2_review($formData) {
    try {
        global $wpdb;

        $username = $formData['user_name'];
        $year = $formData['targetyear'];
        $target_id = $formData['target_id'];
        $results = $formData['results'];

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'target2_manager_reviewed' => 1,
                'target2_manager_review_date' => date("Y-m-d H:i:s"),
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']                         
            ),
            array(
                'results' => $results,
                'staff_user_name' => $username,
                'year' => $year,
                'target1_manager_reviewed' => null,
                'target_id' => $target_id
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_update_term1_review($formData) {
    try {
        global $wpdb;

        $manager_email = $formData['manager_email'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];
        $target_level = $formData['target_level'];
        $results = $formData['results'];

        $value = 0;

        switch ($target_level) {
            case 'Below Target':
                $value = 0;
                break;
            case 'Baseline Target':
                $value = 1;
                break;
            case 'Stretch Target':
                $value = 2;
                break;
            case 'Above Stretch':
                $value = 3;
                break;
            default:
                $value = 0;
        }

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'results' => $results,
                'target1_level' => $target_level,
                'target1_value' => $value,
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']  
            ),
            array(
                'manager_email' => $manager_email,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_approve_term1_review($formData) {
    try {
        global $wpdb;

        $username = $formData['user_name'];
        $year = $formData['targetyear'];
        $target_id = $formData['target_id'];
        $results = $formData['results'];

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'results' => $results,
                'target1_manager_reviewed' => 1,
                'target1_manager_review_date' => date("Y-m-d H:i:s"),
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']                         
            ),
            array(
                'staff_user_name' => $username,
                'year' => $year,
                'target1_manager_reviewed' => null,
                'target_id' => $target_id
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_update_target($formData) {
    try {
        global $wpdb;

        $manager_email = $formData['manager_email'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];

        
        $objective = $formData['objective'];
        $source = $formData['source'];
        $target = $formData['target'];
        $stretch_target = $formData['stretch_target'];
        $training = $formData['training'];
        $results = $formData['results'];
        $weight = $formData['weight'];
    

        error_log('Updating target for ' . $username .', ' . $target_id . ', ' . $year);

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                
                'objective' => $objective,
                'source' => $source,
                'target' => $target,
                'stretch_target' => $stretch_target,
                'training' => $training,
                'results' => $results,
                'weight' => $weight,
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']          
            ),
            array(
                'manager_email' => $manager_email,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_staff_term2_review($formData) {
    try {
        global $wpdb;

        $username = $formData['username'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];
        $target_level = $formData['target_level'];
        $results = $formData['results'];

        $value = 0;

        switch ($target_level) {
            case 'Below Target':
                $value = 0;
                break;
            case 'Baseline Target':
                $value = 1;
                break;
            case 'Stretch Target':
                $value = 2;
                break;
            case 'Above Stretch':
                $value = 3;
                break;
            default:
                $value = 0;
        }

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'results' => $results,
                'target2_level' => $target_level,
                'target2_value' => $value,
                'target2_completed_date' => date("Y-m-d H:i:s"),
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']          
            ),
            array(
                'staff_user_name' => $username,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_staff_term1_review($formData) {
    try {
        global $wpdb;

        $username = $formData['username'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];
        $target_level = $formData['target_level'];
        $results = $formData['results'];

        error_log('Updating target for ' . $username .', ' . $target_id . ', ' . $target_level);

        $value = 0;

        switch ($target_level) {
            case 'Below Target':
                $value = 0;
                break;
            case 'Baseline Target':
                $value = 1;
                break;
            case 'Stretch Target':
                $value = 2;
                break;
            case 'Above Stretch':
                $value = 3;
                break;
            default:
                $value = 0;
        }

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'results' => $results,
                'target1_level' => $target_level,
                'target1_value' => $value,
                'target1_completed_date' => date("Y-m-d H:i:s"),
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']          
            ),
            array(
                'staff_user_name' => $username,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}



function perfman_staff_update_target($formData) {
    try {
        global $wpdb;

        $username = $formData['username'];
        $target_id = $formData['target_id'];
        $year = $formData['targetyear'];

        
        $objective = $formData['objective'];
        $source = $formData['source'];
        $target = $formData['target'];
        $stretch_target = $formData['stretch_target'];
        $training = $formData['training'];
        $results = $formData['results'];
        $weight = $formData['weight'];
    

        error_log('Updating target for ' . $username .', ' . $target_id . ', ' . $year);

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                
                'objective' => $objective,
                'source' => $source,
                'target' => $target,
                'stretch_target' => $stretch_target,
                'training' => $training,
                'results' => $results,
                'weight' => $weight,
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']          
            ),
            array(
                'staff_user_name' => $username,
                'target_id' => $target_id,
                'year' => $year
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_manager_review_target($formData) {
    try {
        global $wpdb;

        $username = $formData['user_name'];
        $year = $formData['targetyear'];
        $target_id = $formData['target_id'];

        $wpdb->update( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'manager_approved' => 1,
                'manager_approved_date' => date("Y-m-d H:i:s"),
                'aud_updated' => date("Y-m-d H:i:s"),
                'aud_updated_by' => $GLOBALS['my_user_login']                         
            ),
            array(
                'staff_user_name' => $username,
                'year' => $year,
                'manager_approved' => null,
                'target_id' => $target_id
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_staff_add_target($formData) {
    try {
        global $wpdb;

        $username = $formData['username'];
        $year = $formData['targetyear'];
        //error_log('year: ' . $year);
        $focus_area = $formData['focus_area'];
        $objective = $formData['objective'];
        $source = $formData['source'];
        $target = $formData['target'];
        $stretch_target = $formData['stretch_target'];
        $training = $formData['training'];
        $results = $formData['results'];
        $weight = $formData['weight'];
        $manager_email = perfmon_get_manager_email($username);
        //error_log('manager email for ' . $username . ' : ' . $manager_email);
        if (!isset($manager_email)) {
            $manager_email = "NA";
        }

        $wpdb->insert( $wpdb->prefix . 'perfman_staff_performance_plan', 
            array(
                'staff_user_name' => $username,
                'year' => $year,
                'focus_area' => $focus_area,
                'objective' => $objective,
                'source' => $source,
                'target' => $target,
                'stretch_target' => $stretch_target,
                'training' => $training,
                'results' => $results,
                'weight' => $weight,
                'manager_email' => $manager_email,
                'aud_inserted' => date("Y-m-d H:i:s"),
                'aud_inserted_by' => $GLOBALS['my_user_login']                         
            )
        );



        $subject = 'Performance Management review targets for ' . $username;
        
        $body = 'Please review staff targets for  <a href="http://begeekings.com/manager-view-staff-targets-for-approval/?staff_user_name=' . $username . '">' . $username .'</a>';
        //error_log($body);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        if ($manager_email != "NA") {
            perfman_send_email($manager_email, $subject, $body);
        }
        //wp_mail( $manager_email, $subject, $body, $headers );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfmon_get_manager_email($username) {
    try {
        global $wpdb;
        $sql_query = "SELECT manager_email FROM wp_perfman_staff WHERE user_name = '" . $username."'";
        error_log($sql_query);
        $manager_email = $wpdb->get_var( $sql_query);
        return $manager_email;
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_new_hr_staff_creation_form($formData) {
    try {
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
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_staff_checkdetails_form($formData) {
    try {
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
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_log_error($message) {
    try {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'perfman_log', 
            array( 
                            'log_date'  => date("Y-m-d H:i:s"),
                            'log_level' => 2,
                            'log_message' => $message,
                            'aud_inserted' => date("Y-m-d H:i:s"),
                            'aud_inserted_by' => $GLOBALS['my_user_login']                         
            )
        );
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

function perfman_send_email($send_to, $subject, $body) {
    try {
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
    } catch (Exception $e) {
        perfman_log_error("Error: " . $e->getMessage());
    }
}

remove_all_filters ('wpcf7_before_send_mail');
add_action( 'wpcf7_before_send_mail', 'contactform7_before_send_mail' );