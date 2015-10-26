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
       
        <div class="pull-right">
            <a href="{{{ URL::to('admin/admissions/group_define') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.groups_create') }}}</a> &nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/admin_group') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.admin_group') }}}</a>

        </div>
<br>
    </h3>
</div>
<div class="form-group" align="center">
    <h3>
        {{Lang::get('admin/admissions/table.query_input')}}
    </h3>
</div>

<!-- CSRF Token -->
<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
<!-- ./ csrf token -->
<input id="selectedGroups" name="selectedGroups" type="hidden" value="" />
<input id="selectedIds" name="selectedIds" type="hidden" value="" />
<input id="selectedClassAdvisers" name="selectedClassAdvisers" type="hidden" value="" />
<div class="form-group" align="center">
    <label for="year" class="rlbl" >{{ Lang::get('admin/admissions/table.year') }}</label>&nbsp;&nbsp;
    <select name="year" id="year" style="width:150px;">
        <option value="">全部</option>
        @for ($i=2000;$i<2025;$i++)
            <option value="{{{$i}}}">{{$i}}</option>
        @endfor
    </select>
    <label for="student_type" class="rlbl">{{ Lang::get('admin/admissions/table.student_type') }}</label>&nbsp;&nbsp;
    <select  name="student_type" id="student_type" style="width:150px;">
        <option value="">全部</option>
        <option value="12">本科</option>
        <option value="14">专科</option>
    </select>
</div>
<div class="form-group" align="center">
    <label for="exam_point" class="rlbl" >{{ Lang::get('admin/admissions/table.exam_point') }}</label>&nbsp;&nbsp;
    <select name="exam_point" id="exam_point" style="width:150px;">
        <option value="">全部</option>
    </select>
    <label for="student_type" class="rlbl">{{ Lang::get('admin/admissions/table.group_name') }}</label>&nbsp;&nbsp;
    <input type="text" name="groupname" id="groupname" style="width: 150px">
</div>
<div class="form-group" align="center">
    <button id="btnQuery" name="btnQuery" value="1" class="btn btn-small btn-info" >
        {{{ Lang::get('admin/admissions/table.query') }}}</button>

</div>
<br><br>


<table id="groups" class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="col-md-1"></th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.groups_sysid') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.groups_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.program_plan_count') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.class_adviser') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.year') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.semester') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.exam_point') }}}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="form-group" align="center">
<button id="btnSave" name="btnSave" value="2" class="btn btn-small btn-info" >
    {{{ Lang::get('admin/admissions/table.save') }}}</button>
<input type="hidden" id="btnValue" name="btnValue" value="1">
</div>
@stop

@section('styles')

    <style>
        .rlbl{
            width:150px;
            text-align: right;
        }
        .col-md-1{
            width:20px;
        }
        .col-md-2{
            width:15%;
        }
        .col-md-3{
            width:50px;
        }
    </style>
@stop
{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function checkSelection() {
            $('#selectedGroups').val('');
            $('#selectedIds').val('');
            $('#selectedClassAdvisers').val('');
            $("input[name='group_name[]']").each(function(){
                $('#selectedGroups').val($('#selectedGroups').val() + ',' + $(this).val());
            });
            $("input[name='ids[]']").each(function(){
                $('#selectedIds').val($('#selectedIds').val() + ',' + $(this).val());
            });
            $('input[name="class_adviser[]"]').each(function(){
                $('#selectedClassAdvisers').val($('#selectedClassAdvisers').val() + ',' + $(this).val());
            });
        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#groups').dataTable( {
                "searching":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_",
                    "sProcessing" : "正在加载中......",
                    "sZeroRecords" : "没有数据！",
                    "sEmptyTable" : "表中无数据存在！",
                    "sInfo" : "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
                    "sInfoEmpty" : "显示0到0条记录",
                    "sInfoFiltered" : "数据表中共为 _MAX_ 条记录",
                    "oPaginate" : {
                        "sFirst" : "首页",
                        "sPrevious" : "上一页",
                        "sNext" : "下一页",
                        "sLast" : "末页"
                    }
                },
                "bFilter": true,
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth":  false,
                "bSort": false,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_group_edit') }}}",
                    "data": function ( d ) {
                        d["class_advisers"]= $('#selectedClassAdvisers').val();
                        d["groups"]= $('#selectedGroups').val();
                        d["ids"]= $('#selectedIds').val();
                        d["year"]= $('#year').val();
                        d["exam_point"]=$('#exam_point').val();
                        d["student_type"]=$('#student_type').val();
                        d["groupname"]=$('#groupname').val();
                        d["state"]=$('#btnValue').val();
                    }

                },
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});

                    if ( oSettings.bSorted || oSettings.bFiltered || oSettings.bDrawing)
                    {
                        for ( var i=0, iLen=oSettings.aiDisplayMaster.length ; i<iLen ; i++ )
                        {
                            var counter = oSettings._iDisplayStart + i + 1;
                            $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( counter );
                        }
                    }
                },
                "aaSorting": [ [0,'asc'] ]
            });
            $(function() {
                $("#btnQuery").click(function(){
                    $('#btnValue').val(1);
                    oTable.fnReloadAjax();
                });
                $("#btnSave").click(function(){
                    $('#btnValue').val(2);
                    checkSelection();
                    oTable.fnReloadAjax();
                });
            });
        });
    </script>
@stop