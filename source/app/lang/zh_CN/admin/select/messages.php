<?php

return array(

	'already_exists' => '角色名已经被选用!',
	'does_not_exist' => 'Role does not exist.',
	'name_required'  => 'The name field is required',
    'import_success' => '导入Excel文件!',
	'create' => array(
		'error'   => 'Role was not created, please try again.',
		'course_success' => '课程已建立',
        'program_success' => '教学计划已建立',
        'module_success' => '模块课程已添加',
        'department_sysid_exist' => '部门名称或代码已存在',
        'department_success' => '部门已添加',
        'module_sysid_exist' => '模块名称或代码已存在',
        'module_success' => '模块已添加'
	),

	'update' => array(
		'error'   => 'Role was not updated, please try again',
		'success' => '角色已修改',
        'department_id_not_exist' => '部门ID不存在',
        'department_success' => '部门已修改',
        'module_id_not_exist' => '模块ID不存在',
        'module_success' => '模块已修改'
	),

	'delete' => array(
		'error'   => 'There was an issue deleting the role. Please try again.',
		'success' => 'The role was deleted successfully.'
	)

);
