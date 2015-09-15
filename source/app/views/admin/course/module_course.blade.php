@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')
    <div class="pull-right">
        <a href="{{{ URL::to('admin/course/teaching_plan') }}}" class="btn btn-default btn-small btn-inverse">
            <span class="glyphicon glyphicon-circle-arrow-left"></span> {{{ Lang::get('general.back') }}}</a>
    </div>
    <br><br>
    <table id="courses" class="table table-striped table-hover">
        <tr>
            <th class="col-md-20">{{{ Lang::get('admin/course/table.teaching_plan_code') }}}</th>
            <th class="col-md-20">{{{ Lang::get('admin/course/title.classification') }}}</th>
            <th class="col-md-20">{{{ Lang::get('admin/course/title.major') }}}</th>
            <th class="col-md-20">{{{ Lang::get('admin/course/table.min_credit_graduation') }}}</th>
            <th class="col-md-20">{{{ Lang::get('admin/course/table.schooling_period') }}}</th>
        </tr>
        <tr>
            <td>{{{ $rst->code }}}</td>
            <td>{{{ $rst->major_classification == 12 ? '本科':'专科'}}}</td>
            <td>{{{ $rst->major }}}</td>
            <td>{{{ $rst->min_credit_graduation }}}</td>
            <td>{{{ $rst->schooling_period }}}</td>
        </tr>
    </table>
    <br>
    <div class="form-group" align="center">
        <a id="btnAdd" href="{{{ URL::to('admin/course/' . $id . '/module_course_add' ) }}}" class="btn btn-small btn-info iframe">
            {{{ Lang::get('admin/course/title.add') }}}</a>
        <a href="{{{ URL::to('admin/course/module/importExcel') }}}" class="btn btn-small btn-info iframe_s">
            {{{ Lang::get('admin/course/title.module_import') }}}</a>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            {{{ $rst->code }}}对应教学计划，模块下已定义课程
        </h3>
    </div>

	<table id="modules" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-12">{{{ Lang::get('admin/course/table.checked') }}}</th>
                <th class="col-md-12">{{{ Lang::get('admin/course/table.module_name') }}}</th>
				<th class="col-md-12">{{{ Lang::get('admin/course/table.code') }}}</th>
				<th class="col-md-16">{{{ Lang::get('admin/course/table.name') }}}</th>
                <th class="col-md-12">{{{ Lang::get('admin/course/table.is_obligatory') }}}</th>
                <th class="col-md-12">{{{ Lang::get('admin/course/table.credit') }}}</th>
                <th class="col-md-12">{{{ Lang::get('admin/course/table.suggested_semester') }}}</th>
                <th class="col-md-12">{{{ Lang::get('admin/course/table.is_masked') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div align="center">
        <button id="btnSet" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.module_set') }}}</button>
        <button id="btnUnset" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.module_unset') }}}</button>
        <button id="btnDelete" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.module_course_delete') }}}</button>
	</div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
    <br><br>
@stop

@section('styles')

<style>
    .col-md-12 {
        width: 12%;
    }
    .col-md-16 {
        width: 16%;
    }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
    $(".iframe_m").colorbox({iframe:true, width:"60%", height:"70%"});
    $(".iframe_s").colorbox({iframe:true, width:"40%", height:"50%"});
    var oTable;
    var teachingPlanId = "<?php echo $id;?>";
    $(document).ready(function() {
        oTable = $('#modules').dataTable( {
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
                "url": "{{ URL::to('admin/course/module_data_add') }}",
                "data": function ( d ) {
                    var fields = $('#modules :checked').serializeArray();
                    if (fields.length > 0)
                        d[fields[0].name]=Array();
                    fields.forEach(function (e) {
                        d[e.name].push(e.value);
                    });
                    d["teaching_plan_id"] = teachingPlanId;
                    d["state"] = $('#btnValue').val();
                }
            },
            "aaSorting": [ [0,'asc'] ]
        });


        $("#btnQuery").click(function(){
            $("#btnValue").val(2);
            oTable.fnReloadAjax();
        });
        $("#btnSet").click(function(){
            if ($('#modules :checked').length <= 0){
                alert("请选择模块！");
                return false;
            }
            $("#btnValue").val(0);
            oTable.fnReloadAjax();
        });
        $("#btnUnset").click(function(){
            if ($('#modules :checked').length <= 0){
                alert("请选择模块！");
                return false;
            }
            $("#btnValue").val(1);
            oTable.fnReloadAjax();
        });
        $("#btnDelete").click(function(){
            if ($('#modules :checked').length <= 0){
                alert("请选择模块！");
                return false;
            }
            $("#btnValue").val(3);
            oTable.fnReloadAjax();
        });
    });
</script>
@stop

