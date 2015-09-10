@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="form"  class="form-horizontal" method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->

       <!-- name -->
        <div class="form-group {{{ $errors->has('agency_name') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="agency_name">{{{Lang::get('admin/exemption/table.name') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="agency_name" id="agency_name"
                           value="{{{ Input::old('agency_name') }}}" /> {{ $errors->first('agency_name', '<span
					class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="agency_name" id="agency_name"
                           value="{{{ $exemptionagency->agency_name }}}" /> {{ $errors->first('agency_name', '<span
					class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ name -->


    <!-- code -->
    <div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="sysid">{{{Lang::get('admin/exemption/table.sysid') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ Input::old('code') }}}" /> {{ $errors->first('code', '<span
                        class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ $exemptionagency->code }}}" /> {{ $errors->first('code', '<span
                        class="help-inline">:message</span>') }}
            @endif
        </div>
    </div>
    <!-- ./ code -->

    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group" align="center">
        <div class="controls">
            <button type="submit" class="btn btn-success">{{{ Lang::get('admin/exemption/table.ok') }}}</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            <button type="cancel" class="btn-cancel close_popup" onclick="javascript:window.location.href='admin/exemption/exemption_agency'">{{{ Lang::get('admin/exemption/table.cancel') }}}</button>

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
                        var agency_name = $("#agency_name").val();
                        var code = $("#code").val();

                        if (agency_name == "") {
                            alert("请输入系统外专业名称！");
                            $("#agency_name").focus();
                            return false;
                        } else if (agency_name.search(/(^[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                            alert("只能输入文字、数字！");
                            $("#agency_name").focus();
                            return false;
                        }

                        if (code == "") {
                            alert("请输入5位数的代号！");
                            $("#code").focus();
                            return false;
                        } else if ((code.length !=5) || (code.search(/(^(?!_)(?!.*?_$)[0-9]+$)/) == -1)) {
                            alert("请输入5位数的代号");
                            $("#code").focus();
                            return false;
                        }

                        return true;
                    });
                }
        )
    </script>
@stop