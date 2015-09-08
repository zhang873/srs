@extends('admin.layouts.frame_modal')

{{-- Web site Title --}}
@section('title')
	exam:: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="form-group" align="center">
        <h3>
            {{ Lang::get('admin/admissions/title.exam_info') }}
        </h3>
    </div>
    <table id="exam" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-04"></th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.course_ID') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.course_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.credit') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exampaper_code') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exampaper_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.major_name') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.course_type') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exam_year') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exam_semester') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.isRequired') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exam_room_code') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.seat_code') }}}</th>
                <th class="col-md-03">{{{ Lang::get('admin/admissions/table.exam_flag') }}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="form-group" align="center">
        <label id="lbl"></label>
    </div>


@stop

@section('styles')

<style>
    div.DTTT .btn {
        color:#ffffff !important;
        font-size: 14px;
        margin-left: 20px;
    }
    div.DTTT{
        margin-left: .3em;
    }
    .rlbl{
        text-align:right;
        width:100px;
    }
    .twidth{
        width:150px;
    }
    .col-md-01 {
        width: 10%;
    }
    .col-md-02 {
        width: 8%;
    }
    .col-md-03 {
        width: 7%;
    }
    .col-md-04 {
        width: 5%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        var oTable;

        $(document).ready(function() {
            oTable = $('#exam').dataTable( {
                "searching":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/course/table.records_per_page') }}} _MENU_",
                        "sInfo": "{{{ Lang::get('admin/course/table.records_sInfo') }}}",
                        "oPaginate": {
                        "sFirst": "{{{ Lang::get('admin/course/table.records_sFirst') }}}",
                        "sPrevious": "{{{ Lang::get('admin/course/table.records_sPrevious') }}}",
                        "sNext": "{{{ Lang::get('admin/course/table.records_sNext') }}}",
                        "sLast": "{{{ Lang::get('admin/course/table.records_sLast') }}}"
                        },
                        "sZeroRecords": "{{{ Lang::get('admin/course/table.records_sZeroRecords') }}}",
                        "sInfoEmpty": "{{{ Lang::get('admin/course/table.records_sInfoEmpty') }}}",
                        "sProcessing": "{{{ Lang::get('admin/course/table.records_processing') }}}"
                },
                "bProcessing": true,
                "bServerSide": true,
                "ajax": {
                    "url": "{{ URL::to('admin/admissions/query_exam_data') }}",
                    "data": function ( d ) {
                        d["student_id"] = "{{$id}}";
                    }
                },

                "aaSorting": [ [1,'asc'] ],
                "columnDefs":[{"orderable":false,"targets":0}]
            });
            var tableTools = new $.fn.dataTable.TableTools( oTable, {
                "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                        "sButtonClass":"btn btn-small btn-info",
                        "sFileName": "{{{ Lang::get('admin/admissions/title.exam_info') }}}.xls",
                        "mColumns": "all"
                    }
                ]
            } );
            $( tableTools.fnContainer() ).insertAfter('#lbl');
		});
	</script>
@stop