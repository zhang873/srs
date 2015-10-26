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
            <a href="{{{ URL::to('admin/admissions/approve_admissions') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_info') }}}(省校权限)</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/admissions_edit_province') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_info') }}}</a>&nbsp;&nbsp;
        </div>
    </div>

    <div class="form-group" align="center">
        <h3>
            优先使用“学号”查询学生
        </h3>
    </div>

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="exemption" class="table-striped table-hover" align="center" width="800">
            <thead>
            <tr>
                <td class="col-md-2" align="right">{{{ Lang::get('admin/admissions/table.student_id') }}}</td>
                <td class="col-md-2" align="left">  <input class="form-control" type="text" name="student_id" id="student_id" value="{{{ Input::old('student_id') }}}" />
                    {{ $errors->first('sno', '<span class="help-inline">:message</span>') }}</td>
                <td class="col-md-2" align="right">{{{ Lang::get('admin/admissions/table.student_name') }}}</td>
                <td class="col-md-2" align="left">
                    <input class="form-control" type="text" name="student_name" id="student_name" value="{{{ Input::old('student_name') }}}" />
                    {{ $errors->first('student_name', '<span class="help-inline">:message</span>') }}</td>
            </tr>
            <tr>
                <td colspan="4" height="10px"></td>
            </tr>
            <tr>
                <td class="col-md-2" colspan="4" align="center">
                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-default" value="query" id="btnQuery" name="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
                    <!-- ./ form actions -->
                </td>
            </tr>
            <thead>
        </table>
    <br><br>
    <div id="frame">
        <iframe src="" id="other_info" name="other_info" width="100%" height="800px" frameborder="0" scrolling="no"></iframe>
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
        function query_otherinfo(){
            var ff = document.getElementById("other_info");
            if (ff != null){
                ff.src ="{{{ URL::to('admin/admissions/edit_admissions_otherInfo') }}}" + "?student_id="+ $("#student_id").val() +"&student_name="+$("#student_name").val();
            }
            $('#frame').show();

        }
        function check(){
            if (($('#student_id').val() == '') && ($('#student_name').val() == '')) {
                alert('学号、姓名不能同时为空，请至少填写一项');
                $('#student_id').focus();
                return false;
            } else {
                if(($('#student_name').val() != '') &&  ($('#student_name').val().length < 2)){
                    alert('请输入完整的姓名');
                    return false;
                }
                return true;
            }
        }
        $(document).ready(function() {
            $(function () {
                $("#btnQuery").click(function () {
                    if(check()){
                        query_otherinfo();
                    }

                });
            });
        });
    </script>
@stop