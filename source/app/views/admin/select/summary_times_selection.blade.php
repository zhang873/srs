@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	<div class="page-header">
		<h3>
			{{{ $title }}}
		</h3>
	</div>


	<div class="form-group" align="center">
        <h3>
            请输入查询条件
        </h3>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.selection_year') }}</label>
        <select class="twidth" size="1" tabindex="1" name="year" id="year">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.selection_semester') }}</label>
        <select class="twidth" size="1" tabindex="2" name="semester" id="semester">
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
        <label class="rlbl">{{ Lang::get('admin/select/table.major') }}</label>
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
        <label class="rlbl" >{{ Lang::get('admin/select/table.course_code') }}</label>
        <input class="twidth" tabindex="5" type="text" name="course_code" id="course_code">
        <label class="rlbl">{{ Lang::get('admin/select/table.course_name') }}</label>
        <input class="twidth" tabindex="6" type="text" name="course_name" id="course_name">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.selection_status') }}</label>
        <select class="twidth" size="1" tabindex="7" name="selection_status" id="selection_status">
            <option value="全部" selected="selected">全部</option>
            <option value="1">首次</option>
            <option value="2">再次</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="8" name="student_classification" id="student_classification">
            <option value="全部" selected="selected">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query') }}}</button>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            学生选课统计情况
        </h3>
    </div>

	<table id="selection" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-03">{{{ Lang::get('admin/select/title.selection_year') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/title.selection_semester') }}}</th>
                <th class="col-md-01">{{{ Lang::get('admin/select/table.campus') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.year_in') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.semester_in') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/title.student_classification') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/title.major_classification') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.major') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_code') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.selection_number') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.total_credit') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_first') }}}</th>

			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')

<style>
div.DTTT .btn {
    color:#ffffff !important;
    font-size: 14px;
    margin-left: 20px;
}
div.DTTT{
    margin-left: .3em;
}
    .rlbl{
        text-align:right;
        width:80px;
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
        var oTable = null;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
		$(document).ready(function() {
            $("#btnQuery").click(function(){
                var ex = /^\d+$/;
                var str = $.trim($('#course_code').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("课程编号只接受数字");
                        $("#course_code").focus();
                        return false;
                    }
                    if (str.length > 5) {
                        alert("课程编号超过5位");
                        $("#course_code").focus();
                        return false;
                    }
                }
                ex = /^[\u4e00-\u9fa5\w()]+$/;
                str = $.trim($('#course_name').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("课程名字只能包括文字、数字、括号、下划线");
                        $("#course_name").focus();
                        return false;
                    }
                }
                if (oTable == null){
                    oTable = $('#selection').dataTable( {
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
                            "url": "{{ URL::to('admin/select/summary_times_selection_data') }}",
                            "data": function ( d ) {
                                d["year"]=$('#year').val();
                                d["semester"]= $('#semester').val();
                                d["major_classification"]= $('#major_classification').val()
                                d["major"]= $('#major').val()
                                d["course_code"]=$.trim($('#course_code').val());
                                d["course_name"]=$.trim($('#course_name').val());
                                d["selection_status"]=$('#selection_status').val();
                                d["student_classification"]=$('#student_classification').val();
                            }
                        },
                        "aaSorting": [ [0,'asc'] ]
                    });
                } else {
                    oTable.fnReloadAjax();
                }

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

		});

	</script>
@stop