@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/admissions/'.$id.'/delete_punish_cause') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                <br>
                <br>
                <button type="submit" class="btn btn-danger" id="btnSubmit" value="1">{{{ Lang::get('general.delete') }}}</button>
                <button type="button" class="btn-cancel close_popup" onclick="javascript:window.location.href='{{URL::to('admin/admissions/punish_cause_index')}}';">{{{ Lang::get('general.cancel') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop