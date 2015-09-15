@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="create_Form"  method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    
        <!--Name -->
        <div class="form-group" align="center">
            <label class="rlbl">{{{ Lang::get('admin/course/table.module_name') }}}</label>
            <select class="twidth" size="1" tabindex="1" name="module_id" id="module_id">
                <option value="" selected="selected">请选择</option>
                @foreach ($moduleInfos as $moduleInfo)
                    <option value="{{{ $moduleInfo->mid }}}"{{{ $moduleInfo->mid == Input::old('module_id') ? ' selected="selected"' : '' }}}>
                    {{{ $moduleInfo->name }}} </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" align="center">
            <label class="rlbl" >{{ Lang::get('admin/course/table.code') }}</label>
            <input tabindex="2" type="text" class="twidth" name="course_code" id="course_code" value="{{ Input::old('course_code') }}">
        </div>

        <div class="form-group" align="center">
            <label class="rlbl">{{ Lang::get('admin/course/table.name') }}</label>
            <input tabindex="3" type="text" class="twidth" name="course_name" id="course_name"
                readonly="true" style="border:0px;background-color:#d3d3d3" value="{{ Input::old('course_name') }}">
        </div>

        <div class="form-group" align="center">
            <label class="rlbl">{{ Lang::get('admin/course/table.is_obligatory') }}</label>
            <select class="twidth" size="1" tabindex="4" name="is_obligatory" id="is_obligatory">
            @if (Input::old('is_obligatory') == null)
                <option value="1" selected="selected">必修</option>
                <option value="0">选修</option>
            @else
                <option value="1" {{{ Input::old('is_obligatory') == 1 ? ' selected="selected"' : '' }}}>必修</option>
                <option value="0" {{{ Input::old('is_obligatory') == 0 ? ' selected="selected"' : '' }}}>选修</option>
            @endif
            </select>
        </div>

        <div class="form-group" align="center">
            <label class="rlbl">{{ Lang::get('admin/course/table.credit') }}</label>
            <input tabindex="5" type="text" class="twidth" name="course_credit" id="course_credit"
                readonly="true" style="border:0px;background-color:#d3d3d3" value="{{ Input::old('course_credit') }}">
        </div>

        <div class="form-group" align="center">
            <label class="rlbl">{{ Lang::get('admin/course/table.suggested_semester') }}</label>
            <input tabindex="6" type="text" class="twidth" name="suggested_semester" id="suggested_semester" value="{{ Input::old('suggested_semester') }}">
        </div>

        <div class="form-group" align="center">
            <label class="rlbl">{{ Lang::get('admin/course/table.is_masked') }}</label>
            <select class="twidth" size="1" tabindex="7" name="is_masked" id="is_masked">
            @if (Input::old('is_masked') == null)
                <option value="1">是</option>
                <option value="0" selected="selected">否</option>
            @else
                <option value="1" {{{ Input::old('is_masked') == 1 ? ' selected="selected"' : '' }}}>是</option>
                <option value="0" {{{ Input::old('is_masked') == 0 ? ' selected="selected"' : '' }}}>否</option>
            @endif
            </select>
        </div>

    <div id="show">

    </div>

    <!-- Form Actions -->
    <div class="form-group">
        <div class="controls">
            <button class="btn-cancel close_popup">{{{ Lang::get('admin/depart/table.cancel') }}}</button>
            <button id="btnAdd" type="submit" class="btn btn-success">{{{
				Lang::get('admin/depart/table.ok') }}}</button>
		</div>
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
</style>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){

        $("#course_code").keyup(function(){
            $("#course_credit").val('');
            $("#course_name").val('');
            var str = $("#course_code").val();
            var ex = /^\d{5}$/;
            if (!ex.test(str)) {
                return;
            }
            var jsonData = {
                "course_code": $("#course_code").val()
            };
            $.ajax({
                type: 'GET',
                url: '{{ URL::to('admin/course/query_course') }}',
                data: jsonData,
                success: function (json) {
                    if (json != 'err') {
                        var infos = json.split("|");
                        $("#course_credit").val(infos[0]);
                        $("#course_name").val(infos[1]);
                    }
                }
            });
        });

        $("#btnAdd").click(function(){
            var ex = /^[1-9]\d{4}$/;
            var str = $("#module_id").val();
            if (str=='') {
                alert("请选择模块");
                $("#module_id").focus();
                return false;
            }

            str = $("#course_code").val();
            if (!ex.test(str)) {
                alert("请输入课程编号，只接受5位数字输入，且最高位从1开始");
                $("#course_id").focus();
                return false;
            }

            str = $("#course_name").val();
            if (str=='') {
                alert("请输入课程名称");
                $("#course_name").focus();
                return false;
            }
            str = $("#course_credit").val();
            if (str=='') {
                alert("请输入学分");
                $("#course_credit").focus();
                return false;
            }

            str = $("#suggested_semester").val();
            if (str=='') {
                alert("请输入建议开设学期");
                $("#suggested_semester").focus();
                return false;
            }

        });
    });
</script>
@stop

