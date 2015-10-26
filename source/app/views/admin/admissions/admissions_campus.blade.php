@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
        <div class="pull-right">
            <a href="{{{ URL::to('admin/admissions/admissions_other_info') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_other_info') }}}</a>&nbsp;&nbsp;
        </div>
        <br>
    </div>

   {{-- choose input form --}}

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
    <input id="selectedPoliticalStatus" name="selectedPoliticalStatus" type="hidden" value="" />
    <input id="selectedIds" name="selectedIds" type="hidden" value="" />
    <input id="selectedMaritalStatus" name="selectedMaritalStatus" type="hidden" value="" />
    <input id="selectedJiguan" name="selectedJiguan" type="hidden" value="" />
    <input id="selectedHukou" name="selectedHukou" type="hidden" value="" />
    <input id="selectedDistribution" name="selectedDistribution" type="hidden" value="" />
    <input id="selectedIs_serving" name="selectedIs_serving" type="hidden" value="" />
        <div class="form-group" align="center">
            <h3>
                {{Lang::get('admin/admissions/table.query_input')}}
            </h3>

        </div>
        <div class="form-group" align="center" width="600px">
            <label for="student_name" class="rlbl">{{ Lang::get('admin/admissions/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="2" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">
            <label for="student_id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="1" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
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
        <div  class="form-group" align="center">
            <button id="btnQuery" name="btnQuery" type="submit" value="2" class="btn btn-small btn-info" >查询</button>
        </div>

   <br>
   <br>
   <br>
    <div  class="form-group" align="center">

        <table id="admissions" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <th class="col_1" ></th>
                <th class="col_2">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.certification_type') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.certification_code') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.group_code') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.maritalStatus') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.jiGuan') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.huKou') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.distribution') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.occupationState') }}}</th>
                <th></th>
            </tr>
            </thead>
        </table>

    </div>
    <div  class="form-group" align="center">
        <button id="btnOK" name="btnOK" value="1" class="btn btn-small btn-info" type="submit">确定</button>
        <input id="btnValue" name="btnValue" value="2" type="hidden">
    </div>
@stop

@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:200px;
        }
        .col_1{
            width:20px;
        }
        .col_2{
            width:80px;
        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function countSelects() {
            $("#selectedPoliticalStatus").val('');
            $("#selectedIds").val('');
            $("#selectedMaritalStatus").val('');
            $("#selectedJiguan").val('');
            $("#selectedHukou").val('');
            $("#selectedDistribution").val('');
            $("#selectedIs_serving").val('');
            $( "select[name='politicalstatus[]'] option:selected" ).each(function() {
                $("#selectedPoliticalStatus").val($("#selectedPoliticalStatus").val() + ',' + $(this).val());
            });
            $( "input[name='ids[]']" ).each(function() {
                $("#selectedIds").val($("#selectedIds").val() + ',' + $(this).val());
            });
            $( "select[name='maritalstatus[]'] option:selected" ).each(function() {
                $("#selectedMaritalStatus").val($("#selectedMaritalStatus").val() + ',' + $(this).val());
            });
            $( "select[name='jiguan[]'] option:selected" ).each(function() {
                $("#selectedJiguan").val($("#selectedJiguan").val() + ',' + $(this).val());
            });
            $( "select[name='hukou[]'] option:selected" ).each(function() {
                $("#selectedHukou").val($("#selectedHukou").val() + ',' + $(this).val());
            });
            $( "select[name='distribution[]'] option:selected" ).each(function() {
                $("#selectedDistribution").val($("#selectedDistribution").val() + ',' + $(this).val());
            });
            $( "select[name='is_serving[]'] option:selected" ).each(function() {
                $("#selectedIs_serving").val($("#selectedIs_serving").val() + ',' + $(this).val());
            });

        }
        var oTable;
        $(document).ready(function() {
            oTable = $('#admissions').dataTable({
                "ordering": false,
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
                    "url": "{{{ URL::to('admin/admissions/data_admissions_campus') }}}",
                    "data": function (d) {
                        d["politicalstatus"]= $('#selectedPoliticalStatus').val();
                        d["ids"]= $('#selectedIds').val();
                        d["maritalstatus"]= $('#selectedMaritalStatus').val();
                        d["jiguan"]= $('#selectedJiguan').val();
                        d["hukou"]= $('#selectedHukou').val();
                        d["distribution"]= $('#selectedDistribution').val();
                        d["is_serving"]= $('#selectedIs_serving').val();
                        d["student_id"] = $('#student_id').val();
                        d["student_name"] = $('#student_name').val();
                        d["major"] = $('#major').val();
                        d["major_classification"] = $('#major_classification').val();
                        d["state"] =$('#btnValue').val();
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
                "aaSorting": [[0, 'asc']]
            });

            $("#btnQuery").click(function () {
                $("#btnValue").val(2);
                oTable.fnReloadAjax();
            });
            $("#btnOK").click(function(){
                $("#btnValue").val(1);
                countSelects();
                oTable.fnReloadAjax();
            });


        });

    </script>
@stop