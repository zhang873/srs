@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
        <div class="pull-right">

            <a href="{{{ URL::to('admin/exemption/input_student') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/exemption/title.input_student') }}}</a>

        </div>
        <br>
    </div>

   {{-- choose input form --}}
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <div class="form-group" align="center">
            <h3>
                {{{ Lang::get('admin/exemption/title.choose_query') }}}
            </h3>
        </div>
        <div class="form-group" align="center" width="600px">
            <label for="student_id" class="rlbl" >{{ Lang::get('admin/exemption/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">

            <label for="student_name" class="rlbl">{{ Lang::get('admin/exemption/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">
        </div>
        <div class="form-group" align="center"  width="600px">
            <label for="major_classification" class="rlbl" >{{ Lang::get('admin/exemption/table.major_classification') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <select name="major_classification" id="major_classification" style="width:200px;">
                <option value="2">全部</option>
                <option value="14">专科</option>
                <option value="12">本科</option>
            </select>
            <label for="final_result" class="rlbl">{{ Lang::get('admin/exemption/table.final_result') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <select name="final_result" id="final_result" style="width:200px;">
                <option value="3">全部</option>
                <option value="0">不通过</option>
                <option value="1">通过</option>
                <option value="2">未审核</option>
            </select>
        </div>
        <div class="form-group" align="center"  width="600px">
            <label for="application_year" class="rlbl" >{{ Lang::get('admin/exemption/table.input_year') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <select name="application_year" id="application_year" style="width:200px;">
                <option value="全部">全部</option>
                @for ($i=2000;$i<2025;$i++)
                    <option value="{{{$i}}}">{{$i}}</option>
                @endfor
            </select>
            <label for="application_semester" class="rlbl">{{ Lang::get('admin/exemption/table.input_semester') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <select name="application_semester" id="application_semester" style="width:200px;">
                <option value="2">全部</option>
                <option value="0">秋季</option>
                <option value="1">春季</option>
            </select>
        </div>
        <div class="form-group" align="center"  width="600px">
            <label for="major" class="rlbl" >{{ Lang::get('admin/exemption/table.major_inside') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <select name="major" id="major" style="width:200px;">
                <option value="全部">全部</option>
                @foreach($rawprograms as $rawprogram)
                    <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
                @endforeach
            </select>
            <label for="student_classification" class="rlbl">{{ Lang::get('admin/exemption/table.student_classification') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
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
        {{{ Lang::get('admin/exemption/title.exemption_info') }}}
    </div>
        <table id="exemption" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/exemption/table.class_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.major_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.campus') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_inside') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_classification') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.credit') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.exemption_year') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.exemption_type_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.major_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.classification_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.credit_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.certification_year') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.agency_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.final_results') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.remark') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.action') }}}</th>
            </tr>
            </thead>

        </table>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop

@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:200px;

        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#exemption').dataTable( {
                "searching":false,
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_",
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
                     "url": "{{{ URL::to('admin/exemption/data') }}}",
                     "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["final_result"]=$('#final_result').val();
                        d["application_year"]= $('#application_year').val();
                        d["application_semester"]=$('#application_semester').val();
                        d["major"]=$('#major').val();
                        d["student_classification"]=$('#student_classification').val();
                       // d["state"] == $('#btnValue').val();
                    }
                },

                "aaSorting": [ [0,'asc'] ]
            });

            $("#btnQuery").click(function(){
                $("#btnValue").val(2);
                oTable.fnReloadAjax();
            });
        });

    </script>
@stop