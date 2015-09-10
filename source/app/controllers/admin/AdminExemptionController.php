<?php

class AdminExemptionController extends AdminController
{
    protected $exemption;

    /**
     * User Model
     * @var User
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/exemption/title.manageexemption');
        $rawprograms = RawProgram::All();

        return View::make('admin/exemption/index', compact('rawprograms','title'));
    }

    public function getDataForCampus()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
                   $filter = array();
                   $filter_program = array();
                   $filter_student = array();
                   $student_ids = array();
                   $student_ids1 = array();
                   $student_ids2 = array();
                   $student_id = Input::get('student_id');
                   $student_name = Input::get('student_name');
                   $major_classification = Input::get('major_classification');
                   $final_result = Input::get('final_result');
                   $application_year = Input::get('application_year');
                   $application_semester = Input::get('application_semester');
                   $major = Input::get('major');
                   $student_classification = Input::get('student_classification');
            //     $type = Input::get('state');
                   $flag = 0;
                    $flag1=0;
                    $flag2=0;
                         if (!empty($student_id)) {
                             $filter_student["studentno"] = '%'.$student_id. '%';
                         }
                        if (!empty($student_name)) {
                            $filter_student["fullname"] = '%'.$student_name. '%';
                        }
                         if (!empty($major_classification)&& $major_classification != '2') {
                             $filter_program["type"] = $major_classification;
                         }
                         if (!empty($major) && $major != '全部') {
                             $filter_program['id'] = $major;
                         }
                         if ($final_result != 3) {
                             $filter["final_result"] = $final_result;
                         }
                         if ($application_semester !=  2) {
                             $filter["application_semester"] = $application_semester;
                         }
                         if ($application_year != '全部') {
                             $filter["application_year"] = $application_year;
                         }
                        if (!empty($student_classification)) {
                            $filter_student["enrollmenttype"] = $student_classification;
                        }


        if (!empty($filter_student))
        {
            $student_ids1 = DB::table('admissions')->select('id')
                ->where(function ($query) use ($filter_student) {
                    if (!is_array($filter_student)) {
                        return $query;
                    }
                    foreach ($filter_student as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
            ->lists('id');
            $flag=1;
            $flag1=1;
        }
        if (!empty($filter_program))
        {
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
            $student_ids2 = DB::table('admissions')->select('id')
                ->whereIn('programcode',$program_ids)
                ->lists('id');
            $flag=1;
            $flag2=1;
        }

        if (($flag1==1) && ($flag2==1))
        {
            $student_ids = array_intersect($student_ids1, $student_ids2);
        }elseif (($flag1==0) && ($flag2==1))
        {
            $student_ids = $student_ids2;
        }elseif (($flag1==1) && ($flag2==0))
        {
            $student_ids = $student_ids1;
        }
        if ($flag == 1){
                $programs = DB::table('exemption_info')->whereIn('exemption_info.student_id', $student_ids)
                    ->leftjoin('admissions', function ($join) {
                        $join->on('exemption_info.student_id', '=', 'admissions.id');
                    })
                    ->leftjoin('course', function ($join) {
                        $join->on('exemption_info.course_id', '=', 'course.id');
                    })
                    ->leftjoin('rawprograms', function ($join) {
                        $join->on('rawprograms.id', '=', 'admissions.programcode');
                    })
                    ->leftjoin('admission_group', function ($join) {
                        $join->on('admission_group.admission_id', '=', 'admissions.id');
                    })
                    ->leftjoin('groups', function ($join) {
                        $join->on('groups.id', '=', 'admission_group.group_id');
                    })
                    ->leftjoin('campuses', function ($join) {
                        $join->on('admissions.campuscode', '=', 'campuses.id');
                    })
                    ->leftjoin('exemption_agency', function ($join) {
                        $join->on('exemption_info.agency_id', '=', 'exemption_agency.id');
                    })
                    ->leftjoin('exemption_type', function ($join) {
                        $join->on('exemption_type.id', '=', 'exemption_info.exemption_type_id');
                    })
                    ->where(function ($query) use ($filter) {
                        if (!is_array($filter)) {
                            return $query;
                        }
                        foreach ($filter as $key => $value) {
                            $query = $query->where('exemption_info.' . $key, 'like', $value);
                        }
                        return $query;
                    })
                    ->where('exemption_info.is_deleted', 0)
                    ->where('admissions.campuscode',$campus->id)
                    ->select('exemption_info.id', 'groups.name as group_name', 'admissions.studentno as student_id', 'admissions.fullname as student_name',
                        'rawprograms.name as major_name', 'campuses.name as campus_name', 'course.name as course_name', 'course.classification',
                        'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                        'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                        'exemption_info.credit_outer', 'exemption_info.certification_year', 'exemption_agency.agency_name', 'exemption_info.final_result', 'exemption_info.failure_cause');
             }elseif ($flag == 0) {
                $programs = DB::table('exemption_info')
                    ->leftjoin('admissions', function ($join) {
                        $join->on('exemption_info.student_id', '=', 'admissions.id');
                    })
                    ->leftjoin('course', function ($join) {
                        $join->on('exemption_info.course_id', '=', 'course.id');
                    })
                    ->leftjoin('rawprograms', function ($join) {
                        $join->on('rawprograms.id', '=', 'admissions.programcode');
                    })
                    ->leftjoin('admission_group', function ($join) {
                        $join->on('admission_group.admission_id', '=', 'admissions.id');
                    })
                    ->leftjoin('groups', function ($join) {
                        $join->on('groups.id', '=', 'admission_group.group_id');
                    })
                    ->leftjoin('campuses', function ($join) {
                        $join->on('admissions.campuscode', '=', 'campuses.id');
                    })
                    ->leftjoin('exemption_agency', function ($join) {
                        $join->on('exemption_info.agency_id', '=', 'exemption_agency.id');
                    })
                    ->leftjoin('exemption_type', function ($join) {
                        $join->on('exemption_type.id', '=', 'exemption_info.exemption_type_id');
                    })
                    ->where(function ($query) use ($filter) {
                        if (!is_array($filter)) {
                            return $query;
                        }
                        foreach ($filter as $key => $value) {
                            $query = $query->where('exemption_info.' . $key, 'like', $value);
                        }
                        return $query;
                    })
                    ->where('exemption_info.is_deleted', 0)
                    ->where('admissions.campuscode',$campus->id)
                    ->select('exemption_info.id', 'groups.name as group_name', 'admissions.studentno as student_id', 'admissions.fullname as student_name',
                        'rawprograms.name as major_name', 'campuses.name as campus_name', 'course.name as course_name', 'course.classification',
                        'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                        'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                        'exemption_info.credit_outer', 'exemption_info.certification_year', 'exemption_agency.agency_name', 'exemption_info.final_result', 'exemption_info.failure_cause');
        }
            return Datatables::of($programs)
                ->edit_column('classification', '@if($classification == 12)
                                                        本科
                                                @elseif($classification == 14)
                                                        专科
                                                @endif')
                ->edit_column('classification_outer', '@if($classification_outer == 12)
                                                        本科
                                                  @elseif($classification_outer == 14)
                                                        专科
                                                  @endif')
                ->edit_column('final_result', '@if($final_result == 0)
                                                        不通过
                                          @elseif($final_result == 1)
                                                        通过
                                          @elseif($final_result == 2)
                                                        未审核
                                          @endif')
                ->edit_column('application_year','@if($application_semester == 0)
                                                        {{$application_year}} 秋季
                                             @elseif($application_semester == 1)
                                                        {{$application_year}} 春季
                                             @endif')
                ->add_column ( 'actions', '@if (($final_result == 0) || ($final_result == 2))
                                             <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/edit\' ) }}}" class="btn btn-xs btn-default iframe">{{{ Lang::get(\'button.edit\') }}}</a>
                                             <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/delete\' ) }}}" class="btn btn-xs btn-default  iframe">{{{ Lang::get(\'button.delete\') }}}</a>
                                            @endif
                                            '
                )
                ->remove_column('application_semester')
                ->remove_column('id')
                ->make();

    }

    public function getInputStudent()
{

    $title = Lang::get('admin/exemption/title.input_stuinfo');

    return View::make('admin/exemption/input_student', compact('title'));
}


    public function getDataForQueryStudent()
    {
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $filter =  array();
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        if (!empty($student_id)) {
            $filter["studentno"] = '%'.$student_id. '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%'.$student_name. '%';
        }
        $filter["campuscode"] = $campus->id;
        if (!empty($filter)){
            $exemptions = DB::table('admissions')
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
            ->select('id','studentno','fullname');
        }else{
            $exemptions = DB::table('admissions')
                ->select('id','studentno','fullname');
        }
        return Datatables::of ($exemptions)
                    ->add_column( 'isSelect', '
                          <div align="center"><input type = "radio" name = "ids" id= "ids[]" value="{{ $id }}"></div> '
            )
            ->remove_column('id')
            ->make ();

    }


   public function postSelection()
   {

       $title = Lang::get('admin/exemption/title.input_score_stuinfo');
       $ids = Input::get('ids');
       if (!empty($ids))
       {
       // validate the flag
          $exemption = DB::table('admissions')
              ->leftjoin('campuses','campuses.id','=','admissions.campuscode')
              ->leftjoin('rawprograms','rawprograms.id','=','admissions.programcode')
              ->where('admissions.id', '=', $ids)
              ->select('admissions.id','admissions.studentno as student_id','admissions.fullname as student_name','campuses.name as campus_name','rawprograms.name as major_name','rawprograms.type as major_classification')
              ->first();
       }

       $selections = DB::table('student_selection')
           ->leftjoin('course', function($join)
           {
               $join->on('student_selection.course_id', '=', 'course.id');
           })
           ->leftjoin('admissions', function($join)
           {
               $join->on('student_selection.student_id', '=', 'admissions.id');
           })
           ->leftjoin('rawprograms', function($join)
           {
               $join->on('rawprograms.id', '=', 'admissions.programcode');
           })
           ->where('student_selection.student_id',$ids)
           ->select('admissions.id','course.id as course_id','course.name as course_name','rawprograms.type as major_classification','course.credit','student_selection.selection_status')
           ->get();
       // Show the page
       return View::make('admin/exemption/student_selection', compact('exemption','selections' ,'title'));
   }


    public function postCreateExemption()
    {
        // Title
        $title = Lang::get('admin/exemption/title.choose_course');
        $mode = 'create';
        $course_id = Input::get('id');
        $id = $_POST['ids'];

        $exemption = DB::table('student_selection')
                ->where('student_selection.student_id',$id)
                ->where('student_selection.course_id','=',$course_id)
                ->join('course', function($join)
                {
                    $join->on('student_selection.course_id', '=', 'course.id');
                })
                ->join('admissions', function($join)
                {
                    $join->on('student_selection.student_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function($join)
                {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->select('student_selection.student_id','admissions.studentno','course.id as course_id','course.name as course_name','rawprograms.type as major_classification','course.credit','student_selection.selection_status')
                ->first();

        $majors = DB::table('exemption_major_outer')->get();
        $agencys = DB::table('exemption_agency')->get();
        $types = DB::table('exemption_type')->get();

        return View::make('admin/exemption/exemption_create_edit',compact('id','exemption','majors','agencys','types','title','mode'));

    }

//save info into table exemption_info
    public function postCreateIntoExemption()
    {

        $course_id = Input::get('course_id');
        $student_id = Input::get('student_id');
        $year_semester = DB::table('module_current')
                ->where('module_id',2)
                ->first();
        $application_year = $year_semester->current_year;
        $application_semester = $year_semester->current_semester;
        $title = Lang::get('admin/exemption/title.manageexemption');

        $recordexists = DB::table('exemption_info')
        ->where('course_id', $course_id)
        ->where('student_id', $student_id)
        ->count();
        $max_credit = DB::table('teaching_plan')
            ->join('teaching_plan_module', function($join)
            {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id');
            })
            ->join('module_course', function($join)
            {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id');
            })
            ->where('module_course.course_id','=',$course_id)
            ->select('teaching_plan.max_credit_exemption as max_credit')
            ->first();
        //pass_subject_credit
        $sum_credit = DB::table('exemption_info')
            ->where('exemption_info.student_id', $student_id)
            ->join('course', function ($join) {
                $join->on('exemption_info.course_id', '=', 'course.id');
            })
            ->sum('course.credit');
        //get ready_to_pass_subject_credit
        $sel_credit = DB::table('course')
            ->where('id', $course_id)
            ->select('credit')->first();
       $total = intval($sum_credit) + intval($sel_credit->credit);
        if ($recordexists > 0) {
             return Redirect::to('admin/exemption/student_selection?ids='.$student_id)->withErrors(Lang::get('admin/exemption/messages.already_exists'));
        }else{
            if ($total <= $max_credit->max_credit){
                $exemptioninfo = new ExemptionInfo();
                $exemptioninfo->course_id = $course_id;
                $exemptioninfo->course_name_outer = Input::get('course_outer');
                $exemptioninfo->student_id = $student_id;
                $exemptioninfo->credit_outer =  Input::get('credit_outer');
                $exemptioninfo->major_name_outer =  Input::get('major_outer');
                $exemptioninfo->agency_id =  Input::get('agency_id');
                $exemptioninfo->certification_year =  Input::get('certification_year');
                $exemptioninfo->application_year = $application_year;
                $exemptioninfo->application_semester = $application_semester;
                $exemptioninfo->exemption_type_id =  Input::get('exemption_type_id');
                $exemptioninfo->classification_outer =  Input::get('classification_outer');
                $exemptioninfo->score =  Input::get('score');
                $exemptioninfo->remark =  Input::get('remark');
                $exemptioninfo->final_result = 2;
                $exemptioninfo->failure_cause = '';
                $exemptioninfo->is_deleted = 0;
                $exemptioninfo->created_at = new DateTime();
                $exemptioninfo->updated_at = new DateTime();
                $exemptioninfo->save();
                return Redirect::to('admin/exemption') ->with('success', Lang::get('admin/exemption/messages.create.success'));
            }else{
                return Redirect::to('admin/exemption') ->withInput()->withError(Lang::get('admin/exemption/messages.create.overcredit'));
            }

        }

        return Redirect::to('admin/exemption/create')->withInput()->withErrors($exemptioninfo)->errors();


    }

    public function getEdit($id) {
        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );
        $mode = 'edit';
        $exemption = DB::table('exemption_info')
            ->where('exemption_info.id','=',$id)
            ->join('course', function($join)
            {
                $join->on('exemption_info.course_id', '=', 'course.id');
            })
            ->join('admissions', function($join)
            {
                $join->on('exemption_info.student_id', '=', 'admissions.id');
            })
            ->leftjoin('rawprograms', function($join)
            {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->where('exemption_info.is_deleted',0)
            ->select('admissions.id as student_id','course.id as course_id','course.name as course_name',
                'rawprograms.type as major_classification','course.credit','exemption_info.credit_outer',
                'exemption_info.major_name_outer','exemption_info.agency_id','exemption_info.certification_year',
                'exemption_info.application_semester','exemption_info.exemption_type_id','exemption_info.score',
                'exemption_info.course_name_outer','exemption_info.classification_outer','exemption_info.remark')
            ->first();
        $majors = DB::table('exemption_major_outer')->get();
        $agencys = DB::table('exemption_agency')->get();
        $types = DB::table('exemption_type')->get();

         if (isset( $exemption ) && $id) {

            // Show the page
        return View::make ( 'admin/exemption/exemption_create_edit', compact ( 'majors','agencys','id','types','title','exemption','mode') );
        } else {
            return View::make ( 'admin/exemption/index', compact ( 'title') )->withError ( 'exemption ID not found' );

        }
    }

    public function postEdit($id) {


            $exemptions=ExemptionInfo::find($id);

            $exemptions->course_name_outer = Input::get('course_outer');
            $exemptions->credit_outer = Input::get('credit_outer');
            $exemptions->major_name_outer = Input::get('major_outer');
            $exemptions->agency_id = Input::get('agency_id');
            $exemptions->certification_year = Input::get('certification_year');
            $exemptions->exemption_type_id = Input::get('exemption_type_id');
            $exemptions->classification_outer = Input::get('classification_outer');
            $exemptions->score = Input::get('score');
            $exemptions->remark = Input::get('remark');
            $exemptions->created_at = new DateTime();
            $exemptions->updated_at = new DateTime();
            $exemptions->save();

            return Redirect::to('admin/exemption') ->with('success', Lang::get('admin/exemption/messages.edit.success'));


        //    } else {
        //        return Redirect::to('admin/exemption')->withErrors($exemption->errors());
        //    }
    }

    public function getDelete($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.exemption_delete');

        // Show the page
        return View::make('admin/exemption/delete', compact('id','title'));
    }

    public function postDelete($id)
    {
        // Check if we are not trying to delete ourselves

        $exemption = ExemptionInfo::find($id);
        $exemption->is_deleted = 1;
        $exemption->save();
        $exemption = ExemptionInfo::find($id);
        if ($exemption->is_deleted == 1)
        {
            // TODO needs to delete all of that exemption's content
            return Redirect::to('admin/exemption') ->with('success', Lang::get('admin/exemption/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the exemptioninfo
            return Redirect::to('admin/exemption')->with('error', Lang::get('admin/exemption/messages.delete.error'));
        }

    }


//control for exemption province
    public function getIndexProvince()
    {
        // Title
        $title = Lang::get('admin/exemption/title.manageexemptionprovince');

       $campuses = Campus::All();
        //    var_dump($exemptions);
        // Show the page
        return View::make('admin/exemption/exemption_province_index', compact('campuses','title'));
    }

    public function getDataForProvince() {

        $filter = array();
        $filter_program = array();
        $filter_student = array();
        $student_ids = array();
        $student_ids1 = array();
        $student_ids2 = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $result = Input::get('result');
        $start_year = Input::get('start_year');
        $start_semester = Input::get('start_semester');
        $terminal_year = Input::get('terminal_year');
        $terminal_semester = Input::get('terminal_semester');
        $campus = Input::get('campus');
        $student_classification = Input::get('student_classification');
        $ids = explode(',',Input::get('selectedExemptions'));
        $final_result = Input::get('final_result');

        $flag = 0;
        $flag1 = 0;
        $flag2 = 0;

        if (($start_year == $terminal_year) && ($start_semester == $terminal_semester)){
            $semester = array($start_semester);
        }else{
            $semester = array('01','02');
        }

        if($final_result==1)
        {
            if (!empty($ids)) {
                DB::table('exemption_info')
                    ->whereIn('id', $ids)
                    ->update(array('final_result' => $final_result));
                DB::table('exemption_info')
                    ->whereIn('id', $ids)
                    ->update(array('failure_cause' => ''));
            }
        }
        if (!empty($student_id)) {
            $filter_student["studentno"] = '%'.$student_id. '%';
        }
        if ($result != 3) {
            $filter["final_result"] = $result;
        }
        if (!empty($student_classification)) {
            $filter_student["enrollmenttype"] = $student_classification;
        }
        if (!empty($student_name)) {
            $filter_student["fullname"] = '%'.$student_name. '%';
        }

        if (!empty($campus) && $campus != '全部') {
            $filter_student['campuscode'] = $campus;
        }

        if (!empty($major_classification)&& $major_classification != '全部') {
            $filter_program['type'] = $major_classification;
        }



        if (!empty($filter_student))
        {
            $student_ids1 = DB::table('admissions')->select('id')
                ->where(function ($query) use ($filter_student) {
                    if (!is_array($filter_student)) {
                        return $query;
                    }
                    foreach ($filter_student as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $flag1=1;
            $flag=1;
        }
        if (!empty($filter_program))
        {
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
            $student_ids2 = DB::table('admissions')->select('id')
                ->whereIn('programcode',$program_ids)
                ->lists('id');
            $flag=1;
            $flag2=1;
        }

        if (($flag1==1) && ($flag2==1))
        {
            $student_ids = array_intersect($student_ids1, $student_ids2);
        }elseif (($flag1==0) && ($flag2==1))
        {
            $student_ids = $student_ids2;
        }elseif (($flag1==1) && ($flag2==0))
        {
            $student_ids = $student_ids1;
        }
        if ($flag == 1){
            $programs = DB::table('exemption_info')->whereIn('exemption_info.student_id', $student_ids)
                ->leftjoin('admissions', function ($join) {
                    $join->on('exemption_info.student_id', '=', 'admissions.id');
                })
                ->leftjoin('course', function ($join) {
                    $join->on('exemption_info.course_id', '=', 'course.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('exemption_agency', function ($join) {
                    $join->on('exemption_info.agency_id', '=', 'exemption_agency.id');
                })
                ->leftjoin('exemption_type', function ($join) {
                    $join->on('exemption_type.id', '=', 'exemption_info.exemption_type_id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('exemption_info.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->where('exemption_info.is_deleted', 0)
                ->whereBetween('exemption_info.application_year',array($start_year,$terminal_year))
                ->whereIn('exemption_info.application_semester',$semester)
                ->select('exemption_info.id',  'admissions.studentno as student_id', 'admissions.fullname as student_name',
                    'rawprograms.name as major_name', 'campuses.name as campus_name', 'course.name as course_name', 'course.classification',
                    'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                    'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                    'exemption_info.credit_outer', 'exemption_info.certification_year', 'exemption_agency.agency_name', 'exemption_info.final_result', 'exemption_info.failure_cause')
            ;
        }elseif ($flag == 0) {
            $programs = DB::table('exemption_info')
                ->leftjoin('admissions', function ($join) {
                    $join->on('exemption_info.student_id', '=', 'admissions.id');
                })
                ->leftjoin('course', function ($join) {
                    $join->on('exemption_info.course_id', '=', 'course.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('exemption_agency', function ($join) {
                    $join->on('exemption_info.agency_id', '=', 'exemption_agency.id');
                })
                ->leftjoin('exemption_type', function ($join) {
                    $join->on('exemption_type.id', '=', 'exemption_info.exemption_type_id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                   foreach ($filter as $key => $value) {
                        $query = $query->where('exemption_info.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->where('exemption_info.is_deleted', 0)
                ->whereBetween('exemption_info.application_year',array($start_year,$terminal_year))
                ->whereIn('exemption_info.application_semester',$semester)
                ->select('exemption_info.id',  'admissions.studentno as student_id', 'admissions.fullname as student_name',
                    'rawprograms.name as major_name', 'campuses.name as campus_name', 'course.name as course_name', 'course.classification',
                    'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                    'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                    'exemption_info.credit_outer', 'exemption_info.certification_year', 'exemption_agency.agency_name', 'exemption_info.final_result', 'exemption_info.failure_cause')
            ;
        }

        return Datatables::of($programs)
            ->edit_column('classification', '@if($classification == 12)
                                                        本科
                                         @elseif($classification == 14)
                                                        专科
                                         @endif')
            ->edit_column('classification_outer', '@if($classification_outer == 12)
                                                        本科
                                               @elseif($classification_outer == 14)
                                                        专科
                                               @endif')
            ->edit_column('final_result', '@if($final_result == 0)
                                                        不通过
                                       @elseif($final_result == 1)
                                                        通过
                                       @elseif($final_result == 2)
                                                        未审核
                                       @endif')
            ->edit_column('application_year','@if($application_semester == 0)
                                                        {{$application_year}} 秋季
                                          @elseif($application_semester == 1)
                                                        {{$application_year}} 春季
                                          @endif')
                     ->add_column( 'actions', '@if($final_result == 1)
                                       <a href="{{ URL::to(\'admin/exemption/\' . $id .\'/exemption_province_nopass\')}} ">不通过</a><br>
                                       <a href="{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_province_nocheck\' ) }}" >未审核</a>
                                   @elseif($final_result == 2)
                                       <a href="{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_province_pass\' ) }}" >通过</a><br>
                                       <a href="{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_province_nopass\' ) }}">不通过</a>
                                   @elseif($final_result == 0)
                                       <a href="{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_province_pass\' ) }}" >通过</a><br>
                                      <a href="{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_province_nocheck\' ) }}" >未审核</a>
                                   @endif '
                                   )

           ->add_column( 'isCheck', '
                          <div align="center"><input type = "checkbox" name = "checkItem[]" id= "checkItem" value="{{ $id }}"></div> '
            )
            ->remove_column('application_semester')
            ->remove_column('id')
            ->make();
    }


    public function getNoPassForProvince($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.input_no_pass_cause');

        $exemptions = DB::table ('exemption_info')
            ->where('exemption_info.id',$id)
            ->leftjoin('admissions', function ($join) {
                $join->on('exemption_info.student_id', '=', 'admissions.id');
            })
            ->leftjoin('course', function ($join) {
                $join->on('exemption_info.course_id', '=', 'course.id');
            })
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->leftjoin('exemption_agency', function ($join) {
                $join->on('exemption_info.agency_id', '=', 'exemption_agency.id');
            })
            ->leftjoin('exemption_type', function ($join) {
                $join->on('exemption_type.id', '=', 'exemption_info.exemption_type_id');
            })
            ->where('exemption_info.is_deleted', 0)
            ->select('exemption_info.id',  'admissions.studentno as student_id', 'admissions.fullname as student_name',
                'rawprograms.name as major_name', 'campuses.name as campus_name', 'course.name as course_name', 'course.classification',
                'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                'exemption_info.credit_outer', 'exemption_info.certification_year', 'exemption_agency.agency_name', 'exemption_info.final_result', 'exemption_info.failure_cause')
        ->get();
            //var_dump($exemptions);
        return View::make('admin/exemption/exemption_province_nopass',compact('exemptions','title'));

    }

    public function postNoPassForProvince($id)
    {
        // Title

        $exemptions = ExemptionInfo::find($id);
        $failure_cause = Input::get('failure_cause');

        $exemptions->failure_cause = $failure_cause;
        $exemptions->final_result = 0;
        $exemptions->save();

        return Redirect::to('admin/exemption/approve_exemption')->with('success',Lang::get('admin/exemption/messages.approve_suceess'));

    }

    public function getNoCheckForProvince($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.check_exemption');

        // Show the page
        return View::make('admin/exemption/exemption_province_nocheck', compact('id','title'));
    }


    public function postNoCheckForProvince($id)
    {

            $exemptions = ExemptionInfo::find($id);
            $exemptions->final_result=2;
            $exemptions->save();
             return Redirect::to('admin/exemption/approve_exemption')->with('success',Lang::get('admin/exemption/messages.approve_suceess'));

    }

    public function getPassForProvince($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.check_exemption');

        // Show the page
        return View::make('admin/exemption/exemption_province_pass', compact('id','title'));
    }

    public function postPassForProvince($id)
    {

            $student = DB::table('exemption_info')
                ->where('id', $id)
                ->select('student_id as id')
                ->first();

            //pass_subject_credit
        $max_credit = DB::table('teaching_plan')
            ->join('teaching_plan_module', function($join)
            {
                $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id');
            })
            ->join('module_course', function($join)
            {
                $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id');
            })
            ->join('exemption_info', function($join)
            {
                $join->on('exemption_info.course_id', '=', 'module_course.course_id');
            })
            ->where('exemption_info.id','=',$id)
            ->select('teaching_plan.max_credit_exemption as max_credit')
            ->first();
        //pass_subject_credit
        $sum_credit = DB::table('exemption_info')
            ->where('exemption_info.student_id', $student->id)
            ->join('course', function ($join) {
                $join->on('exemption_info.course_id', '=', 'course.id');
            })
            ->sum('course.credit');
        //get ready_to_pass_subject_credit
        $sel_credit = DB::table('course')
            ->join('exemption_info', function($join)
            {
                $join->on('exemption_info.course_id', '=', 'course.id');
            })
            ->where('exemption_info.id','=',$id)
            ->select('course.credit')->first();
        $total = intval($sum_credit) + intval($sel_credit->credit);

            if ($total >= $max_credit->max_credit) {
                $exemptions = ExemptionInfo::find($id);
                $exemptions->failure_cause = '';
                $exemptions->final_result = 1;
                $exemptions->save();
            } else {
                return Redirect::to('admin/exemption/approve_exemption')->with('error', Lang::get('admin/exemption/messages.create.overcredit'));
            }
            return Redirect::to('admin/exemption/approve_exemption')->with('success',Lang::get('admin/exemption/messages.approve_suceess'));
    }

    public function postAllPassForProvince()
    {
        // Title
        $title = Lang::get('admin/exemption/title.manageexemptionprovince');
        $ids = Input::get("checkItem");
        $stu = array();
        $flag = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $student = DB::table('exemption_info')
                ->where('id', $ids[$i])
                ->select('student_id as id')
                ->first();
            // passed_subject_credit$sum_credit = DB::table('exemption_info')
            $max_credit = DB::table('teaching_plan')
                ->join('teaching_plan_module', function($join)
                {
                    $join->on('teaching_plan_module.teaching_plan_id', '=', 'teaching_plan.id');
                })
                ->join('module_course', function($join)
                {
                    $join->on('module_course.teaching_plan_module_id', '=', 'teaching_plan_module.id');
                })
                ->join('exemption_info', function($join)
                {
                    $join->on('exemption_info.course_id', '=', 'module_course.course_id');
                })
                ->where('exemption_info.id','=',$ids[$i])
                ->select('teaching_plan.max_credit_exemption as max_credit')
                ->first();
            //pass_subject_credit
            $sum_credit = DB::table('exemption_info')
                ->where('exemption_info.student_id', $student->id)
                ->join('course', function ($join) {
                    $join->on('exemption_info.course_id', '=', 'course.id');
                })
                ->sum('course.credit');
            //get ready_to_pass_subject_credit
            $sel_credit = DB::table('course')
                ->join('exemption_info', function($join)
                {
                    $join->on('exemption_info.course_id', '=', 'course.id');
                })
                ->where('exemption_info.id','=',$ids[$i])
                ->select('course.credit')->first();
            $total = intval($sum_credit) + intval($sel_credit->credit);
            if ($max_credit->max_credit >= $total) {
                $exemptioninfo = ExemptionInfo::find($ids[$i]);
                if ($exemptioninfo->final_result <> 1) {
                    $exemptioninfo->final_result = 1;
                    $exemptioninfo->failure_cause = '';
                    $exemptioninfo->created_at = new DateTime();
                    $exemptioninfo->updated_at = new DateTime();
                    $exemptioninfo->save();
                }
            } else {
                $flag = 1;
            }
        }
        if ($flag == 1){
            return Redirect::to('admin/exemption/approve_exemption')->withError(Lang::get('admin/exemption/messages.create.overcredit'));
        }else{
            return Redirect::to('admin/exemption/approve_exemption')->with('success',Lang::get('admin/exemption/messages.approve_suceess'));
        }
    }



    // Control for ExemptionType
    public function getIndexForExemptionType() {
        // Title
        $title = Lang::get ( 'admin/exemption/title.manageexemptiontype' );

        //      $exemptions =ExemptionType::all();
        // Show the page
        //      return View::make ( 'admin/exemption_type/index', compact ('exemptions', 'title'));
        return View::make ( 'admin/exemption/exemption_type_index', compact ( 'title'));
    }

    public function getCreateExemptionType() {

        $title = Lang::get ( 'admin/exemption/table.define_exemption_type' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/exemption/exemption_type_define_edit', compact ('title', 'mode') );
    }

    public function postCreateExemptionType() {

        $exemptiontype = new ExemptionType();
        $exemptiontype->code = Input::get('code');
        $exemptiontype->exemption_type_name = Input::get('exemption_type_name');

        if ($exemptiontype->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('exemption_type')
                ->where('code', Input::get('code'))
                ->where('exemption_type_name', Input::get('exemption_type_name'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/exemption/data_add_exemption_type')->withErrors('error',Lang::get ( 'admin/exemption/messages.create_major_outer.already_exists'));
            }
//
            $exemptiontype->save();
        } else {
            return Redirect::to('admin/exemption_type')->withErrors($exemptiontype->errors());
        }

        return Redirect::to('admin/exemption/data_add_exemption_type')->with('success', Lang::get('admin/exemption/messages.major_outer.create'));



    }

    public function getDataForExemptionType() {

        $programs = DB::table ('exemption_type')
            ->where('is_deleted',0)
            ->select( 'id', 'exemption_type_name','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_type_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_type_delete\' ) }}}" class="iframe"> {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditExemptionType($id) {
        $title = Lang::get ( 'admin/exemption/title.exemption_type_edit' );

        // Mode
        $mode = 'edit';
//        $action = 'edit';

        $exemptiontype = ExemptionType::find ( $id );
        if (isset ( $exemptiontype ) && $exemptiontype->id) {

            //prepare available 專業 list

            // Show the page
            return View::make ( 'admin/exemption/exemption_type_define_edit', compact ( 'title', 'mode', 'exemptiontype'));
        } else {
            return View::make ( 'admin/exemption/exemption_type_index', compact ( 'title' , 'mode') )->withError ( 'Major ID not found' );

        }
    }

    public function postEditExemptionType($id) {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
//        $mode = 'create';

        $exemptiontype = ExemptionType::find ( $id );
        if (isset ( $exemptiontype ) && $exemptiontype->id) {
            $exemptiontype->exemption_type_name = Input::get('exemption_type_name');
            $exemptiontype->code = Input::get('code');

            if ($exemptiontype->validate(Input::all())) {

                $exemptiontype->save();
            } else {
                return Redirect::to('admin/exemption_type')->withErrors($exemptiontype->errors());
            }

            return Redirect::to('admin/exemption/'.$id.'/exemption_type_edit')->withInput()->with('success', Lang::get('admin/exemption/messages.edit.success'));

        }
    }

    public function getDeleteExemptionType($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.exemption_type_delete');

        // Show the page
        return View::make('admin/exemption/exemption_type_delete', compact('id', 'title'));
    }

    public function postDeleteExemptionType($id )
    {
        $is_used = DB::table('exemption_info')
            ->where('major_name_outer',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/exemption_major_outer')->with('error', Lang::get('admin/exemption/messages.major_outer.error'));
        }else {
            // Was the comment post deleted?
            $exemptiontype = ExemptionType::find($id);
            $exemptiontype->is_deleted = 1;

            $exemptiontype->save();

            $count = DB::table('exemption_type')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();
            if ($count != 0) {

                return Redirect::to('admin/exemption_type')->with('success', Lang::get('admin/exemption/messages.delete.success'));
            } else {
                // There was a problem deleting the user
                return Redirect::to('admin/exemption_type')->with('error', Lang::get('admin/exemption/messages.delete.error'));
            }
        }
    }


    // Control for ExemptionAgency
    public function getIndexForExemptionAgency() {
        // Title
        $title = Lang::get ( 'admin/exemption/title.manageexemptionagency' );

        //    $exemptionagency =ExemptionAgency::all();
        // Show the page
        return View::make ( 'admin/exemption/exemption_agency_index', compact ('title'));
    }

    public function getCreateExemptionAgency() {

        $title = Lang::get ( 'admin/exemption/table.define_agency' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/exemption/exemption_agency_define_edit', compact ('title', 'mode') );
    }

    public function postCreateExemptionAgency() {

            $exemptionagency = new ExemptionAgency();

            //verify overall
            if ($exemptionagency->validate(Input::all())) {

                $exemptionagency->code = Input::get('code');
                $exemptionagency->agency_name = Input::get('agency_name');
                $recordexists = DB::table('exemption_agency')
                    ->where('code', Input::get('code'))
                    ->where('agency_name', Input::get('agency_name'))
                    ->count();

                if ($recordexists > 0) {

                    return Redirect::to('admin/exemption/data_add_exemption_agency')->withErrors('error',Lang::get ( 'admin/exemption/messages.exemption_agency.already_exists'));
                }
                $exemptionagency->save();

            } else {
                return Redirect::to('admin/exemption_agency')->withErrors($exemptionagency->errors());
            }
        return Redirect::to('admin/exemption/data_add_exemption_agency')->with('success', Lang::get('admin/exemption/messages.exemption_agency.create'));

    }

    public function getDataForExemptionAgency() {

        $programs = DB::table ('exemption_agency')
            ->where('is_deleted',0)
            ->select( 'id', 'agency_name','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_agency_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_agency_delete\' ) }}}" class="iframe" > {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditExemptionAgency($id) {
        $title = Lang::get ( 'admin/exemption/title.exemption_agency_edit' );

        // Mode
        $mode = 'edit';
//        $action = 'edit';

        $exemptionagency =ExemptionAgency::find($id);
        if (isset ( $exemptionagency ) && $exemptionagency->id) {

            //prepare available 專業 list

            // Show the page
            return View::make ( 'admin/exemption/exemption_agency_define_edit', compact ( 'title', 'mode', 'exemptionagency'));
        } else {
            return View::make ( 'admin/exemption/exemption_agency_index', compact ( 'title' , 'mode') )->withError ( 'Agency ID not found' );

        }
    }

    public function postEditExemptionAgency($id) {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
        //      $mode = 'edit';

        $exemptionagency =ExemptionAgency::find($id);
        if (isset ( $exemptionagency ) && $exemptionagency->id) {
            $exemptionagency->agency_name = Input::get('agency_name');
            $exemptionagency->code = Input::get('code');

            if ($exemptionagency->validate(Input::all())) {

                $exemptionagency->save();
            } else {
                return Redirect::to('admin/exemption_agency')->withErrors($exemptionagency->errors());
            }

            return Redirect::to('admin/exemption/'.$id.'/exemption_agency_edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));

        }
    }

    public function getDeleteExemptionAgency($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.exemption_agency_delete');

        // Show the page
        return View::make('admin/exemption/exemption_agency_delete', compact('id', 'title'));
    }

    public function postDeleteExemptionAgency($id )
    {

        $is_used = DB::table('exemption_info')
            ->where('major_name_outer',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/exemption_major_outer')->with('error', Lang::get('admin/exemption/messages.exemption_agency.error'));
        }else {
            // Was the comment post deleted?
            $exemptionagency = ExemptionAgency::find($id);
            $exemptionagency->is_deleted = 1;
            $exemptionagency->save();

            $count = DB::table('exemption_agency')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count > 0) {
                return Redirect::to('admin/exemption_agency')->with('success', Lang::get('admin/exemption/messages.delete.success'));
            } else {
                // There was a problem deleting the user
                return Redirect::to('admin/exemption_agency')->with('error', Lang::get('admin/exemption/messages.delete.error'));
            }
        }
    }


    // Control for ExemptionMajorOuter
    public function getIndexForMajorOuter() {
        // Title
        $title = Lang::get ( 'admin/exemption/title.managemajorouter' );

        // Show the page
        return View::make ( 'admin/exemption/exemption_major_outer_index', compact ('title'));
    }

    public function getCreateMajorOuter() {

        $title = Lang::get ( 'admin/exemption/table.define_major_outer' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/exemption/exemption_major_outer_define_edit', compact ('title', 'mode') );
    }

    public function postCreateMajorOuter() {

        $majorouter = new ExemptionMajorOuter();
        $majorouter->code = Input::get('code');
        $majorouter->major_name = Input::get('major_name');

        if ($majorouter->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('exemption_major_outer')
                ->where('code', Input::get('code'))
                ->where('major_name', Input::get('major_name'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/exemption/data_add_exemption_major_outer')->withErrors('error',Lang::get ( 'admin/exemption/messages.create_major_outer.already_exists'));
            }
//
            $majorouter->save();
        } else {
            return Redirect::to('admin/exemption_major_outer')->withErrors($majorouter->errors());
        }

//        return Redirect::to('admin/edu_department/'.$id.'/edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));
        return Redirect::to('admin/exemption/data_add_exemption_major_outer')->with('success', Lang::get('admin/exemption/messages.major_outer.create'));



    }

    public function getDataForMajorOuter() {

        $programs = DB::table ('exemption_major_outer')
            ->where('is_deleted',0)
            ->select( 'id', 'major_name','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_major_outer_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/exemption/\' . $id . \'/exemption_major_outer_delete\' ) }}}" class="iframe"> {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditMajorOuter($id) {
        $title = Lang::get ( 'admin/exemption/title.major_outer_edit' );

        // Mode
        $mode = 'edit';

        $majorouter = ExemptionMajorOuter::find ( $id );
        if (isset ( $majorouter ) && $majorouter->id) {


            // Show the page
            return View::make ( 'admin/exemption/exemption_major_outer_define_edit', compact ( 'title', 'mode', 'majorouter'));
        } else {
            return View::make ( 'admin/exemption/exemption_major_outer_index', compact ( 'title' , 'mode') )->withError ( 'Major ID not found' );

        }
    }

    public function postEditMajorOuter($id) {

        $majorouter = ExemptionMajorOuter::find ( $id );
        if (isset ( $majorouter ) && $majorouter->id) {
            $majorouter->major_name = Input::get('major_name');
            $majorouter->code = Input::get('code');

            if ($majorouter->validate(Input::all())) {

                $majorouter->save();
            } else {
                return Redirect::to('admin/exemption_major_outer')->withErrors($majorouter->errors());
            }

            return Redirect::to('admin/exemption/'.$id.'/exemption_major_outer_edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));

        }
    }

    public function getDeleteMajorOuter($id)
    {
        // Title
        $title = Lang::get('admin/exemption/title.major_outer_delete');

        // Show the page
        return View::make('admin/exemption/exemption_major_outer_delete', compact('id', 'title'));
    }

    public function postDeleteMajorOuter($id )
    {

        // Was the comment post deleted?

        $is_used = DB::table('exemption_info')
            ->where('major_name_outer',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/exemption_major_outer')->with('error', Lang::get('admin/exemption/messages.major_outer.error'));
        }else{
            $majorouter = ExemptionMajorOuter::find ( $id );
            $majorouter->is_deleted = 1;
            $majorouter->save();

            $count = DB::table('exemption_major_outer')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();
            if ($count != 0) {

                return Redirect::to('admin/exemption_major_outer')->with('success', Lang::get('admin/exemption/messages.delete.success'));
            } else {
                // There was a problem deleting the user
                return Redirect::to('admin/exemption_major_outer')->with('error', Lang::get('admin/exemption/messages.delete.error'));
            }
        }
    }
}