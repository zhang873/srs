@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
    </div>
    <form id="form" method="post" action="{{URL::to('admin/admissions/change_year_semester')}}">
   {{-- choose input form --}}

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <div class="form-group" align="center">
            <h5>
                {{ Lang::get('admin/admissions/title.change_admissions_module_year_semester'); }}
            </h5>
        </div>

        <div class="form-group" align="center" width="600px">
            <label class="rlbl">{{ Lang::get('admin/admissions/table.current_semester') }}：</label>
            <label class="rlbl2" id="current_year">{{$current_year}}</label>年
            <label class="rlbl2" id="current_semester">@if ($current_semester== '01') 春季 @else 秋季 @endif</label>
        </div>
        <div class="form-group" align="center" width="600px">
            <label class="rlbl">{{ Lang::get('admin/admissions/table.next_semester') }}：</label>
            <select name="year" id="year"   style="width: 60px">
                <option value="">请选择</option>
            </select>
            <select name="semester" id="semester" style="width: 60px">
                <option value="">请选择</option>
                <option value="02"  @if ($current_semester == '02') selected="selected" @endif>秋季</option>
                <option value="01"  @if ($current_semester == '01') selected="selected" @endif>春季</option>
            </select>
        </div>
        <div  class="form-group" align="center">
            <label id="ayear"></label>
            <button id="btnChange" name="state" type="submit" value="2" class="btn btn-small btn-info" >更替学籍模块年度学期</button>
        </div>
    </form>
   <br>
   <br>
   <br>

@stop

@section('styles')
    <style>
        .rlbl{
            text-align:right;
            width:100px;
        }
        .rlbl2{
            text-align:center;
            width:55px;
        }
    </style>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        var cur_year = $("#current_year").html();
         var cur_semester = $("#current_semester").html();
        var obj = $('#year');
        for (var i=2000;i<=2025;i++) {
            //     $("<option value='"+i+"'>"+i+"</option>").appendTo("#year");
            var option = $("<option>").text(i).val(i);
            obj.append(option);
        }
        if ($.trim(cur_semester) == "秋季"){
            cur_year = Number(cur_year) +1;
     //       alert(cur_semester+cur_year);
        }
        obj.val(cur_year);


    </script>
@stop