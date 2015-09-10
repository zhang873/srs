@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($dropout)){{ URL::to('admin/admissions/delete_dropout?id='.$dropout->id) }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $dropout->id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                确定删除{{$dropout->studentno}}的退学申请吗？
                <br>
                <br>
                <button type="submit" class="btn btn-danger" id="btnSubmit" value="1">{{{ Lang::get('general.delete') }}}</button>
                <button type="button" class="btn-cancel close_popup" onclick="javascript:window.location.href='{{URL::to('admin/admissions/edit_dropout?id='.$dropout->id)}}';">{{{ Lang::get('general.cancel') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop