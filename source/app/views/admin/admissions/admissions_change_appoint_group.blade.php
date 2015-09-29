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
        <input type="hidden" name="campus" id="campus" value="{{$campus->id}}">
    <input id="selectedIds" name="selectedIds" type="hidden" value="" />
    <input id="selectedGroup" name="selectedGroup" type="hidden" value="" />
        <div class="form-group" align="center">
            <h4>
                {{{ Lang::get('admin/admissions/title.change_admissions_appoint_group') }}}
            </h4>
        </div>
        <table id="admissions" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th></th>
                <th>{{{ Lang::get('admin/admissions/table.select') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.select_group') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.major') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
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
        <button id="btnOk" name="btnOk" value="2" class="btn btn-small btn-info" type="submit">确定</button>
    </div>
    <div>
        <input type="hidden" id="btnValue" name="btnValue" value="1"/>
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

        function countCheckedBoxes() {
            var n = $("input[name='checkItem[]']:checkbox:checked" ).length;
            var ids = $('#checkItem');
            var m = ids.length;
            var group = $('#group');
            $("#selectedIds").val('');
            $("#selectedGroup").val('');
            if (n==0) {
                alert('请选择需指定班级的学生！');
                return false;
            }else  {
              /* ids.each(function(i,e){
                   if(e.checked()){
                       $("#selectedIds").val($("#selectedIds").val() + ',' + ids[i].val());
                       $("#selectedGroup").val($("#selectedGroup").val() + ',' + group[i].val());
                   }
               });
             */   return true;
            }
        }

        var oTable;
        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "searching":false,
                "ordering": false,
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
                    "url": "{{{ URL::to('admin/admissions/data_admissions_change_appoint_group') }}}",
                    "data": function ( d ) {
                        d["selectedIds"]= $('#selectedIds').val();
                        d["selectedGroup"]= $('#selectedGroup').val();
                        d["state"]= $('#btnValue').val();
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

                $("#btnOk").click(function(){
                        $('btnValue').val(2);
                        if(countCheckedBoxes()){
                            oTable.fnReloadAjax();
                        }



            });
        });
    </script>
@stop