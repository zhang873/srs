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
            <a href="{{{ URL::to('admin/admissions/admissions_appoint_group') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.admission_appoint_group') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/admin_group') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admin_group') }}}</a>
        </div>
        <br>
    </div>
    <form class="form-horizontal" method="post"  id="form" action="" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="exemption"  class="table table-striped table-hover table-bordered" align="center" style="width:600px">
            <thead>
                <tr>
                    <td colspan="4" align="center"><h3>建立管理班</h3></td>
                </tr>
            </thead>
                  <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                    <td style="width: 150px;">
                        <select  name="major_classification" id="major_classification" style="width:150px;">
                            <option value="">全部</option>
                            <option value="12">本科</option>
                            <option value="14">专科</option>
                        </select>
                    </td>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major') }}}</th>
                    <td style="width: 120px;">
                        <select name="major" id="major" style="width:200px;">
                            <option value="">全部</option>
                            @foreach($rawprograms as $rawprogram)
                                <option value="{{$rawprogram->id}}">{{$rawprogram->pname}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.year') }}}</th>
                    <td style="width: 150px;">
                        <select name="year" id="year" style="width:150px;">
                            <option value="">请选择</option>
                            @for ($i=2005;$i<2025;$i++)
                                <option value="{{{$i}}}">{{$i}}</option>
                            @endfor
                        </select>
                    </td>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.semester') }}}</th>
                    <td style="width: 150px;">
                    <select name="semester" id="semester" style="width:150px;">
                        <option value="">请选择</option>
                        <option value="02">秋季</option>
                        <option value="01">春季</option>
                    </select>
                   </td>
                </tr>
                <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.hasStudentno') }}}</th>
                    <td style="width: 150px;">
                        <select name="having_stu_no" id="having_stu_no" style="width:150px;">
                            <option value="">请选择</option>
                            <option value="0">没有</option>
                            <option value="1">有</option>
                        </select>
                    </td>

                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
                    <td style="width: 200px;">
                        <input type="text"  style="width:200px;" id="group_name" name="group_name">
                    </td>
                </tr>
                <tr>
                    <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.class_adviser') }}}</th>
                    <td style="width: 150px;"><input type="text"  style="width:150px;" id="class_adviser" name="class_adviser"></td>
                    <th class="col-md-1" style="width: 150px;"></th>
                    <td style="width: 150px;"></td>
                </tr>

        </table>
        <div align="center">
                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-default" value="2" id="btnAdd">{{{ Lang::get('button.add') }}}</button>
                    <!-- ./ form actions -->
        </div>
    </form>

@stop

@section('styles')

    <style>
        .width220{
            width:220px;
        }
        .width230{
            width:230px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(
                function() {
                    $("#form").submit(function () {
                        var major_classification = $("#major_classification").val();
                        var major = $("#major").val();
                        var year = $("#year").val();
                        var semester = $("#semester").val();
                        var having_stu_no = $("#having_stu_no").val();
                        var group_name = $("#group_name").val();
                        var class_adviser = $("#class_adviser").val();


                        if (major_classification == "") {
                            alert("请选择专业层次！");
                            $("#major_classification").focus();
                            return false;
                        }

                        if (major == "") {
                            alert("请选择专业！");
                            $("#major").focus();
                            return false;
                        }

                        if (year == "") {
                            alert("请选择年度！");
                            $("#year").focus();
                            return false;
                        }

                        if (semester == "") {
                            alert("请选择学期！");
                            $("#semester").focus();
                            return false;
                        }

                        if (having_stu_no == "") {
                            alert("请选择 已有学号否！");
                            $("#having_stu_no").focus();
                            return false;
                        }

                        if (group_name == "") {
                            alert("请输入班名称！");
                            $("#group_name").focus();
                            return false;
                        }

                        if (class_adviser == "") {
                            alert("请输入班主任姓名！");
                            $("#class_adviser").focus();
                            return false;
                        }

                        return true;
                    });
                })
    </script>
@stop