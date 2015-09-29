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
            <a href="{{{ URL::to('/admin') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('site.backtohome') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/admissions/admissions_campus') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/title.edit_admissions_info') }}}</a>&nbsp;&nbsp;
        </div>
        <br>
    </div>
   {{-- choose input form --}}
         <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <div class="form-group" align="center">
            <h3>
                优先使用“学号”查询学生
            </h3>
        </div>
        <div class="form-group" align="center" width="600px">

            <label class="rlbl" >{{ Lang::get('admin/admissions/table.student_id') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="1" type="text" name="student_id" id="student_id"  style="width:200px;">
            <label  class="rlbl">{{ Lang::get('admin/admissions/table.student_name') }}&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input tabindex="2" type="text" name="student_name" id="student_name" style="width:200px;">
        </div>

        <div  class="form-group" align="center">
            <button id="btnQuery" name="state"  value="2" class="btn btn-small btn-info" >查询</button>
        </div>

   <br>
   <br>
    <div id="frame" style="display: none;">
        <iframe src="" id="detail_info" name="detail_info" width="100%" height="600px" frameborder="0" scrolling="no"></iframe>
    </div>
   <br>

@stop

@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:150px;

        }
    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function query_base_info(){
            if (($('#student_id').val() == '') && ($('#student_name').val() == '')) {
                alert('学号、姓名不能同时为空，请至少填写一项');
                $('#student_id').focus();
                return false;
            } else {
                if(($('#student_name').val() != '') &&  ($('#student_name').val().length < 2)){
                    alert('请输入完整的姓名');
                    return false;
                }
            }
            var ff = document.getElementById("detail_info");
            if (ff != null){
                ff.src ="{{{ URL::to('admin/admissions/edit_admissions_other_info') }}}" + "?student_id="+ $("#student_id").val() + "&student_name="+ $("#student_name").val() ;
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