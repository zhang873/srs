@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
    {{-- Delete User Form --}}
<form  id="uploadForm"  method="post" action="" enctype="multipart/form-data"">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="form-group" align="center">
            <input type="file" name="file" id="file">
        </div>
        <div class="form-group" align="center">
            <button type="submit" id="btnUpload" name="btnUpload" class="btn btn-small btn-info" >
                {{{ Lang::get('admin/course/title.upload_excel') }}}</button>
        </div>
</form>
@stop

