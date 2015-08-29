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

        $exemptions = DB::table ('exemption_info')
            ->join('stuinfo','exemption_info.student_id','=','stuinfo.student_id')
            ->join('course','exemption_info.course_id','=','course.course_id')
            ->join('exemption_agency','exemption_info.agency_id','=','exemption_agency.id')
            ->select('stuinfo.student_id','stuinfo.student_name','course.name as course_inside','course.classification','course.credit','exemption_info.*','exemption_agency.agency_name')
            ->get();

        //var_dump($exemptions);
        // Show the page
        return View::make('admin/exemption/index', compact('exemptions','title'));
    }

    public function getDataForAdd() {
        $exemptions = DB::table ('exemption_info')
            ->join('stuinfo','exemption_info.student_id','=','stuinfo.student_id')
            ->join('course','student_selection.course_id','=','course.id')
            ->select('stuinfo.student_id','stuinfo.student_name','course.name as course_inside','course.classification','course.credit','exemption_info.*')
            ->get();
        return Datatables::of ( $exemptions )
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/exemption/\' . $exemption_id . \'/edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/exemption/\' . $exemption_id . \'/delete\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.delete\') }}}</a>
                                '
            )
            ->remove_column('exemption_id')
            ->make ();
    }


    public function getInputStudent()
    {

        $title = Lang::get('admin/exemption/title.input_stuinfo');


        return View::make('admin/exemption/input_student', compact('title'));
    }



    // query studentinfo
    public function postQuery()
    {

        // make a new validator object
        // $v = Validator::make(Input::all(), Stuinfo::$rules);
        //    Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))
        // check for failure
        //   if ($v->passes()) {
        // Title
        $title = Lang::get('admin/exemption/title.choose_stuinfo');
        $sno = Input::get('student_id');
        $sname = Input::get('student_name');
        //get the stuinfo
        $exemptions = DB::table('stuinfo')->where('student_id', 'like', '%' . $sno . '%')->where('student_name', 'like', '%' . $sname . '%')->get();

        // Show the page
        return View::make('admin/exemption/query', compact('exemptions', 'title'));

    }


   public function postRequire()
   {

       // make a new validator object
       // $v = Validator::make(Input::all(), Stuinfo::$rules);
       //    Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))
       // check for failure
       //   if ($v->passes()) {
       // Title
       $title = Lang::get('admin/exemption/title.input_score_stuinfo');
       $sno = $_POST['student_id'];

       // validate the flag
      if (emptyString($sno)) {

          $exemptions = DB::table('stuinfo')->where('student_id', '=', $sno)->get();
   //         var_dump($stuinfo);
       // Show the page
             return View::make('admin/exemption/input_require', compact('exemptions', 'title'));

       } else {

             return View::make('admin/exemption/query') ->with('友情提醒', Lang::get('admin/exemption/messages.pleaseselect'));

       }

   }

    public function postSelection(){

        $title = Lang::get('admin/exemption/title.input_course_name_outer');
        $student_id = $_POST['student_id'];

        $exemptions = DB::table('student_selection')
            ->where('student_selection.student_id',$student_id)
            ->join('stuinfo','student_selection.student_id','=','stuinfo.student_id')
            ->join('course','student_selection.course_id','=','course.id')
            ->select('stuinfo.student_id','course.id as course_id','course.name as course_name','stuinfo.classification','course.credit','student_selection.selection_status')
            ->get();
        return View::make('admin/exemption/student_selection', compact('exemptions','title'));
        //      return Datatables::of ( $programs )
        //      ->make ();

    }

    public function postInsertExemption()
    {

        // make a new validator object
        // $v = Validator::make(Input::all(), Stuinfo::$rules);
        //    Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))
        // check for failure
        //   if ($v->passes()) {
        // Title
        //  $title = Lang::get('admin/exemption/title.choose_course');
        $course_id = $_POST['course_id'];
        $student_id = $_POST['student_id'];
        $exemptions = DB::table('student_selection')
            ->where('student_selection.course_id','=',$course_id)
            ->where('stuinfo.student_id','=',$student_id)
            ->join('course','student_selection.course_id','=','course.id')
            ->join('stuinfo','student_selection.student_id','=','stuinfo.student_id')
            ->select('stuinfo.student_id as student_id','course.id as course_id','course.name as course_name','stuinfo.classification','course.credit')
            ->get();

        $majors = DB::table('exemption_major_outer')->get();
        $agencys = DB::table('exemption_agency')->get();
        $types = DB::table('exemption_type')->get();
        return View::make('admin/exemption/insert_exemption',compact('exemptions','majors','agencys','types'))->with('student_id');

    }

//save info into table exemption_info
    public function postIntoExemption()
    {

        // make a new validator object
        // $v = Validator::make(Input::all(), Stuinfo::$rules);
        //    Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))
        //     check for failure
        //   if ($v->passes()) {
        // Title
        //  $title = Lang::get('admin/exemption/title.choose_course');
        $course_id = $_POST['course_id'];
        $student_id = $_POST['student_id'];
        $results = Input::except('_token');

        var_dump($results);

        $exemptioninfo = new ExemptionInfo();
        $exemptioninfo->course_id = $course_id;
        $exemptioninfo->course_name_outer = $results['course_outer'];
        $exemptioninfo->student_id =$student_id;
        $exemptioninfo->credit_outer = $results['credit_outer'];
        $exemptioninfo->major_name_outer = $results['major_outer'];
        $exemptioninfo->agency_id = $results['agency_id'];
        $exemptioninfo->certification_year = $results['certification_year'];
        $exemptioninfo->application_year = 2015;
        $exemptioninfo->application_semester = 0;
        $exemptioninfo->exemption_type_id = $results['exemption_type_id'];
        $exemptioninfo->classification_outer = $results['classification_outer'];
        $exemptioninfo->score = $results['score'];
        $exemptioninfo->remark = $results['remark'];
        $exemptioninfo->final_result = 2;
        $exemptioninfo->failure_cause = '111';
        $exemptioninfo->is_deleted = '0';
        $exemptioninfo->created_at = new DateTime();
        $exemptioninfo->updated_at = new DateTime();

/*
        if ($exemptioninfo->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('exemtpion_info')
                ->where('course_id', $course_id)
                ->where('student_id', $student_id)
                ->count();

            if ($recordexists > 0) {
                return View::make('admin/exemption/insert_exemption')->withErrors('已经添加该免修课程信息');
            }

*/
            $exemptioninfo->save();
/*
       } else {
            return View::make('admin/exemption/insert_exemption')->withErrors($exemptioninfo->errors());
        }
  */
        return View::make('admin/exemption');

/*
      $exemption=DB::table('exemtpion_info')
            ->join('course','exemtpion_info.course_id','=','course.id')
            ->join('stuinfo','exemtpion_info.student_id','=','stuinfo.student_id')
            ->select('stuinfo.student_id as student_id','stuinfo.student_name','stuinfo.major','course.id as course_id','course.name as course_name','stuinfo.classification','course.credit')
            ->get();

       var_dump($exemption);
      return View::make('admin/exemption/query_exemption',compact('exemption'));
*/
    }

}