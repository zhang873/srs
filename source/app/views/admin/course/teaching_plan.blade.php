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
            教学计划查询条件
        </h3>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/course/title.fit_year') }}</label>
        <input class="twidth" tabindex="1" type="text" name="year" id="year">
        <label class="rlbl">{{ Lang::get('admin/course/title.fit_semester') }}</label>
        <select class="twidth" size="1" tabindex="2" name="semester" id="semester">
            <option value="全部" selected="selected">全部</option>
            <option value="1">春季</option>
            <option value="2">秋季</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/course/title.classification') }}</label>
        <select class="twidth" size="1" tabindex="3" name="major_classification" id="major_classification">
          <option value="全部" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/course/title.major') }}</label>
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
        <label class="rlbl" >{{ Lang::get('admin/course/table.teaching_plan_code') }}</label>
        <input class="twidth" tabindex="5" type="text" name="teaching_plan_code" id="teaching_plan_code">
        <label class="rlbl">{{ Lang::get('admin/course/table.is_activated') }}</label>
        <select class="twidth" size="1" tabindex="6" name="is_activated" id="is_activated">
            <option value="全部" selected="selected">全部</option>
            <option value="0">关</option>
            <option value="1">启</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/course/table.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="7" name="student_classification" id="student_classification">
            <option value="全部" selected="selected">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
        <label style="width:243px;"/>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.course_query') }}}</button>
        <a href="{{{ URL::to('admin/course/teaching_plan_add') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>
            {{{ Lang::get('admin/course/title.teaching_plan_add') }}}</a>
        <a href="{{{ URL::to('admin/course/teaching_plan/importExcel') }}}" class="btn btn-small btn-info iframe_s">
            {{{ Lang::get('admin/course/title.teaching_plan_import') }}}</a>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            课程信息
        </h3>
    </div>

	<table id="teaching_plan" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-03">{{{ Lang::get('admin/course/table.checked') }}}</th>
                <th class="col-md-05">{{{ Lang::get('admin/course/table.teaching_plan_code') }}}</th>
				<th class="col-md-05">{{{ Lang::get('admin/course/table.student_classification') }}}</th>
				<th class="col-md-05">{{{ Lang::get('admin/course/title.classification') }}}</th>
                <th class="col-md-06">{{{ Lang::get('admin/course/title.major') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.min_credit_graduation') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/course/table.schooling_period') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.max_credit_exemption') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.max_credit_semester') }}}</th>
                <th class="col-md-04">{{{ Lang::get('admin/course/table.is_activated') }}}</th>
                <th class="col-md-010">{{{ Lang::get('table.actions') }}}</th>

			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div align="center">
        <button id="btnSet" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/course/title.teaching_plan_set') }}}</button>
        <button id="btnUnset" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/course/title.teaching_plan_unset') }}}</button>

	</div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')

<style>
    .rlbl{
        text-align:right;
        width:90px;
    }
    .twidth{
        width:150px;
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
        $(".iframe").colorbox({iframe:true, width:"60%", height:"90%"});
        var oTable = null;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
		$(document).ready(function() {
            oTable = $('#teaching_plan').dataTable( {
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
                    "url": "{{ URL::to('admin/course/teaching_plan_data') }}",
                    "data": function ( d ) {
                        var fields = $('#teaching_plan :checked').serializeArray();
                        if (fields.length > 0)
                            d[fields[0].name]=Array();
                        fields.forEach(function (e) {
                            d[e.name].push(e.value);
                        });
                        d["teaching_plan_code"]= $('#teaching_plan_code').val();
                        d["year"]=$('#year').val();
                        d["semester"]= $('#semester').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["student_classification"]=$('#student_classification').val();
                        d["is_activated"]=$('#is_activated').val();
                        d["state"]=$('#btnValue').val();
                    }
                },
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe_m").colorbox({iframe:true, width:"60%", height:"70%"});
                    $(".iframe_s").colorbox({iframe:true, width:"40%", height:"40%"});
                },
                "aaSorting": [ [0,'asc'] ]
            });
            $("#btnQuery").click(function(){
                $("#btnValue").val(2);
                oTable.fnReloadAjax();
            });
            $("#btnSet").click(function(){
                if ($('#teaching_plan :checked').length <= 0){
                    alert("请选择教学计划！");
                    return false;
                }

                $("#btnValue").val(1);
                oTable.fnReloadAjax();
            });
            $("#btnUnset").click(function(){
                if ($('#teaching_plan :checked').length <= 0){
                    alert("请选择教学计划！");
                    return false;
                }
                $("#btnValue").val(0);
                oTable.fnReloadAjax();
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