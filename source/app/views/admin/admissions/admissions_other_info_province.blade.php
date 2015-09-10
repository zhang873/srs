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
            优先使用“学号”查询学生
        </h3>
    </div>


    <form class="form-horizontal" method="post"  action="{{ URL::to('admin/admissions/edit_admissions_otherInfo') }}" autocomplete="off">
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
                    <button type="submit" class="btn btn-default" value="query" id="btnQuery">{{{ Lang::get('admin/admissions/table.query') }}}</button>
                    <!-- ./ form actions -->
                </td>
            </tr>
            <thead>
            <tbody>
            </tbody>
        </table>
        <hr>

    </form>
    <br><br>
    <div id="show_info">

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
        function getSelectVal(){
            $.getJSON("{{URL::to('admin/admissions/getOtherInfo')}}",{id:$("#student_id").val(),name:$('#student_name').val()},function(json){
                var show_admissions = $("#show_admissions");
                $.each(json,function(index,html){
                    show_admissions.html(html);
                });
            });
        }
        $(document).ready(function() {
            $(function () {
                $("#btnQuery").click(function () {
                    if (($('#student_id').val() == '') && ($('#student_name').val() == '')) {
                        alert('学号、姓名不能同时为空，请至少填写一项');
                        $('#student_id').focus();
                        return false;
                    } else {
                        if(($('#student_name').val() != '') &&  ($('#student_name').val().length < 2)){
                            alert('请输入完整的姓名');
                            return false;
                        }else{
                            return true;
                        }
                    }
                    return true;

                });
            });
        });
    </script>
@stop