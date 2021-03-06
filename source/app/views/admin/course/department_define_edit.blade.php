@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="create_edit_Form"  class="form-horizontal" method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    
        <!-- Name -->
        <div class="form-group {{{ $errors->has('department_name') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="name">{{{
				Lang::get('admin/course/table.department_name') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ Input::old('name') }}}" /> {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ $department->name }}}" /> {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ name -->

        <div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
            <label class="col-md-2 control-label">{{{
				Lang::get('admin/course/table.department_code') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="code" id="code"
                           value="{{{ Input::old('code') }}}" /> {{ $errors->first('code', '<span class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="code" id="code"
                           value="{{{ $department->code }}}" /> {{ $errors->first('code', '<span class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
  
    <!-- ./ tabs content -->

    <!-- Form Actions -->

    <div class="controls">
        <button class="btn btn-default close_popup">{{{ Lang::get('general.cancel') }}}</button>
        @if ($mode == 'create')
            <button type="submit" class="btn btn-success">{{{ Lang::get('general.add') }}}</button>
        @else
            <button type="submit" class="btn btn-success">{{{ Lang::get('button.ok') }}}</button>
        @endif
    </div>

    <!-- ./ form actions -->
</form>
@stop
