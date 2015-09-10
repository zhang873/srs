@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/unified_exam/' . $id . '/unified_exam_subject_delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                确定删除{{$subject->subject}}吗？
                    <br>
                    <br>
                    <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
                    <button class="btn-cancel close_popup" onclick="javascript:window.location.href='{{URL::to('admin/unified_exam/unified_exam_subject')}}';">{{{ Lang::get('general.cancel') }}}</button>
                </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop