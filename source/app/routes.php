<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 */
Route::model('user', 'User');
Route::model('comment', 'Comment');
Route::model('post', 'Post');
Route::model('role', 'Role');

/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------
 */
Route::pattern('comment', '[0-9]+');
Route::pattern('post', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('role', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');


/** ------------------------------------------
 *  Admin Routes
 *  ------------------------------------------
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{

	# User Management
	Route::get('users/{user}/show', 'AdminUsersController@getShow');
	Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
	Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
	Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
	Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
	Route::controller('users', 'AdminUsersController');

	# User Role Management
	Route::get('roles/{role}/show', 'AdminRolesController@getShow');
	Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
	Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
	Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
	Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
	Route::controller('roles', 'AdminRolesController');

    # Admin Education Department
    Route::get('edu_department', 'AdminEduDepartmentController@getIndex');
    Route::get('edu_department/data_add', 'AdminEduDepartmentController@getCreate');
    Route::post('edu_department/data_add', 'AdminEduDepartmentController@postCreate');
    Route::get('edu_department/data', 'AdminEduDepartmentController@getDataForAdd');
    Route::get('edu_department/{id}/edit', 'AdminEduDepartmentController@getEdit');
    Route::post('edu_department/{id}/edit', 'AdminEduDepartmentController@postEdit');

    # Admin Course
    Route::get('course', 'AdminCourseController@getIndex');
    Route::post('course', 'AdminCourseController@postIndex');
    Route::get('course/data_add', 'AdminCourseController@getCreate');
    Route::post('course/data_add', 'AdminCourseController@postCreate');
    Route::get('course/data', 'AdminCourseController@getDataForAdd');
    //Route::get('course/importExcel', 'AdminCourseController@getImportExcel');
    //Route::post('course/importExcel', 'AdminCourseController@postImportExcel');
    Route::get('course/{type}/importExcel', 'AdminCourseController@getImportExcel');
    Route::post('course/{type}/importExcel', 'AdminCourseController@postImportExcel');

    Route::get('course/establish', 'AdminCourseController@getEstablish');
    Route::get('course/establish_data', 'AdminCourseController@getEstablishData');
    Route::get('course/establish_browse', 'AdminCourseController@getBrowse');
    Route::get('course/establish_browse_data', 'AdminCourseController@getBrowseData');
    Route::get('course/establish_next', 'AdminCourseController@getNext');

    Route::get('course/establish_school', 'AdminCourseController@getEstablishSchool');
    Route::get('course/establish_school_data', 'AdminCourseController@getEstablishSchoolData');
    Route::get('course/establish_school_browse', 'AdminCourseController@getSchoolBrowse');
    Route::get('course/establish_school_browse_data', 'AdminCourseController@getSchoolBrowseData');
    Route::get('course/establish_school_next', 'AdminCourseController@getSchoolNext');

    Route::get('course/update_semester', 'AdminCourseController@getUpdateSemester');
    Route::get('course/update_year_semester', 'AdminCourseController@getUpdateYearSemester');

    Route::get('course/teaching_plan', 'AdminCourseController@getTeachingPlan');
    Route::get('course/teaching_plan_data', 'AdminCourseController@getTeachingPlanData');
    Route::get('course/teaching_plan_add', 'AdminCourseController@getTeachingPlanCreate');
    Route::post('course/teaching_plan_add', 'AdminCourseController@postTeachingPlanCreate');
    Route::get('course/{id}/module_query', 'AdminCourseController@getModules');
    Route::get('course/{id}/teaching_plan_query', 'AdminCourseController@getMajorTeachingPlan');
    Route::get('course/{id}/module_course', 'AdminCourseController@getModuleCourse');
    Route::get('course/module_data_add', 'AdminCourseController@getModuleDataForAdd');
    Route::get('course/{id}/module_course_add', 'AdminCourseController@getModuleCourseCreate');
    Route::post('course/{id}/module_course_add', 'AdminCourseController@postModuleCourseCreate');
    Route::get('course/query_course', 'AdminCourseController@getQueryCourse');

    Route::get('course/update_teaching_plan_semester', 'AdminCourseController@getTeachingPlanSemester');
    Route::get('course/update_teaching_plan_semester_rst', 'AdminCourseController@getTeachingPlanSemesterRst');

    Route::get('course/department_define', 'AdminCourseController@getDepartmentDefine');
    Route::get('course/department_data', 'AdminCourseController@getDepartmentData');
    Route::get('course/department_create', 'AdminCourseController@getDepartmentCreate');
    Route::post('course/department_create', 'AdminCourseController@postDepartmentCreate');
    Route::get('course/{id}/department_edit', 'AdminCourseController@getDepartmentEdit');
    Route::post('course/{id}/department_edit', 'AdminCourseController@postDepartmentEdit');
    Route::get('course/{id}/department_delete', 'AdminCourseController@getDepartmentDelete');
    Route::post('course/{id}/department_delete', 'AdminCourseController@postDepartmentDelete');

    Route::get('course/module_define', 'AdminCourseController@getModuleDefine');
    Route::get('course/module_data', 'AdminCourseController@getModuleData');
    Route::get('course/module_create', 'AdminCourseController@getModuleCreate');
    Route::post('course/module_create', 'AdminCourseController@postModuleCreate');
    Route::get('course/{id}/module_edit', 'AdminCourseController@getModuleEdit');
    Route::post('course/{id}/module_edit', 'AdminCourseController@postModuleEdit');
    Route::get('course/{id}/module_delete', 'AdminCourseController@getModuleDelete');
    Route::post('course/{id}/module_delete', 'AdminCourseController@postModuleDelete');

    Route::get('select/update_module_semester', 'AdminSelectController@getModuleSemester');
    Route::get('select/update_module_semester_rst', 'AdminSelectController@getModuleSemesterRst');
    Route::get('select/{ctype}/control', 'AdminSelectController@getCtrl');
    Route::get('select/{ctype}/control_data', 'AdminSelectController@getCtrlData');
    Route::get('select/count_number_course', 'AdminSelectController@getCountNumberCourse');
    Route::get('select/count_number_course_data', 'AdminSelectController@getCountNumberCourseData');
    Route::get('select/campus_group', 'AdminSelectController@getCampusGroup');
    Route::get('select/query_group_selection', 'AdminSelectController@getQueryGroupSelection');
    Route::get('select/query_group_selection_data', 'AdminSelectController@getQueryGroupSelectionData');
    Route::get('select/query_selection_record', 'AdminSelectController@getQuerySelectionRecord');
    Route::get('select/query_selection_record_data', 'AdminSelectController@getQuerySelectionRecordData');
    Route::get('select/count_selection', 'AdminSelectController@getCountSelection');
    Route::get('select/count_selection_data', 'AdminSelectController@getCountSelectionData');

    Route::get('select/change_campus_selection', 'AdminSelectController@getChangeCampusSelection');
    Route::get('select/change_campus_selection_rst', 'AdminSelectController@getChangeCampusSelectionRst');
   
    Route::get('select/group_selection', 'AdminSelectController@getGroupSelection');
    Route::get('select/group_selection_course_data', 'AdminSelectController@getGroupSelectionCourseData');
    Route::get('select/group_selection_class_data', 'AdminSelectController@getGroupSelectionClassData');
    Route::get('select/group_selection_student_data', 'AdminSelectController@getGroupSelectionStudentData');
    Route::get('select/group_submit_class', 'AdminSelectController@getGroupSubmitClass');
    Route::get('select/group_submit_student', 'AdminSelectController@getGroupSubmitStudent');

    Route::get('select/class_selection', 'AdminSelectController@getClassSelection');
    Route::get('select/class_selection_class_data', 'AdminSelectController@getClassSelectionClassData');
    Route::get('select/class_selection_course_data', 'AdminSelectController@getClassSelectionCourseData');
    Route::get('select/class_selection_student_data', 'AdminSelectController@getClassSelectionStudentData');
    Route::get('select/class_submit_class', 'AdminSelectController@getClassSubmitClass');
    Route::get('select/class_submit_student', 'AdminSelectController@getClassSubmitStudent');

    Route::get('select/batch_confirm_selection', 'AdminSelectController@getBatchConfirmSelection');
    Route::get('select/batch_confirm_selection_rst', 'AdminSelectController@getBatchConfirmSelectionRst');
    Route::get('select/confirm_selection', 'AdminSelectController@getConfirmSelection');
    Route::get('select/confirm_selection_data', 'AdminSelectController@getConfirmSelectionData');
    Route::get('select/delete_selection', 'AdminSelectController@getDeleteSelection');
    Route::get('select/delete_selection_data', 'AdminSelectController@getDeleteSelectionData');
    Route::get('select/delete_selection_submit', 'AdminSelectController@getDeleteSelectionSubmit');
    Route::get('select/range_delete_selection', 'AdminSelectController@getRangeDeleteSelection');
    Route::get('select/range_delete_selection_data', 'AdminSelectController@getRangeDeleteSelectionData');
    Route::get('select/number_query_selection', 'AdminSelectController@getNumberQuerySelection');
    Route::get('select/number_query_selection_data', 'AdminSelectController@getNumberQuerySelectionData');
    Route::get('select/summary_class_selection', 'AdminSelectController@getSummaryClassSelection');
    Route::get('select/summary_class_selection_data', 'AdminSelectController@getSummaryClassSelectionData');
    Route::get('select/summary_times_selection', 'AdminSelectController@getSummaryTimesSelection');
    Route::get('select/summary_times_selection_data', 'AdminSelectController@getSummaryTimesSelectionData');
    Route::get('select/campus_count_selection', 'AdminSelectController@getCampusCountSelection');
    Route::get('select/campus_count_selection_data', 'AdminSelectController@getCampusCountSelectionData');

    Route::get('admissions/comprehensive_student_info', 'AdminAdmissionController@getComprehensiveStudentInfo');
    Route::get('admissions/base_student_info', 'AdminAdmissionController@getBaseStudentInfo');

    Route::get('admissions/{id}/query_admission', 'AdminAdmissionController@getQueryAdmission');
    Route::get('admissions/score_record', 'AdminAdmissionController@getScoreRecord');
    Route::get('admissions/{id}/query_selection', 'AdminAdmissionController@getQuerySelection');
    Route::get('admissions/query_selection_data', 'AdminAdmissionController@getQuerySelectionData');
    Route::get('admissions/{id}/query_exam', 'AdminAdmissionController@getQueryExam');
    Route::get('admissions/query_exam_data', 'AdminAdmissionController@getQueryExamData');
    Route::get('admissions/{id}/query_exemption', 'AdminAdmissionController@getQueryExemption');
    Route::get('admissions/query_exemption_data', 'AdminAdmissionController@getQueryExemptionData');
    Route::get('admissions/{id}/query_unified_exam', 'AdminAdmissionController@getQueryUnifiedExam');
    Route::get('admissions/query_unified_exam_data', 'AdminAdmissionController@getQueryUnifiedExamData');
    Route::get('admissions/{id}/query_rewards_punishments', 'AdminAdmissionController@getQueryRewardsPunishments');
    Route::get('admissions/query_rewards_data', 'AdminAdmissionController@getQueryRewardsData');
    Route::get('admissions/query_punishments_data', 'AdminAdmissionController@getQueryPunishmentsData');
    Route::get('admissions/{id}/query_graduate', 'AdminAdmissionController@getQueryGraduate');
    Route::get('admissions/query_graduate_data', 'AdminAdmissionController@getQueryGraduateData');

    Route::get('admissions/student_info', 'AdminAdmissionController@getStudentInfo');
    Route::get('admissions/campus_class', 'AdminAdmissionController@getCampusClass');
    Route::get('admissions/school_campus', 'AdminAdmissionController@getSchoolCampus');
    Route::get('admissions/student_info_data', 'AdminAdmissionController@getStudentInfoData');

    Route::get('admissions/basic_student_info', 'AdminAdmissionController@getBasicStudentInfo');
    Route::get('admissions/basic_student_info_data', 'AdminAdmissionController@getBasicStudentInfoData');
    Route::get('admissions/reward_punish_info', 'AdminAdmissionController@getRewardPunishInfo');
    Route::get('admissions/reward_punish_info_data', 'AdminAdmissionController@getRewardPunishInfoData');
    Route::get('admissions/status_changing_info', 'AdminAdmissionController@getStatusChangingInfo');
    Route::get('admissions/status_changing_info_data', 'AdminAdmissionController@getStatusChangingInfoData');
    Route::get('admissions/change_need_select_courses', 'AdminAdmissionController@getChangeNeedSelectCourses');
    Route::get('admissions/change_need_select_courses_data', 'AdminAdmissionController@getChangeNeedSelectCoursesData');
    Route::get('admissions/teaching_plan_count', 'AdminAdmissionController@getTeachingPlanCount');
    Route::get('admissions/teaching_plan_count_data', 'AdminAdmissionController@getTeachingPlanCountData');
    Route::get('admissions/information_count', 'AdminAdmissionController@getInformationCount');
    Route::get('admissions/information_count_data', 'AdminAdmissionController@getInformationCountData');
    Route::get('admissions/information_classification_count', 'AdminAdmissionController@getInformationClassificationCount');
    Route::get('admissions/information_classification_count_data', 'AdminAdmissionController@getInformationClassificationCountData');
    Route::get('admissions/to_check_number', 'AdminAdmissionController@getToCheckNumber');
    Route::post('admissions/upload_photos', 'AdminAdmissionController@postUploadPhotos');
    Route::get('admissions/check_photos', 'AdminAdmissionController@getCheckPhotos');
    Route::get('admissions/result_details', 'AdminAdmissionController@getResultDetails');
    Route::post('admissions/result_details', 'AdminAdmissionController@postResultDetails');
    Route::get('admissions/download_photos', 'AdminAdmissionController@getDownloadPhotos');

    Route::get('admissions/campus_student_info', 'AdminAdmissionController@getCampusStudentInfo');
    Route::get('admissions/campus_student_info_data', 'AdminAdmissionController@getCampusStudentInfoData');
    Route::get('admissions/campus_basic_student_info', 'AdminAdmissionController@getCampusBasicStudentInfo');
    Route::get('admissions/campus_basic_student_info_data', 'AdminAdmissionController@getCampusBasicStudentInfoData');
    Route::get('admissions/campus_reward_punish_info', 'AdminAdmissionController@getCampusRewardPunishInfo');
    Route::get('admissions/campus_reward_punish_info_data', 'AdminAdmissionController@getCampusRewardPunishInfoData');
    Route::get('admissions/campus_status_changing_info', 'AdminAdmissionController@getCampusStatusChangingInfo');
    Route::get('admissions/campus_status_changing_info_data', 'AdminAdmissionController@getCampusStatusChangingInfoData');
    Route::get('admissions/campus_information_classification_count', 'AdminAdmissionController@getCampusInformationClassificationCount');
    Route::get('admissions/campus_information_classification_count_data', 'AdminAdmissionController@getCampusInformationClassificationCountData');

    Route::get('admissions/photo_link', 'AdminAdmissionController@getPhotoLink');

    # Admin Dashboard
	Route::controller('/', 'AdminDashboardController');


});

/** ------------------------------------------
 *  Frontend Routes
 *  ------------------------------------------
 */

//:: User Account Routes ::
Route::post('user/login', 'UserController@postLogin');

# User RESTful Routes (Login, Logout, Register, etc)
Route::post('user/{user}/edit', 'UserController@postEdit');
Route::get('forgot','UserController@getForgot');
Route::controller('user', 'UserController');

# Index Page - Last route, no matches
Route::get('/', array('before' => 'detectLang','uses' => 'UserController@getLogin'));
