@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
    {{-- Delete User Form --}}
<form  id="uploadForm"  method="post" action="" enctype="multipart/form-data">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="form-group" align="center">
            <input type="file" name="file" id="file">
        </div>
        <div class="form-group" align="center">

            <button id="btnDownloadExcel" name="btnDownloadExcel" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/course/title.download_excel') }}}</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="submit" id="btnUpload" name="btnUpload" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/course/title.upload_excel') }}}</button>
        </div>
</form>
<form  id="downloadForm"  method="post" action="{{ URL::to('admin/course/download_excel') }}" style="display:none">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <input id="excel_type" name="excel_type" type="hidden" value="{{$title}}" />
    <button type="submit" id="btnDownload" name="btnDownload" class="btn btn-small btn-info" >
            {{{ Lang::get('admin/course/title.upload_excel') }}}</button>
</form>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">

        $(document).ready(function() {
            $("#btnUpload").click(function(){
                if ($("#file").val() == ''){
                    alert("请选择导入的文件！");
                    return false;
                }
            });
            $("#btnDownloadExcel").click(function(){
                $("#btnDownload").trigger("click");
                return false;
            });
		});
	</script>
@stop

