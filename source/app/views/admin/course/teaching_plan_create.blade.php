@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="create_Form"  method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    
        <!--id Name -->
        <div class="form-group {{{ $errors->has('id') || $errors->has('student_classification') ? 'error' : '' }}}">
            <label class="rlbl">{{{ Lang::get('admin/course/table.teaching_plan_code') }}}</label>
            <input type="text" class="twidth" tabindex="1" name="code" id="code" value="{{{ Input::old('code') }}}"/>
            <label class="rlbl">{{ Lang::get('admin/course/table.student_classification') }}</label>
            <select class="twidth" size="1" tabindex="2" name="student_classification" id="student_classification">
                @if (Input::old('student_classification') == null)
                    <option value="请选择" selected="selected">请选择</option>
                    <option value="14">专科</option>
                    <option value="12">本科</option>
                @else
                    <option value="请选择">请选择</option>
                    <option value="14" {{{ Input::old('student_classification') == 14 ? ' selected="selected"' : '' }}}>专科</option>
                    <option value="12" {{{ Input::old('student_classification') == 12 ? ' selected="selected"' : '' }}}>本科</option>
                @endif
            </select>
        </div>
        <div class="form-group">
            <label class="rlbl">{{ Lang::get('admin/course/title.classification') }}</label>
            <select class="twidth" size="1" tabindex="3" name="major_classification" id="major_classification">

            @if (Input::old('major_classification') == null)
                <option value="请选择" selected="selected">请选择</option>
                <option value="14">专科</option>
                <option value="12">本科</option>
            @else
                <option value="请选择">请选择</option>
                <option value="14" {{{ Input::old('major_classification') == 14 ? ' selected="selected"' : '' }}}>专科</option>
                <option value="12" {{{ Input::old('major_classification') == 12 ? ' selected="selected"' : '' }}}>本科</option>
            @endif
            </select>
            <label class="rlbl">{{ Lang::get('admin/course/title.major') }}</label>
            <select class="twidth" size="1" tabindex="4" name="major" id="major">
                @if (Input::old('major') == null)
                    <option value="请选择" selected="selected">请选择</option>
                    @foreach ($b_majors as $major)
                        <option value="{{{ $major }}}"> {{{ $major }}} </option>
                    @endforeach
                    @foreach ($z_majors as $major)
                        <option value="{{{ $major }}}"> {{{ $major }}} </option>
                    @endforeach
                @else
                    <option value="请选择">请选择</option>
                    @if (Input::old('major_classification') == 12)
                        @foreach ($b_majors as $major)
                            <option value="{{{ $major }}}" {{{ $major == Input::old('major') ? ' selected="selected"' : '' }}}> {{{ $major }}} </option>
                        @endforeach
                    @else
                        @foreach ($z_majors as $major)
                            <option value="{{{ $major }}}" {{{ $major == Input::old('major') ? ' selected="selected"' : '' }}}> {{{ $major }}} </option>
                        @endforeach
                    @endif
                @endif
            </select>
        </div>
        <div class="form-group {{{ $errors->has('min_credit_graduation') || $errors->has('schooling_period') ? 'error' : '' }}}">
            <label class="rlbl">{{{ Lang::get('admin/course/table.min_credit_graduation') }}}</label>
            <input type="text" class="twidth" tabindex="1" name="min_credit_graduation" id="min_credit_graduation" value="{{{ Input::old('min_credit_graduation') }}}"/>

            <label class="rlbl">{{ Lang::get('admin/course/table.schooling_period') }}</label>
            <select class="twidth" size="1" tabindex="2" name="schooling_period" id="schooling_period">
              <option value="2.0" selected="selected">2.0</option>
            </select>
        </div>
        <!--abbreviation credit -->
        <div class="form-group {{{ $errors->has('max_credit_exemption') || $errors->has('max_credit_semester') ? 'error' : '' }}}">
            <label class="rlbl">{{{ Lang::get('admin/course/table.max_credit_exemption') }}}</label>
            <input type="text" class="twidth" name="max_credit_exemption" id="max_credit_exemption" value="{{{ Input::old('max_credit_exemption') }}}" />

            <label class="rlbl">{{{ Lang::get('admin/course/table.max_credit_semester') }}}</label>
            <input type="text" class="twidth" name="max_credit_semester" id="max_credit_semester" value="{{{ Input::old('max_credit_semester') }}}" />

        </div>
        <!--res_teacher pra_tag -->
        <div class="form-group {{{ $errors->has('is_activated') ? 'error' : '' }}}">
            <label class="rlbl">{{{ Lang::get('admin/course/table.is_activated') }}}</label>
            <select class="twidth" size="1" tabindex="5" name="is_activated" id="is_activated">
                @if (Input::old('is_activated') == '1')
                    <option value="0">关</option>
                    <option value="1" selected="selected">启</option>
                @else
                    <option value="0" selected="selected">关</option>
                    <option value="1">启</option>
                @endif
            </select>
        </div>
        @for ($i = 0; $i < count($modules); $i++)
        <div class="form-group" align="center">
            @if (Input::old('checkitem') == null)
                <input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$modules[$i]->id}}}" ></input>
            @else
                <input type="checkbox" name="checkitem[]" id="checkitem" value="{{{$modules[$i]->id}}}" {{{ ( in_array($modules[$i]->id, Input::old('checkitem', array())))  ? ' checked="checked"' : '' }}}></input>
            @endif
            <label class="width1">{{{ $modules[$i]->name }}}</label>
            <label>{{{ Lang::get('admin/course/table.credit') }}}</label>
            <input type="text" class="width1" name="credit[]" id="credit" value="{{{ count($credits) > 0 ? $credits[$i] : '' }}}"/>
            <label class="width2">{{{ Lang::get('admin/course/table.min_credit_module') }}}</label>
            <input type="text" class="width1" name="min_credit_module[]" id="min_credit_module" value="{{{ count($min_credits) > 0 ? $min_credits[$i] : '' }}}" />
        </div>
        @endfor
    <!-- Form Actions -->
    <div class="form-group">
        <div class="controls">
            <button class="btn btn-default close_popup">{{{ Lang::get('general.cancel') }}}</button>
            <button id="btnAdd" type="submit" class="btn btn-success">{{{ Lang::get('general.add') }}}</button>
		</div>
    </div>
    <div id="show">
        <input type="hidden" name="chk" id="chk" value=""/>
    </div>
    <!-- ./ form actions -->
</form>

@stop

@section('styles')

<style>
    .rlbl{
        text-align:right;
        width:120px;
            }

    .lalign{
        text-align:left;
        width:120px;
    }

    .twidth{
        width:150px;
    }

    .width1{
        width:80px;
    }
    .width2{
        width:100px;
    }
    .col-md-03 {
        width: 3%;
    }
    .col-md-04 {
        width: 4.5%;
    }
    .col-md-05 {
        width: 5%;
    }
    .col-md-06 {
        width: 6%;
    }
    .col-md-07 {
        width: 7.2%;
    }
    .col-md-010 {
        width: 12%;
    }
</style>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
        $("#major_classification").change(function(){
            var sVal=$("#major_classification").val();
            $("#major").empty();
            $("#major").append("<option value='请选择'>请选择</option>");
            if (sVal=='请选择') {
                for (var i = 0; i < b_majors.length; i ++) {
                    $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                }
                for (var i = 0; i < z_majors.length; i ++) {
                    $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                }
            }
            if (sVal==12) {
                for (var i = 0; i < b_majors.length; i ++) {
                    $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                }
            }

            if (sVal==14) {
                for (var i = 0; i < z_majors.length; i ++) {
                    $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                }
            }
        });
        $(document).ready(function() {
            $("#btnAdd").click(function(){
                var ex = /^[1-9]\d{6}$/;
                var str = $("#code").val();
                if (!ex.test(str)) {
                    alert("请输入教学计划编号，只接受7位数字输入，且最高位从1开始");
                    $("#code").focus();
                    return false;
                }
                str = $("#student_classification").val();
                if (str=='请选择') {
                    alert("请选择学生类型");
                    $("#student_classification").focus();
                    return false;
                }
                str = $("#major_classification").val();
                if (str=='请选择') {
                    alert("请选择专业层次");
                    $("#major_classification").focus();
                    return false;
                }
                str = $("#major").val();
                if (str=='请选择') {
                    alert("请选择专业");
                    $("#major").focus();
                    return false;
                }
                ex = /^\d+(\.\d+)?$/;
                str = $("#min_credit_graduation").val();
                if (!ex.test(str)) {
                    alert("请输入毕业最低学分，只接受数字和.");
                    $("#min_credit_graduation").focus();
                    return false;
                }
                ex = /^[1-9]\d*$/;
                str = $("#max_credit_exemption").val();
                if (!ex.test(str)) {
                    alert("请输入免修免考最高学分，只接受数字且不能为0");
                    $("#max_credit_exemption").focus();
                    return false;
                }
                str = $("#max_credit_semester").val();
                if (!ex.test(str)) {
                    alert("请输入新选课最高学分，只接受数字且不能为0");
                    $("#max_credit_semester").focus();
                    return false;
                }
                var pass = true;
                $("#create_Form div").each(function(i) {
                    if (i > 4) {
                        var ck = $(this).find(":checkbox").is(':checked');
                        if (ck == true){
                            var obj = $(this).find("[name='credit[]']");
                            str = obj.val();

                            if (!ex.test(str)) {
                                alert("请输入学分，只接受数字");
                                obj.focus();
                                pass = false;
                                return false;
                            }
                            var credit = parseInt(str);
                            obj = $(this).find("[name='min_credit_module[]']");
                            str = obj.val();
                            if (!ex.test(str)) {
                                alert("请输入模块最低学分，只接受数字");
                                obj.focus();
                                pass = false;
                                return false;
                            }
                            var min_credit = parseInt(str);
                            if (min_credit > credit){
                                alert("模块最低学分不能大于模块学分");
                                obj.focus();
                                pass = false;
                                return false;
                            }
                        }
                    }
                });
                if (pass == false)
                    return false;
                $("#chk").val("");
                $("#create_Form input:checkbox").each(function(){
                    var checked = $(this).is(':checked');
                    var str = $("#chk").val();
                    if (checked == true) {
                        str = str + $(this).val() + ",";
                    }else {
                        str = str + "0,";
                    }
                    $("#chk").val(str);
                });
            });
        });
	</script>
@stop

