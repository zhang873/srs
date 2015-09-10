<?php

class AdminUnifiedExamController extends AdminController
{
    /**
     * Unified Exam Campus
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.manageunifiedexam');
        $campuses = Campus::All();
        // Show the page
      return View::make('admin/unified_exam/index', compact('campuses','title'));
    }

    public function getDataForCampus() {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter_student = array();
        $filter = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $final_result = Input::get('final_result');
        $start_time = Input::get('start_time');
        $terminal_time = Input::get('terminal_time');
        $campuscode = Input::get('campus');
        $student_classification = Input::get('student_classification');
        $flag = 0;

        if (!empty($student_classification)) {
            $filter_student["enrollmenttype"] = $student_classification;
        }
        if (!empty($student_id)) {
            $filter_student["studentno"] = '%'.$student_id. '%';
        }
        if (!empty($student_name)) {
            $filter_student["fullname"] = '%'.$student_name. '%';
        }
        if (!empty($major_classification)) {
            $filter_student["program"] = $major_classification;
        }
        if (!empty($final_result)) {
            $filter["final_result"] = $final_result;
        }
        if (!empty($campuscode)) {
            $filter_student["campuscode"] = $campuscode;
        }


        $start = explode('|', $start_time);
        $start_year = $start[0];
        $start_semester = $start[1];

        $terminal = explode('|', $terminal_time);
        $terminal_year = $terminal[0];
        $terminal_semester = $terminal[1];

        if (($start_year == $terminal_year) && ($start_semester == $terminal_semester)){
            $semester = array($start_semester);
        }else{
            $semester = array('01','02');
        }

        if (!empty($filter_student))
        {
            $student_ids = DB::table('admissions')->select('id')
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
        }

        if ($flag==1) {
            $exemptions = DB::table('unified_exam_info')
                ->whereIn('unified_exam_info.student_id', $student_ids)
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'unified_exam_info.student_id');
                })
                ->leftjoin('unified_exam_subject', function ($join) {
                    $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
                })
                ->leftjoin('unified_exam_cause', function ($join) {
                    $join->on('unified_exam_info.unified_exam_cause_id', '=', 'unified_exam_cause.id');
                })
                ->leftjoin('unified_exam_type', function ($join) {
                    $join->on('unified_exam_info.unified_exam_type_id', '=', 'unified_exam_type.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('unified_exam_info.' . $key, 'like', $value);
                    }

                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->where('unified_exam_info.is_deleted',0)
                ->whereBetween('unified_exam_info.registration_year',array($start_year,$terminal_year))
                ->whereIn('unified_exam_info.registration_semester',$semester)
                ->where('unified_exam_info.unified_exam_subject_id','>',0)
                ->select('unified_exam_info.id', 'admissions.studentno', 'admissions.fullname', 'campuses.name as campus_name', 'groups.name as group_name', 'unified_exam_info.registration_year', 'unified_exam_info.registration_semester','unified_exam_subject.subject', 'unified_exam_cause.cause', 'unified_exam_type.type', 'unified_exam_info.final_result');
        }else{
            $exemptions = DB::table ('unified_exam_info')
                ->leftjoin('admissions', function($join)
                {
                    $join->on('admissions.id', '=', 'unified_exam_info.student_id');
                })
                ->leftjoin('unified_exam_subject', function($join)
                {
                    $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
                })
                ->leftjoin('unified_exam_cause', function($join)
                {
                    $join->on('unified_exam_info.unified_exam_cause_id', '=', 'unified_exam_cause.id');
                })
                ->leftjoin('unified_exam_type', function($join)
                {
                    $join->on('unified_exam_info.unified_exam_type_id', '=', 'unified_exam_type.id');
                })
                ->leftjoin('campuses', function($join)
                {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function($join)
                {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('groups', function($join)
                {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('unified_exam_info.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->whereBetween('unified_exam_info.registration_year',array($start_year,$terminal_year))
                ->whereIn('unified_exam_info.registration_semester',$semester)
                ->where('unified_exam_info.is_deleted',0)
                ->where('unified_exam_info.unified_exam_subject_id','>',0)
                ->select('unified_exam_info.id', 'admissions.studentno', 'admissions.fullname',
                    'campuses.name as campus_name', 'groups.name as group_name', 'unified_exam_info.registration_year',
                    'unified_exam_info.registration_semester','unified_exam_subject.subject', 'unified_exam_cause.cause',
                    'unified_exam_info.final_result');
        }

        return Datatables::of ($exemptions)
            ->add_column ( 'actions', '@if($final_result == 2)
                                        <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/edit\' ) }}}">{{{ Lang::get(\'button.edit\') }}}</a>
                                        <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/delete\' ) }}}"  class="iframe">{{{ Lang::get(\'button.delete\') }}}</a>
                                        @elseif($final_result == 0)
                                        <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/edit\' ) }}}">{{{ Lang::get(\'button.edit\') }}}</a>
                                        @endif
                                  '
            )
            ->edit_column('registration_semester','@if ($registration_semester == 0)
                                                        秋季
                                             @elseif ($registration_semester == 1)
                                                        春季
                                             @endif')
            ->edit_column('final_result', '@if($final_result == 0)
                                                        不通过
                                          @elseif($final_result == 1)
                                                        通过
                                          @elseif($final_result == 2)
                                                        未审核
                                          @endif')
            ->remove_column('id')

            ->make ();
    }

    public function getInputStudent()
{

    $title = Lang::get('admin/unified_exam/title.input_admissions');

    return View::make('admin/unified_exam/input_student', compact('title'));
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
        //get the stuinfo
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
                          <div align="center"><input type = "radio" name = "id[]" id= "id" value="{{{ $id }}}"></div> '
            )
            ->remove_column('id')
            ->make ();
    }


   public function postRequire()
   {

       $title = Lang::get('admin/unified_exam/title.input_score_admissions');
       $student_id = Input::get('id');
       $subjects = DB::table('unified_exam_subject')
           ->where('is_deleted',0)
           ->select('id','subject')->get();
       $causes = DB::table('unified_exam_cause')
           ->where('is_deleted',0)
           ->select('id','cause')
           ->get();

       // validate the flag

      if (empty($student_id)) {
             View::make('admin/unified_exam/student_select') ->with('友情提醒', Lang::get('admin/unified_exam/messages.pleaseselect'));

       } else {
           $exemption = DB::table('admissions')
               ->where('admissions.id',$student_id)
               ->leftjoin('rawprograms', function($join)
               {
                   $join->on('admissions.programcode', '=', 'rawprograms.id');
               })
               ->leftjoin('campuses', function($join)
               {
                   $join->on('admissions.campuscode', '=', 'campuses.id');
               })
               ->select('admissions.id','admissions.studentno as student_id','admissions.fullname as student_name','campuses.name as campus_name','rawprograms.name as major_name','rawprograms.type as major_classification')
               ->first();

          //  Show the page
          return View::make('admin/unified_exam/student_select', compact('student_id','types','exemption', 'subjects','causes','title'));

      }

   }

    public function getRequire()
    {


        $title = Lang::get('admin/unified_exam/title.input_score_admissions');
        $student_id = $_GET['id'];
        $subjects = DB::table('unified_exam_subject')
            ->where('is_deleted',0)
            ->select('id','subject')->get();
        $causes = DB::table('unified_exam_cause')
            ->where('is_deleted',0)
            ->select('id','cause')
            ->get();

        // validate the flag

        if (empty($student_id)) {
            View::make('admin/unified_exam/student_select') ->with('友情提醒', Lang::get('admin/unified_exam/messages.pleaseselect'));

        } else {
            $exemption = DB::table('admissions')
                ->where('admissions.id',$student_id)
                ->leftjoin('rawprograms', function($join)
                {
                    $join->on('admissions.programcode', '=', 'rawprograms.id');
                })
                ->leftjoin('campuses', function($join)
                {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->select('admissions.id','admissions.studentno as student_id','admissions.fullname as student_name','campuses.name as campus_name','rawprograms.name as major_name','rawprograms.type as major_classification')
                ->first();

            //  Show the page
            return View::make('admin/unified_exam/student_select', compact('student_id','types','exemption', 'subjects','causes','title'));

        }

    }

//save info into table exemption_info
    public function postIntoUnifiedExamInfo()
    {

        $unified_exam_cause = Input::get('cause');
        $unified_exam_subject = Input::get('subject');
        $student_id = $_POST['student_id'];
        $year_semester = DB::table('module_current')
            ->where('module_id',2)
            ->first();
        $application_year = $year_semester->current_year;
        $application_semester = $year_semester->current_semester;

            $recordexists = DB::table('unified_exam_info')
                  ->where('student_id', $student_id)
                  ->where('unified_exam_subject_id', $unified_exam_subject)
                  ->count();

              if ($recordexists > 0) {
                  return Redirect::to('admin/unified_exam/student_selection?id='.$student_id)->withErrors(Lang::get('admin/unified_exam/messages.already_exists'));
              }else{
                  $unifiedexaminfo = new UnifiedExamInfo();
                  $unifiedexaminfo->student_id = $student_id;
                  $unifiedexaminfo->registration_year = $application_year;
                  $unifiedexaminfo->registration_semester =  $application_semester;
                  $unifiedexaminfo->unified_exam_subject_id = $unified_exam_subject;
                  $unifiedexaminfo->unified_exam_cause_id = $unified_exam_cause;
                  $unifiedexaminfo->final_result = 2;
                  $unifiedexaminfo->failure_cause = '';
                  $unifiedexaminfo->is_deleted = 0;
                  $unifiedexaminfo->created_at = new DateTime();
                  $unifiedexaminfo->updated_at = new DateTime();
                  $unifiedexaminfo->save();

                  return Redirect::to('admin/unified_exam') ->with('success', Lang::get('admin/unified_exam/messages.create.success'));
              }

    }


    public function getEdit($id) {
        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );
        $subjects = DB::table('unified_exam_subject')
            ->where('is_deleted',0)
            ->select('id','subject')->get();
        $causes = DB::table('unified_exam_cause')
            ->where('is_deleted',0)
            ->select('id','cause')->get();
        $exemption = DB::table ('unified_exam_info')
            ->where('unified_exam_info.id',$id)
            ->where('unified_exam_info.is_deleted',0)
            ->leftjoin('admissions','unified_exam_info.student_id','=','admissions.id')
            ->leftjoin('unified_exam_subject','unified_exam_info.unified_exam_subject_id','=','unified_exam_subject.id')
            ->leftjoin('unified_exam_cause','unified_exam_info.unified_exam_cause_id','=','unified_exam_cause.id')
            ->select('admissions.studentno as student_id','admissions.fullname as student_name','unified_exam_subject.subject as subject_name',
                'unified_exam_cause.cause as cause_name','unified_exam_info.registration_year','unified_exam_info.registration_semester',
                'unified_exam_info.id','unified_exam_info.unified_exam_subject_id','unified_exam_info.unified_exam_cause_id')
            ->first();

        if (isset( $exemption ) && $id) {

            // Show the page
        return View::make ( 'admin/unified_exam/edit', compact ('title','exemption','subjects','causes') );
        } else {
            return View::make ( 'admin/unified_exam/index', compact ( 'title') )->withError ( '无此记录' );

       }
    }

    public function postEdit($id) {

            $unified_exam_cause = Input::get('cause');
            $unified_exam_subject = Input::get('subject');
            $input_year = Input::get('input_year');
            $input_semester = Input::get('input_semester');
            $unifiedexaminfo=UnifiedExamInfo::find($id);

            if (!empty($unified_exam_cause)){
                $unifiedexaminfo->unified_exam_cause_id = $unified_exam_cause;
            }
            if (!empty($unified_exam_subject)){
                $unifiedexaminfo->unified_exam_subject_id = $unified_exam_subject;
            }
            if (!empty($input_semester)){
                $unifiedexaminfo->registration_semester = $input_semester;
            }
            if (!empty($input_year)){
                $unifiedexaminfo->registration_year = $input_year;
            }
                $unifiedexaminfo->created_at = new DateTime();
                $unifiedexaminfo->updated_at = new DateTime();
                $unifiedexaminfo->save();
                return Redirect::to('admin/unified_exam') ->with('success', Lang::get('admin/unified_exam/messages.edit.success'));

    }

    public function getDelete($id)
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.exemption_delete');

        // Show the page
        return View::make('admin/unified_exam/delete', compact('id','title'));
    }

    public function postDelete($id)
    {
        // Check if we are not trying to delete ourselves

        $unifiedexaminfo = UnifiedExamInfo::find($id);
        $unifiedexaminfo->is_deleted = 1;
        $unifiedexaminfo->save();
        $count = UnifiedExamInfo::find($id)
                 ->where('is_deleted',1)
                 ->count();
        if ($count>0)
        {
            // TODO needs to delete all of that exemption's content
            return Redirect::to('admin/unified_exam') ->with('success', Lang::get('admin/unified_exam/messages.delete.success'));

        }
        else
        {
            // There was a problem deleting the exemptioninfo
            return Redirect::to('admin/unified_exam')->with('error', Lang::get('admin/unified_exam/messages.delete.error'));
        }

    }


    public function getSelectType()
    {
        $title = Lang::get('admin/unified_exam/title.choose_unified_type');
        $types = DB::table('unified_exam_type')
            ->where('is_deleted',0)
            ->select('id','type')
            ->get();
        return View::make('admin/unified_exam/unified_exam_select_type', compact('types', 'title'));
    }

    public function postSelectStudent()
    {

        $title = Lang::get('admin/unified_exam/title.choose_admissions');
        $type = Input::get('type');

        // Show the page
        return View::make('admin/unified_exam/unified_exam_select_student_with_type', compact('type' ,'title'));

    }

    public function getSelectStudent()
    {

        $title = Lang::get('admin/unified_exam/title.choose_admissions');
        $type = $_REQUEST['type'];

        // Show the page
        return View::make('admin/unified_exam/unified_exam_select_student_with_type', compact('type' ,'title'));

    }

    public function getDataQueryStudentType()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();

        //get the stuinfo
        $exemptions = DB::table('admissions')
            ->where('campuscode',$campus->id)
            ->select('id','studentno','fullname');

        return Datatables::of ($exemptions)
            ->add_column( 'isCheck', '
                          <div align="center"><input type = "checkbox" name = "ids[]" id= "ids" value="{{{ $id }}}"></div> '
            )
            ->remove_column('id')
            ->make ();
    }

    public function postSaveUnifiedExam() {

        $type=$_REQUEST["type"];
        $ids = explode(',',Input::get('selectedStudent'));
        $flag = 0;
        $year_semester = DB::table('module_current')
            ->where('module_id',3)
            ->first();

        for($i=0;$i<count($ids);$i++) {
        //    var_dump($student_id[$i]);
            $recordExists = DB::table('unified_exam_info')
                        ->where('student_id',$ids[$i])
                        ->where('unified_exam_type_id',$type)
                        ->where('is_deleted',0)
                        ->count();

            if ($recordExists == 0) {

                $unifiedexaminfo = new UnifiedExamInfo();
                $unifiedexaminfo->student_id = $ids[$i];
                $unifiedexaminfo->registration_year = $year_semester->current_year;
                $unifiedexaminfo->registration_semester =  $year_semester->current_semester;
                $unifiedexaminfo->unified_exam_type_id = $type;
                $unifiedexaminfo->final_result = 2;
                $unifiedexaminfo->failure_cause = '';
                $unifiedexaminfo->is_deleted = 0;
                $unifiedexaminfo->created_at = new DateTime();
                $unifiedexaminfo->updated_at = new DateTime();
                $unifiedexaminfo->save();

            } else {
                $flag=1;
             }

        }
    if ($flag == 0){
        return Redirect::to('admin/unified_exam/index_type') ->with('success', Lang::get('admin/unified_exam/messages.create.success'));
    }elseif ($flag == 1)
        return Redirect::to('admin/unified_exam/select_student?type='.$type)->withError(Lang::get('admin/unified_exam/messages.already_exists'));

    }


    public function getIndexForType()
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.manageunifiedexam');
        $campuses = Campus::All();
        // Show the page
        return View::make('admin/unified_exam/index_type', compact('campuses','title'));
    }

    public function getDataForTypeIndex() {

        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
            $exemptions = DB::table ('unified_exam_info')
                ->leftjoin('admissions', function($join)
                {
                    $join->on('admissions.id', '=', 'unified_exam_info.student_id');
                })
                ->leftjoin('unified_exam_type', function($join)
                {
                    $join->on('unified_exam_info.unified_exam_type_id', '=', 'unified_exam_type.id');
                })
                ->leftjoin('campuses', function($join)
                {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function($join)
                {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('groups', function($join)
                {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->where('unified_exam_info.is_deleted',0)
                ->where('admissions.campuscode',$campus->id)
                ->select('unified_exam_info.id', 'admissions.studentno', 'admissions.fullname', 'campuses.name as campus_name', 'groups.name as group_name', 'unified_exam_info.registration_year', 'unified_exam_info.registration_semester', 'unified_exam_type.type', 'unified_exam_info.final_result');


        return Datatables::of ($exemptions)
            ->add_column('action','
                     @if ($final_result <> 1)
                        <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/edit_type\' ) }}}" class=" btn btn-xs btn-default">{{{ Lang::get(\'button.edit\') }}}</a>
                        <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/delete_type\' ) }}}" class="btn btn-xs btn-danger btn-small btn-info ">{{{ Lang::get(\'button.delete\') }}}</a>
                        @endif
            ')
            ->edit_column('registration_semester','@if ($registration_semester == 0)
                                                        秋季
                                             @elseif ($registration_semester == 1)
                                                        春季
                                             @endif')
            ->edit_column('final_result', '@if($final_result == 0)
                                                        不通过
                                          @elseif($final_result == 1)
                                                        通过
                                          @elseif($final_result == 2)
                                                        未审核
                                          @endif')
            ->remove_column('id')
            ->make ();
    }


    public function getEditType($id) {
        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );
        $types = DB::table('unified_exam_type')->select('id','type')->get();
        $campuses = Campus::All();
        $mode = 'edit';
        $exemption = DB::table ('unified_exam_info')
            ->where('unified_exam_info.id',$id)
            ->join('admissions','unified_exam_info.student_id','=','admissions.id')
            ->join('unified_exam_type','unified_exam_info.unified_exam_type_id','=','unified_exam_type.id')
            ->select('admissions.studentno','admissions.fullname','unified_exam_type.type','unified_exam_info.*')
            ->first();

        if (isset( $exemption ) && $id) {

            // Show the page
            return View::make ( 'admin/unified_exam/edit_type', compact ('title','exemption','types','mode') );
        } else {
            return View::make ( 'admin/unified_exam/index_type', compact ( 'campuses','title') )->withError ( 'unified_exam ID not found' );

        }
    }

    public function postEditType($id) {

        $unified_exam_type = Input::get('unified_exam_type');
        $input_year = Input::get('input_year');
        $input_semester = Input::get('input_semester');

        $unifiedexaminfo=UnifiedExamInfo::find($id);

        //        var_dump($unifiedexaminfo);

        $unifiedexaminfo->registration_year = Config::get('customsettings.admissionyear');;
        $unifiedexaminfo->registration_semester =  Config::get('customsettings.semester');
        $unifiedexaminfo->unified_exam_type_id = $unified_exam_type;
        $unifiedexaminfo->final_result = 2;
        $unifiedexaminfo->failure_cause = '';
        $unifiedexaminfo->is_deleted = 0;
        $unifiedexaminfo->created_at = new DateTime();
        $unifiedexaminfo->updated_at = new DateTime();
        $unifiedexaminfo->save();

        if($unifiedexaminfo->save()){
            return Redirect::to('/admin/unified_exam/index_type') ->with('success', Lang::get('admin/unified_exam/messages.edit.success'));
        }else{

            return Redirect::to('admin/unified_exam/'.$unifiedexaminfo->id .'/edit_type')->with('error', Lang::get('admin/unified_exam/messages.update.error'));
        }

        //    } else {
        //        return Redirect::to('admin/exemption')->withErrors($exemption->errors());
        //    }
    }

    public function getDeleteType($id)
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.exemption_delete');

        // Show the page
        return View::make('admin/unified_exam/delete_type', compact('id','title'));
    }

    public function postDeleteType($id)
    {
        // Check if we are not trying to delete ourselves
        $is_used = DB::table('unified_exam_info')
            ->where('unified_exam_type_id',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/unified_exam_type')->with('error', Lang::get('admin/exemption/messages.unified_exam_type.error'));
        }else {
            $unifiedexaminfo = UnifiedExamInfo::find($id);
            $unifiedexaminfo->is_deleted = 1;
            $unifiedexaminfo->save();
            $count = UnifiedExamInfo::find($id)
                ->where('is_deleted', 1)
                ->count();
            if ($count > 0) {
                // TODO needs to delete all of that exemption's content
                return Redirect::to('admin/unified_exam/index')->with('success', Lang::get('admin/unified_exam/messages.delete.success'));

            } else {
                // There was a problem deleting the exemptioninfo
                return Redirect::to('admin/unified_exam/index')->with('error', Lang::get('admin/unified_exam/messages.delete.error'));
            }
        }
    }

    /**
     * Unified Exam For Province
     */
    public function getNoPassForProvince($id) {
        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );
        $exemptions = DB::table('unified_exam_info')
            ->where('unified_exam_info.id', $id)
            ->leftjoin('admissions', function ($join) {
                $join->on('admissions.id', '=', 'unified_exam_info.student_id');
            })
            ->leftjoin('unified_exam_subject', function ($join) {
                $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
            })
            ->leftjoin('unified_exam_cause', function ($join) {
                $join->on('unified_exam_info.unified_exam_cause_id', '=', 'unified_exam_cause.id');
            })
            ->leftjoin('unified_exam_type', function ($join) {
                $join->on('unified_exam_info.unified_exam_type_id', '=', 'unified_exam_type.id');
            })
            ->leftjoin('campuses', function ($join) {
                $join->on('admissions.campuscode', '=', 'campuses.id');
            })
            ->leftjoin('rawprograms', function ($join) {
                $join->on('rawprograms.id', '=', 'admissions.programcode');
            })
            ->where('unified_exam_info.is_deleted',0)
            ->select('unified_exam_info.id', 'admissions.studentno as student_id', 'admissions.fullname as student_name', 'rawprograms.name as program_name','campuses.name as campus_name', 'unified_exam_info.registration_year', 'unified_exam_info.registration_semester','unified_exam_subject.subject as subject_name', 'unified_exam_cause.cause as cause_name', 'unified_exam_type.type as type_name', 'unified_exam_info.final_result','unified_exam_info.failure_cause')
             ->get();

            return View::make ( 'admin/unified_exam/unified_exam_province_nopass', compact ('title','exemptions') );

    }

    public function postNoPassForProvince($id) {

        $failure_cause = Input::get('failure_cause');

        $unifiedexaminfo=UnifiedExamInfo::find($id);

        $unifiedexaminfo->failure_cause = $failure_cause;
        $unifiedexaminfo->final_result = 0;
        $unifiedexaminfo->created_at = new DateTime();
        $unifiedexaminfo->updated_at = new DateTime();
        $unifiedexaminfo->save();
            return Redirect::to('admin/unified_exam/approve_unified_exam');


    }

    public function getIndexForProvince()
    {
        // Title
        $title = Lang::get('admin/admin.unified_exam_province');
        $campuses = Campus::All();
        $schools =  School::All();
   //     $schools = School::All();
        return View::make('admin/unified_exam/unified_exam_province_index',compact('title','schools','campuses'));
    }

    public function getDataForProvince() {

        $filter = array();
        $filter_school = array();
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
        $schools = Input::get('school');
        $student_classification = Input::get('student_classification');
        $final_result = Input::get('final_result');
        $ids = explode(',',Input::get('selectedUnifiedExams'));
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
                DB::table('unified_exam_info')
                    ->whereIn('id', $ids)
                    ->update(array('final_result' => $final_result));
                DB::table('unified_exam_info')
                    ->whereIn('id', $ids)
                    ->update(array('failure_cause' => ''));
            }
        }

        if (!empty($student_id)) {
            $filter_student["studentno"] = '%'.$student_id. '%';
        }
        if (!empty($result)) {
            $filter["final_result"] = $result;
        }
        if (!empty($student_classification)) {
            $filter_student["enrollmenttype"] = $student_classification;
        }
        if (!empty($student_name)) {
            $filter_student["fullname"] = '%'.$student_name. '%';
        }

        if (!empty($campus)) {
            $filter_student['campuscode'] = $campus;
        }

        if (!empty($major_classification)) {
            $filter_student['program'] = $major_classification;
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
        if (!empty($filter_school))
        {
            $school_ids = DB::table('school_info')->select('id')
                ->where(function ($query) use ($filter_school) {
                    if (!is_array($filter_school)) {
                        return $query;
                    }
                    foreach ($filter_school as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->lists('id');
            $student_ids2 = DB::table('admissions')->select('id')
                ->whereIn('admissions.school_id',$school_ids)
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
            $programs = DB::table('unified_exam_info')->whereIn('unified_exam_info.student_id', $student_ids)
                    ->leftjoin('admissions', function ($join) {
                        $join->on('admissions.id', '=', 'unified_exam_info.student_id');
                    })
                    ->leftjoin('unified_exam_subject', function ($join) {
                        $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
                    })
                    ->leftjoin('unified_exam_cause', function ($join) {
                        $join->on('unified_exam_info.unified_exam_cause_id', '=', 'unified_exam_cause.id');
                    })
                    ->leftjoin('campuses', function ($join) {
                        $join->on('admissions.campuscode', '=', 'campuses.id');
                    })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                    ->where(function ($query) use ($filter) {
                        if (!is_array($filter)) {
                            return $query;
                        }
                        foreach ($filter as $key => $value) {
                            $query = $query->where('unified_exam_info.' . $key, 'like', $value);
                        }
                        return $query;
                    })
                ->where('unified_exam_info.is_deleted',0)
                ->where('unified_exam_subject_id',">",0)
                ->whereBetween('unified_exam_info.registration_year',array($start_year,$terminal_year))
                ->whereIn('unified_exam_info.registration_semester',$semester)
                ->select(array(
                    'unified_exam_info.id',
                    'admissions.studentno',
                    'admissions.fullname',
                    'campuses.name as campus_name',
                    'groups.name',
                    'unified_exam_info.registration_year',
                    'unified_exam_info.registration_semester',
                    'unified_exam_subject.subject',
                    'unified_exam_cause.cause',
                    'unified_exam_info.final_result',
                    'unified_exam_info.failure_cause'
                ));

        }elseif ($flag == 0) {
            $programs = DB::table('unified_exam_info')
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'unified_exam_info.student_id');
                })
                ->leftjoin('unified_exam_subject', function ($join) {
                    $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
                })
                ->leftjoin('unified_exam_cause', function ($join) {
                    $join->on('unified_exam_info.unified_exam_cause_id', '=', 'unified_exam_cause.id');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('admissions.campuscode', '=', 'campuses.id');
                })
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('groups', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('unified_exam_info.' . $key, 'like', $value);
                    }
                    return $query;
                })
                ->where('unified_exam_info.is_deleted',0)
                ->where('unified_exam_subject_id',">",0)
                ->whereBetween('unified_exam_info.registration_year',array($start_year,$terminal_year))
                ->whereIn('unified_exam_info.registration_semester',$semester)
                ->select(array(
                    'unified_exam_info.id',
                    'admissions.studentno',
                    'admissions.fullname',
                    'campuses.name as campus_name',
                    'groups.name',
                    'unified_exam_info.registration_year',
                    'unified_exam_info.registration_semester',
                    'unified_exam_subject.subject',
                    'unified_exam_cause.cause',
                    'unified_exam_info.final_result',
                    'unified_exam_info.failure_cause'
                ));
        }
        return Datatables::of($programs)
            ->edit_column('final_result', '@if($final_result == 0)
                                                        不通过
                                       @elseif($final_result == 1)
                                                        通过
                                       @elseif($final_result == 2)
                                                        未审核
                                       @endif')
            ->edit_column('registration_year','@if($registration_semester == 0)
                                                        {{$registration_year}} 秋季
                                          @elseif($registration_semester == 1)
                                                        {{$registration_year}} 春季
                                          @endif')
            ->add_column( 'actions', '@if($final_result == 1)
                                       <a href="{{ URL::to(\'admin/unified_exam/\' . $id .\'/unified_exam_province_nopass\')}} " class="iframe">不通过</a><br>
                                       <a href="{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_province_nocheck\' ) }}"  class="iframe">未审核</a>
                                   @elseif($final_result == 2)
                                       <a href="{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_province_pass\' ) }}" class="iframe">通过</a><br>
                                       <a href="{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_province_nopass\' ) }}" class="iframe">不通过</a>
                                   @elseif($final_result == 0)
                                       <a href="{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_province_pass\' ) }}"  class="iframe">通过</a><br>
                                      <a href="{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_province_nocheck\' ) }}"  class="iframe">未审核</a>
                                   @endif '
            )
            ->add_column( 'isCheck', '
                          <div align="center"><input type = "checkbox" name = "checkItem[]" id= "checkItem" value="{{ $id }}"></div> '
            )
            ->remove_column('registration_semester')
            ->remove_column('id')
            ->make();
    }

    public function getPassForProvince($id)
      {
          // Title
          $title = Lang::get('admin/unified_exam/title.exemption_pass');

          // Show the page
          return View::make('admin/unified_exam/unified_exam_province_pass', compact('id','title'));
      }

      public function postPassForProvince($id) {


          $unifiedexaminfo=UnifiedExamInfo::find($id);
          $unifiedexaminfo->failure_cause = '';
          $unifiedexaminfo->final_result = 1;
          $unifiedexaminfo->created_at = new DateTime();
          $unifiedexaminfo->updated_at = new DateTime();
          $unifiedexaminfo->save();

          return Redirect::to('admin/unified_exam/approve_unified_exam');


      }

      public function getNoCheckForProvince($id)
      {
          // Title
          $title = Lang::get('admin/unified_exam/title.exemption_nocheck');

          // Show the page
          return View::make('admin/unified_exam/unified_exam_province_nocheck', compact('id','title'));
      }

      public function postNoCheckForProvince($id) {


          $unifiedexaminfo=UnifiedExamInfo::find($id);
          $unifiedexaminfo->failure_cause = "";
          $unifiedexaminfo->final_result = 2;
          $unifiedexaminfo->created_at = new DateTime();
          $unifiedexaminfo->updated_at = new DateTime();
          $unifiedexaminfo->save();

          return Redirect::to('admin/unified_exam/approve_unified_exam');


      }



      /**
       * Unified Exam Cause
       */
    public function getIndexForCause() {
        // Title
        $title = Lang::get ( 'admin/admin.unified_exam_cause' );

        //      $exemptions =ExemptionMajorOuter::all();
        // Show the page
        return View::make ( 'admin/unified_exam/unified_exam_cause_index', compact ('title'));
    }

    public function getCreateCause() {

        $title = Lang::get ( 'admin/unified_exam/table.create_a_new_cause' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/unified_exam/unified_exam_cause_define_edit', compact ('title', 'mode') );
    }

    public function postCreateCause() {

        $cause = new UnifiedCause();
        $cause->code = Input::get('code');
        $cause->cause = Input::get('cause');

        if ($cause->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('unified_exam_cause')
                ->where('code', Input::get('code'))
                ->where('cause', Input::get('cause'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/unified_exam/data_add_unified_exam_cause')->withErrors('error',Lang::get ( 'admin/unified_exam/messages.already_exists'));
            }

            $cause->save();

        } else {
            return Redirect::to('admin/unified_exam/unified_exam_cause')->withErrors($cause->errors());
        }

        return Redirect::to('admin/unified_exam/data_add_unified_exam_cause')->with('success', Lang::get('admin/unified_exam/messages.create.success'));



    }

    public function getDataForCause() {

        $programs = DB::table ('unified_exam_cause')
            ->where('is_deleted',0)
            ->select( 'id', 'cause','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_cause_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_cause_delete\' ) }}}"  class="iframe"> {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditCause($id) {
        $title = Lang::get ( 'admin/unified_exam/title.edit_unified_cause' );

        // Mode
        $mode = 'edit';

        $cause = UnifiedCause::find ( $id );

        if (isset ( $cause ) && $cause->id) {

            // Show the page
            return View::make ( 'admin/unified_exam/unified_exam_cause_define_edit', compact ( 'title', 'mode', 'cause'));
        } else {
            return View::make ( 'admin/unified_exam/unified_exam_cause_index', compact ( 'title' , 'mode') )->withError ( 'Cause ID not found' );

        }
    }

    public function postEditCause($id) {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
//        $mode = 'create';

        $cause = UnifiedCause::find ( $id );
        if (isset ( $cause ) && $cause->id) {
            $cause->cause = Input::get('cause');
            $cause->code = Input::get('code');

            if ($cause->validate(Input::all())) {

                $cause->save();
            } else {
                return Redirect::to('admin/unified_exam_cause')->withErrors($cause->errors());
            }

            return Redirect::to('admin/unified_exam/'.$id.'/unified_exam_cause_edit')->withInput()->with('success', Lang::get('admin/unified_exam/messages.edit.success'));

        }
    }

    public function getDeleteCause($id)
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.delete_unified_cause');
        $cause = DB::table('unified_exam_cause')
                ->where('id',$id)
                ->first();

        // Show the page
        return View::make('admin/unified_exam/unified_exam_cause_delete', compact('id','cause', 'title'));
    }

    public function postDeleteCause($id )
    {
        $is_used = DB::table('unified_exam_info')
            ->where('unified_exam_cause_id',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/unified_exam/unified_exam_cause')->with('error', Lang::get('admin/unified_exam/messages.unified_exam_cause.error'));
        }else {
            // Was the comment post deleted?
            $cause = UnifiedCause::find($id);
            $cause->is_deleted = 1;
            $cause->save();

            $count = DB::table('unified_exam_cause')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count != 0) {
                return Redirect::to('admin/unified_exam/unified_exam_cause')->with('success', Lang::get('admin/unified_exam/messages.delete.success'));
            } else {
                return Redirect::to('admin/unified_exam/unified_exam_cause')->with('error', Lang::get('admin/unified_exam/messages.delete.error'));
            }
        }
    }


    /**
     * Unified Exam Subject
     */
    public function getIndexForSubject() {
        // Title
        $title = Lang::get ( 'admin/admin.unified_exam_subject' );

        //      $exemptions =ExemptionMajorOuter::all();
        // Show the page
        return View::make ( 'admin/unified_exam/unified_exam_subject_index', compact ('title'));
    }

    public function getCreateSubject() {

        $title = Lang::get ( 'admin/unified_exam/table.create_a_new_subject' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/unified_exam/unified_exam_subject_define_edit', compact ('title', 'mode') );
    }

    public function postCreateSubject() {

        $subject = new UnifiedSubject();
        $subject->code = Input::get('code');
        $subject->subject = Input::get('subject');

        if ($subject->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('unified_exam_subject')
                ->where('code', Input::get('code'))
                ->where('subject', Input::get('subject'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/unified_exam/data_add_unified_exam_subject')->withErrors('error',Lang::get ( 'admin/unified_exam/messages.already_exists'));
            }
//
            $subject->save();
        } else {
            return Redirect::to('admin/unified_exam_subject')->withErrors($subject->errors());
        }

        return Redirect::to('admin/unified_exam/data_add_unified_exam_subject')->with('success', Lang::get('admin/unified_exam/messages.create.success'));



    }

    public function getDataForSubject() {

        $programs = DB::table ('unified_exam_subject')
            ->where('is_deleted',0)
            ->select( 'id', 'subject','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_subject_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_subject_delete\' ) }}}"  class="iframe"> {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditSubject($id) {
        $title = Lang::get ( 'admin/unified_exam/title.edit_unified_subject' );

        // Mode
        $mode = 'edit';

        $subject = UnifiedSubject::find ( $id );
        if (isset ( $subject ) && $subject->id) {

            //prepare available 專業 list

            // Show the page
            return View::make ( 'admin/unified_exam/unified_exam_subject_define_edit', compact ( 'title', 'mode', 'subject'));
        } else {
            return View::make ( 'admin/unified_exam/unified_exam_subject_index', compact ( 'title' , 'mode') )->withError ( 'Major ID not found' );

        }
    }

    public function postEditSubject($id) {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
//        $mode = 'create';

        $subject = UnifiedSubject::find ( $id );
        if (isset ( $subject ) && $subject->id) {
            $subject->subject = Input::get('subject');
            $subject->code = Input::get('code');

            if ($subject->validate(Input::all())) {

                $subject->save();
            } else {
                return Redirect::to('admin/unified_exam_subject')->withErrors($subject->errors());
            }

            return Redirect::to('admin/unified_exam/'.$id.'/unified_exam_subject_edit')->withInput()->with('success', Lang::get('admin/unified_exam/messages.edit.success'));

        }
    }

    public function getDeleteSubject($id)
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.delete_unified_subject');
        $subject = DB::table('unified_exam_subject')->where('id',$id)->first();
        // Show the page
        return View::make('admin/unified_exam/unified_exam_subject_delete', compact('id','subject', 'title'));
    }

    public function postDeleteSubject($id )
    {
        $is_used = DB::table('unified_exam_info')
            ->where('unified_exam_subject_id',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/unified_exam/unified_exam_subject')->with('error', Lang::get('admin/exemption/messages.unified_exam_subject.error'));
        }else {
            // Was the comment post deleted?
            $subject = UnifiedSubject::find($id);
            $subject->is_deleted = 1;
            $subject->save();

            $count = DB::table('unified_exam_subject')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count != 0) {
                return Redirect::to('admin/unified_exam_subject')->with('success', Lang::get('admin/unified_exam/messages.delete.success'));
            } else {
                // There was a problem deleting the user
                return Redirect::to('admin/unified_exam_subject')->with('error', Lang::get('admin/unified_exam/messages.delete.error'));
            }
        }
    }


    /**
     * Unified Exam Type
     */
    public function getIndexForUnifiedExamType() {
        // Title
        $title = Lang::get ( 'admin/admin.unified_exam_type' );

        //      $exemptions =ExemptionMajorOuter::all();
        // Show the page
        return View::make ( 'admin/unified_exam/unified_exam_type_index', compact ('title'));
    }

    public function getCreateUnifiedExamType() {

        $title = Lang::get ( 'admin/unified_exam/table.create_a_new_type' );

        // Mode
        $mode = 'create';

        return View::make ( 'admin/unified_exam/unified_exam_type_define_edit', compact ('title', 'mode') );
    }

    public function postCreateUnifiedExamType() {

        $type = new UnifiedType();
        $type->code = Input::get('code');
        $type->type = Input::get('type');

        if ($type->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('unified_exam_type')
                ->where('code', Input::get('code'))
                ->where('type', Input::get('type'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/unified_exam/data_add_unified_exam_type')->withErrors('error',Lang::get ( 'admin/unified_exam/messages.already_exists'));
            }
//
            $type->save();
        } else {
            return Redirect::to('admin/unified_exam_type')->withErrors($type->errors());
        }

//        return Redirect::to('admin/edu_department/'.$id.'/edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));
        return Redirect::to('admin/unified_exam/create_unified_exam_type')->with('success', Lang::get('admin/unified_exam/messages.create.success'));



    }

    public function getDataForUnifiedExamType() {

        $programs = DB::table ('unified_exam_type')
            ->where('is_deleted',0)
            ->select( 'id', 'type','code');

        return Datatables::of ($programs)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_type_edit\' ) }}}" class="iframe"> {{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/unified_exam/\' . $id . \'/unified_exam_type_delete\' ) }}}"  class="iframe"> {{{ Lang::get(\'button.delete\') }}}</a>
                                ')
            ->remove_column('id')
            ->make ();
    }

    public function getEditUnifiedExamType($id) {
        $title = Lang::get ( 'admin/unified_exam/title.edit_unified_type' );

        // Mode
        $mode = 'edit';
//        $action = 'edit';

        $type = UnifiedType::find ( $id );
        if (isset ( $type ) && $type->id) {

            //prepare available 專業 list

            // Show the page
            return View::make ( 'admin/unified_exam/unified_exam_type_define_edit', compact ( 'title', 'mode', 'type'));
        } else {
            return View::make ( 'admin/unified_exam/unified_exam_type_index', compact ( 'title' , 'mode') )->withError ( 'Type ID not found' );

        }
    }

    public function postEditUnifiedExamType($id) {
//        $result = INPUT::ALL();
//

        $type = UnifiedType::find ( $id );
        if (isset ( $type ) && $type->id) {
            $type->type = Input::get('type');
            $type->code = Input::get('code');

            if ($type->validate(Input::all())) {

                $type->save();
            } else {
                return Redirect::to('admin/unified_exam_type')->withErrors($type->errors());
            }

            return Redirect::to('admin/unified_exam/'.$id.'/unified_exam_type_edit')->withInput()->with('success', Lang::get('admin/unified_exam/messages.edit.success'));

        }
    }

    public function getDeleteUnifiedExamType($id)
    {
        // Title
        $title = Lang::get('admin/unified_exam/title.delete_unified_type');
        $type = DB::table('unified_exam_type')->where('id',$id)->first();
        // Show the page
        return View::make('admin/unified_exam/unified_exam_type_delete', compact('id', 'type','title'));
    }

    public function postDeleteUnifiedExamType($id )
    {
        $is_used = DB::table('unified_exam_info')
            ->where('unified_exam_type_id',$id)
            ->count();
        if ($is_used > 0) {
            return Redirect::to('admin/unified_exam_type')->with('error', Lang::get('admin/exemption/messages.unified_exam_type.error'));
        }else {
            // Was the comment post deleted?

            $type = UnifiedType::find($id);
            $type->is_deleted = 1;
            $type->save();

            $count = DB::table('unified_exam_type')
                ->where('id', $id)
                ->where('is_deleted', 1)
                ->count();

            if ($count != 0) {

                return Redirect::to('admin/unified_exam_type')->with('success', Lang::get('admin/unified_exam/messages.delete.success'));
            } else {
                // There was a problem deleting the user
                return Redirect::to('admin/unified_exam_type')->with('error', Lang::get('admin/unified_exam/messages.delete.error'));
            }
        }
    }
}