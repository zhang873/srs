@extends('admin.layouts.modal')
{{-- Web site Title --}}
{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form id="form" name="form" class="form-horizontal" method="post"  autocomplete="off" action="{{URL::to('admin/admissions/'.$id.'/record_reward_campus')}}">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <input type="hidden" name="id" id="id" value="{{$id}}">
    <div class="form-group" align="center">
        <h3>
            {{ Lang::get('admin/admissions/table.record_reward') }}
        </h3>
    </div>
    <!-- Tabs Content -->
    <div class="form-group" align="center" width="600px">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.reward_date') }}</label>
        <input type="text" id="reward_date" name="reward_date" readonly style="width:150px;"/>&nbsp;
        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.reward_level') }}</label>
        <select name="rewardType" id="rewardType" style="width:150px;">
            @foreach($rewards as $reward)
                <option value="{{$reward->id}}">{{$reward->reward_level}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group" align="center" width="600px">
        <label for="fileNo" class="rlbl" >{{ Lang::get('admin/admissions/table.fileNo') }}</label>
        <input type="text" name="file_num" id="file_num" style="width:150px;">
        <label for="operator" class="rlbl" >{{ Lang::get('admin/admissions/table.actor') }}</label>
        <input type="text" name="operator" id="operator" style="width:150px;">
    </div>
    <div class="form-group" align="center" width="600px">
        <label for="remark" class="rlbl">{{ Lang::get('admin/admissions/table.remark') }}</label>
        <input type="text" name="remark" id="remark" style="width:150px;">
        <label for="remark" class="rlbl">&nbsp;&nbsp;</label>
        <label for="remark"  style="width:150px;"></label>
    </div>

    <!-- ./ form actions -->
    <div align="center">
        <button type="submit" id="btnSave" name="btnSave" >记录奖励</button>
    </div>
</form>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:80px;
        }
        button{
            margin: 0;
        }
        .calendar{
            width:130px;
            line-height:20px;
            padding:2px;
        }
        img{border:none;width:20px;font-size: 12px;}
        table{border-collapse:collapse;border-spacing:0}

    </style>
    {{ HTML::style('assets/css/jquery-ui.css') }}
@stop
{{-- Scripts --}}
@section('scripts')
    {{ HTML::script('assets/js/jquery.min.js') }}
    {{ HTML::script('assets/js/jquery-ui-datepicker.js') }}
    <script type="text/javascript">
        $(document).ready(function() {
            $(function() {
                $("#reward_date").datepicker({
                    dateFormat: 'yy/mm/dd'
                });
            });
            $("#form").submit(function () {
                var reward_date = $("#reward_date").val();
                var rewardType = $("#rewardType").val();
                var file_num = $("#file_num").val();
                var operator = $("#operator").val();
                var remark = $("#remark").val();

                if (reward_date == "") {
                    alert("请选择日期！");
                    $("#reward_date").focus();
                    return false;
                }
                if (rewardType == "") {
                    alert("请选择奖励级别！");
                    $("#rewardType").focus();
                    return false;
                }

                if (file_num == "") {
                    alert("请输入文号！");
                    $("#file_num").focus();
                    return false;
                } else if (file_num.search(/(^(?!_)(?!.*?_$)[0-9]+$)/) == -1) {
                    alert("请输入数字文号");
                    $("#file_num").focus();
                    return false;
                }

                if (operator == "") {
                    alert("请输入操作员姓名！");
                    $("#operator").focus();
                    return false;
                }else if (operator.search(/(^(?!.*?_$)[0-9_a-z_\u4e00-\u9fa5]+$)/) == -1) {
                    alert("只能输入文字！");
                    $("#operator").focus();
                    return false;
                }

                if (!empty(remark) && (remark.search(/(^(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("只能输入文字、数字！");
                    $("#remark").focus();
                    return false;
                }

                return true;
            });
        });
    </script>
@stop