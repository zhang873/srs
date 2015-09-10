@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/unified_exam/title.delete_unified_exam_info') }}}
        </h3>
    </div>
    {{-- Delete Unified Exam Info Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/unified_exam/' . $id . '/delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="cancel" class="btn btn-general">{{{ Lang::get('general.cancel') }}}</button>
                <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop