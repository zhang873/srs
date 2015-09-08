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
        <label class="rlbl">{{ Lang::get('admin/select/table.campus') }}</label>
        <select class="twidth" size="1" tabindex="1" name="campus" id="campus">
            <option value="全部" selected="selected">全部</option>
            @foreach ($campuses as $campus)
                <option value="{{{ $campus->id }}}"> {{{ $campus->name }}} </option>
            @endforeach
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/table.group_name') }}</label>
        <select class="twidth" size="1" tabindex="2" name="group" id="group">
            <option value="全部" selected="selected">全部</option>
            @foreach ($groups as $group)
                <option value="{{{ $group->id }}}"> {{{ $group->name }}} </option>
            @endforeach
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/title.selection_year') }}</label>
        <select class="twidth" size="1" tabindex="3" name="year" id="year">
            <option value="全部" selected="selected">全部</option>
            @if ($cur_year != null)
                @for( $i = 0; $i < 10; $i++)
                    <option value="{{{$cur_year - $i}}}">{{{$cur_year - $i}}}</option>
                @endfor
            @endif
        </select>
        <label class="rlbl">{{ Lang::get('admin/select/title.selection_semester') }}</label>
        <select class="twidth" size="1" tabindex="4" name="semester" id="semester">
          <option value="全部" selected="selected">全部</option>
          <option value="1">春季</option>
          <option value="2">秋季</option>
        </select>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/select/table.course_code') }}</label>
        <input class="twidth" tabindex="5" type="text" name="course_code" id="course_code">
        <label class="rlbl">{{ Lang::get('admin/select/table.course_name') }}</label>
        <input class="twidth" tabindex="6" type="text" name="course_name" id="course_name">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/select/table.is_obligatory') }}</label>
        <select class="twidth" size="1" tabindex="4" name="is_obligatory" id="is_obligatory">
            <option value="全部" selected="selected">全部</option>
            <option value="0">选修</option>
            <option value="1">必修</option>
        </select>
        <label style="width:333px">注意：课程编号或课程名称必须填写</label>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/select/title.query') }}}</button>
    </div>
    <br><br>

    <div class="form-group" align="center">
        <h3>
            统计选课人数
        </h3>
    </div>

	<table id="selection" class="table table-striped table-hover">
		<thead>
			<tr>
			    <th class="col-md-01">{{{ Lang::get('admin/select/table.campus') }}}</th>
                <th class="col-md-01">{{{ Lang::get('admin/select/table.group_name') }}}</th>
				<th class="col-md-03">{{{ Lang::get('admin/select/table.year') }}}</th>
				<th class="col-md-03">{{{ Lang::get('admin/select/table.semester') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_code') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/select/table.course_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.credit') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.is_obligatory') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/select/table.number') }}}</th>
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
                /*
                var ex = /^[1-9]\d{3}$/;
                var str = $("#year").val();
                if (!ex.test(str)) {
                    alert("请输入选课年度（4位数字）");
                    $("#year").focus();
                    return false;
                }
                str = $("#semester").val();
                if (str=='3') {
                    alert("请选择选课学期");
                    $("#semester").focus();
                    return false;
                }*/
                if ($('#course_code').val() == '' && $('#course_name').val() == '') {
                    alert("课程编号或课程名称必须填写");
                    $("#course_code").focus();
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
                            "url": "{{ URL::to('admin/select/count_number_course_data') }}",
                            "data": function ( d ) {
                                d["campus"]= $('#campus').val();
                                d["group"]=$('#group').val();
                                d["year"]=$('#year').val();
                                d["semester"]= $('#semester').val();
                                d["course_code"]=$('#course_code').val();
                                d["course_name"]=$('#course_name').val();
                                d["is_obligatory"]=$('#is_obligatory').val();
                            }
                        },
                        "aaSorting": [ [4, 'asc'] ]
                    });
                    var tableTools = new $.fn.dataTable.TableTools( oTable, {
                        "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "{{{ Lang::get('admin/select/title.export_excel') }}}",
                                "sButtonClass":"btn btn-small btn-info",
                                "sFileName": "统计选课人数.xls",
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

                var jsonData = {
                    "campus_id": campus_id
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/select/campus_group') }}',
                    async: true,
                    dataType: "json",
                    data: jsonData,
                    success: function (json) {
                        var obj = eval(json);
                        for (var i=0; i<obj.length; i++){
                            $("#group").append("<option value='" + obj[i].gid + "'>" + obj[i].gname + "</option>");
                        }
                    }
                });
            });

		});

	</script>
@stop