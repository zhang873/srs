@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/exemption/' . $id . '/exemption_major_outer_delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                <button class="btn-cancel btn close_popup" type="button" onclick="window.location.href='{{URL::to('admin/exemption/exemption_major_outer')}}'">{{{ Lang::get('general.cancel') }}}</button>
                <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop