CREATE TABLE wp_perfmon_staff (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    user_name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    name varchar(100),
    surname varchar(100),
    date_added datetime,
    personnel_number varchar(20),
    business_unit varchar(100),
    job_title varchar(100),
    manager_name varchar(100),
    manager_user_name varchar(100),
    manager_email varchar(100)
)

CREATE TABLE wp_perfmon_staff_performance_plan (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    staff_user_name varchar(100) NOT NULL,
    staff_email varchar(100) NOT NULL,
    year int(4),
    objective varchar(500),
    source varchar(100),
    target varchar(500),
    stretch_target varchar(500),
    training,
    results varchar(500),
    weight decimal(3,2),
    manager_approved tinyint,
    manager_approved_date datetime,
    
)