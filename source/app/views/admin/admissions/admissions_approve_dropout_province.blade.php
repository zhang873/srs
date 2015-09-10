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
        <h4>
            {{Lang::get('admin/admissions/table.query_input')}}
        </h4>
    </div>
    {{-- choose input form --}}
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <input id="selectedAdmissions" name="selectedAdmissions" type="hidden" value="" />
    <div class="form-group" align="center" width="700px">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_name') }}</label>
        <input tabindex="1" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:150px;">

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:150px;">
    </div>
    <div class="form-group" align="center" width="700px">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.major_classification') }}</label>
        <select size="1" tabindex="3" name="major_classification" id="major_classification" style="width:150px;">
            <option value="">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>

        <label class="rlbl">{{ Lang::get('admin/admissions/table.major') }}</label>
        <select size="1" tabindex="4" name="major" id="major" style="width:150px;">
            <option value="">全部</option>
            @foreach($majors as $major)
                <option value="{{$major->id}}">{{$major->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center" width="700px">
        <label class="rlbl">{{ Lang::get('admin/admissions/table.campus') }}</label>
        <select size="1" tabindex="5" name="campus" id="campus" style="width:150px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>
        <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
    </div>

    <div class="form-group" align="center">

        <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" type="submit">
            {{{ Lang::get('admin/admissions/table.query') }}}</button>

    </div>
    <br><br>

    <div id="show_info" align="center">
        <table id="withdrawal_info" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.source_major') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.withdrawal_cause') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.province_final_result') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.province_final_device') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.action') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.multi-results') }}}
                    <br>
                    <label id="checkAll"><a style="cursor: hand">全选</a></label>
                    <br>
                    <label id="checkNo"><a style="cursor:hand">全否</a></label>
                </th>
            </tr>
            </thead>
        </table>
        <div align="center">
            <!-- Form Actions -->
            <button type="submit" class="btn btn-default" value="1" id="btnAgree">{{{ Lang::get('button.agree') }}}</button>
            <!-- ./ form actions -->
        </div>
        <div>
            <input type="hidden" id="btnValue" value="2">
        </div>
    </div>

@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:150px;

        }
        .rtxt{
            text-align:left;
            width:120px;
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
        var oTable;
        $(document).ready(function() {
            oTable = $('#withdrawal_info').dataTable({
                "searching": false,
                "ordering" : false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_",
                    "sProcessing": "正在加载中......",
                    "sZeroRecords": "没有数据！",
                    "sEmptyTable": "表中无数据存在！",
                    "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
                    "sInfoEmpty": "显示0到0条记录",
                    "sInfoFiltered": "数据表中共为 _MAX_ 条记录",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "上一页",
                        "sNext": "下一页",
                        "sLast": "末页"
                    }
                },
                "bFilter": true,
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth": true,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_admissions_approve_dropout') }}}",
                    "data": function (d) {
                        d["selectedAdmissions"]= $('#selectedAdmissions').val();
                        d["student_name"] = $('#student_name').val();
                        d["student_id"] = $('#student_id').val();
                        d["year"] = $('#year').val();
                        d["semester"] = $('#semester').val();
                        d["major_classification"] = $('#major_classification').val();
                        d["major"] = $('#major').val();
                        d["campus"] = $('#campus').val();
                        d["cause"] = $('#cause').val();
                        d["state"] = $('#btnValue').val();
                    }
                },
                "aaSorting": [[0, 'asc']]
            });
            $(function() {
                $("#btnQuery").click(function () {
                    $('#btnValue').val(2);
                    oTable.fnReloadAjax();
                });
                $("#btnAgree").click(function () {
                    $('#btnValue').val(1);
                    oTable.fnReloadAjax();
                });
                $('#checkAll').click(function () {

                    $(':checkbox').prop("checked", true);
                    countCheckedBoxes();
                });
                $('#checkNo').click(function () {

                    $(':checkbox').prop("checked", false);
                    countCheckedBoxes();
                });
            });
        });
    </script>
@stop
