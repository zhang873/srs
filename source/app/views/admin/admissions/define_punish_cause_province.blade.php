@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Create Form --}}
    <form  id="form"  class="form-horizontal" method="post" action="" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <!-- Tabs Content -->
        <div class="form-group" align="center">
            <h4>
                输入惩罚原因
            </h4>
        </div>
        <!-- level -->
        <div class="form-group {{{ $errors->has('punishment_cause') ? 'error' : '' }}}" align="center">
            <label class="rlbl" for="cause">{{{
                    Lang::get('admin/admissions/table.punish_cause') }}}</label>
                @if ($mode == 'create')
                    <input type="text" name="punishment_cause" id="punishment_cause"
                           value="{{{ Input::old('punishment_cause') }}}" /> {{ $errors->first('punishment_cause', '<span
                        class="help-inline">:message</span>') }}
                @else
                    <input  type="text" name="punishment_cause" id="punishment_cause"
                           value="{{{ $cause->punishment_cause }}}" /> {{ $errors->first('punishment_cause', '<span
                        class="help-inline">:message</span>') }}
                @endif
        </div>
        <!-- ./ level -->

        <!-- sysid -->
        <div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}" align="center">
            <label class="rlbl" for="code">{{{
				Lang::get('admin/admissions/table.code') }}}</label>
                @if ($mode == 'create')
                    <input  type="text" name="code" id="code"
                           value="{{{ Input::old('sysid') }}}" /> {{ $errors->first('code', '<span
					class="help-inline">:message</span>') }}
                @else
                    <input type="text" name="code" id="code"
                           value="{{{ $cause->code }}}" /> {{ $errors->first('code', '<span
					class="help-inline">:message</span>') }}
                @endif
        </div>
        <!-- ./ sysid -->

        <!-- ./ tabs content -->

        <!-- Form Actions -->
        <div class="form-group" align="center">
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
            width:150px;
        }
        button{
            margin: 0;
        }

    </style>

@stop
{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        $(document).ready(function() {
            $("#form").submit(function () {
                var punishment_cause = $("#punishment_cause").val();
                var code = $("#code").val();

                if (punishment_cause == "") {
                    alert("请输入惩罚原因！");
                    $("#punishment_cause").focus();
                    return false;
                }else if (!empty(punishment_cause) && (punishment_cause.search(/(^(?!.*?_$)[\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("只能输入文字！");
                    $("#punishment_cause").focus();
                    return false;
                }

                if (code == "") {
                    alert("请输入5位数字代号！");
                    $("#code").focus();
                    return false;
                }else if ((code.length !=5) || (code.search(/(^(?!.*?_$)[0-9_]+$)/) == -1)) {
                    alert("只能输入5位数字代号！");
                    $("#code").focus();
                    return false;
                }
                return true;
            });
        });
    </script>
@stop