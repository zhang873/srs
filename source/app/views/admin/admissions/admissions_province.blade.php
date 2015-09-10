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
            <a href="{{{ URL::to('admin/admissions/admissions_otherInfo') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_other_info') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/admissions_edit_province') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_info') }}}</a>&nbsp;&nbsp;
        </div>
        <br>
    </div>

    <div class="form-group" align="center">
        <h3>
            {{Lang::get('admin/admissions/table.query_input')}}
        </h3>
    </div>
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

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
        <select size="1" tabindex="4" name="major" id="major" style="width:200px;">
            <option value="">全部</option>
            @foreach($majors as $major)
                <option value="{{$major->id}}">{{$major->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.school') }}</label>
        <select size="1" name="school" id="school" style="width:200px;">
            <option value="">全部</option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.campus') }}</label>
        <select size="1"  name="campus" id="campus" style="width:200px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.admission_state') }}</label>
        <select size="1" name="admission_state" id="admission_state" style="width:200px;">
            <option value="">全部</option>
            <option value="6">毕业</option>
            <option value="4">在籍</option>
        </select>

        <label class="rlbl">&nbsp;&nbsp;</label>
        <label style="width:200px">&nbsp;&nbsp;</label>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" name="state" value="1" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/admissions/table.query') }}}</button>

    </div>
    <br>
    <br>

    <div id="show_admissions">
        <div>备注：点击[查询]按钮后，出现以下页面</div>
        <br>
    <table id="admissions" class="table table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th class="width30"></th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.school') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_type') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_code') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.dateofbirth') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.source_classification') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.source_major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.change_info') }}}</th>
        </tr>
        </thead>
    </table>
</div>
    <br>
    <br>

    <div id="show">
        <input type="hidden" id="btnValue" value="1" />
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:120px;

        }
        .width30{
            text-align:center;
            width:30px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
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
                "bAutoWidth":  true,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_admissions_province') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["campus"]=$('#campus').val();
                        d["school"]=$('#school').val();
                        d["admission_state"]=$('#admission_state').val();
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

            $(function(){
                $("#show_admissions").hide();
                $("#show_info").hide();
                $("#btnQuery").click(function(){
                    $('#btnValue').val(1);
                    $('#show_admissions').show();
                    oTable.fnReloadAjax();
                });
                $("#btnEdit").click(function(){
                    //id=$(this).attr('title');
                    $('#show_info').show();
                    $('#btnValue').val(2);
                 //   $("#show_info").load("admin/admissions/edit_admissions_province");
                });

                $("#btnSave").click(function(){
                    $('#btnValue').val(3);
                    oTable.fnReloadAjax();
                });
            });
        });

    </script>
@stop