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
            {{ Lang::get('admin/admissions/title.query_condition_input') }}
        </h3>
    </div>

    <table id="qryCondition"  class="table table-striped table-hover table-bordered" align="center" width="500px">
        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <td style="width: 150px;">
                <input type="text"  style="width:200px;" id="student_name">
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <td style="width: 120px;">
                <input type="text"  style="width:200px;" id="student_id">
            </td>
        </tr>
        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.out_of_campus') }}}</th>
            <td style="width: 150px;">
                <select name="out_of_campus" id="out_of_campus" style="width:150px;">
                    <option value="全部">全部</option>
                    @foreach ($campuses as $campus)
                        <option value="{{{ $campus->id }}}"> {{{ $campus->name }}} </option>
                    @endforeach
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.into_campus') }}}</th>
            <td style="width: 200px;">
                <select name="into_campus" id="into_campus" style="width:150px;">
                    <option value="全部">全部</option>
                    @foreach ($campuses as $campus)
                        <option value="{{{ $campus->id }}}"> {{{ $campus->name }}} </option>
                    @endforeach
                </select>
            </td>
        </tr>

        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
            <td style="width: 150px;">
                <select name="application_year" id="application_year" style="width:150px;">
                    <option value="全部">全部</option>
                    @for ($i=2000;$i<2025;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
            <td style="width: 150px;">
                <select name="application_semester" id="application_semester" style="width:150px;">
                    <option value="全部">全部</option>
                    <option value="02">秋季</option>
                    <option value="01">春季</option>
                </select>
            </td>
        </tr>

        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.province_final_result') }}}</th>
            <td style="width: 150px;">
                <select name="province_final_result" id="province_final_result" style="width:150px;">
                    <option value="全部">全部</option>
                    <option value="0">未审核</option>
                    <option value="1">同意</option>
                    <option value="2">不同意</option>
                </select>
            </td>
        </tr>
    </table>
    <div align="center">
        <button class="btn btn-small btn-info" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
    </div>

    <br><br>


    <table id="students" class="table table-striped table-hover">
        <caption><h4>{{ Lang::get('admin/admissions/title.status_changing_record') }}</h4></caption>
        <thead>
            <tr>
                <th class="col-md-1"></th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.out_of_major') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.into_major') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.out_of_campus') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.into_campus') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.province_final_result') }}}</th>
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
        var oTable = null;

        $(document).ready(function() {
            $("#btnQuery").click(function(){
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
                            "url": "{{ URL::to('admin/admissions/status_changing_info_data') }}",
                            "data": function ( d ) {
                                d["student_name"] = $('#student_name').val();
                                d["student_id"] = $('#student_id').val();
                                d["out_of_campus"] = $('#out_of_campus').val();
                                d["into_campus"] = $('#into_campus').val();
                                d["application_year"] = $('#application_year').val();
                                d["application_semester"] = $('#application_semester').val();
                                d["province_final_result"] = $('#province_final_result').val();
                            }
                        },
                        "fnDrawCallback": function ( oSettings ) {
                            if ( oSettings.bSorted || oSettings.bFiltered || oSettings.bDrawing){
                                for ( var i=0, iLen=oSettings.aiDisplayMaster.length ; i<iLen ; i++ ){
                                    var counter = oSettings._iDisplayStart + i + 1;
                                    $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( counter );
                                }
                            }
                        },

                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                    var tableTools = new $.fn.dataTable.TableTools( oTable, {
                        "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                                "sButtonClass":"btn btn-small btn-info",
                                "sFileName": "{{{ Lang::get('admin/admissions/title.status_changing_record') }}}.xls",
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