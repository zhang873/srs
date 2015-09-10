@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop
{{-- Web site Title --}}
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
        <h4>
            {{ Lang::get('admin/admissions/title.admissions_change_authority') }}
        </h4>
    </div>
    <br>
    <div id="show_admissions" >
    <table id="admissions" class="table table-striped table-hover table-bordered" width="600px" align="center">
        <thead>
        <tr>
            <th>序号</th>
            <th>{{{ Lang::get('admin/admissions/table.campus_name') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.campus_code') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.select') }}}
                <br>
                <label id="checkAll"><a>全选</a></label>&nbsp;&nbsp;
                <label id="checkNo"><a>全否</a></label>
            </th>
            <th>{{{ Lang::get('admin/admissions/table.authority') }}}</th>
        </tr>
        </thead>
    </table>
        <br>
        <div align="center">
            <button id="btnSave" name="state" value="2" class="btn btn-small btn-info" >{{Lang::get('admin/admissions/table.save')}}</button>
            <input type="hidden" id="btnValue" name="btnValue" value="1">
        </div>
</div>

@stop

@section('style')
    <style>
        .tdalign{
            alignment:center;
        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "ordering":  false,
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
                "bSort": true,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_admissions_change_authority') }}}",
                    "data": function (d) {
                        var fields = $(' :checked').serializeArray();
                        if (fields.length > 0) {
                            d["checkItem[]"] = new Array();
                            $.each(fields, function (n, value) {
                                d["checkItem[]"].push(value.value);
                            });
                        }
                        d['final_result'] = $('#btnValue').val();
                    }
                },
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%", align:"center"});

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
            $(function () {
                $("#btnSave").click(function(){
                    $('#btnValue').val(1);
                    oTable.fnReloadAjax();
                });

                $('#checkAll').click(function () {
                    $(this).css("cursor","pointer");
                    $(':checkbox').prop("checked", true);

                });
                $('#checkNo').click(function () {
                    $(this).css("cursor","pointer");
                    $(':checkbox').prop("checked", false);

                });
            });

        });

    </script>
@stop