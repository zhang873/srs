@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/admissions/' . $id . '/school_delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <element class="btn-cancel close_popup">{{{ Lang::get('general.cancel') }}}</element>
                <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop