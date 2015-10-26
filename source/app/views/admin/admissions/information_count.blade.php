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
            {{ Lang::get('admin/admissions/title.count_condition_input') }}
        </h3>
    </div>

    <table id="admission_group"  class="table table-striped table-hover table-bordered" align="center" width="500px">
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.start_year') }}}</th>
            <td class="width150 col-md-1">
                <select name="start_year" id="start_year" class="twidth">
                    <option value="全部">全部</option>
                    @for ($i=2000;$i<2025;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
            </td>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.start_semester') }}}</th>
            <td class="width150 col-md-1">
                <select name="start_semester" id="start_semester" class="twidth">
                    <option value="全部">全部</option>
                    <option value="02">秋季</option>
                    <option value="01">春季</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.end_year') }}}</th>
            <td class="width150 col-md-1">
                <select name="end_year" id="end_year" class="twidth">
                    <option value="全部">全部</option>
                    @for ($i=2000;$i<2025;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>

            </td>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.end_semester') }}}</th>
            <td class="width150 col-md-1">
                <select name="end_semester" id="end_semester" class="twidth">
                    <option value="全部">全部</option>
                    <option value="02">秋季</option>
                    <option value="01">春季</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.school') }}}</th>
            <td class="width150 col-md-1">
                <select name="school" id="school" class="twidth">
                    <option value="全部">全部</option>
                    @foreach($schools as $school)
                        <option value="{{$school->school_id}}">{{$school->school_name}}</option>
                    @endforeach
                </select>
            </td>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <td class="width150 col-md-1">
                <select name="campus" id="campus" class="twidth">
                    <option value="全部">全部</option>
                    @foreach($campuses as $campus)
                        <option value="{{$campus->id}}">{{$campus->name}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
        <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.admissions') }}}</th>
            <td class="width150 col-md-1">
            <select name="admission_state" id="admission_state" class="twidth">
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

             <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
             <td class="width150 col-md-1">
                      <select size="1"  name="gender" id="gender" class="twidth">
                          <option value="全部">全部</option>
                          <option value="m">男</option>
                          <option value="f">女</option>
                      </select>
              </td>
        </tr>
        <tr>
             <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.former_level') }}}</th>
             <td class="width150 col-md-1">
                    <select size="1"  name="former_level" id="former_level" class="twidth">
                        <option value="全部">全部</option>
                        <option value="1">高中</option>
                        <option value="2">职高</option>
                        <option value="3">中专</option>
                        <option value="4">技校</option>
                        <option value="5">专科</option>
                        <option value="6">本科</option>
                        <option value="7">硕士研究生</option>
                        <option value="8">博士研究生</option>
                        <option value="9">其他</option>
                    </select>
                </td>
             <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
             <td class="width150 col-md-1">
                    <select id="nationgroup" name="nationgroup" class="twidth">
                        <option value="全部">全部</option>
                        @for($i=1;$i<=9;$i++)
                            <option value="0{{$i}}">{{Lang::get('table.nationgroup_0'.$i)}}</option>
                        @endfor
                        @for($i=10;$i<=56;$i++)
                            <option value="{{$i}}">{{Lang::get('table.nationgroup_'.$i)}}</option>
                        @endfor
                        <option value="97">{{Lang::get('table.nationgroup_97')}}</option>
                        <option value="98">{{Lang::get('table.nationgroup_98')}}</option>
                    </select>
               </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.occupation_status') }}}</th>
            <td style="width: 150px;">
                <select size="1"  name="occupation_status" id="occupation_status"  class="twidth">
                    <option value="全部">全部</option>
                    <option value="0">不在职</option>
                    <option value="1">在职</option>
                </select>
            </td>

             <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.student_distribution') }}}</th>
             <td class="width150 col-md-1">
                <select size="1"  name="student_distribution" id="student_distribution" class="twidth">
                    <option value="全部">全部</option>
                    <option value="0">城镇应届</option>
                    <option value="1">农村应届</option>
                    <option value="2">城镇往届</option>
                    <option value="3">农村往届</option>
                    <option value="4">工人</option>
                    <option value="5">干部</option>
                    <option value="6">服役军人</option>
                    <option value="7">台籍青年</option>
                    <option value="8">港澳台侨</option>
                    <option value="9">其他</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <td class="width150 col-md-1">
                <select size="1"  name="major" id="major" class="twidth">
                    <option value="全部">全部</option>
                    @foreach ($b_majors as $major)
                    <option value="{{{ $major }}}"> {{{ $major }}} </option>
                    @endforeach
                    @foreach ($z_majors as $major)
                    <option value="{{{ $major }}}"> {{{ $major }}} </option>
                    @endforeach
                </select>
            </td>

            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <td class="width150 col-md-1">
                <select size="1"  name="major_classification" id="major_classification" class="twidth">
                    <option value="全部">全部</option>
                    <option value="14">专科</option>
                    <option value="12">本科</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
            <td class="width150 col-md-1">
                <select id="politicalstatus" name="politicalstatus" class="twidth">
                    <option value="全部">全部</option>
                    <option value="1">中共党员</option>
                    <option value="2">共青团员</option>
                    <option value="3">民革会员</option>
                    <option value="4">民盟盟员</option>
                    <option value="5">民进会员</option>
                    <option value="6">民建会员</option>
                    <option value="7">农工党党员</option>
                    <option value="8">致公党党员</option>
                    <option value="9">九三学社社员</option>
                    <option value="10">台盟盟员</option>
                    <option value="11">无党派民主人士</option>
                    <option value="12">群众</option>
                    <option value="13">其他</option>
                </select>
            </td>

            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.age_range') }}}</th>
            <td class="width150 col-md-1">
               {{{ Lang::get('admin/admissions/table.start_age') }}} <input type="text" id="start_age"  style="width: 60px;"> &nbsp;&nbsp;&nbsp;&nbsp;{{{ Lang::get('admin/admissions/table.end_age') }}} <input type="text" id="end_age"  style="width: 60px;">
            </td>
        </tr>
        <tr>
            <th class="col-md-1 width150">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
            <td class="width150 col-md-1">
                <select size="1"  name="student_type" id="student_type"  class="twidth">
                    <option value="全部">全部</option>
                    <option value="11">学历</option>
                    <option value="12">课程</option>
                </select>
            </td>

            <th class="col-md-1" style="width: 150px;"></th>
            <td style="width: 150px;"></td>
        </tr>
    </table>
    <div align="center">
        <button class="btn btn-small btn-info" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
    </div>

    <br>

    <div style="display: none;">
    <table id="tbNone">
        <thead>
            <tr>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
    <table id="count" class="table table-striped table-hover">
        <tr>
            <th class="col-md-04">{{{ Lang::get('admin/admissions/table.total_number') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.start_year') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.start_semester') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.end_year') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.end_semester') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.former_level') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.occupation_status') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.student_distribution') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.start_age') }}}</th>
            <th class="col-md-02">{{{ Lang::get('admin/admissions/table.end_age') }}}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
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
        width: 5%;
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

        var oTable = $('#tbNone').dataTable();
        var tableTools = new $.fn.dataTable.TableTools( oTable, {
            "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                    "sButtonClass":"btn btn-small btn-info",
                    "sFileName": "{{{ Lang::get('admin/admissions/title.admissions_information_count') }}}.xls",
                    "mColumns": "visible",
                    "fnClick": function( nButton, oConfig, flash ) {
                        var obj = this;
                        var regex = new RegExp(oConfig.sFieldBoundary, "g"); /* Do it here for speed */

                        var aRow, aData=[];

                        $("#count tr").each(function(i) {
                            if (i == 0) {
                                aRow = [];
                                $(this).children("th").each(function(){
                                    aRow.push(obj._fnBoundData( $(this).text(), oConfig.sFieldBoundary, regex));
                                });
                                aData.push( aRow.join(oConfig.sFieldSeperator) );
                            }
                            else if (i > 0) {
                                aRow = [];
                                $(this).children("td").each(function(){
                                    aRow.push(obj._fnBoundData( $(this).text(), oConfig.sFieldBoundary, regex));
                                });
                                aData.push( aRow.join(oConfig.sFieldSeperator) );
                            }
                        });
                        var str = aData.join( this._fnNewline(oConfig) );
                        this.fnSetText( flash, str);
                    }
                }
            ]
        } );
        $( tableTools.fnContainer() ).insertAfter('#lbl');

        $(document).ready(function() {
            $("#btnQuery").click(function(){
                $("#count tr:eq(1) td:eq(0)").html('');
                var start_year = $('#start_year').val();
                var start_semester = $('#start_semester').val();
                var end_year = $('#end_year').val();
                var end_semester = $('#end_semester').val();
                var school_id = $('#school').val();
                var campus_id = $('#campus').val();
                var admission_state = $('#admission_state').val();
                var gender = $('#gender').val();
                var former_level = $('#former_level').val();
                var nationgroup = $('#nationgroup').val();
                var occupation_status = $('#occupation_status').val();
                var student_distribution = $('#student_distribution').val();
                var major = $('#major').val();
                var major_classification = $('#major_classification').val();
                var politicalstatus = $('#politicalstatus').val();
                var student_type = $('#student_type').val();
                var start_age = $('#start_age').val();
                var end_age = $('#end_age').val();

                var tdCount;
                $("#count tr").each(function(i) {
                    if (i == 1) {
                        tdCount = $(this).find("td").eq(0);
                        $(this).find("td").eq(1).html($('#start_year').find("option:selected").text());
                        $(this).find("td").eq(2).html($('#start_semester').find("option:selected").text());
                        $(this).find("td").eq(3).html($('#end_year').find("option:selected").text());
                        $(this).find("td").eq(4).html($('#end_semester').find("option:selected").text());
                        $(this).find("td").eq(5).html($('#student_type').find("option:selected").text());
                        $(this).find("td").eq(6).html($('#major_classification').find("option:selected").text());
                        $(this).find("td").eq(7).html($('#campus').find("option:selected").text());
                        $(this).find("td").eq(8).html($('#major').find("option:selected").text());
                        $(this).find("td").eq(9).html($('#admission_state').find("option:selected").text());
                        $(this).find("td").eq(10).html($('#former_level').find("option:selected").text());
                        $(this).find("td").eq(11).html($('#nationgroup').find("option:selected").text());
                        $(this).find("td").eq(12).html($('#occupation_status').find("option:selected").text());
                        $(this).find("td").eq(13).html($('#student_distribution').find("option:selected").text());
                        $(this).find("td").eq(14).html($('#politicalstatus').find("option:selected").text());
                        $(this).find("td").eq(15).html($('#gender').find("option:selected").text());
                        $(this).find("td").eq(16).html($('#start_age').val());
                        $(this).find("td").eq(17).html($('#end_age').val());
                    }
                });

                var jsonData = {
                    "start_year": start_year,
                    "start_semester": start_semester,
                    "end_year": end_year,
                    "end_semester": end_semester,
                    "school_id": school_id,
                    "campus_id": campus_id,
                    "admission_state": admission_state,
                    "gender": gender,
                    "former_level": former_level,
                    "nationgroup": nationgroup,
                    "occupation_status": occupation_status,
                    "student_distribution": student_distribution,
                    "major": major,
                    "major_classification": major_classification,
                    "politicalstatus": politicalstatus,
                    "student_type": student_type,
                    "start_age": start_age,
                    "end_age": end_age
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/admissions/information_count_data') }}',
                    async: true,
                    dataType: "json",
                    data: jsonData,
                    success: function (json) {
                        if (tdCount != null)
                            tdCount.html(json);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        if (tdCount != null)
                            tdCount.html('error');
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