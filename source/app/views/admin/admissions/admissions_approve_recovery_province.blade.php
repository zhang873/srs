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
        {{-- choose input form --}}
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <input id="selectedAdmissions" name="selectedAdmissions" type="hidden" value="" />
    <div align="center">
        {{Lang::get('admin/admissions/table.query_input')}}
    </div>
        <br>
        <br>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.school') }}}&nbsp;&nbsp;</label>
            <select size="1" tabindex="4" name="school" id="school" style="width:150px;">
                <option value="">全部</option>
                @foreach($schools as $school)
                    <option value="{{$school->id}}">{{$school->name}}</option>
                @endforeach
            </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.campus') }}}&nbsp;&nbsp;</label>
            <select size="1" tabindex="4" name="campus" id="campus" style="width:150px;">
                <option value="">全部</option>
                @foreach($campuses as $campus)
                    <option value="{{$campus->id}}">{{$campus->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_name') }}}&nbsp;&nbsp;</label>
            <input type="text"  style="width:150px;" id="student_name">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_id') }}}&nbsp;&nbsp;</label>
            <input type="text"  style="width:150px;" id="student_id">
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.student_type') }}}&nbsp;&nbsp;</label>
                         <select size="1" tabindex="5" name="student_type" id="student_type" style="width:150px;">
                            <option value="">全部</option>
                            <option value="14">专科</option>
                            <option value="12">本科</option>
                        </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major_classification') }}}&nbsp;&nbsp;</label>
            <select size="1" tabindex="5" name="major_classification" id="major_classification" style="width:150px;">
                <option value="">全部</option>
                <option value="14">专科</option>
                <option value="12">本科</option>
            </select>
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.admissionyear') }}}&nbsp;&nbsp;</label>
                        <select name="admission_year" id="admission_year" style="width:150px;">
                            <option value="">请选择</option>
                            @for ($i=2000;$i<2025;$i++)
                                <option value="{{{$i}}}">{{$i}}</option>
                            @endfor
                        </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}&nbsp;&nbsp;</label>
                        <select name="admission_semester" id="admission_semester" style="width:150px;">
                            <option value="">请选择</option>
                            <option value="0">秋季</option>
                            <option value="1">春季</option>
                        </select>
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.application_year') }}}&nbsp;&nbsp;</label>
            <select name="application_year" id="application_year" style="width:150px;">
                <option value="">请选择</option>
                @for ($i=2000;$i<2025;$i++)
                    <option value="{{{$i}}}">{{$i}}</option>
                @endfor
            </select>
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.application_semester') }}}&nbsp;&nbsp;</label>
            <select name="application_semester" id="application_semester" style="width:150px;">
                <option value="">请选择</option>
                <option value="0">秋季</option>
                <option value="1">春季</option>
            </select>
        </div>
        <div class="form-group" align="center" width="700px">
            <label  class="rlbl">{{{ Lang::get('admin/admissions/table.major') }}}&nbsp;&nbsp;</label>
                    <select size="1" tabindex="4" name="major" id="major" style="width:150px;">
                        <option value="">全部</option>
                        @foreach($rawprograms as $rawprogram)
                            <option value="{{$rawprogram->id}}">{{$rawprogram->name}}</option>
                        @endforeach
                    </select>
            <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <label  class="rlbl">&nbsp;&nbsp;&nbsp;&nbsp;</label>
        </div>
        <div align="center">
                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-default" value="2" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
                    <!-- ./ form actions -->
        </div>

<br>
    <br>
    <table id="admissions" class="table table-striped table-hover table-bordered" align="center">
        <thead>
        <tr>
            <th class="col-md-1" style="width: 40px">{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.final_result') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.remark') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.action') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.multi-results') }}}
                <br>
                <label id="checkAll"><a style="cursor: hand">全选</a></label>
                <br>
                <label id="checkNo"><a style="cursor:hand">全否</a></label>
            </th>
        </tr>
        </thead>
    </table>
    <div align="center">
        <button id="btnPass" name="state" value="1" class="btn btn-small btn-info" disabled="disabled" type="submit">同意</button>
    </div>
    <div>
        <input type="hidden" id="btnValue" value="2">
    </div>
@stop

@section('styles')

    <style>
        .rlbl{
            width:150px;
            text-align:right;
        }
        .width230{
            width:230px;
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
        $(document).ready(function() {
            oTable = $('#admissions').dataTable( {
                "searching":false,
                "ordering" : false,
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
                    "url": "{{{ URL::to('admin/admissions/data_approve_recovery') }}}",
                    "data": function ( d ) {
                        d["selectedAdmissions"]= $('#selectedAdmissions').val();
                        d["school"]=$('#school').val();
                        d["campus"]=$('#campus').val();
                        d["student_name"]=$('#student_name').val();
                        d["student_id"]= $('#student_id').val();
                        d["student_type"]=$('#student_type').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["admissionyear"]=$('#admissionyear').val();
                        d["admissionsemester"]=$('#admissionsemester').val();
                        d["application_year"]=$('#application_year').val();
                        d["application_semester"]=$('#application_semester').val();
                        d["major"]=$('#major').val();
                        d["state"]=$('#btnValue').val();
                    }

                },

                "aaSorting": [ [0,'asc'] ]
            });

            $(function() {
                $("#btnQuery").click(function () {
                    $('#btnValue').val(2);
                    oTable.fnReloadAjax();
                });
                $("#btnPass").click(function () {
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
                $('#checkItem').change(countCheckedBoxes());
                $('#btnNoPass').click(function(){
                            $.post("{{URL::to('admin/admissions/admissions_recovery_nopass')}}}",{id:$('#id').val(),address:$('#address').val()},
                                    function(data){
                                        //$('#msg').html("please enter the email!");
                                        //alert(data);
                                        $('#msg').html(data);
                                    },
                                    "text");//这里返回的类型有：json,html,xml,text
                }
                )

            });
        });

    </script>
@stop