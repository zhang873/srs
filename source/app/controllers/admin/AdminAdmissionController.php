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
}