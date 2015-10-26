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

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
    <div align="center">
        {{Lang::get('admin/admissions/table.query_input')}}
    </div>
        <br>
        <br>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_name') }}}&nbsp;&nbsp;</label>
            <input type="text"  style="width:150px;" id="student_name">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_id') }}}&nbsp;&nbsp;</label>
            <input type="text"  style="width:150px;" id="student_id">
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major_classification') }}}&nbsp;&nbsp;</label>
            <select size="1" tabindex="5" name="major_classification" id="major_classification" style="width:150px;">
                <option value="">全部</option>
                <option value="14">专科</option>
                <option value="12">本科</option>
            </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_type') }}}&nbsp;&nbsp;</label>
            <select size="1" tabindex="5" name="student_type" id="student_type" style="width:150px;">
                <option value="">全部</option>
                <option value="11">学历</option>
                <option value="12">课程</option>
            </select>
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.admissionyear') }}}&nbsp;&nbsp;</label>
                        <select name="year" id="year" style="width:150px;">
                            <option value="">全部</option>
                            @for ($i=2000;$i<2025;$i++)
                                <option value="{{{$i}}}">{{$i}}</option>
                            @endfor
                        </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}&nbsp;&nbsp;</label>
                        <select name="semester" id="semester" style="width:150px;">
                            <option value="">全部</option>
                            <option value="02">秋季</option>
                            <option value="01">春季</option>
                        </select>
        </div>

        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major') }}}&nbsp;&nbsp;</label>
                    <select size="1" tabindex="4" name="major" id="major" style="width:150px;">
                        <option value="">全部</option>
                        @foreach($rawprograms as $rawprogram)
                            <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
                        @endforeach
                    </select>
            <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;</label>
        </div>
        <div align="center">
                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-default" value="2" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
                    <!-- ./ form actions -->
        </div>
<br>
    <br>
    <table id="admissions" class="table table-striped table-hover table-bordered" align="center">
        <thead>
        <tr>
            <th></th>
            <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.application') }}}</th>
        </tr>
        </thead>
    </table>
    <br>
    <div align="left">
        <iframe align="left" src="" id="recovery" name="recovery" width="100%" height="600px" frameborder="0" scrolling="no" marginwidth="0"></iframe>
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            width:150px;
            text-align:right;
        }
        .width230{
            width:230px;
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
                    "url": "{{{ URL::to('admin/admissions/data_application_recovery_admissions') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["semester"]=$('#semester').val();
                        d["year"]=$('#year').val();
                        d["student_type"]=$('#student_type').val();
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

            $("#btnQuery").click(function(){
                $("#btnValue").val(2);
                oTable.fnReloadAjax();
            });
        });

    </script>
@stop