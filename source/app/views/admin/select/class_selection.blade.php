@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div id="class_select_class">
	<div class="page-header">
		<h3>
			{{{ $title }}}
		</h3>
	</div>
	<div class="form-group" align="center">
            请输入查询条件
    </div>

    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="1" name="student_classification" id="student_classification">
            <option value="全部" selected="selected">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
        <label class="rlbl" >{{ Lang::get('admin/select/table.year') }}</label>
        <select class="twidth" size="1" tabindex="2" name="year" id="year">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.major') }}</label>
        <select class="twidth" size="1" tabindex="3" name="major" id="major">
          <option value="全部" selected="selected">全部</option>
          @foreach ($b_majors as $major)
              <option value="{{{ $major }}}"> {{{ $major }}} </option>
          @endforeach
          @foreach ($z_majors as $major)
              <option value="{{{ $major }}}"> {{{ $major }}} </option>
          @endforeach
        </select>
        <label class="rlbl" >{{ Lang::get('admin/select/title.class_sysid') }}</label>
        <input class="twidth" tabindex="4" type="text" name="class_sysid" id="class_sysid">
    </div>

    <div class="form-group" align="center">
            <button id="btnQueryClass" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.query_class') }}}</button>
    </div>
    <br><br><br><br>


    <div class="form-group" align="center">
        <h3>
            请选择管理班
        </h3>
    </div>
	<table id="classes" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-03">{{{ Lang::get('admin/select/table.selection') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.teaching_plan_code') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<br>
	<div class="form-group" align="center">
            <button id="btnQueryCourse" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.query_course') }}}</button>
    </div>
</div>

<br><br>

<div id="selected_class" style="display: none;">
    <div class="form-group" align="center">
            你选择了此管理班
    </div>
    <table id="sel_classes" class="table table-striped table-hover">
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.teaching_plan_code') }}}</th>
        </tr>
    </table>
    <div class="form-group" align="center">
        <button id="reselectClass" class="btn btn-small btn-info">
            {{{ Lang::get('admin/select/title.reselect_class') }}}</button>
    </div>
</div>
<br><br>
<div id="class_select_course" style="display: none;">
    <div class="form-group" align="center">
        <h3>
            请选择课程
        </h3>
    </div>
	<table id="courses" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-03">{{{ Lang::get('admin/select/table.selection') }}}<br>
                    <a href="javascript:void(0)" id="selectAll">{{{ Lang::get('admin/select/table.selection_all') }}}</a><br>
                    <a href="javascript:void(0)" id="selectNone">{{{ Lang::get('admin/select/table.selection_none') }}}</a></th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_code') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_classification') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.suggested_semester') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_remark') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<br>
	<div class="form-group" align="center">
            <button id="selStudent" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.select_student') }}}</button>
            <button id="submitClass" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.class_submit') }}}</button>
    </div>

</div>

<div id="selected_course" style="display: none;">
    <div class="form-group" align="center">
                你选择了下列课程
    </div>
    <table id="sel_courses" class="table table-striped table-hover">
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_code') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_classification') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.suggested_semester') }}}</th>
        </tr>
    </table>
    <div class="form-group" align="center">
        <button id="reselectCourse" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.reselect_course') }}}</button>
    </div>
</div>
<br><br>
<div id="class_select_student" style="display: none;">
    <div class="form-group" align="center">
        <h3>
            请选择选课的学生
        </h3>
    </div>
	<table id="students" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-03">{{{ Lang::get('admin/select/table.selection') }}}<br>
                    <a href="javascript:void(0)" id="selectAllStudent">{{{ Lang::get('admin/select/table.selection_all') }}}</a><br>
                    <a href="javascript:void(0)" id="selectNoneStudent">{{{ Lang::get('admin/select/table.selection_none') }}}</a></th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.student_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.course_code') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.is_turn') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.is_first') }}}</th>

			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<br>
	<div class="form-group" align="center">
            <button id="submitStudent" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.student_submit') }}}</button>
    </div>
</div>

<div id="show">

</div>
@stop

@section('styles')

<style>
    .rlbl{
        text-align:right;
        width:120px;

    }
    .twidth{
        width:150px;
    }
    .col-md-01 {
        width: 10%;
    }
    .col-md-02 {
        width: 8%;
    }
    .col-md-03 {
        width: 5%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        var tbCourse = null;
        var tbClass = null;
        var tbStudent = null;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
        var cur_year = "<?php echo $cur_year;?>";
        var cur_semester = "<?php echo $cur_semester;?>";
        var cur = cur_year.toString() + cur_semester.toString();
        var teaching_plan_code;
		$(document).ready(function() {
            $("#btnQueryClass").click(function(){
                var str;
                str = $("#student_classification").val();
                if (str=='全部') {
                    alert("请选择学生类别");
                    $("#student_classification").focus();
                    return false;
                }
                str = $("#year").val();
                if (str=='全部') {
                    alert("请选择年度");
                    $("#year").focus();
                    return false;
                }

                str = $("#major").val();
                if (str=='全部') {
                    alert("请选择专业");
                    $("#major").focus();
                    return false;
                }
                str = $.trim($('#class_sysid').val());
                if (str=='') {
                    alert("请输入班代码");
                    $("#class_sysid").focus();
                    return false;
                }
                var ex = /^\d+$/;

                if (str != ''){
                    if (!ex.test(str)) {
                        alert("班号只接受数字");
                        $("#class_sysid").focus();
                        return false;
                    }
                }

                if (tbClass == null){
                    tbClass = $('#classes').dataTable({
                        "searching":false,
                        "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                            "sLengthMenu": "{{{ Lang::get('admin/course/table.records_per_page') }}} _MENU_",
                            "sInfo": "{{{ Lang::get('admin/course/table.records_sInfo') }}}",
                            "oPaginate": {
                                "sFirst": "{{{ Lang::get('admin/course/table.records_sFirst') }}}",
                                "sPrevious": "{{{ Lang::get('admin/course/table.records_sPrevious') }}}",
                                "sNext": "{{{ Lang::get('admin/course/table.records_sNext') }}}",
                                "sLast": "{{{ Lang::get('admin/course/table.records_sLast') }}}"
                            },
                            "sZeroRecords": "{{{ Lang::get('admin/course/table.records_sZeroRecords') }}}",
                            "sInfoEmpty": "{{{ Lang::get('admin/course/table.records_sInfoEmpty') }}}",
                            "sProcessing": "{{{ Lang::get('admin/course/table.records_processing') }}}"

                        },
                        "bFilter": true,
                        "bProcessing": true,
                        "bServerSide": true,
                        "ajax": {
                            "url": "{{ URL::to('admin/select/class_selection_class_data') }}",
                            "data": function ( d ) {
                                d["student_classification"]= $('#student_classification').val();
                                d["year"]=$('#year').val();
                                d["major"]=$('#major').val();
                                d["class_sysid"]=$.trim($('#class_sysid').val());
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                } else{
                    tbClass.fnReloadAjax();
                }
            });

            $("#btnQueryCourse").click(function(){
                if ($('#classes :checked').length <= 0){
                    alert("请选择管理班！");
                    return false;
                }
                $("#sel_classes tr").each(function(i) {
                    if (i > 0) {
                        $(this).remove();
                    }
                });

                $("#classes tr").each(function(i) {
                    if (i > 0) {
                        var radio = $(this).find(":radio");
                        if (radio == null)
                            return;
                        var ck = radio.is(':checked');
                        if (ck == true){
                            var sysid = $(this).find("td").eq(1).html();
                            var name = $(this).find("td").eq(2).html();
                            teaching_plan_code = $(this).find("td").eq(3).html();

                            $('#sel_classes').append("<tr><td>" + sysid + "</td><td>" + name + "</td><td>"
                                + teaching_plan_code + "</td></tr>");

                        }
                    }
                });


                if (tbCourse == null){
                    tbCourse = $('#courses').dataTable({
                        "searching":false,
                        "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                            "sLengthMenu": "{{{ Lang::get('admin/course/table.records_per_page') }}} _MENU_",
                            "sInfo": "{{{ Lang::get('admin/course/table.records_sInfo') }}}",
                            "oPaginate": {
                                "sFirst": "{{{ Lang::get('admin/course/table.records_sFirst') }}}",
                                "sPrevious": "{{{ Lang::get('admin/course/table.records_sPrevious') }}}",
                                "sNext": "{{{ Lang::get('admin/course/table.records_sNext') }}}",
                                "sLast": "{{{ Lang::get('admin/course/table.records_sLast') }}}"
                            },
                            "sZeroRecords": "{{{ Lang::get('admin/course/table.records_sZeroRecords') }}}",
                            "sInfoEmpty": "{{{ Lang::get('admin/course/table.records_sInfoEmpty') }}}",
                            "sProcessing": "{{{ Lang::get('admin/course/table.records_processing') }}}"

                        },
                        "bFilter": true,
                        "bProcessing": true,
                        "bServerSide": true,
                        "ajax": {
                            "url": "{{ URL::to('admin/select/class_selection_course_data') }}",
                            "data": function ( d ) {
                                d["year"]=$('#year').val();
                                d["teaching_plan_code"]= teaching_plan_code;
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                } else{
                    tbCourse.fnReloadAjax();
                }

                $('#class_select_class').hide();
                $('#selected_class').show();
                $('#class_select_course').show();
            });

            $('#reselectClass').click(function(){
                $('#class_select_class').show();
                $('#selected_course').hide();
                $('#selected_class').hide();
                $('#class_select_course').hide();
                $('#class_select_student').hide();
            });

            $("#selStudent").click(function(){
                if ($('#courses :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }
                $("#sel_courses tr").each(function(i) {
                    if (i > 0) {
                        $(this).remove();
                    }
                });

                $("#courses tr").each(function(i) {
                    if (i > 0) {
                        var checkbox = $(this).find(":checkbox");
                        if (checkbox == null)
                            return;
                        var ck = checkbox.is(':checked');
                        if (ck == true){
                            var code = $(this).find("td").eq(1).html();
                            var name = $(this).find("td").eq(2).html();
                            var credit = $(this).find("td").eq(3).html();
                            var classification = $(this).find("td").eq(4).html();
                            var suggested_semester = $(this).find("td").eq(5).html();
                            $('#sel_courses').append("<tr><td>" + code + "</td><td>" + name + "</td><td>"
                                + credit + "</td><td>" + classification + "</td><td>" + suggested_semester + "</td></tr>");
                        }
                    }
                });

                if (tbStudent == null){
                    tbStudent = $('#students').dataTable({
                        "searching":false,
                        "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                            "sLengthMenu": "{{{ Lang::get('admin/course/table.records_per_page') }}} _MENU_",
                            "sInfo": "{{{ Lang::get('admin/course/table.records_sInfo') }}}",
                            "oPaginate": {
                                "sFirst": "{{{ Lang::get('admin/course/table.records_sFirst') }}}",
                                "sPrevious": "{{{ Lang::get('admin/course/table.records_sPrevious') }}}",
                                "sNext": "{{{ Lang::get('admin/course/table.records_sNext') }}}",
                                "sLast": "{{{ Lang::get('admin/course/table.records_sLast') }}}"
                            },
                            "sZeroRecords": "{{{ Lang::get('admin/course/table.records_sZeroRecords') }}}",
                            "sInfoEmpty": "{{{ Lang::get('admin/course/table.records_sInfoEmpty') }}}",
                            "sProcessing": "{{{ Lang::get('admin/course/table.records_processing') }}}"

                        },
                        "bFilter": true,
                        "bProcessing": true,
                        "bServerSide": true,
                        "ajax": {
                            "url": "{{ URL::to('admin/select/class_selection_student_data') }}",
                            "data": function ( d ) {
                                var fields = $('#classes :checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }

                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                } else {
                    tbStudent.fnReloadAjax();
                }
                $('#class_select_course').hide();
                $('#selected_course').show();
                $('#class_select_student').show();
            });

            $('#reselectCourse').click(function(){
                $('#class_select_course').show();
                $('#selected_course').hide();
                $('#class_select_student').hide();
            });

            $("#submitClass").click(function(){

                if ($('#courses :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }
                var teaching_plan_semester = 0;
                var teaching_plan_year = parseInt($('#year').val());
                $("#classes tr").each(function(i) {
                    if (i > 0) {
                        var radio = $(this).find(":radio");
                        if (radio == null)
                            return;
                        var ck = radio.is(':checked');
                        if (ck == true){
                            teaching_plan_semester = parseInt($(this).find("#sm").val());
                        }
                    }
                });
                if (teaching_plan_semester == 0)
                    return false;
                var courses = new Array();
                var obligatory = new Array();
                $("#courses tr").each(function(i) {
                    if (i > 0) {
                        var checkbox = $(this).find(":checkbox");
                        if (checkbox == null)
                            return;
                        var ck = checkbox.is(':checked');
                        if (ck == true){
                            var course_name = $(this).find("td").eq(2).html();
                            var suggested_semester = parseInt($(this).find("td").eq(5).html());
                            var is_obligatory = $(this).find("#ob").val();
                            var plan_year = teaching_plan_year;
                            var plan_semester = teaching_plan_semester;
                            var plan;
                            var aa = suggested_semester - 1;
                            if (aa > 0){
                                if (aa % 2  == 0){
                                    plan_year = plan_year + aa / 2;
                                }
                                else {
                                    plan_year = plan_year + (aa - 1) / 2;
                                    plan_semester++;
                                    if (plan_semester > 2){
                                        plan_semester = 1;
                                        plan_year++;
                                    }
                                }
                            }

                            var canPush = true;
                            var needConfirm = false;
                            plan = plan_year.toString() + plan_semester.toString();

                            if (cur < plan){
                                needConfirm = true;
                            }
                            if (needConfirm){
                                canPush = confirm(course_name + '已提前选课，确定要提交?');
                            }
                            if (canPush){
                                courses.push(checkbox.val());
                                obligatory.push(is_obligatory);
                            }
                        }
                    }
                });

                if (courses.length <= 0){
                    return false;
                }

                var fields = $('#classes :checked').serializeArray();
                var classes = new Array();
                if (fields.length > 0) {
                    $.each(fields, function(n, value){
                        var pos = $.inArray(value.value, classes);
                        if (pos == -1){
                            classes.push(value.value);
                        }
                    });
                }

                var jsonData = {
                    "classes[]": classes,
                    "courses[]": courses,
                    "obligatory[]": obligatory
                };

                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/select/group_submit_class') }}',
                    async: false,
                    data: jsonData,
                    success: function (json) {
                        if (json == 'ok'){
                            alert('选课成功');
                            if (tbStudent != null){
                                tbStudent.fnReloadAjax();
                            }
                        }
                    }
                });

            });

            $("#submitStudent").click(function(){
                if ($('#students input:checkbox:checked').length <= 0){
                    alert("请选择学生！");
                    return false;
                }
                var teaching_plan_semester = 0;
                var teaching_plan_year = parseInt($('#year').val());
                $("#classes tr").each(function(i) {
                    if (i > 0) {
                        var radio = $(this).find(":radio");
                        if (radio == null)
                            return;
                        var ck = radio.is(':checked');
                        if (ck == true){
                            teaching_plan_semester = parseInt($(this).find("#sm").val());
                        }
                    }
                });
                if (teaching_plan_semester == 0)
                    return false;
                var courses = new Array();
                var obligatory = new Array();
                $("#courses tr").each(function(i) {
                    if (i > 0) {
                        var checkbox = $(this).find(":checkbox");
                        if (checkbox == null)
                            return;
                        var ck = checkbox.is(':checked');

                        if (ck == true){
                            var course_name = $(this).find("td").eq(2).html();
                            var suggested_semester = parseInt($(this).find("td").eq(5).html());
                            var is_obligatory = $(this).find("#ob").val();
                            var plan_year = teaching_plan_year;
                            var plan_semester = teaching_plan_semester;
                            var plan;
                            var aa = suggested_semester - 1;
                            if (aa > 0){
                                if (aa % 2  == 0){
                                    plan_year = plan_year + aa / 2;
                                }
                                else {
                                    plan_year = plan_year + (aa - 1) / 2;
                                    plan_semester++;
                                    if (plan_semester > 2){
                                        plan_semester = 1;
                                        plan_year++;
                                    }
                                }
                            }
                            var canPush = true;
                            var needConfirm = false;
                            plan = plan_year.toString() + plan_semester.toString();
                            if (cur < plan){
                                needConfirm = true;
                            }
                            if (needConfirm){
                                canPush = confirm(course_name + '已提前选课，确定要提交?');
                            }
                            if (canPush){
                                courses.push(checkbox.val());
                                obligatory.push(is_obligatory);
                            }
                        }
                    }
                });

                if (courses.length <= 0){
                    return false;
                }

                var fields = $('#students :checked').serializeArray();
                var students = new Array();
                if (fields.length > 0) {
                    $.each(fields, function(n, value){
                        var pos = $.inArray(value.value, students);
                        if (pos == -1){
                            students.push(value.value);
                        }
                    });
                }

                var jsonData = {
                    "students[]": students,
                    "courses[]": courses,
                    "obligatory[]": obligatory
                };

                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/select/group_submit_student') }}',
                    async: false,
                    data: jsonData,
                    success: function (json) {
                        if (json == 'ok'){
                            alert('选课成功');
                            if (tbStudent != null){
                                tbStudent.fnReloadAjax();
                            }
                        }
                    }
                });

            });

            $("#student_classification").change(function(){
                var sVal=$("#student_classification").val();
                $("#major").empty();
                $("#major").append("<option value='全部'>全部</option>");
                if (sVal=="全部") {
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
            $("#selectAll").click(function(){
                $("#courses input:checkbox").each(function(){
                    $(this).prop("checked",true);
                });
            });
            $("#selectNone").click(function(){
                $("#courses input:checkbox").each(function(){
                    $(this).prop("checked",false);
                });
            });
            $("#selectAllStudent").click(function(){
                $("#students input:checkbox").each(function(){
                    $(this).prop("checked",true);
                });
            });
            $("#selectNoneStudent").click(function(){
                $("#students input:checkbox").each(function(){
                    $(this).prop("checked",false);
                });
            });
		});

	</script>
@stop