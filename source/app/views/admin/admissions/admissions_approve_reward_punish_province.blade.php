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
            {{Lang::get('admin/admissions/table.query_input')}}
        </h3>
    </div>

    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <input type="hidden" name="selectedAdmissions" id="selectedAdmissions" />
    <div class="form-group" align="center">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_name') }}</label>
        <input tabindex="1" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>
    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.major_classification') }}</label>
        <select size="1" tabindex="5" name="major_classification" id="major_classification" style="width:200px;">
            <option value="">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.major') }}</label>
        <select size="1" tabindex="4" name="major" id="major" style="width:200px;">
            <option value="">全部</option>
            @foreach($rawprograms as $rawprogram)
                <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.school') }}</label>
        <select size="1" tabindex="5" name="school" id="school" style="width:200px;">
            <option value="">全部</option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->school_name}}</option>
            @endforeach
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.campus') }}</label>
        <select size="1" tabindex="4" name="campus" id="campus" style="width:200px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center">
        <label class="rlbl">年度</label>
        <select name="year" id="year" style="width:200px;">
            <option value="">全部</option>
            @for ($i=2000;$i<2025;$i++)
                <option value="{{{$i}}}">{{$i}}</option>
            @endfor
        </select>

        <label class="rlbl">学期</label>
        <select name="semester"  id="semester" style="width:200px;">
            <option value="">请选择</option>
            <option value="02">秋季</option>
            <option value="01">春季</option>
        </select>
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/admissions/table.query') }}}</button>

    </div>
    <br><br>

    <table id="admissions" class="table table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.punish_code') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.punish_cause') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.award_type') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.remark') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.fileNo') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.actor') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/admissions/table.final_result') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.remark') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/admissions/table.action') }}}</th>
            <th class="col-md-3">
                {{{ Lang::get('admin/admissions/table.multi-results') }}}
                <br>
                <label id="select_all"><a style="cursor: hand">全选</a></label>
                <br>
                <label id="select_none"><a style="cursor:hand">全否</a></label>
            </th>

        </tr>
        </thead>

    </table>
    <div align="center">
        <button id="btnPass" name="btnPass" value="1" class="btn btn-small btn-info" disabled="disabled" >同意</button>
    </div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:120px;

        }
        th{
            text-align: center;
            font-size: 14px;
        }
        td{
            text-align: center;
            font-size: 14px;
        }
        .col-md-1{
            text-align:center;
            width:20px;

        }
        .col-md-2{
            text-align:center;
            width:50px;

        }
        .col-md-3{
            text-align:center;
            width:80px;

        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function countCheckedBoxes() {
            var n = $( "input:checked" ).length;
            if (n>0) {
                $("#btnPass").attr("disabled", false);
                for (i=0;i<$( "input:checked" ).length;i++) {
                    if (i==0) {
                        $("#selectedAdmissions").val($( "input:checked" )[i].value);
                    } else {
                        $("#selectedAdmissions").val($("#selectedAdmissions").val() + ',' + $( "input:checked" )[i].value);
                    }
                }
            } else {
                $("#btnPass").attr("disabled", true);
            }
        }
        function getSelectVal(){
            $.getJSON("{{URL::to('admin/admissions/getCampuses')}}",{school:$("#school").val()},function(json){
                var campus = $("#campus");
                $("option",campus).remove();  //清空原有的选项
                $.each(json,function(index,array){
                    var option = "<option value='"+array['id']+"'>"+array['name']+"</option>";
                    campus.append(option);
                });
            });
        }
        var oTable;
        $('#admissions').delegate("#checkItem", "change",function(){
            var ck = $(this).is(':checked');
            if (ck === true){
                var tr = $(this).parent().parent().parent();
                var tdApprovalStatus = tr.find("td:eq(12)");
                var approval_status = $.trim(tdApprovalStatus.html());
                if (approval_status != "未审核"){
                    $(this).prop("checked",false);
                    alert("已审批");
                    return false;
                }
            }
            countCheckedBoxes();
        });
        $('#admissions').delegate("#noPass", "click",function(){
            var id = $(this).attr("value");
            var tr = $(this).parent().parent();
            if (tr != null){
                var tdRemark = tr.find("td:eq(13)");
                var tdApprovalStatus = tr.find("td:eq(12)");
                var approval_status = $.trim(tdApprovalStatus.html());
                if (approval_status != "未审核"){
                    alert("已审批");
                    return false;
                }
                var remark = tdRemark.find("#remark").val();
                var jsonData = {
                    "id": id,
                    "remark": remark
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/admissions/approve_reward_punish_nopass') }}',
                    async: false,
                    data: jsonData,
                    success: function (json) {
                        if (json == 'ok'){
                            tdApprovalStatus.html("不同意");
                            alert('审批成功');
                        }
                    }
                });
            }
        });
        $('#admissions').delegate("#noCheck", "click",function(){
            var id = $(this).attr("value");
            var tr = $(this).parent().parent();
            if (tr != null){
                var tdApprovalStatus = tr.find("td:eq(12)");
                var approval_status = $.trim(tdApprovalStatus.html());
                if (approval_status == "未审核"){
                    return false;
                }

                var jsonData = {
                    "id": id
                };
                $.ajax({
                    type: 'GET',
                    url: '{{ URL::to('admin/admissions/approve_reward_punish_no_approve') }}',
                    async: false,
                    data: jsonData,
                    success: function (json) {
                        if (json == 'ok'){
                            tdApprovalStatus.html("未审核");
                            alert('修改为未审核状态成功');
                        }
                    }
                });
            }
        });
        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "searching":false,
                "ordering":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_",
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
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_approve_reward_punish') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["campus"]=$('#campus').val();
                        d["school"]=$('#school').val();
                        d["year"]=$('#year').val();
                        d["semester"]=$('#semester').val();
                        d["state"]=$('#btnValue').val();
                        d["selectedAdmissions"]=$('#selectedAdmissions').val();
                    }

                },

                "aaSorting": [ [0,'asc'] ]
            });
            $(function(){
                getSelectVal();
                $("#school").change(function(){
                    getSelectVal();
                });
                $("#btnQuery").click(function(){
                    $("#btnValue").val(2);
                    oTable.fnReloadAjax();
                });
                $('#select_all').click(function () {
                    $("#admissions tr").each(function(i) {
                        if (i > 0) {
                            var checkbox = $(this).find(":checkbox");
                            if (checkbox == null)
                                return;
                            var tdApprovalStatus = $(this).find("td:eq(12)");
                            var approval_status = $.trim(tdApprovalStatus.html());
                            if (approval_status == "未审核"){
                                checkbox.prop("checked",true);
                            }
                        }
                    });
                    countCheckedBoxes();
                });
                $('#select_none').click(function () {
                    $(':checkbox').prop("checked", false);
                    countCheckedBoxes();
                });
                $("#btnPass").click(function(){
                    $("#btnValue").val(1);
                    oTable.fnReloadAjax();
                    alert('审批成功');
                    $(this).attr("disabled", true);
                });
            });

        });

    </script>
@stop；