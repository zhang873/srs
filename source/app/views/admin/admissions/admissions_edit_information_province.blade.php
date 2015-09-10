@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
    </div>

   {{-- choose input form --}}

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <div class="form-group" align="center">
            <h3>
                {{Lang::get('admin/admissions/table.query_input')}}
            </h3>
        </div>
        <div class="form-group" align="center" width="600px">
            <label for="student_name" class="rlbl">{{ Lang::get('admin/admissions/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">
            <label for="student_id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
        </div>
        <div  class="form-group" align="center">
            <button id="btnQuery" name="state" type="submit" value="2" class="btn btn-small btn-info" >查询</button>
        </div>

   <br>
   <br>
   <br>

        <table id="admissions" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th></th>
                <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.campus') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.maritalStatus') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.jiGuan') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.huKou') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.distribution') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.occupationState') }}}</th>
            </tr>
            </thead>
        </table>
    <div  class="form-group" align="center">
        <button id="btnOk" name="state" value="1" class="btn btn-small btn-info" type="submit">确定</button>
        <input type="hidden" id="admission_id[]" name="admission_id"/>
    </div>
@stop

@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:200px;

        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#admissions').dataTable({
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_",
                    "sProcessing": "正在加载中......",
                    "sZeroRecords": "没有数据！",
                    "sEmptyTable": "表中无数据存在！",
                    "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
                    "sInfoEmpty": "显示0到0条记录",
                    "sInfoFiltered": "数据表中共为 _MAX_ 条记录",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "上一页",
                        "sNext": "下一页",
                        "sLast": "末页"
                    }
                },
                "bFilter": true,
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth": true,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_admissions_edit_province') }}}",
                    "data": function (d) {
                        d["student_id"] = $('#student_id').val();
                        d["student_name"] = $('#student_name').val();
                        d["politicalstatus"]=$('#politicalstatus').val();
                         d["maritalstatus"]=$('#maritalstatus').val();
                         d["jiguan"]=$('#jiguan').val();
                         d["hukou"]=$('#hukou').val();

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
                "aaSorting": [[0, 'asc']]
            });

            $("#btnQuery").click(function () {
                oTable.fnReloadAjax();
            });
        });

    </script>
@stop