<?php

class AdminCourseController extends AdminController {

    public function getIndex() {
        // Title
        $title = Lang::get('admin/course/title.make_course');

        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        // Show the page
        return View::make ( 'admin/course/index', compact ( 'title', 'b_majors', 'z_majors'));
    }

    /**
     * @return mixed
     */
    public function postIndex()
    {

    }

    public function getDataForAdd()
    {
        $filter = array();
        $filter_out = array();
        $code = Input::get('code');
        $name = Input::get('name');
        $abbreviation = Input::get('abbreviation');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $type = Input::get('state');
        $ids = Input::get('checkitem');
        if (!empty($code)) {
            $filter["course.code"] = '%'.$code.'%';
        }
        if (!empty($name)) {

            $filter["course.name"] = '%'.$name.'%';
        }
        if (!empty($abbreviation)) {
            $filter["abbreviation"] = '%'.$abbreviation.'%';
        }

        if (!empty($major) && $major != "全部") {
            $filter_out = array_add($filter_out, 'major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $filter_out = array_add($filter_out, 'major_classification', $major_classification);
        }

        if ($type == 0 || $type == 1) {
            if (!empty($ids)) {
                DB::table('course')
                    ->whereIn('id', $ids)
                    ->update(array('state' => $type));
            }
        }

        if (count($filter_out) == 0) {
            $programs = DB::table('course')->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach($filter as $key => $value)
                    {
                        $query = $query->where($key , 'like', $value);
                    }
                })
                ->leftjoin('department_info', function ($join) {
                    $join->on('course.department_id', '=', 'department_info.id')
                        ->where('department_info.is_deleted', '=', 0)
                        ->where('course.is_deleted', '=', 0);
                })
                ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation', 'credit', 'credit_hour', 'is_practice',
                    'lecturer', 'is_certification', 'define_date', 'remark', 'department_info.name as dname', 'classification', 'state');


        }
        else {
            $teaching_plan_ids = DB::table('teaching_plan')->where('is_deleted', '=', 0)->select('id')
                ->where($filter_out)->lists('id');
            $course_ids = DB::table('module_course')
                ->join('teaching_plan_module', function ($join) {
                    $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                        ->where('module_course.is_deleted', '=', 0)
                        ->where('teaching_plan_module.is_deleted', '=', 0);
                })
                ->select('course_id')->whereIn('teaching_plan_id', $teaching_plan_ids)->lists('course_id');
            $programs = DB::table('course')->whereIn('course.id', $course_ids)
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                })
                ->leftjoin('department_info', function ($join) {
                    $join->on('course.department_id', '=', 'department_info.id')
                        ->where('department_info.is_deleted', '=', 0)
                        ->where('course.is_deleted', '=', 0);
                })
                ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation', 'credit', 'credit_hour', 'is_practice',
                    'lecturer', 'is_certification', 'define_date', 'remark', 'department_info.name as dname', 'classification', 'state');

        }
        return Datatables::of ( $programs )
            ->edit_column ( 'is_practice', '@if($is_practice == \'0\')无@else有@endif')
            ->edit_column ( 'is_certification', '@if($is_certification == \'0\')无@else有@endif')
            ->edit_column ( 'classification', '@if($classification == \'14\')专科@else本科@endif')
            ->edit_column ( 'state', '@if($state == \'0\')停用@else启用@endif')
            ->add_column ( 'checked', '
                <div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}"></div>'
            )
            ->remove_column('cid')
            ->make ();
    }


    public function getCreate() {
        $title = Lang::get ( 'admin/course/title.course_add' );
        // Mode
        $mode = 'create';

        $departs = EduDepartment::all();

        return View::make ( 'admin/course/define_edit', compact ('title', 'mode', 'departs') );
    }

    public function postCreate()
    {
        $codes = DB::table('course')->select('code')->lists('code');
        $get = false;
        for ($code = 10001; $code <= 99999; $code++) {
            if (!in_array($code, $codes)) {
                $get = true;
                break;
            }
        }
        if ($get == false)
            retrun;
        $course = new Course();
        $course->code = $code;
        $course->name = Input::get('name');
        $course->abbreviation = Input::get('abbreviation');
        $course->credit = Input::get('credit');
        $course->credit_hour = Input::get('credit') * 36;
        $course->is_practice = Input::get('is_practice');
        $course->lecturer = Input::get('lecturer');
        $course->is_certification = Input::get('is_certification');
        $course->define_date = Input::get('define_date');
        $course->remark = Input::get('remark');
        $course->department_id = Input::get('department_id');
        $course->classification = Input::get('classification');
        $course->state = Input::get('state');

        if ($course->validate(Input::all())) {
            $course->save();
        } else {
            return Redirect::to('admin/course/data_add')->withInput()->withErrors($course->errors());
        }
        return Redirect::to('admin/course/data_add')->withInput()->with('success', Lang::get('admin/course/messages.create.course_success'));
    }

    public function getImportExcel($type){
        if ($type == "course")
            $title = '导入课程表';
        else if ($type == "teaching_plan")
            $title = '导入教学计划';
        else if ($type == "module")
            $title = '导入模块课程';
        return View::make ( 'admin/course/importExcel', compact ( 'title'));
    }

    public function postImportExcel($type)
    {
        $file = Input::file('file');
        $url = 'admin/course/' . $type . '/importExcel';
        //检验一下上传的文件是否有效.
        $rst = 0;
        if (!empty($file) && $file->isValid()) {

            Excel::load($file, function ($reader) use ($type, &$rst) {

                //获取excel的第几张表
                $reader = $reader->getSheet(0);
                $results = $reader->toArray("");
                $start = -1;
                $idx_ary = array();
                for ($i = 0; $i < count($results); $i++) {
                    for ($j = 0; $j < count($results[$i]); $j++) {
                        if ($results[$i][$j] != "") {
                            if (($type == "course" && $results[$i][$j] == Lang::get('admin/course/table.code'))
                                || ($type == "teaching_plan" && $results[$i][$j] == Lang::get('admin/course/table.teaching_plan_code'))
                                || ($type == "module" && $results[$i][$j] == Lang::get('admin/course/table.teaching_plan_code'))
                            ) {
                                $start = $i;
                            }
                            break 2;
                        }
                    }
                }

                if ($start != -1) {

                    $idx_ary = ($results[$start]);
                    foreach ($idx_ary as $k => $v) {
                        if (empty($v))
                            unset($idx_ary[$k]);
                    }
                    $idx_ary = array_flip($idx_ary);

                    for ($i = $start + 1; $i < count($results); $i++) {
                        if (empty($results[$i][$j]))
                            continue;
                        if ($type == "course") {
                            $course_code = $results[$i][$idx_ary[Lang::get('admin/course/table.code')]];
                            $course = Course::where('code', $course_code)->first();
                            if (is_null($course)) {
                                $course = new Course();
                                $course->code = $results[$i][$idx_ary[Lang::get('admin/course/table.code')]];
                            }
                            $department_code = $results[$i][$idx_ary[Lang::get('admin/course/title.manage')]];
                            $department = EduDepartment::where('code', $department_code)->first();
                            $course->name = $results[$i][$idx_ary[Lang::get('admin/course/table.name')]];
                            $course->abbreviation = $results[$i][$idx_ary[Lang::get('admin/course/table.abbreviation')]];
                            $course->credit = $results[$i][$idx_ary[Lang::get('admin/course/table.credit')]];
                            $course->credit_hour = $course->credit * 36;
                            $course->is_practice = $results[$i][$idx_ary[Lang::get('admin/course/table.is_practice')]];
                            $course->lecturer = $results[$i][$idx_ary[Lang::get('admin/course/table.lecturer')]];
                            $course->is_certification = $results[$i][$idx_ary[Lang::get('admin/course/table.is_certification')]];
                            $course->define_date = $results[$i][$idx_ary[Lang::get('admin/course/table.define_date')]];
                            $course->remark = $results[$i][$idx_ary[Lang::get('admin/course/table.remark')]];
                            if (is_null($department)){
                                $rst = 5;
                                return;
                            }
                            else
                                $course->department_id = $department->id;
                            $course->classification = $results[$i][$idx_ary[Lang::get('admin/course/title.classification')]];
                            $course->state = $results[$i][$idx_ary[Lang::get('admin/course/table.state')]];
                            $course->save();
                        } elseif ($type == "teaching_plan") {
                            $teaching_plan_code = $results[$i][$idx_ary[Lang::get('admin/course/table.teaching_plan_code')]];
                            $teachingPlan = TeachingPlan::where('code', $teaching_plan_code)->first();
                            if (is_null($teachingPlan)) {
                                $teachingPlan = new TeachingPlan();
                                $teachingPlan->code = $results[$i][$idx_ary[Lang::get('admin/course/table.teaching_plan_code')]];
                            } else {
                                DB::table('teaching_plan_module')
                                    ->where('teaching_plan_id', $teachingPlan->id)
                                    ->update(array('is_deleted' => 1));
                            }
                            $teachingPlan->student_classification = $results[$i][$idx_ary[Lang::get('admin/course/table.student_classification')]];
                            $teachingPlan->major_classification = $results[$i][$idx_ary[Lang::get('admin/course/title.classification')]];
                            $teachingPlan->major = $results[$i][$idx_ary[Lang::get('admin/course/title.major')]];
                            $teachingPlan->min_credit_graduation = $results[$i][$idx_ary[Lang::get('admin/course/table.min_credit_graduation')]];
                            $teachingPlan->schooling_period = $results[$i][$idx_ary[Lang::get('admin/course/table.schooling_period')]];
                            $teachingPlan->max_credit_exemption = $results[$i][$idx_ary[Lang::get('admin/course/table.max_credit_exemption')]];
                            $teachingPlan->max_credit_semester = $results[$i][$idx_ary[Lang::get('admin/course/table.max_credit_semester')]];
                            $teachingPlan->is_activated = $results[$i][$idx_ary[Lang::get('admin/course/table.is_activated')]];
                            if ($teachingPlan->is_activated == 1) {
                                $curInfo = ModuleCurrent::where('module_id', 2)->first();
                                if (!empty($curInfo)) {
                                    $teachingPlan->year = $curInfo->current_year;
                                    $teachingPlan->semester = $curInfo->current_semester;
                                }
                            }
                            $teachingPlan->save();

                            $module_codes = array();
                            $credits = array();
                            $min_credits = array();
                            $start_idx = $idx_ary[Lang::get('admin/course/table.teaching_plan_code')] + 9;
                            for ($k = $start_idx; $k < count($results[$i]); $k += 3) {
                                $module_codes[] = $results[$i][$k];
                                $credits[] = $results[$i][$k + 1];
                                $min_credits[] = $results[$i][$k + 2];
                            }
                            $cnt = count($module_codes);
                            for ($l = 0; $l < $cnt; $l++) {
                                $majorModuleInfo = MajorModuleInfo::where('code', $module_codes[$l])->first();
                                if ($majorModuleInfo != null) {
                                    $teachingPlanModule = TeachingPlanModule::where('teaching_plan_id', $teachingPlan->id)
                                        ->where('module_id', $majorModuleInfo->id)->first();
                                    if ($teachingPlanModule == null) {
                                        $teachingPlanModule = new TeachingPlanModule();
                                        $teachingPlanModule->teaching_plan_id = $teachingPlan->id;
                                        $teachingPlanModule->module_id = $majorModuleInfo->id;
                                    }
                                    $teachingPlanModule->credit = $credits[$l];
                                    $teachingPlanModule->min_credit = $min_credits[$l];
                                    $teachingPlanModule->is_deleted = 0;
                                    $teachingPlanModule->save();
                                }
                            }
                        } elseif ($type == "module") {

                            $teaching_plan_code = $results[$i][$idx_ary[Lang::get('admin/course/table.teaching_plan_code')]];
                            $teaching_plan = TeachingPlan::where('code', $teaching_plan_code)->first();
                            if (is_null($teaching_plan)) {
                                $rst = 1;
                                return;
                            }
                            $module_code = $results[$i][$idx_ary[Lang::get('admin/course/table.module_code')]];
                            $module_info = MajorModuleInfo::where('code', $module_code)->first();
                            if (is_null($module_info)) {
                                $rst = 2;
                                return;
                            }
                            $course_code = $results[$i][$idx_ary[Lang::get('admin/course/table.code')]];
                            $course = Course::where('code', $course_code)->first();
                            if (is_null($course)) {
                                $rst = 3;
                                return;
                            }
                            $teaching_plan_id = $teaching_plan->id;
                            $module_id = $module_info->id;
                            $course_id = $course->id;
                            $teaching_plan_module = TeachingPlanModule::where('teaching_plan_id', $teaching_plan_id)
                                ->where('module_id', $module_id)->where('is_deleted', 0)
                                ->first();
                            if (is_null($teaching_plan_module)) {
                                $rst = 4;
                                return;
                            }
                            $module_course = ModuleCourse::where('teaching_plan_module_id', $teaching_plan_module->id)
                                ->where('course_id', $course_id)
                                ->first();

                            if (is_null($module_course)) {
                                $module_course = new ModuleCourse();
                                $module_course->teaching_plan_module_id = $teaching_plan_module->id;
                                $module_course->course_id = $course_id;
                            }
                            $is_obligatory = $results[$i][$idx_ary[Lang::get('admin/course/table.is_obligatory')]];
                            $is_obligatory = ($is_obligatory == '必修' ? 1 : 0);
                            $module_course->is_obligatory = $is_obligatory;
                            $module_course->suggested_semester = $results[$i][$idx_ary[Lang::get('admin/course/table.suggested_semester')]];
                            $module_course->is_masked = $results[$i][$idx_ary[Lang::get('admin/course/table.is_masked')]];
                            $module_course->is_deleted = 0;
                            $module_course->save();
                        }
                    }
                }
            });
        }
        switch ($rst) {
            case 0:
                return Redirect::to($url)->with('success', Lang::get('admin/course/messages.import_success'));
            case 1:
                return Redirect::to($url)->withErrors(Lang::get('admin/course/title.teaching_plan_code_not_exist'));
            case 2:
                return Redirect::to($url)->withErrors(Lang::get('admin/course/title.module_code_not_exist'));
            case 3:
                return Redirect::to($url)->withErrors(Lang::get('admin/course/title.course_code_not_exist'));
            case 4:
                return Redirect::to($url)->withErrors(Lang::get('admin/course/title.teaching_plan_module_not_exist'));
            case 5:
                return Redirect::to($url)->withErrors(Lang::get('admin/course/title.department_code_not_exist'));
        }
    }

    public function getEstablish(){
        $title = Lang::get('admin/course/title.establish_province_course');

        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        // Show the page
        return View::make ( 'admin/course/establish', compact ( 'title', 'b_majors', 'z_majors'));
    }

    public function getEstablishData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('code');
        $student_classification = Input::get('student_classification');
        $type = Input::get('state');
        $ids = Input::get('checkitem');

        if ($type == 1) {
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $establish_province = EstablishProvince::where('course_id', $id)
                        ->where('year', $year)
                        ->where('semester', $semester)->first();
                    if ($establish_province == null)
                        $establish_province = new EstablishProvince();
                    $establish_province->year = $year;
                    $establish_province->semester = $semester;
                    $establish_province->course_id = $id;
                    $establish_province->is_deleted = 0;
                    $establish_province->save();
                }
            }
        }


        $query = DB::table('course_establish_province')->where('is_deleted',0)->select('course_id');
        if (!empty($year)) {
            $query = $query->where('year', 'like', '%'.$year.'%');
        }
        if (!empty($semester)) {
            $query = $query->where('semester', $semester);
        }
        $establish_ids = $query->lists('course_id');

        $query = DB::table('teaching_plan')->where('is_deleted',0)->select('id');
        if (!empty($year)) {
            $query = $query->where('year', 'like', '%'.$year.'%');
        }
        if (!empty($semester)) {
            $query = $query->where('semester', $semester);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('major_classification', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('student_classification', $student_classification);
        }
        $teaching_plan_ids = $query->lists('id');

        $programs = DB::table('module_course')
            ->join('teaching_plan_module', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0)
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->whereIn('teaching_plan_id', $teaching_plan_ids)
            ->whereNotIn('course_id', $establish_ids)
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('department_info', function ($join) {
                $join->on('course.department_id', '=', 'department_info.id')
                    ->where('department_info.is_deleted', '=', 0);
            })
            ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation', 'course.credit', 'credit_hour',
                'teaching_plan.student_classification', 'is_practice', 'lecturer', 'is_certification', 'define_date', 'remark',
                'department_info.name as dname', 'classification', 'state')
            ->groupBy('course.id');

        if (!empty($course_code)) {
            $programs = $programs->where('course.code', 'like', '%'.$course_code.'%');
        }

        return Datatables::of ( $programs )
            ->edit_column ( 'student_classification', '@if($student_classification == \'14\')专科@elseif($student_classification == \'12\')本科@endif')
            ->edit_column ( 'is_practice', '@if($is_practice == \'0\')无@elseif($is_practice == \'1\')有@endif')
            ->edit_column ( 'is_certification', '@if($is_certification == \'0\')无@elseif($is_certification == \'1\')有@endif')
            ->edit_column ( 'classification', '@if($classification == \'14\')专科@elseif($classification == \'12\')本科@endif')
            ->edit_column ( 'state', '@if($state == \'0\')停用@elseif($state == \'1\')启用@endif')
            ->add_column ( 'checked', '
                                <div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}"></div>'
            )
            ->remove_column('cid')
            ->make ();
    }

    public function getBrowse(){
        $title = Lang::get('admin/course/title.browse_province_course');

        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        // Show the page
        return View::make ( 'admin/course/establish_browse', compact ( 'title', 'b_majors', 'z_majors'));
    }

    public function getBrowseData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('code');
        $student_classification = Input::get('student_classification');
        $type = Input::get('state');
        $ids = Input::get('checkitem');

        if ($type == 1) {
            if (!empty($ids)) {
                DB::table('course_establish_province')
                    ->where('year', 'like', '%' . $year . '%')
                    ->where('semester', $semester)
                    ->whereIn('course_id', $ids)
                    ->update(array('is_deleted' => 1));
                DB::table('course_establish_campus')
                    ->where('year', 'like', '%' . $year . '%')
                    ->where('semester', $semester)
                    ->whereIn('course_id', $ids)
                    ->update(array('is_deleted' => 1));

            }
        }

        $qry_province_ids = DB::table('course_establish_province')->where('is_deleted', 0)->select('course_id');
        $qry_campus_ids = DB::table('course_establish_campus')->where('is_deleted', 0)->select('course_id');
        $qry_teaching_plan = DB::table('teaching_plan')->where('is_deleted', 0)->select('id');
        if (!empty($year)) {
            $qry_province_ids = $qry_province_ids->where('year', 'like', '%' . $year . '%');
            $qry_campus_ids = $qry_campus_ids->where('year', 'like', '%' . $year . '%');
            $qry_teaching_plan = $qry_teaching_plan->where('year', 'like', '%' . $year . '%');
        }
        if (!empty($semester)) {
            $qry_province_ids = $qry_province_ids->where('semester', $semester);
            $qry_campus_ids = $qry_campus_ids->where('semester', $semester);
            $qry_teaching_plan = $qry_teaching_plan->where('semester', $semester);
        }

        if (!empty($major) && $major != "全部") {
            $qry_teaching_plan = $qry_teaching_plan->where('major', $major);
        }
        if (!is_null($major_classification) && $major_classification != 2) {
            $qry_teaching_plan = $qry_teaching_plan->where('major_classification', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != 2) {
            $qry_teaching_plan = $qry_teaching_plan->where('student_classification', $student_classification);
        }
        $province_ids = $qry_province_ids->lists('course_id');
        $campus_ids = $qry_campus_ids->lists('course_id');
        $teaching_plan_ids = $qry_teaching_plan->lists('id');

        $programs = DB::table('module_course')
            ->join('teaching_plan_module', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0)
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->whereIn('teaching_plan_id', $teaching_plan_ids)
            ->whereIn('module_course.course_id', $province_ids)
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('department_info', function ($join) {
                $join->on('course.department_id', '=', 'department_info.id')
                    ->where('department_info.is_deleted', '=', 0);
            })
            ->leftjoin('student_selection', function ($join) {
                $join->on('course.id', '=', 'student_selection.course_id')
                    ->where('student_selection.is_deleted', '=', 0);
            })
            ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation', 'course.credit', 'credit_hour',
                'teaching_plan.student_classification', 'is_practice', 'lecturer', 'is_certification', 'define_date', 'remark',
                'department_info.name as dname', 'classification', 'state', DB::raw('count(distinct student_selection.student_id) as number'))
            ->groupBy('course.id');

        if (!empty($course_code)) {
            $programs = $programs->where('course.code', 'like', '%' . $course_code . '%');
        }

        return Datatables::of($programs)
            ->edit_column ( 'student_classification', '@if($student_classification == \'14\')专科@elseif($student_classification == \'12\')本科@endif')
            ->edit_column ( 'is_practice', '@if($is_practice == \'0\')无@elseif($is_practice == \'1\')有@endif')
            ->edit_column ( 'is_certification', '@if($is_certification == \'0\')无@elseif($is_certification == \'1\')有@endif')
            ->edit_column ( 'classification', '@if($classification == \'14\')专科@elseif($classification == \'12\')本科@endif')
            ->edit_column ( 'state', '@if($state == \'0\')停用@elseif($state == \'1\')启用@endif')
            ->add_column('is_establish_school', function ($row) use ($campus_ids) {
                if (in_array($row->cid, $campus_ids))
                    return '有';
                else
                    return '无';
            })
            ->add_column('checked', '<div align="center">
                <input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}">
                <input type="hidden" id="num" value="{{{$number}}}">
                </div>'
            )
            ->remove_column('cid')
            ->remove_column('number')
            ->make();
    }

    public function getNext(){
        $ids = Input::get('checkitem');
        if (!empty($ids)){
            $md = ModuleCurrent::where('module_id', 1)->first();
            if (!empty($md)){
                $save_year = $md->current_year;
                $save_semester = $md->current_semester;
                if ($save_semester == 1){
                    $save_semester = 2;
                }else{
                    $save_semester = 1;
                    $save_year++;
                }
                foreach ($ids as $id) {
                    $establish_province = EstablishProvince::where('course_id', $id)
                        ->where('year', $save_year)
                        ->where('semester', $save_semester)->first();
                    if ($establish_province == null)
                        $establish_province = new EstablishProvince();
                    $establish_province->year = $save_year;
                    $establish_province->semester = $save_semester;
                    $establish_province->course_id = $id;
                    $establish_province->is_deleted = 0;
                    $establish_province->save();
                }
                return 'ok';
            }
        }
        return 'err';
    }

    public function getEstablishSchool(){
        $title = Lang::get('admin/course/title.establish_school_course');
        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        // Show the page
        return View::make ( 'admin/course/establish_school', compact ( 'title', 'b_majors', 'z_majors'));
    }

    public function getEstablishSchoolData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('code');
        $student_classification = Input::get('student_classification');
        $type = Input::get('state');
        $ids = Input::get('checkitem');
        $campus_id = Session::get('campus_id');
        if ($type == 1) {
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $establish_campus = EstablishCampus::where('course_id', $id)
                        ->where('campus_id', $campus_id)
                        ->where('year', $year)
                        ->where('semester', $semester)->first();
                    if ($establish_campus == null)
                        $establish_campus = new EstablishCampus();
                    $establish_campus->year = $year;
                    $establish_campus->semester = $semester;
                    $establish_campus->course_id = $id;
                    $establish_campus->campus_id = $campus_id;
                    $establish_campus->is_deleted = 0;
                    $establish_campus->save();
                }
            }
        }

        $qryProvince = DB::table('course_establish_province')->where('is_deleted', 0)->select('course_id');
        $qrySchool = DB::table('course_establish_campus')->where('campus_id', $campus_id)
            ->where('is_deleted', 0)->select('course_id');
        if (!empty($year)) {
            $qryProvince = $qryProvince->where('year', 'like', '%' . $year . '%');
            $qrySchool = $qrySchool->where('year', 'like', '%' . $year . '%');
        }
        if (!empty($semester)) {

            $qryProvince = $qryProvince->where('semester', $semester);
            $qrySchool = $qrySchool->where('semester', $semester);
        }
        $establish_province_ids = $qryProvince->lists('course_id');
        $establish_school_ids = $qrySchool->lists('course_id');

        $query = DB::table('teaching_plan')->select('id');
        if (!empty($year)) {
            $query = $query->where('year', 'like', '%' . $year . '%');
        }
        if (!empty($semester)) {
            $query = $query->where('semester', $semester);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('major_classification', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('student_classification', $student_classification);
        }
        $teaching_plan_ids = $query->lists('id');

        $programs = DB::table('module_course')
            ->join('teaching_plan_module', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0)
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->whereIn('teaching_plan_id', $teaching_plan_ids)
            ->whereIn('course_id', $establish_province_ids)
            ->whereNotIn('course_id', $establish_school_ids)
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('department_info', function ($join) {
                $join->on('course.department_id', '=', 'department_info.id')
                    ->where('department_info.is_deleted', '=', 0);
            })
            ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation', 'course.credit', 'credit_hour',
                'teaching_plan.student_classification', 'is_practice', 'lecturer', 'is_certification', 'define_date', 'remark',
                'department_info.name as dname', 'classification', 'state')
            ->groupBy('course.id');

        if (!empty($course_code)) {
            $programs = $programs->where('course.code', 'like', '%' . $course_code . '%');
        }

        return Datatables::of($programs)
            ->edit_column('student_classification', '@if($student_classification == \'14\')专科@elseif($student_classification == \'12\')本科@endif')
            ->edit_column('is_practice', '@if($is_practice == \'0\')无@elseif($is_practice == \'1\')有@endif')
            ->edit_column('is_certification', '@if($is_certification == \'0\')无@elseif($is_certification == \'1\')有@endif')
            ->edit_column('classification', '@if($classification == \'14\')专科@elseif($classification == \'12\')本科@endif')
            ->edit_column('state', '@if($state == \'0\')停用@elseif($state == \'1\')启用@endif')
            ->add_column('checked', '
                <div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}"></div>'
            )
            ->remove_column('cid')
            ->make();
    }

    public function getSchoolBrowse(){
        $title = Lang::get('admin/course/title.browse_school_course');

        $campus_id = Session::get('campus_id');
        $b_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '12')->select('rawprograms.name')->lists('rawprograms.name');
        $z_majors = DB::table('programs')
            ->join('rawprograms', 'programs.name', '=', 'rawprograms.id')
            ->where('campus_id', $campus_id)
            ->where('rank', '14')->select('rawprograms.name')->lists('rawprograms.name');
        // Show the page
        return View::make ( 'admin/course/establish_school_browse', compact ( 'title', 'b_majors', 'z_majors'));
    }

    public function getSchoolBrowseData()
    {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $course_code = Input::get('code');
        $student_classification = Input::get('student_classification');
        $type = Input::get('state');
        $ids = Input::get('checkitem');
        $in_schools = Input::get('school');
        $campus_id = Session::get('campus_id');

        if ($type == 1) {
            if (!empty($ids)) {
                DB::table('course_establish_campus')
                    ->where('year', 'like', '%' . $year . '%')
                    ->where('semester', $semester)
                    ->where('campus_id', $campus_id)
                    ->whereIn('course_id', $ids)
                    ->update(array('is_deleted' => 1));
            }
        }

        $qry_school_ids = DB::table('course_establish_campus')->where('campus_id', $campus_id)
            ->where('is_deleted', 0)->select('course_id');
        $qry_program = DB::table('teaching_plan')->select('id');
        if (!empty($year)) {

            $qry_school_ids = $qry_school_ids->where('year', 'like', '%' . $year . '%');
            $qry_program = $qry_program->where('year', 'like', '%' . $year . '%');
        }
        if (!empty($semester)) {

            $qry_school_ids = $qry_school_ids->where('semester', $semester);
            $qry_program = $qry_program->where('semester', $semester);
        }
        if (!empty($major) && $major != "全部") {
            $qry_program = $qry_program->where('major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $qry_program = $qry_program->where('major_classification', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $qry_program = $qry_program->where('student_classification', $student_classification);
        }
        $school_ids = $qry_school_ids->lists('course_id');
        $teaching_plan_ids = $qry_program->lists('id');


        $programs = DB::table('module_course')
            ->join('teaching_plan_module', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('module_course.is_deleted', '=', 0)
                    ->where('teaching_plan_module.is_deleted', '=', 0);
            })
            ->whereIn('teaching_plan_id', $teaching_plan_ids)
            ->whereIn('module_course.course_id', $school_ids)
            ->leftjoin('teaching_plan', function ($join) {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id')
                    ->where('teaching_plan.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->leftjoin('department_info', function ($join) {
                $join->on('course.department_id', '=', 'department_info.id')
                    ->where('department_info.is_deleted', '=', 0);
            })
            ->leftjoin('student_selection', function ($join) {
                $join->on('course.id', '=', 'student_selection.course_id')
                    ->where('student_selection.is_deleted', '=', 0);
            })
            ->select('course.id as cid', 'course.code', 'course.name as cname', 'abbreviation',
                'course.credit', 'credit_hour', 'is_practice', 'lecturer', 'is_certification',
                'define_date', 'remark', 'department_info.name as dname', 'classification', 'state',
                DB::raw('count(distinct student_selection.student_id) as number'))
            ->groupBy('course.id');

        if (!empty($course_code)) {
            $programs = $programs->where('course.code', 'like', '%' . $course_code . '%');
        }

        return Datatables::of($programs)
            ->edit_column('is_practice', '@if($is_practice == \'0\')无@elseif($is_practice == \'1\')有@endif')
            ->edit_column('is_certification', '@if($is_certification == \'0\')无@elseif($is_certification == \'1\')有@endif')
            ->edit_column('classification', '@if($classification == \'14\')专科@elseif($classification == \'12\')本科@endif')
            ->edit_column('state', '@if($state == \'0\')停用@elseif($state == \'1\')启用@endif')
            ->add_column('number', '@if($number >  0)有@else无@endif')
            ->add_column('checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$cid}}}"></div>'
            )
            ->remove_column('cid')
            ->make();
    }

    public function getSchoolNext(){
        $ids = Input::get('checkitem');
        $campus_id = Session::get('campus_id');
        if (!empty($ids)){
            $md = ModuleCurrent::where('module_id', 1)->first();
            if (!empty($md)){
                $save_year = $md->current_year;
                $save_semester = $md->current_semester;
                if ($save_semester == 1){
                    $save_semester = 2;
                }else{
                    $save_semester = 1;
                    $save_year++;
                }
                foreach ($ids as $id) {
                    $establish_campus = EstablishCampus::where('course_id', $id)
                        ->where('campus_id', $campus_id)
                        ->where('year', $save_year)
                        ->where('semester', $save_semester)->first();
                    if ($establish_campus == null)
                        $establish_campus = new EstablishCampus();
                    $establish_campus->year = $save_year;
                    $establish_campus->semester = $save_semester;
                    $establish_campus->course_id = $id;
                    $establish_campus->campus_id = $campus_id;
                    $establish_campus->is_deleted = 0;
                    $establish_campus->save();
                }
                return 'ok';
            }
        }
        return 'err';
    }

    public function getUpdateSemester()
    {
        $title = Lang::get('admin/course/title.update_course_year');

        $curInfo = ModuleCurrent::where('module_id', 1)->first();

        return View::make('admin/course/update_semester', compact('title', 'curInfo'));
    }

    public function getUpdateYearSemester() {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $curInfo = ModuleCurrent::where('module_id', 1)->first();
        if (!empty($curInfo)) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
            $records = EstablishProvince::where('year', $cur_year)->where('semester', $cur_semester)->get();
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

    public function getTeachingPlanSemester()
    {
        $title = Lang::get('admin/course/title.update_teaching_plan_year');

        $curInfo = ModuleCurrent::where('module_id', 2)->first();

        return View::make('admin/course/update_teaching_plan_semester', compact('title', 'curInfo'));
    }

    public function getTeachingPlanSemesterRst() {
        $year = Input::get('year');
        $semester = Input::get('semester');
        $curInfo = ModuleCurrent::where('module_id', 2)->first();
        if (!empty($curInfo)) {
            $cur_year = $curInfo->current_year;
            $cur_semester = $curInfo->current_semester;
            $records = TeachingPlan::where('year', $cur_year)->where('semester', $cur_semester)->get();
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

    public function getTeachingPlan(){
        $title = Lang::get('admin/course/title.make_teaching_plan');
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        // Show the page
        return View::make ( 'admin/course/teaching_plan', compact ( 'title', 'b_majors', 'z_majors'));
    }

    public function getTeachingPlanData()
    {
        $teaching_plan_code = Input::get('teaching_plan_code');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $student_classification = Input::get('student_classification');
        $is_activated = Input::get('is_activated');
        $type = Input::get('state');
        $ids = Input::get('checkitem');

        if ($type == 0 || $type == 1) {
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $teachingPlan = TeachingPlan::find($id);
                    if (isset ($teachingPlan) && $teachingPlan->id) {
                        $teachingPlan->is_activated = $type;
                        if ($type == 1) {
                            $curInfo = ModuleCurrent::where('module_id', 2)->first();
                            if (!empty($curInfo)) {
                                $teachingPlan->year = $curInfo->current_year;
                                $teachingPlan->semester = $curInfo->current_semester;
                            }
                        }
                        $teachingPlan->save();
                    }
                }
            }
        }

        $query = DB::table('teaching_plan')->where('is_deleted', 0)
            ->select('id', 'code', 'student_classification', 'major_classification','major', 'min_credit_graduation',
                'schooling_period', 'max_credit_exemption','max_credit_semester', 'is_activated');
        if (!empty($year)) {
            $query = $query->where('year', 'like', '%' . $year . '%');
        }
        if (!is_null($semester) && $semester != "全部") {
            $query = $query->where('semester', $semester);
        }
        if (!empty($major) && $major != "全部") {
            $query = $query->where('major', $major);
        }
        if (!is_null($major_classification) && $major_classification != "全部") {
            $query = $query->where('major_classification', $major_classification);
        }
        if (!is_null($student_classification) && $student_classification != "全部") {
            $query = $query->where('student_classification', $student_classification);
        }
        if (!is_null($is_activated) && $is_activated != "全部") {
            $query = $query->where('is_activated', $is_activated);
        }
        if (!empty($teaching_plan_code)) {
            $query = $query->where('code', 'like', '%' . $teaching_plan_code . '%');
        }

        return Datatables::of ( $query )
            ->edit_column ( 'student_classification', '@if($student_classification == \'14\')专科@elseif($student_classification == \'12\')本科@endif')
            ->edit_column ( 'major_classification', '@if($major_classification == \'14\')专科@elseif($major_classification == \'12\')本科@endif')
            ->edit_column ( 'is_activated', '@if($is_activated == \'0\')关@elseif($is_activated == \'1\')启@endif')
            ->add_column ( 'checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$id}}}"></div>',
                0
            )
            ->add_column ( 'actions', '
                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/module_query\' ) }}}" class="iframe_m btn btn-xs">{{{ Lang::get(\'admin/course/title.query_module\') }}}</a>
                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/teaching_plan_query\' ) }}}" class="iframe_m btn btn-xs">{{{ Lang::get(\'admin/course/title.query_teaching_plan\') }}}</a>
                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/module_course\' ) }}}" class="btn btn-xs">{{{ Lang::get(\'admin/course/title.add_module\') }}}</a>
                '
            )
            ->remove_column('id')
            ->make ();
    }

    public function getTeachingPlanCreate() {
        $title = Lang::get ( 'admin/course/title.teaching_plan_add' );
        $b_majors = RawProgram::where('type', '12')->lists('name');
        $z_majors = RawProgram::where('type', '14')->lists('name');
        $modules = MajorModuleInfo::all();
        $credits = Input::old('credit', array());
        $min_credits = Input::old('min_credit_module', array());
        // Show the page
        return View::make ( 'admin/course/teaching_plan_create', compact ( 'title', 'b_majors', 'z_majors','modules','credits', 'min_credits'));
    }

    public function postTeachingPlanCreate() {
        //prepare model and save
        $is_act = Input::get('is_activated');
        $teachingPlan = new TeachingPlan();
        $teachingPlan->code = Input::get('code');
        $teachingPlan->student_classification = Input::get('student_classification');
        $teachingPlan->major_classification = Input::get('major_classification');
        $teachingPlan->major = Input::get('major');
        $teachingPlan->min_credit_graduation = Input::get('min_credit_graduation');
        $teachingPlan->schooling_period = Input::get('schooling_period');
        $teachingPlan->max_credit_exemption = Input::get('max_credit_exemption');
        $teachingPlan->max_credit_semester = Input::get('max_credit_semester');
        $teachingPlan->is_activated = Input::get('is_activated');
        if ($is_act == 1){
            $curInfo = ModuleCurrent::where('module_id', 2)->first();
            if (!empty($curInfo)){
                $teachingPlan->year = $curInfo->current_year;
                $teachingPlan->semester = $curInfo->current_semester;
            }
        }

        $ids = Input::get('checkitem');
        $chks = Input::get('chk');
        $credits = Input::get('credit');
        $min_credits_module = Input::get('min_credit_module');

        if ($teachingPlan->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('teaching_plan')->where('code', '=', Input::get('code'))->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/course/teaching_plan_add')->withInput()->withErrors(Lang::get ( 'admin/course/title.teaching_plan_code_exist'));
            }
            $teachingPlan->save();
            if (!empty($chks)) {
                $chk_array = explode(",", $chks);
                for ($i = 0; $i < count($chk_array) - 1; $i++) {
                    if ($chk_array[$i] != 0) {
                        $teachingPlanModule = new TeachingPlanModule();
                        $teachingPlanModule->teaching_plan_id = $teachingPlan->id;
                        $teachingPlanModule->module_id = $chk_array[$i];
                        $teachingPlanModule->credit = $credits[$i];
                        $teachingPlanModule->min_credit = $min_credits_module[$i];
                        $teachingPlanModule->save();
                    }
                }
            }
        } else {
            return Redirect::to('admin/course/teaching_plan_add')->withInput()->withErrors($program->errors());
        }
        return Redirect::to('admin/course/teaching_plan_add')->withInput()->with('success', Lang::get('admin/course/messages.create.teaching_plan_success'));
    }

    public function getModules($id)
    {
        $teachingPlan = TeachingPlan::find($id);
        if (!isset($teachingPlan))
            return;
        $major = $teachingPlan->major;
        $teachingPlan_code = $teachingPlan->code;
        $title = $major . " 专业模块";
        $rsts = DB::table('teaching_plan_module')->where('teaching_plan_id', $id)
            ->where('teaching_plan_module.is_deleted', 0)
            ->leftjoin('major_module_info', function ($join) {
                $join->on('teaching_plan_module.module_id', '=', 'major_module_info.id')
                    ->where('major_module_info.is_deleted', '=', 0);
            })
            ->leftjoin('module_course', function ($join) {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id')
                    ->where('is_obligatory', '=', 1)->where('module_course.is_deleted', '=', 0);
            })
            ->leftjoin('course', function ($join) {
                $join->on('module_course.course_id', '=', 'course.id')
                    ->where('course.is_deleted', '=', 0);
            })
            ->select(DB::raw('major_module_info.name as mname, sum(course.credit) as scredit, teaching_plan_module.min_credit as mcredit'))
            ->groupBy('major_module_info.name')
            ->get();
        // Show the page
        return View::make('admin/course/teaching_plan_module', compact('title', 'teachingPlan_code', 'rsts'));
    }

    public function getMajorTeachingPlan($id)
    {
        $teachingPlan = TeachingPlan::find($id);
        if (!isset($teachingPlan))
            return;
        $major = $teachingPlan->major;
        $teachingPlan_code = $teachingPlan->code;
        $title = $major." 专业";
        $rsts = DB::table('teaching_plan')->where('major', $major)
            ->select('code','year', 'semester','major', 'is_activated','major_classification','student_classification')
            ->get();
        // Show the page
        return View::make('admin/course/teaching_plan_major', compact('title', 'rsts'));
    }

    public function getModuleCourse($id)
    {
        $title = "添加模块课程";
        $rst = DB::table('teaching_plan')->where('id', $id)
            ->select('code','major_classification', 'major','min_credit_graduation','schooling_period')
            ->first();
        // Show the page
        return View::make('admin/course/module_course', compact('title', 'id', 'rst'));
    }

    public function getModuleDataForAdd()
    {
        $teaching_plan_id = Input::get('teaching_plan_id');
        $type = Input::get('state');
        $ids = Input::get('checkitem');

        if ($type == 0 || $type == 1) {
            if (!empty($ids)) {
                DB::table('module_course')
                    ->whereIn('id', $ids)
                    ->update(array('is_masked' => $type));
            }
        }

        $query = DB::table('teaching_plan_module')->where('teaching_plan_id', $teaching_plan_id)
            ->where('teaching_plan_module.is_deleted', 0)
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
            ->select('module_course.id as mid', 'major_module_info.name as mname','course.code', 'course.name as cname',
                'is_obligatory', 'course.credit','suggested_semester', 'is_masked');


        return Datatables::of ( $query )
            ->edit_column ( 'is_obligatory', '@if($is_obligatory == \'1\')必修@elseif($is_obligatory == \'0\')选修@endif')
            ->edit_column ( 'is_masked', '@if($is_masked == \'1\')是@elseif($is_masked == \'0\')否@endif')
            ->add_column ( 'checked',
                '<div align="center"><input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$mid}}}"></div>',
                0
            )
            ->remove_column('mid')
            ->make ();
    }

    public function getModuleCourseCreate($id)
    {
        $title = "输入添加模块课程";
        $moduleInfos = DB::table('teaching_plan_module')->where('teaching_plan_id', $id)
            ->where('teaching_plan_module.is_deleted', 0)
            ->join('major_module_info', 'teaching_plan_module.module_id', '=', 'major_module_info.id')
            ->select('major_module_info.id as mid', 'name','code')
            ->get();
        // Show the page
        return View::make('admin/course/module_course_create', compact('title', 'moduleInfos'));
    }

    public function postModuleCourseCreate($id)
    {
        $url = 'admin/course/' . $id . '/module_course_add';
        $module_id = Input::get('module_id');

        if (empty($module_id)) {
            return Redirect::to($url)->withInput()->withErrors(Lang::get('admin/course/title.select_module'));
        }
        $course_code = Input::get('course_code');
        $course = Course::where('code', $course_code)->first();
        if (is_null($course)){
            return Redirect::to($url)->withInput()->withErrors(Lang::get('admin/course/messages.create.course_code_not_exist'));
        }
        $course_id = $course->id;
        $teachingPlan_id = $id;

        $course_name = Input::get('course_name');
        $is_obligatory = Input::get('is_obligatory');
        $course_credit = Input::get('course_credit');
        $suggested_semester = Input::get('suggested_semester');
        $is_masked = Input::get('is_masked');

        $teaching_plan_module = TeachingPlanModule::where('teaching_plan_id', $teachingPlan_id)
            ->where('module_id', $module_id)
            ->first();
        if (is_null($teaching_plan_module)){
            return Redirect::to($url)->withInput()->withErrors(Lang::get('admin/course/messages.create.teaching_plan_module_not_exist'));
        }

        $module_course = ModuleCourse::where('teaching_plan_module_id', $teaching_plan_module->id)
            ->where('course_id', $course_id)
            ->first();

        if (empty($module_course)) {
            $module_course = new ModuleCourse();
            $module_course->teaching_plan_module_id = $teaching_plan_module->id;
            $module_course->course_id = $course_id;
        }

        if ($module_course->validate(Input::all())) {
            //verify overall
            $module_course->is_obligatory = $is_obligatory;
            $module_course->suggested_semester = $suggested_semester;
            $module_course->is_masked = $is_masked;
            $module_course->save();

        } else {
            return Redirect::to($url)->withInput()->withErrors($module_course->errors());
        }
        return Redirect::to($url)->withInput()->with('success', Lang::get('admin/course/messages.create.module_success'));
    }

    public function getQueryCourse(){
        $course_code = Input::get('course_code');
        if (!empty($course_code)){
            $course = Course::where('code', $course_code)->first();
            if (isset ( $course )) {
                $str = $course->credit.'|'.$course->name;
                return $str;
            }
        }
        return 'err';
    }

    public function getDepartmentDefine() {
        // Title
        $title = Lang::get ( 'admin/course/title.department_define' );

        // Show the page
        return View::make ( 'admin/course/department_define', compact ( 'title'));
    }
    public function getDepartmentData() {
        $programs = DB::table ('department_info')->select( 'id', 'name', 'code');
        return Datatables::of ( $programs )
            ->add_column('itemnumber', '', 0)
            ->add_column ( 'actions', '
                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/department_edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/department_delete\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.delete\') }}}</a>
                                '
            )
            ->remove_column('id')
            ->make ();
    }
    public function getDepartmentCreate() {
        $title = Lang::get ( 'admin/course/title.department_input' );
        $mode = 'create';
        return View::make ( 'admin/course/department_define_edit', compact ('title', 'mode') );
    }
    public function postDepartmentCreate()
    {
        $department = new EduDepartment();
        $department->name = Input::get('name');
        $department->code = Input::get('code');

        if ($department->validate(Input::all())) {
            //verify overall
            $record_exists = DB::table('department_info')
                ->where('name', Input::get('name'))
                ->orWhere('code', Input::get('code'))
                ->count();
            if ($record_exists > 0) {
                return Redirect::to('admin/course/department_create')->withInput()
                    ->withErrors(Lang::get('admin/course/messages.create.department_code_exist'));
            }
            $department->save();
        } else {
            return Redirect::to('admin/course/department_create')->withInput()->withErrors($department->errors());
        }
        return Redirect::to('admin/course/department_create')->withInput()->with('success', Lang::get('admin/course/messages.create.department_success'));
    }
    public function getDepartmentEdit($id) {

        $title = Lang::get ( 'admin/course/title.department_input' );
        // Mode
        $mode = 'edit';
        $department = EduDepartment::find ( $id );

        if (isset ( $department ) && $department->id) {
            return View::make ( 'admin/course/department_define_edit', compact ( 'title', 'mode', 'department') );
        } else {
            return Redirect::to('admin/course/department_define')
                ->withError (Lang::get('admin/course/messages.update.department_id_not_exist'));
        }
    }
    public function postDepartmentEdit($id) {
        $department = EduDepartment::find ( $id );
        if (isset ( $department ) && $department->id) {
            $department->name = Input::get('name');
            $department->code = Input::get('code');

            if ($department->validate(Input::all())) {
                $record_exists = DB::table('department_info')
                    ->whereRaw('id != ? and (name = ? or code = ?)',
                        array($id, Input::get('name'), Input::get('code')))
                    ->count();
                if ($record_exists > 0) {
                    return Redirect::to('admin/course/'.$id.'/department_edit')->withInput()
                        ->withErrors(Lang::get('admin/course/messages.create.department_code_exist'));
                }
                $department->save();
            } else {
                return Redirect::to('admin/course/'.$id.'/department_edit')->withInput()->withErrors($department->errors());
            }
            return Redirect::to('admin/course/'.$id.'/department_edit')->withInput()->with('success', Lang::get('admin/course/messages.update.department_success'));

        }
    }
    public function getDepartmentDelete($id) {

        $title = Lang::get ( 'admin/course/title.department_delete' );
        $department = EduDepartment::find ( $id );
        if (isset ( $department ) && $department->id) {
            return View::make ( 'admin/course/department_delete', compact ( 'title') );
        } else {
            return Redirect::to('admin/course/department_define')
                ->withError (Lang::get('admin/course/messages.update.department_id_not_exist'));
        }
    }
    public function postDepartmentDelete($id)
    {
        $department = EduDepartment::find($id);
        if (isset ($department) && $department->id) {
            $department->delete();
            return Redirect::to('admin/course/department_define')
                ->with('success', Lang::get('admin/course/messages.update.department_success'));
        } else {
            return Redirect::to('admin/course/department_define')->withErrors($department->errors());
        }
    }

    public function getModuleDefine() {
        // Title
        $title = Lang::get ( 'admin/course/title.module_define' );

        // Show the page
        return View::make ( 'admin/course/module_define', compact ( 'title'));
    }
    public function getModuleData() {
        $programs = DB::table ('major_module_info')->select( 'id', 'name', 'code');
        return Datatables::of ( $programs )
            ->add_column('itemnumber', '', 0)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/module_edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/course/\' . $id . \'/module_delete\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.delete\') }}}</a>
                                '
            )
            ->remove_column('id')
            ->make ();
    }
    public function getModuleCreate() {
        $title = Lang::get ( 'admin/course/title.module_input' );
        $mode = 'create';
        return View::make ( 'admin/course/module_define_edit', compact ('title', 'mode') );
    }
    public function postModuleCreate()
    {
        $module = new MajorModuleInfo();
        $module->name = Input::get('name');
        $module->code = Input::get('code');

        if ($module->validate(Input::all())) {
            //verify overall
            $record_exists = DB::table('major_module_info')
                ->where('name', Input::get('name'))
                ->orWhere('code', Input::get('code'))
                ->count();
            if ($record_exists > 0) {
                return Redirect::to('admin/course/module_create')->withInput()
                    ->withErrors(Lang::get('admin/course/messages.create.module_code_exist'));
            }
            $module->save();
        } else {
            return Redirect::to('admin/course/module_create')->withInput()->withErrors($module->errors());
        }
        return Redirect::to('admin/course/module_create')->withInput()->with('success', Lang::get('admin/course/messages.create.module_success'));
    }
    public function getModuleEdit($id) {

        $title = Lang::get ( 'admin/course/title.module_input' );
        // Mode
        $mode = 'edit';
        $module = MajorModuleInfo::find ( $id );

        if (isset ( $module ) && $module->id) {
            return View::make ( 'admin/course/module_define_edit', compact ( 'title', 'mode', 'module') );
        } else {
            return Redirect::to('admin/course/module_define')
                ->withError (Lang::get('admin/course/messages.update.module_id_not_exist'));
        }
    }
    public function postModuleEdit($id) {
        $module = MajorModuleInfo::find ( $id );
        if (isset ( $module ) && $module->id) {
            $module->name = Input::get('name');
            $module->code = Input::get('code');

            if ($module->validate(Input::all())) {
                $record_exists = DB::table('major_module_info')
                    ->whereRaw('id != ? and (name = ? or code = ?)',
                        array($id, Input::get('name'), Input::get('code')))
                    ->count();
                if ($record_exists > 0) {
                    return Redirect::to('admin/course/'.$id.'/module_edit')->withInput()
                        ->withErrors(Lang::get('admin/course/messages.create.module_code_exist'));
                }
                $module->save();
            } else {
                return Redirect::to('admin/course/'.$id.'/module_edit')->withInput()->withErrors($module->errors());
            }
            return Redirect::to('admin/course/'.$id.'/module_edit')->withInput()->with('success', Lang::get('admin/course/messages.update.module_success'));

        }
    }
    public function getModuleDelete($id) {

        $title = Lang::get ( 'admin/course/title.module_delete' );
        $module = MajorModuleInfo::find ( $id );
        if (isset ( $module ) && $module->id) {
            return View::make ( 'admin/course/module_delete', compact ( 'title') );
        } else {
            return Redirect::to('admin/course/module_define')
                ->withError (Lang::get('admin/course/messages.update.module_id_not_exist'));
        }
    }
    public function postModuleDelete($id)
    {
        $module = MajorModuleInfo::find($id);
        if (isset ($module) && $module->id) {
            $module->delete();
            return Redirect::to('admin/course/module_define')
                ->with('success', Lang::get('admin/course/messages.update.module_success'));
        } else {
            return Redirect::to('admin/course/module_define')->withErrors($module->errors());
        }
    }
}