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

    <table id="admission_group"  class="table table-striped table-hover table-bordered" align="center" width="500px">
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
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <td style="width: 150px;">
                <select name="admission_state" id="admission_state" style="width:150px;">
                    <option value="全部">全部</option>
                    <option value="0">已录入数据</option>
                    <option value="1">已上报省校</option>
                    <option value="2">省校已审批</option>
                    <option value="3">未注册</option>
                    <option value="4">在籍</option>
                    <option value="5">异动中</option>
                    <option value="6">毕业</option>
                    <option value="7">退学</option>
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
            <td style="width: 150px;">    <input type="text"  style="width:200px;" id="group_code"></td>
        </tr>
        <tr>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
            <td style="width: 150px;">
                <select name="admissionyear" id="admissionyear" style="width:150px;">
                    <option value="全部">全部</option>
                    @for ($i=2000;$i<2025;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
            </td>
            <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
            <td style="width: 150px;">
                <select name="admissionsemester" id="admissionsemester" style="width:150px;">
                    <option value="全部">全部</option>
                    <option value="02">秋季</option>
                    <option value="01">春季</option>
                </select>
            </td>
        </tr>
              <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <td style="width: 150px;">
                    <select size="1" tabindex="5" name="major_classification" id="major_classification" style="width:150px;">
                        <option value="全部">全部</option>
                        <option value="14">专科</option>
                        <option value="12">本科</option>
                    </select>
                </td>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                <td style="width: 120px;">
                    <select size="1" tabindex="4" name="major" id="major" style="width:200px;">
                        <option value="全部">全部</option>
                        @foreach ($b_majors as $major)
                        <option value="{{{ $major }}}"> {{{ $major }}} </option>
                        @endforeach
                        @foreach ($z_majors as $major)
                        <option value="{{{ $major }}}"> {{{ $major }}} </option>
                        @endforeach
                    </select>
                </td>
            </tr>
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
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
                <td style="width: 200px;">
                    <select name="group_name" id="group_name" style="width:150px;">
                        <option value="全部">全部</option>
                    </select>
                </td>
            </tr>
    </table>
    <div align="center">
        <button class="btn btn-small btn-info" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
    </div>

    <br>


    <table id="students" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-04"></th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.teaching_plan_ID') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.school') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
                <th class="col-md-02">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
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
        width: 4%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        var oTable = null;
        var str = "<?php $str = implode("|", $b_majors); echo $str;?>";
        var b_majors = str.split("|");
        str = "<?php $str = implode("|", $z_majors); echo $str;?>";
        var z_majors = str.split("|");
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
                            "url": "{{ URL::to('admin/admissions/student_info_data') }}",
                            "data": function ( d ) {
                                d["student_name"] = $('#student_name').val();
                                d["student_id"] = $('#student_id').val();
                                d["admission_state"] = $('#admission_state').val();
                                d["group_code"] = $('#group_code').val();
                                d["admissionyear"] = $('#admissionyear').val();
                                d["admissionsemester"] = $('#admissionsemester').val();
                                d["major_classification"] = $('#major_classification').val();
                                d["major"] = $('#major').val();
                                d["school"] = $('#school').val();
                                d["campus"] = $('#campus').val();
                                d["create_admin_group_year"] = $('#create_admin_group_year').val();
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
                        "aaSorting": [ [2,'asc'] ],
                        "columnDefs":[{"orderable":false,"targets":0}]
                    });
                    var tableTools = new $.fn.dataTable.TableTools( oTable, {
                        "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                                "sButtonClass":"btn btn-small btn-info",
                                "sFileName": "{{{ Lang::get('admin/admissions/title.student_info') }}}.xls",
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

            $("#major_classification").change(function(){
                var sVal=$("#major_classification").val();
                $("#major").empty();
                $("#major").append("<option value='全部'>全部</option>");
                if (sVal=="全部") {
                    for (var i = 0; i < b_majors.length; i ++) {
                        $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                    }
                    for (var i = 0; i < z_majors.length; i ++) {
                        $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                    }
                }
                if (sVal==12) {
                    for (var i = 0; i < b_majors.length; i ++) {
                        $("#major").append("<option value='"+b_majors[i]+"'>"+b_majors[i]+"</option>");
                    }
                }
                if (sVal==14) {
                    for (var i = 0; i < z_majors.length; i ++) {
                        $("#major").append("<option value='"+z_majors[i]+"'>"+z_majors[i]+"</option>");
                    }
                }
            });
		});
	</script>
@stop