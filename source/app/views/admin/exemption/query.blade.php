@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.input_score_stuinfo') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{URL::to('admin/exemption/input_require') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="exemption" class="table table-striped table-hover table-bordered" width="50%" align="center">
            <thead>
                <tr>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.input_flag') }}}</th>
                </tr>
             </thead>
        </table>
        <div align="center">
            <button type="submit" value="1" id="btnInput" disabled="disabled">{{{ Lang::get('admin/exemption/table.input_score') }}} </button>
        </div>
     </form>

@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#exemption').dataTable( {
                "searching":true,
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
                "sAjaxSource": "{{ URL::to('admin/exemption/data_query') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"90%", height:"90%"});
                },
                "aaSorting": [ [0,'asc'] ]
            });
        });
    </script>
@stop