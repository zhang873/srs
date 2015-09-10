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


    <div class="form-group" align="center">
        <label for="id" class="rlbl" >{{ Lang::get('admin/admissions/table.student_name') }}</label>
        <input tabindex="1" type="text" name="student_name" id="student_name" value="{{ Input::old('student_name') }}" style="width:200px;">

        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>

    <div class="form-group" align="center">
            <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/admissions/table.query') }}}</button>

    </div>
    <br><br>
<form method="post" id="edit_student_info">
    <table id="studentInfo" class="table table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th class="col-md-1"></th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.campus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.maritalStatus') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.jiGuan') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.huKou') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.postcode') }}}</th>
            <th class="col-md-1">{{{ Lang::get('admin/admissions/table.occupationState') }}}</th>
           </tr>
        </thead>
        {{$i=0}}
        @foreach ($admissions as $admission)
           {{$i++;}}
            <tr>
                <td>{{$i}}</td>
                <td>{{ $admission->studentno }}</td>
                <td>{{ $admission->fullname }}</td>
                <td>{{ $admission->campuscode }}</td>
                <td><select id="politicalstatus" name="politicalstatus">
                        @foreach($politicalstatus as $political)
                            <option value="{{$political->id}}" @if ($admission->politicalstatus == $political->id) selected @endif>{{$political->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select id="maritalstatus" name="maritalstatus">
                        <option value="0" @if ($admission->maritalstatus==1) selected @endif>未婚</option>
                        <option value="1" @if ($admission->maritalstatus==2) selected @endif>已婚</option>
                        <option value="2" @if ($admission->maritalstatus==3) selected @endif>其他</option>
                    </select>
                </td>
                <td>
                    <select id="jiguan" name="jiguan">
                        @foreach($jiguans as $jiguan)
                            <option value="{{$jiguan->id}}" @if ($admission->jiguan == $jiguan->id) selected @endif>{{$jiguan->name}}</option>
                         @endforeach
                    </select>
                </td>
                <td>
                    <select id="maritalstatus" name="maritalstatus">
                        <option value="0" @if ($admission->hukou==0) selected @endif>城镇户口</option>
                        <option value="1" @if ($admission->hukou==1) selected @endif>农村户口</option>
                        <option value="2" @if ($admission->hukou==2) selected @endif>外国公民</option>
                        <option value="3" @if ($admission->hukou==3) selected @endif>其他</option>
                    </select>
                </td>
                <td>
                    <select id="postcode" name="postcode">
                        @foreach($postcodes as $postcode)
                            <option value="{{$postcode->id}}" @if ($admission->jiguan == $postcode->id) selected @endif>{{$postcode->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select id="occupationstate" name="occupationstate">
                        <option value="0" @if ($admission->occupation==0) selected @endif>不在职</option>
                        <option value="1" @if ($admission->occupation==1) selected @endif>在职</option>
                    </select>
                    <input type="hidden" id="id" name="id" value="{{$admission->id}}">
                </td>

            </tr>
        @endforeach
    </table>
    <div class="form-group" align="center">
        <button id="btnOk" name="state" value="1" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/admissions/table.ok') }}}</button>

    </div>
</form>
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
        .rtxt{
            text-align:left;
            width:120px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {
            oTable = $('#studentInfo').dataTable( {
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
                    "url": "{{{ URL::to('admin/admissions/edit_student_campus') }}}",
                    "data": function ( d ) {
                        d["student_id"]= $('#student_id').val();
                        d["student_name"]=$('#student_name').val();
                        d["major"]=$('#major').val();
                        d["major_classification"]=$('#major_classification').val();
                        d["campus"]=$('#campus').val();
                        d["school"]=$('#school').val();
                        d["admission_state"]=$('#admission_state').val();
                        d["state"]=$('#btnValue').val();
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