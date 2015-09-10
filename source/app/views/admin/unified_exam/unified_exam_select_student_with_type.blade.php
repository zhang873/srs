@extends('admin.layouts.default')
{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop
{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/unified_exam/title.choose_unified_admissions') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{URL::to('admin/unified_exam/save_type') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="type" value="{{{ $type }}}" />
        <input id="selectedStudent" name="selectedStudent" type="hidden" value="" />
        <!-- ./ csrf token -->

        <table id="stuinfo" class="table table-striped table-hover table-bordered text-center" align="center" style="width: 800px">
            <thead>
            <tr>
                <td class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_id') }}}</td>
                <td class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_name') }}}</td>
                <td class="col-md-2">{{{ Lang::get('admin/unified_exam/table.input_flag') }}}
                    <br>
                    <label id="check_all"><a>全选</a></label>
                    <label id="check_none"><a>全否</a></label>
                </td>
            </tr>
            </thead>
        </table>
        <div align="center">
            <button type="submit" name="btnSubmit" id="btnSubmit" disabled="disabled">{{{ Lang::get('admin/unified_exam/table.input_score') }}}</button>
        </div>
     </form>

@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        function countCheckedBoxes() {
            var n = $( "input:checked" ).length;
            if (n>0) {
                $("#btnSubmit").attr("disabled", false);
                for (i=0;i<$( "input:checked" ).length;i++) {
                    if (i==0) {
                        $("#selectedStudent").val($( "input:checked" )[i].value);
                    } else {
                        $("#selectedStudent").val($("#selectedStudent").val() + ',' + $( "input:checked" )[i].value);
                    }
                }
            } else {
                $("#btnSubmit").attr("disabled", true);
            }
        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#stuinfo').dataTable( {
                "ordering":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/unified_exam/table.records_per_page') }}} _MENU_"
                },
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "{{ URL::to('admin/unified_exam/data_student_type') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                }
            //    "aaSorting": [ [0,'asc'] ]
            });


            $(function () {
                $('#check_all').click(function () {
                    $(':checkbox').prop("checked", true);
                    countCheckedBoxes();
                });
                $('#check_none').click(function () {
                    $(':checkbox').prop("checked", false);
                    countCheckedBoxes();
                });
                $("#btnSubmit").click(function () {
                        $.post("{{URL::to('admin/unified_exam/save_type') }}",{type:$('#type').val(),selectedStudent:$('#selectedStudent').val()});
                        return true;
                });
            });
        });
    </script>
@stop