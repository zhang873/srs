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
        <h4>
            请输入查询条件
        </h4>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.student_id_start') }}*</label>
        <input class="twidth" tabindex="1" type="text" name="student_id_start" id="student_id_start">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.student_id_end') }}*</label>
        <input class="twidth" tabindex="2" type="text" name="student_id_end" id="student_id_end">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.is_obligatory') }}</label>
        <select class="twidth" size="1" tabindex="3" name="is_obligatory" id="is_obligatory">
            <option value="1" selected="selected">必修</option>
            <option value="0">选修</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query') }}}</button>
    </div>

    <table id="selection" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.student_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/title.class_sysid') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.selection_confirm') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/select/table.is_obligatory') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.selection') }}}<br>
                    <a href="javascript:void(0)" id="selectAll">{{{ Lang::get('admin/select/table.selection_all') }}}</a>
                    <a href="javascript:void(0)" id="selectNone">{{{ Lang::get('admin/select/table.selection_none') }}}</a></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <br><br>
    <div class="form-group" align="center">
        <button id="delete" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/table.delete') }}}</button>
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
		$(document).ready(function() {
            $("#btnQuery").click(function(){
                var ex = /^[1-9]\d{12}$/;
                var str = $.trim($("#student_id_start").val());
                if (!ex.test(str)) {
                    alert("请输入起始学号（13位数字）");
                    $("#student_id_start").focus();
                    return false;
                }
                str = $.trim($("#student_id_end").val());
                if (!ex.test(str)) {
                    alert("请输入终止学号（13位数字）");
                    $("#student_id_end").focus();
                    return false;
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
                            "url": "{{ URL::to('admin/select/range_delete_selection_data') }}",
                            "data": function ( d ) {
                                d["student_id_start"]= $.trim($("#student_id_start").val());
                                d["student_id_end"]=$.trim($("#student_id_end").val());
                                d["is_obligatory"]=$('#is_obligatory').val();
                                var fields = $('#selection :checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }
                            }
                        },
                        "aaSorting": [ [1,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":7}]
                    });
                } else {
                    oTable.fnReloadAjax();
                }

            });
            $("#selectAll").click(function(){
                $("#selection input:checkbox").each(function(){
                    $(this).prop("checked",true);
                });
            });
            $("#selectNone").click(function(){
                $("#selection input:checkbox").each(function(){
                    $(this).prop("checked",false);
                });
            });
            $("#delete").click(function(){
                if ($('#selection :checked').length <= 0){
                    alert("请选择选课记录！");
                    return false;
                }
                if (oTable != null)
                    oTable.fnReloadAjax();
            });
		});

	</script>
@stop