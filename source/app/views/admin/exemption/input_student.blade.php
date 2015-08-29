@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	{{-- Create User Form --}}
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.input_stuinfo')}}}
        </h3>
    </div>

	<form class="form-horizontal" method="post"  action="{{ URL::to('admin/exemption/query') }}" autocomplete="off">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->

        <table id="exemption" class="table-striped table-hover" align="center" width="800">
            <thead>
            <tr>
                <td class="col-md-2" align="right">{{{ Lang::get('admin/exemption/table.student_id') }}}</td>
                <td class="col-md-2" align="left">  <input class="form-control" type="text" name="student_id" id="student_id" value="{{{ Input::old('student_id') }}}" />
                    {{ $errors->first('sno', '<span class="help-inline">:message</span>') }}</td>
                <td class="col-md-2" align="right">{{{ Lang::get('admin/exemption/table.student_name') }}}</td>
                <td class="col-md-2" align="left">
                    <input class="form-control" type="text" name="student_name" id="student_name" value="{{{ Input::old('student_name') }}}" />
                    {{ $errors->first('student_name', '<span class="help-inline">:message</span>') }}</td>
            </tr>
            <tr>
                <td colspan="4" height="10px"></td>
            </tr>
            <tr>
                <td class="col-md-2" colspan="4" align="center">
                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-default" value="query" id="query">{{{ Lang::get('admin/exemption/table.query') }}}</button>
                    <!-- ./ form actions -->
                </td>
            </tr>
            <thead>
            <tbody>
            </tbody>
        </table>
        <hr>

	</form>
@stop