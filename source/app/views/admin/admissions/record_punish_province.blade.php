@extends('admin.layouts.modal')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form id="form" name="form" class="form-horizontal" method="post"  autocomplete="off" action="{{URL::to('admin/admissions/'.$id.'/record_punish_province')}}">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <input type="hidden" name="id" id="id" value="{{$id}}">
    <div class="form-group" align="center">
        <h3>
            {{ Lang::get('admin/admissions/table.record_punish') }}
        </h3>
    </div>
    <!-- Tabs Content -->
    <div class="form-group" align="center" width="600px">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.punish_code') }}</label>
        <select name="punish_code" id="punish_code"  style="width:150px;">
            <option value="">{{Lang::get('general.pleaseselect')}}</option>
            @foreach($codes as $code)
                <option value="{{$code->id}}">{{$code->punishment}}</option>
            @endforeach
        </select>

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.punish_cause') }}</label>
        <select name="punish_cause" id="punish_cause"  style="width:150px;">
            <option value="">{{Lang::get('general.pleaseselect')}}</option>
            @foreach($causes as $cause)
                <option value="{{$cause->id}}">{{$cause->punishment_cause}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group" align="center" width="600px">
        <label for="punish_date" class="rlbl" >{{ Lang::get('admin/admissions/table.punish_date') }}</label>
        <input type="text" name="punish_date" id="punish_date" style="width:150px;">
        <label for="fileNo" class="rlbl" >{{ Lang::get('admin/admissions/table.fileNo') }}</label>
        <input  type="text" name="file_num" id="file_num" style="width:150px;">

    </div>
    <div class="form-group" align="center" width="600px">
        <label for="remark" class="rlbl">{{ Lang::get('admin/admissions/table.remark') }}</label>
        <input  type="text" name="remark" id="remark" style="width:150px;">
        <label for="tt" class="rlbl">&nbsp;</label>
        <label for="tt"  style="width:150px;">&nbsp;</label>
    </div>
<!-- ./ form actions -->
        <div align="center">
              <button class="btn  btn-success close_popup" tabindex="7" >记录惩罚</button>
		</div>
</form>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:80px;
        }
    </style>
    {{ HTML::style('assets/css/jquery-ui.css') }}
@stop

{{-- Scripts --}}
@section('scripts')
    {{ HTML::script('assets/js/jquery.min.js') }}
    {{ HTML::script('assets/js/jquery-ui-datepicker.js') }}
<script type="text/javascript">
    $(document).ready(
            function() {
                $(function() {
                    $("#punish_date").datepicker({
                        dateFormat: 'yy/mm/dd'
                    });
                });
                $("#form").submit(function () {
                    var punish_code = $("#punish_code").val();
                    var punish_cause = $("#punish_cause").val();
                    var punish_date = $("#punish_date").val();
                    var file_num = $("#file_num").val();
                    var remark = $("#remark").val();
                    if (punish_code == "") {
                        alert("请选择惩罚代码！");
                        $("#punish_code").focus();
                        return false;
                    }

                    if (punish_cause == "") {
                        alert("请选择惩罚原因！");
                        $("#punish_cause").focus();
                        return false;
                    }
                    if (punish_date == "") {
                     alert("请选择日期！");
                     $("#punish_date").focus();
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

                    if ((remark!="") && (remark.search(/(^(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1)) {
                            alert("只能输入文字、数字！");
                            $("#remark").focus();
                            return false;
                    }
                    return true;
                });
            }
    );
</script>

    @stop