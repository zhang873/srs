<?php

class AdminAdmissionController extends AdminController
{
    protected $admissions;
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

        $action = Input::get('state');

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
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/edit_admissions_province/\')}}">修改该生信息</a>
                           ')
            ->make();

    }

    public function getApproveChangeAdmissionsIndexForProvince()
    {
        $title = Lang::get('admin/admissions/title.check_change_admissions');
        $campuses = Campus::All();
        $rawprograms = RawProgram::All();
        return View::make('admin/admissions/admissions_approve_change_province', compact('rawprograms', 'campuses', 'title'));
    }

    public function getDataApproveChangingAdmissionsForProvince()
    {

        $filter = array();
        $filter_school = array();
        $filter_group = array();
        $student_ids = array();

        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $state = Input::get('state');
        $flag = 0;
        $ids = explode(',',Input::get('selectedAdmissions'));
        if ($state == 1){
            if (!empty($ids)){
                DB::table('student_status_changing')->whereIn('id',$ids)
                    ->update(array('approval_status'=>1));
                DB::table('admissions')->join('student_status_changing','student_status_changing.student_id','=','admissions.id')
                    ->whereIn('student_status_changing.id',$ids)
                    ->update(array('admissions.status'=>4));
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

  /*  if (!empty($school)) {
            $program_ids = DB::table('school_info')->select('id')
               ->where('id',$school)
                ->lists('id');
            $student_ids1 = DB::table('admission_group')->select('admission_group.admission_id as id')
                ->whereIn('admission_group.group_id', $program_ids)
                ->lists('id');
            $flag = 1;

        }

    */
    $student_ids = DB::table('admissions')
        ->where(function ($query) use ($filter) {
            if (!is_array($filter)) {
                return $query;
            }
            foreach ($filter as $key => $value) {
                $query = $query->where('admissions.'.$key, 'like', $value);
            }
            return $query;
        })
        ->select('id')
        ->lists('id');

            $programs = DB::table('student_status_changing')
                ->whereIn('student_status_changing.student_id',$student_ids)
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
 //               ->where('student_status_changing.current_class_id',">",0)
                ->select('student_status_changing.id','student_status_changing.application_year','student_status_changing.application_semester',
                    'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admissions.status',
                    'rawprograms.name as pname','student_status_changing.current_major_id',
                   'campuses.name as cname','student_status_changing.current_campus_id',
                    'groups.name as gname','student_status_changing.cause',
                    'student_status_changing.approval_status','student_status_changing.remark'
                    );

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
            ->edit_column('approval_status','@if ($approval_status == 0)
                                      未审核
                                 @elseif ($approval_status == 1)
                                      同意
                                 @elseif ($approval_status == 2)
                                      不同意
                                 @endif
')
            ->add_column( 'actions', '@if($approval_status == 1)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id .\'/admissions_change_nopass\')}} ">不同意</a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_change_no_approve\' ) }}" >未审核</a>
                                   @elseif($approval_status == 0)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_change_pass\' ) }}" >通过</a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_change_nopass\' ) }}">不同意</a>
                                   @elseif($approval_status == 2)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_change_pass\' ) }}" >通过</a><br>
                                      <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_change_no_approve\' ) }}" >未审核</a>
                                   @endif '
            )

            ->add_column( 'isCheck', '
                          <div align="center"><input type = "checkbox" name = "checkItem[]" id= "checkItem" value="{{ $id }}"></div> '
            )
            ->remove_column('id')
            ->make();

    }

    public function getNoPassChangeForProvince($id){

    $title = Lang::get('admin/admissions/title.input_no_pass_cause');
    return View::make('admin/admissions/admissions_change_province_nopass',compact('title','id'));
}

    public function postNoPassChangeForProvince($id) {

        $failure_cause = Input::get('remark'.$id);

        $admissionchanging=AdmissionChanging::find($id);

        $admissionchanging->cause = $failure_cause;
        $admissionchanging->approval_status = 2;
        $admissionchanging->created_at = new DateTime();
        $admissionchanging->updated_at = new DateTime();
        $admissionchanging->save();
        DB::table('admissions')->join('student_status_changing','student_status_changing.student_id','=','admissions.id')
            ->where('student_status_changing.id',$id)->update(array('admissions.status'=>4));
        return Redirect::to('admin/admissions/approve_admission_changing')->with('success',Lang::get('admin/admissions/messages.changing_success'));


    }


    public function getNoCheckChangeForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_change');
        return View::make('admin/admissions/admissions_change_province_nocheck',compact('title','id'));
    }

    public function postNoCheckChangeForProvince($id) {

        $admissionchanging=AdmissionChanging::find($id);

        $admissionchanging->approval_status = 0;
        $admissionchanging->created_at = new DateTime();
        $admissionchanging->updated_at = new DateTime();
        $admissionchanging->save();

        DB::table('admissions')->join('student_status_changing','student_status_changing.student_id','=','admissions.id')
            ->where('student_status_changing.id',$id)->update(array('admissions.status'=>5));
        return Redirect::to('admin/admissions/approve_admission_changing')->with('success',Lang::get('admin/admissions/messages.changing_success'));


    }

    public function getPassChangeForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_change');
        return View::make('admin/admissions/admissions_change_province_pass',compact('title','id'));
    }

    public function postPassChangeForProvince($id) {

        $admissionchanging=AdmissionChanging::find($id);

        $admissionchanging->approval_status = 1;
        $admissionchanging->created_at = new DateTime();
        $admissionchanging->updated_at = new DateTime();
        $admissionchanging->save();
        DB::table('admissions')->join('student_status_changing','student_status_changing.student_id','=','admissions.id')
            ->where('student_status_changing.id',$id)->update(array('admissions.status'=>4));
        return Redirect::to('admin/admissions/approve_admission_changing')->with('success',Lang::get('admin/admissions/messages.changing_success'));


    }

    public function getIndexForRecordAdmissionsRewardPunishProvince()
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $rawprograms = RawProgram::All();
        $campuses = Campus::All();
        return View::make('admin/admissions/admissions_record_reward_punish_province', compact('campuses', 'rawprograms', 'title'));
    }

    public function getDataForRecordAdmissionsRewardPunishProvince()
    {
        //    $groups  =AdminGroup::All();
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $filter_school = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $school = Input::get('school');
        $campus_id = Input::get('campus');
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
        if (!empty($campus_id)) {
            $filter['campuscode'] = $campus_id;
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
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_reward/\')}}" class="iframe">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_punish/\')}}" class="iframe">惩罚</a>
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

    public function getIndexForApproveAdmissionsRewardPunishProvince()
    {
        $title = Lang::get('admin/admissions/title.approve_reward_punish');
        $rawprograms = RawProgram::All();
        $campuses = Campus::All();
        return View::make('admin/admissions/admissions_approve_reward_punish_province', compact('campuses', 'rawprograms', 'title'));
    }

    public function getDataForCheckAdmissionsRewardPunishProvince()
    {
        //    $groups  =AdminGroup::All();

        $filter = array();
        $filter_program = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
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
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
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
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
                })
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
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
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno','campuses.name',
                    'rawprograms.name as major_name','rawprograms.type', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');

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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname', 'admissions.studentno','campuses.name',
                    'rawprograms.name as major_name','rawprograms.type', 'admissions.dateofbirth',
                    'admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan');
        }
        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('gender', '@if ($gender == \'f\')
                                          女
                                 @elseif ($gender == \'m\')
                                          男
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
            ->add_column('actions', '
                           <a href="{{URL::to(\'admin/admissions/define_reward_province/?id=\'.$id)}}" class="iframe">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/define_punish_province/?id=\'.$id)}}" class="iframe">惩罚</a>
                           ')
            ->remove_column('id')
            ->make();

    }

    public function getIndexForEliminationProvince()
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
        return View::make('admin/admissions/admissions_elimination_province', compact('majors', 'campuses', 'schools', 'title'));
    }

    public function getDataForEliminationProvince()
    {

        $filter = array();
        $filter_school = array();

        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $flag = 0;

        $action = Input::get('state');

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }

        if (!empty($school)) {
            $filter_school["id"] = $school;
        }
        if (!empty($major)) {
        $filter['programcode'] = $major;
        }
        if (!empty($major_classification)) {
            $filter['program'] = $major_classification;
        }

    /*    if (!empty($filter_school)) {
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
            $student_ids = DB::table('admission_group')->select('admission_group.admission_id as id')
                ->join('groups','groups.id','=','admission_group.group_id')
                ->whereIn('groups.school_id', $school_ids)
                ->lists('id');
            $flag = 1;

        }

*/
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
                    $query = $query->where('admissions.status',4) -> orwhere('admissions.status',7);
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    $query = $query->where('admissions.status',4) -> orwhere('admissions.status',7);
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id',
                    'campuses.name as campus_name','rawprograms.name','admissions.program','groups.sysid',
                    'admissions.dateofbirth','admissions.gender', 'admissions.nationgroup', 'admissions.politicalstatus',
                    'admission_details.formerlevel', 'admissions.jiguan', 'admissions.status');
        }

        return Datatables::of($programs)
            ->edit_column('program', '@if($program == 12)
                                          本科
                                 @elseif($program == 14)
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
                                    <a href="{{URL::to(\'admin/admissions/\'.$id.\'/admissions_elimination_details\')}}">开除</a>
                               @endif
                           ')
            ->add_column('cancel_elimination', '@if($status==7)
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/admissions_cancel_elimination/\')}}">撤销</a>
                           @else
                           <a>撤销</a>
                           @endif
                           ')
            ->remove_column('id')
            ->make();

    }


    public function getEliminationDetailsProvince($id)
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
        return View::make('admin/admissions/admissions_elimination_details_province',compact('id','admissions','yearsemester','title'));

    }

    public function postEliminationDetailsProvince($id)
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

    public function getCancelEliminationProvince($id)
    {
        $title = Lang::get('admin/admissions/title.cancel_expel_admissions');
        $admission = DB::table('admissions')
            ->select('studentno','fullname')
            ->where('id',$id)
            ->first();
        return View::make('admin/admissions/admissions_cancel_elimination_province',compact('id','admission','title'));

    }

    public function postCancelEliminationProvince($id)
    {

        $admission = Admission::find($id);
        $admission->status = 4;
        $admission->save();

        AdmissionExpel::where('student_id',$id)->update(array('is_deleted'=>1));

        return Redirect::to('admin/admissions/expel_admissions')->with('success',Lang::get('admin/admissions/messages.expel.cancel'));

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

        if($state == 1){
            if (!empty($ids)) {
                DB::table('admissions')
                    ->join('student_dropout','student_dropout.student_id','=','admissions.id')
                    ->whereIn('student_dropout.id', $ids)
                    ->update(array('admissions.status' => 7));
            }
            DB::table('student_dropout')->whereIn('id',$ids)->update(array('approval_result_province'=>1));
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
                                <input type="text" id="approval_suggestion_province[]" name="approval_suggestion_province" value="{{$approval_suggestion_province}}" style="width: 60px">
            ')
            ->add_column( 'actions', '@if($approval_result_province == 1)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id .\'/admissions_dropout_nopass\')}} ">不同意</a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_dropout_no_approve\' ) }}" >未审核</a>
                                   @elseif($approval_result_province == 0)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_dropout_pass\' ) }}" >通过</a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_dropout_nopass\' ) }}">不同意</a>
                                   @elseif($approval_result_province == 2)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_dropout_pass\' ) }}" >通过</a><br>
                                      <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_dropout_no_approve\' ) }}" >未审核</a>
                                   @endif '
            )
            ->add_column( 'isCheck', '
                                  <div align="center"><input type = "checkbox" name = "checkItem[]"  id= "checkItem" value="{{$id}}"></div>
                                   ')
            ->remove_column('id')
            ->make();

    }

    public function getNoPassDropOutForProvince($id){

        $title = Lang::get('admin/admissions/title.input_no_pass_cause');
        return View::make('admin/admissions/admissions_dropout_province_nopass',compact('title','id'));
    }

    public function postNoPassDropOutForProvince($id) {

        $failure_cause = Input::get('remark'.$id);

        $dropout=AdmissionDropOut::find($id);

        $dropout->cause = $failure_cause;
        $dropout->approval_result_province = 2;
        $dropout->created_at = new DateTime();
        $dropout->updated_at = new DateTime();
        $dropout->save();

        DB::table('admissions')->join('student_dropout','student_dropout.student_id','=','admissions.id')
            ->where('student_dropout.id',$id)->update(array('admissions.status'=>4));

        return Redirect::to('admin/admissions/approve_dropout')->with('success',Lang::get('admin/admissions/messages.approve_success'));


    }


    public function getNoApproveDropOutForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_change');
        return View::make('admin/admissions/admissions_dropout_province_nocheck',compact('title','id'));
    }

    public function postNoApproveDropOutForProvince($id) {

        $dropout=AdmissionDropOut::find($id);

        $dropout->approval_result_province = 0;
        $dropout->created_at = new DateTime();
        $dropout->updated_at = new DateTime();
        $dropout->save();

        DB::table('admissions')->join('student_dropout','student_dropout.student_id','=','admissions.id')
            ->where('student_dropout.id',$id)->update(array('admissions.status'=>4));
        return Redirect::to('admin/admissions/approve_dropout')->with('success',Lang::get('admin/admissions/messages.approve_success'));


    }

    public function getPassDropOutForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_change');
        return View::make('admin/admissions/admissions_dropout_province_pass',compact('title','id'));
    }

    public function postPassDropOutForProvince($id) {

        $dropout=AdmissionDropOut::find($id);

        $dropout->approval_result_province = 1;
        $dropout->created_at = new DateTime();
        $dropout->updated_at = new DateTime();
        $dropout->save();

        DB::table('admissions')->join('student_dropout','student_dropout.student_id','=','admissions.id')
            ->where('student_dropout.id',$id)->update(array('admissions.status'=>7));

        return Redirect::to('admin/admissions/approve_dropout')->with('success',Lang::get('admin/admissions/messages.approve_success'));


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
            ->edi_column('remark','<input type="text" name="remark{{$id}}" id="remark{{$id}}" style="width:60px"')
            ->add_column( 'actions', '@if($approval_result == 1)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id .\'/admissions_recovery_nopass\')}} "><label id="btnNoPass">不同意</label></a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_recovery_no_approve\' ) }}" <label id="btnNoApprove">未审核</label>>未审核</a>
                                   @elseif($approval_result == 0)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_recovery_pass\' ) }}" >通过</a><br>
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_recovery_nopass\' ) }}" <label id="btnNoPass">不同意</label>>不同意</a>
                                   @elseif($approval_result == 2)
                                       <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_recovery_pass\' ) }}" >通过</a><br>
                                      <a href="{{ URL::to(\'admin/admissions/\' . $id . \'/admissions_recovery_no_approve\' ) }}" <label id="btnNoApprove">未审核</label>>未审核</a>
                                   @endif '
            )
            ->add_column( 'isCheck', '
                                  <div align="center"><input type = "checkbox" name = "checkItem[]"  id= "checkItem" value="{{$id}}"></div>
                                   ')
            ->remove_column('id')
            ->make();

    }



    public function getNoPassRecoveryForProvince($id){

        $title = Lang::get('admin/admissions/title.input_no_pass_cause');
        return View::make('admin/admissions/admissions_recovery_province_nopass',compact('title','id'));
    }

    public function postNoPassRecoveryForProvince($id) {

        $failure_cause = Input::get('remark'.$id);

        $recovery=AdmissionRecovery::find($id);

        $recovery->cause = $failure_cause;
        $recovery->approval_result = 2;
        $recovery->created_at = new DateTime();
        $recovery->updated_at = new DateTime();
        $recovery->save();
        DB::table('admissions')->join('student_recovery','student_recovery.student_id','=','admissions.id')
            ->where('student_recovery.id',$id)->update(array('admissions.status'=>6));
        return Redirect::to('admin/admissions/approve_recovery')->with('success',Lang::get('admin/admissions/messages.approve_success'));


    }


    public function getNoApproveRecoveryForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_recovery');
        return View::make('admin/admissions/admissions_recovery_province_nocheck',compact('title','id'));
    }

    public function postNoApproveRecoveryForProvince($id) {

        $recovery=AdmissionRecovery::find($id);

        $recovery->approval_result = 0;
        $recovery->created_at = new DateTime();
        $recovery->updated_at = new DateTime();
        $recovery->save();

        DB::table('admissions')->join('student_recovery','student_recovery.student_id','=','admissions.id')
            ->where('student_recovery.id',$id)->update(array('admissions.status'=>6));
        return Redirect::to('admin/admissions/approve_recovery')->with('success',Lang::get('admin/admissions/messages.approve_success'));


    }

    public function getPassRecoveryForProvince($id){

        $title = Lang::get('admin/admissions/title.approve_recovery');
        return View::make('admin/admissions/admissions_recovery_province_pass',compact('title','id'));
    }

    public function postPassRecoveryForProvince($id) {

        $recovery=AdmissionRecovery::find($id);

        $recovery->approval_result = 1;
        $recovery->created_at = new DateTime();
        $recovery->updated_at = new DateTime();
        $recovery->save();

        DB::table('admissions')->join('student_recovery','student_recovery.student_id','=','admissions.id')
            ->where('student_recovery.id',$id)->update(array('admissions.status'=>4));

        return Redirect::to('admin/admissions/approve_recovery')->with('success',Lang::get('admin/admissions/messages.approve_success'));


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

    public function getAdmissionsUploadForProvince()
    {
        $title = Lang::get('admin/admissions/title.admissions_upload');
        $campuses = Campus::All();
        $schools = School::All();
        $rawprograms = RawProgram::All();
        return View::make('admin/admissions/admissions_upload_province', compact('schools','campuses','rawprograms','title'));
    }

    public function getDataAdmissionsUploadForProvince()
    {

        $filter = array();
        $filter_program = array();
        //   $filter_student = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major_classification = Input::get('major_classification');
        $major = Input::get('major');
        $campus = Input::get('campus');
        $school = Input::get('school');
        $admission_state = Input::get('admission_state');
        $flag = 0;

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($campus)) {
            $filter['campuscode'] = $campus;
        }
        /*
        if (!empty($admission_state)) {
               $filter["status"] = $admission_state;
           }
             if (!empty($school)) {
                   $filter["program"] = $school;
               }
           */
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select(array('admissions.id',
                    'admissions.fullname as student_name',
                    'admissions.studentno as student_id',
                    'campuses.name as campus_name',
                    'admissions.gender',
                    'admissions.idtype',
                    'admissions.idnumber',
                    'admissions.dateofbirth',
                    'admission_details.formerlevel',
                    'admission_details.attainmentcert',
                    'admissions.nationgroup',
                    'admission_details.formerschool',
                    'admission_details.dategraduated',
                    'admissions.politicalstatus',
                    'admissions.maritalstatus',
                    'admissions.jiguan',
                    'admissions.hukou',
                    'admissions.occupation',
                    'rawprograms.name',
                    'rawprograms.type',
                    'admissions.address as s1',
                    'admissions.address',
                    'admissions.postcode as s1',
                    'admissions.phone',
                    'admissions.address as s2',
                    'admissions.mobile',
                    'admissions.postcode',
                    'admissions.postcode as email',
                    'admissions.ksh'));
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select(array('admissions.id',
                    'admissions.fullname as student_name',
                    'admissions.studentno as student_id',
                    'campuses.name as campus_name',
                    'admissions.gender',
                    'admissions.idtype',
                    'admissions.idnumber',
                    'admissions.dateofbirth',
                    'admission_details.formerlevel',
                    'admission_details.attainmentcert',
                    'admissions.nationgroup',
                    'admission_details.formerschool',
                    'admission_details.dategraduated',
                    'admissions.politicalstatus',
                    'admissions.maritalstatus',
                    'admissions.jiguan',
                    'admissions.hukou',
                    'admissions.occupation',
                    'rawprograms.name',
                    'rawprograms.type',
                    'admissions.address as s1',
                    'admissions.address',
                    'admissions.postcode as s1',
                    'admissions.phone',
                    'admissions.address as s2',
                    'admissions.mobile',
                    'admissions.postcode',
                    'admissions.postcode as email',
                    'admissions.ksh'));
        }

        return Datatables::of($programs)
            ->edit_column('gender', '@if($gender == \'f\')
                                          女
                                 @elseif($gender == \'m\')
                                          男
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
            ->make();

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

        return View::make('admin/admissions/define_reward_province', compact('title'));
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
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'campuses.name', 'admissions.politicalstatus',
                'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou');

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
                ->where('admissions.id', $student_id)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();

            return View::make('admin/admissions/admissions_other_info_province_edit', compact('title','student_id', 'admission'));

        }else{
            return Redirect::to('admin/admissions/admissions_otherInfo')->withError(Lang::get('admin/admissions/messages.update.error'));
        }
    }

    public function postAdmissionsOtherInfoForProvince()
    {
        $title = Lang::get('admin/admissions/title.edit_admissions_other_info');
        $student_id = trim(Input::get('student_id'));
        $student_name = trim(Input::get('student_name'));

        $filter = array();
        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }

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
                        $query = $query->where('admissions.'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();

        return View::make('admin/admissions/admissions_other_info_province_edit', compact('title','student_id', 'admission'));

    }

    public function postSaveAdmissionsOtherInfoForProvince()
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
            return Redirect::to('admin/admissions/edit_admissions_otherInfo?student_id='.$student_id)->with('success', Lang::get('admin/admissions/messages.edit.success'));
        } else {
            // Redirect to the role page
            return Redirect::to('admin/admissions/edit_admissions_otherInfo?student_id='.$student_id)->withError(Lang::get('admin/admissions/messages.update.error'));
        }

    }




    public function getAdmissionsEditInfoForCampus()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');

        // Show the page

        return View::make('admin/admissions/admissions_edit_information_province', compact('title'));
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

    public function getEditBasics($id)
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_admissions_info');
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

    public function postEditBasics($id)
    {

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
            return Redirect::to("admin/admissions/" . $id . "/edit_admissions_province")->with('success',Lang::get('admin/admissions/messages.edit.success'));
        }else{
            return Redirect::to("admin/admissions/" . $id . "/edit_admissions_province");
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
        $title = Lang::get('admin/admissions/title.edit_admissions_other_info');
        $student_id = $_GET['student_id'];
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
                ->where('admissions.id', $student_id)
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
        $title = Lang::get('admin/admissions/title.edit_admissions_other_info');
        $student_id = trim(Input::get('student_id'));
        $student_name = trim(Input::get('student_name'));
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
             $filter["campuscode"] = $campus->id;

        if (!empty($filter)) {
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
                ->where('admissions.status',4)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();
        } else{
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
                ->where('admissions.status',4)
                ->select('admissions.*', 'admission_details.attainmentcert', 'admission_details.formerlevel', 'rawprograms.type', 'campuses.name as campus_name')
                ->first();
        }
        return View::make('admin/admissions/admissions_other_info_campus_edit', compact('title','admission'));

    }

     public function postSaveAdmissionsOtherInfo()
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
            return Redirect::to('admin/admissions/edit_admissions_other_info?student_id='.$student_id)->with('success', Lang::get('admin/admissions/messages.edit.success'));
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
        if (isset ($School) && $School->id) {
            $School->school_id = Input::get('school_id');
            $School->school_name = Input::get('school_name');

            if ($School->validate(Input::all())) {

                $School->save();
            } else {
                return Redirect::to('admin/school')->withErrors($School->errors());
            }

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
    /*    $schoolcampus = DB::table('school_campus')->where('school_id',$id)->select('campus_id')->lists('campus_id');
        $campuses = DB::table('campuses')->select('id', 'name', 'sysid')->get();
        return View::make('admin/admissions/school_add_campus', compact('id', 'school','campuses','schoolcampus' ,'title'));
   */
        return View::make('admin/admissions/school_add_campus', compact('id', 'school','title'));
    }

    public function getDataForSchoolCampus()
    {
        $id = Input::get('id');
        $campuses = DB::table('campuses') ->select('id', 'name', 'sysid');
        $campusid = Campus::where('school_id',$id)->select('id')->lists('id');
        return Datatables::of($campuses)
            ->add_column('actions', '
                                <input type="checkbox" id="checkItem[]" name="checkItem" value="{{$id}}" >
                                 ')
            ->make();
    }

    public function postSchoolAddCampus($id)
    {
        $ids = explode(',',Input::get('selectedCampuses'));
        var_dump($id,$ids);
        //verify overall
        if (!empty($ids)) {
            for ($i = 0; $i < count($ids); $i++) {
                $count = DB::table('campuses')
                    ->where('school_id', $id)
                    ->where('id', $ids[$i])
                    ->where('is_deleted', 0)
                    ->count();
                if ($count == 0) {
                    $schoolcampus = new SchoolCampus();
                    $schoolcampus->campus_id = $ids[$i];
                    $schoolcampus->school_id = $id;
                    $schoolcampus->save();
                }
            }
        }
    return Redirect::to('admin/admissions/'.$id.'/school_add_campus');
 //       return Redirect::to('admin/admissions/school_add_campus')->with('success', Lang::get('admin/admissions/messages.school.create'));

    }


    public function getChangeAdmissionsIndexForCampus()
    {
        $title = Lang::get('admin/admissions/title.change_admissions');
        $user = Auth::user();
        if (!empty($user->id)) {
            $campus = DB::table('campuses')
                ->where('userID', $user->id)
                ->first();
        }
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
        $user = Auth::user();
        if (!empty($user->id)) {
            $original_campus = DB::table('campuses')
                ->where('userID', $user->id)
                ->first();
        }
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
                'admissions.nationgroup', 'admissions.admissionyear', 'admissions.admissionsemester','admissions.programcode','admission_group.group_id')
            ->first();

        return View::make('admin/admissions/admissions_change_submit', compact('original_campus','rawprograms', 'campuses','current_year_semester', 'admission', 'title'));
    }

    public function postAdmissionChangeSubmit($id){

        $year = Input::get('year');
        $semester = Input::get('semester');
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
        if (($admissionyear->year == $year) && ($admissionyear->semester ==$semester))
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
            ->where('year',$year)
            ->where('semester',$semester)
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
            $admission_changing->application_year = $year;
            $admission_changing->application_semester = $semester;
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

    public function getDataMajorWithCampus()
    {
        $user = Auth::user();
        if (!empty($user->id)) {
            $campus1 = DB::table('campuses')
                ->where('userID', $user->id)
                ->first();
        }

        $campus = intval(trim($_GET["campus"]));
        if (isset($campus)) {
          $majors = DB::table('rawprograms')
                ->join('programs', 'rawprograms.id', '=', 'programs.name')
                ->where('programs.campus_id', $campus)
                ->select('rawprograms.id as rid','rawprograms.name as rname')
                ->get();
                $select[] = array("id"=>'',"name"=>'请选择');
            foreach ($majors as $major){
                $select[] = array("id"=>$major->rid,"name"=>$major->rname);
            }
            echo json_encode($select);
        }
    }

    public function getAdmissionChangeAppointGroup()
    {
        $title = '';
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        return View::make('admin/admissions/admissions_change_appoint_group', compact('campus','title'));
    }

    public function getDataGroup(){
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $groups = Groups::where('groups.name', '<>', 'null')
            ->leftjoin('programs', 'programs.id', '=', 'groups.programs_id')
            ->where('programs.campus_id', '=', $campus->id)
            ->select('groups.id as gid','groups.name as gname')
            ->get();
        $select[] = array("id"=>'',"name"=>'请选择');
        foreach ($groups as $group){
            $select[] = array("id"=>$group->gid,"name"=>$group->gname);
        }
        echo json_encode($select);
    }

    public function getDataChangeAdmissionsForAppointGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();

        $admissions = DB::table('student_status_changing')
            ->leftjoin('admissions', function ($join) {
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
            ->select('admissions.id', 'admissions.id as select', 'admissions.studentno', 'admissions.fullname', 'admissions.program', 'admissions.status',
                'groups.sysid', 'groups.name as gname', 'rawprograms.name as major_name', 'rawprograms.type', 'admissions.nationgroup',
                'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou', 'admissions.distribution', 'admissions.is_serving');
        return Datatables::of($admissions)
            ->edit_column('select', '
                <input type="checkbox" id="checkItem[]" name="checkItem" value="{{$id}}">
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
            ->edit_column('gname', '<select id="group" name="group[]" style="width:70px;">
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
            ->make();

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
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
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
                        $query = $query->where('admissions'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('campuses.id',$campus->id)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'groups.sysid',
                    'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status');

        } elseif ($flag == 0) {
            $programs = DB::table('admissions')
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
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
                        $query = $query->where('admissions'.$key, 'like', $value);
                    }
                    return $query;
                })
                ->where('campuses.id',$campus->id)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'groups.sysid',
                    'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status');
        }

        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                      已录入
                                 @elseif ($status == 1)
                                      已上报
                                 @elseif ($status == 2)
                                      已审批
                                 @elseif ($status == 3)
                                     <select id="status" name="status">
                                       <option value="3"  selected="selected">未注册</option>
                                       <option value="4" >在籍</option>
                                     </select>
                                 @elseif ($status == 4)
                                     <select id="status" name="status">
                                       <option value="3">未注册</option>
                                       <option value="4" selected="selected" >在籍</option>
                                     </select>
                                 @elseif ($status == 5)
                                      异动中
                                 @elseif ($status == 6)
                                      毕业
                                 @elseif ($status == 7)
                                      退学
                                 @endif')
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
        $filter_program = array();
        $student_ids = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->where('admissions.campuscode',$campus->id)
                ->where('admissions.status',4)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno as student_id', 'admissions.idtype',
                    'admissions.idnumber', 'groups.sysid', 'admissions.politicalstatus',
                    'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou', 'admissions.distribution', 'admissions.is_serving');

        } elseif ($flag == 0) {
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
                    'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou', 'admissions.distribution', 'admissions.is_serving');
        }
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
                              <select id="distribution[]" name="distribution">
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
                            <select id="is_serving[]" name="is_serving">
                                <option value="0" {{{ $is_serving == 0 ? \' selected="selected"\' : "" }}}>不在职</option>
                                <option value="1" {{{ $is_serving == 1 ? \' selected="selected"\' : "" }}}>在职</option>
                            </select>
            ')
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
                    $join->on('admissions.id', '=', 'graduation_info.admissions_id');
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
                    $join->on('admissions.id', '=', 'graduation_info.admissions_id');
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
                            <a href="{{ URL::to(\'admin/admissions/\' . $id .\'/admissions_recovery_submit\')}} ">申请</a>
                                    @else
                                    毕业信息已经上报学信息网
                                    @endif
                            ')
            ->remove_column('is_reported')
            ->make();

    }

    public function getApplicationRecovery($id)
    {
        $title = Lang::get('admin/admissions/title.application_recovery_admissions');
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

    public function postApplicationRecovery($id){

        $year = Input::get('recovery_year');
        $semester = Input::get('recovery_semester');
        $status = Input::get('status');

        $count = AdmissionRecovery::where('student_id',$id)->count();
        if($count == 0){
            $admission_recovery = new AdmissionRecovery();
            $admission_recovery->recovery_year = $year;
            $admission_recovery->recovery_semester = $semester;
            $admission_recovery->student_id = $id;
            $admission_recovery->student_status = $status;
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
            if ($recovery->approval_result == 0){
                $admission_recovery = AdmissionRecovery::find($recovery->id);
                $admission_recovery->recovery_year = $year;
                $admission_recovery->recovery_semester = $semester;
                $admission_recovery->student_id = $id;
                $admission_recovery->student_status = $status;
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
                return Redirect::to('admin/admissions/application_recovery')->withError(Lang::get('admin/admissions/messages.recovery.is_approve'));
            }
        }
    }



    public function getIndexForApplicationDropOut()
    {
        $title = Lang::get('admin/admissions/title.application_dropout_admissions');

        // Show the page
        return View::make('admin/admissions/admissions_application_dropout', compact('title'));
    }

    public function postApplicationDropOut()
    {
        // Title
        $title = Lang::get('admin/admissions/title.application_dropout_admissions');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $id = trim(Input::get('student_id'));
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        if (!empty($id)) {
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
                ->where('admissions.campuscode',$campus->id)
                ->where('admissions.status',4)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno',
                    'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                    'admissions.hukou','admissions.program','admissions.dateofbirth', 'admission_details.formerlevel',
                    'rawprograms.name as major_name', 'campuses.name as campus_name','admissions.nationgroup')
                ->first();
            //    var_dump($admissionss);
            // Show the page
        }
        return View::make('admin/admissions/admissions_application_dropout_create', compact('admission','yearsemester','title'));
    }

    public function postSaveApplicationDropOut()
    {
        $title = Lang::get('admin/admissions/title.application_dropout_admissions');
        $id = Input::get('student_id');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $cause = Input::get('cause');

        $record_exists = DB::table('student_dropout')
            ->where('student_id',$id)
            ->count();
        if ($record_exists>0){
            return Redirect::to('admin/admissions/application_dropout')->withError(Lang::get('admin/admissions/messages.dropout.already_exists'));
        }else{
            $dropout = new AdmissionDropOut();
            $dropout->application_year = $year;
            $dropout->application_semester = $semester;
            $dropout->student_id = $id;
            $dropout->cause = $cause;
            $dropout->approval_result_province = 0;
            $dropout->is_deleted = 0;
            $dropout->created_at = new DateTime() ;
            $dropout->updated_at = new DateTime() ;
            $dropout->save();
            return Redirect::to('admin/admissions/application_dropout')->with('success',Lang::get('admin/admissions/messages.dropout.success'));
        }
    }

    public function getEditIndexApplicationDropOut()
    {
        $title = Lang::get('admin/admissions/title.edit_dropout_application');
        return View::make('admin/admissions/admissions_edit_application_dropout', compact( 'title'));
    }

    public function getEditDropOut()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_dropout_application');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $id = trim($_GET['id']);
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        if (!empty($id)) {
            $admission = DB::table('admissions')
                ->where('admissions.id', $id)
                ->leftjoin('admission_details', function ($join) {
                    $join->on('admission_details.admission_id', '=', 'admissions.id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('campuses', function ($join) {
                    $join->on('campuses.id', '=', 'admissions.campuscode');
                })
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno',
                    'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                    'admissions.hukou','admissions.program','admissions.dateofbirth', 'admission_details.formerlevel',
                    'rawprograms.name as major_name', 'campuses.name as campus_name','admissions.nationgroup')
                ->first();
            $dropout = DB::table('student_dropout')
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'student_dropout.student_id');
                })
                ->where('student_dropout.student_id',$admission->id)
                ->where('admissions.campuscode',$campus->id)
                ->select(array(
                    'admissions.id',
                    'student_dropout.application_year',
                    'student_dropout.application_semester',
                    'student_dropout.cause',
                    'admissions.studentno',
                    'admissions.fullname',
                    DB::raw('count(*) as count')
                ))
                ->where('student_dropout.is_deleted',0)
                ->where('student_dropout.approval_result_province',0)
                ->first();
            if ($dropout->count > 0){
                return View::make('admin/admissions/admissions_application_dropout_edit', compact('admission','yearsemester','dropout','title'));
            }else{
                return Redirect::to('admin/admissions/admissions_edit_dropout')->withError(Lang::get('admin/admissions/messages.no_dropout_records'));
            }
            // Show the page
        }else{
                return Redirect::to('admin/admissions/admissions_edit_dropout')->withError(Lang::get('admin/admissions/messages.no_dropout_records'));
        }
    }

    public function postEditDropOut()
    {
        // Title
        $title = Lang::get('admin/admissions/title.edit_dropout_application');
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $id = trim(Input::get('student_id'));
        $yearsemester = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
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
                ->where('admissions.campuscode',$campus->id)
                ->select('admissions.id', 'admissions.fullname as student_name', 'admissions.studentno',
                    'admissions.gender', 'admissions.idtype', 'admissions.idnumber', 'admissions.politicalstatus',
                    'admissions.hukou','admissions.program','admissions.dateofbirth', 'admission_details.formerlevel',
                    'rawprograms.name as major_name', 'campuses.name as campus_name','admissions.nationgroup')
                ->first();
            $dropout = DB::table('student_dropout')
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'student_dropout.student_id');
                })
                ->where('student_dropout.student_id',$admission->id)
                ->where('admissions.campuscode',$campus->id)
                ->select(array(
                    'admissions.id',
                    'student_dropout.application_year',
                    'student_dropout.application_semester',
                    'student_dropout.cause',
                    'admissions.studentno',
                    'admissions.fullname',
                    DB::raw('count(*) as count')
                ))
                ->where('student_dropout.is_deleted',0)
                ->where('student_dropout.approval_result_province',0)
                ->first();
            // Show the page

            if ($dropout->count > 0){
                 return View::make('admin/admissions/admissions_application_dropout_edit', compact('admission','yearsemester','dropout','title'));
            }else{
                return Redirect::to('admin/admissions/admissions_edit_dropout')->withError(Lang::get('admin/admissions/messages.no_dropout_records'));
            }
    }

    public function postSaveEditDropOut()
    {
        $student_id = Input::get('student_id');
        $cause = Input::get('cause');
        $drop = DB::table('student_dropout')->where('student_id',$student_id)->select('id')->first();
        $dropout = AdmissionDropOut::find($drop->id);
        $dropout->cause = $cause;
        $dropout->updated_at = new DateTime() ;
        $dropout->save();
        return Redirect::to('admin/admissions/admissions_edit_dropout')->with('success',Lang::get('admin/admissions/messages.dropout.edit_success'));
    }

    public function getDeleteDropOut(){
        $id = $_GET['id'];
        $title = Lang::get('admin/admissions/title.delete_dropout');
        $dropout = AdmissionDropOut::join('admissions','admissions.id','=','student_dropout.student_id')
            ->where('student_dropout.student_id',$id)
            ->select('admissions.studentno','admissions.id')
            ->first();
        return View::make('admin/admissions/admissions_dropout_delete', compact('dropout','id','title'));
    }

    public function postDeleteDropOut(){
        $id = Input::get('id');
        $drop = DB::table('student_dropout')
            ->where('student_id', '=', $id)
            ->select('id')
            ->first();
        $dropout = AdmissionDropout::find($drop->id);
        $dropout->is_deleted = 1;
        $dropout->save();

        $count = AdmissionDropout::find($drop->id)->count();
        if($count == 0){
            // There was a problem deleting the group
            return Redirect::to('admin/admissions/admissions_edit_dropout')->with('success', Lang::get('admin/admissions/messages.dropout.delete_success'));
    }else
            return Redirect::to('admin/admissions/edit_dropout?id='.$dropout->id);
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
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_reward/\')}}" class="iframe">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_punish/\')}}" class="iframe">惩罚</a>
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
        return View::make('admin/admissions/define_reward_campus', compact('id', 'title'));
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
        return View::make('admin/admissions/define_punish_campus', compact('id', 'title'));
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

    public function getIndexForComQueryAdmissions()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_comprehensive_query_campus', compact('title'));
    }

    public function getDataForComQueryAdmissions()
    {
        //    $groups  =AdminGroup::All();

        $filter = array();
        $student_id = Input::get('student_id');
        $idno = Input::get('ID_number');
        $digitno = Input::get('digital_registration_number');


        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';

        }
        if (!empty($idno)) {
            $filter["idnumber"] = '%' . $idno . '%';

        }
        if (!empty($digitno)) {
            $filter['admissionid'] = $digitno;

        }

        $admissions = DB::table('admissions')
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
                    $query = $query->where($key, 'like', $value);
                }
                return $query;
            })
            ->select('admissions.id', 'admissions.fullname', 'admissions.studentno', 'admissions.idnumber',
                'admissions.admissionid', 'admissions.nationgroup', 'admissions.politicalstatus');
        /*               ->get();
                       return View::make('admin/admissions/admissions_comprehensive_query_campus', compact('admissions','titl));
          */
        return Datatables::of($admissions)
            ->edit_column('admissionid', '@if ($admissionid == "")
                                                 无
                                        @else
                                               {{$admissionid}}
                                        @endif
                                        <input type="hidden" value="{{$id}}" id="id">
                                        ')
            ->remove_column('id')
            ->add_column('admissions_id', '<input type="hidden" value="{{$id}}" id="id">')
            ->make();

    }

    public function getAdmissionsQueryInfo()
    {
        $id = $_GET['id'];
        $admissions = DB::table('admissions')->where('admissions.id', $id)
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
            ->select('admissions.id', 'admissions.gender', 'admissions.jiguan', 'groups.sysid as group_code',
                'groups.name as group_name', 'rawprograms.name as major_name', 'rawprograms.type','admissions.dateofbirth',
                'admission_details.formerlevel','admissions.nationgroup','admissions.politicalstatus', 'admissions.maritalstatus',
                'admissions.admissionyear', 'admissions.admissionsemester','admissions.postcode','admissions.address','admissions.phone')
            ->get();
$querys = DB::getQueryLog();
        print_r($querys);

        return View::make('admin/admissions/admissions_query_info', compact('admissions'));
    }

    public function getAdmissionsAchievementRecord()
    {
        $id = $_GET['id'];
        $teaching_plan_id = $_GET['teaching_plan_id'];
        $admissions = DB::table('admissions')->where('admissions.id', $id)
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
            ->select('admissions.id', 'admissions.studentno as student_id','admissions.fullname as student_name','admissions.gender', 'admissions.jiguan', 'groups.sysid as group_code',
                'groups.name as group_name', 'rawprograms.name as major_name', 'rawprograms.type','admissions.dateofbirth',
                'admission_details.formerlevel','admissions.nationgroup','admissions.politicalstatus', 'admissions.maritalstatus',
                'admissions.admissionyear', 'admissions.admissionsemester','admissions.postcode','admissions.address','admissions.phone')
            ->get();
  //      $querys = DB::getQueryLog();
  //      print_r($querys);

        return View::make('admin/admissions/admissions_achievement_record', compact('admissions'));
    }

    public function getAdmissionsSelection()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_selection_query', compact('title'));
    }

    public function getDataForAdmissionsSelection()
    {
        $id = $_GET['id'];
        print_r($id);
        $exemptions = DB::table('student_selection')
            ->leftjoin('course', function($join)
            {
                $join->on('student_selection.course_id', '=', 'course.id');
            })
            ->where('student_selection.student_id',$id)
            ->select('course.id','course.code','course.name','course.credit','student_selection.is_obligatory','student_selection.year','student_selection.semester','student_selection.selection_status')            ;

        return Datatables::of ($exemptions)

            ->edit_column('is_obligatory','
                        @if ($is_obligatory == 0)
                            选修
                        @elseif ($is_obligatory == 1)
                            必修
                        @endif
            ' )
            ->edit_column('semester','
                            @if ($semester == \'01\')
                                春季
                            @else
                                秋季
                            @endif
            ')
            ->edit_column('selection_status','
                            @if ($selection_status == 1)
                                是
                            @elseif ($selection_status == 0)
                                否
                            @endif
            ')
            ->make ();
    }

    public function getAdmissionsExam()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_exam_query', compact('title'));
    }

    public function getDataForAdmissionsExam()
    {
        $id=83;
      //  $id = $_GET['id'];
     //   print_r($id);
        $exemptions = DB::table('student_selection')
            ->leftjoin('course', function($join)
            {
                $join->on('student_selection.course_id', '=', 'course.id');
            })
            ->where('student_selection.student_id',$id)
            ->select('course.id','course.code','course.name','course.credit','student_selection.is_obligatory','student_selection.year','student_selection.semester','student_selection.selection_status');

        return Datatables::of ($exemptions)

            ->edit_column('is_obligatory','
                        @if ($is_obligatory == 0)
                            选修
                        @elseif ($is_obligatory == 1)
                            必修
                        @endif
            ' )
            ->edit_column('semester','
                            @if ($semester == \'01\')
                                春季
                            @else
                                秋季
                            @endif
            ')
            ->edit_column('selection_status','
                            @if ($selection_status == 1)
                                是
                            @elseif ($selection_status == 0)
                                否
                            @endif
            ')
            ->make ();
    }


    public function getAdmissionsExemption()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_exemption_query', compact('title'));
    }

    public function getDataForAdmissionsExemption()
    {
        $id=83;
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
        ->where('exemption_info.is_deleted', 0)
        ->where('exemption_info.student_id',$id)
        ->select('exemption_info.id', 'course.name as course_name', 'course.classification',
                'course.credit', 'exemption_info.application_year', 'exemption_info.application_semester',
                'exemption_type.exemption_type_name', 'exemption_info.major_name_outer', 'exemption_info.course_name_outer', 'exemption_info.classification_outer',
                'exemption_info.credit_outer', 'exemption_info.final_result', 'exemption_info.failure_cause');

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

        ->remove_column('application_semester')
        ->make();
    }

    public function getAdmissionsUnifiedExam()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_unifiedexam_query', compact('title'));
    }

    public function getDataForAdmissionsUnifiedExam()
    {
        $id=240;
    $exemptions = DB::table ('unified_exam_info')
    ->leftjoin('admissions', function($join)
    {
        $join->on('admissions.id', '=', 'unified_exam_info.student_id');
    })
    ->leftjoin('unified_exam_subject', function($join)
    {
        $join->on('unified_exam_info.unified_exam_subject_id', '=', 'unified_exam_subject.id');
    })

    ->leftjoin('campuses', function($join)
    {
        $join->on('admissions.campuscode', '=', 'campuses.id');
    })

   ->where('unified_exam_info.student_id',$id)
    ->where('unified_exam_info.is_deleted',0)
        ->select('unified_exam_info.id',  'unified_exam_info.registration_year', 'unified_exam_info.registration_semester','admissions.studentno', 'admissions.fullname', 'unified_exam_subject.subject','unified_exam_info.final_result','campuses.name');

    return Datatables::of ($exemptions)

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
        ->add_column('isRecord','1')
        ->make ();
    }


    public function getAdmissionsAllResults()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_allresult_query', compact('title'));
    }

    public function getDataForAdmissionsAllResults()
    {
        $id=83;
        //  $id = $_GET['id'];
        //   print_r($id);
        $exemptions = DB::table('student_selection')
            ->leftjoin('course', function($join)
            {
                $join->on('student_selection.course_id', '=', 'course.id');
            })
            ->where('student_selection.student_id',$id)
            ->select('course.id','course.code','course.name','course.credit','student_selection.is_obligatory','student_selection.year','student_selection.semester','student_selection.selection_status');

        return Datatables::of ($exemptions)

            ->edit_column('is_obligatory','
                        @if ($is_obligatory == 0)
                            选修
                        @elseif ($is_obligatory == 1)
                            必修
                        @endif
            ' )
            ->edit_column('semester','
                            @if ($semester == \'01\')
                                春季
                            @else
                                秋季
                            @endif
            ')
            ->edit_column('selection_status','
                            @if ($selection_status == 1)
                                是
                            @elseif ($selection_status == 0)
                                否
                            @endif
            ')
            ->make ();
    }

    public function getAdmissionsAwardPunish()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        $id=240;
   /*     $punishs = DB::table('punish')
                    ->where('student_id',$id)
                    ->get();
        $rewards = DB::table('reward')
            ->where('student_id',$id)
            ->get();
   */
   //     return View::make('admin/admissions/admissions_reward_punish_query', compact('punishs','rewards','title'));
        return View::make('admin/admissions/admissions_reward_punish_query', compact('title'));
    }

    public function getAdmissionsGraduationApplication()
    {
        $title = Lang::get('admin/admissions/title.admissions_comprehensive_query');
        return View::make('admin/admissions/admissions_graduation_application_query', compact('title'));
    }

    public function getDataForGraduationApplication()
    {
        $id= 83;
        //  $id = $_GET['id'];
        //   print_r($id);
        $exemptions = DB::table('student_selection')
            ->leftjoin('course', function($join)
            {
                $join->on('student_selection.course_id', '=', 'course.id');
            })
            ->where('student_selection.student_id',$id)
            ->select('course.id','course.code','course.name','course.credit','student_selection.is_obligatory','student_selection.year','student_selection.semester','student_selection.selection_status');

        return Datatables::of ($exemptions)

            ->edit_column('is_obligatory','
                        @if ($is_obligatory == 0)
                            选修
                        @elseif ($is_obligatory == 1)
                            必修
                        @endif
            ' )
            ->edit_column('semester','
                            @if ($semester == \'01\')
                                春季
                            @else
                                秋季
                            @endif
            ')
            ->edit_column('selection_status','
                            @if ($selection_status == 1)
                                是
                            @elseif ($selection_status == 0)
                                否
                            @endif
            ')
            ->make ();
    }

    public function getAdmissionsInformation()
    {
        $title = Lang::get('admin/admissions/title.admissions_information_query');
        $rawprograms = RawProgram::All();
        $groups = Groups::All();
        return View::make('admin/admissions/admissions_information_query', compact('groups', 'rawprograms', 'title'));
    }

    public function getDataForAdmissionInformation()
    {
        //    $groups  =AdminGroup::All();

        $filter = array();
        $filter_program = array();
        $filter_group = array();
        $student_ids = array();
        $student_ids1 = array();
        $student_ids2 = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $admissionyear = Input::get('admissionyear');
        $admissionsemester = Input::get('admissionsemester');
        $admission_state = Input::get('admission_state');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $groupyear = Input::get('create_admin_group_year');
        $group_code = Input::get('group_code');
        $group_name = Input::get('group_name');
        $flag = 0;
        $flag1 = 0;
        $flag2 = 0;

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($major_classification)) {
            $filter_program['type'] = $major_classification;
        }
        if (!empty($admission_state) && $admission_state!=6) {
            $filter['status'] = $admission_state;
        }
        if (!empty($admissionyear)) {
            $filter["admissionyear"] = $admissionyear;
        }
        if (!empty($admissionsemester)) {
            $filter["admissionsemester"] = $admissionsemester;
        }
        if (!empty($major)) {
            $filter_program['id'] = $major;
        }
      if (!empty($group_code)) {
            $filter_group['sysid'] = '%'.$group_code.'%';
        }
        if (!empty($group_name)) {
            $filter_group['id'] = $group_name;
        }
   /*       if (!empty($groupyear)) {
            $filter_group['year'] = $groupyear;
        }
*/
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
            $student_ids1 = DB::table('admissions')->select('id')
                ->whereIn('programcode', $program_ids)
                ->lists('id');
            $flag = 1;
            $flag1 = 1;

        }

             if (!empty($filter_group))
              {
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
                  $student_ids2 = DB::table('admission_group')->select('admission_id as id')
                      ->whereIn('group_id',$group_ids)
                      ->lists('id');
         /*         $student_ids2 = DB::table('admissions')->select('id')
                      ->leftjoin('admission_group', function ($join) {
                          $join->on('admission_group.admission_id', '=', 'admissions.id');
                      })
                      ->whereIn('id',$program_ids)
                      ->lists('id');
            */
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

        if ($flag == 1) {
            $programs = DB::table('admissions')
                ->whereIn('admissions.id', $student_ids)
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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname','admissions.studentno', 'admissions.program','groups.sysid', 'groups.name',
                    'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status','admissions.admissionyear', 'admissions.admissionsemester');
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
                ->where(function ($query) use ($filter) {
                    if (!is_array($filter)) {
                        return $query;
                    }
                    foreach ($filter as $key => $value) {
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname','admissions.studentno', 'admissions.program','groups.sysid', 'groups.name',
                    'rawprograms.name as major_name', 'rawprograms.type', 'admissions.status','admissions.admissionyear', 'admissions.admissionsemester');

        }
        return Datatables::of($programs)
            ->edit_column('type', '@if ($type == 12)
                                          本科
                                 @elseif ($type == 14)
                                          专科
                                 @endif')
            ->edit_column('status', '@if ($status == 0)
                                          未注册
                                 @elseif ($status == 1)
                                          在籍
                                 @elseif ($status == 2)
                                          毕业
                                 @endif')
            ->edit_column('admissionsemester', '@if ($admissionsemester == 1)
                                          春季
                                 @elseif ($admissionsemester == 2)
                                          秋季
                                 @endif')

            ->make();

    }

    public function getAdmissionsBasicInformation()
    {
        $title = Lang::get('admin/admissions/title.admissions_information_query');
        $rawprograms = RawProgram::All();
        $groups = Groups::All();
        return View::make('admin/admissions/admissions_basic_information_query', compact('groups', 'rawprograms', 'title'));
    }

    public function getDataForAdmissionBasicInformation()
    {
        //    $groups  =AdminGroup::All();

        $filter = array();
        $filter_program = array();
        $filter_group = array();
        $student_ids = array();
        $student_ids1 = array();
        $student_ids2 = array();
        $student_id = Input::get('student_id');
        $student_name = Input::get('student_name');
        $admissionyear = Input::get('admissionyear');
        $admissionsemester = Input::get('admissionsemester');
        $major = Input::get('major');
        $major_classification = Input::get('major_classification');
        $group_code = Input::get('group_code');
        $group_name = Input::get('group_name');
        $flag = 0;
        $flag1 = 0;
        $flag2 = 0;

        if (!empty($student_id)) {
            $filter["studentno"] = '%' . $student_id . '%';
        }
        if (!empty($student_name)) {
            $filter["fullname"] = '%' . $student_name . '%';
        }
        if (!empty($major_classification)) {
            $filter_program['type'] = $major_classification;
        }
        if (!empty($admissionyear)) {
            $filter["admissionyear"] = $admissionyear;
        }
        if (!empty($admissionsemester)) {
            $filter["admissionsemester"] = $admissionsemester;
        }
        if (!empty($major)) {
            $filter_program['id'] = $major;
        }
        if (!empty($group_code)) {
            $filter_group['sysid'] = '%'.$group_code.'%';
        }
        if (!empty($group_name)) {
            $filter_group['id'] = $group_name;
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
            $student_ids1 = DB::table('admissions')->select('id')
                ->whereIn('programcode', $program_ids)
                ->lists('id');
            $flag = 1;
            $flag1 = 1;

        }

        if (!empty($filter_group))
        {
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
            $student_ids2 = DB::table('admission_group')->select('admission_id as id')
                ->whereIn('group_id',$group_ids)
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

        if ($flag == 1) {
            $programs = DB::table('admissions')
                ->whereIn('admissions.id', $student_ids)
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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname','admissions.studentno','admissions.gender', 'admissions.nationgroup','admissions.politicalstatus',
                    'admissions.jiguan', 'admissions.dateofbirth', 'admission_details.formerlevel', 'admission_details.formerschool', 'admission_details.dategraduated',
                    'admission_details.attainmentcert','admissions.maritalstatus','admissions.idnumber', 'admissions.phone', 'admissions.address',
                    'admissions.postcode', 'admissions.postcode as s1');
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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
                ->select('admissions.id', 'admissions.fullname','admissions.studentno','admissions.gender', 'admissions.nationgroup','admissions.politicalstatus',
                    'admissions.jiguan', 'admissions.dateofbirth', 'admission_details.formerlevel', 'admission_details.formerschool', 'admission_details.dategraduated',
                    'admission_details.attainmentcert','admissions.maritalstatus','admissions.idnumber', 'admissions.phone', 'admissions.address',
                    'admissions.postcode', 'admissions.postcode as s1');
        }
        return Datatables::of($programs)
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
            ->edit_column('maritalstatus', '@if ($maritalstatus == 0)
                                          未婚
                                 @elseif ($maritalstatus == 1)
                                          已婚
                                 @endif')
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
            ->make();

    }

    public function getAdmissionsRewardPunish()
    {
        $title = Lang::get('admin/admissions/title.record_reward_punish');
        $rawprograms = RawProgram::All();
      //  $rewards = Reward::All();
      //  $punishes = Punish::All();
      //  return View::make('admin/admissions/admissions_reward_punish', compact('rewards','punishes','rawprograms', 'title'));
        return View::make('admin/admissions/admissions_reward_punish', compact('rawprograms', 'title'));
    }

    public function getDataForAdmissionsRewardPunish()
    {
        //    $groups  =AdminGroup::All();

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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
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
                        $query = $query->where($key, 'like', $value);
                    }
                    return $query;
                })
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
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_reward/\')}}">奖励</a><br>
                           <a href="{{URL::to(\'admin/admissions/\'.$id.\'/define_punish/\')}}">惩罚</a>
                           ')
            ->remove_column('id')
            ->make();

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
        $groups = Groups::leftjoin('programs', 'programs.name', '=', 'groups.programs_id')
            ->leftjoin('rawprograms', 'rawprograms.id', '=', 'programs.name')
            ->leftjoin('admission_group', 'admission_group.group_id', '=', 'groups.id')
            ->where('programs.campus_id', '=', $campus->id)
            ->select(array(
                'groups.id',
                'groups.name',
                'groups.sysid',
                'rawprograms.type',
                'rawprograms.name as programsname',
                DB::raw('count(admission_group.group_id) as student_count'),
                'groups.created_at'
            ))
            ->groupBy('groups.id')
            ->orderBy('groups.name', 'asc');
        return Datatables::of($groups)
            ->edit_column('type', '@if($type == \'12\')
                                        	   本科
                                         @elseif($type == \'14\')
            								    专科
                                         @elseif($type == \'3\')
            								    研究生及以上
                                         @endif')
            ->add_column('actions',
                '<a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/edit_group\' ) }}}" class="iframe btn btn-xs btn-default">{{{ Lang::get(\'button.edit\') }}}</a>
                                @if ($student_count == 0)
                                    <a href="{{{ URL::to(\'admin/admissions/\' . $id . \'/delete_group\' ) }}}" class="iframe btn btn-xs btn-danger">{{{ Lang::get(\'button.delete\') }}}</a>
                                @endif
                               ', 7)
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

    public function getGroupSysid($groupid, $program){
        $admissions = DB::table('module_current')
            ->where('module_id',4)
            ->select('current_year','current_semester')
            ->first();
        $admissionyear = $admissions->current_year;
        $semester      = intval($admissions->current_semester);
        $campuse_sysid = str_pad($program->sysid, 4, "0", STR_PAD_LEFT);
        $program_rank  = str_pad($program->rank, 3, "0", STR_PAD_LEFT);
        $waterflow_num = str_pad($groupid, 3, "0", STR_PAD_LEFT);

        $group_sysid = $admissionyear.$semester.$campuse_sysid.$program_rank.$waterflow_num;

        return $group_sysid;
    }

    public function getDeleteGroups($groupid){
        // Title
        $title = Lang::get('admin/admissions/title.groups_delete');

        // Show the page
        return View::make('admin/admissions/group_delete', compact('groupid', 'title'));

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



    public function getDataForAdminGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $filter = array();
        $filter_student = array();
        $year = Input::get('year');
        $group = Input::get('group');
        $student_type = Input::get('student_type');
        //  $exam_point = Input::get('examination_point');
        $group_name = Input::get('group_name');
        $group_admin = Input::get('group_admin');
        //   $state = Input::get('state');
        $flag = 0;
        if (!empty($year)) {
            $filter["year"] =  $year;
        }
        if (!empty($group)) {
            $filter["name"] = '%' . $group . '%';
        }
        if (!empty($student_type)) {
            $filter_student["enrollmenttype"] =  $student_type;
        }
        if (!empty($filter_student)){
            $admission_ids = DB::table('admissions')->select('id')
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
            $group_ids = DB::table('admission_group')->select('group_id as id')
                ->whereIn('admission_id',$admission_ids)
                ->lists('id');
            $flag=1;
        }
        if ($flag == 1) {
            $programs = DB::table('groups')
                ->whereIn('groups.id',$group_ids)
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('programs', function ($join) {
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
                ->where('programs.campus_id', '=', $campus->id)
                ->select('admissions.id', 'groups.sysid', 'groups.name as gname',
                    'rawprograms.name as pname', 'teaching_plan.code',
                    DB::raw('count(admission_group.group_id) as student_count'),
                    'groups.class_adviser', 'rawprograms.type', 'groups.year','groups.semester','admissions.ksh');
        }else{
            $programs = DB::table('groups')
                ->leftjoin('admission_group', function ($join) {
                    $join->on('admission_group.group_id', '=', 'groups.id');
                })
                ->leftjoin('admissions', function ($join) {
                    $join->on('admissions.id', '=', 'admission_group.admission_id');
                })
                ->leftjoin('rawprograms', function ($join) {
                    $join->on('rawprograms.id', '=', 'admissions.programcode');
                })
                ->leftjoin('programs', function ($join) {
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
                ->where('programs.campus_id', '=', $campus->id)
                ->select('admissions.id', 'groups.sysid', 'groups.name as gname',
                    'rawprograms.name as pname', 'teaching_plan.code',
                    DB::raw('count(admission_group.group_id) as student_count'),
                    'groups.class_adviser', 'rawprograms.type', 'groups.year','groups.semester','admissions.ksh');
        }
        return Datatables::of($programs)
            ->edit_column('gname', '
                                <input id="group_name" name="group_name[]" value="{{$gname}}">
                                 ')
            ->edit_column('pname', '
                                {{$pname}}_{{$code}}_{{$student_count}}
                                ')
            ->edit_column('class_adviser', '
                                <input id="class_adviser" name="class_adviser[]" value="{{$class_adviser}}" style="width: 100px">
                       ')
            ->edit_column('type', '@if($type == \'12\')
                                        	   本科
                                         @elseif($type == \'14\')
            								    专科
                                         @elseif($type == \'3\')
            								    研究生及以上
            								    @endif
           ')
            ->edit_column('semester', '@if ($semester == 1)
                                      春季
                                 @else
                                      秋季
                                 @endif')
            ->remove_column('code')
            ->remove_column('student_count')
            ->make();
    }

    public function getCreateAdminGroup()
    {
        // Title
        $title = Lang::get('admin/admissions/title.create_admin_group');
        $rawprograms = RawProgram::All();
        // Show the page

        return View::make('admin/admissions/group_define_edit', compact('rawprograms', 'title'));
    }

    public function postCreateAdminGroup()
    {
        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
        $student_type = Input::get('student_type');
        $major = Input::get('major');
        $year = Input::get('year');
        $semester = Input::get('semester');
        $having_stu_no = Input::get('having_stu_no');
        $group_name = Input::get('group_name');
        $class_adviser = Input::get('class_adviser');

        $groups_count = DB::table('groups')
            ->where('name', $group_name)
            ->where('programs_id', $major)
            ->count();
        if($groups_count > 0){
            return Redirect::to('admin/admissions/group_define')
                ->withInput()
                ->withErrors(Lang::get ( 'admin/admissions/messages.already_exists'));
        }

        //program data with campus sysid
        $program = DB::table('programs')
            ->join('campuses', 'programs.campus_id', '=', 'campuses.id')
            ->where('programs.status', '=', '1')
            ->where('programs.name', '=', $major)
            ->select('programs.id', 'programs.name', 'programs.rank', 'campuses.sysid')->first();

        if(!$program){
            return Redirect::to('admin/admissions/group_define')
                ->withInput()
                ->withErrors(Lang::get ( 'admin/admissions/messages.programs_disapprove'));
        }

        //store
        if(!$groups_count){
            $groups = new Groups();
            $groups->name = $group_name;
            $groups->programs_id = $major;
            $groups->student_type = $student_type;
            $groups->year = $year;
            $groups->semester = $semester;
            $groups->campus_id = $campus->id;
            $groups->having_stu_no = $having_stu_no;
            $groups->class_adviser = $class_adviser;
            $groups->save();
            //get the group id first then update group's sysid
            $groups->sysid = $this->getGroupSysid($groups->id, $program);
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


        $start_stu_no = Input::get('start_stu_no');
        $end_stu_no = Input::get('end_stu_no');
        $group_id = Input::get('group_id');

        $state = Input::get('state');

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
                ->select('admissions.id', 'admissions.studentno', 'admissions.fullname', 'admissions.program', 'admissions.status',
                    'groups.sysid', 'groups.name as gname', 'rawprograms.name as pname', 'rawprograms.type', 'admissions.nationgroup',
                    'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou',
                    'admissions.distribution', 'admissions.is_serving');
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
                ->select('admissions.id', 'admissions.studentno', 'admissions.fullname', 'admissions.program', 'admissions.status',
                    'groups.sysid', 'groups.name as gname', 'rawprograms.name as pname', 'rawprograms.type', 'admissions.nationgroup',
                    'admissions.politicalstatus', 'admissions.maritalstatus', 'admissions.jiguan', 'admissions.hukou',
                    'admissions.distribution', 'admissions.is_serving');

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
            ->edit_column('gname', '<select id="group" name="group[]" style="width:70px;">
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
            ->make();

    }

}