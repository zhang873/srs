@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="form"  class="form-horizontal" method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->

       <!-- major_name -->
        <div class="form-group {{{ $errors->has('exemption_type_name') ? 'error' : '' }}}">
            <label class="col-md-2 control-label" for="name">{{{Lang::get('admin/exemption/table.name') }}}</label>
            <div class="col-md-10">
                @if ($mode == 'create')
                    <input class="form-control" type="text" name="exemption_type_name" id="name"
                           value="{{{ Input::old('exemption_type_name') }}}" /> {{ $errors->first('exemption_type_name', '<span
					class="help-inline">:message</span>') }}
                @else
                    <input class="form-control" type="text" name="exemption_type_name" id="exemption_type_name"
                           value="{{{ $exemptiontype->exemption_type_name }}}" /> {{ $errors->first('exemption_type_name', '<span
					class="help-inline">:message</span>') }}
                @endif
            </div>
        </div>
        <!-- ./ major_name -->


    <!-- major_id -->
    <div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="code">{{{Lang::get('admin/exemption/table.sysid') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ Input::old('code') }}}" /> {{ $errors->first('code', '<span
                        class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ $exemptiontype->code }}}" /> {{ $errors->first('code', '<span
                        class="help-inline">:message</span>') }}
            @endif
        </div>
    </div>
    <!-- ./ major_id -->

    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group" align="center">
        <div class="controls">
            <button type="submit" class="btn btn-success">{{{ Lang::get('admin/exemption/table.ok') }}}</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            <button type="cancel" class="btn-cancel close_popup">{{{ Lang::get('admin/exemption/table.cancel') }}}</button>

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
                        var exemption_type_name = $("#exemption_type_name").val();
                        var code = $("#code").val();

                        if (exemption_type_name == "") {
                            alert("请输入系统外专业名称！");
                            $("#exemption_type_name").focus();
                            return false;
                        } else if (exemption_type_name.search(/(^[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                            alert("只能输入文字、数字！");
                            $("#exemption_type_name").focus();
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