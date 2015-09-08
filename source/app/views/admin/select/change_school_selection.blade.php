@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')

    <div class="form-group">
        <label>{{{ Lang::get('admin/select/title.change_school_selection_state') }}}&nbsp;</label>
        <select size="1" name="state" id="state">
            <option value="1" selected="selected">可以选课</option>
            <option value="0">不可以选课</option>
        </select>
    </div>
    <div id="show">
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

            var jsonData = {
                "state": $("#state").val()
            };
            $.ajax({
                type: 'GET',
                url: '{{ URL::to('admin/select/change_school_selection_rst') }}',
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

