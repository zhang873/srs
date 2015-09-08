<?php

class AdminDashboardController extends AdminController {

	/**
	 * Admin dashboard
	 *
	 */
	public function getIndex()
    {
        $state = -1;
        if (Entrust::hasRole('staff')) {
            $user_id = Auth::user()->id;
            $campus_id = DB::table('campuses')->where('userID', $user_id)->first()->id;
            Session::put('campus_id', $campus_id);
            $records = DB::table('state_info')->where('campus_id', $campus_id)->first();
            if (!is_null($records))
                $state = $records->campus_student_selection;
        }
        return View::make('admin/dashboard', compact('state'));
    }

}