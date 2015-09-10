@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}

            <div class="pull-right">
                <a href="{{{ URL::to('admin/unified_exam/select_type_save') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/unified_exam/table.select_student_input') }}}</a>
                <a href="{{{ URL::to('admin/unified_exam/input_student') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/unified_exam/title.input_unified_exam') }}}</a>
            </div>
        </h3>
    </div>

        <table id="unified_exam" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <td colspan="19"  class="col-md-2" align="center">{{{ Lang::get('admin/unified_exam/title.unified_exam_info') }}}</td>
            </tr>
            <tr>
                <th>{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.campus') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.class') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_year') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_type') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.final_results') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.action') }}}</th>
            </tr>
            </thead>

        </table>

@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#unified_exam').dataTable( {
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth":  true,
                "sAjaxSource": "{{ URL::to('admin/unified_exam/data_type') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                },
                "aaSorting": [ [0,'asc'] ]
            });
        });
    </script>
@stop
