@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div id="group_select_course">
	<div class="page-header">
		<h3>
			{{{ $title }}}
		</h3>
	</div>
	<div class="form-group" align="center">
            以下条件都选择，才能确定一个教学计划
    </div>

    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.student_year_in') }}</label>
        <select class="twidth" size="1" tabindex="1" name="year_in" id="year_in">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.student_semester_in') }}</label>
        <select class="twidth" size="1" tabindex="2" name="semester_in" id="semester_in">
            <option value="全部" selected="selected">全部</option>
            <option value="1">春季</option>
            <option value="2">秋季</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/title.major_classification') }}</label>
        <select class="twidth" size="1" tabindex="3" name="major_classification" id="major_classification">
            <option value="全部" selected="selected">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.campus_major') }}</label>
        <select class="twidth" size="1" tabindex="4" name="major" id="major">
            <option value="全部" selected="selected">全部</option>
            @foreach ($b_majors as $major)
                <option value="{{{ $major }}}"> {{{ $major }}} </option>
            @endforeach
            @foreach ($z_majors as $major)
                <option value="{{{ $major }}}"> {{{ $major }}} </option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query_course') }}}</button>
    </div>
    <br><br><br><br>


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
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_classification') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_remark') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<br>
	<div class="form-group" align="center">
            <button id="selClass" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.select_class') }}}</button>
    </div>
</div>

<br><br>

<div id="selected_course" style="display: none;">
    <div class="form-group" align="center">
            你选择了下列课程
    </div>
    <table id="sel_courses" class="table table-striped table-hover">
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.course_classification') }}}</th>
        </tr>
    </table>
    <div class="form-group" align="center">
        <button id="reselectCourse" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.reselect_course') }}}</button>
    </div>
</div>
<br><br>
<div id="group_select_class" style="display: none;">
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="5" name="student_classification" id="student_classification">
            <option value="12" selected="selected">本科</option>
            <option value="14">专科</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/table.year') }}</label>
        <select class="twidth" size="1" tabindex="6" name="year" id="year">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
        <button id="btnQueryClass" class="col-md-offset-1 btn btn-small btn-info" >
                {{{ Lang::get('admin/select/title.query_class') }}}</button>
    </div>
    <br><br>
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

<div id="selected_class" style="display: none;">
    <div class="form-group" align="center">
                你选择了下列管理班
    </div>
    <table id="sel_classes" class="table table-striped table-hover">
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_name') }}}</th>

        </tr>
    </table>
    <div class="form-group" align="center">
        <button id="reselectClass" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.reselect_class') }}}</button>
    </div>
</div>
<br><br>
<div id="group_select_student" style="display: none;">
    <div class="form-group" align="center">
    <label class="rlbl">{{ Lang::get('admin/select/title.campus_major') }}</label>
    <select class="twidth" size="1" tabindex="7" name="student_major" id="student_major">
      <option value="全部" selected="selected">全部</option>
      @foreach ($b_majors as $major)
          <option value="{{{ $major }}}"> {{{ $major }}} </option>
      @endforeach
      @foreach ($z_majors as $major)
          <option value="{{{ $major }}}"> {{{ $major }}} </option>
      @endforeach
    </select>
    <button id="btnQueryStudent" class="col-md-offset-1 btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query_student') }}}</button>
    </div>
    <br><br>
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
                <th class="col-md-01">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.student_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-01">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_turn') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_first') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_out') }}}</th>
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
        var tbCourses = null;
        var tbClass = null;
        var tbStudent = null;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
        var cur_year = "<?php echo $cur_year;?>";
        var cur_semester = "<?php echo $cur_semester;?>";
        var cur = cur_year.toString() + cur_semester.toString();
		$(document).ready(function() {
            $("#btnQuery").click(function(){
                var str = $("#year_in").val();
                if (str=='全部') {
                    alert("请选择学生入学年度");
                    $("#year_in").focus();
                    return false;
                }
                str = $("#semester_in").val();
                if (str=='全部') {
                    alert("请选择学生入学学期");
                    $("#semester_in").focus();
                    return false;
                }
                str = $("#major_classification").val();
                if (str=='全部') {
                    alert("请选择专业层次");
                    $("#major_classification").focus();
                    return false;
                }
                str = $("#major").val();
                if (str=='全部') {
                    alert("请选择教学点开设专业");
                    $("#major").focus();
                    return false;
                }
                if (tbCourses == null){
                    tbCourses = $('#courses').dataTable({
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
                            "url": "{{ URL::to('admin/select/group_selection_course_data') }}",
                            "data": function ( d ) {
                                d["year_in"]=$('#year_in').val();
                                d["semester_in"]= $('#semester_in').val();
                                d["major_classification"]= $('#major_classification').val();
                                d["major"]=$('#major').val();
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[
                            {"orderable":false,"targets":0}
                        ]
                    });
                } else{
                    tbCourses.fnReloadAjax();
                }
            });

            $("#selClass").click(function(){
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
                            $('#sel_courses').append("<tr><td>" + code + "</td><td>" + name + "</td><td>"
                                + credit + "</td><td>" + classification + "</td></tr>");

                        }
                    }
                });

                $('#group_select_course').hide();
                $('#selected_course').show();
                $('#group_select_class').show();
            });

            $('#reselectCourse').click(function(){
                $('#group_select_course').show();
                $('#selected_course').hide();
                $('#selected_class').hide();
                $('#group_select_class').hide();
                $('#group_select_student').hide();
            });

            $("#btnQueryClass").click(function(){

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
                            "url": "{{ URL::to('admin/select/group_selection_class_data') }}",
                            "data": function ( d ) {
                                d["year"]=$('#year').val();
                                d["student_classification"]= $('#student_classification').val();
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                } else{
                    tbClass.fnReloadAjax();
                }
            });

            $("#selStudent").click(function(){
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
                        var checkbox = $(this).find(":checkbox");
                        if (checkbox == null)
                            return;
                        var ck = checkbox.is(':checked');
                        if (ck == true){
                            var sysid = $(this).find("td").eq(1).html();
                            var name = $(this).find("td").eq(2).html();
                            $('#sel_classes').append("<tr><td>" + sysid + "</td><td>" + name + "</td></tr>");
                        }
                    }
                });

                $('#group_select_class').hide();
                $('#selected_class').show();
                $('#group_select_student').show();
                if (tbStudent != null){
                    tbStudent.fnReloadAjax();
                }
            });

            $('#reselectClass').click(function(){
                $('#group_select_class').show();
                $('#selected_class').hide();
                $('#group_select_student').hide();
            });

            $("#btnQueryStudent").click(function(){
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
                            "url": "{{ URL::to('admin/select/group_selection_student_data') }}",
                            "data": function ( d ) {
                                var fields = $('#classes :checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }
                                d["student_major"]=$('#student_major').val();
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                } else{
                    tbStudent.fnReloadAjax();
                }
            });

            $("#submitClass").click(function(){
                if ($('#classes :checked').length <= 0){
                    alert("请选择管理班！");
                    return false;
                }
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
                            var suggested_semester = parseInt($(this).find("#sm").val());
                            var is_obligatory = $(this).find("#ob").val();
                            var plan_year = parseInt($('#year_in').val());
                            var plan_semester = parseInt($('#semester_in').val());
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
                            var suggested_semester = parseInt($(this).find("#sm").val());
                            var is_obligatory = $(this).find("#ob").val();
                            var plan_year = parseInt($('#year_in').val());
                            var plan_semester = parseInt($('#semester_in').val());
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

            $("#major_classification").change(function(){
                var sVal=$("#major_classification").val();
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