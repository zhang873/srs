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
            输入年度学期
        </h3>
    </div>
    <div class="form-group" align="center">
        <label for="year" class="rlbl" >{{ Lang::get('admin/course/title.year') }}</label>
        <input tabindex="1" type="text" name="year" id="year" style="width:200px;">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/course/title.semester') }}</label>
        <select size="1" tabindex="2" name="semester" id="semester" style="width:200px;">
          <option value="1" selected="selected">春季</option>
          <option value="2">秋季</option>
        </select>
    </div>
    <br><br>
	<div class="form-group" align="center">
        <h3>
            查询条件输入区
        </h3>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/course/title.major') }}</label>
        <select size="1" tabindex="3" name="major" id="major" style="width:200px;">
          <option value="全部" selected="selected">全部</option>
          @foreach ($b_majors as $major)
              <option value="{{{ $major }}}"> {{{ $major }}} </option>
          @endforeach
          @foreach ($z_majors as $major)
              <option value="{{{ $major }}}"> {{{ $major }}} </option>
          @endforeach
        </select>
        <label class="rlbl">{{ Lang::get('admin/course/title.classification') }}</label>
        <select size="1" tabindex="4" name="major_classification" id="major_classification" style="width:200px;">
          <option value="全部" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/course/table.code') }}</label>
        <input tabindex="5" type="text" name="code" id="code" style="width:200px;">
        <label class="rlbl">{{ Lang::get('admin/course/table.student_classification') }}</label>
        <select size="1" tabindex="6" name="student_classification" id="student_classification" style="width:200px;">
          <option value="全部" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.course_query') }}}</button>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            课程信息
        </h3>
    </div>

	<table id="courses" class="table table-striped table-hover">
		<thead>
			<tr>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.code') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.name') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.abbreviation') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.credit') }}}</th>
                <th class="col-md-05">{{{ Lang::get('admin/course/table.credit_hour') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.student_classification') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.is_practice') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.lecturer') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.is_certification') }}}</th>
                <th class="col-md-07">{{{ Lang::get('admin/course/table.define_date') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.remark') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.department_id') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.classification') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/table.state') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.checked') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div align="center">
        <input type="checkbox" name="checkAll" id="checkAll">{{{ Lang::get('admin/course/title.check_all') }}}</input>&nbsp;&nbsp;
        <button id="btnAdd" class="btn btn-small btn-info">{{{ Lang::get('admin/course/title.add') }}}</button>
	</div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')

<style>

    .rlbl{
        text-align:right;
        width:80px;

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

</style>
@stop



{{-- Scripts --}}
@section('scripts')

	<script type="text/javascript">
        $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
        $("#iframe_s").colorbox({iframe:true, width:"40%", height:"40%"});
        var oTable;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
		$(document).ready(function() {

            $("#btnQuery").click(function(){
                $("#btnValue").val(2);
                $("#checkAll").prop("checked",false);
                if (oTable == null){
                    oTable = $('#courses').dataTable( {
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
                            "url": "{{ URL::to('admin/course/establish_data') }}",
                            "data": function ( d ) {
                                var fields = $('#courses :checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }
                                d["code"]= $('#code').val();
                                d["year"]=$('#year').val();
                                d["semester"]= $('#semester').val();
                                d["major"]=$('#major').val();
                                d["major_classification"]=$('#major_classification').val();
                                d["student_classification"]=$('#student_classification').val();
                                d["state"]=$('#btnValue').val();
                            }
                        },
                        "aaSorting": [ [0,'asc'] ]
                    });
                }
                else {
                    oTable.fnReloadAjax();
                }

            });
            $("#btnAdd").click(function(){
                if ($('#courses :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }
                var str = $("#year").val();
                var ex = /^\d{4}$/;
                if (!ex.test(str)) {
                    alert("请输入年度（4位数字）");
                    $("#year").focus();
                    return false;
                }
                $("#btnValue").val(1);
                $("#checkAll").prop("checked",false);
                oTable.fnReloadAjax();
            });

            $("#checkAll").click(function(){
                var val = $("#checkAll").is(':checked');
                $("#courses input:checkbox").each(function(){
                    $(this).prop("checked",val);
                });
            });


            $("#major_classification").change(function(){
                var sVal=$("#major_classification").val();
                $("#major").empty();
                $("#major").append("<option value='全部'>全部</option>");
                if (sVal=='全部')
                {
                    for (var i = 0; i < b_majors.length; i ++) {
                        $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                    }
                    for (var i = 0; i < z_majors.length; i ++) {
                        $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                    }
                }
                if (sVal==12)
                {
                    for (var i = 0; i < b_majors.length; i ++) {
                        $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                    }
                }

                if (sVal==14)
                {
                    for (var i = 0; i < z_majors.length; i ++) {
                        $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                    }
                }

            });

		});

	</script>
@stop