@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="form"  class="form-horizontal" method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    <div class="form-group">
        <h3>
            输入分校
        </h3>
    </div>
    <!-- name -->
    <div class="form-group {{{ $errors->has('school_name') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="name">{{{Lang::get('admin/admissions/table.school_name') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="school_name" id="school_name"
                       value="{{{ Input::old('school_name') }}}" /> {{ $errors->first('school_name', '<span
					class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="school_name" id="school_name"
                       value="{{{ $schools->school_name }}}" /> {{ $errors->first('school_name', '<span
					class="help-inline">:message</span>') }}
            @endif
        </div>
    </div>
    <!-- ./ name -->


    <!-- code -->
    <div class="form-group {{{ $errors->has('school_id') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="sysid">{{{Lang::get('admin/admissions/table.code') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="school_id" id="school_id"
                       value="{{{ Input::old('school_id') }}}" /> {{ $errors->first('school_id', '<span
                        class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ $schools->school_id }}}" /> {{ $errors->first('school_id', '<span
                        class="help-inline">:message</span>') }}
            @endif
        </div>
    </div>
    <!-- ./ code -->
    <!-- ./ major_id -->

    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group" align="center">
        <div class="controls">
            <button type="submit" class="btn btn-success">{{{ Lang::get('admin/admissions/table.ok') }}}</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            <button type="cancel" class="btn-cancel close_popup" onclick="javascript:windows.location.href='admin/admissions/school'">{{{ Lang::get('admin/admissions/table.cancel') }}}</button>

		</div>
    </div>
    <!-- ./ form actions -->
</form>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(
                function() {
                    $("#form").submit(function () {
                        var school_id = $("#school_id").val();
                        var school_name = $("#school_name").val();


                        if (school_id == "") {
                            alert("请输入分校代号！");
                            $("#school_id").focus();
                            return false;
                        }


                        if (school_name == "") {
                            alert("请输入分校名称！");
                            $("#school_name").focus();
                            return false;
                        }
                        return true;
                    });
                })
    </script>
@stop