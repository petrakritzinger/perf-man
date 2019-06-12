CREATE TABLE wp_perfman_staff (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    email varchar(100) NOT NULL,
    user_name varchar(100),
    name varchar(100),
    nickname varchar(100),
    surname varchar(100),
    date_added datetime,
    personnel_number varchar(20),
    business_unit varchar(10),
    job_title varchar(100),
    manager_name varchar(100),
    manager_user_name varchar(100),
    manager_email varchar(100),
    level tinyint,
    aud_inserted datetime,
    aud_inserted_by varchar(100),
    aud_updated datetime,
    aud_updated_by varchar(100),
    PRIMARY KEY (id),
    UNIQUE KEY(email) 
)

CREATE TABLE wp_perfman_staff_performance_plan (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    staff_email varchar(100) NOT NULL,
    staff_user_name varchar(100), 
    year int(4),
    objective varchar(500),
    source varchar(100),
    target varchar(500),
    stretch_target varchar(500),
    training varchar(500),
    results varchar(500),
    weight decimal(3,2),
    manager_approved tinyint,
    manager_approved_date datetime,
    target1_level varchar(50),
    target1_value decimal(3,2),
    target1_completed_date datetime,
    target1_manager_reviewed tinyint,
    target1_manager_review_date datetime,
    target2_level varchar(50),
    target2_value decimal(3,2),
    target2_completed_date datetime,
    target2_manager_reviewed tinyint,
    target2_manager_review_date datetime,
    aud_inserted datetime,
    aud_inserted_by varchar(100),
    aud_updated datetime,
    aud_updated_by varchar(100),
    PRIMARY KEY (id),
    UNIQUE KEY(staff_email, year)
)

CREATE TABLE wp_perfman_log (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    log_date datetime,
    log_level tinyint,
    log_message varchar(500),
    aud_inserted datetime,
    aud_inserted_by varchar(100),
    PRIMARY KEY (id)
)

CREATE TABLE wp_perfman_email (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    date_sent datetime,
    sent_to varchar(100),
    message_subject varchar(100),
    message_body varchar(500),  
    PRIMARY KEY (id)
)