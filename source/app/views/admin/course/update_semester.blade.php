@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')

    <div class="form-group">
        <label>{{{ Lang::get('admin/course/title.current_semester') }}}&nbsp;</label>
        <label id="current_year"></label>
        <label id="current_semester"></label>
    </div>
    <div class="form-group">
        <label>{{{ Lang::get('admin/course/title.next_semester') }}}&nbsp;</label>
        <label id="next_year"></label>
        <label id="next_semester"></label>
    </div>

<div id="show">

    </div>

    <div class="form-group" align="center">
        <button id="btnUpdate" class="btn btn-small btn-info">
            {{{ Lang::get('admin/course/title.update_next') }}}</button>
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
            if (semester == 1)
            {
                $("#current_year").html(year + "年");
                $("#current_semester").html("春季");
                $("#next_year").html(year + "年");
                $("#next_semester").html("秋季");
            }
            else if (semester == 2)
            {
                $("#current_year").html(year + "年");
                $("#current_semester").html("秋季");
                var next_year = year+1;
                $("#next_year").html(next_year + "年");
                $("#next_semester").html("春季");
            }
        }

        $("#btnUpdate").click(function(){
            if (isNaN(year) || isNaN(semester))
            {
                $("#show").html("无法获取年度和学期");
                return;
            }

            var update_year = year;
            var update_semester = semester;
            if (semester == 1)
                update_semester = 2;
            else if (semester == 2)
            {
                update_semester = 1;
                update_year = year + 1;
            }
            var jsonData = {
                "year": update_year,
                "semester": update_semester
            };
            $.ajax({
                type: 'GET',
                url: '{{ URL::to('admin/course/update_year_semester') }}',
                data: jsonData,
                success: function (json) {
                    if (json == 'ok') {

                        $("#show").html("更新成功");
                        year = update_year;
                        semester = update_semester;
                        if (semester == 1)
                        {
                            $("#current_year").html(year + "年");
                            $("#current_semester").html("春季");
                            $("#next_year").html(year + "年");
                            $("#next_semester").html("秋季");
                        }
                        else if (semester == 2)
                        {
                            $("#current_year").html(year + "年");
                            $("#current_semester").html("秋季");
                            var next_year = year+1;
                            $("#next_year").html(next_year + "年");
                            $("#next_semester").html("春季");
                        }
                    } else {
                        $("#show").html("更新失败");
                    }
                }
            });
        });
    });
</script>
@stop

