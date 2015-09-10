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
        <div class="pull-right">
            <a href="{{URL::to('admin/admissions/admissions_change_appoint_group')}}">学籍异动指定班级</a>
        </div>
        <br>
    </div>

    <div class="form-group" align="center">
        <h4>
            {{Lang::get('admin/admissions/table.query_input')}}
        </h4>
    </div>


    <div class="form-group" align="center">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_name') }}</label>
        <input tabindex="1" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.major_classification') }}</label>
        <select size="1" tabindex="5" name="major_classification" id="major_classification" style="width:200px;">
            <option value="">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.major') }}</label>
        <select name="major" id="major" style="width:200px;">
            <option value="">全部</option>
            @foreach($rawprograms as $rawprogram)
                <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.create_group_year') }}</label>
        <select size="1" tabindex="5" name="create_group_year" id="create_group_year" style="width:200px;">
            <option value="">全部</option>
            @for ($i=2000;$i<2025;$i++)
                <option value="{{{$i}}}">{{$i}}</option>
            @endfor

        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.group_name') }}</label>
        <select size="1" tabindex="4" name="group_name" id="group_name" style="width:200px;">
            <option value="">全部</option>
            @foreach($groups as $group)
                <option value="{{$group->id}}">{{$group->name}}</option>
            @endforeach
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
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admission_change') }}}</th>
        </tr>
        </thead>
    </table>
    <div id="frames" style="display: none;">
        <iframe src="" id="base_info" name="base_info" width="100%" height="600px" frameborder="0"></iframe>
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:120px;

        }

    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function query_base_info() {
            if (($('#student_id').val() == '') && ($('#id_number').val() == '')
                    && ($('#reg_number').val() == '')) {
                alert('{{ Lang::get('admin/admissions/title.comprehensive_student_info_hint') }}');
                return false;
            }
            var ff = document.getElementById("base_info");
            if (ff != null) {

                ff.src = "{{{ URL::to('admin/admissions/base_student_info') }}}" + "?student_id=" + $("#student_id").val()
                + "&id_number=" + $("#id_number").val() + "&reg_number=" + $("#reg_number").val();
            }
        }
        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "searching":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_",
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
                    "url": "{{{ URL::to('admin/admissions/data_change_admissions_campus') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["create_group_year"]=$('#create_group_year').val();
                        d["group_name"]=$('#group_name').val();
                    }
                },

                "aaSorting": [ [0,'asc'] ]
            });
            $("#btnQuery").click(function(){
                oTable.fnReloadAjax();
            });
        });

    </script>
@stop