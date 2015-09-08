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
        <label class="rlbl">{{ Lang::get('admin/select/table.class_name') }}</label>
        <select class="twidth" size="1" tabindex="1" name="class_name" id="class_name">
          <option value="全部" selected="selected">全部</option>
        </select>
        <label class="rlbl" >{{ Lang::get('admin/select/title.class_num') }}</label>
        <input class="twidth" tabindex="2" type="text" name="class_num" id="class_num">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.school') }}</label>
        <select class="twidth" size="1" tabindex="3" name="school" id="school">
          <option value="全部" selected="selected">全部</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.major_classification') }}</label>
        <select class="twidth" size="1" tabindex="4" name="major_classification" id="major_classification">
          <option value="2" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.selection_year') }}</label>
        <input class="twidth" tabindex="5" type="text" name="year" id="year">
        <label class="rlbl">{{ Lang::get('admin/select/title.selection_semester') }}</label>
        <select class="twidth" size="1" tabindex="6" name="semester" id="semester">
          <option value="3" selected="selected">全部</option>
          <option value="1">春季</option>
          <option value="2">秋季</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.course_name') }}</label>
        <input class="twidth" tabindex="7" type="text" name="course_name" id="course_name">
        <label class="rlbl">{{ Lang::get('admin/select/table.is_obligatory') }}</label>
        <select class="twidth" size="1" tabindex="8" name="is_obligatory" id="is_obligatory">
            <option value="0">选修</option>
            <option value="1">必修</option>
            <option value="2" selected="selected">全部</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.selection_status') }}</label>
        <select class="twidth" size="1" tabindex="9" name="selection_status" id="selection_status">
          <option value="0" selected="selected">全部</option>
          <option value="1">首次</option>
          <option value="2">再次</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="10" name="student_classification" id="student_classification">
          <option value="2" selected="selected">全部</option>
          <option value="14">专科</option>
          <option value="12">本科</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <div class="col-md-offset-2 col-md-10">
            <button id="btnQuery" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/course/title.course_query') }}}</button>
        </div>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            班级选课情况记录
        </h3>
    </div>

	<table id="selection" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-01">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.student_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_id') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.major') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_obligatory') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.year') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.semester') }}}</th>
			    <th class="col-md-01">{{{ Lang::get('admin/select/table.school') }}}</th>
                <th class="col-md-01">{{{ Lang::get('admin/select/table.class_name') }}}</th>
				<th class="col-md-02">{{{ Lang::get('admin/select/table.selection_status') }}}</th>
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
        var oTable;
		$(document).ready(function() {
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
                    "url": "{{ URL::to('admin/select/query_class_selection_data') }}",
                    "data": function ( d ) {
                        d["class_name"]=$('#class_name').val();
                        d["class_num"]=$('#class_num').val();
                        d["school"]= $('#school').val();
                        d["major_classification"]= $('#major_classification').val()
                        d["year"]=$('#year').val();
                        d["semester"]= $('#semester').val();
                        d["course_name"]=$('#course_name').val();
                        d["is_obligatory"]=$('#is_obligatory').val();
                        d["selection_status"]=$('#selection_status').val();
                        d["student_classification"]=$('#student_classification').val();
                    }
                },
                "aaSorting": [ [0,'asc'] ]
			});

			var tableTools = new $.fn.dataTable.TableTools( oTable, {
                "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sButtonText": "{{{ Lang::get('admin/select/title.export_excel') }}}",
                        "sButtonClass":"btn btn-small btn-info",
                        "sFileName": "班级选课情况记录.xls",
                        "mColumns": "visible"
                    }
                ]
            } );
            $( tableTools.fnContainer() ).insertAfter('#btnQuery');

            $("#btnQuery").click(function(){
                oTable.fnReloadAjax();
            });

		});

	</script>
@stop