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
    <div class="form-group {{{ $errors->has('cause') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="cause">{{{Lang::get('admin/unified_exam/table.name') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="cause" id="cause"
                       value="{{{ Input::old('cause') }}}" /> {{ $errors->first('cause', '<span
					class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="cause" id="cause"
                       value="{{{ $cause->cause }}}" /> {{ $errors->first('cause', '<span
					class="help-inline">:message</span>') }}
            @endif
        </div>
    </div>
    <!-- ./ name -->


    <!-- code -->
    <div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="code">{{{Lang::get('admin/exemption/table.sysid') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ Input::old('code') }}}" /> {{ $errors->first('code', '<span
                        class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="code" id="code"
                       value="{{{ $cause->code }}}" /> {{ $errors->first('code', '<span
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
            <button type="submit" class="btn btn-success">{{{ Lang::get('admin/depart/table.ok') }}}</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            <button type="cancel" class="btn-cancel close_popup" onclick="javascript:windows.location.href='admin/unified_exam/unified_exam_cause'">{{{ Lang::get('admin/depart/table.cancel') }}}</button>

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
                        var cause = $("#cause").val();
                        var code = $("#code").val();

                        if (cause == "") {
                            alert("请输入免统考原因！");
                            $("#cause").focus();
                            return false;
                        } else if (cause.search(/(^[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                            alert("免统考原因只接受文字、数字！");
                            $("#cause").focus();
                            return false;
                        }

                        if (code == "") {
                            alert("请输入5位数字类型的代号！");
                            $("#code").focus();
                            return false;
                        } else if ((code.length !=5) || (code.search(/(^(?!_)(?!.*?_$)[0-9]+$)/) == -1)) {
                            alert("请输入5位数字类型的代号");
                            $("#code").focus();
                            return false;
                        }

                        return true;
                    });
                }
        )
    </script>
@stop