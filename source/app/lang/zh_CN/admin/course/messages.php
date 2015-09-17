<?php

return array(

	'already_exists' => '角色名已经被选用!',
	'does_not_exist' => 'Role does not exist.',
	'name_required'  => 'The name field is required',
    'import_success' => '导入Excel文件!',
	'create' => array(
		'error'   => 'Role was not created, please try again.',
		'course_success' => '课程已建立',
        'teaching_plan_success' => '教学计划已建立',
        'module_course_success' => '模块课程已添加',
        'department_code_exist' => '部门名称或代码已存在',
        'department_success' => '部门已添加',
        'module_code_exist' => '模块名称或代码已存在',
        'course_code_not_exist' => '课程编号不存在',
        'teaching_plan_module_not_exist' => '教学计划下的模块不存在',
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
	),

    'excel_error' => array(
        '10' =>  '课程编号格式错误（只接受5位数字输入，且最高位从1开始）',
        '11' =>  '教学部门代号格式错误（只接受5位数字输入）',
        '12' =>  '课程名字只能包括文字、数字、括号、下划线',
        '13' =>  '课程简称只能包括文字、数字、括号、下划线',
        '14' =>  '学分格式错误（1位数字，1-9）',
        '15' =>  '实践环节标志取值错误（填有或无）',
        '16' =>  '责任教师格式错误（只接受文字）',
        '17' =>  '证书有无取值错误（填有或无）',
        '18' =>  '定义日期格式错误（正确格式yyyy-mm-dd，例如 2015-09-03）',
        '19' =>  '备注格式错误（只接受文字）',
        '20' =>  '课程状态取值错误（填启用或停用）',
        '21' =>  '专业层次取值错误（填本科或专科）',

        '22' =>  '教学计划编号格式错误（只接受7位数字输入，且最高位从1开始）',
        '23' =>  '学生类型取值错误（填本科或专科）',
        '24' =>  '专业层次取值错误（填本科或专科）',
        '25' =>  '毕业最低学分格式错误（只接受数字和“.”）',
        '26' =>  '学制格式错误（只接受数字和“.”）',
        '27' =>  '免修免考最高学分格式错误（只接受数字）',
        '28' =>  '新选课最高学分格式错误（只接受数字）',
        '29' =>  '启用标志取值错误（填启或关）',
        '30' =>  '模块代号格式错误（只接受5位数字输入）',
        '31' =>  '模块学分格式错误（只接受数字）',
        '32' =>  '模块最低学分格式错误（只接受数字）',
        '33' =>  '教学计划下的模块重复',
        '34' =>  '模块课程重复',
        '35' =>  '课程性质取值错误（填必修或选修）',
        '36' =>  '建议开设学期取值错误（只接受数字）',
        '37' =>  '是否屏蔽取值错误（填是或否）',
        '38' =>  '专业名称不存在',
    )

);
