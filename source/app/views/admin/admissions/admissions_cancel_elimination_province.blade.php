@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/admissions/title.cancel_expel_admissions') }}}
        </h3>
    </div>
    {{-- Delete Unified Exam Info Form --}}
    <form id="passForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/admissions/' . $id . '/admissions_cancel_elimination') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                {{Lang::get('admin/admissions/table.student_name')}}:{{$admission->fullname}} &nbsp;&nbsp;
                {{Lang::get('admin/admissions/table.student_id')}}:{{ $admission->studentno}}<br>
                <input type="button" class="btn btn-general" id="cancel" name="cancel" value="取消" onclick="javascript:window.location.href='/admin/admissions/expel_admissions'">
                <button type="submit" class="btn btn-sucess" name="elimination" value="elimination">撤销开除</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop