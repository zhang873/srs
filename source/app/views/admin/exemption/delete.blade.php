@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.exemption_delete') }}}
        </h3>
    </div>
    {{-- Delete Unified Exam Info Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/exemption/' . $id . '/delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                <button type="button" class="btn btn-general" onclick="javascript:window.location.href='{{URL::to('admin/exemption')}}'">{{{ Lang::get('general.cancel') }}}</button>
                <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop