<?php

class AdminEduDepartmentController extends AdminController {
    public function getIndex() {
        // Title
        $title = Lang::get ( 'admin/depart/title.edu_department_management' );

        // Show the page
        return View::make ( 'admin/depart/index', compact ( 'title'));
    }
//    public function getApproveIndex() {
//        // Title
//        $title = Lang::get ( 'admin/admin.function_group_approve' ).Lang::get ( 'admin/program/title.program' );
//
//        $mode = 'approve';
//
//        // Show the page
//        return View::make ( 'admin/programs/index', compact ( 'title' ,'mode') );
//    }
    public function getCreate() {
//        $title = Lang::get ( 'admin/depart.function_group_add' ) . Lang::get ( 'admin/admin.program' );
        $title = Lang::get ( 'admin/depart/table.eidt_title' );

        // Mode
        $mode = 'create';
//
//        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
//        if (! $campus || $campus->id < 0)
//            return View::make ( 'admin/programs/index', compact ( 'title','mode' ) )->withError ( 'need to create campus first' );
//        else {
//            $teachers = Teacher::where ( 'campus_id', '=', $campus->id )->get ();
//
//            // Show the page
//            return View::make ( 'admin/programs/create_edit', compact ( 'teachers', 'title', 'mode' ) );
//        }

        return View::make ( 'admin/depart/define_edit', compact ('title', 'mode') );
    }
    public function postCreate() {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
//        $mode = 'create';

        //prepare model and save
        $edudepartment = new EduDepartment();
        $edudepartment->name = Input::get('name');
        $edudepartment->code = Input::get('code');

        if ($edudepartment->validate(Input::all())) {
            //verify overall
            $recordexists = DB::table('education_department')->where('name', Input::get('name'))
                ->where('code', Input::get('code'))
                ->count();

            if ($recordexists > 0) {
                return Redirect::to('admin/edu_department/data_add')->withErrors(/*Lang::get ( 'admin/managerawprograms/title.error:programexists')*/ '1232131');
            }

//            //verify sysid
//            $recordexists = 0;
//            $recordexists = DB::table('rawprograms')->where('sysid', Input::get('sysid'))
//                ->count();
//            if ($recordexists > 0) {
//                return Redirect::to('admin/add_rawprogram')->withErrors(Lang::get ( 'admin/managerawprograms/title.error:sysidexists'));
//            }
            $edudepartment->save();
        } else {
            return Redirect::to('admin/edu_department')->withErrors($edudepartment->errors());
        }

//        return Redirect::to('admin/edu_department/'.$id.'/edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));
        return Redirect::to('admin/edu_department/data_add')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));



    }

    public function getDataForAdd() {
        $programs = DB::table ('education_department')->select( 'id', 'name', 'code');
        return Datatables::of ( $programs )
            ->add_column('itemnumber', '', 0)
            ->add_column ( 'actions', '
                                <a href="{{{ URL::to(\'admin/edu_department/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
                                <a href="{{{ URL::to(\'admin/edu_department/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.delete\') }}}</a>
                                '
            )
            ->remove_column('id')
            ->make ();
    }

//    public function getDataForAdd() {
//        $programs = DB::table ('education_department')->select( 'name', 'code');
//        return Datatables::of ( $programs )
//            ->add_column ( 'actions', '
//                        		 <a href="{{{ URL::to(\'admin/edu_department/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
//                        		' )
//            ->make ();
//    }
    public function getEdit($id) {
        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );

        // Mode
        $mode = 'edit';
//        $action = 'edit';

        $edudepartment = EduDepartment::find ( $id );
        if (isset ( $edudepartment ) && $edudepartment->id) {

                        //prepare available 專業 list

            // Show the page
            return View::make ( 'admin/depart/define_edit', compact ( 'title', 'mode', 'edudepartment') );
        } else {
            return View::make ( 'admin/depart/index', compact ( 'title' , 'mode') )->withError ( 'programs ID not found' );

        }
    }

    public function postEdit($id) {
//        $result = INPUT::ALL();
//
//        var_dump($result);
//
//
//        $mode = 'create';

        $edudepartment = EduDepartment::find ( $id );
        if (isset ( $edudepartment ) && $edudepartment->id) {
            $edudepartment->name = Input::get('name');
            $edudepartment->code = Input::get('code');

            if ($edudepartment->validate(Input::all())) {

                $edudepartment->save();
            } else {
                return Redirect::to('admin/edu_department')->withErrors($edudepartment->errors());
            }

            return Redirect::to('admin/edu_department/'.$id.'/edit')->withInput()->with('success', Lang::get('admin/program/messages.create.success'));

        }
    }
//    public function getApprove($programID) {
//
//        $action = Input::get('action');
//
//        if($action == 'revert')
//            $title = Lang::get ( 'admin/admin.function_group_revert' ) . Lang::get ( 'admin/admin.program' );
//        else
//            $title = Lang::get ( 'admin/admin.function_group_approve' ) . Lang::get ( 'admin/admin.program' );
//
//        // Mode
//        $mode = 'approve';
//
//        //get program name and level
//        $program = DB::table('programs')
//            ->join('rawprograms', 'rawprograms.id', '=', 'programs.name')
//            ->where('programs.id', '=', $programID)
//            ->select('rawprograms.name', 'rawprograms.type as rank', 'programs.id', 'programs.campus_id', 'programs.status', 'programs.approval_comment')
//            ->first();
//        $comment = DB::table('program_approval_log')
//            ->where('program_id', '=', $program->id)
//            ->where('action', '=', 'approve')
//            ->orderby('id', 'desc')
//            ->first();
//
//        if (isset ( $program ) && $program->id) {
//
//            $teachers = Teacher::where ( 'campus_id', '=', $program->campus_id )->get ()->toArray ();
//            $selected_teachers = DB::table ( 'program_teacher' )->where ( 'program_id', '=', $programID )->get ();
//
//            if (count ( $teachers ) > 0 && count ( $selected_teachers ) > 0) {
//                foreach ( $selected_teachers as $selected_teacher ) {
//                    for($i = 0; $i < count ( $teachers ); ++ $i) {
//                        if ($teachers [$i] ['id'] == $selected_teacher->teacher_id) {
//                            $teachers [$i] ['checked'] = true;
//                        }
//                    }
//                }
//            }
//
//            // Show the page
//            return View::make ( 'admin/programs/create_edit', compact ( 'teachers', 'title', 'mode', 'program','action','comment' ) );
//        } else {
//            return View::make ( 'admin/programs/index', compact ( 'title','mode' ) )->withError ( 'programs ID not found' );
//            ;
//        }
//    }
//    public function postApprove($programsID) {
//        if ($programsID > 0) {
//            $program = Program::find ( $programsID );
//        }
//        $title = Lang::get ( 'admin/admin.function_group_approve' ) . Lang::get ( 'admin/admin.program' );
//        $mode = 'approve';
//
//        if (isset ( $program ) && $program->id) {
//            $program->status = Input::get ( 'approve_result' );
//            $program->approval_comment = Input::get ( 'approval_comment' );
//            $program->save ();
//
//            if ($program->status == 1)
//                $result = 'approve';
//            else if ($program->status == -1)
//                $result = 'reject';
//            else if ($program->status == 0)
//                $result = 'revert';
//
//            DB::table('program_approval_log')->insert(
//                array('action' => $result, 'program_id' => $program->id, 'comments' => $program->approval_comment, 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'))
//            );
//
//            // Show the page
//            if ($result == 'revert') {
//                $title=Lang::get ( 'admin/admin.function_group_revert' ) . Lang::get ( 'admin/admin.program' ).' 成功';
//            } else {
//                $title=$title.'成功';
//            }
//            return View::make ( 'admin/programs/approved', compact ( 'title', 'mode' ) )->withSuccess ( 'ok' );
//        } else {
//            $title=$title.'失败';
//            return View::make ( 'admin/programs/approved', compact ( 'title', 'mode' ) )->withError ( 'programs ID not found' );
//        }
//    }
//    public function postEdit($programID) {
//        $rules = array (
//            'name' => array('required', 'integer', 'min:1')
//        );
//
//        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
//
//        $title = Lang::get ( 'admin/admin.function_group_add' ) . Lang::get ( 'admin/admin.program' );
//        $mode = 'edit';
//
//        if (! $campus || $campus->id < 0)
//            return View::make ( 'admin/program/' . $programID . '/edit' , compact ( 'title','mode' ) )->withError ( 'need to create campus first' );
//        else {
//
//            $validator = Validator::make(Input::all(), $rules);
//
//            if ($validator->fails()) {
//                if (Input::get ( 'name' ) == 0) {
//                    $error['name'] = Lang::get ( 'admin/program/messages.programnotset' );
//                }
//                return Redirect::to ( 'admin/program/' . $programID . '/edit' )->withInput ()->with ( 'error', $error );
//            } else {
//
//                $program = Program::find ( $programID );
//
//                //approved program list
//                $approvedprogramslist = DB::table('programs')
//                    ->select('name')
//                    ->where('campus_id', '=', $program->campus_id)
//                    ->where('name', '<>', $program->name)
//                    ->lists('name');
//
//                // return error if user select a 專業 which is already approved
//                if (in_array(Input::get ( 'name' ), $approvedprogramslist)) {
//
//                    //override programrank 層次 and rawid
//                    $rawprogram = DB::table('rawprograms')->where('id', '=', $program->name)->first();
//                    $program->rank = $rawprogram->type;
//                    $program->rawprogramid = $rawprogram->id;
//
//                    //prepare available 專業 list
//                    $approvedprogramslist = DB::table('programs')
//                        ->select('name')
//                        ->where('campus_id', '=', $program->campus_id)
//                        ->where('status', '=', 1)
//                        ->lists('name');
//                    $availableprograms = DB::table('rawprograms')
//                        ->where('rawprograms.type', '=', $rawprogram->type)
//                        ->whereNotIn('id', $approvedprogramslist)
//                        ->lists('name', 'id');
//
//                    $teachers = Teacher::where ( 'campus_id', '=', $program->campus_id )->get ()->toArray ();
//                    $selected_teachers = DB::table ( 'program_teacher' )->where ( 'program_id', '=', $programID )->get ();
//
//                    if (count ( $teachers ) > 0 && count ( $selected_teachers ) > 0) {
//                        foreach ( $selected_teachers as $selected_teacher ) {
//                            for($i = 0; $i < count ( $teachers ); ++ $i) {
//                                if ($teachers [$i] ['id'] == $selected_teacher->teacher_id) {
//                                    $teachers [$i] ['checked'] = true;
//                                }
//                            }
//                        }
//                    }
//                    return View::make ( 'admin/programs/create_edit', compact (  'teachers', 'title', 'mode', 'program', 'availableprograms' ) )->withErrors(Lang::get ( 'admin/program/messages.already_exists' ));
//                } else {
//                    $program = Program::find ( $programID );
//                    $program->name = Input::get ( 'name' );
//                    $program->rank = Input::get ( 'rank' );
//                    $program->campus_id = $campus->id;
//                    $program->save ();
//                }
//
//                if ($program->id) {
//                    DB::table ( 'program_teacher' )->where ( 'program_id', '=', $programID )->delete ();
//                    if (null !== Input::get ( 'teachers' ) && (Input::get ( 'teachers' )) > 0)
//                        foreach ( Input::get ( 'teachers' ) as $teacher => $key )
//                            if ($key)
//                                DB::insert ( 'insert into program_teacher (program_id, teacher_id) values (?, ?)', array (
//                                    $program->id,
//                                    $teacher
//                                ) );
//
//                    return Redirect::to ( 'admin/program/' . $program->id . '/edit' )->with ( 'success', Lang::get ( 'admin/program/messages.edit.success' ) );
//                } else {
//                    // Get validation errors (see Ardent package)
//                    $error = $program->errors ()->all ();
//
//                    return Redirect::to ( 'admin/program/create' )->withInput ()->with ( 'error', $error );
//                }
//            }
//        }
//    }
//    public function getDataForAdd() {
//        $campus = DB::table ( 'campuses' )->where ( 'userID', Auth::user ()->id )->first ();
//        $programs = Program::where ( 'programs.name', '<>', 'null' )
//            ->where ( 'programs.campus_id', '=', $campus->id )
//            ->leftjoin ('rawprograms', 'programs.name', '=', 'rawprograms.id')
//            ->leftjoin ( 'program_teacher', 'programs.id', '=', 'program_teacher.program_id' )
//            ->leftjoin ( 'campuses', 'programs.campus_id', '=', 'campuses.id' )
//            ->groupBy ( 'programs.id' )
//            ->select ( array (
//                'programs.id',
//                'rawprograms.type',
//                'rawprograms.name',
//                'campuses.name as campusname',
//                DB::raw ( 'count(program_teacher.teacher_id) as teacher_count' ),
//                'programs.status',
//                'programs.created_at'
//            ));
//
//        return Datatables::of ( $programs )->edit_column ( 'type', '@if($type == \'12\')
//                            	本科
//                        @elseif($type == \'14\')
//								专科
//            			@elseif($type == \'3\')
//								研究生及以上
//                        @endif' )->edit_column ( 'status', '@if($status == \'0\')
//                            	未审
//                        @elseif($status == \'-1\')
//								不同意
//						@elseif($status == \'1\')
//								已审
//                        @endif' )
//            ->add_column ( 'actions', '@if($status == \'0\')
//                        		 <a href="{{{ URL::to(\'admin/program/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs">{{{ Lang::get(\'button.edit\') }}}</a>
//                                @endif')
//            ->remove_column ( 'id' )->filter_column ( 'teacher_count', 'where', 'teacher_count', '=', '-1', 'and' )->make ();
//    }
//
//    public function getDataForApprove() {
//        $programs = Program::where ( 'programs.name', '<>', 'null' )
//            ->join('rawprograms', 'rawprograms.id', '=', 'programs.name')
//            ->leftjoin ( 'program_teacher', 'programs.id', '=', 'program_teacher.program_id' )
//            ->leftjoin ( 'campuses', 'programs.campus_id', '=', 'campuses.id' )
//            ->groupBy ( 'programs.id' )
//            ->select ( array (
//                'programs.id',
//                'rawprograms.type',
//                'rawprograms.name',
//                'campuses.name as campusname',
//                DB::raw ( 'count(program_teacher.teacher_id) as teacher_count' ),
//                'programs.status',
//                'programs.created_at'
//            ))
//            ->orderby('rawprograms.type')
//            ->orderby('rawprograms.name')
//            ->orderby('campuses.name');
//
//        return Datatables::of ( $programs )->edit_column ( 'type', '@if($type == \'12\')
//                            	本科
//                        @elseif($type == \'14\')
//								专科
//            			@elseif($type == \'3\')
//								研究生及以上
//                        @endif' )->edit_column ( 'status', '@if($status == \'0\')
//                            	未审
//                        @elseif($status == \'-1\')
//								不同意
//						@elseif($status == \'1\')
//								已审
//                        @endif' )
//            ->add_column ( 'actions', '
//                        	@if (Entrust::can(\'adminprogram\') && $status == \'0\')
//                        		<a href="{{{ URL::to(\'admin/program/\' . $id . \'/approve\' ) }}}" class="iframe btn btn-xs">
//									{{{ Lang::get(\'admin/admin.function_group_approve\') }}}
//								</a>
//                        	@else
//								<a href="{{{ URL::to(\'admin/program/\' . $id . \'/approve\' ) }}}" class="iframe btn btn-xs">
//									{{{ Lang::get(\'admin/admin.function_group_view\') }}}
//								</a>
//								<a href="{{{ URL::to(\'admin/program/\' . $id . \'/approve?action=revert\' ) }}}" class="iframe btn btn-xs">
//									{{{ Lang::get(\'admin/admin.function_group_revert\') }}}
//								</a>
//							@endif
//                        		' )
//            ->remove_column ( 'id' )->filter_column ( 'teacher_count', 'where', 'teacher_count', '=', '-1', 'and' )->make ();
//    }
//
//    public function getRawPrograms() {
//        $inrank = Input::get('option');
//
//        //get campus
//        $campus = Campus::where('userID', '=', Auth::id())->first();
//
//        // get existings programs
//        $programs = DB::table('programs')
//            ->select('rawprograms.id AS rid')
//            ->join('rawprograms', 'rawprograms.id', '=', 'programs.name')
//            ->where('programs.campus_id', '=', $campus->id)
//            ->where('programs.rank', '=', $inrank)
//            ->where('programs.status', '>=', 0)
//            ->lists('rid');
//
//        // filter existings programs from rawprograms
//        if ($programs) {
//            $rawprograms = DB::table('rawprograms')
//                ->where('rawprograms.type', $inrank)
//                ->whereNotIn('rawprograms.id', $programs)
//                ->orderBy('rawprograms.name', 'ASC')
//                ->lists('name', 'id');
//        } else {
//            $rawprograms = DB::table('rawprograms')
//                ->where('rawprograms.type', $inrank)
//                ->orderBy('rawprograms.name', 'ASC')
//                ->lists('name', 'id');
//        }
//        if ($rawprograms) {
//            return Response::json($rawprograms);
//        } else {
//            return Response::json(array(Lang::get ( 'admin/program/messages.programnotset' )));
//        }
//    }

}