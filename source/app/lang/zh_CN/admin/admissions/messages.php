<?php
return array (
		
		'already_exists' => '记录已经存在!',
		'does_not_exist' => '学生信息不存在.',
        'no_recovery_records' => '该生没有恢复学籍的申请',
        'no_dropout_records' => '未找到指定条件的学生信息',
        'no_student_info' => '未找到指定条件的学生信息',
        'changing_success' => '审批学籍异动成功',
        'no_check' => '修改为未审核状态成功',
        'no_change_authority' => '教学点没有学籍异动权限',
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

        'changing' => array (
            'success' => '申请学籍异动成功',
            'error_year' => '入学后的第二个学期开始才能申请学籍异动，您现在还不能进行学籍异动申请',
            'error_selection' => '本学期该学生已有选课记录，不允许申请学籍异动',
            'error_alreadyexist' => '该学生已有学籍异动申请未得到审批，不允许申请学籍异动'
        ),

        'recovery' => array (
            'success' => '申请恢复学籍成功',
            'error' =>'申请失败',
            'is_approve' => '省校已经审核其恢复学籍申请'
            ),
        'dropout' => array (
            'success' => '提交退学申请成功',
            'error' =>'申请失败',
            'already_exists' => '该生已经申请退学',
            'edit_success' => '修改成功',
            'delete_success' => '删除退学申请成功',
            'delete_error' => '删除失败',
        ),
        'expel' => array(
            'success' => '开除学生成功',
            'cancel' => '撤销成功',
        ),
        'reward' => array(
            'success' => '保存奖励级别成功',
            'delete_success' => '删除成功',
            'delete_error' => '该奖励级别已被使用，无法删除'
        ),
    'punish_code' => array(
        'success' => '保存惩罚代码成功',
        'delete_success' => '删除成功',
        'delete_error' => '该惩罚代码已被使用，无法删除'
    ),
    'punish_cause' => array(
        'success' => '保存惩罚原因成功',
        'delete_success' => '删除成功',
        'delete_error' => '该惩罚原因已被使用，无法删除'
    ),
        'pleaseselect' => '请点选学生记录后的录入标记',
        'approve_success' => '审批成功',

    'groups' => array(
        'programid_required' => '请选择专业名称',
            'programid_min' => '请选择专业名称',
            'groupnam_required' => '请输入班级名称',
            'already_exists' => '班级名称已经被选用!',
            'programs_disapprove' => '专业未审批',

            'create' => array (
            'error' => '班级建立失败, 请再尝试',
            'success' => '班级建立成功'
        ),

            'edit' => array (
            'impossible' => 'You cannot edit yourself.',
            'error' => 'There was an issue editing the Campus. Please try again.',
            'success' => '班级修改成功'
        ),
            'delete' => array (
            'impossible' => 'You cannot delete yourself.',
            'error' => 'There was an issue deleting the Group. Please try again.',
            'success' => 'The Group was deleted successfully.'
        ),
    )
)
;
