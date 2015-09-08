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
        <div class="form-group {{{ $errors->has('name') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="name">{{{
				Lang::get('admin/course/table.module_name') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ Input::old('name') }}}" /> {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ $module->name }}}" /> {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ name -->

        <div class="form-group {{{ $errors->has('sysid') ? 'error' : '' }}}">
            <label class="col-md-2 control-label">{{{
				Lang::get('admin/course/table.module_code') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="code" id="code"
                           value="{{{ Input::old('code') }}}" /> {{ $errors->first('code', '<span class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="code" id="code"
                           value="{{{ $module->code }}}" /> {{ $errors->first('code', '<span class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
  
    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group">
        <div class="controls">
            <button class="btn-cancel close_popup">{{{ Lang::get('admin/depart/table.cancel') }}}</button>
            <button type="submit" class="btn btn-success">{{{ Lang::get('admin/depart/table.ok') }}}</button>
		</div>
    </div>
    <!-- ./ form actions -->
</form>
@stop
