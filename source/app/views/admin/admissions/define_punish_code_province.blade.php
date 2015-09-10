@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Create Form --}}
    <form  id="create_edit_Form"  class="form-horizontal" method="post" action="" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <input type="hidden" name="id" id="id" value="{{$_GET['id']}}">
        <!-- Tabs Content -->
        <div class="form-group" align="center">
            <h4>
                输入惩罚代码
            </h4>
        </div>
        <!-- level -->
        <div class="form-group {{{ $errors->has('name') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="name">{{{
                    Lang::get('admin/admissions/table.punish_code') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ Input::old('name') }}}" /> {{ $errors->first('name', '<span
                        class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="name" id="name"
                           value="{{{ $reward->name }}}" /> {{ $errors->first('name', '<span
                        class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ level -->

        <!-- sysid -->
        <div class="form-group {{{ $errors->has('sysid') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="name">{{{
				Lang::get('admin/depart/table.sysid') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="sysid" id="sysid"
                           value="{{{ Input::old('sysid') }}}" /> {{ $errors->first('sysid', '<span
					class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="sysid" id="sysid"
                           value="{{{ $reward->sysid }}}" /> {{ $errors->first('sysid', '<span
					class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ sysid -->

        <!-- ./ tabs content -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success">{{{ Lang::get('admin/admissions/table.save') }}}</button>
                <button class="btn-cancel close_popup">{{{ Lang::get('admin/admissions/table.cancel') }}}</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>




@stop


@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:80px;

        }
    </style>
@stop
