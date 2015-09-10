<?php
return array (
		
		'already_exists' => '学生的免修免考记录已经存在!<br>请重新选择课程',
		'does_not_exist' => '学生信息不存在.',

		'create' => array (
				'error' => 'record was not created, please try again.',
				'success' => '记录添加成功',
                'overcredit' => '该学生的免修免考学分已经达到教学计划中规定的"免修免考最高学分"，不可再录入',

		),
		
		'edit' => array (
				'impossible' => 'You cannot edit yourself.',
				'error' => 'There was an issue editing the user. Please try again.',
				'success' => '记录修改成功'
		),
		
		'delete' => array (
				'impossible' => 'You cannot delete yourself.',
				'error' => 'There was an issue deleting the user. Please try again.',
				'success' => '免修免考记录已删除.'
		),

        'error' => array (
                'nopass' => '此记录审核状态无法修改成 不通过',
                'pass' => '此记录审核状态无法修改成 通过审核',
                'nocheck' => '此记录审核状态无法修改成 不审核',

        ),

        'major_outer' => array (
                'already_exists' => '数据库中已经存在相同代号或者相同名称的专业',
                'create' => '专业添加成功',
                'edit' => '专业修改成功',
                'delete' => '专业删除成功',
                'error' => '已有学生选择此专业名称，无法删除！'
        ),

        'exemption_type' => array (
            'already_exists' => '数据库中已经存在相同代号或者相同名称的免修类型',
            'create' => '免修类型添加成功',
            'edit' => '免修类型修改成功',
            'delete' => '免修类型删除成功',
            'error' => '已有学生选择此免修类型，无法删除！'
        ),

        'exemption_agency' => array (
            'already_exists' => '数据库中已经存在相同代号或者相同名称的颁证单位',
            'create' => '颁证单位添加成功',
            'edit' => '颁证单位修改成功',
            'delete' => '颁证单位删除成功',
            'error' => '已有学生选择此颁证单位，无法删除！'
        ),

        'pleaseselect' => '请点选学生记录后的录入标记',
        'approve_suceess' => '审核成功'


)
;
