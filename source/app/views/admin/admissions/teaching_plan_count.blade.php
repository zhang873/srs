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
            {{ Lang::get('admin/admissions/title.input_query_condition') }}
        </h3>
    </div>

    <table id="admission_group"  class="table table-striped table-hover table-bordered" align="center" width="500px">
        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.school') }}}</th>
            <td style="width: 150px;">
                <select name="school" id="school" style="width:150px;">
                    <option value="全部">全部</option>
                    @foreach ($schools as $school)
                        <option value="{{{ $school->school_id }}}"> {{{ $school->school_name }}} </option>
                    @endforeach
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <td style="width: 200px;">
                <select name="campus" id="campus" style="width:150px;">
                    <option value="全部">全部</option>
                    @foreach ($campuses as $campus)
                        <option value="{{{ $campus->id }}}"> {{{ $campus->name }}} </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.create_admin_group_year') }}}</th>
            <td style="width: 150px;">
                <select name="create_admin_group_year" id="create_admin_group_year" style="width:150px;">
                    <option value="全部">全部</option>
                    @for ($i=2000;$i<2025;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
            <td style="width: 150px;"><input type="text"  style="width:200px;" id="group_code"></td>
        </tr>


        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
            <td style="width: 150px;">
                <select name="group_name" id="group_name" style="width:150px;">
                    <option value="全部">全部</option>
                </select>
            </td>
            <th></th>
            <td></td>
        </tr>




    </table>
    <div align="center">
        <button class="btn btn-small btn-info" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
    </div>

    <br><br>


    <table id="students" class="table table-striped table-hover">
        <caption><h4>{{ Lang::get('admin/admissions/title.admissions_teaching_plan_count') }}</h4></caption>
        <thead>
            <tr>
                <th class="col-md-1"></th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus_code') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.teaching_plan_ID') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_code') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_name') }}}</th>
                <th class="col-md-1">{{{ Lang::get('admin/admissions/table.numbers') }}}</th>
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
                            "url": "{{ URL::to('admin/admissions/teaching_plan_count_data') }}",
                            "data": function ( d ) {
                                d["school"] = $('#school').val();
                                d["campus"] = $('#campus').val();
                                d["create_admin_group_year"] = $('#create_admin_group_year').val();
                                d["group_code"] = $('#group_code').val();
                                d["group"] = $('#group_name').val();
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
                                "sFileName": "{{{ Lang::get('admin/admissions/title.admissions_teaching_plan_count') }}}.xls",
                                "mColumns": "all"
                            }
                        ]
                    } );
                    $( tableTools.fnContainer() ).insertAfter('#lbl');
                } else{
                    oTable.fnReloadAjax();
                }
            });



            $("#campus").change(function(){
                var campus_id = $('#campus').val();
                $("#group_name").empty();
                $("#group_name").append("<option value='全部'>全部</option>");
                var jsonData = {
                    "campus_id": campus_id
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/admissions/campus_class') }}',
                    async: true,
                    dataType: "json",
                    data: jsonData,
                    success: function (json) {
                        var obj = eval(json);
                        for (var i=0; i<obj.length; i++){
                            $("#group_name").append("<option value='" + obj[i].gid + "'>" + obj[i].gname + "</option>");
                        }
                    }
                });
            });

            $("#school").change(function(){
                var school_id = $('#school').val();
                $("#campus").empty();
                $("#campus").append("<option value='全部'>全部</option>");
                var jsonData = {
                    "school_id": school_id
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/admissions/school_campus') }}',
                    async: true,
                    dataType: "json",
                    data: jsonData,
                    success: function (json) {
                        var obj = eval(json);
                        for (var i=0; i<obj.length; i++){
                            $("#campus").append("<option value='" + obj[i].id + "'>" + obj[i].name + "</option>");
                        }
                    }
                });
            });


		});
	</script>
@stop