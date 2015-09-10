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
        <div class="pull-right">
            <a href="{{{ URL::to('admin/admissions/group_define') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.create_admin_group') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/group_edit') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.admin_group') }}}</a>
        </div>
        <br>
    </div>

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
    <div align="center">
        <h4>
            {{Lang::get('admin/admissions/table.query_input')}}
        </h4>
    </div>
    <div>
        <table id="admission_group"  class="table table-striped table-hover table-bordered" align="center" style="width:600px">
            <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <td style="width: 150px;">
                    <input type="text"  style="width:200px;" id="student_name" name="student_name">
                </td>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <td style="width: 120px;">
                    <input type="text"  style="width:200px;" id="student_id" name="student_id">
                </td>
            </tr>
                  <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
                    <td style="width: 150px;">
                        <select size="1" tabindex="5" name="student_type" id="student_type" style="width:150px;">
                            <option value="">全部</option>
                            <option value="11">学历</option>
                            <option value="12">课程</option>
                        </select>
                    </td>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                    <td style="width: 120px;">
                        <select size="1" tabindex="4" name="major" id="major" style="width:200px;">
                            <option value="">全部</option>
                            @foreach($rawprograms as $rawprogram)
                                <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="col-md-1" style="width: 150px;">学生{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
                    <td style="width: 150px;">
                        <select name="admissionyear" id="admissionyear" style="width:150px;">
                            <option value="">请选择</option>
                            @for ($i=2000;$i<2025;$i++)
                                <option value="{{{$i}}}">{{$i}}</option>
                            @endfor
                        </select>
                    </td>
                    <th class="col-md-1" style="width: 150px;">学生{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
                    <td style="width: 150px;">
                        <select name="admissionsemester" id="admissionsemester" style="width:150px;">
                            <option value="">请选择</option>
                            <option value="02">秋季</option>
                            <option value="01">春季</option>
                        </select>
                   </td>
                </tr>
            <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
                <td style="width: 150px;">
                    <select name="admission_state" id="admission_state" style="width:150px;">
                        <option value="3">未注册</option>
                        <option value="4" selected="selected">在籍</option>
                    </select>
                </td>
                <th class="col-md-1" style="width: 150px;"></th>
                <td style="width: 150px;"></td>
            </tr>
                <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.create_admin_group_year') }}}</th>
                    <td style="width: 150px;">
                        <select name="year" id="year" style="width:150px;">
                            <option value="">请选择</option>
                            @for ($i=2000;$i<2025;$i++)
                                <option value="{{{$i}}}">{{$i}}</option>
                            @endfor
                        </select>
                    </td>

                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.create_admin_group_semester') }}}</th>
                    <td style="width: 200px;">
                        <select name="semester" id="semester" style="width:150px;">
                            <option value="">请选择</option>
                            <option value="02">秋季</option>
                            <option value="01">春季</option>
                        </select>
                    </td>
                </tr>
        </table>
        <div align="center">
                    <!-- Form Actions -->
                    <button type="submit"  class="btn btn-small btn-info" value="1" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
                    <!-- ./ form actions -->
        </div>
    </div>
    <br>
    <div align="center" id="show_admissions">
        <div align="left">
            备注：点击【查询】按钮后，会出现以下页面
        </div>
        <div align="center">
            <h4>
                {{Lang::get('admin/admissions/title.admission_appoint_group')}}
            </h4>
        </div>
        <table id="groups" class="table table-striped table-hover table-bordered">
            <thead>
            <th style="width:20px"></th>
            <th style="width:100px">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.select_group') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.maritalStatus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.jiGuan') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.huKou') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.distribution') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.occupationState') }}}</th>
            </thead>
        </table>
        <div  align="center">
            <button id="btnOK" name="state" value="2" name="btnOK" class="btn btn-small btn-info" type="submit">
                {{{ Lang::get('admin/admissions/table.ok') }}}</button>
            <input type="hidden" id="btnValue" name="btnValue" value="1" />
        </div>
        <br>
        <div align="center">
            <h4>
                按学号范围和查询条件输入区的条件为学生指定管理班
            </h4>
        </div>
        <table  class="table table-striped table-hover table-bordered" style="width: 600px">
            <tr>
                <td>{{Lang::get('admin/admissions/table.start_stu_no')}}</td>
                <td><input type="text" id="start_stu_no" name="start_stu_no" style="width: 120px"></td>
                <td>{{Lang::get('admin/admissions/table.end_stu_no')}}</td>
                <td><input type="text" id="end_stu_no" name="end_stu_no" style="width: 120px"></td>
            </tr>
            <tr>
                <td>管理班</td>
                <td>
                    <select id="group_id" name="group_id" style="width:120px">
                        <option value="">{{Lang::get('general.pleaseselect')}}</option>
                        @foreach($groups as $group)
                            <option value="{{$group->id}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <div align="center">
            <button id="btnEdit" value="3"  class="btn btn-small btn-info" name="btnEdit" type="submit">批量修改</button>
        </div>
    </div>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function check(){

            var student_type = $('#student_type').val();
            var start_stu_no = $('#start_stu_no').val();
            var end_stu_no = $('#end_stu_no').val();
            var group_id = $('#group_id').val();
            var state = $('#btnValue').val();
            if (student_type == '') {
                alert('请选择学生类别！');
                return false;
            }
            if (state == 3){
                if(start_stu_no == '' || end_stu_no=='' || group_id == ''){
                    alert('请选择批量修改的选项！');
                    return false;
                }else{
                    return true;
                }
            }

            return true;
        }
            var oTable;
        $(document).ready(function() {
            oTable = $('#groups').dataTable( {
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
                "bAutoWidth":  false,
                "bSort": false,
                "ajax": {
                    "url": "{{{ URL::to('admin/admissions/data_admissions_appoint_group') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["student_type"]=$('#student_type').val();
                        d["admissionyear"]=$('#admissionyear').val();
                        d["admissionsemester"]=$('#admissionsemester').val();
                        d["year"]=$('#year').val();
                        d["semester"]=$('#semester').val();
                        d["admission_state"]=$('#admission_state').val();
                        d["start_stu_no"] =$('#start_stu_no').val();
                        d["end_stu_no"] =$('#end_stu_no').val();
                        d["group_id"] =$('#group_id').val();
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
            $(function() {
                $("#show_admissions").hide();
                $("#btnQuery").click(function(){
                    $('#btnValue').val(1);
                    if (check()){
                        $('#show_admissions').show();
                        oTable.fnReloadAjax();
                    }
                });
                $("#btnOK").click(function(){
                    $('#btnValue').val(2);
                    if (check()){
                        $('#show_admissions').show();
                        oTable.fnReloadAjax();
                    }
                });
                $("#btnEdit").click(function(){
                    $('#btnValue').val(3);
                    if (check()){
                        $('#show_admissions').show();
                        oTable.fnReloadAjax();
                    }
                });
            });
        });
    </script>
@stop