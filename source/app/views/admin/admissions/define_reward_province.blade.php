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
                {{{Lang::get('admin/admissions/title.input_reward_level') }}}
            </h4>
        </div>
        <!-- level -->
        <div class="form-group" align="center">
            <label  for="reward_level" class="rlbl">{{{Lang::get('admin/admissions/table.reward_level') }}}</label>&nbsp;&nbsp;
                @if ($mode == 'create')
                    <input  type="text" name="reward_level" id="reward_level"  value="{{{ Input::old('reward_level') }}}" style="width: 150px" />
                @else
                    <input type="text" name="reward_level" id="reward_level"  value="{{{ $reward->reward_level }}}" style="width: 150px" />
                @endif
        </div>
        <!-- ./ level -->

        <!-- sysid -->
        <div class="form-group" align="center">
            <label for="code" class="rlbl">{{{	Lang::get('admin/admissions/table.code') }}}</label>&nbsp;&nbsp;
                @if ($mode == 'create')
                    <input type="text" name="code" id="code"
                           value="{{{ Input::old('code') }}}" style="width: 150px" />
                @else
                    <input  type="text" name="code" id="code"
                           value="{{{ $reward->code }}}" style="width: 150px" />
                @endif
        </div>
        <!-- ./ sysid -->

        <!-- ./ tabs content -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                <button type="submit" class="btn btn-success">{{{ Lang::get('admin/admissions/table.save') }}}</button>
                <button type="button" class="btn btn-success" onclick="history.go(-1)">{{{ Lang::get('admin/admissions/table.cancel') }}}</button>
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
                var level = $("#level").val();
                var sysid = $("#sysid").val();

                if (level == "") {
                    alert("请输入奖励级别！");
                    $("#level").focus();
                    return false;
                }else if (!empty(level) && (level.search(/(^(?!.*?_$)[\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("只能输入文字！");
                    $("#level").focus();
                    return false;
                }

                if (sysid == "") {
                    alert("请输入5位数字代号！");
                    $("#sysid").focus();
                    return false;
                }else if ((sysid.length !=5) || (sysid.search(/(^(?!.*?_$)[0-9_]+$)/) == -1)) {
                    alert("只能输入5位数字代号！");
                    $("#sysid").focus();
                    return false;
                }






                return true;
            });
        });
    </script>
@stop