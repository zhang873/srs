<?php

class AdminAdmissionController extends AdminController
{
    public function getComprehensiveStudentInfo()
    {
        // Title
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');

        // Show the page
        return View::make('admin/admissions/comprehensive_student_info', compact('title'));
    }

    public function getBaseStudentInfo()
    {
        $student_no = Input::get('student_id');
        $id_number = Input::get('id_number');
        $reg_number = Input::get('reg_number');
        $query = DB::table('admissions')
            ->leftjoin('graduation_info', function ($join) {
                $join->on('graduation_info.student_id', '=', 'admissions.id')
                    ->where('graduation_info.is_deleted', '=', 0);
            })
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno','admissions.idnumber',
                'graduation_info.elec_regis_no', 'graduation_info.graduation_year','graduation_info.graduation_semester');
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }
        if (!empty($id_number)) {
            $query = $query->where('admissions.idnumber', 'like', '%' . $id_number . '%');
        }
        if (!empty($reg_number)) {
            $query = $query->where('graduation_info.elec_regis_no', 'like', '%' . $reg_number . '%');
        }
        $baseInfo = $query->first();
        // Show the page
        return View::make('admin/admissions/base_student_info', compact('baseInfo'));
    }

    public function getQueryAdmission($id)
    {
        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')

            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            })
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan.major', '=', 'rawprograms.name')
                    ->on('teaching_plan.student_classification', '=', 'admissions.program');
            })
            ->where('admissions.id', $id);

        $query = $query->select('admissions.id as aid', 'admissions.gender', 'admissions.nationgroup',
            'admissions.jiguan', 'admissions.dateofbirth', 'admission_details.formerlevel',
            'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.mobile', 'admissions.email',
            'rawprograms.name as rname', 'admissions.maritalstatus', 'admissions.program', 'admissions.postcode',
            'groups.sysid', 'groups.name as gname', 'teaching_plan.code', 'admissions.admissionyear',
            'admissions.admissionsemester', 'admissions.status', 'admissions.address');
        $info = $query->first();
        // Show the page
        return View::make('admin/admissions/detail_student_info', compact('info'));
    }

    public function getScoreRecord()
    {
        $admission_id = Input::get('admission_id');
        $teaching_plan_code = Input::get('plan_code');
        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('admissions.id', $admission_id)
            ->select('admissions.studentno', 'admissions.fullname', 'admissions.gender', 'rawprograms.name as rname');
        $admission_info = $query->first();

        $query = DB::table('teaching_plan')->where('teaching_plan.code', $teaching_plan_code)
            ->join('teaching_plan_module', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan.is_deleted', '=', 0)
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->leftjoin('major_module_info', function ($join) {
                $join->on('teaching_plan_module.module_id', '=', 'major_module_info.id')
                    ->where('major_module_info.is_deleted', '=', 0);
            })
            ->leftjoin('module_course', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->select('teaching_plan.min_credit_graduation', 'teaching_plan.max_credit_semester', 'major_module_info.name as mname',
                'teaching_plan_module.min_credit', 'teaching_plan_module.credit as tcredit', 'course.code', 'course.name as cname',
                'is_obligatory', 'course.credit as ccredit', 'suggested_semester')
            ->groupBy('major_module_info.name', 'course.code');
        $teaching_plan_info = $query->get();
        return View::make('admin/admissions/score_record_info', compact('admission_info', 'teaching_plan_info', 'teaching_plan_code'));
    }

    public function getQuerySelection($id)
    {
        return View::make('admin/admissions/query_selection', compact('id'));
    }

    public function getQuerySelectionData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('student_selection')
            ->where('student_selection.student_id', $student_id)
            ->where('student_selection.is_deleted', '=', 0)
            ->leftjoin('course', function ($join) {
                $join->on('student_selection.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'course.code', 'course.name', 'course.credit', 'student_selection.is_obligatory',
                'student_selection.year', 'student_selection.semester', 'student_selection.selection_status');
        return Datatables::of($programs)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('selection_status', '@if($selection_status == \'1\')已确认@elseif($selection_status == \'0\')未确认@endif')
            ->make();
    }

    public function getQueryExam($id)
    {
        return View::make('admin/admissions/query_exam', compact('id'));
    }

    public function getQueryExamData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('student_selection')
            ->where('student_selection.student_id', $student_id)
            ->where('student_selection.is_deleted', '=', 0)
            ->leftjoin('course', function ($join) {
                $join->on('student_selection.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'course.code', 'course.name', 'course.credit', 'student_selection.is_obligatory',
                'student_selection.year', 'student_selection.semester', 'student_selection.is_confirmed');
        return Datatables::of($programs)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('is_confirmed', '@if($is_confirmed == \'1\')已确认@elseif($is_confirmed == \'0\')未确认@endif')
            ->make();
    }

    public function getQueryExemption($id)
    {
        return View::make('admin/admissions/query_exemption', compact('id'));
    }

    public function getQueryExemptionData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('exemption_info')
            ->where('exemption_info.student_id', $student_id)
            ->where('exemption_info.is_deleted', '=', 0)
            ->leftjoin('course', function ($join) {
                $join->on('exemption_info.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('exemption_type', function ($join) {
                $join->on('exemption_info.exemption_type_id', '=', 'exemption_type.id')
                    ->where('exemption_type.is_deleted', '=', 0);
            })
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'course.name', 'course.classification',
                'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer',
                'exemption_info.classification_outer', 'exemption_info.credit_outer', 'exemption_info.final_result');
        return Datatables::of($programs)
            ->add_column('exemption_application_time', function ($row) {
                $rst = $row->application_year;
                if ($row->application_semester == 1)
                    $rst = $rst . '春季';
                else if ($row->application_semester == 1)
                    $rst = $rst . '秋季';
                return $rst;
            }, 4)
            ->remove_column('application_year')
            ->remove_column('application_semester')
            ->edit_column('classification', '@if($classification == \'12\')本科@elseif($classification == \'14\')专科@endif')
            ->edit_column('classification_outer', '@if($classification_outer == \'12\')本科@elseif($classification_outer == \'14\')专科@endif')
            ->edit_column('final_result', '@if($final_result == \'0\')不通过@elseif($final_result == \'1\')通过@elseif($final_result == \'2\')未审核@endif')
            ->make();
    }

    public function getQueryUnifiedExam($id)
    {
        return View::make('admin/admissions/query_unified_exam', compact('id'));
    }

    public function getQueryUnifiedExamData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('exemption_info')
            ->where('exemption_info.student_id', $student_id)
            ->where('exemption_info.is_deleted', '=', 0)
            ->leftjoin('course', function ($join) {
                $join->on('exemption_info.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('exemption_type', function ($join) {
                $join->on('exemption_info.exemption_type_id', '=', 'exemption_type.id')
                    ->where('exemption_type.is_deleted', '=', 0);
            })
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'course.name', 'course.classification',
                'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer',
                'exemption_info.classification_outer', 'exemption_info.credit_outer', 'exemption_info.final_result');
        return Datatables::of($programs)
            ->add_column('exemption_application_time', function ($row) {
                $rst = $row->application_year;
                if ($row->application_semester == 1)
                    $rst = $rst . '春季';
                else if ($row->application_semester == 1)
                    $rst = $rst . '秋季';
                return $rst;
            }, 4)
            ->remove_column('application_year')
            ->remove_column('application_semester')
            ->edit_column('classification', '@if($classification == \'12\')本科@elseif($classification == \'14\')专科@endif')
            ->edit_column('classification_outer', '@if($classification_outer == \'12\')本科@elseif($classification_outer == \'14\')专科@endif')
            ->edit_column('final_result', '@if($final_result == \'0\')不通过@elseif($final_result == \'1\')通过@elseif($final_result == \'2\')未审核@endif')
            ->make();
    }

    public function getQueryRewardsPunishments($id)
    {
        return View::make('admin/admissions/query_rewards_punishments', compact('id'));
    }

    public function getQueryRewardsData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('student_reward_punishment')
            ->where('student_reward_punishment.student_id', $student_id)
            ->where('student_reward_punishment.is_deleted', '=', 0)
            ->where('student_reward_punishment.reward_level', '!=', '')
            ->leftjoin('reward_info', function ($join) {
                $join->on('student_reward_punishment.reward_level', '=', 'reward_info.code')
                    ->where('reward_info.is_deleted', '=', 0);
            })

            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'reward_info.reward_level',
                'student_reward_punishment.date', 'student_reward_punishment.operator',
                'student_reward_punishment.year', 'student_reward_punishment.semester',
                'student_reward_punishment.approval_result');
        return Datatables::of($programs)
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('approval_result', function ($row) {
                $rst = '';
                switch ($row->approval_result) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getQueryPunishmentsData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('student_reward_punishment')
            ->where('student_reward_punishment.student_id', $student_id)
            ->where('student_reward_punishment.is_deleted', '=', 0)
            ->where('student_reward_punishment.punishment', '!=', '')
            ->leftjoin('punishment_info', function ($join) {
                $join->on('student_reward_punishment.punishment', '=', 'punishment_info.code')
                    ->where('punishment_info.is_deleted', '=', 0);
            })
            ->leftjoin('punishment_cause_info', function ($join) {
                $join->on('student_reward_punishment.punishment_cause', '=', 'punishment_cause_info.code')
                    ->where('punishment_cause_info.is_deleted', '=', 0);
            })
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'punishment_info.punishment',
                'punishment_cause_info.punishment_cause', 'student_reward_punishment.date',
                'student_reward_punishment.operator', 'student_reward_punishment.year',
                'student_reward_punishment.semester', 'student_reward_punishment.approval_result');
        return Datatables::of($programs)
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('approval_result', function ($row) {
                $rst = '';
                switch ($row->approval_result) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getQueryGraduate($id)
    {
        return View::make('admin/admissions/query_graduate', compact('id'));
    }

    public function getQueryGraduateData()
    {
        $student_id = Input::get('student_id');
        DB::select('set @rownum=0');
        $programs = DB::table('graduation_info')
            ->where('graduation_info.student_id', $student_id)
            ->select(DB::raw('@rownum:=@rownum+1 as rownum'), 'graduation_info.application_year',
                'graduation_info.application_semester', 'graduation_info.is_applied', 'graduation_info.approval_status',
                'graduation_info.elec_regis_no', 'graduation_info.is_reported');
        return Datatables::of($programs)
            ->add_column('degree', '', 4)
            ->edit_column('application_semester', '@if($application_semester == \'1\')春季@elseif($application_semester == \'2\')秋季@endif')
            ->edit_column('is_applied', '@if($is_applied == \'0\')未申请@elseif($is_applied == \'1\')已申请@endif')
            ->edit_column('approval_status', function ($row) {
                $rst = '';
                switch ($row->approval_status) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->edit_column('is_reported', function ($row) {
                $rst = '';
                switch ($row->is_reported) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getStudentInfo()
    {
        // Title
        $title = Lang::get('admin/admissions/title.admissions_information_query');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        return View::make('admin/admissions/student_info', compact('title', 'schools', 'campuses', 'b_majors', 'z_majors'));
    }

    public function getCampusClass()
    {
        $campus_id = Input::get('campus_id');
        if ($campus_id == '全部') {
            $rsts = Group::select('groups.id as gid', 'groups.name as gname')->get();
        } else {
            $rsts = Group::join('programs', 'groups.programs_id', '=', 'programs.id')
                ->where('programs.campus_id', $campus_id)
                ->select('groups.id as gid', 'groups.name as gname')->get();
        }
        return $rsts->toJson();
    }

    public function getSchoolCampus()
    {
        $school_id = Input::get('school_id');
        if ($school_id == '全部') {
            $rsts = Campus::select('id', 'name')->get();
        } else {
            $rsts = Campus::select('id', 'name')
                ->where('school_id', $school_id)->get();
        }
        return $rsts->toJson();
    }

    public function getStudentInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $admission_state = Input::get('admission_state');
        $group_code = Input::get('group_code');
        $year_in = Input::get('admissionyear');
        $semester_in = Input::get('admissionsemester');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $school_id = Input::get('school');
        $campus_id = Input::get('campus');
        $create_group_year = Input::get('create_admin_group_year');
        $group_id = Input::get('group');


        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('school_info', 'campuses.school_id', '=', 'school_info.school_id')
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan.major', '=', 'rawprograms.name')
                    ->on('teaching_plan.student_classification', '=', 'admissions.program')
                    ->where('teaching_plan.is_deleted', '=', 0);
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }

        if (!is_null($admission_state) && $admission_state != "全部") {
            $query = $query->where('admissions.status', $admission_state);
        }
        if (!empty($group_code)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_code . '%');
        }
        if (!is_null($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('campuses.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($create_group_year) && $create_group_year != "全部") {
            $query = $query->where('groups.year', $create_group_year);
        }
        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno',
            'teaching_plan.code', 'groups.sysid as gsysid', 'groups.name as gname', 'rawprograms.name as rname',
            'admissions.program', 'admissions.status', 'school_info.school_name as sname', 'campuses.name as cpname',
            'admissions.admissionyear', 'admissions.admissionsemester');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
            ->edit_column('program', function ($row) {
                $rst = '';
                if ($row->program == 12)
                    $rst = '本科';
                else if ($row->program == 14)
                    $rst = '专科';
                return $rst;
            })
            ->edit_column('status', function ($row) {
                $rst = '';
                if ($row->status == 0)
                    $rst = '已录入数据';
                else if ($row->status == 1)
                    $rst = '已上报省校';
                else if ($row->status == 2)
                    $rst = '省校已审批';
                else if ($row->status == 3)
                    $rst = '未注册';
                else if ($row->status == 4)
                    $rst = '在籍';
                else if ($row->status == 5)
                    $rst = '异动中';
                else if ($row->status == 6)
                    $rst = '毕业';
                else if ($row->status == 7)
                    $rst = '退学';
                return $rst;
            })
            ->make();
    }

    public function getBasicStudentInfo()
    {
        // Title
        $title = Lang::get('admin/admissions/title.basic_student_info');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        return View::make('admin/admissions/basic_student_info', compact('title', 'schools', 'campuses', 'b_majors', 'z_majors'));
    }

    public function getBasicStudentInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $group_code = Input::get('group_code');
        $year_in = Input::get('admissionyear');
        $semester_in = Input::get('admissionsemester');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $school_id = Input::get('school');
        $campus_id = Input::get('campus');
        $group_id = Input::get('group');


        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')

            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            });


        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }

        if (!empty($group_code)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_code . '%');
        }
        if (!is_null($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('campuses.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }

        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno','admissions.gender', 'admissions.nationgroup',
            'admissions.politicalstatus', 'admissions.jiguan', 'admissions.dateofbirth', 'admission_details.formerlevel',
            'admission_details.formerschool', 'admission_details.dategraduated', 'admission_details.attainmentcert',
            'admissions.maritalstatus', 'admissions.idnumber','admissions.mobile', 'admissions.address',
            'admissions.postcode','admissions.email');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('gender', '@if($gender == \'f\')女@elseif($gender == \'m\')男@endif')
            ->edit_column('nationgroup', function ($row) {
                $rst = '';
                switch ($row->nationgroup){
                    case '01' :
                        $rst =  '汉族';
                        break;
                    case '02' :
                        $rst =  '蒙古族';
                        break;
                    case '03' :
                        $rst =  '回族';
                        break;
                    case '04' :
                        $rst =  '藏族';
                        break;
                    case '05' :
                        $rst =  '维吾尔族';
                        break;
                    case '06' :
                        $rst =  '苗族';
                        break;
                    case '07' :
                        $rst =  '彝族';
                        break;
                    case '08' :
                        $rst =  '壮族';
                        break;
                    case '09' :
                        $rst =  '布依族';
                        break;
                    case '10' :
                        $rst =  '朝鲜族';
                        break;
                    case '11' :
                        $rst =  '满族';
                        break;
                    case '12' :
                        $rst =  '侗族';
                        break;
                    case '13' :
                        $rst =  '瑶族';
                        break;
                    case '14' :
                        $rst =  '白族';
                        break;
                    case '15' :
                        $rst =  '土家族';
                        break;
                    case '16' :
                        $rst =  '哈尼族';
                        break;
                    case '17' :
                        $rst =  '哈萨克族';
                        break;
                    case '18' :
                        $rst =  '傣族';
                        break;
                    case '19' :
                        $rst =  '黎族';
                        break;
                    case '20' :
                        $rst =  '傈僳族';
                        break;
                    case '21' :
                        $rst =  '佤族';
                        break;
                    case '22' :
                        $rst =  '畲族';
                        break;
                    case '23' :
                        $rst =  '高山族';
                        break;
                    case '24' :
                        $rst =  '拉祜族';
                        break;
                    case '25' :
                        $rst =  '水族';
                        break;
                    case '26' :
                        $rst =  '东乡族';
                        break;
                    case '27' :
                        $rst =  '纳西族';
                        break;
                    case '28' :
                        $rst =  '景颇族';
                        break;
                    case '29' :
                        $rst =  '柯尔克孜族';
                        break;
                    case '30' :
                        $rst =  '土族';
                        break;
                    case '31' :
                        $rst =  '达斡尔族';
                        break;
                    case '32' :
                        $rst =  '仫佬族';
                        break;
                    case '33' :
                        $rst =  '羌族';
                        break;
                    case '34' :
                        $rst =  '布朗族';
                        break;
                    case '35' :
                        $rst =  '撒拉族';
                        break;
                    case '36' :
                        $rst =  '毛南族';
                        break;
                    case '37' :
                        $rst =  '仡佬族';
                        break;
                    case '38' :
                        $rst =  '锡伯族';
                        break;
                    case '39' :
                        $rst =  '阿昌族';
                        break;
                    case '40' :
                        $rst =  '普米族';
                        break;
                    case '41' :
                        $rst =  '塔吉克族';
                        break;
                    case '42' :
                        $rst =  '怒族';
                        break;
                    case '43' :
                        $rst =  '乌孜别克族';
                        break;
                    case '44' :
                        $rst =  '俄罗斯族';
                        break;
                    case '45' :
                        $rst =  '鄂温克族';
                        break;
                    case '46' :
                        $rst =  '崩龙族';
                        break;
                    case '47' :
                        $rst =  '保安族';
                        break;
                    case '48' :
                        $rst =  '裕固族';
                        break;
                    case '49' :
                        $rst =  '京族';
                        break;
                    case '50' :
                        $rst =  '塔塔尔族';
                        break;
                    case '51' :
                        $rst =  '独龙族';
                        break;
                    case '52' :
                        $rst =  '鄂伦春族';
                        break;
                    case '53' :
                        $rst =  '赫哲族';
                        break;
                    case '54' :
                        $rst =  '门巴族';
                        break;
                    case '55' :
                        $rst =  '珞巴族';
                        break;
                    case '56' :
                        $rst =  '基诺族';
                        break;
                    case '97' :
                        $rst =  '其它';
                        break;
                    case '98' :
                        $rst =  '外国血统中国籍人士';
                        break;
                }
                return $rst;
            })
            ->edit_column('politicalstatus', function ($row) {
                $rst = '';
                switch ($row->politicalstatus) {
                    case 1:
                        $rst =  '中共党员';
                        break;
                    case 2:
                        $rst =  '共青团员';
                        break;
                    case 3:
                        $rst =  '民革会员';
                        break;
                    case 4:
                        $rst =  '民盟盟员';
                        break;
                    case 5:
                        $rst =  '民进会员';
                        break;
                    case 6:
                        $rst =  '民建会员';
                        break;
                    case 7:
                        $rst =  '农工党党员';
                        break;
                    case 8:
                        $rst =  '致公党党员';
                        break;
                    case 9:
                        $rst =  '九三学社社员';
                        break;
                    case 10:
                        $rst =  '台盟盟员';
                        break;
                    case 11:
                        $rst =  '无党派民主人士';
                        break;
                    case 12:
                        $rst =  '群众';
                        break;
                    case 13:
                        $rst =  '其他';
                        break;
                }
                return $rst;
            })
            ->edit_column('formerlevel', function ($row) {
                $rst = '';
                switch ($row->formerlevel) {
                    case 1 :
                        $rst = '高中毕业';
                        break;
                    case 2 :
                        $rst = '职高毕业';
                        break;
                    case 3 :
                        $rst = '中专毕业';
                        break;
                    case 4 :
                        $rst = '技校毕业';
                        break;
                    case 5 :
                        $rst = '专科毕业';
                        break;
                    case 6 :
                        $rst = '本科毕业';
                        break;
                    case 7 :
                        $rst = '硕士研究生毕业';
                        break;
                    case 8 :
                        $rst = '博士研究生毕业';
                        break;
                    case 9 :
                        $rst = '其他毕业';
                        break;
                }
                return $rst;
            })
            ->edit_column('maritalstatus', function ($row) {
                $rst = '';
                switch ($row->maritalstatus) {
                    case 0 :
                        $rst = '未婚';
                        break;
                    case 1 :
                        $rst = '已婚';
                        break;
                    case 2 :
                        $rst = '其他';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getRewardPunishInfo()
    {
        $title = Lang::get('admin/admissions/title.admissions_reward_punish');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        $rewards = RewardInfo::select('reward_level', 'code')->where('is_deleted', 0)->get();
        $punishments = PunishmentInfo::select('punishment', 'code')->where('is_deleted', 0)->get();
        return View::make('admin/admissions/reward_punish_info',
            compact('title', 'schools', 'campuses', 'b_majors', 'z_majors', 'rewards', 'punishments'));

    }

    public function getRewardPunishInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $school_id = Input::get('school');
        $campus_id = Input::get('campus');
        $reward_punish_type = Input::get('reward_punish_type');
        $reward_punish_level = Input::get('reward_punish_level');
        $year_in = Input::get('admissionyear');
        $semester_in = Input::get('admissionsemester');


        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('student_reward_punishment', function ($join) {
                $join->on('student_reward_punishment.student_id', '=', 'admissions.id')
                    ->where('student_reward_punishment.is_deleted', '=', 0);
            })
            ->leftjoin('reward_info', function ($join) {
                $join->on('student_reward_punishment.reward_level', '=', 'reward_info.code')
                    ->where('reward_info.is_deleted', '=', 0);
            })
            ->leftjoin('punishment_info', function ($join) {
                $join->on('student_reward_punishment.punishment', '=', 'punishment_info.code')
                    ->where('punishment_info.is_deleted', '=', 0);
            })
            ->leftjoin('punishment_cause_info', function ($join) {
                $join->on('student_reward_punishment.punishment_cause', '=', 'punishment_cause_info.code')
                    ->where('punishment_cause_info.is_deleted', '=', 0);
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }
        if (!is_null($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('campuses.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($reward_punish_level) && $reward_punish_level != "全部") {
            $query = $query->where(function($qry) use ($reward_punish_level)
            {
                $qry->where('student_reward_punishment.reward_level', $reward_punish_level)
                    ->orWhere('student_reward_punishment.punishment', $reward_punish_level);
            });
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno','reward_info.reward_level as rreward_level',
            'punishment_info.punishment as ppunishment', 'punishment_cause_info.punishment_cause as ppunishment_cause',
            'student_reward_punishment.date', 'student_reward_punishment.operator', 'student_reward_punishment.document_id',
            'student_reward_punishment.remark');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->make();
    }

    public function getStatusChangingInfo()
    {
        $title = Lang::get('admin/admissions/title.admissions_status_changing');
        $campuses = Campus::select('id', 'name')->get();
        return View::make('admin/admissions/status_changing_info', compact('title', 'campuses'));
    }

    public function getStatusChangingInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $out_of_campus = Input::get('out_of_campus');
        $into_campus = Input::get('into_campus');
        $application_year = Input::get('application_year');
        $application_semester = Input::get('application_semester');
        $province_final_result = Input::get('province_final_result');


        $query = DB::table('admissions')
            ->join('student_status_changing', function ($join) {
                $join->on('student_status_changing.student_id', '=', 'admissions.id')
                    ->where('student_status_changing.is_deleted', '=', 0);
            })
            ->leftjoin('rawprograms as major_out', function ($join) {
                $join->on('student_status_changing.original_major_id', '=', 'major_out.id');
            })
            ->leftjoin('rawprograms as major_in', function ($join) {
                $join->on('student_status_changing.current_major_id', '=', 'major_in.id');
            })
            ->leftjoin('campuses as campus_out', function ($join) {
                $join->on('student_status_changing.original_campus_id', '=', 'campus_out.id');
            })
            ->leftjoin('campuses as campus_in', function ($join) {
                $join->on('student_status_changing.current_campus_id', '=', 'campus_in.id');
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }
        if (!is_null($out_of_campus) && $out_of_campus != "全部") {
            $query = $query->where('student_status_changing.original_campus_id', $out_of_campus);
        }
        if (!is_null($into_campus) && $into_campus != "全部") {
            $query = $query->where('student_status_changing.current_campus_id', $into_campus);
        }
        if (!is_null($application_year) && $application_year != "全部") {
            $query = $query->where('student_status_changing.application_year', $application_year);
        }
        if (!is_null($application_semester) && $application_semester != "全部") {
            $query = $query->where('student_status_changing.application_semester', $application_semester);
        }
        if (!is_null($province_final_result) && $province_final_result != "全部") {
            $query = $query->where('student_status_changing.approval_status', $province_final_result);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno', 'student_status_changing.application_year',
            'student_status_changing.application_semester', 'major_out.name as major_out',  'major_in.name as major_in',
            'campus_out.name as campus_out', 'campus_in.name as campus_in', 'student_status_changing.approval_status');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('application_semester', '@if($application_semester == \'1\')春季@elseif($application_semester == \'2\')秋季@endif')
            ->edit_column('approval_status', function ($row) {
                $rst = '';
                switch ($row->approval_status) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getChangeNeedSelectCourses()
    {
        $title = Lang::get('admin/admissions/title.admissions_change_need_select_courses');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        return View::make('admin/admissions/change_need_select_courses', compact('title', 'schools', 'campuses', 'b_majors', 'z_majors'));
    }

    public function getChangeNeedSelectCoursesData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $out_of_campus = Input::get('out_of_campus');
        $into_campus = Input::get('into_campus');
        $application_year = Input::get('application_year');
        $application_semester = Input::get('application_semester');
        $province_final_result = Input::get('province_final_result');



    }

    public function getTeachingPlanCount()
    {
        // Title
        $title = Lang::get('admin/admissions/title.admissions_teaching_plan_count');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();

        return View::make('admin/admissions/teaching_plan_count', compact('title', 'schools', 'campuses'));
    }

    public function getTeachingPlanCountData()
    {
        $group_code = Input::get('group_code');
        $school_id = Input::get('school');
        $campus_id = Input::get('campus');
        $create_group_year = Input::get('create_admin_group_year');
        $group_id = Input::get('group');


        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan.major', '=', 'rawprograms.name')
                    ->on('teaching_plan.student_classification', '=', 'admissions.program')
                    ->where('teaching_plan.is_deleted', '=', 0);
            });

        if (!empty($group_code)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_code . '%');
        }

        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('campuses.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($create_group_year) && $create_group_year != "全部") {
            $query = $query->where('groups.year', $create_group_year);
        }
        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }

        $query = $query->select('campuses.sysid as csysid', 'campuses.name as cpname',
            'groups.sysid as gsysid', 'groups.name as gname', 'teaching_plan.code',
            'rawprograms.sysid as rsysid', 'rawprograms.name as rname',
            DB::raw('count(*) as number'))
            ->groupBy('campuses.name', 'groups.name');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->make();
    }

    public function getInformationCount()
    {
        $title = Lang::get('admin/admissions/title.admissions_information_count');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        return View::make('admin/admissions/information_count', compact('title', 'schools', 'campuses', 'b_majors', 'z_majors'));
    }

    public function getInformationCountData()
    {
        $start_year = Input::get('start_year');
        $start_semester = Input::get('start_semester');
        $end_year = Input::get('end_year');
        $end_semester = Input::get('end_semester');
        $school_id = Input::get('school_id');
        $campus_id = Input::get('campus_id');
        $admission_state = Input::get('admission_state');
        $gender = Input::get('gender');
        $former_level = Input::get('former_level');
        $nationgroup = Input::get('nationgroup');
        $occupation_status = Input::get('occupation_status');
        $student_distribution = Input::get('student_distribution');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $politicalstatus = Input::get('politicalstatus');
        $start_age = Input::get('start_age');
        $end_age = Input::get('end_age');
        $student_type = Input::get('student_type');

        $cur_year = date("Y");
        $start_birth = '';
        $end_birth = '';
        $start = '';
        $end = '';

        if (!empty($start_age)) {
            $end_birth = $cur_year - $start_age;
            $end_birth = $end_birth . '-12-31';
        }
        if (!empty($end_age)) {
            $start_birth = $cur_year - $end_age;
            $start_birth = $start_birth . '-01-01';
        }
        if ($start_year == '全部')
            $start = '0000';
        else
            $start = $start_year;
        if ($start_semester == '全部')
            $start = $start . '00';
        else
            $start = $start . $start_semester;

        if ($end_year == '全部')
            $end = '9999';
        else
            $end = $end_year;

        if ($end_semester == '全部')
            $end = $end . '02';
        else
            $end = $end . $end_semester;

        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            });

        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('campuses.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($admission_state) && $admission_state != "全部") {
            $query = $query->where('admissions.status', $admission_state);
        }
        if (!is_null($gender) && $gender != "全部") {
            $query = $query->where('admissions.gender', $gender);
        }
        if (!is_null($former_level) && $former_level != "全部") {
            $query = $query->where('admission_details.formerlevel', $former_level);
        }
        if (!is_null($nationgroup) && $nationgroup != "全部") {
            $query = $query->where('admissions.nationgroup', $nationgroup);
        }
        if (!is_null($occupation_status) && $occupation_status != "全部") {
            $query = $query->where('admissions.is_serving', $occupation_status);
        }
        if (!is_null($student_distribution) && $student_distribution != "全部") {
            $query = $query->where('admissions.distribution', $student_distribution);
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!empty($politicalstatus) && $politicalstatus != "全部") {
            $query = $query->where('admissions.politicalstatus', $politicalstatus);
        }
        if (!empty($student_type) && $student_type != "全部") {
            $query = $query->where('admissions.enrollmenttype', $student_type);
        }
        if (!empty($start_birth)) {
            $query = $query->where('admissions.dateofbirth', '>=', $start_birth);
        }
        if (!empty($end_birth)) {
            $query = $query->where('admissions.dateofbirth', '<=', $end_birth);
        }
        if (!empty($start)) {
            $query = $query->where(DB::raw('concat(admissions.admissionyear, admissions.admissionsemester)'), '>=', $start);
        }
        if (!empty($end)) {
            $query = $query->where(DB::raw('concat(admissions.admissionyear, admissions.admissionsemester)'), '<=', $end);
        }

        $count = $query->select('admissions.id')->count();
        return $count;
    }

    public function getInformationClassificationCount()
    {
        $title = Lang::get('admin/admissions/title.admissions_information_classification_count');

        return View::make('admin/admissions/information_classification_count', compact('title'));
    }

    public function getInformationClassificationCountData()
    {
        $checks = Input::get('checkitem');
        $groups = array();

        if (!empty($checks)) {
            foreach ($checks as $check) {
                switch ($check) {
                    case 1:
                        $groups[] = 'admissions.admissionyear';
                        break;
                    case 2:
                        $groups[] = 'admissions.admissionsemester';
                        break;
                    case 3:
                        $groups[] = 'admissions.enrollmenttype';
                        break;
                    case 4:
                        $groups[] = 'rawprograms.name';
                        break;
                    case 5:
                        $groups[] = 'rawprograms.type';
                        break;
                    case 6:
                        $groups[] = 'admissions.status';
                        break;
                    case 7:
                        $groups[] = 'school_info.school_name';
                        break;
                    case 8:
                        $groups[] = 'campuses.name';
                        break;
                }
            }
        }

        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('school_info', 'campuses.school_id', '=', 'school_info.school_id');


        $query = $query->select('admissions.admissionyear', 'admissions.admissionsemester', 'admissions.enrollmenttype',
            'rawprograms.name as rname', 'rawprograms.type', 'admissions.status', 'school_info.school_name',
            'campuses.name as cname', DB::raw('count(*) as number'))->groupBy($groups);
        $result = Datatables::of($query)
            ->edit_column('admissionyear', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.admissionyear', $groups))
                    $rst = '全部';
                else
                    $rst = $row->admissionyear;
                return $rst;
            })
            ->edit_column('admissionsemester', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.admissionsemester', $groups))
                    $rst = '全部';
                else {
                    if ($row->admissionsemester == '01')
                        $rst = '春季';
                    else if ($row->admissionsemester == '02')
                        $rst = '秋季';
                }
                return $rst;
            })
            ->edit_column('enrollmenttype', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.enrollmenttype', $groups))
                    $rst = '全部';
                else {
                    if ($row->enrollmenttype == 11)
                        $rst = '学历';
                    else if ($row->enrollmenttype == 12)
                        $rst = '课程';
                }
                return $rst;
            })
            ->edit_column('rname', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('rawprograms.name', $groups))
                    $rst = '全部';
                else
                    $rst = $row->rname;
                return $rst;
            })
            ->edit_column('type', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('rawprograms.type', $groups))
                    $rst = '全部';
                else {
                    if ($row->type == 12)
                        $rst = '本科';
                    else if ($row->type == 14)
                        $rst = '专科';
                }
                return $rst;
            })
            ->edit_column('status', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.status', $groups))
                    $rst = '全部';
                else {
                    if ($row->status == 0)
                        $rst = '已录入数据';
                    else if ($row->status == 1)
                        $rst = '已上报省校';
                    else if ($row->status == 2)
                        $rst = '省校已审批';
                    else if ($row->status == 3)
                        $rst = '未注册';
                    else if ($row->status == 4)
                        $rst = '在籍';
                    else if ($row->status == 5)
                        $rst = '异动中';
                    else if ($row->status == 6)
                        $rst = '毕业';
                    else if ($row->status == 7)
                        $rst = '退学';
                }
                return $rst;
            })
            ->edit_column('school_name', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('school_info.school_name', $groups))
                    $rst = '全部';
                else
                    $rst = $row->school_name;
                return $rst;
            })
            ->edit_column('cname', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('campuses.name', $groups))
                    $rst = '全部';
                else
                    $rst = $row->cname;
                return $rst;
            });

        return $result
            ->make();
    }

    public function getCampusStudentInfo()
    {
        $campus_id = Session::get('campus_id');
        $title = Lang::get('admin/admissions/title.admissions_information_query');
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        $groups = Group::join('programs', 'groups.programs_id', '=', 'programs.id')
            ->where('programs.campus_id', $campus_id)
            ->select('groups.id as gid', 'groups.name as gname')->get();

        return View::make('admin/admissions/campus_student_info', compact('title', 'b_majors', 'z_majors', 'groups'));
    }
    public function getCampusStudentInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $admission_state = Input::get('admission_state');
        $group_code = Input::get('group_code');
        $year_in = Input::get('admissionyear');
        $semester_in = Input::get('admissionsemester');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus_id = Session::get('campus_id');
        $create_group_year = Input::get('create_admin_group_year');
        $group_id = Input::get('group');


        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan.major', '=', 'rawprograms.name')
                    ->on('teaching_plan.student_classification', '=', 'admissions.program')
                    ->where('teaching_plan.is_deleted', '=', 0);
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }

        if (!is_null($admission_state) && $admission_state != "全部") {
            $query = $query->where('admissions.status', $admission_state);
        }
        if (!empty($group_code)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_code . '%');
        }
        if (!is_null($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($create_group_year) && $create_group_year != "全部") {
            $query = $query->where('groups.year', $create_group_year);
        }
        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno','teaching_plan.code',
            'groups.sysid as gsysid', 'groups.name as gname', 'rawprograms.name as rname', 'admissions.program',
            'admissions.status', 'admissions.admissionyear', 'admissions.admissionsemester');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
            ->edit_column('program', function ($row) {
                $rst = '';
                if ($row->program == 12)
                    $rst = '本科';
                else if ($row->program == 14)
                    $rst = '专科';
                return $rst;
            })
            ->edit_column('status', function ($row) {
                $rst = '';
                if ($row->status == 0)
                    $rst = '已录入数据';
                else if ($row->status == 1)
                    $rst = '已上报省校';
                else if ($row->status == 2)
                    $rst = '省校已审批';
                else if ($row->status == 3)
                    $rst = '未注册';
                else if ($row->status == 4)
                    $rst = '在籍';
                else if ($row->status == 5)
                    $rst = '异动中';
                else if ($row->status == 6)
                    $rst = '毕业';
                else if ($row->status == 7)
                    $rst = '退学';
                return $rst;
            })
            ->make();
    }

    public function getCampusBasicStudentInfo()
    {
        $campus_id = Session::get('campus_id');
        $title = Lang::get('admin/admissions/title.basic_student_info');
        $groups = Group::join('programs', 'groups.programs_id', '=', 'programs.id')
            ->where('programs.campus_id', $campus_id)
            ->select('groups.id as gid', 'groups.name as gname')->get();
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        return View::make('admin/admissions/campus_basic_student_info', compact('title', 'groups', 'b_majors', 'z_majors'));
    }
    public function getCampusBasicStudentInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $group_code = Input::get('group_code');
        $year_in = Input::get('admissionyear');
        $semester_in = Input::get('admissionsemester');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus_id = Session::get('campus_id');
        $group_id = Input::get('group');


        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            });


        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }

        if (!empty($group_code)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_code . '%');
        }
        if (!is_null($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno','admissions.gender', 'admissions.nationgroup',
            'admissions.politicalstatus', 'admissions.jiguan', 'admissions.dateofbirth', 'admission_details.formerlevel',
            'admission_details.formerschool', 'admission_details.dategraduated', 'admission_details.attainmentcert',
            'admissions.maritalstatus', 'admissions.idnumber','admissions.mobile', 'admissions.address',
            'admissions.postcode','admissions.email');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('gender', '@if($gender == \'f\')女@elseif($gender == \'m\')男@endif')
            ->edit_column('nationgroup', function ($row) {
                $rst = '';
                switch ($row->nationgroup){
                    case '01' :
                        $rst =  '汉族';
                        break;
                    case '02' :
                        $rst =  '蒙古族';
                        break;
                    case '03' :
                        $rst =  '回族';
                        break;
                    case '04' :
                        $rst =  '藏族';
                        break;
                    case '05' :
                        $rst =  '维吾尔族';
                        break;
                    case '06' :
                        $rst =  '苗族';
                        break;
                    case '07' :
                        $rst =  '彝族';
                        break;
                    case '08' :
                        $rst =  '壮族';
                        break;
                    case '09' :
                        $rst =  '布依族';
                        break;
                    case '10' :
                        $rst =  '朝鲜族';
                        break;
                    case '11' :
                        $rst =  '满族';
                        break;
                    case '12' :
                        $rst =  '侗族';
                        break;
                    case '13' :
                        $rst =  '瑶族';
                        break;
                    case '14' :
                        $rst =  '白族';
                        break;
                    case '15' :
                        $rst =  '土家族';
                        break;
                    case '16' :
                        $rst =  '哈尼族';
                        break;
                    case '17' :
                        $rst =  '哈萨克族';
                        break;
                    case '18' :
                        $rst =  '傣族';
                        break;
                    case '19' :
                        $rst =  '黎族';
                        break;
                    case '20' :
                        $rst =  '傈僳族';
                        break;
                    case '21' :
                        $rst =  '佤族';
                        break;
                    case '22' :
                        $rst =  '畲族';
                        break;
                    case '23' :
                        $rst =  '高山族';
                        break;
                    case '24' :
                        $rst =  '拉祜族';
                        break;
                    case '25' :
                        $rst =  '水族';
                        break;
                    case '26' :
                        $rst =  '东乡族';
                        break;
                    case '27' :
                        $rst =  '纳西族';
                        break;
                    case '28' :
                        $rst =  '景颇族';
                        break;
                    case '29' :
                        $rst =  '柯尔克孜族';
                        break;
                    case '30' :
                        $rst =  '土族';
                        break;
                    case '31' :
                        $rst =  '达斡尔族';
                        break;
                    case '32' :
                        $rst =  '仫佬族';
                        break;
                    case '33' :
                        $rst =  '羌族';
                        break;
                    case '34' :
                        $rst =  '布朗族';
                        break;
                    case '35' :
                        $rst =  '撒拉族';
                        break;
                    case '36' :
                        $rst =  '毛南族';
                        break;
                    case '37' :
                        $rst =  '仡佬族';
                        break;
                    case '38' :
                        $rst =  '锡伯族';
                        break;
                    case '39' :
                        $rst =  '阿昌族';
                        break;
                    case '40' :
                        $rst =  '普米族';
                        break;
                    case '41' :
                        $rst =  '塔吉克族';
                        break;
                    case '42' :
                        $rst =  '怒族';
                        break;
                    case '43' :
                        $rst =  '乌孜别克族';
                        break;
                    case '44' :
                        $rst =  '俄罗斯族';
                        break;
                    case '45' :
                        $rst =  '鄂温克族';
                        break;
                    case '46' :
                        $rst =  '崩龙族';
                        break;
                    case '47' :
                        $rst =  '保安族';
                        break;
                    case '48' :
                        $rst =  '裕固族';
                        break;
                    case '49' :
                        $rst =  '京族';
                        break;
                    case '50' :
                        $rst =  '塔塔尔族';
                        break;
                    case '51' :
                        $rst =  '独龙族';
                        break;
                    case '52' :
                        $rst =  '鄂伦春族';
                        break;
                    case '53' :
                        $rst =  '赫哲族';
                        break;
                    case '54' :
                        $rst =  '门巴族';
                        break;
                    case '55' :
                        $rst =  '珞巴族';
                        break;
                    case '56' :
                        $rst =  '基诺族';
                        break;
                    case '97' :
                        $rst =  '其它';
                        break;
                    case '98' :
                        $rst =  '外国血统中国籍人士';
                        break;
                }
                return $rst;
            })
            ->edit_column('politicalstatus', function ($row) {
                $rst = '';
                switch ($row->politicalstatus) {
                    case 1:
                        $rst =  '中共党员';
                        break;
                    case 2:
                        $rst =  '共青团员';
                        break;
                    case 3:
                        $rst =  '民革会员';
                        break;
                    case 4:
                        $rst =  '民盟盟员';
                        break;
                    case 5:
                        $rst =  '民进会员';
                        break;
                    case 6:
                        $rst =  '民建会员';
                        break;
                    case 7:
                        $rst =  '农工党党员';
                        break;
                    case 8:
                        $rst =  '致公党党员';
                        break;
                    case 9:
                        $rst =  '九三学社社员';
                        break;
                    case 10:
                        $rst =  '台盟盟员';
                        break;
                    case 11:
                        $rst =  '无党派民主人士';
                        break;
                    case 12:
                        $rst =  '群众';
                        break;
                    case 13:
                        $rst =  '其他';
                        break;
                }
                return $rst;
            })
            ->edit_column('formerlevel', function ($row) {
                $rst = '';
                switch ($row->formerlevel) {
                    case 1 :
                        $rst = '高中毕业';
                        break;
                    case 2 :
                        $rst = '职高毕业';
                        break;
                    case 3 :
                        $rst = '中专毕业';
                        break;
                    case 4 :
                        $rst = '技校毕业';
                        break;
                    case 5 :
                        $rst = '专科毕业';
                        break;
                    case 6 :
                        $rst = '本科毕业';
                        break;
                    case 7 :
                        $rst = '硕士研究生毕业';
                        break;
                    case 8 :
                        $rst = '博士研究生毕业';
                        break;
                    case 9 :
                        $rst = '其他毕业';
                        break;
                }
                return $rst;
            })
            ->edit_column('maritalstatus', function ($row) {
                $rst = '';
                switch ($row->maritalstatus) {
                    case 0 :
                        $rst = '未婚';
                        break;
                    case 1 :
                        $rst = '已婚';
                        break;
                    case 2 :
                        $rst = '其他';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getCampusRewardPunishInfo()
    {
        $title = Lang::get('admin/admissions/title.admissions_reward_punish');
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        $rewards = RewardInfo::select('reward_level', 'code')->where('is_deleted', 0)->get();
        $punishments = PunishmentInfo::select('punishment', 'code')->where('is_deleted', 0)->get();
        return View::make('admin/admissions/campus_reward_punish_info',
            compact('title', 'b_majors', 'z_majors', 'rewards', 'punishments'));

    }
    public function getCampusRewardPunishInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus_id = Session::get('campus_id');
        $reward_punish_type = Input::get('reward_punish_type');
        $reward_punish_level = Input::get('reward_punish_level');

        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('student_reward_punishment', function ($join) {
                $join->on('student_reward_punishment.student_id', '=', 'admissions.id')
                    ->where('student_reward_punishment.is_deleted', '=', 0);
            })
            ->leftjoin('reward_info', function ($join) {
                $join->on('student_reward_punishment.reward_level', '=', 'reward_info.code')
                    ->where('reward_info.is_deleted', '=', 0);
            })
            ->leftjoin('punishment_info', function ($join) {
                $join->on('student_reward_punishment.punishment', '=', 'punishment_info.code')
                    ->where('punishment_info.is_deleted', '=', 0);
            })
            ->leftjoin('punishment_cause_info', function ($join) {
                $join->on('student_reward_punishment.punishment_cause', '=', 'punishment_cause_info.code')
                    ->where('punishment_cause_info.is_deleted', '=', 0);
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }

        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }

        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($reward_punish_level) && $reward_punish_level != "全部") {
            $query = $query->where(function($qry) use ($reward_punish_level)
            {
                $qry->where('student_reward_punishment.reward_level', $reward_punish_level)
                    ->orWhere('student_reward_punishment.punishment', $reward_punish_level);
            });
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno','reward_info.reward_level as rreward_level',
            'punishment_info.punishment as ppunishment', 'punishment_cause_info.punishment_cause as ppunishment_cause',
            'student_reward_punishment.date', 'student_reward_punishment.operator', 'student_reward_punishment.document_id',
            'student_reward_punishment.remark');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->make();
    }

    public function getCampusStatusChangingInfo()
    {
        $title = Lang::get('admin/admissions/title.admissions_status_changing');
        return View::make('admin/admissions/campus_status_changing_info', compact('title'));
    }
    public function getCampusStatusChangingInfoData()
    {
        $student_name = Input::get('student_name');
        $student_no = Input::get('student_id');
        $application_year = Input::get('application_year');
        $application_semester = Input::get('application_semester');
        $province_final_result = Input::get('province_final_result');
        $in_out_campus = Input::get('in_out_campus');
        $campus_id = Session::get('campus_id');

        $query = DB::table('admissions')
            ->join('student_status_changing', function ($join) {
                $join->on('student_status_changing.student_id', '=', 'admissions.id')
                    ->where('student_status_changing.is_deleted', '=', 0);
            })
            ->leftjoin('rawprograms as major_out', function ($join) {
                $join->on('student_status_changing.original_major_id', '=', 'major_out.id');
            })
            ->leftjoin('rawprograms as major_in', function ($join) {
                $join->on('student_status_changing.current_major_id', '=', 'major_in.id');
            })
            ->leftjoin('campuses as campus_out', function ($join) {
                $join->on('student_status_changing.original_campus_id', '=', 'campus_out.id');
            })
            ->leftjoin('campuses as campus_in', function ($join) {
                $join->on('student_status_changing.current_campus_id', '=', 'campus_in.id');
            });

        if (!empty($student_name)) {
            $query = $query->where('admissions.fullname', 'like', '%' . $student_name . '%');
        }
        if (!empty($student_no)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_no . '%');
        }
        if ($in_out_campus == 1){
            $query = $query->where('student_status_changing.original_campus_id', $campus_id);
        }
        else if ($in_out_campus == 0){
            $query = $query->where('student_status_changing.current_campus_id', $campus_id);
        }

        if (!is_null($application_year) && $application_year != "全部") {
            $query = $query->where('student_status_changing.application_year', $application_year);
        }
        if (!is_null($application_semester) && $application_semester != "全部") {
            $query = $query->where('student_status_changing.application_semester', $application_semester);
        }
        if (!is_null($province_final_result) && $province_final_result != "全部") {
            $query = $query->where('student_status_changing.approval_status', $province_final_result);
        }

        $query = $query->select('admissions.fullname', 'admissions.studentno', 'student_status_changing.application_year',
            'student_status_changing.application_semester', 'major_out.name as major_out',  'major_in.name as major_in',
            'campus_out.name as campus_out', 'campus_in.name as campus_in', 'student_status_changing.approval_status');

        return Datatables::of($query)
            ->add_column('order', '', 0)
            ->edit_column('application_semester', '@if($application_semester == \'1\')春季@elseif($application_semester == \'2\')秋季@endif')
            ->edit_column('approval_status', function ($row) {
                $rst = '';
                switch ($row->approval_status) {
                    case 0 :
                        $rst = '未审核';
                        break;
                    case 1 :
                        $rst = '同意';
                        break;
                    case 2 :
                        $rst = '不同意';
                        break;
                }
                return $rst;
            })
            ->make();
    }

    public function getCampusInformationClassificationCount()
    {
        $title = Lang::get('admin/admissions/title.admissions_information_classification_count');

        return View::make('admin/admissions/campus_information_classification_count', compact('title'));
    }

    public function getCampusInformationClassificationCountData()
    {
        $checks = Input::get('checkitem');
        $campus_id = Session::get('campus_id');
        $groups = array();
        if (!empty($checks)) {
            foreach ($checks as $check) {
                switch ($check) {
                    case 1:
                        $groups[] = 'admissions.admissionyear';
                        break;
                    case 2:
                        $groups[] = 'admissions.admissionsemester';
                        break;
                    case 3:
                        $groups[] = 'admissions.enrollmenttype';
                        break;
                    case 4:
                        $groups[] = 'rawprograms.name';
                        break;
                    case 5:
                        $groups[] = 'rawprograms.type';
                        break;
                    case 6:
                        $groups[] = 'admissions.status';
                        break;

                }
            }
        }

        $query = DB::table('admissions')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->where('campuses.id', $campus_id);


        $query = $query->select('admissions.admissionyear', 'admissions.admissionsemester', 'admissions.enrollmenttype',
            'rawprograms.name as rname', 'rawprograms.type', 'admissions.status', DB::raw('count(*) as number'))
            ->groupBy($groups);
        $result = Datatables::of($query)
            ->edit_column('admissionyear', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.admissionyear', $groups))
                    $rst = '全部';
                else
                    $rst = $row->admissionyear;
                return $rst;
            })
            ->edit_column('admissionsemester', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.admissionsemester', $groups))
                    $rst = '全部';
                else {
                    if ($row->admissionsemester == '01')
                        $rst = '春季';
                    else if ($row->admissionsemester == '02')
                        $rst = '秋季';
                }
                return $rst;
            })
            ->edit_column('enrollmenttype', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.enrollmenttype', $groups))
                    $rst = '全部';
                else {
                    if ($row->enrollmenttype == 11)
                        $rst = '学历';
                    else if ($row->enrollmenttype == 12)
                        $rst = '课程';
                }
                return $rst;
            })
            ->edit_column('rname', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('rawprograms.name', $groups))
                    $rst = '全部';
                else
                    $rst = $row->rname;
                return $rst;
            })
            ->edit_column('type', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('rawprograms.type', $groups))
                    $rst = '全部';
                else {
                    if ($row->type == 12)
                        $rst = '本科';
                    else if ($row->type == 14)
                        $rst = '专科';
                }
                return $rst;
            })
            ->edit_column('status', function ($row)  use ($groups) {
                $rst = '';
                if (!in_array('admissions.status', $groups))
                    $rst = '全部';
                else {
                    if ($row->status == 0)
                        $rst = '已录入数据';
                    else if ($row->status == 1)
                        $rst = '已上报省校';
                    else if ($row->status == 2)
                        $rst = '省校已审批';
                    else if ($row->status == 3)
                        $rst = '未注册';
                    else if ($row->status == 4)
                        $rst = '在籍';
                    else if ($row->status == 5)
                        $rst = '异动中';
                    else if ($row->status == 6)
                        $rst = '毕业';
                    else if ($row->status == 7)
                        $rst = '退学';
                }
                return $rst;
            });

        return $result
            ->make();
    }

    public function getPhotoLink()
    {
        $title = Lang::get('admin/admissions/title.photo_link');
        $schools = School::select('school_id', 'school_name')->where('is_deleted', 0)->get();
        $campuses = Campus::select('id', 'name')->get();
        return View::make('admin/admissions/photo_link', compact('title', 'schools', 'campuses'));
    }

    public function getToCheckNumber()
    {
        $graduated_year = Input::get('graduated_year');
        $graduated_semester = Input::get('graduated_semester');
        $student_type = Input::get('student_type');
        $school_id = Input::get('school_id');
        $campus_id = Input::get('campus_id');
        $major_classification = Input::get('major_classification');
        $degree_pass = Input::get('degree_pass');

        $query = DB::table('admissions')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('school_info', 'campuses.school_id', '=', 'school_info.school_id')
            ->join('graduation_info', function ($join) {
                $join->on('graduation_info.student_id', '=', 'admissions.id')
                    ->where('graduation_info.is_deleted', '=', 0);
            })
            ->select('admissions.id');
        if (!is_null($graduated_year) && $graduated_year != "全部") {
            $query = $query->where('graduation_info.graduation_year', $graduated_year);
        }
        if (!is_null($graduated_semester) && $graduated_semester != "全部") {
            $query = $query->where('graduation_info.graduation_semester', $graduated_semester);
        }
        if (!is_null($student_type) && $student_type != "全部") {
            $query = $query->where('admissions.enrollmenttype', $student_type);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('school_info.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('admissions.program', $major_classification);
        }
        if (!is_null($degree_pass) && $degree_pass != "全部") {
            $query = $query->where('graduation_info.approval_status', $degree_pass);
        }
        $count = $query->count();
        return $count;
    }

    public function postUploadPhotos()
    {
        $photos = Input::file('photos');
        $uploadDir = './photos';
        $fileTypes = array('jpg', 'png', 'gif', 'bmp');
        foreach ($photos as $photo) {
            $fileName = $photo->getClientOriginalName();
            $fileType = substr($fileName, strrpos($fileName, ".") + 1);
            if (in_array($fileType, $fileTypes)) {
                $photo->move($uploadDir, $fileName);
            }
        }
        echo "<script>window.parent.CallbackFunction();</script>";
    }

    public function getCheckPhotos()
    {
        $graduated_year = Input::get('graduated_year');
        $graduated_semester = Input::get('graduated_semester');
        $student_type = Input::get('student_type');
        $school_id = Input::get('school_id');
        $campus_id = Input::get('campus_id');
        $major_classification = Input::get('major_classification');
        $degree_pass = Input::get('degree_pass');
        $type = Input::get('type');

        $query = DB::table('admissions')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('school_info', 'campuses.school_id', '=', 'school_info.school_id')
            ->join('graduation_info', function ($join) {
                $join->on('graduation_info.student_id', '=', 'admissions.id')
                    ->where('graduation_info.is_deleted', '=', 0);
            });


        if (!is_null($graduated_year) && $graduated_year != "全部") {
            $query = $query->where('graduation_info.graduation_year', $graduated_year);
        }
        if (!is_null($graduated_semester) && $graduated_semester != "全部") {
            $query = $query->where('graduation_info.graduation_semester', $graduated_semester);
        }
        if (!is_null($student_type) && $student_type != "全部") {
            $query = $query->where('admissions.enrollmenttype', $student_type);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('school_info.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('admissions.program', $major_classification);
        }
        if (!is_null($degree_pass) && $degree_pass != "全部") {
            $query = $query->where('graduation_info.approval_status', $degree_pass);
        }
        $ary_rst = array();
        $ary_rst[0] = 0;
        $ary_rst[1] = 0;
        $dir = './photos';
        $filesnames = scandir($dir);
        if ($type == 1){
            $rsts = $query->select('admissions.idnumber')->lists('idnumber');
        }
        else if ($type == 2){
            $rsts= $query->select('admissions.studentno')->lists('studentno');
        }
        foreach($rsts as $rst){
            $found = false;
            foreach($filesnames as $name){
                $pos = strpos($name, $rst);
                if ($pos !== false){
                    $found = true;
                    break;
                }
            }
            if ($found == true){
                $ary_rst[0]++;
            }
            else{
                $ary_rst[1]++;
            }
        }
        $result = implode(",", $ary_rst);
        return $result;

    }

    public function getResultDetails()
    {
        $graduated_year = Input::get('graduated_year');
        $graduated_semester = Input::get('graduated_semester');
        $student_type = Input::get('student_type');
        $school_id = Input::get('school_id');
        $campus_id = Input::get('campus_id');
        $major_classification = Input::get('major_classification');
        $degree_pass = Input::get('degree_pass');
        $type = Input::get('type');

        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('school_info', 'campuses.school_id', '=', 'school_info.school_id')
            ->join('graduation_info', function ($join) {
                $join->on('graduation_info.student_id', '=', 'admissions.id')
                    ->where('graduation_info.is_deleted', '=', 0);
            });


        if (!is_null($graduated_year) && $graduated_year != "全部") {
            $query = $query->where('graduation_info.graduation_year', $graduated_year);
        }
        if (!is_null($graduated_semester) && $graduated_semester != "全部") {
            $query = $query->where('graduation_info.graduation_semester', $graduated_semester);
        }
        if (!is_null($student_type) && $student_type != "全部") {
            $query = $query->where('admissions.enrollmenttype', $student_type);
        }
        if (!is_null($school_id) && $school_id != "全部") {
            $query = $query->where('school_info.school_id', $school_id);
        }
        if (!is_null($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('admissions.program', $major_classification);
        }
        if (!is_null($degree_pass) && $degree_pass != "全部") {
            $query = $query->where('graduation_info.approval_status', $degree_pass);
        }
        $results = $query->select('admissions.id as aid', 'admissions.studentno', 'admissions.fullname', 'admissions.gender',
            'admissions.idnumber', 'school_info.school_name', 'campuses.name as cname', 'admissions.program',
            'rawprograms.name as rname', 'groups.sysid', 'groups.name as gname')->get();
        $dir = './photos';
        $fileNames = scandir($dir);
        return View::make('admin/admissions/result_details', compact('results', 'type', 'fileNames'));
    }

    public function postResultDetails()
    {
        $admissionIDs = explode(',', Input::get('selected_students'));
        $type = Input::get('link_type');
        $query = AdmissionGroup::whereIn('admissions.id', $admissionIDs)
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('programs', 'admissions.programcode', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->join('school_info', 'campuses.school_id', '=', 'school_info.school_id')
            ->join('graduation_info', function ($join) {
                $join->on('graduation_info.student_id', '=', 'admissions.id')
                    ->where('graduation_info.is_deleted', '=', 0);
            });
        $result = $query->select('admissions.studentno', 'admissions.fullname', 'admissions.gender',
            'admissions.idnumber', 'admissions.dateofbirth', 'admissions.enrollmenttype', 'admissions.program',
            'admissions.admissionyear', 'admissions.admissionsemester', 'rawprograms.sysid as rsysid',
            'rawprograms.name as rname', 'campuses.sysid as csysid', 'campuses.name as cname',
            'groups.sysid as gsysid', 'groups.name as gname')->get()->toArray();

        $dir = './photos';
        $photo_names = scandir($dir);
        $add_photo_names = array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['order'] = $i + 1;
            if ($result[$i]['gender'] == 'm')
                $result[$i]['gender'] = '男';
            else if ($result[$i]['gender'] == 'f')
                $result[$i]['gender'] = '女';
            if ($result[$i]['enrollmenttype'] == 11)
                $result[$i]['enrollmenttype'] = '学历';
            else if ($result[$i]['enrollmenttype'] == 12)
                $result[$i]['enrollmenttype'] = '课程';
            if ($result[$i]['program'] == 12)
                $result[$i]['program'] = '本科';
            else if ($result[$i]['program'] == 14)
                $result[$i]['program'] = '专科';
            if ($result[$i]['admissionsemester'] == '01')
                $result[$i]['admissionsemester'] = '春季';
            else if ($result[$i]['admissionsemester'] == '02')
                $result[$i]['admissionsemester'] = '秋季';
            if ($type == 1)
                $rst = $result[$i]['idnumber'];
            else
                $rst = $result[$i]['studentno'];

            foreach ($photo_names as $name) {
                $pos = strpos($name, $rst);
                if ($pos !== false) {
                    $add_photo_names[] = $name;
                    break;
                }
            }
        }

        $filename = 'export_' . strftime('%Y%m%d');
        $excel = Excel::create($filename, function ($excel) use ($result) {
            $excel->sheet('New sheet', function ($sheet) use ($result) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array('A' => '0'));
                $sheet->setColumnFormat(array('C' => '0'));
                $sheet->setColumnFormat(array('E' => '0'));
                $sheet->setColumnFormat(array('I' => '0'));
                $sheet->loadView('admin/admissions/photo_export', array('result' => $result));
            });
        });

        $excel_file = $excel->store('xls', false, true);
        $file_path = 'photos.zip';


        $zip = new ZipArchive ();
        if (file_exists($file_path) === true) {
            $res = $zip->open($file_path, ZIPARCHIVE::OVERWRITE);
        } else {
            $res = $zip->open($file_path, ZIPARCHIVE::CREATE);
        }
        if ($res !== TRUE) {
            return;
        }
        $zip_excel_name = 'File/' . basename($excel_file['full']);

        $zip->addFile($excel_file['full'], $zip_excel_name);
        foreach ($add_photo_names as $add_photo_name) {
            $zipFile = './photos/' . basename($add_photo_name);
            $photo_name = 'Photo/' . basename($add_photo_name);
            $zip->addFile($zipFile, $photo_name);
        }
        $zip->close();
        header("Content-Type: application/zip");
        header("Content-Length: " . filesize($file_path));
        header("Content-Disposition: attachment; filename=" . $file_path);
        readfile($file_path);
    }

    public function getApproveChangeAdmissionsIndexForProvince()
    {
        $title = Lang::get('admin/admissions/title.check_change_admissions');
        $campuses = Campus::All();
        $schools = School::All();
        $rawprograms = RawProgram::All();
        return View::make('admin/admissions/admissions_approve_change_province', compact('rawprograms', 'schools','campuses', 'title'));
    }
    public function getDataApproveChangingAdmissionsForProvince()
    {
        $filter = array();

        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $state = Input::get('state');
        $flag = 0;
        $ids = explode(',', Input::get('selectedAdmissions'));


        if ($state == 1) {
            if (!empty($ids)) {
                DB::table('student_status_changing')->whereIn('id', $ids)
                    ->update(array('approval_status' => 1));
                DB::table('admissions')->join('student_status_changing', 'student_status_changing.student_id', '=', 'admissions.id')
                    ->whereIn('student_status_changing.id', $ids)
                    ->update(array('admissions.status' => 4));
            }
        }

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }


        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }

        if (!empty($school)) {
            $school_ids = DB::table('school_info')->select('id')
                ->where('id', $school)
                ->lists('id');
            $campus_ids = DB::table('campuses_school')->select('campuses_school.campus_id as id')
                ->join('school_info', 'school_info.id', '=', 'campuses_school.school_id')
                ->whereIn('campuses_school.school_id', $school_ids)
                ->lists('id');
            $flag = 1;

        }

        if ($flag == 1) {
            $programs = DB::table('student_status_changing')
                ->leftjoin('admissions', function ($join) {
                    $join->on('student_status_changing.student_id', '=', 'admissions.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('admissions.programcode', '=', 'rawprograms.id');
                })
                ->whereIn('admissions.campuscode', $campus_ids)
                //          ->where('student_status_changing.current_class_id', ">", 0)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->select('student_status_changing.id', 'student_status_changing.application_year', 'student_status_changing.application_semester',
                    'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admissions.status',
                    'student_status_changing.original_major_id', 'student_status_changing.current_major_id',
                    'student_status_changing.original_campus_id', 'student_status_changing.current_campus_id',
                    'student_status_changing.current_class_id', 'student_status_changing.cause',
                    'student_status_changing.approval_status', 'student_status_changing.remark'
                );
        } else {
            $programs = DB::table('student_status_changing')
                ->leftjoin('admissions', function ($join) {
                    $join->on('student_status_changing.student_id', '=', 'admissions.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('admissions.programcode', '=', 'rawprograms.id');
                })
                //          ->where('student_status_changing.current_class_id', ">", 0)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->select('student_status_changing.id', 'student_status_changing.application_year', 'student_status_changing.application_semester',
                    'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admissions.status',
                    'student_status_changing.original_major_id', 'student_status_changing.current_major_id',
                    'student_status_changing.original_campus_id', 'student_status_changing.current_campus_id',
                    'student_status_changing.current_class_id', 'student_status_changing.cause',
                    'student_status_changing.approval_status', 'student_status_changing.remark'
                );
        }
        return Datatables::of($programs)
            ->edit_column('application_semester', '@if ($application_semester == \'02\')
                                      秋季
                                 @elseif ($application_semester == \'01\')
                                      春季
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                      已录入
                                 @elseif ($status == 1)
                                      已上报
                                 @elseif ($status == 2)
                                      已审批
                                 @elseif ($status == 3)
                                      未注册
                                 @elseif ($status == 4)
                                      在籍
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @elseif ($status == 7)
                                      退学
                                 @endif')
            ->edit_column('approval_status', '@if ($approval_status == 0)
                                      未审核
                                 @elseif ($approval_status == 1)
                                      同意
                                 @elseif ($approval_status == 2)
                                      不同意
                                 @endif
')
            ->edit_column('original_major_id', '
            @foreach ($programs=RawProgram::where(\'id\',$original_major_id)->get() as $program)
                {{$program->name}}
            @endforeach
            ')
            ->edit_column('current_major_id', '
            @foreach ($programs=RawProgram::where(\'id\',$current_major_id)->get() as $program)
               {{$program->name}}
            @endforeach
            ')
            ->edit_column('original_campus_id', '
            @foreach ($campuses=Campus::where(\'id\',$original_campus_id)->get() as $campus)
                {{$campus->name}}
            @endforeach
            ')
            ->edit_column('current_campus_id', '
            @foreach ($campuses=Campus::where(\'id\',$current_campus_id)->get() as $campus)
                {{$campus->name}}
            @endforeach
            ')
            ->edit_column('current_class_id', '
            @foreach ($groups=Groups::where(\'id\',$current_class_id)->get() as $group)
                {{$group->name}}
            @endforeach
            ')
            ->edit_column('remark', '<input type="text" id="remark" value="{{$remark}}" style="width: 100%;">')
            ->add_column('actions', '<a href="javascript:void(0)" id="noPass" value="{{ $id }}">不同意</a><br>
                                       <a href="javascript:void(0)" id="noCheck" value="{{ $id }}">未审核</a>'
            )
            ->add_column('isCheck', '
                          <div align="center"><input type = "checkbox" name = "checkItem[]" id= "checkItem" value="{{ $id }}"></div> '
            )
            ->remove_column('id')
            ->make();
    }
    public function getNoPassChangeForProvince()
    {
        $id = Input::get('id');
        $remark = Input::get('remark');

        DB::table('admissions')->join('student_status_changing', 'student_status_changing.student_id', '=', 'admissions.id')
            ->where('student_status_changing.id', $id)
            ->update(array('admissions.status' => 4,
                'student_status_changing.approval_status' => 2,
                'student_status_changing.remark' => $remark));
        return 'ok';
    }
    public function getNoCheckChangeForProvince()
    {
        $id = Input::get('id');
        DB::table('admissions')->join('student_status_changing', 'student_status_changing.student_id', '=', 'admissions.id')
            ->where('student_status_changing.id', $id)
            ->update(array('admissions.status' => 5,
                'student_status_changing.approval_status' => 0));
        return 'ok';
    }

    public function getIndexForApproveRewardPunishProvince()
    {
        $title = Lang::get('admin/admissions/title.approve_reward_punish');
        $rawprograms = RawProgram::All();
        $campuses = Campus::All();
        $schools = School::All();
        return View::make('admin/admissions/admissions_approve_reward_punish_province', compact('campuses','schools', 'rawprograms', 'title'));
    }
    public function getDataForApproveRewardPunishProvince()
    {
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $state = Input::get('state');
        $flag = 0;

        $ids = explode(',',Input::get('selectedAdmissions'));
        if ($state == 1) {
            if (!empty($ids)) {
                DB::table('student_reward_punishment')->whereIn('id', $ids)
                    ->update(array('approval_result' => 1));
            }
        }

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }
        if (!empty($school)) {
            $school_ids = DB::table('school_info')->select('id')
                ->where('id',$school)
                ->lists('id');
            $campus_ids = DB::table('campuses_school')->select('campuses_school.campus_id as id')
                ->join('school_info','school_info.id','=','campuses_school.school_id')
                ->whereIn('campuses_school.school_id', $school_ids)
                ->lists('id');
            $flag = 1;
        }

        if ($flag == 1) {
            $programs = DB::table('admissions')->whereIn('admissions.campuscode', $campus_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('student_reward_punishment', function ($join) {
                    $join->on('student_reward_punishment.student_id', '=', 'admissions.id');
                })
                ->leftjoin('reward_info', function ($join) {
                    $join->on('student_reward_punishment.reward_level', '=', 'reward_info.code');
                })
                ->leftjoin('punishment_info', function ($join) {
                    $join->on('student_reward_punishment.punishment', '=', 'punishment_info.code');
                })
                ->leftjoin('punishment_cause_info', function ($join) {
                    $join->on('student_reward_punishment.punishment_cause', '=', 'punishment_cause_info.code');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.status',4)
                ->select('student_reward_punishment.id', 'admissions.fullname', 'admissions.studentno','campuses.name',
                    'admissions.status','rawprograms.name as major_name','rawprograms.type', 'punishment_info.punishment',
                    'punishment_cause_info.punishment_cause', 'reward_info.reward_level', 'student_reward_punishment.remark',
                    'student_reward_punishment.document_id', 'student_reward_punishment.operator','student_reward_punishment.approval_result');

        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('student_reward_punishment', function ($join) {
                    $join->on('student_reward_punishment.student_id', '=', 'admissions.id');
                })
                ->leftjoin('reward_info', function ($join) {
                    $join->on('student_reward_punishment.reward_level', '=', 'reward_info.id');
                })
                ->leftjoin('punishment_info', function ($join) {
                    $join->on('student_reward_punishment.punishment', '=', 'punishment_info.code');
                })
                ->leftjoin('punishment_cause_info', function ($join) {
                    $join->on('student_reward_punishment.punishment_cause', '=', 'punishment_cause_info.code');
                })
                ->where('admissions.status',4)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('student_reward_punishment.id', 'admissions.fullname', 'admissions.studentno','campuses.name',
                    'admissions.status','rawprograms.name as major_name','rawprograms.type', 'punishment_info.punishment',
                    'punishment_cause_info.punishment_cause', 'reward_info.reward_level', 'student_reward_punishment.remark',
                    'student_reward_punishment.document_id', 'student_reward_punishment.operator','student_reward_punishment.approval_result');
        }
        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('status', '@if ($status == 4)
                                       在籍
                                 @endif')

            ->edit_column('remark', '@if ($reward_level >0)
                                       奖励
                                 @else
                                       处分
                                 @endif')
            /*->edit_column('major_name','<?php echo subStr($major_name,0,6)."..."?>')*/
            ->edit_column('approval_result', '@if ($approval_result == 0)
                                          未审核
                                 @elseif ($approval_result == 1)
                                          同意
                                @elseif ($approval_result == 2)
                                          不同意
                                 @endif')
            ->add_column('remark1', '<input type="text" id="remark" value="{{$remark}}" style="width: 60px;">')
            ->add_column('actions', '<a href="javascript:void(0)" id="noPass" value="{{ $id }}">不同意</a><br>
                                       <a href="javascript:void(0)" id="noCheck" value="{{ $id }}">未审核</a>'
            )
            ->add_column( 'isCheck', '
                                  <div align="center"><input type = "checkbox" name = "checkItem[]"  id= "checkItem" value="{{$id}}"></div>
                                   ')
            ->remove_column('id')
            ->make();
    }
    public function getApproveRewardPunishNoPass()
    {
        $id = Input::get('id');
        $remark = Input::get('remark');
        DB::table('student_reward_punishment')->where('id', $id)
            ->update(array('approval_result' => 2, 'remark' => $remark));
        return 'ok';
    }
    public function getApproveRewardPunishNoApprove()
    {
        $id = Input::get('id');
        DB::table('student_reward_punishment')->where('id', $id)
            ->update(array('approval_result' => 0));
        return 'ok';
    }

    public function getApproveDropOutProvince()
    {
        $title = Lang::get('admin/admissions/title.approve_dropout');
        $campuses = Campus::All();
        $majors = RawProgram::All();
        return View::make('admin/admissions/admissions_approve_dropout_province', compact('majors', 'campuses', 'title'));
    }
    public function getDataApproveDropOutProvince()
    {
        $ids = explode(',',Input::get('selectedAdmissions'));
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $state = Input::get('state');

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }

        if($state == 1) {
            if (!empty($ids)) {
                DB::table('admissions')
                    ->join('student_dropout', 'student_dropout.student_id', '=', 'admissions.id')
                    ->whereIn('student_dropout.id', $ids)
                    ->update(array('admissions.status' => 7, 'student_dropout.approval_result_province' => 1));
            }
            //DB::table('student_dropout')->whereIn('id',$ids)->update(array('approval_result_province'=>1));
        }

        $dropout_stu_ids = DB::table('student_dropout')->select('student_id as id')->lists('id');
        $programs = DB::table('admissions')

            ->whereIn('admissions.id',$dropout_stu_ids)
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->leftjoin('student_dropout', function ($join) {
                $join->on('student_dropout.student_id', '=', 'admissions.id');
            })
            ->where(function ($query) use ($filter) {
                if (!is_array($filter)) {
                    return $query;
                }
                foreach ($filter as $key => $value) {
                    $query = $query->where('admissions.'.$key, 'like', $value);
                }
                return $query;
            })
            ->where('student_dropout.is_deleted',0)
            ->select('student_dropout.id', 'student_dropout.application_year',
                'student_dropout.application_semester', 'admissions.fullname',
                'admissions.studentno', 'admissions.status', 'admissions.program',
                'rawprograms.name as pname', 'student_dropout.cause',
                'student_dropout.approval_result_province', 'student_dropout.approval_suggestion_province');

        return Datatables::of($programs)
            ->edit_column('program', '@if ($program == 12)
                                          本科
                                 @elseif ($program == 14)
                                          专科
                                 @endif')
            ->edit_column('application_semester', '@if ($application_semester == \'02\')
                                      秋季
                                 @elseif ($application_semester == \'01\')
                                      春季
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                      已录入
                                 @elseif ($status == 1)
                                      已上报
                                 @elseif ($status == 2)
                                      已审批
                                 @elseif ($status == 3)
                                      未注册
                                 @elseif ($status == 4)
                                      在籍
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @elseif ($status == 7)
                                      退学
                                 @endif')
            ->edit_column('approval_result_province', '@if ($approval_result_province == 0)
                                          未审核
                                 @elseif ($approval_result_province == 1)
                                          同意
                                 @elseif ($approval_result_province == 2)
                                          不同意
                                 @endif')
            ->edit_column('approval_suggestion_province', '
                                <input type="text" id="approval_suggestion_province" value="{{$approval_suggestion_province}}" style="width: 60px">
            ')
            ->add_column('actions', '<a href="javascript:void(0)" id="noPass" value="{{ $id }}">不同意</a><br>
                                       <a href="javascript:void(0)" id="noCheck" value="{{ $id }}">未审核</a>'
            )
            ->add_column( 'isCheck', '
                                  <div align="center"><input type = "checkbox" name = "checkItem[]"  id= "checkItem" value="{{$id}}"></div>
                                   ')
            ->remove_column('id')
            ->make();

    }
    public function getNoPassDropOutForProvince()
    {
        $id = Input::get('id');
        $remark = Input::get('remark');
        DB::table('admissions')->join('student_dropout','student_dropout.student_id','=','admissions.id')
            ->where('student_dropout.id',$id)
            ->update(array('admissions.status' => 4,
                'student_dropout.approval_result_province' => 2,
                'student_dropout.approval_suggestion_province' => $remark));
        return 'ok';
    }
    public function getNoApproveDropOutForProvince()
    {
        $id = Input::get('id');
        DB::table('admissions')->join('student_dropout','student_dropout.student_id','=','admissions.id')
            ->where('student_dropout.id',$id)
            ->update(array('admissions.status' => 4,
                'student_dropout.approval_result_province' => 0));
        return 'ok';
    }

    public function getApproveRecoveryApplicationProvince()
    {
        $title = Lang::get('admin/admissions/title.check_recovery_admissions_application');
        $campuses = Campus::All();
        $rawprograms = RawProgram::All();
        $schools = School::All();
        return View::make('admin/admissions/admissions_approve_recovery_province', compact('schools','rawprograms', 'campuses', 'title'));
    }
    public function getDataApproveRecoveryApplicationProvince()
    {
        $ids = explode(',',Input::get('selectedAdmissions'));
        $filter = array();
        $filter_recovery = array();
        $student_ids = array();
        $school = Input::get('school');
        $campus = Input::get('campus');
        $student_name = Input::get('student_name');
        $student_id = Input::get('student_id');
        $student_type = Input::get('student_type');
        $major_classification = Input::get('major_classification');
        $admissionyear = Input::get('admissionyear');
        $admissionsemester = Input::get('admissionsemester');
        $application_year = Input::get('application_year');
        $application_semester = Input::get('application_semester');
        $major = Input::get('major');
        $state = Input::get('state');
        $select_id=Input::get('btnID');
        $flag = 0 ;
        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }

        if (!empty($admissionyear)) {
            $filter["admissionyear"] = $admissionyear;
        }
        if (!empty($admissionsemester)) {
            $filter["admissionsemester"] = $admissionsemester;
        }

        if (!empty($student_type)) {
            $filter['enrollmenttype'] = $student_type;
        }
        if (!empty($application_year)) {
            $filter_recovery['recovery_year'] = $application_year;
        }
        if (!empty($application_semester)) {
            $filter_recovery['recovery_semester'] = $application_semester;
        }

        if($state == 1){
            if (!empty($ids)) {
                DB::table('admissions')
                    ->join('student_recovery','student_recovery.student_id','=','admissions.id')
                    ->whereIn('student_recovery.id', $ids)
                    ->update(array('admissions.status' => 4));
            }
            DB::table('student_recovery')->whereIn('id',$ids)->update(array('approval_result'=>1));
        }

        if (!empty($filter_recovery)) {
            $student_ids = DB::table('student_recovery')->select('student_id as id')
                ->where(function ($query) use ($filter_recovery) {
                    if (!is_array($filter_recovery)) {
                        return $query;
                    }
                    foreach ($filter_recovery as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $flag = 1;
        }else{
            $student_ids = DB::table('student_recovery')->select('student_id as id')->lists('id');
            $flag = 1;
        }

        if ($flag == 1){
            $programs = DB::table('admissions')
                ->whereIn('admissions.id',$student_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('student_recovery', function ($join) {
                    $join->on('student_recovery.student_id', '=', 'admissions.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('student_recovery.is_deleted',0)
                ->select('student_recovery.id', 'student_recovery.recovery_year',
                    'student_recovery.recovery_semester', 'admissions.fullname',
                    'admissions.studentno', 'campuses.name as cname','admissions.program','rawprograms.name as pname',
                    'admissions.admissionyear','admissions.admissionsemester',
                    'admissions.status', 'student_recovery.approval_result',
                    'student_recovery.remark');
        }else{
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('student_recovery', function ($join) {
                    $join->on('student_recovery.student_id', '=', 'admissions.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('student_recovery.is_deleted',0)
                ->select('student_recovery.id', 'student_recovery.recovery_year',
                    'student_recovery.recovery_semester', 'admissions.fullname',
                    'admissions.studentno', 'campuses.name as cname','admissions.program','rawprograms.name as pname',
                    'admissions.admissionyear','admissions.admissionsemester',
                    'admissions.status', 'student_recovery.approval_result',
                    'student_recovery.remark');
        }
        return Datatables::of($programs)
            ->edit_column('program', '@if ($program == 12)
                                          本科
                                 @elseif ($program == 14)
                                          专科
                                 @endif')
            ->edit_column('recovery_semester', '@if ($recovery_semester == \'02\')
                                      秋季
                                 @elseif ($recovery_semester == \'01\')
                                      春季
                                 @endif')
            ->edit_column('admissionsemester', '@if ($admissionsemester == \'02\')
                                      秋季
                                 @elseif ($admissionsemester == \'01\')
                                      春季
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                      已录入
                                 @elseif ($status == 1)
                                      已上报
                                 @elseif ($status == 2)
                                      已审批
                                 @elseif ($status == 3)
                                      未注册
                                 @elseif ($status == 4)
                                      在籍
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @elseif ($status == 7)
                                      退学
                                 @endif')
            ->edit_column('approval_result', '@if ($approval_result == 0)
                                          未审核
                                 @elseif ($approval_result == 1)
                                          同意
                                 @elseif ($approval_result == 2)
                                          不同意
                                 @endif')
            ->edit_column('remark', '<input type="text" id="remark" value="{{$remark}}" style="width: 100%;">')
            ->add_column('actions', '<a href="javascript:void(0)" id="noPass" value="{{ $id }}">不同意</a><br>
                                       <a href="javascript:void(0)" id="noCheck" value="{{ $id }}">未审核</a>'
            )
            ->add_column( 'isCheck', '
                                  <div align="center"><input type = "checkbox" name = "checkItem[]"  id= "checkItem" value="{{$id}}"></div>
                                   ')
            ->remove_column('id')
            ->make();
    }
    public function getNoPassRecoveryForProvince()
    {
        $id = Input::get('id');
        $remark = Input::get('remark');
        DB::table('student_recovery')->where('id', $id)
            ->update(array('approval_result' => 2, 'remark' => $remark));
        return 'ok';
    }
    public function getNoApproveRecoveryForProvince()
    {
        $id = Input::get('id');
        DB::table('student_recovery')->where('id', $id)
            ->update(array('approval_result' => 0));
        return 'ok';
    }

    public function getAdmissionChangeAppointGroup()
    {
        $title = Lang::get('admin/admissions/title.change_admissions_appoint_group');
        $user_id = Auth::user()->id;
        //$user_id = 186;
        $campus = DB::table('campuses')->where('userID', $user_id)->first();
        return View::make('admin/admissions/admissions_change_appoint_group', compact('campus', 'title'));
    }
    public function getDataChangeAdmissionsForAppointGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $ids = explode(',',Input::get('selectedIds'));
        $group = explode(',',Input::get('selectedGroup'));
        $state = Input::get('state');

        if ($state == 2) {
            for ($i = 1; $i < count($ids); $i++) {
                if ($group[$i] > 0) {
                    DB::table('student_status_changing')->where('id', $ids[$i])->update(array('current_class_id' => $group[$i]));
                }
            }
        }
        $student_ids = DB::table('student_status_changing')->where('current_campus_id',$campus->id)->select('student_id as id')->lists('id');
        $admissions = DB::table('student_status_changing')
            ->join('admissions', function ($join) {
                $join->on('student_status_changing.student_id', '=', 'admissions.id');
            })
            ->leftjoin('admission_group', function ($join) {
                $join->on('admission_group.admission_id', '=', 'admissions.id');
            })
            ->leftjoin('groups', function ($join) {
                $join->on('admission_group.group_id', '=', 'groups.id');
            })
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->where('student_status_changing.current_campus_id',$campus->id)
            ->where('admissions.status',5)
            ->select('student_status_changing.id', 'admissions.id as select', 'admissions.studentno',
                'admissions.fullname', 'admissions.program', 'admissions.status', 'groups.sysid',
                'groups.name as gname', 'rawprograms.name as major_name', 'rawprograms.type',
                'admissions.nationgroup', 'admissions.politicalstatus', 'admissions.maritalstatus',
                'admissions.jiguan', 'admissions.hukou', 'admissions.distribution',
                'admissions.is_serving', 'student_status_changing.current_class_id');
        return Datatables::of($admissions)
            ->edit_column('select', '
                <input type="checkbox" id="checkItem" name="checkItem[]" value="{{$id}}">
            ')
            ->edit_column('program', '@if ($program == 12)
                                          本科
                                 @elseif ($program == 14)
                                          专科
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                      已录入
                                 @elseif ($status == 1)
                                      已上报
                                 @elseif ($status == 2)
                                      已审批
                                 @elseif ($status == 3)
                                      未注册
                                 @elseif ($status == 4)
                                      在籍
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @elseif ($status == 7)
                                      退学
                                 @endif')
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('gname', '<select id="group" name="group[]" style="width:100%;">
                                        <option value="">请选择</option>
                                        @foreach( ($groups = Groups::leftjoin(\'programs\', \'programs.id\', \'=\', \'groups.programs_id\')
                                        ->leftjoin(\'campuses\',\'campuses.id\',\'=\',\'programs.campus_id\')
                                         ->where(\'campuses.userID\', \'=\', Auth::user ()->id)
                                         ->select(\'groups.id as gid\',\'groups.name as gname\')
                                         ->get())  as $group)
                                            @if ($group->gid == $current_class_id)
                                            <option value="{{$group->gid}}" selected="selected">{{$group->gname}}</option>
                                            @else
                                            <option value="{{$group->gid}}">{{$group->gname}}</option>
                                            @endif
                                        @endforeach
                                    </select>
            ')
            ->edit_column('maritalstatus', '@if ($maritalstatus == 0)
                                          未婚
                                 @elseif ($maritalstatus == 1)
                                          已婚
                                 @endif')
            ->edit_column('hukou', '@if ($hukou == 0)
                            城镇户口
                        @elseif ($hukou == 1)
                            农村户口
                        @elseif ($hukou == 2)
                            外国公民
                        @elseif ($hukou == 3)
                            其他
                        @endif
            ')
            ->edit_column('politicalstatus', '@if($politicalstatus == 1)
                                            中共党员
                                        @elseif($politicalstatus == 2)
                                            共青团员
                                        @elseif($politicalstatus == 3)
                                            民革会员
                                        @elseif($politicalstatus == 4)
                                            民盟盟员
                                        @elseif($politicalstatus == 5)
                                            民进会员
                                        @elseif($politicalstatus == 6)
                                            民建会员
                                        @elseif($politicalstatus == 7)
                                            农工党党员
                                        @elseif($politicalstatus == 8)
                                            致公党党员
                                        @elseif($politicalstatus == 9)
                                            九三学社社员
                                        @elseif($politicalstatus == 10)
                                            台盟盟员
                                        @elseif($politicalstatus == 11)
                                            无党派民主人士
                                        @elseif($politicalstatus == 12)
                                            群众
                                        @elseif($politicalstatus == 13)
                                            其他
                                        @endif
                                 ')
            ->edit_column('nationgroup', '@if ($nationgroup==1)
                                        汉族
                                     @elseif ($nationgroup==2)
                                        蒙古族
                                     @elseif ($nationgroup==3)
                                        回族
                                     @elseif ($nationgroup==4)
                                        藏族
                                     @elseif ($nationgroup==5)
                                        维吾尔族
                                     @elseif ($nationgroup==6)
                                        苗族
                                     @elseif ($nationgroup==7)
                                        彝族
                                     @elseif ($nationgroup==8)
                                        壮族
                                     @elseif ($nationgroup==9)
                                        布依族
                                     @elseif ($nationgroup==10)
                                        朝鲜族
                                     @elseif ($nationgroup==11)
                                        满族
                                     @elseif ($nationgroup==12)
                                        侗族
                                     @elseif ($nationgroup==13)
                                        瑶族
                                     @elseif ($nationgroup==14)
                                        白族
                                     @elseif ($nationgroup==15)
                                        土家族
                                     @elseif ($nationgroup==16)
                                        哈尼族
                                     @elseif ($nationgroup==17)
                                        哈萨克族
                                     @elseif ($nationgroup==18)
                                        傣族
                                     @elseif ($nationgroup==19)
                                        黎族
                                     @elseif ($nationgroup==20)
                                        傈僳族
                                     @elseif ($nationgroup==21)
                                        佤族
                                     @elseif ($nationgroup==22)
                                        畲族
                                     @elseif ($nationgroup==23)
                                        高山族
                                     @elseif ($nationgroup==24)
                                        拉祜族
                                     @elseif ($nationgroup==25)
                                        水族
                                     @elseif ($nationgroup==26)
                                        东乡族
                                     @elseif ($nationgroup==27)
                                        纳西族
                                     @elseif ($nationgroup==28)
                                        景颇族
                                     @elseif ($nationgroup==29)
                                        柯尔克孜族
                                     @elseif ($nationgroup==30)
                                        土族
                                     @elseif ($nationgroup==31)
                                        达斡尔族
                                     @elseif ($nationgroup==32)
                                        仫佬族
                                     @elseif ($nationgroup==33)
                                        羌族
                                     @elseif ($nationgroup==34)
                                        布朗族
                                     @elseif ($nationgroup==35)
                                        撒拉族
                                     @elseif ($nationgroup==36)
                                        毛南族
                                     @elseif ($nationgroup==37)
                                        仡佬族
                                     @elseif ($nationgroup==38)
                                        锡伯族
                                     @elseif ($nationgroup==39)
                                        阿昌族
                                     @elseif ($nationgroup==40)
                                        普米族
                                     @elseif ($nationgroup==41)
                                        塔吉克族
                                     @elseif ($nationgroup==42)
                                        怒族
                                     @elseif ($nationgroup==43)
                                        乌孜别克族
                                     @elseif ($nationgroup==44)
                                        俄罗斯族
                                     @elseif ($nationgroup==45)
                                        鄂温克族
                                     @elseif ($nationgroup==46)
                                        崩龙族
                                     @elseif ($nationgroup==47)
                                        保安族
                                     @elseif ($nationgroup==48)
                                        裕固族
                                     @elseif ($nationgroup==49)
                                        京族
                                     @elseif ($nationgroup==50)
                                        塔塔尔族
                                     @elseif ($nationgroup==51)
                                           独龙族
                                     @elseif ($nationgroup==52)
                                        鄂伦春族
                                     @elseif ($nationgroup==53)
                                        赫哲族
                                     @elseif ($nationgroup==54)
                                        门巴族
                                     @elseif ($nationgroup==55)
                                        珞巴族
                                     @elseif ($nationgroup==56)
                                        基诺族
                                     @elseif ($nationgroup==97)
                                        其它
                                     @elseif ($nationgroup==98)
                                        外国血统中国籍人士\';
                       @endif
                       ')
            ->edit_column('distribution','@if ($distribution == 0)
                                      城镇应届
                                 @elseif ($distribution == 1)
                                      农村应届
                                 @elseif ($distribution == 2)
                                      城镇往届
                                 @elseif ($distribution == 3)
                                      农村往届
                                 @elseif ($distribution == 4)
                                      工人
                                 @elseif ($distribution == 5)
                                      干部
                                 @elseif ($distribution == 6)
                                      服役军人
                                 @elseif ($distribution == 7)
                                      台籍青年
                                 @elseif ($distribution == 8)
                                      港澳台侨
                                 @elseif ($distribution == 9)
                                      其他
                                 @endif
           ')
            ->edit_column('is_serving','@if ($is_serving == 0)
                                      不在职
                                    @elseif ($is_serving == 1)
                                      在职
                                      @endif
            ')
            ->remove_column('current_class_id')
            ->make();

    }
}