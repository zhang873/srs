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
				<th class="col-md-2">{{{ Lang::get('admin/select/table.balance') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
    <br><br><br><br>
    <div class="form-group" align="center">
        <h4>
            在已选的课程中，教学点已经停开的课程
        </h4>
    </div>
    <table id="course_stop" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.cost') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.confirm') }}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <br><br><br>

    <div class="form-group" align="center">
        <h4>
            其他已选课程
        </h4>
    </div>
    <table id="course_select" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.selection') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_num') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.cost') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.confirm') }}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <br>

    <div class="form-group" align="center">
        <button id="btnConfirm" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/table.confirm') }}}</button>
    </div>

    <div id="show">

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
        var tbCourseStop = null;
        var tbCourseSel = null;
        var tbStudent = null;
        $.initDataTable = function(tab,type){
            var rst;
            if (type == 1 || type == 2){
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
                        "url": "{{ URL::to('admin/select/confirm_selection_data') }}",
                        "data": function ( d ) {
                            d["student_no"] = $("#student_id").val();
                            d["type"]=type;
                        }
                    },
                    "aaSorting": [ [0,'asc'] ]
                });
            }
            else if (type == 3){
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
                        "url": "{{ URL::to('admin/select/confirm_selection_data') }}",
                        "data": function ( d ) {
                            d["student_no"] = $.trim($("#student_id").val());
                            d["type"] = type;
                            var fields = $('#course_select :checked').serializeArray();
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
            }
            return rst;
        };
		$(document).ready(function() {
            $("#btnQuery").click(function(){
                var str = $.trim($("#student_id").val());
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

                if (tbCourseStop == null){
                    var tab = $('#course_stop');
                    tbCourseStop = $.initDataTable(tab, 2);
                } else{
                    tbCourseStop.fnReloadAjax();
                }
                if (tbCourseSel == null){
                    var tab = $('#course_select');
                    tbCourseSel = $.initDataTable(tab, 3);
                } else{
                    tbCourseSel.fnReloadAjax();
                }


            });
            $("#btnConfirm").click(function(){
                if ($('#course_select :checked').length <= 0){
                    alert("请选择课程！");
                    return false;
                }

                if (tbCourseSel == null){
                    var tab = $('#course_select');
                    tbCourseSel = $.initDataTable(tab, 3);
                } else{
                    tbCourseSel.fnReloadAjax();
                }


            });
		});

	</script>
@stop