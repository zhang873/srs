@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')

    <div class="form-group">
        <label id="current_year">
        </label>
        <label id="current_semester">
        </label>
    </div>


    <div id="show">

    </div>

    <div class="form-group" align="center">
        <button id="btnConfirm" class="btn btn-small btn-info">
            {{{ Lang::get('admin/select/title.batch_confirm_selection') }}}</button>
    </div>


@stop
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var str_year = "<?php
                        if (is_null($curInfo))
                            $str = '';
                        else
                            $str = $curInfo->current_year;
                        echo $str;
                    ?>";
        var str_semester = "<?php
                           if (is_null($curInfo))
                               $str = '';
                           else
                               $str = $curInfo->current_semester;
                           echo $str;
                       ?>";
        var year = parseInt(str_year);
        var semester = parseInt(str_semester);
        if (!isNaN(year) && !isNaN(semester))
        {
            if (semester == 1) {
                $("#current_year").html(year + "年");
                $("#current_semester").html("春季");
            }
            else if (semester == 2) {
                $("#current_year").html(year + "年");
                $("#current_semester").html("秋季");
            }
        }
        $("#btnConfirm").click(function(){
            if (isNaN(year) || isNaN(semester))
            {
                $("#show").html("无法获取年度和学期");
                return;
            }
            var jsonData = {
                "year": year,
                "semester": semester
            };
            $.ajax({
                type: 'GET',
                url: '{{ URL::to('admin/select/batch_confirm_selection_rst') }}',
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

