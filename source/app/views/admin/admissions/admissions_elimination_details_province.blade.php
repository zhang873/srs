@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<br>
<br>
    <div>
        <a href="{{URL::to('admin/admissions/admissions_province')}}"> 学籍</a> > <a href="{{URL::to('admin/admissions/expel_admissions')}}">开除学生</a> > 生成开除学生名单
    </div>
<br>

    {{-- Delete Unified Exam Info Form --}}
    <form id="form" class="form-horizontal" method="post" action="@if (isset($id)){{ URL::to('admin/admissions/' . $id . '/admissions_elimination_details') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $id }}" />
        <!-- ./ csrf token -->
        <table  class="table table-striped table-hover table-bordered" align="center">
            <tr>
                <td colspan="4" align="center"><h3>开除学号是{{$admissions[0]}}的学生</h3></td>
            </tr>
            <tr>
                <td>开除年度</td>
                <td>
                    <select name="year" id="year" style="width:120px;">
                        <option value="">全部</option>
                        @for ($i=2000;$i<2025;$i++)
                            <option value="{{{$i}}}" @if($yearsemester->current_year==$i) selected="selected" @endif>{{$i}}</option>
                        @endfor
                    </select>
                </td>
                <td>开除学期</td>
                <td>
                    <select name="semester" id="semester" style="width:120px;">
                        <option value="">全部</option>
                        <option value="02"  @if($yearsemester->current_semester=='02') selected="selected" @endif>秋季</option>
                        <option value="01"  @if($yearsemester->current_semester=='01')  selected="selected" @endif>春季</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>开除原因</td>
                <td>
                    <input type="text" id="cause" name="cause">
                </td>
                <td>发文文号</td>
                <td>
                    <input type="text" id="document_id" name="document_id">
                </td>
            </tr>
            <tr>
                <td>备注</td>
                <td>
                    <input type="text" id="remark" name="remark">
                </td>
                <td></td>
                <td>
                </td>
            </tr>
        </table>
        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                <button type="submit" class="btn btn-sucess" name="elimination" value="elimination">确定</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

       $('#form').submit(function(){
           var year = $("#year" ).val();
           var semester = $("#semester" ).val();
           var cause = $("#cause" ).val();
           var document_id = $("#document_id" ).val();
           if (year=='') {
               alert('请选择开除年度！');
               return false;
           }

           if (semester == '') {
               alert('请选择开除学期！');
               return false;
           }

           if (cause == '') {
               alert('请输入开除原因！');
               return false;
           }

           if (document_id == '') {
               alert('请输入发文文号！');
               return false;
           }
           return true;
       })
  </script>
 @stop