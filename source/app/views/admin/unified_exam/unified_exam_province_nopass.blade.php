@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/unified_exam/title.unified_exam_info') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="input_course" class="form-horizontal" method="post" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->


        <table id="unified_exam" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <td colspan="19"  class="col-md-2" align="center">{{{ Lang::get('admin/unified_exam/title.unified_exam_info') }}}</td>
            </tr>
            <tr>
                <th>{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.major_name') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.campus') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_year') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_course') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_cause') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_type') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.final_results') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.remark') }}}</th>
            </tr>
            </thead>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td>{{ $exemption->student_id }}</td>
                    <td>{{ $exemption->student_name }}</td>
                    <td>{{ $exemption->program_name }}</td>
                    <td>{{ $exemption->campus_name }}</td>
                     <td>{{ $exemption->registration_year }}</td>
                    <td>{{ $exemption->subject_name }}</td>
                    <td>{{ $exemption->cause_name }}</td>
                    <td>{{ $exemption->type_name }}</td>
                    <td>@if ( $exemption->final_result == 0) <label>不通过</label>  @elseif  ( $exemption->final_result == 1) <label>通过</label> @elseif ( $exemption->final_result == 2) <lable>未审核</lable>@endif</td>
                    <td>{{ $exemption->failure_cause }}</td>
                </tr>
            @endforeach

        </table>
        <table class="table table-striped table-hover">
            <tr>
                <td>审核不通过原因</td>
                <td><input type="text" class="text-info text-primary" style="width: 100%" id="failure_cause" name="failure_cause"></td>
                <td><input type="submit" class="btn btn-info" value="{{{Lang::get('admin/exemption/table.nopass')}}}"></td>
                <td> <input type="button" class="btn btn-general" id="cancel" name="cancel" value="取消" onclick="javascript:window.location.href='/admin/unified_exam/approve_unified_exam'"></td>
            </tr>
        </table>
        </form>
@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#unified_exam1').dataTable( {
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth":  true,
                "sAjaxSource": "{{ URL::to('admin/unified_exam/data_unified_exam_province') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                },
                "aaSorting": [ [0,'asc'] ]
            });
        });
    </script>
@stop
