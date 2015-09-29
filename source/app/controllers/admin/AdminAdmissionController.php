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
                'student_reward_punishment.approval_result');
        return Datatables::of($programs)
            ->add_column('year', '', 4)
            ->add_column('semester', '', 5)
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
                'student_reward_punishment.operator', 'student_reward_punishment.approval_result');
        return Datatables::of($programs)
            ->add_column('year', '', 5)
            ->add_column('semester', '', 6)
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
                if ($pos === false){

                }
                else{
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


        for($i = 0; $i < count ( $result ); $i ++) {
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
        }

        $filename = 'export_' . strftime('%Y%m%d');
        $excel = Excel::create($filename, function ($excel) use ($result) {
            $excel->sheet('New sheet', function ($sheet) use ($result) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array('A' => '0'));
                $sheet->setColumnFormat(array('C' => '0'));
                $sheet->setColumnFormat(array('E' => '0'));
                $sheet->setColumnFormat(array('I' => '0'));
                $sheet->loadView('admin/admissions/photo_export', array(
                    'result' => $result
                ));
            });
        });

        $excel_file = $excel->store('xls', false, true);
        $file_path = './my.zip';
        $zipFile = './photos/Sunset.jpg';

        $zip = new ZipArchive ();
        if ($zip->open($file_path, ZIPARCHIVE::OVERWRITE) !== TRUE) {
            return;
        }
        $zip_excel_name = 'File/'.basename($excel_file['full']);
        $photo_name = 'Photo/' . basename($zipFile);

        $zip->addFile($excel_file['full'], $zip_excel_name);
        $zip->addFile($zipFile, $photo_name);
        $zip->close();

        $fp = fopen($file_path, "r");
        $file_size = filesize($file_path);

        header("Content-Type: application/zip");
        header("Content-Length: " . filesize('my.zip'));
        header("Content-Disposition: attachment; filename=\"my.zip\"");
        readfile('my.zip');
        /*
//下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:" . $file_size);
        Header("Content-Disposition: attachment; filename=" . $file_path);
        $buffer = 1024;
        $file_count = 0;
//向浏览器返回数据
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp);*/
    }


    public function getAdmissionsIndexForProvince()
    {
        $title = Lang::get('admin/admissions/title.edit_admissions_info');
        $campuses = DB::table('campuses')
            ->select('id', 'name')
            ->get();
        $schools = DB::table('school_info')
            ->where('is_deleted', 0)
            ->select('id', 'school_name')
            ->get();
        $majors = RawProgram::All();
        return View::make('admin/admissions/admissions_province', compact('majors', 'campuses', 'schools', 'title'));
    }

    public function getDataAdmissionsForProvince()
    {

        $filter = array();
        $filter_program = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $admission_state = Input::get('admission_state');

        $flag = 0;

        $status = array(4,6);

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }
        if (!empty($admission_state)) {
            $filter['status'] = $admission_state;
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }

        if (!empty($school)) {
            $filter_program['id'] = $school;
        }
        if (!empty($filter_program)) {
            $program_ids = DB::table('school_info')->select('id')
                ->where(function ($query) use ($filter_program) {
                    if (!is_array($filter_program)) {
                        return $query;
                    }
                    foreach ($filter_program as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $student_ids = DB::table('groups')->select('id')
                ->whereIn('school_id', $program_ids)
                ->lists('id');
            $flag = 1;
        }

        if ($flag == 1) {
            $programs = DB::table('admissions')->whereIn('admission_group.admission_id', $student_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('admission_group',function ($join){
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->whereIn('admissions.status',$status)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admission_details.attainment',
                    'campuses.name as campus_name', 'admissions.gender', 'admissions.idtype', 'admissions.idnumber',
                    'admissions.dateofbirth', 'admission_details.formerlevel', 'admission_details.attainmentcert',
                    'admissions.nationgroup');
        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->whereIn('admissions.status',$status)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admission_details.attainment',
                    'campuses.name as campus_name', 'admissions.gender', 'admissions.idtype', 'admissions.idnumber',
                    'admissions.dateofbirth', 'admission_details.formerlevel', 'admission_details.attainmentcert',
                    'admissions.nationgroup');
        }

        return Datatables::of($programs)
            ->edit_column('gender', '@if($gender == \'f\')
                                          女
                                 @elseif($gender == \'m\')
                                          男
                                 @endif')
            ->edit_column('idtype', '@if ($idtype == 1)
                                      身份证
                                 @elseif ($idtype == 2)
                                      军官证
                                 @elseif ($idtype == 3)
                                      护照
                                 @elseif ($idtype == 4)
                                      港澳居民证件
                                 @elseif ($idtype == 5)
                                      其他
                                 @endif')
            ->edit_column('formerlevel', '@if($formerlevel == 1)
                                        高中毕业
                                    @elseif($formerlevel == 2)
                                        职高毕业
                                    @elseif($formerlevel == 3)
                                        中专毕业
                                    @elseif($formerlevel == 4)
                                        技校毕业
                                    @elseif($formerlevel == 5)
                                        专科毕业
                                    @elseif($formerlevel == 6)
                                        本科毕业
                                    @elseif($formerlevel == 7)
                                        硕士研究生毕业
                                    @elseif($formerlevel == 8)
                                        博士研究生毕业
                                    @elseif($formerlevel == 9)
                                        其他毕业
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
            ->add_column('actions', '
                           <a href="{{URL::to(\'admin/admissions/edit_admissions_province?student_id=\'.$id)}}"  target="detail_info"><label id="btnEdit" onmouseover="this.style.cursor=\'hand\'">修改该生信息</label></a>
                           ')
            ->make();

    }

    public function getIndexForRecordRewardPunishProvince()
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $rawprograms = RawProgram::All();
        $campuses = Campus::All();
        return View::make('admin/admissions/admissions_record_reward_punish_province', compact('campuses', 'rawprograms', 'title'));
    }

    public function getDataForRecordRewardPunishProvince()
    {
        $filter = array();
        $filter_school = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $school = Input::get('school');
        $campus = Input::get('campus');
        $flag = 0;

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
            $campus_ids = DB::table('campuses')->select('id')
                ->where('campuses.school_id',$school)
                ->lists('id');
            $student_ids = DB::table('admissions')->select('id')
                ->whereIn('campuscode', $campus_ids)
                ->lists('id');
            $flag = 1;
        }


        if ($flag == 1) {
            $programs = DB::table('admissions')->whereIn('admissions.id', $student_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
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
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'campuses.name as cname','rawprograms.name as major_name',
                    'rawprograms.type', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');

        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
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
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'campuses.name as cname', 'rawprograms.name as major_name',
                    'rawprograms.type', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');
        }
        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('formerlevel', '@if($formerlevel == 1)
                                        高中毕业
                                    @elseif($formerlevel == 2)
                                        职高毕业
                                    @elseif($formerlevel == 3)
                                        中专毕业
                                    @elseif($formerlevel == 4)
                                        技校毕业
                                    @elseif($formerlevel == 5)
                                        专科毕业
                                    @elseif($formerlevel == 6)
                                        本科毕业
                                    @elseif($formerlevel == 7)
                                        硕士研究生毕业
                                    @elseif($formerlevel == 8)
                                        博士研究生毕业
                                    @elseif($formerlevel == 9)
                                        其他毕业
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
            ->add_column('actions', '
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/record_reward_province/\')}}" class="iframe">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/record_punish_province/\')}}" class="iframe">惩罚</a>
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
            ->edit_column('gender', '@if($gender == \'f\')
                                          女
                                 @elseif($gender == \'m\')
                                          男
                                 @endif')
            ->remove_column('id')
            ->make();

    }

    public function getRecordAward($id)
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $rewards = RewardInfo::where('is_deleted',0)->get();
        return View::make('admin/admissions/record_reward_province', compact('id', 'title','rewards'));
    }

    public function postRecordAward($id)
    {
        $reward_date = Input::get('reward_date');
        $rewardType = Input::get('rewardType');
        $file_num = Input::get('file_num');
        $remark = Input::get('remark');

        $recordexists = DB::table('student_reward_punishment')
            ->where('document_id', $file_num)
            ->where('reward_level', $rewardType)
            ->where('student_id',$id)
            ->count();
        if ($recordexists == 0) {
            $rewardinfo = new RewardPunishment();
            $rewardinfo->document_id = $file_num;
            $rewardinfo->reward_level = $rewardType;
            $rewardinfo->approval_result = 0;
            $rewardinfo->date = $reward_date;
            $rewardinfo->remark = $remark;
            $rewardinfo->student_id = $id;
            $rewardinfo->is_deleted = 0;
            $rewardinfo->created_at = new DateTime();
            $rewardinfo->updated_at = new DateTime();
            $rewardinfo->save();
            return Redirect::to('admin/admissions/record_reward_punish_province')->with('success',Lang::get('admin/admissions/messages.create.success'));
        }else{
            return Redirect::to('admin/admissions/'.$id.'/record_reward_province')->withErrors(Lang::get('admin/admissions/messages.already_exists'));
        }
    }

    public function getRecordPunish($id)
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $codes = PunishmentInfo::where('is_deleted',0)->get();
        $causes = PunishmentCause::where('is_deleted',0)->get();
        return View::make('admin/admissions/record_punish_province', compact('id', 'title','codes','causes'));
    }

    public function postRecordPunish($id)
    {
        $punish_code = Input::get('punish_code');
        $punish_cause = Input::get('punish_cause');
        $file_num = Input::get('file_num');
        $remark = Input::get('remark');
        $punish_date = Input::get('punish_date');

        $recordexists = DB::table('student_reward_punishment')
            ->where('document_id', $file_num)
            ->where('punishment', $punish_code)
            ->where('punishment_cause', $punish_cause)
            ->where('student_id',$id)
            ->count();
        if ($recordexists == 0) {
            $rewardinfo = new RewardPunishment();
            $rewardinfo->document_id = $file_num;
            $rewardinfo->punishment = $punish_code;
            $rewardinfo->punishment_cause = $punish_cause;
            $rewardinfo->approval_result = 0;
            $rewardinfo->date = $punish_date;
            $rewardinfo->is_deleted = 0;
            $rewardinfo->remark = $remark;
            $rewardinfo->student_id = $id;
            $rewardinfo->created_at = new DateTime();
            $rewardinfo->updated_at = new DateTime();
            $rewardinfo->save();
            return Redirect::to('admin/admissions/record_reward_punish_province')->with('success',Lang::get('admin/admissions/messages.create.success'));
        }else{
            return Redirect::to('admin/admissions/'.$id.'/record_punish_province')->withErrors(Lang::get('admin/admissions/messages.already_exists'));
        }

    }


    public function getIndexForExpelProvince()
    {
        $title = Lang::get('admin/admissions/title.expel_admissions');
        $campuses = DB::table('campuses')
            ->select('id', 'name')
            ->get();
        $schools = DB::table('school_info')
            ->where('is_deleted', 0)
            ->select('id', 'school_name')
            ->get();
        $majors = RawProgram::All();
        return View::make('admin/admissions/admissions_expel_province', compact('majors', 'campuses', 'schools', 'title'));
    }

    public function getDataCampusesWithSchool()
    {
        $school = intval(trim($_GET["school"]));
        if (isset($school)) {
            $campuses = DB::table('campuses')
                ->join('campuses_school', 'campuses_school.campus_id', '=', 'campuses.id')
                ->join('school_info', 'school_info.id', '=', 'campuses_school.school_id')
                ->where('school_info.id', $school)
                ->select('campuses.id as cid','campuses.name as cname')
                ->get();
            $select[] = array("id"=>'',"name"=>'全部');
            foreach ($campuses as $campus){
                $select[] = array("id"=>$campus->cid,"name"=>$campus->cname);
            }
            echo json_encode($select);
        }
    }

    public function getDataForExpelProvince()
    {

        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $flag = 0;
        $status = array(4,7);

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
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->whereIn('admissions.status',$status)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id',
                    'campuses.name as campus_name','rawprograms.name','rawprograms.type','groups.sysid',
                    'admissions.dateofbirth','admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan', 'admissions.status');
        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->whereIn('admissions.status',$status)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id',
                    'campuses.name as campus_name','rawprograms.name','rawprograms.type','groups.sysid',
                    'admissions.dateofbirth','admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan', 'admissions.status');
        }

        return Datatables::of($programs)
            ->edit_column('type', '@if($type == 12)
                                          本科
                                 @elseif($type == 14)
                                          专科
                                 @endif')
            ->edit_column('gender', '@if($gender == \'f\')
                                          女
                                 @elseif($gender == \'m\')
                                          男
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
            ->edit_column('formerlevel', '@if($formerlevel == 1)
                                        高中毕业
                                    @elseif($formerlevel == 2)
                                        职高毕业
                                    @elseif($formerlevel == 3)
                                        中专毕业
                                    @elseif($formerlevel == 4)
                                        技校毕业
                                    @elseif($formerlevel == 5)
                                        专科毕业
                                    @elseif($formerlevel == 6)
                                        本科毕业
                                    @elseif($formerlevel == 7)
                                        硕士研究生毕业
                                    @elseif($formerlevel == 8)
                                        博士研究生毕业
                                    @elseif($formerlevel == 9)
                                        其他毕业
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
            ->add_column('actions', '@if($status==7)
                                    开除
                                @elseif ($status==4)
                                    <a href="{{URL::to(\'admin/admissions/\'.$id.\'/admissions_expel_details\')}}">开除</a>
                               @endif
                           ')
            ->add_column('cancel_expel', '@if($status==7)
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/admissions_cancel_expel/\')}}">撤销</a>
                           @else
                           <a>撤销</a>
                           @endif
                           ')
            ->remove_column('id')
            ->make();

    }


    public function getExpelDetailsProvince($id)
    {
        $title = Lang::get('admin/admissions/title.expel_admissions');
        $admissions = DB::table('admissions')
            ->select('studentno')
            ->where('id',$id)
            ->lists('studentno');
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        return View::make('admin/admissions/admissions_expel_details_province',compact('id','admissions','yearsemester','title'));

    }

    public function postExpelDetailsProvince($id)
    {
        $count = AdmissionExpel::where('student_id',$id)->where('is_deleted',0)->count();
        if ($count == 0) {
            $expel = new AdmissionExpel();
            $expel->expel_year = Input::get('year');
            $expel->expel_semester = Input::get('semester');
            $expel->student_id = $id;
            $expel->cause = Input::get('cause');
            $expel->document_id = Input::get('document_id');
            $expel->remark = Input::get('remark');
            $expel->is_deleted = 0;
            $expel->save();

            $admission = Admission::find($id);
            $admission->status = 7;
            $admission->save();

            return Redirect::to('admin/admissions/expel_admissions')->with('success',Lang::get('admin/admissions/messages.expel.success'));
        }else{
            return Redirect::to('admin/admissions/expel_admissions')->withError(Lang::get('admin/admissions/messages.already_exists'));
        }

    }

    public function getCancelExpelProvince($id)
    {
        $title = Lang::get('admin/admissions/title.cancel_expel_admissions');
        $admission = DB::table('admissions')
            ->select('studentno','fullname')
            ->where('id',$id)
            ->first();
        return View::make('admin/admissions/admissions_cancel_expel_province',compact('id','admission','title'));

    }

    public function postCancelExpelProvince($id)
    {

        $admission = Admission::find($id);
        $admission->status = 4;
        $admission->save();

        AdmissionExpel::where('student_id',$id)->update(array('is_deleted'=>1));

        return Redirect::to('admin/admissions/expel_admissions')->with('success',Lang::get('admin/admissions/messages.expel.cancel'));

    }


    public function getChangeAdmissionsModuleYearSemesterProvince()
    {
        $title = Lang::get('admin/admissions/title.change_admissions_module_year_semester');
        $admissions = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->get();
        foreach ($admissions as $admission){
            $current_year = $admission->current_year;
            $current_semester = $admission->current_semester;
        };
        return View::make('admin/admissions/change_admissions_module_year_semester_province', compact('current_year','current_semester','title'));
    }

    public function postChangeYearSemesterForAdmissions(){
        $year = Input::get('year');
        $semester = Input::get('semester');
        $module_current = ModuleCurrent::find(4);
        $module_current->current_year=$year;
        $module_current->current_semester=$semester;
        $module_current->save();
        return redirect::to('admin/admissions/change_admissions_module_year_semester')->with('success','更替年度与学期成功');
    }

    public function getDataCampus()
    {

        $school = intval(trim($_GET["school"]));
        if (isset($school)) {
            $campuses = DB::table('campuses')
                ->join('campuses_school', 'campuses_school.campus_id', '=', 'campuses.id')
                ->join('school', 'campuses_school.school_id', '=', 'school.id')
                ->where('school.id', $school)
                ->select('campuses.id as cid','campuses.name as cname')
                ->get();
            $select[] = array("id"=>'',"name"=>'全部');
            foreach ($campuses as $campus){
                $select[] = array("id"=>$campus->cid,"name"=>$campus->cname);
            }
            echo json_encode($select);
        }
    }

    public function getAdmissionsChangeAuthorityForProvince()
    {
        $title = Lang::get('admin/admissions/title.admissions_change_authority');
        return View::make('admin/admissions/admissions_change_authority_province', compact('title'));
    }

    public function getDataAdmissionsChangeAuthorityForProvince()
    {

        $ids = Input::get('checkItem');
        $state = Input::get('final_result');


        if ($state == 1)
        {
            if (!empty($ids)){
                //      DB::table('campuses')->whereIn('id',$ids)->update(array('status_changing'=>1));
                for ($i=0;$i<count($ids);$i++){
                    $changings = Campus::find($ids[$i]);
                    $changings->status_changing == 0 ? $changings->status_changing=1 : $changings->status_changing=0 ;
                    $changings->save();
                }
            }
        }
        $campus = DB::table('campuses')->select('id','name','sysid','sysid as actions','status_changing');
        return Datatables::of($campus)
            ->edit_column('actions', '
                                <input type="checkbox" id="checkItem[]" name="checkItem" value="{{$id}}" >
                                 ')
            ->edit_column('status_changing','
                      <label>  {{$status_changing == 1 ? "开通":"关闭 "}}</label>
                ')
            ->make();

    }

    public function getCreateRewardProvince()
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $mode = "create";
        return View::make('admin/admissions/define_reward_province', compact('title','mode'));
    }
    public function postCreateRewardProvince()
    {
        $level = Input::get('reward_level');
        $code = Input::get('code');

        $count = RewardInfo::where('reward_level',$level)->where('code',$code)->count();

        if ($count==0) {
            $reward = new RewardInfo();
            $reward->reward_level = $level;
            $reward->code = $code;
            $reward->is_deleted = 0;
            $reward->created_at = new DateTime();
            $reward->updated_at = new DateTime();
            $reward->save();

            return Redirect::to('admin/admissions/reward_index')->with('success', Lang::get('admin/admissions/messages.reward.success'));
        }else{
            return Redirect::to('admin/admissions/reward_index')->withError(Lang::get('admin/admissions/messages.already_exists'));
        }
    }
    public function getRewardIndexForProvince()
    {
        $title = Lang::get('admin/admissions/table.define_reward_level');
        return View::make('admin/admissions/reward_index_province', compact('title'));
    }

    public function getRewardForProvince()
    {

        $rewards = DB::table('reward_info')
            ->where('is_deleted',0)
            ->select('id', 'reward_level', 'code');

        return Datatables::of($rewards)
            ->add_column('action','
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/edit_reward\')}}" class="iframe">{{Lang::get(\'general.edit\')}}</a> &nbsp;&nbsp;
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/delete_reward\')}}" class="iframe">{{Lang::get(\'general.delete\')}}</a>
            ')
            ->remove_column('id')
            ->make();
    }

    public function getEditRewardProvince($id)
    {
        $title = Lang::get('admin/admissions/table.edit_reward_level');
        $mode = "edit";
        $reward = RewardInfo::find($id)->first();
        return View::make('admin/admissions/define_reward_province', compact('title','mode','reward'));
    }
    public function postEditRewardProvince($id)
    {
        $level = Input::get('reward_level');
        $sysid = Input::get('code');

        $reward = RewardInfo::find($id);
        $reward->reward_level = $level;
        $reward->code = $sysid;
        $reward->updated_at = new DateTime();
        $reward->save();

        return Redirect::to('admin/admissions/reward_index')->with('success', Lang::get('admin/admissions/messages.reward.success'));

    }

    public function getDeleteRewardProvince($id)
    {
        // Title
        $title = Lang::get('admin/admissions/table.delete_reward_level');
        // Show the page
        return View::make('admin/admissions/delete_reward_province', compact('id', 'title'));
    }

    public function postDeleteRewardProvince($id)
    {

        $is_used = DB::table('student_reward_punishment')->where('reward_level',$id)->count();
        if ($is_used ==0){
            $reward = RewardInfo::find($id);
            $reward->is_deleted = 1;
            $reward->save();

            $count = DB::table('reward_info')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count > 0) {
                return Redirect::to('admin/admissions/reward_index')->with('success', Lang::get('admin/admissions/messages.reward.delete_success'));
            }
        }else {
            // There was a problem deleting the user
            return Redirect::to('admin/admissions/reward_index')->withError(Lang::get('admin/admissions/messages.reward.delete_error'));
        }
    }

    public function getPunishCodeIndexForProvince()
    {
        $title = Lang::get('admin/admissions/table.define_punish_code');
        return View::make('admin/admissions/punish_code_index_province', compact('title'));
    }

    public function getPunishCodeForProvince()
    {

        $rewards = DB::table('punishment_info')
            ->where('is_deleted',0)
            ->select('id', 'punishment', 'code');

        return Datatables::of($rewards)
            ->add_column('action','
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/edit_punish_code\')}}" class="iframe">{{Lang::get(\'general.edit\')}}</a> &nbsp;&nbsp;
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/delete_punish_code\')}}" class="iframe">{{Lang::get(\'general.delete\')}}</a>
            ')
            ->remove_column('id')
            ->make();
    }

    public function getCreatePunishCodeProvince()
    {
        $title = Lang::get('admin/admissions/table.define_punish_code');
        $mode = "create";
        return View::make('admin/admissions/define_punish_code_province', compact('title','mode'));
    }
    public function postCreatePunishCodeProvince()
    {
        $punishment = Input::get('punishment');
        $code = Input::get('code');

        $count = PunishmentInfo::where('punishment',$punishment)->where('code',$code)->count();

        if ($count==0) {
            $codes = new PunishmentInfo();
            $codes->punishment = $punishment;
            $codes->code = $code;
            $codes->is_deleted = 0;
            $codes->created_at = new DateTime();
            $codes->updated_at = new DateTime();
            $codes->save();

            return Redirect::to('admin/admissions/punish_code')->with('success', Lang::get('admin/admissions/messages.punish_code.success'));
        }else{
            return Redirect::to('admin/admissions/punish_code')->withError(Lang::get('admin/admissions/messages.already_exists'));
        }
    }

    public function getEditPunishCodeProvince($id)
    {
        $title = Lang::get('admin/admissions/table.define_punish_code');
        $mode = "edit";
        $code = PunishmentInfo::find($id)->first();
        return View::make('admin/admissions/define_punish_code_province', compact('title','mode','code'));
    }
    public function postEditPunishCodeProvince($id)
    {
        $punishment = Input::get('punishment');
        $code = Input::get('code');

        $codes = PunishmentInfo::find($id);
        $codes->punishment = $punishment;
        $codes->code = $code;
        $codes->updated_at = new DateTime();
        $codes->save();

        return Redirect::to('admin/admissions/punish_code')->with('success', Lang::get('admin/admissions/messages.punish_code.success'));

    }

    public function getDeletePunishCodeProvince($id)
    {
        // Title
        $title = Lang::get('admin/admissions/table.delete_punish_code');
        // Show the page
        return View::make('admin/admissions/delete_punish_code_province', compact('id', 'title'));
    }

    public function postDeletePunishCodeProvince($id)
    {

        $is_used = DB::table('student_reward_punishment')->where('punishment',$id)->count();
        if ($is_used ==0){
            $code = PunishmentInfo::find($id);
            $code->is_deleted = 1;
            $code->save();

            $count = DB::table('punishment_info')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count > 0) {
                return Redirect::to('admin/admissions/punish_code')->with('success', Lang::get('admin/admissions/messages.punish_code.delete_success'));
            }
        }else {
            // There was a problem deleting the user
            return Redirect::to('admin/admissions/punish_code')->withError(Lang::get('admin/admissions/messages.punish_code.delete_error'));
        }
    }

    public function getPunishCauseIndexForProvince()
    {
        $title = Lang::get('admin/admissions/table.define_punish_cause');
        return View::make('admin/admissions/punish_cause_index_province', compact('title'));
    }

    public function getPunishCauseForProvince()
    {

        $causes = DB::table('punishment_cause_info')
            ->where('is_deleted',0)
            ->select('id', 'punishment_cause', 'code');

        return Datatables::of($causes)
            ->add_column('action','
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/edit_punish_cause\')}}" class="iframe">{{Lang::get(\'general.edit\')}}</a> &nbsp;&nbsp;
                <a href="{{URL::to(\'admin/admissions/\'.$id.\'/delete_punish_cause\')}}" class="iframe">{{Lang::get(\'general.delete\')}}</a>
            ')
            ->remove_column('id')
            ->make();
    }

    public function getCreatePunishCauseProvince()
    {
        $title = Lang::get('admin/admissions/table.define_punish_cause');
        $mode = "create";
        return View::make('admin/admissions/define_punish_cause_province', compact('title','mode'));
    }
    public function postCreatePunishCauseProvince()
    {
        $punishment_cause = Input::get('punishment_cause');
        $code = Input::get('code');

        $count = PunishmentCause::where('punishment_cause',$punishment_cause)->where('code',$code)->count();

        if ($count==0) {
            $causes = new PunishmentCause();
            $causes->punishment_cause = $punishment_cause;
            $causes->code = $code;
            $causes->is_deleted = 0;
            $causes->created_at = new DateTime();
            $causes->updated_at = new DateTime();
            $causes->save();

            return Redirect::to('admin/admissions/punish_cause')->with('success', Lang::get('admin/admissions/messages.punish_cause.success'));
        }else{
            return Redirect::to('admin/admissions/punish_code')->withError(Lang::get('admin/admissions/messages.already_exists'));
        }
    }

    public function getEditPunishCauseProvince($id)
    {
        $title = Lang::get('admin/admissions/table.define_punish_cause');
        $mode = "edit";
        $cause = PunishmentCause::find($id)->first();
        return View::make('admin/admissions/define_punish_cause_province', compact('title','mode','cause'));
    }
    public function postEditPunishCauseProvince($id)
    {
        $punishment_cause = Input::get('punishment_cause');
        $code = Input::get('code');

        $codes = PunishmentCause::find($id);
        $codes->punishment_cause = $punishment_cause;
        $codes->code = $code;
        $codes->updated_at = new DateTime();
        $codes->save();

        return Redirect::to('admin/admissions/punish_cause')->with('success', Lang::get('admin/admissions/messages.punish_cause.success'));

    }

    public function getDeletePunishCauseProvince($id)
    {
        // Title
        $title = Lang::get('admin/admissions/table.delete_punish_cause');
        // Show the page
        return View::make('admin/admissions/delete_punish_cause_province', compact('id', 'title'));
    }

    public function postDeletePunishCauseProvince($id)
    {

        $is_used = DB::table('student_reward_punishment')->where('punishment_cause',$id)->count();
        if ($is_used == 0){
            $code = PunishmentCause::find($id);
            $code->is_deleted = 1;
            $code->save();

            $count = DB::table('punishment_cause_info')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count > 0) {
                return Redirect::to('admin/admissions/punish_cause')->with('success', Lang::get('admin/admissions/messages.punish_cause.delete_success'));
            }
        }else {
            // There was a problem deleting the user
            return Redirect::to('admin/admissions/punish_cause')->withError(Lang::get('admin/admissions/messages.punish_cause.delete_error'));
        }
    }

    public function getAdmissionsEditForProvince()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');

        // Show the page

        return View::make('admin/admissions/admissions_edit_information_province', compact('title'));
    }

    public function getDataAdmissionsEditForProvince()
    {
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');

        $politicalstatus = explode(',',Input::get('politicalstatus'));
        $ids = explode(',',Input::get('ids'));
        $maritalstatus =  explode(',',Input::get('maritalstatus'));
        $jiguan =  explode(',',Input::get('jiguan'));
        $hukou =  explode(',',Input::get('hukou'));
        $is_serving =  explode(',',Input::get('is_serving'));
        $distribution =  explode(',',Input::get('distribution'));
        $state = Input::get('state');
        $status =  array(4,6);
        if ($state==1){
            for ($i=1;$i<count($ids);$i++){
                DB::table('admissions')->where('id',$ids[$i])->update(array("politicalstatus"=>$politicalstatus[$i],
                    "maritalstatus" => $maritalstatus[$i],"jiguan"=>$jiguan[$i],"hukou"=>$hukou[$i],
                    "distribution"=>$distribution[$i],"is_serving"=>$is_serving[$i]));
            }
        }

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        $programs = DB::table('admissions')
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->whereIn('admissions.status',$status)
            ->where(function ($query) use ($filter) {
                if (!is_array($filter)) {
                    return $query;
                }
                foreach ($filter as $key => $value) {
                    $query = $query->where('admissions.' . $key, 'like', $value);
                }

                return $query;
            })
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'campuses.name', 'admissions.politicalstatus',
                'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou','admissions.distribution','admissions.is_serving','admissions.id as sid');

        return Datatables::of($programs)
            ->edit_column('politicalstatus', '<select id="politicalstatus" name="politicalstatus[]">
                                <option value="1" {{{ $politicalstatus == 1 ? \' selected="selected" \' : "" }}}>中共党员</option>
                                <option value="2" {{{ $politicalstatus == 2 ? \' selected="selected" \' : "" }}}>共青团员</option>
                                <option value="3" {{{ $politicalstatus == 3 ? \' selected="selected" \' : "" }}}>民革会员</option>
                                <option value="4" {{{ $politicalstatus == 4 ? \' selected="selected" \' : "" }}}>民盟盟员</option>
                                <option value="5" {{{ $politicalstatus == 5 ? \' selected="selected" \' : "" }}}>民进会员</option>
                                <option value="6" {{{ $politicalstatus == 6 ? \' selected="selected" \' : "" }}}>民建会员</option>
                                <option value="7" {{{ $politicalstatus == 7 ? \' selected="selected" \' : "" }}}>农工党党员</option>
                                <option value="8" {{{ $politicalstatus == 8 ? \' selected="selected" \' : "" }}}>致公党党员</option>
                                <option value="9" {{{ $politicalstatus == 9 ? \' selected="selected" \' : "" }}}>九三学社社员</option>
                                <option value="10" {{{ $politicalstatus == 10 ? \' selected="selected" \' : "" }}}>台盟盟员</option>
                                <option value="11" {{{ $politicalstatus == 11 ? \' selected="selected" \' : "" }}}>无党派民主人士</option>
                                <option value="12" {{{ $politicalstatus == 12 ? \' selected="selected" \' : "" }}}>群众</option>
                                <option value="13" {{{ $politicalstatus == 13 ? \' selected="selected" \' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('maritalstatus', '<select id="maritalstatus" name="maritalstatus[]">
                                <option value="0" {{{ $maritalstatus == 0 ? \' selected="selected"\' : "" }}}>未婚</option>
                                <option value="1" {{{ $maritalstatus == 1 ? \' selected="selected"\' : "" }}}>已婚</option>
                                <option value="2" {{{ $maritalstatus == 2 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                ')
            ->edit_column('jiguan', '<select id="jiguan" name="jiguan[]">
                                <option value="广东" {{{ $jiguan == \'广东\' ? \' selected="selected"\' : "" }}}>广东</option>
                                <option value="广西" {{{ $jiguan == \'广西\' ? \' selected="selected"\' : "" }}}>广西</option>
                                <option value="福建" {{{ $jiguan == \'福建\' ? \' selected="selected"\' : "" }}}>福建</option>
                            </select>
                       ')
            ->edit_column('hukou', '<select id="hukou" name="hukou[]">
                                <option value="0" {{{ $hukou == 0 ? \' selected="selected"\' : "" }}}>城镇户口</option>
                                <option value="1" {{{ $hukou == 1 ? \' selected="selected"\' : "" }}}>农村户口</option>
                                <option value="2" {{{ $hukou == 2 ? \' selected="selected"\' : "" }}}>外国公民</option>
                                <option value="3" {{{ $hukou == 3 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('distribution','
                              <select id="distribution" name="distribution[]">
                               <option value="">请选择</option>
                                <option value="0" {{{ $distribution == 0 ? \' selected="selected" \' : "" }}}>城镇应届</option>
                                <option value="1" {{{ $distribution == 1 ? \' selected="selected" \' : "" }}}>农村应届</option>
                                <option value="2" {{{ $distribution == 2 ? \' selected="selected" \' : "" }}}>城镇往届</option>
                                <option value="3" {{{ $distribution == 3 ? \' selected="selected" \' : "" }}}>农村往届</option>
                                <option value="4" {{{ $distribution == 4 ? \' selected="selected" \' : "" }}}>工人</option>
                                <option value="5" {{{ $distribution == 5 ? \' selected="selected" \' : "" }}}>干部</option>
                                <option value="6" {{{ $distribution == 6 ? \' selected="selected" \' : "" }}}>服役军人</option>
                                <option value="7" {{{ $distribution == 7 ? \' selected="selected" \' : "" }}}>台籍青年</option>
                                <option value="8" {{{ $distribution == 8 ? \' selected="selected" \' : "" }}}>港澳台侨</option>
                                <option value="9" {{{ $distribution == 9 ? \' selected="selected" \' : "" }}}>其他</option>
                            </select>
           ')
            ->edit_column('is_serving','
                            <select id="is_serving" name="is_serving[]">
                                <option value="0" {{{ $is_serving == 0 ? \' selected="selected"\' : "" }}}>不在职</option>
                                <option value="1" {{{ $is_serving == 1 ? \' selected="selected"\' : "" }}}>在职</option>
                            </select>
            ')
            ->edit_column('sid','<input type="hidden" name="ids[]" id="ids" value="{{$id}}">')
            ->make();
    }


    public function getAdmissionsInfoForProvince()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');
        // Show the page
        return View::make('admin/admissions/admissions_other_info_province', compact('title'));
    }

    public function getAdmissionsOtherInfoForProvince()
    {
        $title = Lang::get('admin/admissions/title.edit_admissions_other_info');
        $student_id = $_GET['student_id'];
        $student_name = $_GET['student_name'];
        $status = array(4,6);
        $filter = array();
        if (!empty($student_id)) {
            $filter["studentno"] = "%".$student_id."%";
        }
        if (!empty($student_name)) {
            $filter["fullname"] = "%".$student_name."%";
        }
        if(!empty($filter)){
            $admission = DB::table('admissions')
                ->whereIn('admissions.status',$status)
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();

            return View::make('admin/admissions/admissions_other_info_province_edit', compact('title','admission'));

        }else{
            return Redirect::to('admin/admissions/admissions_otherInfo')->withError(Lang::get('admin/admissions/messages.update.error'));
        }
    }

    public function postAdmissionsOtherInfoForProvince()
    {
        $student_id = trim(Input::get('student_id'));
        $admissions = Admission::find($student_id);
        $admissions->company_organization = Input::get('company_organization');
        $admissions->company_address = Input::get('company_address');
        $admissions->company_postcode = Input::get('company_postcode');
        $admissions->company_phone = Input::get('company_phone');
        $admissions->email = Input::get('email');
        $admissions->mobile = Input::get('mobile');
        $admissions->phone = Input::get('phone');
        $admissions->address = Input::get('address');
        $admissions->postcode = Input::get('postcode');
        $admissions->created_at = new DateTime();
        $admissions->updated_at = new DateTime();

        if ($admissions->save()) {
            return Redirect::to('admin/admissions/edit_admissions_otherInfo?student_id='.$admissions->student_no.'&student_name='.$admissions->fullname)->with('success', Lang::get('admin/admissions/messages.edit.success'));
        } else {
            // Redirect to the role page
            return Redirect::to('admin/admissions/edit_admissions_otherInfo?student_id='.$admissions->student_no.'&student_name='.$admissions->fullname)->withError(Lang::get('admin/admissions/messages.update.error'));
        }

    }




    public function getAdmissionsEditInfoForCampus()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');

        // Show the page

        return View::make('admin/admissions/admissions_edit_information_campus', compact('title'));
    }

    public function getDataAdmissionsEditInfoForCampus()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        $programs = DB::table('admissions')
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->where(function ($query) use ($filter) {
                if (!is_array($filter)) {
                    return $query;
                }
                foreach ($filter as $key => $value) {
                    $query = $query->where('admissions.' . $key, 'like', $value);
                }
                return $query;
            })
            ->where('admissions.campuscode',$campus->id)
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'campuses.name', 'admissions.politicalstatus',
                'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou','admissions.distribution','admissions.is_serving');

        return Datatables::of($programs)
            ->edit_column('politicalstatus', '<select id="politicalstatus[]" name="politicalstatus">
                                <option value="1" {{{ $politicalstatus == 1 ? \' selected="selected" \' : "" }}}>中共党员</option>
                                <option value="2" {{{ $politicalstatus == 2 ? \' selected="selected" \' : "" }}}>共青团员</option>
                                <option value="3" {{{ $politicalstatus == 3 ? \' selected="selected" \' : "" }}}>民革会员</option>
                                <option value="4" {{{ $politicalstatus == 4 ? \' selected="selected" \' : "" }}}>民盟盟员</option>
                                <option value="5" {{{ $politicalstatus == 5 ? \' selected="selected" \' : "" }}}>民进会员</option>
                                <option value="6" {{{ $politicalstatus == 6 ? \' selected="selected" \' : "" }}}>民建会员</option>
                                <option value="7" {{{ $politicalstatus == 7 ? \' selected="selected" \' : "" }}}>农工党党员</option>
                                <option value="8" {{{ $politicalstatus == 8 ? \' selected="selected" \' : "" }}}>致公党党员</option>
                                <option value="9" {{{ $politicalstatus == 9 ? \' selected="selected" \' : "" }}}>九三学社社员</option>
                                <option value="10" {{{ $politicalstatus == 10 ? \' selected="selected" \' : "" }}}>台盟盟员</option>
                                <option value="11" {{{ $politicalstatus == 11 ? \' selected="selected" \' : "" }}}>无党派民主人士</option>
                                <option value="12" {{{ $politicalstatus == 12 ? \' selected="selected" \' : "" }}}>群众</option>
                                <option value="13" {{{ $politicalstatus == 13 ? \' selected="selected" \' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('maritalstatus', '<select id="maritalstatus[]" name="maritalstatus">
                                <option value="0" {{{ $maritalstatus == 0 ? \' selected="selected"\' : "" }}}>未婚</option>
                                <option value="1" {{{ $maritalstatus == 1 ? \' selected="selected"\' : "" }}}>已婚</option>
                                <option value="2" {{{ $maritalstatus == 2 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                ')
            ->edit_column('jiguan', '<select id="jiguan[]" name="jiguan">
                                <option value="广东" {{{ $jiguan == 1 ? \' selected="selected"\' : "" }}}>广东</option>
                                <option value="广西" {{{ $jiguan == 2 ? \' selected="selected"\' : "" }}}>广西</option>
                                <option value="福建" {{{ $jiguan == 3 ? \' selected="selected"\' : "" }}}>福建</option>
                            </select>
                       ')
            ->edit_column('hukou', '<select id="hukou[]" name="hukou">
                                <option value="0" {{{ $hukou == 0 ? \' selected="selected"\' : "" }}}>城镇户口</option>
                                <option value="1" {{{ $hukou == 1 ? \' selected="selected"\' : "" }}}>农村户口</option>
                                <option value="2" {{{ $hukou == 2 ? \' selected="selected"\' : "" }}}>外国公民</option>
                                <option value="3" {{{ $hukou == 3 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('distribution','
                    <select id="$distribution[]" name="$distribution">
                                <option value="0" {{{ $distribution == 0 ? \' selected="selected"\' : "" }}}>城镇应届</option>
                                <option value="1" {{{ $distribution == 1 ? \' selected="selected"\' : "" }}}>农村应届</option>
                                <option value="2" {{{ $distribution == 2 ? \' selected="selected"\' : "" }}}>城镇往届</option>
                                <option value="3" {{{ $distribution == 3 ? \' selected="selected"\' : "" }}}>农村往届</option>
                                <option value="4" {{{ $distribution == 4 ? \' selected="selected"\' : "" }}}>工人</option>
                                <option value="5" {{{ $distribution == 5 ? \' selected="selected"\' : "" }}}>干部</option>
                                <option value="6" {{{ $distribution == 6 ? \' selected="selected"\' : "" }}}>服役军人</option>
                                <option value="7" {{{ $distribution == 7 ? \' selected="selected"\' : "" }}}>台籍青年</option>
                                <option value="8" {{{ $distribution == 8 ? \' selected="selected"\' : "" }}}>港澳台侨</option>
                                <option value="9" {{{ $distribution == 9 ? \' selected="selected"\' : "" }}}>其他</option>
                   </select>
                                 ')
            ->edit_column('is_serving', '<select id="is_serving[]" name="is_serving">
                                <option value="0" {{{ $is_serving == 0 ? \' selected="selected"\' : "" }}}>不在职</option>
                                <option value="1" {{{ $is_serving == 1 ? \' selected="selected"\' : "" }}}>在职</option>

                            </select>
                                 ')
            ->make();
    }

    public function getEditBasics()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');
        $id = trim($_GET['student_id']);
        $admission = DB::table('admissions')
            ->where('admissions.id', $id)
            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            })
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno',
                'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admission_details.dategraduated',
                'admissions.dateofbirth', 'admission_details.formerlevel', 'admission_details.formerschool', 'admission_details.attainmentcert',
                'admissions.nationgroup')
            ->first();
        //    var_dump($admissionss);
        // Show the page

        return View::make('admin/admissions/admissions_province_edit', compact('admission', 'title'));
    }

    public function postEditBasics()
    {
        $id = trim($_GET['student_id']);
        $admission = Admission::find($id);
        $fullname = Input::get('fullname');
        $gender = Input::get('gender');
        $idtype = Input::get('idtype');
        $idnumber = Input::get('idnumber');
        $dateofbirth = Input::get('dateofbirth');
        $nationgroup = Input::get('nationgroup');
        $formerlevel = Input::get('formerlevel');
        $formerschool = Input::get('formerschool');
        $dategraduated = Input::get('dategraduated');
        $attainmentcert = Input::get('attainmentcert');

        $admission_id = DB::table('admission_details')->select('id')->where('admission_id', $id)->lists('id');

        $admission_detail = AdmissionDetails::find($admission_id[0]);

        $flag = 0;
        $flag_detail = 0;
        $flag_save = 0;
        if (!empty($fullname)) {
            $admission->fullname = $fullname;
            $flag = 1;
        }
        if (!empty($gender)) {
            $admission->gender = $gender;
            $flag = 1;
        }
        if (!empty($idtype)) {
            $admission->idtype = $idtype;
            $flag = 1;
        }
        if (!empty($idtype) && !empty($idnumber)) {
            $admission->idnumber = $idnumber;
            $flag = 1;
        }
        if (!empty($dateofbirth)) {
            $admission->dateofbirth = $dateofbirth;
            $flag = 1;
        }
        if (!empty($nationgroup)) {
            $admission->nationgroup = substr($nationgroup, 0, 2);
            $flag = 1;
        }
        if (!empty($formerlevel)) {
            $admission_detail->formerlevel = $formerlevel;
            $flag_detail = 1;
        }
        if (!empty($formerschool)) {
            $admission_detail->formerschool = $formerschool;
            $flag_detail = 1;
        }
        if (!empty($dategraduated)) {
            $admission_detail->dategraduated = $dategraduated;
            $flag_detail = 1;
        }
        if (!empty($attainmentcert)) {
            $admission_detail->attainmentcert = $attainmentcert;
            $flag_detail = 1;
        }

        if ($flag == 1) {
            $admission->save();
            $flag = 2;
        }
        if ($flag_detail == 1) {
            $admission_detail->save();
            $flag_detail = 2;
        }
        if (($flag == 2) || ($flag_detail == 2)){
            return Redirect::to("admin/admissions/edit_admissions_province?student_id=".$id)->with('success',Lang::get('admin/admissions/messages.edit.success'));
        }else{
            return Redirect::to("admin/admissions/edit_admissions_province?student_id=".$id);
        }
    }

    public function getAdmissionsInfo()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');
        // Show the page
        return View::make('admin/admissions/admissions_other_info_campus', compact('title'));
    }

    public function getAdmissionsOtherInfo()
    {
        $filter = array();
        $title = Lang::get('admin/admissions/title.edit_admissions_other_info');
        $student_id = $_GET['student_id'];
        $student_name = $_GET['student_name'];
        if (!empty($student_id)){
            $filter["studentno"] = $student_id;
        }
        if (!empty($student_name)){
            $filter["fullname"] = $student_name;
        }

        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();

        if (!empty($student_id)) {
            $admission = DB::table('admissions')
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->where('admissions.status',4)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();

            return View::make('admin/admissions/admissions_other_info_campus_edit', compact('title', 'admission'));

        }else{
            return Redirect::to('admin/admissions/admissions_other_info')->withError(Lang::get('admin/admissions/messages.update.error'));
        }
    }


    public function postAdmissionsOtherInfo()
    {
        $student_id = trim(Input::get('student_id'));
        $admissions = Admission::find($student_id);
        $admissions->company_organization = Input::get('company_organization');
        $admissions->company_address = Input::get('company_address');
        $admissions->company_postcode = Input::get('company_postcode');
        $admissions->company_phone = Input::get('company_phone');
        $admissions->email = Input::get('email');
        $admissions->mobile = Input::get('mobile');
        $admissions->phone = Input::get('phone');
        $admissions->address = Input::get('address');
        $admissions->postcode = Input::get('postcode');
        $admissions->created_at = new DateTime();
        $admissions->updated_at = new DateTime();

        $admissions->save();
        return Redirect::to('admin/admissions/edit_admissions_other_info?student_id='.$admissions->studentno.'&student_name='.$admissions->fullname)->with('success', Lang::get('admin/admissions/messages.edit.success'));
    }


    public function getDataOtherInfo(){
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $groups = Groups::where('groups.name', '<>', 'null')
            ->leftjoin('programs', 'programs.id', '=', 'groups.programs_id')
            ->where('programs.campus_id', '=', $campus->id)
            ->select('groups.id as gid','groups.name as gname')
            ->get();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $state = Input::get('state');
        if (!empty($student_id)) {
            $admissions = DB::table('admissions')
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('programs', function ($join) {
                    $join->on('rawprograms.id', '=', 'programs.name');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->where('admissions.id', $student_id)
                ->where('programs.campus_id', '=', $campus->id)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->get();
        } elseif (!empty($student_name)) {
            $admissions = DB::table('admissions')
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('programs', function ($join) {
                    $join->on('rawprograms.id', '=', 'programs.name');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->where('admissions.fullname', $student_name)
                ->where('programs.campus_id', '=', $campus->id)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->get();
        } elseif (!empty($student_id) && !empty($student_name)) {
            $admissions = DB::table('admissions')
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->where('admissions.studentno', $student_id)
                ->where('admissions.fullname', $student_name)
                ->where('programs.campus_id', '=', $campus->id)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->get();
        }
        $select[] = array("id"=>'',"name"=>'请选择');
        foreach ($groups as $group){
            $select[] = array("id"=>$group->gid,"name"=>$group->gname);
        }
        echo json_encode($select);
    }

    public function getSchoolIndex()
    {
        // Title
        $title = Lang::get('admin/admissions/table.define_school');

        // Show the page

        return View::make('admin/admissions/school_index', compact('title'));
    }

    public function getDataForSchool()
    {

        $programs = DB::table('school_info')
            ->where('is_deleted', 0)
            ->select('id', 'school_name', 'school_id');

        return Datatables::of($programs)
            ->add_column('actions', '
                                <a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/school_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/school_delete\' ) }}}" class="iframe" > {{{ Lang::get(\'button.delete\') }}}</a>
                                <a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/school_add_campus\' ) }}}"> {{{ Lang::get(\'admin/admissions/table.create_campus\') }}}</a>
                                ')
            ->remove_column('id')
            ->make();
    }


    public function getCreateSchool()
    {
        // Title
        $title = Lang::get('admin/admissions/table.define_school');
        $mode = 'create';
        // Show the page

        return View::make('admin/admissions/school_define_edit', compact('mode', 'title'));
    }


    public function postCreateSchool()
    {

        $recordexists = DB::table('school_info')
            ->where('school_id', Input::get('school_id'))
            ->where('school_name', Input::get('school_name'))
            ->count();

        if ($recordexists > 0) {

            return Redirect::to('admin/admissions/define_school')->withErrors('error', Lang::get('admin/admissions/messages.already_exists'));
        }else{
            $schools = new School();
            $schools->school_id = Input::get('school_id');
            $schools->school_name = Input::get('school_name');
            $schools->save();
            return Redirect::to('admin/admissions/define_school')->with('success', Lang::get('admin/admissions/messages.school.create'));
        }


    }


    public function getEditSchool($id)
    {
        $title = Lang::get('admin/admissions/table.school_edit');

        // Mode
        $mode = 'edit';

        $schools = School::find($id);
        if (isset ($schools) && $schools->id) {

            //prepare available 專業 list

            // Show the page
            return View::make('admin/admissions/school_define_edit', compact('title', 'mode', 'schools'));
        } else {
            return View::make('admin/admissions/school_index', compact('title', 'mode'))->withError('School ID not found');

        }
    }

    public function postEditSchool($id)
    {

        $School = School::find($id);
        if (isset ($School)) {
            $School->school_id = trim(Input::get('school_id'));
            $School->school_name = trim(Input::get('school_name'));
            $School->save();
            return Redirect::to('admin/admissions/' . $id . '/school_edit')->withInput()->with('success', Lang::get('admin/admissions/messages.edit.success'));
        }

    }

    public function getDeleteSchool($id)
    {
        // Title
        $title = Lang::get('admin/admissions/title.school_delete');

        // Show the page
        return View::make('admin/admissions/school_delete', compact('id', 'title'));
    }

    public function postDeleteSchool($id)
    {

        // Was the comment post deleted?
        $School = School::find($id);
        $School->is_deleted = 1;
        $School->save();

        $count = DB::table('school')
            ->where('id', $id)
            ->where('is_deleted', 1)
            ->count();

        if ($count > 0) {
            return Redirect::to('admin/admissions/school')->with('success', Lang::get('admin/admissions/messages.delete.success'));
        } else {
            // There was a problem deleting the user
            return Redirect::to('admin/admissions/school')->with('error', Lang::get('admin/admissions/messages.delete.error'));
        }
    }

    public function getSchoolAddCampus($id)
    {
        // Title
        $title = Lang::get('admin/admissions/table.create_campus');
        // Show the page
        $school = DB::table('school_info')->where('id',$id)->select('school_name')->first();
        return View::make('admin/admissions/school_add_campus', compact('id', 'school','title'));
    }

    public function getDataForSchoolCampus()
    {
        $id = Input::get('id');
        $campuses = DB::table('campuses') ->select('id', 'sysid', 'name');
        return Datatables::of($campuses)
            ->add_column('actions', '
                                <input type="checkbox" id="checkItem" name="checkItem[]" value="{{$id}}"
                                 @if (in_array($id,($campuses = CampusesSchool::select(\'campus_id as id\')->lists(\'id\'))))
                                            checked="checked"
                                 @endif
                                >
                                 ')
            ->remove_column('id')
            ->make();
    }

    public function postSchoolAddCampus($id)
    {
        $ids = explode(',',Input::get('selectedCampuses'));
        $unselect_ids = explode(',',Input::get('unselectedCampuses'));


        if (!empty($unselect_ids)) {
            DB::table('campuses_school')->whereIn('campus_id',$unselect_ids)->where('school_id',$id)->delete();
        }
        if (!empty($ids)) {
            for ($i = 0; $i < count($ids); $i++) {
                $count = DB::table('campuses_school')
                    ->where('school_id', $id)
                    ->where('campus_id', $ids[$i])
                    ->count();
                if ($count == 0 && $ids[$i] !=0) {
                    $schoolcampus = new CampusesSchool();
                    $schoolcampus->campus_id = $ids[$i];
                    $schoolcampus->school_id = $id;
                    $schoolcampus->save();
                }
            }
            return Redirect::to('admin/admissions/'.$id.'/school_add_campus')->with('success', Lang::get('admin/admissions/messages.campus.create'));
        }else{
            return Redirect::to('admin/admissions/'.$id.'/school_add_campus');
        }



    }


    public function getChangeAdmissionsIndexForCampus()
    {
        $title = Lang::get('admin/admissions/title.change_admissions');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $groups = Groups::All();
        $rawprograms = RawProgram::All();
        return View::make('admin/admissions/admissions_change_campus', compact('rawprograms','campus', 'groups', 'title'));
    }

    public function getDataChangeAdmissionsForCampus()
    {
        $user = Auth::user();
        if (!empty($user->id)) {
            $campus = DB::table('campuses')
                ->where('userID', $user->id)
                ->first();
        }

        $filter = array();
        $filter_group = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $group = Input::get('group_name');
        $year = Input::get('create_group_year');
        $flag = 0;

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($group)) {
            $filter_group['id'] = $group;
        }
        if (!empty($year)) {
            $filter_group['year'] = $year;
        }

        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }


        if (!empty($filter_group)) {
            $group_ids = DB::table('groups')->select('id')
                ->where(function ($query) use ($filter_group) {
                    if (!is_array($filter_group)) {
                        return $query;
                    }
                    foreach ($filter_group as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $student_ids = DB::table('admission_group')->select('admission_id as id')
                ->whereIn('group_id', $group_ids)
                ->lists('id');
            $flag = 1;

        }

        if ($flag == 1) {
            $programs = DB::table('admissions')->whereIn('admissions.id', $student_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->where('admissions.status',4)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'groups.sysid',
                    'groups.name as group_name', 'rawprograms.name as major_name',  'rawprograms.type', 'admissions.status',
                    'campuses.name as campus_name', 'admissions.admissionyear', 'admissions.admissionsemester');

        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->where('admissions.status',4)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'groups.sysid',
                    'groups.name as group_name', 'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status',
                    'campuses.name as campus_name', 'admissions.admissionyear', 'admissions.admissionsemester');
        }

        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('admissionsemester', '@if ($admissionsemester == 0)
                                      秋季
                                 @elseif ($admissionsemester == 1)
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
            ->add_column('actions', '<a href="{{ URL::to(\'admin/admissions/\' . $id .\'/admissions_change_submit\')}}"><lable id="btnSubmit">{{Lang::get(\'admin/admissions/table.application\')}}</lable></a>')
            ->remove_column('id')
            ->make();

    }

    public function getAdmissionChangeSubmit($id)
    {
        $title = Lang::get('admin/admissions/title.change_admissions');
        $campuses = Campus::All();
        $rawprograms = RawProgram::All();
        $current_year_semester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        $admission = DB::table('admissions')
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->leftjoin('admission_details', function ($join) {
                $join->on('admission_details.admission_id', '=', 'admissions.id');
            })
            ->leftjoin('admission_group', function ($join) {
                $join->on('admission_group.admission_id', '=', 'admissions.id');
            })
            ->where('admissions.id', $id)
            ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admission_details.attainment',
                'campuses.name as campus_name', 'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                'admissions.dateofbirth', 'admission_details.formerlevel', 'rawprograms.name as rname', 'admissions.hukou', 'admissions.program',
                'admissions.nationgroup', 'admissions.admissionyear', 'admissions.admissionsemester','admissions.programcode','admission_group.group_id','admissions.campuscode')
            ->first();

        return View::make('admin/admissions/admissions_change_submit', compact('id','rawprograms', 'campuses','current_year_semester', 'admission', 'title'));
    }

    public function postAdmissionChangeSubmit($id){

        $cause = Input::get('cause');
        $major = intval(Input::get('major'));
        $campus = intval(Input::get('campus'));
        $original_major_id = intval(Input::get('original_major_id'));
        $original_campus_id = intval(Input::get('original_campus_id'));
        $original_class_id = intval(Input::get('original_class_id'));
        $flag = 0;
        $flag_save= 0;
        $admissionyear = Admission::where('id',$id)
            ->select('admissionyear as year','admissionsemester as semester')
            ->first();
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year as year','current_semester as semester')
            ->first();
        if (($admissionyear->year ==$yearsemester->year) && ($admissionyear->semester ==$yearsemester->semester))
        {
            $flag = 1;
            return Redirect::to('admin/admissions/admissions_change_campus')->withError(Lang::get('admin/admissions/messages.changing.error_year'));
        }
        $recordexists = AdmissionChanging::where('student_id',$id)
            ->where('approval_status',0)
            ->count();
        if ($recordexists>0)
        {
            $flag = 1;
            return Redirect::to('admin/admissions/admissions_change_campus')->withError(Lang::get('admin/admissions/messages.changing.error_alreadyexist'));
        }
        $selection = StudentSelection::where('student_id',$id)
            ->where('year',$yearsemester->year)
            ->where('semester',$yearsemester->semester)
            ->count();
        if ($selection>0)
        {
            $flag = 1;
            return Redirect::to('admin/admissions/admissions_change_campus')->withError(Lang::get('admin/admissions/messages.changing.error_selection'));
        }

        if ($flag ==0){
            $admission_changing = new AdmissionChanging();
            $admission_changing->cause = $cause;
            $admission_changing->student_id = $id;
            $admission_changing->application_year = $yearsemester->year;
            $admission_changing->application_semester = $yearsemester->semester;
            $admission_changing->original_major_id = $original_major_id;
            $admission_changing->current_major_id = $major;
            $admission_changing->original_campus_id = $original_campus_id;
            $admission_changing->current_campus_id = $campus;
            $admission_changing->original_class_id = $original_class_id;
            $admission_changing->original_major_id = $cause;
            $admission_changing->current_class_id = '';
            $admission_changing->approval_status = 0;
            $admission_changing->is_deleted = 0;
            $admission_changing->created_at = new DateTime();
            $admission_changing->updated_at = new DateTime();
            $admission_changing->save();
            $admission = Admission::find($id);
            $admission->status = 5;
            $admission->save();
            $flag_save=1;
        }

        if ($flag_save == 1) {
            return Redirect::to('admin/admissions/admissions_change_campus')->with('success', Lang::get('admin/admissions/messages.changing.success'));
        } else {
            // Redirect to the role page
            return Redirect::to('admin/admissions/admissions_change_campus')->withError(Lang::get('admin/admissions/messages.update.error'));
        }
    }

    public function getAdmissionsState()
    {
        $title = Lang::get('admin/admissions/title.admin_admissions_state');
        $rawprograms = RawProgram::All();
        return View::make('admin/admissions/admissions_state_campus', compact('rawprograms', 'title'));
    }

    public function getDataForAdmissionsState()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $status = explode(',',Input::get('status'));
        $ids = explode(',',Input::get('ids'));
        $state = Input::get('state');
        if ($state==1){
            for ($i=1;$i<count($status);$i++){
                DB::table('admissions')->where('id',$ids[$i])->update(array('status'=>$status[$i]));
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
        $filter['campuscode'] = $campus->id;
        $states = array(3,4,5,6);
        $programs = DB::table('admissions')
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('admission_group', function ($join) {
                $join->on('admission_group.admission_id', '=', 'admissions.id');
            })
            ->leftjoin('groups', function ($join) {
                $join->on('admission_group.group_id', '=', 'groups.id');
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
            ->whereIn('admissions.status',$states)
            ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'groups.sysid',
                'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status','admissions.id as sid');

        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('status', '@if ($status == 3)
                                     <select id="status" name="status[]">
                                       <option value="3"  selected="selected">未注册</option>
                                       <option value="4" >在籍</option>
                                     </select>
                                 @elseif ($status == 4)
                                     <select id="status" name="status[]">
                                       <option value="3">未注册</option>
                                       <option value="4" selected="selected" >在籍</option>
                                     </select>
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @endif')
            ->edit_column('sid','<input type="hidden" id="ids" name="ids[]" value="{{$id}}">')
            ->make();

    }

    public function getAdmissionsEditForCampus()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');

        $rawprograms = RawProgram::All();

        // Show the page
        return View::make('admin/admissions/admissions_campus', compact('rawprograms', 'title'));
    }

    public function getDataAdmissionsEditForCampus()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $politicalstatus = explode(',',Input::get('politicalstatus'));
        $ids = explode(',',Input::get('ids'));
        $maritalstatus =  explode(',',Input::get('maritalstatus'));
        $jiguan =  explode(',',Input::get('jiguan'));
        $hukou =  explode(',',Input::get('hukou'));
        $is_serving =  explode(',',Input::get('is_serving'));
        $distribution =  explode(',',Input::get('distribution'));
        $state = Input::get('state');

        if ($state==1){
            for ($i=1;$i<count($ids);$i++){
                DB::table('admissions')->where('id',$ids[$i])->update(array("politicalstatus"=>$politicalstatus[$i],
                    "maritalstatus" => $maritalstatus[$i],"jiguan"=>$jiguan[$i],"hukou"=>$hukou[$i],
                    "distribution"=>$distribution[$i],"is_serving"=>$is_serving[$i]));
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

        $programs = DB::table('admissions')
            ->leftjoin('admission_group', function ($join) {
                $join->on('admission_group.admission_id', '=', 'admissions.id');
            })
            ->leftjoin('groups', function ($join) {
                $join->on('admission_group.group_id', '=', 'groups.id');
            })
            ->where(function ($query) use ($filter) {
                if (!is_array($filter)) {
                    return $query;
                }
                foreach ($filter as $key => $value) {
                    $query = $query->where('admissions.' . $key, 'like', $value);
                }
                return $query;
            })
            ->where('admissions.campuscode',$campus->id)
            ->where('admissions.status',4)
            ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admissions.idtype',
                'admissions.idnumber', 'groups.sysid', 'admissions.politicalstatus',
                'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou', 'admissions.distribution', 'admissions.is_serving','admissions.id as sid');

        return Datatables::of($programs)
            ->edit_column('politicalstatus', '<select id="politicalstatus" name="politicalstatus[]" style="width:110px">
                                <option value="1" {{{ $politicalstatus == 1 ? \' selected="selected" \' : "" }}}>中共党员</option>
                                <option value="2" {{{ $politicalstatus == 2 ? \' selected="selected" \' : "" }}}>共青团员</option>
                                <option value="3" {{{ $politicalstatus == 3 ? \' selected="selected" \' : "" }}}>民革会员</option>
                                <option value="4" {{{ $politicalstatus == 4 ? \' selected="selected" \' : "" }}}>民盟盟员</option>
                                <option value="5" {{{ $politicalstatus == 5 ? \' selected="selected" \' : "" }}}>民进会员</option>
                                <option value="6" {{{ $politicalstatus == 6 ? \' selected="selected" \' : "" }}}>民建会员</option>
                                <option value="7" {{{ $politicalstatus == 7 ? \' selected="selected" \' : "" }}}>农工党党员</option>
                                <option value="8" {{{ $politicalstatus == 8 ? \' selected="selected" \' : "" }}}>致公党党员</option>
                                <option value="9" {{{ $politicalstatus == 9 ? \' selected="selected" \' : "" }}}>九三学社社员</option>
                                <option value="10" {{{ $politicalstatus == 10 ? \' selected="selected" \' : "" }}}>台盟盟员</option>
                                <option value="11" {{{ $politicalstatus == 11 ? \' selected="selected" \' : "" }}}>无党派民主人士</option>
                                <option value="12" {{{ $politicalstatus == 12 ? \' selected="selected" \' : "" }}}>群众</option>
                                <option value="13" {{{ $politicalstatus == 13 ? \' selected="selected" \' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('maritalstatus', '<select id="maritalstatus" name="maritalstatus[]">
                                <option value="0" {{{ $maritalstatus == 0 ? \' selected="selected"\' : "" }}}>未婚</option>
                                <option value="1" {{{ $maritalstatus == 1 ? \' selected="selected"\' : "" }}}>已婚</option>
                                <option value="2" {{{ $maritalstatus == 2 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                ')
            ->edit_column('jiguan', '<select id="jiguan" name="jiguan[]">
                                <option value="广东" {{{ $jiguan == \'广东\' ? \' selected="selected"\' : "" }}}>广东</option>
                                <option value="广西" {{{ $jiguan == \'广西\' ? \' selected="selected"\' : "" }}}>广西</option>
                                <option value="福建" {{{ $jiguan == \'福建\' ? \' selected="selected"\' : "" }}}>福建</option>
                            </select>
                       ')
            ->edit_column('hukou', '<select id="hukou" name="hukou[]">
                                <option value="0" {{{ $hukou == 0 ? \' selected="selected"\' : "" }}}>城镇户口</option>
                                <option value="1" {{{ $hukou == 1 ? \' selected="selected"\' : "" }}}>农村户口</option>
                                <option value="2" {{{ $hukou == 2 ? \' selected="selected"\' : "" }}}>外国公民</option>
                                <option value="3" {{{ $hukou == 3 ? \' selected="selected"\' : "" }}}>其他</option>
                            </select>
                                 ')
            ->edit_column('idtype', '@if ($idtype == 1)
                                    身份证
                                @elseif ($idtype == 2)
                                    军官证
                                @elseif ($idtype == 3)
                                    护照
                                @elseif ($idtype == 4)
                                    港澳居民证件
                                @elseif ($idtype == 5)
                                    其他
                                @endif

            ')
            ->edit_column('distribution','
                              <select id="distribution" name="distribution[]">
                               <option value="">请选择</option>
                                <option value="0" {{{ $distribution == 0 ? \' selected="selected" \' : "" }}}>城镇应届</option>
                                <option value="1" {{{ $distribution == 1 ? \' selected="selected" \' : "" }}}>农村应届</option>
                                <option value="2" {{{ $distribution == 2 ? \' selected="selected" \' : "" }}}>城镇往届</option>
                                <option value="3" {{{ $distribution == 3 ? \' selected="selected" \' : "" }}}>农村往届</option>
                                <option value="4" {{{ $distribution == 4 ? \' selected="selected" \' : "" }}}>工人</option>
                                <option value="5" {{{ $distribution == 5 ? \' selected="selected" \' : "" }}}>干部</option>
                                <option value="6" {{{ $distribution == 6 ? \' selected="selected" \' : "" }}}>服役军人</option>
                                <option value="7" {{{ $distribution == 7 ? \' selected="selected" \' : "" }}}>台籍青年</option>
                                <option value="8" {{{ $distribution == 8 ? \' selected="selected" \' : "" }}}>港澳台侨</option>
                                <option value="9" {{{ $distribution == 9 ? \' selected="selected" \' : "" }}}>其他</option>
                            </select>
           ')
            ->edit_column('is_serving','
                            <select id="is_serving" name="is_serving[]">
                                <option value="0" {{{ $is_serving == 0 ? \' selected="selected"\' : "" }}}>不在职</option>
                                <option value="1" {{{ $is_serving == 1 ? \' selected="selected"\' : "" }}}>在职</option>
                            </select>
            ')
            ->edit_column('sid','<input type="hidden" name="ids[]" id="ids" value="{{$sid}}">')
            ->make();
    }

    public function getIndexForApplicationRecovery()
    {
        $title = Lang::get('admin/admissions/title.application_recovery_admissions');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $rawprograms = DB::table('rawprograms')
            ->join('programs','programs.name','=','rawprograms.id')
            ->where('programs.status',1)
            ->where('programs.campus_id',$campus->id)
            ->select('rawprograms.id','rawprograms.name')
            ->get();
        return View::make('admin/admissions/application_recovery_admissions', compact('rawprograms', 'groups', 'title'));
    }

    public function getDataForApplicationRecovery()
    {

        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $student_type = Input::get('student_type');
        $year = Input::get('year');
        $semester = Input::get('semester');

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($student_type)) {
            $filter['enrollmenttype'] = $student_type;
        }
        if (!empty($year)) {
            $filter["admissionyear"] = $year;
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }
        if (!empty($semester)) {
            $filter['semester'] = $semester;
        }


        if (!empty($filter)) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('graduation_info', function ($join) {
                    $join->on('admissions.id', '=', 'graduation_info.student_id');
                })
                ->leftjoin('programs', function ($join) {
                    $join->on('programs.name', '=', 'rawprograms.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.status',6)
                ->where('programs.campus_id',$campus->id)
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'admissions.program',
                    'rawprograms.type', 'rawprograms.name as major_name', 'admissions.admissionyear', 'admissions.admissionsemester',
                    'admissions.status','graduation_info.is_reported');

        } else{
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('graduation_info', function ($join) {
                    $join->on('admissions.id', '=', 'graduation_info.student_id');
                })
                ->leftjoin('programs', function ($join) {
                    $join->on('programs.name', '=', 'rawprograms.id');
                })
                ->where('admissions.status',6)
                ->where('programs.campus_id',$campus->id)
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'admissions.program',
                    'rawprograms.type', 'rawprograms.name as major_name', 'admissions.admissionyear', 'admissions.admissionsemester',
                    'admissions.status','graduation_info.is_reported');
        }

        return Datatables::of($programs)
            ->edit_column('program', '@if ($program == 12)
                                          本科
                                 @elseif ($program == 14)
                                          专科
                                 @endif')
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('admissionsemester', '@if ($admissionsemester == \'01\')
                                      春季
                                 @elseif ($admissionsemester == \'02\')
                                      秋季
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
            ->add_column('actions', '@if ($is_reported == 0)
                            <a href="{{ URL::to(\'admin/admissions/admissions_recovery_submit\')}}?id={{$id}}" target="recovery">申请</a>
                                    @else
                                    毕业信息已经上报学信息网
                                    @endif
                            ')
            ->remove_column('is_reported')
            ->make();

    }

    public function getApplicationRecovery()
    {
        $title = Lang::get('admin/admissions/title.application_recovery_admissions');
        $id=$_GET["id"];
        $admission = DB::table('admissions')
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('programs', function ($join) {
                $join->on('programs.name', '=', 'rawprograms.id');
            })
            ->where('admissions.id',$id)
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'admissions.program',
                'rawprograms.type', 'rawprograms.name as major_name', 'admissions.admissionyear', 'admissions.admissionsemester',
                'admissions.status')
            ->first();

        $admission_recovery = DB::table('student_recovery')
            ->join('admissions','admissions.id','=','student_recovery.student_id')
            ->where('student_recovery.student_id',$id)
            ->select(
                array(
                    'admissions.studentno',
                    'student_recovery.recovery_year',
                    'student_recovery.recovery_semester',
                    'student_recovery.approval_result',
                    'student_recovery.remark',
                    DB::raw('count(*) as count')
                )
            )
            ->first();
        return View::make('admin/admissions/application_recovery_submit', compact('admission','admission_recovery','id','title'));
    }

    public function postApplicationRecovery()
    {
        $id=Input::get("id");
        $year = Input::get('recovery_year');
        $semester = Input::get('recovery_semester');
        $status = Input::get('status');

        $count = AdmissionRecovery::where('student_id',$id)->count();
        if($count == 0){
            $admission_recovery = new AdmissionRecovery();
            $admission_recovery->recovery_year = $year;
            $admission_recovery->recovery_semester = $semester;
            $admission_recovery->student_id = $id;
            $admission_recovery->approval_result = 0;
            $admission_recovery->is_deleted = 0;
            $admission_recovery->created_at = new DateTime();
            $admission_recovery->updated_at = new DateTime();
            if ($admission_recovery->save()) {
                return Redirect::to('admin/admissions/application_recovery')->with('success', Lang::get('admin/admissions/messages.recovery.success'));
            } else {
                // Redirect to the role page
                return Redirect::to('admin/admissions/application_recovery')->withError(Lang::get('admin/admissions/messages.recovery.error'));
            }
        }else{
            $recovery = DB::table('student_recovery')->where('student_id',$id)->first();
            if ($recovery->approval_result == 0) {
                $admission_recovery = AdmissionRecovery::find($recovery->id);
                $admission_recovery->recovery_year = $year;
                $admission_recovery->recovery_semester = $semester;
                $admission_recovery->student_id = $id;
                $admission_recovery->approval_result = 0;
                $admission_recovery->is_deleted = 0;
                $admission_recovery->created_at = new DateTime();
                $admission_recovery->updated_at = new DateTime();
                $admission_recovery->save();
                return Redirect::to('admin/admissions/admissions_recovery_submit?id='.$id)->with('success', Lang::get('admin/admissions/messages.recovery.success'));
            }
        }
    }



    public function getIndexForApplicationDropOut()
    {
        $title = Lang::get('admin/admissions/title.application_dropout_admissions');

        // Show the page
        return View::make('admin/admissions/admissions_application_dropout', compact('title'));
    }

    public function getApplicationDropOut()
    {
        // Title
        $title = Lang::get('admin/admissions/title.application_dropout_admissions');
        $mode = 'create';
        $campus = DB::table('campuses')->where('userID', Auth::user()->id)->first();
        $id = trim($_GET['student_id']);
        $yearsemester = DB::table('module_current')
            ->where('module_id', 4)
            ->select('current_year', 'current_semester')
            ->first();
        if (!empty($id)) {
            $record_exists = DB::table('student_dropout')
                ->leftjoin('admissions', function ($join) {
                    $join->on('student_dropout.student_id', '=', 'admissions.id');
                })
                ->where('admissions.studentno', $id)
                ->count();
            if ($record_exists == 0) {
                $admission = DB::table('admissions')
                    ->where('admissions.studentno', $id)
                    ->leftjoin('admission_details', function ($join) {
                        $join->on('admission_details.admission_id', '=', 'admissions.id');
                    })
                    ->leftjoin('rawprograms', function ($join) {
                        $join->on('rawprograms.id', '=', 'admissions.programcode');
                    })
                    ->leftjoin('campuses', function ($join) {
                        $join->on('campuses.id', '=', 'admissions.campuscode');
                    })
                    ->where('admissions.campuscode', $campus->id)
                    ->where('admissions.status', 4)
                    ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno',
                        'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                        'admissions.hukou', 'admissions.program', 'admissions.dateofbirth', 'admission_details.formerlevel',
                        'rawprograms.name as major_name', 'campuses.name as campus_name', 'admissions.nationgroup')
                    ->first();

                return View::make('admin/admissions/admissions_application_dropout_create', compact('admission', 'yearsemester', 'title', 'mode'));
            } else {
                echo "<table class='table table-striped table-hover' align='center'>
           <tr><td align='center'>" . Lang::get('admin/admissions/messages.dropout.already_exists') . "</td></tr> </table>";
            }
        }
    }

    public function postApplicationDropOut()
    {
        //    $title = Lang::get('admin/admissions/title.application_dropout_admissions');
        $id = Input::get('student_id');
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        $cause = Input::get('cause');

        $record_exists = DB::table('student_dropout')
            ->where('student_id',$id)
            ->count();
        if ($record_exists>0){
            echo "<div class='bg-danger bg-warning' align='center'><label>".Lang::get('admin/admissions/messages.dropout.already_exists')."</label></div>";
        }else{
            $dropout = new AdmissionDropOut();
            $dropout->application_year = $yearsemester->current_year;
            $dropout->application_semester = $yearsemester->current_semester;
            $dropout->student_id = $id;
            $dropout->cause = $cause;
            $dropout->approval_result_province = 0;
            $dropout->is_deleted = 0;
            $dropout->created_at = new DateTime() ;
            $dropout->updated_at = new DateTime() ;
            $dropout->save();
            return Redirect::to('admin/admissions/edit_dropout?student_id='.$id)->with('success',Lang::get('admin/admissions/messages.dropout.success'));
        }
    }

    public function getEditIndexApplicationDropOut()
    {
        $title = Lang::get('admin/admissions/title.edit_dropout_application');
        return View::make('admin/admissions/edit_application_dropout', compact( 'title'));
    }

    public function getEditDropOutAdmissionInfo()
    {
        $id = trim($_REQUEST['student_id']);
        return View::make('admin/admissions/admissions_application_dropout_edit',compact('id'));
    }

    public function getDataDropOut()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $id = trim(Input::get("student_id"));
        $dropout = DB::table('student_dropout')
            ->leftjoin('admissions', function ($join) {
                $join->on('admissions.id', '=', 'student_dropout.student_id');
            })
            ->where('admissions.studentno', $id)
            ->where('admissions.campuscode', $campus->id)
            ->where('student_dropout.is_deleted', 0)
            ->where('student_dropout.approval_result_province', 0)
            ->select('admissions.id', 'student_dropout.application_year',
                'student_dropout.application_semester','admissions.fullname',
                'admissions.studentno', 'student_dropout.cause');
        return Datatables::of($dropout)
            ->edit_column('application_semester', '@if ($application_semester == \'02\')
                                          秋季
                                 @elseif ($application_semester == \'01\')
                                          春季
                                 @endif')

            ->add_column('actions', '
                      <a href="{{URL::to(\'admin/admissions/edit_dropout?student_id=\'.$id)}}" target="detail_info"  ><label id="btnEdit" onmouseover="this.style.cursor=\'hand\'">修改</label></a>&nbsp;&nbsp;
                        <a href="{{URL::to(\'admin/admissions/delete_dropout?student_id=\'.$id)}}" class="iframe" target="dropout"><label id="btnDelete">删除</label></a>
                          ')

            ->remove_column('id')
            ->make();

    }
    public function getEditDropOut()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_dropout_application');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $mode = 'edit';
        $id = trim($_GET['student_id']);
        if(!empty($id)){
            $admission = DB::table('admissions')
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
                })
                ->leftjoin('student_dropout', function ($join) {
                    $join->on('admissions.id', '=', 'student_dropout.student_id');
                })
                ->where('student_dropout.student_id', $id)
                ->where('admissions.campuscode', $campus->id)
                ->select(array('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno',
                    'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                    'admissions.hukou', 'admissions.program', 'admissions.dateofbirth', 'admission_details.formerlevel',
                    'rawprograms.name as major_name', 'campuses.name as campus_name', 'admissions.nationgroup',
                    'student_dropout.application_year','student_dropout.application_semester',
                    'student_dropout.cause'))
                ->first();
            return View::make('admin/admissions/admissions_application_dropout_create', compact('admission','title','mode'));

        }

    }


    public function postEditDropOut()
    {
        $student_id = Input::get('student_id');
        $cause = Input::get('cause');
        $drop = DB::table('student_dropout')->where('student_id',$student_id)->select('id')->first();
        $dropout = AdmissionDropOut::find($drop->id);
        $dropout->cause = $cause;
        $dropout->updated_at = new DateTime() ;
        $dropout->save();
        return Redirect::to('admin/admissions/edit_dropout?student_id='.$student_id)->with('success',Lang::get('admin/admissions/messages.dropout.edit_success'));

    }

    public function getDeleteDropOut(){
        $id = $_GET['student_id'];
        $title = Lang::get('admin/admissions/title.delete_dropout');
        $dropout =DB::table('admissions')->where('id',$id)->first();
        return View::make('admin/admissions/admissions_dropout_delete', compact('dropout','title'));
    }

    public function postDeleteDropOut(){
        $id = Input::get('student_id');
        $drop = DB::table('student_dropout')
            ->where('student_id',$id)
            ->select('id')
            ->first();
        $dropout = AdmissionDropout::find($drop->id);
        $dropout->is_deleted = 1;
        $dropout->save();
        $admission = DB::table('admissions')->where('id',$id)->first();
        $count = DB::table('student_dropout')->where('student_id',$id)->where('is_deleted',1)->count();
        if($count > 0){
            // There was a problem deleting the group
            return Redirect::to('admin/admissions/admissions_edit_dropout')->with('success', Lang::get('admin/admissions/messages.dropout.delete_success'));
            echo "<script>window.opener.location = window.opener.location;window.close();</script>";
        }else
            return Redirect::to('admin/admissions/get_dropout?student_id='.$admission->studentno);
    }


    public function getIndexForRecordAdmissionsRewardPunish()
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $rawprograms = RawProgram::join('programs','programs.name','=','rawprograms.id')
            ->where('programs.campus_id',$campus->id)
            ->select('rawprograms.id','rawprograms.name')
            ->get();
        return View::make('admin/admissions/admissions_record_reward_punish', compact('rawprograms', 'title'));
    }

    public function getDataForRecordAdmissionsRewardPunish()
    {
        //    $groups  =AdminGroup::All();
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $filter_program = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $flag = 0;

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($major)) {
            $filter_program['id'] = $major;
        }
        if (!empty($major_classification)) {
            $filter_program['type'] = $major_classification;
        }

        if (!empty($filter_program)) {
            $program_ids = DB::table('rawprograms')->select('id')
                ->where(function ($query) use ($filter_program) {
                    if (!is_array($filter_program)) {
                        return $query;
                    }
                    foreach ($filter_program as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $student_ids = DB::table('admissions')->select('id')
                ->whereIn('programcode', $program_ids)
                ->lists('id');
            $flag = 1;
        }


        if ($flag == 1) {
            $programs = DB::table('admissions')->whereIn('admissions.id', $student_ids)
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
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
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'rawprograms.name as major_name',
                    'rawprograms.type', 'groups.sysid', 'groups.name as group_name', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');

        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
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
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'rawprograms.name as major_name',
                    'rawprograms.type', 'groups.sysid', 'groups.name as group_name', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');
        }
        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('formerlevel', '@if($formerlevel == 1)
                                        高中毕业
                                    @elseif($formerlevel == 2)
                                        职高毕业
                                    @elseif($formerlevel == 3)
                                        中专毕业
                                    @elseif($formerlevel == 4)
                                        技校毕业
                                    @elseif($formerlevel == 5)
                                        专科毕业
                                    @elseif($formerlevel == 6)
                                        本科毕业
                                    @elseif($formerlevel == 7)
                                        硕士研究生毕业
                                    @elseif($formerlevel == 8)
                                        博士研究生毕业
                                    @elseif($formerlevel == 9)
                                        其他毕业
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
            ->add_column('actions', '
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/record_reward_campus/\')}}" class="iframe">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/record_punish_campus/\')}}" class="iframe">惩罚</a>
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
            ->edit_column('gender', '@if($gender == \'f\')
                                          女
                                 @elseif($gender == \'m\')
                                          男
                                 @endif')
            ->remove_column('id')
            ->make();

    }

    public function getCreateAward($id)
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $rewards = RewardInfo::All();
        return View::make('admin/admissions/record_reward_campus', compact('id', 'title','rewards'));
    }

    public function postCreateAward($id)
    {
        $reward_date = Input::get('reward_date');
        $rewardType = Input::get('rewardType');
        $file_num = Input::get('file_num');
        $operator = Input::get('operator');
        $remark = Input::get('remark');

        $recordexists = DB::table('student_reward_punishment')
            ->where('document_id', $file_num)
            ->where('reward_level', $rewardType)
            ->where('student_id',$id)
            ->count();
        if ($recordexists == 0) {
            $rewardinfo = new RewardPunishment();
            $rewardinfo->document_id = $file_num;
            $rewardinfo->reward_level = $rewardType;
            $rewardinfo->approval_result = 0;
            $rewardinfo->date = $reward_date;
            $rewardinfo->operator = $operator;
            $rewardinfo->remark = $remark;
            $rewardinfo->student_id = $id;
            $rewardinfo->created_at = new DateTime();
            $rewardinfo->updated_at = new DateTime();
            $rewardinfo->save();
            return Redirect::to('admin/admissions/admissions_record_reward_punish')->with('success',Lang::get('admin/admissions/messages.create.success'));
        }else{
            return Redirect::to('admin/admissions/'.$id.'/define_reward')->withErrors(Lang::get('admin/admissions/messages.already_exists'));
        }
    }

    public function getCreatePunish($id)
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        return View::make('admin/admissions/record_punish_campus', compact('id', 'title'));
    }

    public function postCreatePunish($id)
    {
        $punish_code = Input::get('punish_code');
        $punish_cause = Input::get('punish_cause');
        $file_num = Input::get('file_num');
        $operator = Input::get('operator');
        $remark = Input::get('remark');
        $punish_date = Input::get('punish_date');

        $recordexists = DB::table('student_reward_punishment')
            ->where('document_id', $file_num)
            ->where('punishment', $punish_code)
            ->where('punishment_cause', $punish_cause)
            ->where('student_id',$id)
            ->count();
        if ($recordexists == 0) {
            $rewardinfo = new RewardPunishment();
            $rewardinfo->document_id = $file_num;
            $rewardinfo->punishment = $punish_code;
            $rewardinfo->punishment_cause = $punish_cause;
            $rewardinfo->approval_result = 0;
            $rewardinfo->date = $punish_date;
            $rewardinfo->operator = $operator;
            $rewardinfo->remark = $remark;
            $rewardinfo->student_id = $id;
            $rewardinfo->created_at = new DateTime();
            $rewardinfo->updated_at = new DateTime();
            $rewardinfo->save();
            return Redirect::to('admin/admissions/admissions_record_reward_punish')->with('success',Lang::get('admin/admissions/messages.create.success'));
        }else{
            return Redirect::to('admin/admissions/'.$id.'/define_punish')->withErrors(Lang::get('admin/admissions/messages.already_exists'));
        }

    }




    public function getIndex() {
        // Title
        $title = $title = Lang::get('admin/admissions/title.groups_management');

        // Show the page
        return View::make('admin/admissions/group_index', compact('title'));

    }
    public function getGroupsData()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $groups = Groups::leftjoin('programs', 'programs.id', '=', 'groups.programs_id')
            ->leftjoin('rawprograms', 'rawprograms.id', '=', 'programs.name')
            ->leftjoin('admission_group', 'admission_group.group_id', '=', 'groups.id')
            ->where('groups.campus_id', '=', $campus->id)
            ->select(array(
                'groups.id',
                'groups.name',
                'groups.sysid',
                'programs.rank',
                'rawprograms.name as programsname',
                DB::raw('count(admission_group.group_id) as student_count'),
                'groups.created_at'
            ))
            ->groupBy('groups.id')
            ->orderBy('groups.name', 'asc');
        return Datatables::of($groups)
            ->edit_column('rank', '@if($rank == \'12\')
                                        	   本科
                                         @elseif($rank == \'14\')
            								    专科
                                         @elseif($rank == \'3\')
            								    研究生及以上
                                         @endif')
            ->add_column('actions','@if ($student_count == 0)
                                    <a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/delete_group\' ) }}}" class="iframe btn btn-xs btn-danger">{{{ Lang::get(\'button.delete\') }}}</a>
                                @endif
                               ')
            ->remove_column('id')
            ->make();
    }

    public function getEditGroups($groupid){
        // Title
        $title = $title = Lang::get('admin/admissions/title.groups_update');

        $mode = 'edit';

        //group data
        $group = Groups::find($groupid);

        //program data
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $programs = DB::table('programs')
            ->join('rawprograms', 'rawprograms.id', '=', 'programs.name')
            ->where('programs.status', '=', '1')
            ->where( 'programs.campus_id', '=', $campus->id)
            ->select('programs.id', 'rawprograms.name', 'rawprograms.type')
            ->orderby('rawprograms.type', 'ASC')
            ->orderby('rawprograms.name', 'ASC')
            ->get();

        $param = new stdClass();
        $param->programs = $programs;
        $param->selectedProgramID = $group->programs_id;
        $param->groupname = $group->name;

        // Show the page
        return View::make ('admin/admissions/define_create_group', compact ('title', 'mode', 'param'));
    }

    public function postEditGroups($groupid){
        $rules = array(
            'groupname'=> 'required',
        );
        $messages = array(
            'groupname.required' => Lang::get ( 'admin/admissions/messages.groupname_required'),
        );
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('admin/admissions/'.$groupid.'/group_edit')
                ->withInput()
                ->withErrors($validator);
        }
        $groups_count = DB::table('groups')
            ->where('name', Input::get('groupname'))
            ->where('id', '<>', $groupid)
            ->count();
        if($groups_count > 0){
            return Redirect::to('admin/admissions/'.$groupid.'/group_edit')
                ->withInput()
                ->withErrors(Lang::get('admin/admissions/messages.already_exists'));
        }else {
            $groups = Groups::find($groupid);
            $groups->name = Input::get('groupname');
            $groups->save();
        }

        // Show the page
        return Redirect::to('admin/admissions/'.$groupid.'/group_edit')
            ->withSuccess ( Lang::get ( 'admin/admissions/messages.edit.success' ) );
    }

    public function getGroupSysid($year,$semester,$groupid, $program){

        $semester      = intval($semester);
        $campuse_sysid = str_pad($program->sysid, 4, "0", STR_PAD_LEFT);
        $program_rank  = str_pad($program->rank, 3, "0", STR_PAD_LEFT);
        $waterflow_num = str_pad($groupid, 3, "0", STR_PAD_LEFT);

        $group_sysid = $year.$semester.$campuse_sysid.$program_rank.$waterflow_num;

        return $group_sysid;
    }

    public function getDeleteGroups($groupid){
        // Title
        $title = Lang::get('admin/admissions/title.groups_delete');

        // Show the page
        return View::make('admin/admissions/delete_group', compact('groupid', 'title'));

    }

    public function postDeleteGroups($groupid){
        $status = false;
        //check it can delete or not, group can be delete when it is empty
        $group_count = DB::table('groups')
            ->leftjoin('admission_group', 'admission_group.group_id', '=', 'groups.id')
            ->where ('admission_group.group_id', '=', $groupid)
            ->select ( array (
                'groups.id',
            ))
            ->count()
        ;
        if($group_count <= 0){
            $group = Groups::find($groupid);
            $group->delete();
            $group = Groups::find($groupid);
            if (empty($group)){
                $status = true;
            }
        }
        if(!$status)
            // There was a problem deleting the group
            return Redirect::to('admin/admissions/admin_group')->with('error', Lang::get('admin/admissions/messages.delete.error'));
        else
            return Redirect::to('admin/admissions/admin_group')->with('success', Lang::get('admin/admissions/messages.delete.success'));
    }


    public function getEditGroup() {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admin_group');

        // Show the page
        return View::make('admin/admissions/edit_admin_group', compact('title'));

    }
    public function getDataForEditGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $year = Input::get('year');
        $groups = explode(',',Input::get('groups'));
        $ids = explode(',',Input::get('ids'));
        $class_advisers = explode(',',Input::get('class_advisers'));
        $student_type = Input::get('student_type');
        $exam_point = Input::get('exam_point');
        $groupname = Input::get('groupname');

        $state = Input::get('state');

        if (!empty($year)) {
            $filter["year"] =  $year;
        }
        if (!empty($groupname)) {
            $filter["name"] = '%' . $groupname . '%';
        }
        if (!empty($student_type)) {
            $filter["major_classification"] =  $student_type;
        }
        /*    if (!empty($exam_point)) {
                $filter["exam_point"] =  $exam_point;
            }
    */
        if ($state == 2){
            for ($i=1;$i<count($ids);$i++){
                if (($groups[$i]!='')|| ($class_advisers[$i] !='')){
                    DB::table('groups')->where('id',$ids[$i])->update(array('name'=>$groups[$i],'class_adviser'=>$class_advisers[$i]));
                }
            }
        }
        $programs = DB::table('groups')
            ->leftjoin('admission_group', function ($join) {
                $join->on('admission_group.group_id', '=', 'groups.id');
            })
            ->leftjoin('admissions', function ($join) {
                $join->on('admissions.id', '=', 'admission_group.admission_id');
            })
            ->leftjoin('programs', function ($join) {
                $join->on('programs.id', '=', 'groups.programs_id');
            })
            ->leftjoin('rawprograms', function ($join) {
                $join->on('programs.name', '=', 'rawprograms.id');
            })
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan.major', '=', 'rawprograms.name');
            })
            ->where(function ($query) use ($filter) {
                if (!is_array($filter)) {
                    return $query;
                }
                foreach ($filter as $key => $value) {
                    $query = $query->where('groups.' . $key, 'like', $value);
                }
                return $query;
            })
            ->where('groups.campus_id', '=', $campus->id)
            ->select('groups.id', 'groups.sysid', 'groups.name as group_name',
                'rawprograms.name as pname', 'teaching_plan.code',
                DB::raw('count(admission_group.group_id) as student_count'),
                'groups.class_adviser', 'rawprograms.type', 'groups.year','groups.semester','groups.exam_point','groups.id as sid')
            ->groupBy('groups.sysid');

        return Datatables::of($programs)
            ->edit_column('group_name', '
                                <input type="text" id="group_name" name="group_name[]" value="{{$group_name}}" style="width:150px">
                                 ')
            ->edit_column('pname', '
                                {{$pname}}_{{$code}}_{{$student_count}}
                                ')
            ->edit_column('class_adviser', '
                                <input type="text" id="class_adviser" name="class_adviser[]" value="{{$class_adviser}}" style="width: 100px">
                       ')
            ->edit_column('type', '@if($type == \'12\')
                                        	   本科
                                         @elseif($type == \'14\')
            								    专科
                                         @elseif($type == \'3\')
            								    研究生及以上
            								    @endif
           ')
            ->edit_column('semester', '@if ($semester == \'01\')
                                      春季
                                 @else
                                      秋季
                                 @endif')
            ->edit_column('sid','<input type="hidden" name="ids[]" id="ids" value="{{$id}}">')
            ->remove_column('code')
            ->remove_column('student_count')
            ->make();
    }

    public function getCreateAdminGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        // Title
        $title = Lang::get('admin/admissions/title.create_admin_group');
        $rawprograms = RawProgram::leftjoin('programs','programs.name','=','rawprograms.id')
            ->where('programs.campus_id',$campus->id)
            ->where('programs.status',1)
            ->select('programs.id','rawprograms.name as pname')
            ->get();
        // Show the page
        $mode = 'create';
        return View::make('admin/admissions/group_define_edit', compact('rawprograms', 'title','mode'));
    }

    public function postCreateAdminGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $having_stu_no = Input::get('having_stu_no');
        $group_name = Input::get('group_name');
        $class_adviser = Input::get('class_adviser');

        $programs = DB::table('programs')
            ->join('campuses', 'programs.campus_id', '=', 'campuses.id')
            ->where('campuses.id',$campus->id)
            ->where('programs.id',$major)
            ->where('programs.rank',$major_classification)
            ->where('programs.status', '=', '1')
            ->select('programs.id','programs.name', 'programs.rank', 'campuses.sysid')->first();
        $groups_count = DB::table('groups')
            ->where('name', $group_name)
            ->where('programs_id', $major)
            ->where('major_classification',$major_classification)
            ->count();
        if($groups_count > 0){
            return Redirect::to('admin/admissions/group_define')
                ->withInput()
                ->withErrors(Lang::get ( 'admin/admissions/messages.already_exists'));
        }

        //program data with campus sysid


        if(!$programs){
            return Redirect::to('admin/admissions/group_define')
                ->withInput()
                ->withErrors(Lang::get ( 'admin/admissions/messages.groups.programs_disapprove'));
        }

        //store
        if(!$groups_count){
            $groups = new Groups();
            $groups->name = $group_name;
            $groups->programs_id = $major;
            $groups->major_classification = $major_classification;
            $groups->year = $year;
            $groups->semester = $semester;
            $groups->campus_id = $campus->id;
            $groups->having_stu_no = $having_stu_no;
            $groups->class_adviser = $class_adviser;
            $groups->save();
            //get the group id first then update group's sysid
            $groups->sysid = $this->getGroupSysid($year,$semester,$groups->id, $programs);
            $groups->save();
        }

        // Show the page
        return Redirect::to ( "admin/admissions/admin_group" )->withSuccess ( Lang::get ( 'admin/admissions/messages.create.success' ) );
    }


    public function getAdmissionAppointGroup()
    {
        $title = Lang::get('admin/admissions/title.admission_appoint_group');
        $rawprograms = DB::table('rawprograms')
            ->leftjoin('programs', function ($join) {
                $join->on('programs.name', '=', 'rawprograms.id');
            })
            ->where('programs.status',1)
            ->select('rawprograms.id','rawprograms.name')
            ->groupBy('rawprograms.id')
            ->get();
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $groups =  Groups::join('programs','programs.id','=','groups.programs_id')
            ->where('programs.campus_id',$campus->id)->select('groups.id','groups.name')->get();
        return View::make('admin/admissions/admissions_appoint_group', compact('rawprograms','groups','title'));
    }


    public function getDataForAdmissionsAppointGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $filter_group = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major = Input::get('major');
        $student_type = Input::get('student_type');
        $admissionyear = Input::get('admissionyear');
        $admissionsemester = Input::get('admissionsemester');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $admission_state = Input::get('admission_state');

        $group = explode(',',Input::get('group'));
        $ids = explode(',',Input::get('ids'));

        $start_stu_no = Input::get('start_stu_no');
        $end_stu_no = Input::get('end_stu_no');
        $group_id = Input::get('group_id');

        $state = Input::get('state');

        if ($state == 2){
            for ($i=1;$i<count($ids);$i++){
                if ($group[$i] >0){
                    DB::table('admission_group')->where('admission_id',$ids[$i])->update(array('group_id'=>$group[$i]));
                }
            }
        }



        if ($state==3){
            $student_ids = Admission::whereBetween('studentno',array($start_stu_no,$end_stu_no))->select('id')->lists('id');
            DB::table('admission_group')
                ->whereIn('admission_id',$student_ids)
                ->update(array('group_id'=>$group_id));
        }

        $flag = 0;
        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($major)) {
            $filter['programcode'] = $major;
        }
        if (!empty($student_type)) {
            $filter['enrollmenttype'] = $student_type;
        }
        if (!empty($admissionyear)) {
            $filter["admissionyear"] = $admissionyear;
        }
        if (!empty($admissionsemester)) {
            $filter["admissionsemester"] = $admissionsemester;
        }
        if (!empty($year)) {
            $filter_group['year'] = $year;
        }
        if (!empty($semester)) {
            $filter_group['semester'] = $semester;
        }
        if (!empty($admission_state)) {
            $filter['status'] = $admission_state;
        }
        if (!empty($filter_group)) {
            $groups_ids = DB::table('groups')->select('id')
                ->where(function ($query) use ($filter_group) {
                    if (!is_array($filter_group)) {
                        return $query;
                    }
                    foreach ($filter_group as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $student_ids = DB::table('admission_group')
                ->whereIn('group_id', $groups_ids)
                ->select('admission_id as id')
                ->lists('id');
            $flag = 1;
        }
        if ($flag == 1){
            $admissions = DB::table('admissions')
                ->whereIn('admissions.id',$student_ids)
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
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
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id','admissions.studentno', 'admissions.fullname', 'admissions.program', 'admissions.status',
                    'groups.sysid', 'groups.name as gname', 'rawprograms.name as pname', 'rawprograms.type', 'admissions.nationgroup',
                    'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou',
                    'admissions.distribution', 'admissions.is_serving', 'admissions.id as sid');
        }else if($flag == 0){
            $admissions = DB::table('admissions')
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
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
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id',  'admissions.studentno', 'admissions.fullname', 'admissions.program', 'admissions.status',
                    'groups.sysid', 'groups.name as gname', 'rawprograms.name as pname', 'rawprograms.type', 'admissions.nationgroup',
                    'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou',
                    'admissions.distribution', 'admissions.is_serving', 'admissions.id as sid');

        }
        return Datatables::of($admissions)
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
            ->edit_column('gname', '<select id="group" style="width:70px;" name="group[]">
                                        <option value="">请选择</option>
                                        @foreach( ($groups = Groups::leftjoin(\'programs\', \'programs.id\', \'=\', \'groups.programs_id\')
                                        ->leftjoin(\'campuses\',\'campuses.id\',\'=\',\'programs.campus_id\')
                                         ->where(\'campuses.userID\', \'=\', Auth::user ()->id)
                                         ->select(\'groups.id as gid\',\'groups.name as gname\')
                                         ->get())  as $group)
                                            <option value="{{$group->gid}}">{{$group->gname}}</option>
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
            ->edit_column('sid','<input type="hidden" name="ids[]" id="ids" value="{{$id}}">')
            ->make();

    }

}