@extends('admin.layouts.default')

{{-- Web site Title --}}
{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
            <div class="pull-right">

                <a href="{{{ URL::to('admin/unified_exam/select_type_save') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/unified_exam/table.select_student_input') }}}</a>&nbsp;&nbsp;

                <a href="{{{ URL::to('admin/unified_exam/input_student') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/unified_exam/title.input_unified_exam') }}}</a>&nbsp;&nbsp;
            </div>
            <br>

    </div>
    <br>
    <br>

    {{-- choose input form --}}
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <div class="form-group" align="center">
        <h3>
            {{{ Lang::get('admin/unified_exam/title.input_admissions') }}}
        </h3>
    </div>
    <div class="form-group" align="center"  width="600px">
        <label for="student_id" class="rlbl" >{{ Lang::get('admin/unified_exam/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">

        <label for="student_name" class="rlbl">{{ Lang::get('admin/unified_exam/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">
    </div>
    <div class="form-group" align="center"  width="600px">
        <label for="start_time" class="rlbl" >{{ Lang::get('admin/unified_exam/table.start_time') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="start_time" id="start_time"  style="width:200px;">
            <option value="0|0">请选择</option>
            @for ($i=2000;$i<=2025;$i++)
                @for ($j=1;$j<=2;$j++)
                    <option value="{{{$i}}}|{{{$j}}}">{{{$i}}}年@if ($j==2) <label>秋季</label> @elseif ($j==1) <label>春季</label>@endif</option>
                @endfor
            @endfor
        </select>
        <label for="terminal_time" class="rlbl" >{{ Lang::get('admin/unified_exam/table.terminal_time') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="terminal_time" id="terminal_time"  style="width:200px;">
            <option value="3000|0">请选择</option>
            @for ($i=2000;$i<=2025;$i++)
                @for ($j=1;$j<=2;$j++)
                    <option value="{{{$i}}}|{{{$j}}}">{{{$i}}}年@if ($j==2) <label>秋季</label> @elseif ($j==1) <label>春季</label>@endif</option>
                @endfor
            @endfor
        </select>
    </div>
    <div class="form-group" align="center"  width="600px">
        <label for="final_result" class="rlbl">{{ Lang::get('admin/unified_exam/table.final_result') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="final_result" id="final_result" style="width:200px;">
            <option value="">全部</option>
            <option value="0">不通过</option>
            <option value="1">通过</option>
            <option value="2">未审核</option>
        </select>
        <label for="campus" class="rlbl">{{ Lang::get('admin/unified_exam/table.campus') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="campus" id="campus" style="width:200px;">
            <option value="">全部</option>
            @foreach($campuses as $campus)
                <option value="{{$campus->id}}">{{$campus->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" align="center"  width="600px">
        <label for="major_classification" class="rlbl" >{{ Lang::get('admin/unified_exam/table.major_classification') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="major_classification" id="major_classification" style="width:200px;">
            <option value="">全部</option>
            <option value="14">专科</option>
            <option value="12">本科</option>
        </select>
        <label for="student_classification" class="rlbl">{{ Lang::get('admin/unified_exam/table.student_classification') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <select name="student_classification" id="student_classification" style="width:200px;">
            <option value="">全部</option>
            <option value="11">学历</option>
            <option value="12">课程</option>
        </select>
    </div>
    <div  class="form-group" align="center">
        <button type="submit" id="btnQuery" name="state" value="2" class="btn btn-small btn-info" >查询</button>
    </div>

    <br>
    <br>
    <br>
    <div align="center">
        {{{ Lang::get('admin/unified_exam/title.unified_exam_info') }}}
    </div>
        <table id="unified_exam" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th class="width150">{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th class="width80">{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th class="width100">{{{ Lang::get('admin/unified_exam/table.campus') }}}</th>
                <th class="width100">{{{ Lang::get('admin/unified_exam/table.class') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.input_year') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.unified_exam_course') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.unified_exam_cause') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.final_results') }}}</th>
                <th class="alignCenter">{{{ Lang::get('admin/unified_exam/table.action') }}}</th>
            </tr>
            </thead>
        </table>

@stop


@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:200px;

        }
        .alignCenter{
            text-align: center;
            alignment: center;
        }
        .width80{
            text-align: center;
            width:80px;
            alignment: center;

        }
        .width100{
            text-align: center;
            width:100px;
            alignment: center;

        }
        .width150{
            text-align: center;
            width:150px;
            alignment: center;
        }
    </style>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function checkyear(){
            $start_time = $('#start_time').val();
            $terminal_time = $('#terminal_time').val();
            if (($start_time!='') && ($terminal_time!=''))
            {
                $start_year = parseInt($start_time.substring(0,4));
                $end_year = parseInt($terminal_time.substring(0,4));
                if ($start_year > $end_year ){
                    alert('起始时间需小于终止时间，请重新选择！');
                    return false;
                }
            }
            return true;
        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#unified_exam').dataTable( {
                "searching":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/unified_exam/table.records_per_page') }}} _MENU_",
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
                    "url": "{{{ URL::to('admin/unified_exam/data') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["final_result"]=$('#final_result').val();
                        d["start_time"]= $('#start_time').val();
                        d["terminal_time"]= $('#terminal_time').val();
                        d["campus"]=$('#campus').val();
                        d["student_classification"]=$('#student_classification').val();
                    }
                },

                "aaSorting": [ [0,'asc'] ]
            });

            $("#btnQuery").click(function(){
                if (checkyear()){
                    oTable.fnReloadAjax();
                }
            });

        });

    </script>
@stop
