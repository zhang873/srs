@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop


{{-- Content --}}
@section('content')
    {{-- Create User Form --}}

    <!-- ./ csrf token -->

    <div class="form-group" align="center">
        <h4>
            {{{ Lang::get('admin/unified_exam/title.input_admissions')}}}
        </h4>
    </div>


    <div class="form-group" align="center" width="600px">
        <label for="student_id" class="rlbl" >{{ Lang::get('admin/unified_exam/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:120px;">

        <label for="student_name" class="rlbl">{{ Lang::get('admin/unified_exam/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:120px;">
    </div>
    <div  class="form-group" align="center">
        <button id="btnQuery" name="btnQuery" value="1" class="btn btn-small btn-info" type="submit">{{{ Lang::get('admin/unified_exam/table.query') }}}</button>
    </div>
    <br>
    <br>
    <div id="selectStudent">
        <div align="center">
            <h4>
                {{{ Lang::get('admin/unified_exam/title.choose_admissions') }}}
            </h4>
        </div>
        <form id="choose_input" class="form-horizontal" method="post" action="{{URL::to('admin/unified_exam/student_selection') }}" autocomplete="off">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <!-- ./ csrf token -->

            <table id="student_id_name" class="table table-striped table-hover table-bordered" align="center" style="width: 400px;">
                <thead>
                <tr>
                    <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.input_flag') }}}</th>
                </tr>
                </thead>
            </table>
            <div  class="form-group" align="center">
                <button id="btnInput" name="btnInput" value="2" class="btn btn-small btn-info" type="submit" >{{{ Lang::get('admin/unified_exam/table.input_score') }}}</button>
            </div>
        </form>
    </div>
    <input type="hidden" id="btnValue" value="1" />
@stop


{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        function check() {
            var n = $( "input:checked" ).length;
            if (n==0) {
                alert('请选择待录入学生');
                $('#ids').focus();
                return false;
            }
            return true;
        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#student_id_name').dataTable({
                "searching": false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/unified_exam/table.records_per_page') }}} _MENU_",
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
                    "url": "{{{ URL::to('admin/unified_exam/data_student') }}}",
                    "data": function (d) {
                        d["student_id"] = $('#student_id').val();
                        d["student_name"] = $('#student_name').val();
                        d["ids"] = $('ids').val();
                    }
                },
                "aaSorting": [[0, 'asc']]
            });
            $(function () {
                $("#selectStudent").hide();
                $("#btnQuery").click(function () {
                    $('#selectStudent').show();
                    $('btnValue').val(1);
                    oTable.fnReloadAjax();
                });
                $("#btnInput").click(function () {
                    if (check()) {
                        $.post("{{URL::to('admin/exemption/student_selection') }}",{ids:$('#ids').val()});
                        return true;
                    }else{
                        return false;
                    }
                });
            });
        });
    </script>
@stop