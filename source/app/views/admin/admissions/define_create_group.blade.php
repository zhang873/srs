@extends('admin.layouts.modal') 


{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form class="form-horizontal" method="post" action="" autocomplete="off" id="createForm">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    <div class="tab-content">
        <!-- Name -->
        <div class="form-group {{{ $errors->has('programid') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="programid">{{{
				Lang::get('admin/groups/table.groups_program_name') }}}</label>
            <div class="col-md-10">
                <select id="programid" name="programid" class="form-control" {{{ ($mode == 'edit' ? 'disabled=disabled' : '') }}}>
                    <option value="0">{{{ Lang::get('general.pleaseselect') }}}</option>
                    @foreach ($param->programs as $program)
                        {{{ $prefix = '' }}}
                        @if ($program->type == '12') 
                            {{{ $prefix = '本科 - ' }}}
                        @elseif ($program->type == '14') 
                            {{{ $prefix = '专科 - ' }}}
                        @else
                            {{{ $prefix = '研究生及以上 - ' }}}
                        @endif
                        <option value="{{{ $program->id }}}"@if ($param->selectedProgramID==$program->id) selected="selected" @endif>{{{ $prefix.$program->name }}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- ./ name -->

        <div class="form-group {{{ $errors->has('groupname') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="groupname">{{{
				Lang::get('admin/groups/table.groups_name') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                <input class="form-control" type="text" name="groupname" id="groupname"
                       value="{{{ Input::old('groupname') }}}" /> {{ $errors->first('groupname', '<span
					class="help-inline">:message</span>') }}
                @else
                <input class="form-control" type="text" name="groupname" id="groupname"
                       value="{{{ $param->groupname }}}" {{{ ($mode == 'approve' ? 'disabled=disabled' : '') }}}/> {{ $errors->first('groupname', '<span
					class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
    </div>
    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group">

        <div class="col-md-offset-2 col-md-10">
            <button type="reset" class="btn btn-default">{{{
				Lang::get('admin/users/table.reset') }}}</button>
            <button type="submit" class="btn btn-success">{{{
				Lang::get('admin/users/table.ok') }}}</button>
        </div>
    </div>
    <!-- ./ form actions -->
</form>
@stop