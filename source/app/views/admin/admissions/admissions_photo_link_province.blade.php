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


        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
    <div>
        <table class="table table-striped table-hover table-bordered" width="600px" align="center" >
    <tr>
        <td>链接方式：</td>
        <td><input type="radio" id="link_type" value="1">按身份证号链接</td>
        <td><input type="radio" id="link_type" value="2">按学号链接</td>
    </tr>
</table>
        <br>
        <br>
   <div align="center">
        照片路径&nbsp;&nbsp;<input type="text" id="directory" width="100px">&nbsp;&nbsp;
        <button id="btnSelect" name="state" value="1" class="btn btn-small btn-info" >选择</button>
    </div>
        <table align="center" width="600px" class="table table-striped table-hover table-bordered" >
            <tr>
                <td colspan="4"><h4>请选择核对毕业生数据参数</h4></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/admissions/table.graduated_year') }}}</td>
                <td>
                    <select name="year" id="year" style="width:80px;">
                        <option value="">全部</option>
                        @for ($i=2000;$i<2025;$i++)
                            <option value="{{{$i}}}">{{$i}}</option>
                        @endfor
                    </select>
                </td>
                <td>{{{ Lang::get('admin/admissions/table.graduated_semester') }}}</td>
                <td>
                    <select name="semester" id="semester" style="width:80px;">
                        <option value="">全部</option>
                        <option value="1">春季</option>
                        <option value="0">秋季</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/admissions/table.student_type') }}}</td>
                <td>
                    <select name="student_type" id="student_type" style="width:80px;">
                        <option value="">全部</option>
                        <option value="12">本科</option>
                        <option value="14">专科</option>
                    </select>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/admissions/table.school') }}}</td>
                <td>
                    <select name="school" id="school" style="width:80px;">
                        <option value="">全部</option>
                        @foreach($schools as $school)
                            <option value="{{{$school->id}}}">{{$school->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>{{{ Lang::get('admin/admissions/table.campus') }}}</td>
                <td>
                    <select name="campus" id="campus" style="width:80px;">
                        <option value="">全部</option>
                        @foreach($campuses as $campus)
                            <option value="{{{$campus->id}}}">{{$campus->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/admissions/table.major_classification') }}}</td>
                <td>
                    <select name="major_classification" id="major_classification" style="width:80px;">
                        <option value="">全部</option>
                        <option value="12">本科</option>
                        <option value="14">专科</option>
                    </select>
                </td>
                <td>学位终审通过</td>
                <td>
                    <select name="final_result" id="final_result" style="width:80px;">
                        <option value="">全部</option>
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </td>
            </tr>
        </table>
        <table class="table table-striped table-hover table-bordered">
            <tr>
                <td><button type="submit" class="btn btn-default" value="2" id="btnRequire">参数确认</button></td>
                <td>待检测人数：<label id="wait_for_approvol">&nbsp;</label></td>
            </tr>
        </table>
            <div align="center">
                    <!-- Form Actions -->
                <button type="submit" class="btn btn-default" value="3" id="btnApproval">开始检测</button>
                    <!-- ./ form actions -->
            </div>
    </div>
    <br><br><input type="hidden" id="btnValue" value="1" />
        <div id="show_info">
            <table id="results" class="table table-striped table-hover table-bordered">
              <tr><td><h3>{{Lang::get('admin/admissions/table.photo_link_results')}}</h3></td></tr>
              <tr>
                  <td align="center">{{Lang::get('admin/admissions/table.success_number')}}</td>
                  <td align="center"><label id="success_number"></label></td>
              </tr>
                <tr>
                    <td align="center">{{Lang::get('admin/admissions/table.failure_number')}}</td>
                    <td align="center"><label id="failure_number"></label></td>
                </tr>
            </table>
                <div class="form-group" align="center">
                    <button id="btnResult" name="state" value="4" class="btn btn-small btn-info" >{{Lang::get('admin/admissions/table.result_details')}}</button>
                </div>
        </div>
          <div id="result_details">
             <div align="center">{{Lang::get('admin/admissions/table.photo_link_results')}}</div>
              <table id="details" class="table table-striped table-hover table-bordered">
                  <thead>
                  <th class="col-md-1" class="width20">{{{ Lang::get('admin/admissions/table.id') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.ID_number') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.photo_file_name') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.school') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
                  <th class="col-md-1">{{{ Lang::get('admin/admissions/table.results') }}}</th>
                  </thead>
              </table>
              <div align="center">
                  <button id="btnSave" name="state" value="5" class="btn btn-small btn-info" >以excel的方式导出</button>
              </div>
          </div>
@stop

@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:120px;

        }
        .width20{
            text-align:center;
            width:20px;
        }
        .width200{
            text-align:center;
            width:200px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#details').dataTable( {
                "searching":false,
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
                    "url": "{{{ URL::to('admin/admissions/data_photo_results_details') }}}",
                    "data": function ( d ) {
                        d["year"]= $('#year').val();
                        d["group"]=$('#group').val();
                        d["student_type"]=$('#student_type').val();
                        d["examination_point"]=$('#examination_point').val();
                        d["group_name"]=$('#group_name').val();
                        d["group_admin"]=$('#group_admin').val();
                        d["state"]=$('#btnValue').val();
                    }

                },
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});

                    if ( oSettings.bSorted || oSettings.bFiltered || oSettings.bDrawing)
                    {
                        for ( var i=0, iLen=oSettings.aiDisplayMaster.length ; i<iLen ; i++ )
                        {
                            var counter = oSettings._iDisplayStart + i + 1;
                            $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( counter );
                        }
                    }
                },
                "aaSorting": [ [0,'asc'] ]
            });
        $(function(){
            $('#show_info').hide();
            $('#result_details').hide();
            $("#btnApproval").click(function(){
                $("#btnValue").val(3);
                $('#show_info').show();
            });
            $("#btnResult").click(function(){
                $("#btnValue").val(4);
                $('#result_details').show();
                oTable.fnReloadAjax();
            });
        });
    });
    </script>
 @stop
