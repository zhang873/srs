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
    <div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}">
        <label class="col-md-2 control-label" for="subject">{{{Lang::get('admin/exemption/table.name') }}}</label>
        <div class="col-md-10">
            @if ($mode == 'create')
                <input class="form-control" type="text" name="subject" id="subject"
                       value="{{{ Input::old('subject') }}}" /> {{ $errors->first('subject', '<span
					class="help-inline">:message</span>') }}
            @else
                <input class="form-control" type="text" name="subject" id="subject"
                       value="{{{ $subject->subject }}}" /> {{ $errors->first('subject', '<span
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
                       value="{{{ $subject->code }}}" /> {{ $errors->first('code', '<span
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

            <button class="btn-cancel close_popup">{{{ Lang::get('admin/depart/table.cancel') }}}</button>

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
                        var subject = $("#subject").val();
                        var code = $("#code").val();

                        if (subject == "") {
                            alert("请输入免统考科目名称！");
                            $("#subject").focus();
                            return false;
                        } else if (subject.search(/(^[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                            alert("免统考科目名称只接受文字、数字！");
                            $("#subject").focus();
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