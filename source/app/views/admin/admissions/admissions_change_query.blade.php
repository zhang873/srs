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
            {{Lang::get('admin/admissions/table.query_input')}}
        </h3>
    </div>


    <div class="form-group" align="center">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_name') }}</label>
        <input tabindex="1" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.out_of_campus') }}</label>
        <select size="1" tabindex="4" name="out_of_campus" id="out_of_campus" style="width:200px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.into_campus') }}</label>
        <select size="1" tabindex="4" name="into_campus" id="into_campus" style="width:200px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.application_year') }}</label>
        <select name="application_year" id="application_year" style="width:150px;">
            <option value="">全部</option>
            @for ($i=2000;$i<2025;$i++)
                <option value="{{{$i}}}">{{$i}}</option>
            @endfor
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.application_semester') }}</label>
        <select name="application_semester" id="application_semester" style="width:150px;">
            <option value="">全部</option>
            <option value="0">秋季</option>
            <option value="1">春季</option>
        </select>
    </div>

    <div class="form-group" align="center">

        <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/admissions/table.query') }}}</button>

    </div>
    <br><br>

     <table id="admissions" class="table table-striped table-hover table-bordered">
        <thead>
        <tr>

            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.out_of_major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.into_major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.out_of_campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.into_campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.final_result') }}}</th>

        </tr>
        </thead>

    </table>
    <div align="center">
        <button id="btnPass" name="state" value="1" class="btn btn-small btn-info" >同意</button>
    </div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:120px;

        }
        .rtxt{
            text-align:left;
            width:120px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "searching":false,
                'ordering':false,
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
                "bAutoWidth":  true,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_check_change_admissions_province') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["campus"]=$('#campus').val();
                        d["school"]=$('#school').val();
                        d["state"]=$('#btnValue').val();
                    }

                },

                "aaSorting": [ [0,'asc'] ]
            });

            $(function () {
                $("#btnPass").click(function(){
                    $("#btnValue").val(1);
                    oTable.fnReloadAjax();
                });

                $("#btnQuery").click(function(){
                    $("#btnValue").val(2);
                    oTable.fnReloadAjax();
                });
                $('#checkAll').click(function () {
                    //
                    $(':checkbox').prop("checked", true);
                });
                $('#checkNo').click(function () {

                    $(':checkbox').prop("checked", false);
                });
            });
        });

    </script>
@stop