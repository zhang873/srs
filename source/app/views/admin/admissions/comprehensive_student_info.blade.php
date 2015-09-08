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
            {{ Lang::get('admin/admissions/title.input_query_condition') }}
        </h3>
        <label style="color:#ff0000" >{{ Lang::get('admin/admissions/title.comprehensive_student_info_hint') }}</label>
    </div>
    <div class="form-group" align="center">
        <label class="rlbl" >{{ Lang::get('admin/admissions/table.student_id') }}</label>
        <input class="twidth" tabindex="1" type="text" name="student_id" id="student_id">
        <label class="rlbl" >{{ Lang::get('admin/admissions/table.ID_number') }}</label>
        <input class="twidth" tabindex="1" type="text" name="id_number" id="id_number">
        <label class="rlbl" >{{ Lang::get('admin/admissions/table.digital_registration_number') }}</label>
        <input class="twidth" tabindex="1" type="text" name="reg_number" id="reg_number">
    </div>
    <div class="form-group" align="center">
        <button id="btnQuery" class="btn btn-small btn-info" onclick="query_base_info();">
            {{{ Lang::get('admin/admissions/table.query') }}}</button>
    </div>
    <br><br>
    <div id="frames" style="display: none;">
          <iframe src="" id="base_info" name="base_info" width="100%" height="200px" frameborder="0"></iframe>
          <iframe src="" id="detail_info" name="detail_info" width="100%" height="600px" frameborder="0"></iframe>
    </div>

@stop

@section('styles')

<style>
    .rlbl{
        text-align:right;
        width:100px;
    }
    .twidth{
        width:150px;
    }
    .col-md-01 {
        width: 10%;
    }
    .col-md-02 {
        width: 8%;
    }
    .col-md-03 {
        width: 5%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">

        function query_base_info(){
            if (($('#student_id').val() == '') && ($('#id_number').val() == '')
                && ($('#reg_number').val() == '')){
                alert('{{ Lang::get('admin/admissions/title.comprehensive_student_info_hint') }}');
                return false;
            }
            var ff = document.getElementById("base_info");
            if (ff != null){

                ff.src ="{{{ URL::to('admin/admissions/base_student_info') }}}" + "?student_id="+ $("#student_id").val()
                    + "&id_number="+ $("#id_number").val() + "&reg_number="+ $("#reg_number").val();
            }
            $('#frames').show();
            ff = document.getElementById("detail_info");
            if (ff != null){
                ff.src = '';
            }

        }
        $(document).ready(function() {

		});
	</script>
@stop