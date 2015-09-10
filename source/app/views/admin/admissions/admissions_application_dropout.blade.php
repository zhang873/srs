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
            <a href="{{{ URL::to('admin/admissions/admissions_edit_dropout') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_dropout_application') }}}</a>&nbsp;&nbsp;
          </div>
        <br>
    </div>

    <div class="form-group" align="center">
        <h4>
            请输入学生学号
        </h4>
    </div>

<form id="form" method="post" action="{{URL::to('admin/admissions/admission_dropout_application')}}">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <div class="form-group" align="center">
        <label for="name" class="rlbl">{{ Lang::get('admin/admissions/table.student_id') }}（必填）</label>
        <input tabindex="2" type="text" name="student_id" id="student_id" value="{{ Input::old('student_id') }}" style="width:200px;">
    </div>

    <div class="form-group" align="center">

        <button id="btnQuery" name="state" value="2" class="btn btn-small btn-info" type="submit">
            {{{ Lang::get('admin/admissions/table.ok') }}}</button>

    </div>
</form>
    <br><br>

    <div id="show_admissions" style="display: none;">

        <div>
            <input type="hidden" id="btnValue" value="2">
        </div>
    </div>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {
            $('#form').submit(function () {
                var student_id = $('#student_id').val();
                if (student_id == "") {
                    alert('请输入学号！');
                    $('#student_id').focus();
                    return false;
                }
                return true;
            });
        });

    </script>
@stop