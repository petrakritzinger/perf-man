SELECT id,
 email,
 user_name,
 name,
 nickname,
 surname,
 date_added,
 personnel_number,
 business_unit,
 job_title,
 manager_name,
 manager_user_name,
 manager_email,
 level FROM wp_perfman_staff

Manager-Review-Target
SELECT p.staff_user_name as user_name, target_id, s.name, s.surname, s.user_name, p.objective, p.source, p.target, p.stretch_target, p.training, p.results, p.weight
FROM wp_perfman_staff_performance_plan p 
	left join wp_perfman_staff s on p.staff_user_name = s.user_name
    left join wp_users u on s.manager_email = u.user_email
WHERE year=2019 and u.ID = {current_user_id}

Staff view details

SELECT email as Email, user_name, 
    name as Name, nickname as 'Known as', surname as Surname, personnel_number as 'Personnel number', 
    business_unit as 'Business unit', job_title as 'Job title', manager_name as 'Manager', 
    manager_email as 'Manager email', 
    CONCAT('<a href="http://begeekings.com/perfman-staff-checkdetails/?username=', IFNULL(user_name,''), '&staffname=', IFNULL(name,''), 
    '&nickname=', IFNULL(nickname,''), '&surname=', IFNULL(surname,''), '&staffemail=', IFNULL(email,''), '&personnelnumber=', IFNULL(personnel_number,''), '&businessunit=', IFNULL(business_unit,''), '&jobtitle=', IFNULL(job_title,''), '&managername=', IFNULL(manager_name,'') , '&manageremail=', IFNULL(manager_email,''), ' ">UPDATE</a>') AS 'Update'
FROM wp_perfman_staff s
left join wp_users u on s.user_name = u.user_login
WHERE u.ID = {current_user_id}

Staff view performance plan

SELECT  objective, source, target, stretch_target, training, results, weight,
    CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/staff-change-target/?objective=', IFNULL(objective,''), 
    '&source=', IFNULL(source,''), 
    '&target=', IFNULL(target,''), 
    '&stretch_target=', IFNULL(stretch_target,''), 
    '&training=', IFNULL(training,''), 
    '&results=', IFNULL(results,''), 
    '&weight=', IFNULL(weight,''), 
    '&target_id=', IFNULL(target_id,''), 
    '&targetyear=', IFNULL(year,''), ' ">UPDATE</a>') AS 'Update', 
    IFNULL(target1_level,
    CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/staff-term-review/?target_id=', target_id, 
     ' ">REVIEW</a>')) AS 'Target Level 1', 
    IFNULL(target2_level,
    CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/staff-term-2-review/?target_id=', target_id, 
    '&targetyear=', IFNULL(year,''),
     ' ">REVIEW</a>')) AS 'Target Level 2'
FROM wp_perfman_staff_performance_plan p
left join wp_users u on p.staff_user_name = u.user_login
where year=2019 and u.ID = {current_user_id}

Manager View Staff

SELECT CONCAT(nickname, ' ', surname) as Name, email as Email, personnel_number as 'Personnel Number', business_unit as 'Business Unit', job_title as 'Job Title',
  CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-view-staff-targets/?staff_user_name=', user_name,
  ' ">VIEW TARGETS</a>') AS 'Targets'
FROM wp_perfman_staff s
left join wp_users u on s.manager_email = u.user_email
where u.ID = {current_user_id}

Manager Staff Targets

SELECT staff_user_name as Name, focus_area as 'Focus Area', objective as Objective, source as Source, target as Target, stretch_target as 'Stretch Target', training as 'Training', results as Results, weight as Weight,
    target1_level as 'Term 1 Level',
    target2_level as 'Term 2 Level'
FROM wp_perfman_staff_performance_plan p
left join wp_perfman_staff s on p.staff_user_name = s.user_name
left join wp_users u on s.manager_email = u.user_email
where year=2019 and u.ID = {current_user_id}

Manager targets for approval

SELECT staff_user_name, count(target_id), 
CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-view-staff-targets/?staff_user_name=', user_name,
  ' ">VIEW TARGETS</a>') AS 'Targets'
FROM wp_perfman_staff_performance_plan p
left join wp_perfman_staff s on p.staff_user_name = s.user_name
left join wp_users u on s.manager_email = u.user_email
WHERE target is not null and u.ID = {current_user_id}
  AND (manager_approved is null or manager_approved = 0)
group by staff_user_name

Manager staff targets for approval

SELECT staff_user_name as Name, focus_area as 'Focus Area', objective as Objective, source as Source, target as Target, stretch_target as 'Stretch Target', training as 'Training', results as Results, weight as Weight,
    CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-update-target/?objective=', IFNULL(objective,''), 
    '&source=', IFNULL(source,''), 
    '&target=', IFNULL(target,''), 
    '&stretch_target=', IFNULL(stretch_target,''), 
    '&training=', IFNULL(training,''), 
    '&results=', IFNULL(results,''), 
    '&weight=', IFNULL(weight,''), 
    '&target_id=', IFNULL(target_id,''), 
    '&targetyear=', IFNULL(year,''), ' ">UPDATE</a>') AS 'Update', 
     CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-review-target/?user_name=', user_name,
    '&target_id=', IFNULL(target_id,''), 
  ' ">APPROVE</a>') AS 'Approve Target'
FROM wp_perfman_staff_performance_plan p
left join wp_perfman_staff s on p.staff_user_name = s.user_name
left join wp_users u on s.manager_email = u.user_email
where year=2019 and target is not null and u.ID = {current_user_id}
  AND (manager_approved is null or manager_approved = 0)

Manager targets for term 1 review

SELECT staff_user_name, count(target_id), 
CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-view-staff-targets-for-term1-review/?staff_user_name=', user_name,
  ' ">VIEW TARGETS</a>') AS 'View Targets'
FROM wp_perfman_staff_performance_plan p
left join wp_perfman_staff s on p.staff_user_name = s.user_name
left join wp_users u on s.manager_email = u.user_email
WHERE target is not null and u.ID = {current_user_id}
  AND manager_approved = 1 and target1_level is not null and target1_manager_reviewed is null and year=2019
group by staff_user_name

Manager-staff-targets-for-term1-review

SELECT staff_user_name as Name, focus_area as 'Focus Area', objective as Objective, source as Source, target as Target, stretch_target as 'Stretch Target', training as 'Training', results as Results, weight as Weight,
    target1_level as 'Staff Review',
    CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-update-term1-review/?target_id=', IFNULL(target_id,''), 
    '&target1_level=', IFNULL(target1_level,''), 
    '&targetyear=', IFNULL(year,''), ' ">UPDATE</a>') AS 'Update', 
     CONCAT('<a style="text-decoration:underline;color:blue" href="http://begeekings.com/manager-approve-term1-review/?user_name=', user_name,
    '&target_id=', IFNULL(target_id,''), 
  ' ">APPROVE</a>') AS 'Approve Term 1 Review'
FROM wp_perfman_staff_performance_plan p
left join wp_perfman_staff s on p.staff_user_name = s.user_name
left join wp_users u on s.manager_email = u.user_email
where year=2019 and target is not null and u.ID = {current_user_id}
  AND manager_approved = 1 and target1_level is not null and target1_manager_reviewed is null

Manager-Approve-Term1-Review

SELECT p.staff_user_name as user_name, target_id, s.name, s.surname, s.user_name, p.objective, p.source, p.target, p.stretch_target, p.training, p.results, p.weight, p.target1_level as 'Term 1 Level'
FROM wp_perfman_staff_performance_plan p 
	left join wp_perfman_staff s on p.staff_user_name = s.user_name
    left join wp_users u on s.manager_email = u.user_email
WHERE year=2019 and u.ID = {current_user_id}