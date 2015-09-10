@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.check_exemption') }}}
        </h3>
    </div>
    {{-- Delete Unified Exam Info Form --}}
    <form id="passForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/exemption/' . $id . '/exemption_province_pass') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <input type="button" class="btn btn-general" id="cancel" name="cancel" value="取消" onclick="javascript:window.location.href='/admin/exemption/exemption_province'">
                <button type="submit" class="btn btn-sucess" name="pass" value="pass">{{{ Lang::get('general.pass') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop