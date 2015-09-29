@extends('admin.layouts.frame_modal')

{{-- Web site Title --}}
@section('title')
    edit_dropout:: @parent
@stop

{{-- Content --}}
@section('content')
{{-- Web site Title --}}
    <div align="center">
        <div class="form-group" align="left">备注：输入学号，点击【确定】链接后，会出现以下页面</div>
            <br>
    <div align="center"> <h4> {{Lang::get('admin/admissions/title.dropout_application_info')}}</h4></div>
    <table id="dropout_info" class="table table-striped table-hover table-bordered" align="center"  style="width: 800px">
        <thead>
        <tr>
            <th>{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.dropout_cause') }}}</th>
            <th>{{{ Lang::get('admin/admissions/table.action') }}}</th>
        </tr>
        </thead>
    </table>
        <input type="hidden" id="student_id" name="student_id" value="{{$id}}"/>
    </div>
    <br>
    <br>

    <br>
    <div align="left">
        <iframe align="left" src="" id="detail_info" name="detail_info" width="100%" height="600px" frameborder="0" scrolling="no" marginwidth="0"></iframe>
    </div>

    <br>

@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#dropout_info').dataTable({
                "ordering": false,
                "searching":false,
                "bPaginate": false,
                "bfoot":false,
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
                    "url": "{{{ URL::to('admin/admissions/data_dropout') }}}",
                    "data": function (d) {
                        d["student_id"]= $('#student_id').val();
                    }
                },
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});


                },
                "aaSorting": [[0, 'asc']]
            });
            $("#btnEdit").click(function () {
                parent.window.reload();
            });
        });

    </script>
@stop