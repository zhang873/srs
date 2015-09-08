<?php
return array (
		
		'already_exists' => '记录已经存在!',
		'does_not_exist' => '学生信息不存在.',

		
		'create' => array (
				'error' => 'record was not created, please try again.',
				'success' => '记录已建立',

		),
		
		'edit' => array (
				'impossible' => 'You cannot edit yourself.',
				'error' => 'There was an issue editing the user. Please try again.',
				'success' => '记录已修改'
		),
		
		'delete' => array (
				'impossible' => 'You cannot delete yourself.',
				'error' => 'There was an issue deleting the user. Please try again.',
				'success' => '记录已删除.'
		),

        'error' => array (
                'nopass' => '此记录审核状态无法修改成 不通过',
                'pass' => '此记录审核状态无法修改成 通过审核',
                'nocheck' => '此记录审核状态无法修改成 不审核',

        ),

        'school' => array (
                'already_exists' => '数据库中已经存在相同代号或者相同名称的分校',
                'create' => '分校添加成功',
                'edit' => '分校修改成功',
                'delete' => '分校删除成功',
        ),

        'campus' => array (
            'already_exists' => '数据库中已经存在相同代号或者相同名称的教学点',
            'create' => '教学点添加成功',
            'edit' => '教学点修改成功',
            'delete' => '教学点删除成功',
        ),

        'pleaseselect' => '请点选学生记录后的录入标记'


)
;
