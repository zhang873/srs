<?php

class AdminSelectController extends AdminController {
    public function getIndex() {

    }

    public function getModuleSemester() {
        $title = Lang::get ( 'admin/select/title.update_module_year' );

        $curInfo = ModuleCurrent::where('module_id', 3)->first();

        return View::make ( 'admin/select/update_module_semester', compact ('title', 'curInfo') );
    }

    public function getModuleSemesterRst() {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        if (!empty($curInfo)) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
            $records = ProgramInfo::where('year', $cur_year)->where('semester', $cur_semester)->get();
            foreach($records as $record){
                $record->year = $year;
                $record->semester = $semester;
                $record->save();
            }
            $curInfo->current_year = $year;
            $curInfo->current_semester = $semester;
            $curInfo->save();
            return 'ok';
        }
        return 'err';
    }

    public function getCtrl($ctype)
    {
        if ($ctype == 'campus') {
            $title = Lang::get('admin/select/title.control_campus_selection');
        } elseif ($ctype == 'campusConfirm') {
            $title = Lang::get('admin/select/title.control_campus_confirmation');
        } elseif ($ctype == 'provinceStudent') {
            $title = Lang::get('admin/select/title.control_province_student_selection');
        }

        return View::make('admin/select/control', compact('title', 'ctype'));
    }

    public function getCtrlData($ctype)
    {
        $type = Input::get('type');
        $chk_ids = Input::get('check_item');
        $no_chk_ids = Input::get('no_check_item');
        if ($ctype == 'campus') {
            $field = 'campus_selection';
        } elseif ($ctype == 'campusConfirm') {
            $field = 'campus_confirmation';
        } elseif ($ctype == 'provinceStudent') {
            $field = 'province_student_selection';
        }
        if ($type == 1) {
            if (!empty($chk_ids)) {
                DB::table('state_info')
                    ->whereIn('id', $chk_ids)
                    ->update(array($field => 1));
            }
            if (!empty($no_chk_ids)) {
                DB::table('state_info')
                    ->whereIn('id', $no_chk_ids)
                    ->update(array($field => 0));
            }
        }

        $programs = DB::table('state_info')
            ->join('campuses', 'state_info.campus_id', '=', 'campuses.id')
            ->select('state_info.id as sid', 'name', 'sysid', $field);
        return Datatables::of($programs)
            ->edit_column($field, function ($row) use ($field) {
                if ($row->{$field} == 1)
                    return '开通';
                else
                    return '关闭';
            })
            ->add_column('itemnumber', '', 0)
            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$sid}}}"></div>', 4)
            ->remove_column('sid')
            ->make();
    }

    public function getCountNumberCourse()
    {
        $title = Lang::get('admin/select/title.count_number_course');
        $campuses = Campus::select('id', 'name')->get();
        $groups = Group::join('programs',  'groups.programs_id', '=', 'programs.id')
            ->select('groups.id as gid','groups.name as gname', 'programs.campus_id as cid')->get();
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/count_number_course', compact('title', 'campuses', 'groups', 'cur_year'));
    }

    public function getCountNumberCourseData()
    {
        $campus_id = Input::get('campus');
        $group_id = Input::get('group');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $course_code = Input::get('course_code');
        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');

        $query = DB::table('student_selection');
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }

        $query = $query
            ->join('admission_group', 'student_selection.student_id', '=', 'admission_group.admission_id')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('programs', 'groups.programs_id', '=', 'programs.id')
            ->leftjoin('campuses', 'programs.campus_id', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!empty($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!empty($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }
        if (!empty($course_code)) {
            $query = $query->where('course.code', 'like', '%' . $course_code . '%');
        }
        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }

        $query = $query->select('campuses.name as cpname', 'groups.name as gname', 'student_selection.year',
            'student_selection.semester', 'course.code', 'course.name as cname', 'course.credit',
            'student_selection.is_obligatory', DB::raw('count(*) as number'))
            ->groupBy('campuses.name', 'groups.name', 'course.code');

        return Datatables::of($query)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->make();
    }
    public function getCampusGroup()
    {
        $campus_id = Input::get('campus_id');

        if ($campus_id == '全部'){
            $rsts = Group::select('groups.id as gid','groups.name as gname')->get();
        }else{
            $rsts = Group::join('programs',  'groups.programs_id', '=', 'programs.id')
                ->where('programs.campus_id', $campus_id)
                ->select('groups.id as gid','groups.name as gname')->get();
        }
        return $rsts->toJson();
    }

    public function getQueryGroupSelection()
    {
        $title = Lang::get('admin/select/title.query_group_selection');
        $campuses = Campus::select('id', 'name')->get();
        $groups = Group::join('programs',  'groups.programs_id', '=', 'programs.id')
            ->select('groups.id as gid','groups.name as gname', 'programs.campus_id as cid')->get();
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/query_group_selection', compact('title', 'campuses', 'groups', 'cur_year'));
    }

    public function getQueryGroupSelectionData()
    {
        $group_id = Input::get('group');
        $group_num = Input::get('group_num');
        $campus_id = Input::get('campus');
        $major_classification = Input::get('major_classification');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');
        $selection_status = Input::get('selection_status');
        $student_classification = Input::get('student_classification');

        $query = DB::table('student_selection');
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }
        if (!empty($selection_status) && $selection_status != "全部") {
            $query = $query->where('student_selection.selection_status', $selection_status);
        }

        $query = $query
            ->join('admission_group', 'student_selection.student_id', '=', 'admission_group.admission_id')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!empty($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!empty($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }
        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!empty($group_num)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_num . '%');
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }

        $query = $query->select('admissions.studentno', 'admissions.fullname', 'course.code', 'course.name as cname',
            'course.credit', 'rawprograms.name as rname', 'student_selection.is_obligatory', 'student_selection.year',
            'student_selection.semester','campuses.name as cpname', 'groups.name as gname', 'groups.sysid as gsysid',
            'student_selection.selection_status');

        return Datatables::of($query)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('gname', '{{$gname}}{{$gsysid}}')
            ->remove_column('gsysid')
            ->make();
    }

    public function getQuerySelectionRecord()
    {
        $title = Lang::get ( 'admin/select/title.query_selection_record' );
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make ( 'admin/select/query_selection_record', compact ('title', 'cur_year') );
    }

    public function getQuerySelectionRecordData()
    {
        $student_id = Input::get('student_id');
        $major_classification = Input::get('major_classification');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');

        $query = DB::table('student_selection')->where('student_selection.is_deleted', 0);
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }


        $query = $query
            ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');

        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($student_id)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_id . '%');
        }

        $query = $query->select('admissions.studentno', 'admissions.fullname', 'course.code', 'course.name as cname',
            'course.credit', 'rawprograms.name as rname', 'student_selection.is_obligatory', 'student_selection.year',
            'student_selection.semester');

        return Datatables::of($query)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->make();

    }

    public function getCountSelection()
    {
        $title = Lang::get('admin/select/title.count_selection');
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        $campuses = Campus::select('id', 'name')->get();
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/count_selection', compact('title', 'b_majors', 'z_majors', 'campuses', 'cur_year'));
    }

    public function getCountSelectionData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $campus_id = Input::get('campus');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('course_code');
        $course_name = Input::get('course_name');
        $major = Input::get('major');
        $is_obligatory = Input::get('is_obligatory');
        $year_in = Input::get('year_in');
        $semester_in = Input::get('semester_in');
        $selection_status = Input::get('selection_status');
        $credit = Input::get('credit');
        $student_classification = Input::get('student_classification');
        $type = Input::get('type');

        $query = DB::table('student_selection');
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }
        if (!empty($selection_status) && $selection_status != "全部") {
            $query = $query->where('student_selection.selection_status', $selection_status);
        }

        $query = $query
            ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!empty($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!empty($course_code)) {
            $query = $query->where('course.code', 'like', '%' . $course_code . '%');
        }
        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!empty($credit)) {
            $query = $query->where('course.credit', $credit);
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!empty($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!empty($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', 'like', '%' . $semester_in . '%');
        }
        if (!empty($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }

        if ($type == 1) {
            $query = $query->select('student_selection.year', 'student_selection.semester', 'admissions.program', 'rawprograms.type',
                'campuses.name as cpname', 'rawprograms.name as rname', 'course.code', 'course.name as cname', 'student_selection.is_obligatory',
                'student_selection.selection_status', 'admissions.admissionyear', 'admissions.admissionsemester', 'course.credit',
                DB::raw('count(*) as number'))
                ->groupBy('campuses.name', 'course.code');

            return Datatables::of($query)
                ->add_column('itemnumber', '', 0)
                ->edit_column('program', '@if($program == \'12\')本科@elseif($program == \'14\')专科@endif')
                ->edit_column('type', '@if($type == \'12\')本科@elseif($type == \'14\')专科@endif')
                ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
                ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
                ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
                ->make();
        } elseif ($type == 2) {
            $query = $query->select('admissions.studentno', 'admissions.fullname', 'student_selection.year',
                'student_selection.semester', 'admissions.program', 'rawprograms.type', 'campuses.name as cpname',
                'rawprograms.name as rname', 'course.code', 'course.name as cname', 'student_selection.is_obligatory',
                'student_selection.selection_status', 'admissions.admissionyear', 'admissions.admissionsemester', 'course.credit');

            return Datatables::of($query)
                ->edit_column('program', '@if($program == \'12\')本科@elseif($program == \'14\')专科@endif')
                ->edit_column('type', '@if($type == \'12\')本科@elseif($type == \'14\')专科@endif')
                ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
                ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
                ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
                ->make();
        }
    }

    public function getChangeCampusSelection()
    {
        $title = Lang::get('admin/select/title.change_campus_selection');
        $state = 0;
        $campus_id = Session::get('campus_id');
        if (!is_null($campus_id)) {
            $records = StateInfo::where('campus_id', $campus_id)->first();
            if (!is_null($records))
                $state = $records->campus_student_selection;
        }

        return View::make('admin/select/change_campus_selection', compact('title', 'state'));
    }

    public function getChangeCampusSelectionRst() {
        $state = Input::get('state');
        $campus_id = Session::get('campus_id');
        if (!is_null($state)) {
            $records = StateInfo::where('campus_id', $campus_id)->first();
            $records->campus_student_selection = $state;
            $records->save();
            return 'ok';
        }
        return 'err';
    }

    public function getGroupSelection()
    {
        $title = Lang::get('admin/select/title.group_selection');

        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        $cur_semester = null;
        if ($curInfo != null) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
        }

        return View::make('admin/select/group_selection', compact('title', 'b_majors', 'z_majors', 'cur_year', 'cur_semester'));
    }

    public function getGroupSelectionCourseData()
    {
        $year_in = Input::get('year_in');
        $semester_in = Input::get('semester_in');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');


        $query = DB::table('teaching_plan');
        if (!is_null($year_in)) {
            $query = $query->where('teaching_plan.year', 'like', '%' . $year_in . '%');
        }
        if (!is_null($semester_in) && $semester_in != "全部") {
            $query = $query->where('teaching_plan.semester', $semester_in);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('teaching_plan.major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('teaching_plan.major_classification', $major_classification);
        }

        $programs = $query
            ->join('teaching_plan_module', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->join('module_course', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0);
            })
            ->leftjoin('course', 'module_course.course_id', '=', 'course.id')
            ->select('course.id as cid', 'course.code', 'course.name', 'course.credit', 'course.classification',
                'course.remark', 'module_course.suggested_semester as ssemester', 'module_course.is_obligatory')
            ->groupBy('course.id');


        return Datatables::of($programs)
            ->edit_column ( 'classification', '@if($classification == \'14\')专科@else本科@endif')
            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}">
                    <input type="hidden" id="sm" value="{{{$ssemester}}}">
                    <input type="hidden" id="ob" value="{{{$is_obligatory}}}">
                </div>', 0)
            ->remove_column('cid')
            ->remove_column('ssemester')
            ->remove_column('is_obligatory')
            ->make();
    }

    public function getGroupSelectionClassData()
    {
        $year = Input::get('year');
        $student_classification = Input::get('student_classification');
        $campus_id = Session::get('campus_id');
        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->where('campuses.id', $campus_id);
        if (!empty($year) && $year != "全部") {
            $query = $query->where('admissions.admissionyear', $year);
        }
        if (!empty($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }
        $query = $query->select('groups.id as gid', 'groups.sysid', 'groups.name as gname')
            ->groupBy('groups.id');

        return Datatables::of($query)

            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$gid}}}"></div>', 0)
            ->remove_column('gid')
            ->make();
    }

    public function getGroupSelectionStudentData()
    {
        $ids = Input::get('checkitem');
        $student_major = Input::get('student_major');
        $campus_id = Session::get('campus_id');
        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->leftjoin('student_selection', function ($join) {
                $join->on('student_selection.student_id', '=', 'admissions.id')
                    ->where('student_selection.is_deleted', '=', 0);
            })
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('admissions.campuscode', $campus_id)->whereIn('groups.id', $ids);
        if (!empty($student_major) && $student_major != "全部") {
            $query = $query->where('rawprograms.name', $student_major);
        }

        $query = $query->select('admissions.id as aid', 'admissions.studentno', 'admissions.fullname',
            'course.code', 'course.name as cname','groups.sysid', 'student_selection.selection_status',
            'rawprograms.name as rname');


        return Datatables::of($query)

            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$aid}}}"></div>', 0)
            ->add_column('exam',
                '<div align="center"><select size="1">
                    <option value="是" selected="selected">是</option>
                    <option value="否">否</option></select>
                </div>', 7)
            ->add_column('be_out',  function ($row) {
                $major = $row->rname;
                $rsts = DB::table('module_course')
                    ->join('teaching_plan_module', function ($join) {
                        $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                            ->where('teaching_plan_module.is_deleted', '=', 0)
                            ->where('module_course.is_deleted', '=', 0);
                    })
                    ->leftjoin('teaching_plan', function ($join) {
                        $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                            ->where('teaching_plan.is_deleted', '=', 0);
                    })
                    ->leftjoin('course', function ($join) {
                        $join->on('module_course.course_id', '=', 'course.id')
                            ->where('course.is_deleted', '=', 0);
                    })
                    ->where('teaching_plan.major', $major)->select('course.code as ccode')->lists('ccode');
                if (!is_null($row->code)) {
                    if (in_array($row->code, $rsts))
                        return '否';
                    else
                        return '是';
                }
            })
            ->remove_column('aid')
            ->remove_column('rname')
            ->make();
    }

    public function getGroupSubmitClass() {

        $classes = Input::get('classes');
        $courses = Input::get('courses');
        $obligatory = Input::get('obligatory');
        if (is_null($classes) || is_null($courses)) {
            return 'err';
        }
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        $cur_semester = null;
        if ($curInfo != null) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
        }
        else{
            return 'err';
        }

        $students = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->whereIn('groups.id', $classes)->select('admissions.id')->lists('admissions.id');
        if (is_null($students)) {
            return 'err';
        }

        foreach($students as $student){
            for ($i=0; $i < count($courses); $i++){
                $records = StudentSelection::where('student_id', $student)
                    ->where('course_id', $courses[$i])->first();
                if (is_null($records)){
                    $records = new StudentSelection();
                    $records->student_id = $student;
                    $records->course_id = $courses[$i];
                }
                $records->year = $cur_year;
                $records->semester = $cur_semester;
                $records->is_obligatory = $obligatory[$i];
                $records->is_deleted = 0;
                $records->save();
            }
        }
        return 'ok';
    }
    public function getGroupSubmitStudent() {

        $students = Input::get('students');
        $courses = Input::get('courses');
        $obligatory = Input::get('obligatory');
        if (is_null($students) || is_null($courses)) {
            return 'err';
        }
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        $cur_semester = null;
        if ($curInfo != null) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
        }
        else{
            return 'err';
        }
        foreach($students as $student){
            for ($i=0; $i < count($courses); $i++){
                $records = StudentSelection::where('student_id', $student)
                    ->where('course_id', $courses[$i])->first();
                if (is_null($records)){
                    $records = new StudentSelection();
                    $records->student_id = $student;
                    $records->course_id = $courses[$i];
                }
                $records->year = $cur_year;
                $records->semester = $cur_semester;
                $records->is_obligatory = $obligatory[$i];
                $records->is_deleted = 0;
                $records->save();
            }
        }
        return 'ok';
    }

    public function getClassSelection()
    {
        $title = Lang::get('admin/select/title.class_selection');

        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        $cur_semester = null;
        if ($curInfo != null) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
        }
        return View::make('admin/select/class_selection', compact('title', 'b_majors', 'z_majors', 'cur_year', 'cur_semester'));
    }

    public function getClassSelectionClassData()
    {
        $year = Input::get('year');
        $student_classification = Input::get('student_classification');
        $major = Input::get('major');
        $class_sysid = Input::get('class_sysid');
        $campus_id = Session::get('campus_id');
        $query = DB::table('groups')
            ->join('programs', 'groups.programs_id', '=', 'programs.id')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->join('teaching_plan', 'rawprograms.name', '=', 'teaching_plan.major')
            ->where('programs.campus_id', $campus_id);
        if (!is_null($year) && $year != "全部") {
            $query = $query->where('teaching_plan.year', $year);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('teaching_plan.student_classification', $student_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('teaching_plan.major', $major);
        }
        if (!empty($class_sysid)) {
            $query = $query->where('groups.sysid', 'like', '%' . $class_sysid . '%');
        }
        $query = $query->select('groups.id as gid', 'groups.sysid', 'groups.name as gname', 'teaching_plan.code',
            'teaching_plan.semester as tsemester')->groupBy('groups.id');

        return Datatables::of($query)
            ->add_column('selected',
                '<div align="center"><input type="radio" name="classId" value="{{{$gid}}}">
                    <input type="hidden" id="sm" value="{{{$tsemester}}}">
                </div>', 0)
            ->remove_column('gid')
            ->remove_column('tsemester')
            ->make();
    }

    public function getClassSelectionCourseData()
    {
        $year = Input::get('year');
        $teaching_plan_code = Input::get('teaching_plan_code');
        $campus_id = Session::get('campus_id');
        $qry_campus_course = DB::table('course_establish_campus')
            ->where('campus_id', $campus_id)->where('is_deleted', 0)->select('course_id');
        if (!empty($year)) {
            $qry_campus_course = $qry_campus_course->where('year', $year);
        }
        $campus_course = $qry_campus_course->lists('course_id');

        $query = DB::table('teaching_plan');

        $programs = $query
            ->join('teaching_plan_module', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->join('module_course', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0);
            })
            ->leftjoin('course', 'module_course.course_id', '=', 'course.id')
            ->select('course.id as cid', 'course.code', 'course.name', 'course.credit', 'course.classification',
                'module_course.suggested_semester', 'course.remark', 'module_course.is_obligatory')
            ->where('teaching_plan.code', $teaching_plan_code)
            ->whereIn('course.id', $campus_course)
            ->groupBy('course.id');


        return Datatables::of($programs)
            ->edit_column('classification', '@if($classification == \'14\')专科@else本科@endif')
            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}">
                    <input type="hidden" id="ob" value="{{{$is_obligatory}}}">
                </div>', 0)
            ->remove_column('cid')
            ->remove_column('is_obligatory')
            ->make();
    }

    public function getClassSelectionStudentData()
    {
        $ids = Input::get('checkitem');

        $campus_id = Session::get('campus_id');
        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->leftjoin('student_selection', function ($join) {
                $join->on('student_selection.student_id', '=', 'admissions.id')
                    ->where('student_selection.is_deleted', '=', 0);
            })
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
            ->where('admissions.campuscode', $campus_id)->where('groups.id', $ids[0]);

        $query = $query->select('admissions.id as aid', 'admissions.studentno', 'admissions.fullname',
            'course.code', 'course.name as cname','groups.sysid', 'student_selection.selection_status');


        return Datatables::of($query)

            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$aid}}}"></div>', 0)
            ->add_column('exam',
                '<div align="center"><select size="1">
                    <option value="是" selected="selected">是</option>
                    <option value="否">否</option></select>
                </div>', 7)
            ->remove_column('aid')

            ->make();
    }

    public function getBatchConfirmSelection()
    {
        $title = Lang::get('admin/select/title.batch_confirm_selection');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        return View::make('admin/select/batch_confirm_selection', compact('title', 'curInfo'));
    }

    public function getBatchConfirmSelectionRst() {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $campus_id = Session::get('campus_id');
        DB::table('student_selection')->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->where('admissions.campuscode', $campus_id)
            ->where('student_selection.year', $year)
            ->where('student_selection.semester', $semester)
            ->update(array('student_selection.selection_status' => 1));

        return 'ok';
    }

    public function getConfirmSelection() {
        $title = Lang::get ( 'admin/select/title.confirm_selection' );
        return View::make ( 'admin/select/confirm_selection', compact ('title') );
    }

    public function getConfirmSelectionData() {
        $student_no = Input::get('student_no');
        $type = Input::get('type');
        $campus_id = Session::get('campus_id');
        if ($type == 1) {
            $query = DB::table('admission_group')
                ->join('groups', 'admission_group.group_id', '=', 'groups.id')
                ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
                ->where('admissions.campuscode', $campus_id)
                ->where('admissions.studentno', $student_no);

            $query = $query->select('admissions.studentno', 'admissions.fullname', 'groups.sysid');

            return Datatables::of($query)
                ->add_column('balance',  '')
                ->make();
        }
        if ($type == 2) {
            $qry_campus_course = DB::table('course_establish_campus')
                ->where('campus_id', $campus_id)->where('is_deleted', 0)->select('course_id');
            $campus_course = $qry_campus_course->lists('course_id');
            $query = DB::table('student_selection')->where('student_selection.is_deleted', 0)
                ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
                ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
                ->where('admissions.campuscode', $campus_id)
                ->where('admissions.studentno', $student_no)
                ->whereNotIn('course.id', $campus_course);

            $query = $query->select('course.code', 'course.name as cname', 'course.credit',
                'student_selection.expense','student_selection.selection_status');


            return Datatables::of($query)
                ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
                ->make();
        }
        if ($type == 3) {
            $ids = Input::get('checkitem');
            if (!empty($ids)) {
                DB::table('student_selection')
                    ->whereIn('id', $ids)
                    ->update(array('selection_status' => 1));
            }
            $qry_campus_course = DB::table('course_establish_campus')
                ->where('campus_id', $campus_id)->where('is_deleted', 0)->select('course_id');
            $campus_course = $qry_campus_course->lists('course_id');
            $query = DB::table('student_selection')->where('student_selection.is_deleted', 0)
                ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
                ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
                ->where('admissions.campuscode', $campus_id)
                ->where('admissions.studentno', $student_no)
                ->whereIn('course.id', $campus_course);

            $query = $query->select('student_selection.id as sid', 'course.code', 'course.name as cname', 'course.credit',
                'student_selection.expense','student_selection.selection_status');




            return Datatables::of($query)
                ->add_column('checked',
                    '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$sid}}}"></div>', 0)
                ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
                ->remove_column('sid')
                ->make();
        }
    }

    public function getDeleteSelection() {
        $title = Lang::get ( 'admin/select/title.delete_selection' );
        return View::make ( 'admin/select/delete_selection', compact ('title') );
    }

    public function getDeleteSelectionData() {
        $student_no = Input::get('student_no');
        $type = Input::get('type');
        $campus_id = Session::get('campus_id');

        if ($type == 1) {
            $query = DB::table('admission_group')
                ->join('groups', 'admission_group.group_id', '=', 'groups.id')
                ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
                ->where('admissions.campuscode', $campus_id)
                ->where('admissions.studentno', $student_no)
                ->select('admissions.studentno', 'admissions.fullname', 'groups.sysid');

            return Datatables::of($query)
                ->make();
        }
        if ($type == 2) {

            $qry_campus_course = DB::table('course_establish_campus')
                ->where('campus_id', $campus_id)->where('is_deleted', 0)->select('course_id');
            $campus_course = $qry_campus_course->lists('course_id');
            $query = DB::table('student_selection')
                ->join('admissions', function ($join) {
                    $join->on('student_selection.student_id', '=', 'admissions.id')
                        ->where('student_selection.is_deleted', '=', 0);
                })
                ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
                ->where('admissions.campuscode', $campus_id)
                ->where('admissions.studentno', $student_no)
                //->whereIn('course.id', $campus_course)
                ->select('student_selection.id as sid', 'course.code', 'course.name as cname', 'course.credit',
                    'student_selection.selection_status');

            return Datatables::of($query)
                ->add_column('checked',
                    '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$sid}}}"></div>', 0)
                ->add_column('is_new','')
                ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
                ->remove_column('sid')
                ->make();
        }
    }

    public function getDeleteSelectionSubmit() {
        $selection_ids = Input::get('selections');

        DB::table('student_selection')
            ->whereIn('id', $selection_ids)
            ->update(array('is_deleted' => 1));

        return 'ok';

    }

    public function getRangeDeleteSelection() {
        $title = Lang::get ( 'admin/select/title.range_delete_selection' );
        return View::make ( 'admin/select/range_delete_selection', compact ('title') );
    }

    public function getRangeDeleteSelectionData() {
        $student_id_start = Input::get('student_id_start');
        $student_id_end = Input::get('student_id_end');
        $is_obligatory = Input::get('is_obligatory');
        $ids = Input::get('checkitem');
        $campus_id = Session::get('campus_id');
        if (!is_null($ids)) {
            DB::table('student_selection')
                ->whereIn('id', $ids)
                ->update(array('is_deleted' => 1));
        }

        $query = DB::table('admission_group')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->join('student_selection', function ($join) {
                $join->on('student_selection.student_id', '=', 'admissions.id')
                    ->where('student_selection.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('student_selection.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->where('admissions.campuscode', $campus_id);
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }
        if (!is_null($student_id_start) && !is_null($student_id_end)) {
            $query = $query->whereBetween('admissions.studentno', array($student_id_start, $student_id_end));
        }
        $query = $query->select('student_selection.id as sid', 'admissions.studentno', 'admissions.fullname', 'groups.sysid',
            'course.name', 'course.credit','student_selection.selection_status','student_selection.is_obligatory');

        return Datatables::of($query)
            ->add_column('checked', '@if($selection_status == \'0\')<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$sid}}}"></div>@endif')
            ->remove_column('sid')
            ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->make();

    }

    public function getNumberQuerySelection()
    {
        $title = Lang::get('admin/select/title.number_query_selection');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/number_query_selection', compact('title', 'cur_year'));
    }
    public function getNumberQuerySelectionData()
    {
        $student_id = Input::get('student_id');
        $major_classification = Input::get('major_classification');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');
        $campus_id = Session::get('campus_id');

        $query = DB::table('student_selection');
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }


        $query = $query
            ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id')
            ->where('admissions.campuscode', $campus_id);

        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($student_id)) {
            $query = $query->where('admissions.studentno', 'like', '%' . $student_id . '%');
        }

        $query = $query->select('admissions.studentno', 'admissions.fullname', 'course.code', 'course.name as cname',
            'course.credit', 'rawprograms.name as rname', 'student_selection.is_obligatory', 'student_selection.year',
            'student_selection.semester', 'student_selection.selection_status');

        return Datatables::of($query)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
            ->add_column('is_first', '')
            ->make();

    }
    public function getSummaryClassSelection() {
        $title = Lang::get ( 'admin/select/title.summary_class_selection' );
        $campus_id = Session::get('campus_id');
        $groups = DB::table('groups')->join('programs',  'groups.programs_id', '=', 'programs.id')
            ->where('programs.campus_id', $campus_id)
            ->select('groups.id as gid','groups.name as gname')->get();

        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/summary_class_selection', compact('title', 'groups', 'cur_year'));

    }
    public function getSummaryClassSelectionData()
    {
        $group_id = Input::get('group');
        $group_num = Input::get('group_num');
        $major_classification = Input::get('major_classification');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');
        $selection_status = Input::get('selection_status');
        $student_classification = Input::get('student_classification');
        $campus_id = Session::get('campus_id');
        $query = DB::table('student_selection')->where('student_selection.is_deleted', 0);
        if (!is_null($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!is_null($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }
        if (!is_null($selection_status) && $selection_status != "全部") {
            $query = $query->where('student_selection.selection_status', $selection_status);
        }

        $query = $query
            ->join('admission_group', 'student_selection.student_id', '=', 'admission_group.admission_id')
            ->join('groups', 'admission_group.group_id', '=', 'groups.id')
            ->join('admissions', 'admission_group.admission_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!is_null($campus_id)) {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!is_null($group_id) && $group_id != "全部") {
            $query = $query->where('groups.id', $group_id);
        }
        if (!is_null($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!is_null($group_num)) {
            $query = $query->where('groups.sysid', 'like', '%' . $group_num . '%');
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }

        $query = $query->select('admissions.studentno', 'admissions.fullname', 'course.code', 'course.name as cname',
            'course.credit', 'rawprograms.name as rname', 'student_selection.is_obligatory', 'student_selection.year',
            'student_selection.semester', 'campuses.name as cpname', 'groups.sysid as gsysid', 'groups.name as gname',
            'student_selection.selection_status');

        return Datatables::of($query)
            ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('selection_status', '@if($selection_status == \'0\')未确认@elseif($selection_status == \'1\')已确认@endif')
            ->add_column('is_first', '')
            ->make();
    }
    public function getSummaryTimesSelection()
    {
        $title = Lang::get('admin/select/title.summary_times_selection');
        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        $cur_semester = null;
        if ($curInfo != null) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
        }
        return View::make('admin/select/summary_times_selection', compact('title', 'b_majors', 'z_majors', 'cur_year', 'cur_semester'));

    }
    public function getSummaryTimesSelectionData()
    {
        $campus_id = Session::get('campus_id');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('course_code');
        $course_name = Input::get('course_name');
        $selection_status = Input::get('selection_status');
        $student_classification = Input::get('student_classification');


        $query = DB::table('student_selection')->where('student_selection.is_deleted', 0);
        if (!is_null($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!is_null($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($selection_status) && $selection_status != "全部") {
            $query = $query->where('student_selection.selection_status', $selection_status);
        }

        $query = $query
            ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!is_null($campus_id)) {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!empty($course_code)) {
            $query = $query->where('course.code', 'like', '%' . $course_code . '%');
        }
        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }

        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!is_null($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }


        $query = $query->select('student_selection.year', 'student_selection.semester', 'campuses.name as cpname',
            'admissions.admissionyear', 'admissions.admissionsemester', 'admissions.program', 'rawprograms.type',
            'rawprograms.name as rname', 'course.code', 'course.name as cname', 'course.credit',
            DB::raw('count(*) as number'), 'student_selection.selection_status')
            ->groupBy('campuses.name', 'course.code');

        return Datatables::of($query)
            ->edit_column('program', '@if($program == \'12\')本科@elseif($program == \'14\')专科@endif')
            ->edit_column('type', '@if($type == \'12\')本科@elseif($type == \'14\')专科@endif')
            ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
            ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
            ->add_column('total', function ($row){
                $sum = $row->credit * $row->number;
                return $sum;
            }, 12)
            ->make();

    }

    public function getCampusCountSelection()
    {
        $title = Lang::get('admin/select/title.campus_count_selection');
        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        $curInfo = ModuleCurrent::where('module_id', 3)->first();
        $cur_year = null;
        if ($curInfo != null)
            $cur_year = $curInfo->current_year;
        return View::make('admin/select/campus_count_selection', compact('title', 'b_majors', 'z_majors', 'cur_year'));
    }

    public function getCampusCountSelectionData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $campus_id = Session::get('campus_id');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('course_code');
        $course_name = Input::get('course_name');
        $major = Input::get('major');
        $is_obligatory = Input::get('is_obligatory');
        $year_in = Input::get('year_in');
        $semester_in = Input::get('semester_in');
        $selection_status = Input::get('selection_status');
        $credit = Input::get('credit');
        $student_classification = Input::get('student_classification');
        $type = Input::get('type');

        $query = DB::table('student_selection');
        if (!empty($year) && $year != "全部") {
            $query = $query->where('student_selection.year', 'like', '%' . $year . '%');
        }
        if (!empty($semester) && $semester != "全部") {
            $query = $query->where('student_selection.semester', $semester);
        }
        if (!is_null($is_obligatory) && $is_obligatory != "全部") {
            $query = $query->where('student_selection.is_obligatory', $is_obligatory);
        }
        if (!empty($selection_status) && $selection_status != "全部") {
            $query = $query->where('student_selection.selection_status', $selection_status);
        }

        $query = $query
            ->join('admissions', 'student_selection.student_id', '=', 'admissions.id')
            ->leftjoin('programs', 'admissions.programcode', '=', 'programs.id')
            ->leftjoin('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->leftjoin('campuses', 'admissions.campuscode', '=', 'campuses.id')
            ->leftjoin('course', 'student_selection.course_id', '=', 'course.id');
        if (!empty($campus_id) && $campus_id != "全部") {
            $query = $query->where('campuses.id', $campus_id);
        }
        if (!empty($course_code)) {
            $query = $query->where('course.code', 'like', '%' . $course_code . '%');
        }
        if (!empty($course_name)) {
            $query = $query->where('course.name', 'like', '%' . $course_name . '%');
        }
        if (!empty($credit)) {
            $query = $query->where('course.credit', $credit);
        }
        if (!empty($major_classification) && $major_classification != "全部") {
            $query = $query->where('rawprograms.type', $major_classification);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('rawprograms.name', $major);
        }
        if (!empty($year_in) && $year_in != "全部") {
            $query = $query->where('admissions.admissionyear', $year_in);
        }
        if (!empty($semester_in) && $semester_in != "全部") {
            $query = $query->where('admissions.admissionsemester', $semester_in);
        }
        if (!empty($student_classification) && $student_classification != "全部") {
            $query = $query->where('admissions.program', $student_classification);
        }

        if ($type == 1) {
            $query = $query->select('student_selection.year', 'student_selection.semester', 'admissions.program', 'rawprograms.type',
                'rawprograms.name as rname', 'course.code', 'course.name as cname', 'student_selection.is_obligatory',
                'student_selection.selection_status', 'admissions.admissionyear', 'admissions.admissionsemester', 'course.credit',
                DB::raw('count(*) as number'))
                ->groupBy('campuses.name', 'course.code');

            return Datatables::of($query)
                ->add_column('itemnumber', '', 0)
                ->edit_column('program', '@if($program == \'12\')本科@elseif($program == \'14\')专科@endif')
                ->edit_column('type', '@if($type == \'12\')本科@elseif($type == \'14\')专科@endif')
                ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
                ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
                ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
                ->make();
        } elseif ($type == 2) {
            $query = $query->select('admissions.studentno', 'admissions.fullname', 'student_selection.year',
                'student_selection.semester', 'admissions.program', 'rawprograms.type', 'rawprograms.name as rname',
                'course.code', 'course.name as cname', 'student_selection.is_obligatory', 'student_selection.selection_status',
                'admissions.admissionyear', 'admissions.admissionsemester', 'course.credit');

            return Datatables::of($query)
                ->edit_column('program', '@if($program == \'12\')本科@elseif($program == \'14\')专科@endif')
                ->edit_column('type', '@if($type == \'12\')本科@elseif($type == \'14\')专科@endif')
                ->edit_column('is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
                ->edit_column('semester', '@if($semester == \'1\')春季@elseif($semester == \'2\')秋季@endif')
                ->edit_column('admissionsemester', '@if($admissionsemester == \'1\')春季@elseif($admissionsemester == \'2\')秋季@endif')
                ->make();
        }
    }
}