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
        <label class="rlbl">{{ Lang::get('admin/select/table.group_name') }}</label>
        <select class="twidth" size="1" tabindex="1" name="group" id="group">
            <option value="全部" selected="selected">全部</option>
            @foreach ($groups as $group)
                <option value="{{{ $group->gid }}}"> {{{ $group->gname }}} </option>
            @endforeach
        </select>
        <label class="rlbl" >{{ Lang::get('admin/select/title.group_num') }}</label>
        <input class="twidth" tabindex="2" type="text" name="group_num" id="group_num">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.campus') }}</label>
        <select class="twidth" size="1" tabindex="1" name="campus" id="campus">
            <option value="全部" selected="selected">全部</option>
            @foreach ($campuses as $campus)
                <option value="{{{ $campus->id }}}"> {{{ $campus->name }}} </option>
            @endforeach
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.major_classification') }}</label>
        <select class="twidth" size="1" tabindex="4" name="major_classification" id="major_classification">
            <option value="全部" selected="selected">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.selection_year') }}</label>
        <select class="twidth" size="1" tabindex="5" name="year" id="year">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.selection_semester') }}</label>
        <select class="twidth" size="1" tabindex="6" name="semester" id="semester">
            <option value="全部" selected="selected">全部</option>
            <option value="1">春季</option>
            <option value="2">秋季</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.course_name') }}</label>
        <input class="twidth" tabindex="7" type="text" name="course_name" id="course_name">
        <label class="rlbl">{{ Lang::get('admin/select/table.is_obligatory') }}</label>
        <select class="twidth" size="1" tabindex="8" name="is_obligatory" id="is_obligatory">
            <option value="全部" selected="selected">全部</option>
            <option value="0">选修</option>
            <option value="1">必修</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.selection_status') }}</label>
        <select class="twidth" size="1" tabindex="9" name="selection_status" id="selection_status">
          <option value="全部" selected="selected">全部</option>
          <option value="1">首次</option>
          <option value="2">再次</option>
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.student_classification') }}</label>
        <select class="twidth" size="1" tabindex="10" name="student_classification" id="student_classification">
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
            班级选课情况记录
        </h3>
    </div>

	<table id="selection" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-01">{{{ Lang::get('admin/select/table.student_id') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.student_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_code') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.major') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.is_obligatory') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.year') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.semester') }}}</th>
			    <th class="col-md-01">{{{ Lang::get('admin/select/table.campus') }}}</th>
                <th class="col-md-01">{{{ Lang::get('admin/select/table.group_name') }}}</th>
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
        width:250px;
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
                if ($('#group').val() == '全部' && $('#group_num').val() == '') {
                    alert("班名称或班号必须填写");
                    $("#group").focus();
                    return false;
                }
                var ex = /^\d+$/;
                var str = $.trim($('#group_num').val());
                if (str != ''){
                    if (!ex.test(str)) {
                        alert("班号只接受数字");
                        $("#group_num").focus();
                        return false;
                    }
                    if (str.length > 15) {
                        alert("班号超过15位");
                        $("#group_num").focus();
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
                            "url": "{{ URL::to('admin/select/query_group_selection_data') }}",
                            "data": function ( d ) {
                                d["group"]=$('#group').val();
                                d["group_num"]=$.trim($('#group_num').val());
                                d["campus"]= $('#campus').val();
                                d["major_classification"]= $('#major_classification').val()
                                d["year"]=$('#year').val();
                                d["semester"]= $('#semester').val();
                                d["course_name"]=$.trim($('#course_name').val());
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
                } else {
                    oTable.fnReloadAjax();
                }

            });
            $("#campus").change(function(){
                var campus_id = $('#campus').val();
                $("#group").empty();
                $("#group").append("<option value='全部'>全部</option>");
                var obj = eval({{$groups->toJson()}});
                for (var i=0; i<obj.length; i++){
                    if (campus_id == '全部' || obj[i].cid == campus_id){
                        $("#group").append("<option value='" + obj[i].gid + "'>" + obj[i].gname + "</option>");
                    }
                }
            });
		});

	</script>
@stop