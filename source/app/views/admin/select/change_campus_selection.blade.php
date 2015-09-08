@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')
    <div class="pull-right">
        <a href="{{{ URL::to('admin') }}}" class="btn btn-default btn-small btn-inverse">
            <span class="glyphicon glyphicon-circle-arrow-left"></span> {{{ Lang::get('general.back') }}}</a>
    </div>
    <br><br>
    <div class="form-group">
        <label>{{{ Lang::get('admin/select/title.change_campus_selection_state') }}}&nbsp;</label>
        <select size="1" name="state" id="state">
            <option value="1" {{{ $state == 1 ? 'selected=selected' : '' }}}>可以选课</option>
            <option value="0" {{{ $state == 0 ? 'selected=selected' : '' }}}>不可以选课</option>
        </select>
        <label id="show"></label>

    </div>

    <div class="form-group" align="center">
        <button id="btnUpdate" class="btn btn-small btn-info">
            {{{ Lang::get('admin/select/title.save_state') }}}</button>
    </div>


@stop
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){

        $("#btnUpdate").click(function(){
            $("#show").html("");
            var jsonData = {
                "state": $("#state").val()
            };
            $.ajax({
                type: 'GET',
                url: '{{ URL::to('admin/select/change_campus_selection_rst') }}',
                async: false,
                data: jsonData,
                success: function (json) {
                    if (json == 'ok') {
                        $("#show").html("更新成功");
                    } else {
                        $("#show").html("更新失败");
                    }
                }
            });
        });
    });
</script>
@stop

