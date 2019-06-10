INSERT INTO wp_perfman_staff(
 user_name,
 email,
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
 aud_inserted, 
 aud_inserted_by
) VALUES (
 @user_name,
 @email,
 @name,
 @nickname,
 @surname,
 now(),
 @personnel_number,
 @business_unit,
 @job_title,
 @manager_name,
 @manager_user_name,
 @manager_email,
 now(), 
 aud_inserted_by
)


INSERT INTO wp_perfman_staff_performance_plan(
 staff_user_name,
 staff_email,
 year,
 objective,
 source,
 target,
 stretch_target,
 training,
 results,
 weight,
 aud_inserted,
 aud_inserted_by
) VALUES (
 @staff_user_name,
 @staff_email,
 @year,
 @objective,
 @source,
 @target,
 @stretch_target,
 @training,
 @results,
 @weight,
 now(),
 @aud_inserted_by
)