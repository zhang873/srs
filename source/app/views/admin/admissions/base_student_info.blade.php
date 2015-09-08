@extends('admin.layouts.frame_modal')

{{-- Web site Title --}}
@section('title')
	base:: @parent
@stop

{{-- Content --}}
@section('content')
    <table id="admissions"  class="table table-striped table-hover table-bordered" align="center" width="800px">
        <tr>
            <td colspan="6" align="center"><h3>{{{ Lang::get('admin/admissions/title.student_base_info') }}}</h3></td>
        </tr>
        <tr>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.ID_number') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.digital_registration_number') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.graduated_year') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.graduated_semester') }}}</th>
        </tr>
        <tr>
            @if ($baseInfo != null)
                <td>{{$baseInfo->fullname}}</td>
                <td>{{$baseInfo->studentno}}</td>
                <td>{{$baseInfo->idnumber}}</td>
                <td>{{$baseInfo->elec_regis_no}}</td>
                <td>{{$baseInfo->graduation_year}}</td>
                <td>@if ($baseInfo->graduation_semester == 1)春季@elseif($baseInfo->graduation_semester == 2)秋季@else无@endif</td>
            @else
                <td>无</td>
                <td>无</td>
                <td>无</td>
                <td>无</td>
                <td>无</td>
                <td>无</td>
            @endif
        </tr>
    </table>

    @if ($baseInfo != null)
        <div class="form-group" align="center">
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_admission') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_admission') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_selection') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_selection') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_exam') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_exam') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_exemption') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_exemption') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_net_score') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_unified_exam') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_history_score') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_history_score') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_rewards_punishments' ) }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_rewards_punishments') }}}</a>&nbsp;|&nbsp;
        <a href="{{{ URL::to('admin/admissions/'.$baseInfo->id.'/query_graduate') }}}" class="btn btn-xs" target="detail_info">
            {{{ Lang::get('admin/admissions/title.query_graduate') }}}</a>
        </div>
    @endif

@stop

@section('styles')

<style>
    .rlbl{
        text-align:right;
        width:100px;
    }
    .twidth{
        width:150px;
    }
    .col-md-01 {
        width: 10%;
    }
    .col-md-02 {
        width: 8%;
    }
    .col-md-03 {
        width: 5%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        $(document).ready(function() {
		});
	</script>
@stop