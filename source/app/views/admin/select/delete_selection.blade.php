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
        <label class="rlbl" >{{ Lang::get('admin/select/table.student_id') }}</label>
        <input class="twidth" tabindex="1" type="text" name="student_id" id="student_id">
        <button id="btnQuery" class="col-md-offset-1 btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query') }}}</button>
    </div>
    <br><br>
	<table id="students" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-2">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.student_name') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/select/table.mclass_num') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
    <br><br><br><br>
    <div class="form-group" align="center">
        <h4>
            已选课程
        </h4>
    </div>
    <table id="course_select" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.delete') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.selection_confirm') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.new_selection') }}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="form-group" align="center">
        <button id="sel_delete" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/table.delete') }}}</button>
    </div>
    <br><br><br>

    <div class="form-group" align="center">
        <h4>
            已选课程
        </h4>
    </div>
    <table id="course_delete" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.delete') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.selection_confirm') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.new_selection') }}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <br>

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
        var tbCourseSel = null;
        var tbStudent = null;
        $.initDataTable = function(tab,type){
            var rst;
            if (type == 1){
                rst = tab.dataTable( {
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
                        "url": "{{ URL::to('admin/select/delete_selection_data') }}",
                        "data": function ( d ) {
                            d["student_no"] = $("#student_id").val();
                            d["type"]=type;
                        }
                    },
                    "aaSorting": [ [0,'asc'] ]
                });
            }
            else if (type == 2){
                rst = tab.dataTable( {
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
                        "url": "{{ URL::to('admin/select/delete_selection_data') }}",
                        "data": function ( d ) {
                            d["student_no"] = $("#student_id").val();
                            d["type"] = type;
                        }
                    },
                    "aaSorting": [ [1,'asc'] ],
                    "columnDefs":[{"orderable":false,"targets":0}]
                });
            }
            return rst;
        };
        $(document).ready(function() {
            $("#btnQuery").click(function(){
                var str = $("#student_id").val();
                var ex = /^[1-9]\d{12}$/;
                if (!ex.test(str)) {
                    alert("请输入学号（13位数字）");
                    $("#student_id").focus();
                    return false;
                }

                if (tbStudent == null){
                    var tab = $('#students');
                    tbStudent = $.initDataTable(tab, 1);
                } else{
                    tbStudent.fnReloadAjax();
                }

                if (tbCourseSel == null){
                    var tab = $('#course_select');
                    tbCourseSel = $.initDataTable(tab, 2);
                } else{
                    tbCourseSel.fnReloadAjax();
                }

                $("#course_delete tr").each(function(i) {
                    if (i > 0) {
                        $(this).remove();
                    }
                });
            });
            $("#sel_delete").click(function(){
                if ($('#course_select :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }

                $("#course_delete tr").each(function(i) {
                    if (i > 0) {
                        $(this).remove();
                    }
                });

                $("#course_select tr").each(function(i) {
                    if (i > 0) {
                        var checkbox = $(this).find(":checkbox");
                        if (checkbox == null)
                            return;
                        var ck = checkbox.is(':checked');
                        if (ck == true){
                            var confirmed = $(this).find("td").eq(4).html();
                            if (confirmed == '已确认')
                                return;
                            var code = $(this).find("td").eq(1).html();
                            var name = $(this).find("td").eq(2).html();
                            var credit = $(this).find("td").eq(3).html();
                            var is_new = $(this).find("td").eq(5).html();
                            $('#course_delete').append("<tr><td>" + '<label id="sid" value="' + checkbox.val() + '"></label>' + "</td><td>" + code + "</td><td>" + name + "</td><td>"
                                + credit + "</td><td>" + confirmed + "</td><td>" + is_new + "</td></tr>");
                        }
                    }
                })
            });
            $("#delete").click(function(){
                var selections = new Array();
                $("#course_delete tr").each(function(i) {
                    if (i > 0) {
                        var del = $(this).find("#sid");
                        var sid = parseInt(del.attr('value'));
                        if (!isNaN(sid))
                            selections.push(sid);
                        del.html("已删除");
                    }
                })
                if (selections.length <= 0){
                    return false;
                }

                var jsonData = {
                    "selections[]": selections
                };

                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/select/delete_selection_submit') }}',
                    async: false,
                    data: jsonData,
                    success: function (json) {
                        if (json == 'ok'){
                            alert('删除成功');
                        }
                    }
                });
            });
		});
	</script>
@stop