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
    			查询条件输入区
    		</h3>
    </div>


    <div class="form-group" align="center">
        <label for="code" class="rlbl" >{{ Lang::get('admin/course/table.code') }}</label>
        <input class="twidth" tabindex="1" type="text" name="code" id="code">
    </div>
    <div class="form-group" align="center">
        <label for="name" class="rlbl">{{ Lang::get('admin/course/table.name') }}</label>
        <input class="twidth" tabindex="2" type="text" name="name" id="name">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/course/table.abbreviation') }}</label>
        <input class="twidth" tabindex="3" type="text" name="abbreviation" id="abbreviation">
    </div>
    <div class="form-group" align="center">
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
        <label class="rlbl">{{ Lang::get('admin/course/title.classification') }}</label>
        <select class="twidth" size="1" tabindex="5" name="major_classification" id="major_classification">
          <option value="全部" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.course_query') }}}</button>
        <a href="{{{ URL::to('admin/course/data_add') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>
            {{{ Lang::get('admin/course/title.course_add') }}}</a>
        <a href="{{{ URL::to('admin/course/course/importExcel') }}}" id="iframe_s" class="btn btn-small btn-info">
            {{{ Lang::get('admin/course/title.course_import') }}}</a>
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
                <th class="col-md-1">{{{ Lang::get('admin/course/table.code') }}}</th>
				<th class="col-md-1">{{{ Lang::get('admin/course/table.name') }}}</th>
				<th class="col-md-1">{{{ Lang::get('admin/course/table.abbreviation') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.credit') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.credit_hour') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.is_practice') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.lecturer') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.is_certification') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.define_date') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.remark') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.department_id') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.classification') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.state') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/course/table.checked') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div align="center">
        <button id="btnSet" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/course/title.course_set') }}}</button>
        <button id="btnUnset" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/course/title.course_unset') }}}</button>
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
    .twidth{
        width:150px;
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
            $('#courses')
                .on('xhr.dt', function ( e, settings, json, xhr ) {
                    var type = $('#btnValue').val();
                    if (type == 1)
                        alert("启用成功！");
                    else if (type == 0)
                        alert("停用成功！");
                } );
			$("#btnQuery").click(function(){
			    var ex = /^\d+$/;
                var str = $.trim($('#code').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("课程编号只接受数字");
                        $("#code").focus();
                        return false;
                    }
                    if (str.length > 5) {
                        alert("课程编号超过5位");
                        $("#code").focus();
                        return false;
                    }
                }
                ex = /^[\u4e00-\u9fa5\w()]+$/;
                str = $.trim($('#name').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("课程名字只能包括文字、数字、括号、下划线");
                        $("#name").focus();
                        return false;
                    }
                }
                str = $.trim($('#abbreviation').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("课程简称只能包括文字、数字、括号、下划线");
                        $("#abbreviation").focus();
                        return false;
                    }
                }
                $("#btnValue").val(2);
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
                            "url": "{{ URL::to('admin/course/data') }}",
                            "data": function ( d ) {
                                var fields = $('#courses :checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }
                                d["code"] = $.trim($('#code').val());
                                d["name"] = $.trim($('#name').val());
                                d["abbreviation"] = $.trim($('#abbreviation').val());
                                d["major"] = $('#major').val();
                                d["major_classification"]=$('#major_classification').val();
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
            $("#btnSet").click(function(){
                if ($('#courses :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }
                $("#btnValue").val(1);
                oTable.fnReloadAjax();
            });
            $("#btnUnset").click(function(){
                if ($('#courses :checked').length <= 0){
                    alert("请选择课程！");
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