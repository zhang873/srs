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
            <a href="{{{ URL::to('admin/admissions/application_dropout') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.application_dropout_admissions') }}}</a>&nbsp;&nbsp;
          </div>
        <br>
    </div>

    <div class="form-group" align="center">
        <h4>
            请输入学生学号
        </h4>
    </div>
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <div class="form-group" align="center">
        <label for="student_id" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}（必填）</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>

    <div class="form-group" align="center">
        <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" type="submit">
            {{{ Lang::get('admin/admissions/table.ok') }}}</button>
    </div>

    <div id="frame" style="display: none;">
        <iframe src="" id="dropout" name="dropout" width="100%" height="800px" frameborder="0" scrolling="no" ></iframe><br>
    </div>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function query_base_info(){
            var student_id = $('#student_id').val();
            if (student_id == "") {
                alert('请输入学号！');
                $('#student_id').focus();
                return false;
            }
            var ff = document.getElementById("dropout");
            if (ff != null){
                ff.src ="{{{ URL::to('admin/admissions/get_dropout') }}}" + "?student_id="+ $("#student_id").val() ;
            }
            $('#frame').show();

        }
        $(document).ready(function() {
            $("#btnQuery").click(function () {
                query_base_info();
            });
        });

    </script>
@stop