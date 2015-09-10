@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
        <div class="pull-right">
            <a href="{{{ URL::to('admin/exemption/exemption_major_outer') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_major_outer') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/exemption/exemption_type') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_type') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/exemption/exemption_agency') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_agency') }}}</a>&nbsp;&nbsp;
        </div>
        <br>
    </div>

   {{-- choose input form --}}
          <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <input id="selectedExemptions" name="selectedExemptions" type="hidden" value="" />
        <div class="form-group" align="center">
            <h3>
                {{{ Lang::get('admin/exemption/title.choose_query') }}}
            </h3>
        </div>

        <div class="form-group" align="center">
            <label for="student_id" class="rlbl" >{{ Lang::get('admin/exemption/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">

            <label for="student_name" class="rlbl">{{ Lang::get('admin/exemption/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">
        </div>
                <div class="form-group" align="center">
                    <label for="start_year" class="rlbl" >起始时间</label>
                    <select name="start_year" id="start_year" style="width:85px;">
                        <option value="0">请选择</option>
                        @for ($i=2000;$i<2050;$i++)
                            <option value="{{{$i}}}">{{$i}}</option>
                        @endfor
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <select name="start_semester" id="start_semester" style="width:85px;">
                        <option value="">请选择</option>
                        <option value="02">秋季</option>
                        <option value="01">春季</option>
                    </select>
                    <label for="result" class="rlbl" >{{{ Lang::get('admin/exemption/table.final_result') }}}</label>
                    <select name="result" id="result" style="width:200px;">
                            <option value="3">全部</option>
                            <option value="0">不通过</option>
                            <option value="1">通过</option>
                            <option value="2">未审核</option>
                    </select>
                </div>
                <div class="form-group" align="center"  >
                    <label for="terminal_year" class="rlbl" >至终止时间</label>
                    <select name="terminal_year" id="terminal_year" style="width:85px;">
                        <option value="3000">请选择</option>
                        @for ($i=2000;$i<2050;$i++)
                          <option value="{{{$i}}}">{{$i}}</option>
                        @endfor
                    </select>
                &nbsp;&nbsp;&nbsp;&nbsp;
                    <select name="terminal_semester" id="terminal_semester" style="width:85px;">
                        <option value="">请选择</option>
                        <option value="02">秋季</option>
                        <option value="01">春季</option>
                    </select>
                <label for="campus" class="rlbl">{{{ Lang::get('admin/exemption/table.campus') }}}</label>
                <select name="campus" id="campus" id="campus" style="width:200px;">
                        <option value="全部">全部</option>
                        @foreach($campuses as $campus)
                            <option value="{{$campus->id}}">{{$campus->name}}</option>
                        @endforeach
                    </select>
                </div>
            <div class="form-group" align="center">
                <label for="student_classification" class="rlbl" >{{{ Lang::get('admin/exemption/table.student_classification') }}}</label>
                <select name="student_classification" id="student_classification" style="width:200px;">
                    <option value="">全部</option>
                    <option value="11">学历</option>
                    <option value="12">课程</option>
                </select>
                    <label for="major_classification" class="rlbl" >{{{ Lang::get('admin/exemption/table.major_classification') }}}</label>
                <select name="major_classification" id="major_classification" style="width:200px;">
                        <option value="全部">全部</option>
                        <option value="14">专科</option>
                        <option value="12">本科</option>
                    </select>
                </div>
        <div  align="center">
            <button id="btnQuery" type="submit" name="state" value="3" class="btn btn-small btn-info" >查询</button>
        </div>
    <br>
    <br>

    <div align="center">
        {{{ Lang::get('admin/exemption/title.exemption_info') }}}
    </div>
    <br>
        <!-- ./ csrf token -->
        <table id="exemption" class="table table-striped table-hover table-bordered"  align="center">
            <thead>
            <tr>
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
                <th>{{{ Lang::get('admin/exemption/table.multi-results') }}}
                    <br>
                    <label id="select_all"><a style="cursor: hand">全选</a></label>
                    <br>
                    <label id="select_none"><a style="cursor:hand">全否</a></label>
                </th>
            </tr>
            </thead>
        </table>
        <div  align="center">
            <button id="btnPass" type="submit" name="state" value="1" class="btn btn-small btn-info" disabled="disabled" >通过</button>
        </div>

    <div id="show">
        <input type="hidden" id="btnValue" value="3" />
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:80px;

        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function checkyear(){
            $start_year = $('#start_year').val();
            $terminal_year = $('#terminal_year').val();
            if (($start_year!='') && ($terminal_year!=''))
            {
                if ($start_year > $terminal_year ){
                    alert('起始时间需小于终止时间，请重新选择！');
                    return false;
                }
            }
            return true;
        }
        function countCheckedBoxes() {
            var n = $( "input:checked" ).length;
            if (n>0) {
                $("#btnPass").attr("disabled", false);
                for (i=0;i<$( "input:checked" ).length;i++) {
                    if (i==0) {
                          $("#selectedExemptions").val($( "input:checked" )[i].value);
                    } else {
                        $("#selectedExemptions").val($("#selectedExemptions").val() + ',' + $( "input:checked" )[i].value);
                    }
                }
            } else {
                $("#btnPass").attr("disabled", true);
            }
        }
        function checkTime(){
            var start_year = $('#start_year').val();
            var terminal_year =$('#terminal_year').val();
            if (!empty(start_year) && !empty(terminal_year)){
                if (start_year > terminal_year ){
                    alert('起始时间小于终止时间');
                    return false;
                }
            }
            return true;
        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#exemption').dataTable( {
                "searching":false,
                "ordering": false,
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
                    "url": "{{{ URL::to('admin/exemption/data_exemption_province') }}}",
                    "data": function ( d ) {
                        d["selectedExemptions"]= $('#selectedExemptions').val();
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["result"]=$('#result').val();
                        d["start_year"]= $('#start_year').val();
                        d["start_semester"]=$('#start_semester').val();
                        d["terminal_year"]= $('#terminal_year').val();
                        d["terminal_semester"]=$('#terminal_semester').val();
                        d["campus"]=$('#campus').val();
                        d["student_classification"]=$('#student_classification').val();
                        d["final_result"] = $('#btnValue').val();
                    }
                },

                "aaSorting": [ [0,'asc'] ]
            });
            oTable.on('draw', function() {
                $('#checkItem').change(countCheckedBoxes);
            });
            $(function () {
                $("#btnPass").click(function(){
                    $("#btnValue").val(1);
                    oTable.fnReloadAjax();
                });

                $("#btnQuery").click(function(){
                    $("#btnValue").val(3);
                    if (checkyear()){
                        oTable.fnReloadAjax();
                    }
                });
                $("#btnNoCheck").click(function(){
                    $("#btnValue").val(2);
                    oTable.fnReloadAjax();
                });


                $('#select_all').click(function () {
                    $(':checkbox').prop("checked", true);
                    countCheckedBoxes();
                });
                $('#select_none').click(function () {
                    $(':checkbox').prop("checked", false);
                    countCheckedBoxes();
                });
            });

        });

    </script>
@stop