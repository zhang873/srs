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

    <div class="form-group" align="center">
        <h3>
            {{ Lang::get('admin/admissions/title.count_condition_select') }}
        </h3>
    </div>

    <div class="form-group" align="center" width="600px">
        <input tabindex="1" type="checkbox" name="checkItem[]" id="checkItem" value="1">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.year') }}}</label>
        <input tabindex="2"  type="checkbox" name="checkItem[]" id="checkItem" value="2">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.semester') }}}</label>
        <input tabindex="3" type="checkbox" name="checkItem[]" id="checkItem" value="3">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_type') }}}</label>
    </div>
    <div class="form-group" align="center" width="600px">
        <input tabindex="4" type="checkbox" name="checkItem[]" id="checkItem" value="4">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major') }}}</label>
        <input tabindex="5" type="checkbox" name="checkItem[]" id="checkItem" value="5">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major_classification') }}}</label>
        <input tabindex="6" type="checkbox" name="checkItem[]" id="checkItem" value="6">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.admission_state') }}}</label>
    </div>
    <div class="form-group" align="center" width="600px">
        <input tabindex="7" type="checkbox" name="checkItem[]" id="checkItem" value="7">
        <label  class="rlbl">{{{ Lang::get('admin/admissions/table.school') }}}</label>
        <input tabindex="8" type="checkbox" name="checkItem[]" id="checkItem" value="8">
        <label class="rlbl">{{{ Lang::get('admin/admissions/table.campus') }}}</label>
        <label style="width:116px;"/>
    </div>
    <div align="center">
    <button class="btn btn-small btn-info" id="btnQuery">{{{ Lang::get('admin/admissions/table.ok') }}}</button>
    </div>
    <br>

    <table id="students" class="table table-striped table-hover">
        <caption><h4>{{ Lang::get('admin/admissions/title.admissions_information_classification_count') }}</h4></caption>
        <thead>
            <tr>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.year') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.semester') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.school') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.total_number') }}}</th>
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
        text-align:left;
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
        var oTable = null;

        $(document).ready(function() {
            $("#btnQuery").click(function(){
                var fields = $('input:checked').serializeArray();
                if (fields.length <= 0) {
                    alert('请选择统计条件');
                    return false;
                }
                if (oTable == null){
                    oTable = $('#students').dataTable( {
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
                            "url": "{{ URL::to('admin/admissions/information_classification_count_data') }}",
                            "data": function ( d ) {
                                var fields = $('input:checked').serializeArray();
                                if (fields.length > 0) {
                                    d["checkitem[]"] = new Array();
                                    $.each(fields, function(n, value){
                                        d["checkitem[]"].push(value.value);
                                    });
                                }
                            }
                        }
                    });
                    var tableTools = new $.fn.dataTable.TableTools( oTable, {
                        "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                                "sButtonClass":"btn btn-small btn-info",
                                "sFileName": "{{{ Lang::get('admin/admissions/title.admissions_information_classification_count') }}}.xls",
                                "mColumns": "all"
                            }
                        ]
                    } );
                    $( tableTools.fnContainer() ).insertAfter('#lbl');
                } else{
                    oTable.fnReloadAjax();
                }
            });
		});
	</script>
@stop