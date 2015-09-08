@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('admin/admin.admin_functions') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
    This is main dash board
</div>

@if (Entrust::hasRole('admin'))
<h3>{{ Lang::get('admin/admin.function_group_admin') }}</h3>
<div>

    @if (Entrust::can('manage_users'))
        <a href="{{{ URL::to('admin/users') }}}">{{{ Lang::get('admin/admin.manageusers') }}}</a>&nbsp;
    @endif
    @if (Entrust::can('manage_roles'))
        <a href="{{{ URL::to('admin/roles') }}}">{{{ Lang::get('admin/admin.manageroles') }}}</a>&nbsp;
    @endif
</div>

<div>
<a href="{{{ URL::to('admin/course') }}}">{{{ Lang::get('admin/course/title.make_course') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/establish') }}}">{{{ Lang::get('admin/course/title.establish_province_course') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/establish_browse') }}}">{{{ Lang::get('admin/course/title.browse_province_course') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/update_semester') }}}" class="iframe"> {{{ Lang::get('admin/course/title.update_course_year') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/teaching_plan') }}}">{{{ Lang::get('admin/course/title.make_teaching_plan') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/update_teaching_plan_semester') }}}" class="iframe"> {{{ Lang::get('admin/course/title.update_teaching_plan_year') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/department_define') }}}">{{{ Lang::get('admin/course/title.department_define') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/module_define') }}}">{{{ Lang::get('admin/course/title.module_define') }}}</a>&nbsp;
</div>
<br>
<div>
<a href="{{{ URL::to('admin/select/update_module_semester') }}}" class="iframe"> {{{ Lang::get('admin/select/title.update_module_year') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/campus/control') }}}"> {{{ Lang::get('admin/select/title.control_campus_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/campusConfirm/control') }}}"> {{{ Lang::get('admin/select/title.control_campus_confirmation') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/provinceStudent/control') }}}"> {{{ Lang::get('admin/select/title.control_province_student_selection') }}}</a>&nbsp;
</div>
<div>
<a href="{{{ URL::to('admin/select/count_number_course') }}}"> {{{ Lang::get('admin/select/title.count_number_course') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/query_group_selection') }}}"> {{{ Lang::get('admin/select/title.query_group_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/query_selection_record') }}}"> {{{ Lang::get('admin/select/title.query_selection_record') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/count_selection') }}}"> {{{ Lang::get('admin/select/title.count_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/summary_selection') }}}"> {{{ Lang::get('admin/select/title.summary_selection') }}}</a>&nbsp;
</div>

@elseif (Entrust::hasRole('staff'))
<div>
<a href="{{{ URL::to('admin/course/establish_school') }}}">{{{ Lang::get('admin/course/title.establish_school_course') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/course/establish_school_browse') }}}">{{{ Lang::get('admin/course/title.browse_school_course') }}}</a>&nbsp;
</div>

@if ($state != -1)
<div>
<a href="{{{ URL::to('admin/select/change_campus_selection') }}}"> {{{ Lang::get('admin/select/title.change_campus_selection') }}}</a>&nbsp;
@if ($state == 1)
<a href="{{{ URL::to('admin/select/group_selection') }}}"> {{{ Lang::get('admin/select/title.group_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/class_selection') }}}"> {{{ Lang::get('admin/select/title.class_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/delete_selection') }}}"> {{{ Lang::get('admin/select/title.delete_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/range_delete_selection') }}}"> {{{ Lang::get('admin/select/title.range_delete_selection') }}}</a>&nbsp;
@else
<a href="{{{ URL::to('admin/select/batch_confirm_selection') }}}" class="iframe"> {{{ Lang::get('admin/select/title.batch_confirm_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/confirm_selection') }}}"> {{{ Lang::get('admin/select/title.confirm_selection') }}}</a>&nbsp;
@endif
</div>
@endif
<div>
<a href="{{{ URL::to('admin/select/number_query_selection') }}}"> {{{ Lang::get('admin/select/title.number_query_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/summary_class_selection') }}}"> {{{ Lang::get('admin/select/title.summary_class_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/summary_times_selection') }}}"> {{{ Lang::get('admin/select/title.summary_times_selection') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/select/campus_count_selection') }}}"> {{{ Lang::get('admin/select/title.campus_count_selection') }}}</a>&nbsp;
</div>
@endif
<br>
<a href="{{{ URL::to('admin/admissions/comprehensive_student_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_comprehensive_query') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/student_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_information_query') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/basic_student_info') }}}"> {{{ Lang::get('admin/admissions/title.basic_student_info') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/reward_punish_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_reward_punish') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/status_changing_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_status_changing') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/change_need_select_courses') }}}"> {{{ Lang::get('admin/admissions/title.admissions_change_need_select_courses') }}}</a>&nbsp;

<a href="{{{ URL::to('admin/admissions/teaching_plan_count') }}}"> {{{ Lang::get('admin/admissions/title.admissions_teaching_plan_count') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/information_count') }}}"> {{{ Lang::get('admin/admissions/title.admissions_information_count') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/information_classification_count') }}}"> {{{ Lang::get('admin/admissions/title.admissions_information_classification_count') }}}</a>&nbsp;
<br><br>
教学点<br>
<a href="{{{ URL::to('admin/admissions/student_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_information_query') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/basic_student_info') }}}"> {{{ Lang::get('admin/admissions/title.basic_student_info') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/reward_punish_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_reward_punish') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/status_changing_info') }}}"> {{{ Lang::get('admin/admissions/title.admissions_status_changing') }}}</a>&nbsp;
<a href="{{{ URL::to('admin/admissions/information_classification_count') }}}"> {{{ Lang::get('admin/admissions/title.admissions_information_classification_count') }}}</a>&nbsp;

@stop

@section('scripts')
	<script type="text/javascript">
	$(document).ready(function() {
	    $(".iframe").colorbox({iframe:true, width:"40%", height:"50%"});
	});
	</script>
@stop