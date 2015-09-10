@extends('admin.layouts.default')

{{-- Web site Title --}}


{{-- Content --}}
@section('content')
   {{-- choose input form --}}
    <form id="form" class="form-horizontal" method="post"  autocomplete="off" action="@if ($mode =='create') {{URL::to('admin/exemption/index')}} @else {{URL::to('admin/exemption/'.$id.'/edit')}} @endif">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <br>
        <div align="center">
                {{{ Lang::get('admin/exemption/title.input_score_course') }}}
        </div>
        <br>
        <table id="exemption" class="table table-striped table-hover table-bordered" align="center" style="width:500px">
            <tr>
                <th>{{{ Lang::get('admin/exemption/table.course_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.major_classification') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.credit') }}}</th>
            </tr>
                <tr>
                    <td width="80px">{{ $exemption->course_id }}</td>
                    <td width="80px">{{ $exemption->course_name }}</td>
                    <td width="80px">
                        @if ($exemption->major_classification == 12)
                            本科
                        @elseif ($exemption->major_classification == 14)
                            专科
                        @endif
                    </td>
                    <td width="80px">{{ $exemption->credit }}
                        <input type="hidden" name="student_id" value={{$exemption->student_id}}>
                        <input type="hidden" name="course_id" value={{$exemption->course_id}}>
                    </td>
                </tr>
         </table>
        <br>
        <div align="right">
            <a href="#" onclick="javascript:history.go(-1);">返回上一页</a>
        </div>
        <br>
        <br>
        <br>
        <div align="center">
                {{{ Lang::get('admin/exemption/title.input_course_name_outer') }}}
        </div>
        <br>
        <div class="form-group {{{ $errors->has('major_outer') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="major_outer" class="rlbl">{{{ Lang::get('admin/exemption/table.major_outer').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
                @if ($mode == 'create')
                    <select name="major_outer" id="major_outer" style="width: 150px">
                        <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        @foreach  ($majors as $major)
                             <option value="{{{$major->id}}}">{{{$major->major_name}}}</option>
                        @endforeach
                    </select>
                @else
                    <select name="major_outer" id="major_outer" style="width: 150px">
                        <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        @foreach  ($majors as $major)
                            <option value="{{{$major->id}}}" @if ($major->id == $exemption->major_name_outer) selected="selected"  @endif>{{{$major->major_name}}}</option>
                        @endforeach
                    </select>
                @endif
        </div>

        <div class="form-group {{{ $errors->has('course_outer') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="course_outer" class="rlbl">{{{ Lang::get('admin/exemption/table.course_outer').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <input type="text" name="course_outer" id="course_outer" value=""  style="width: 150px">
            @else
                <input type="text" name="course_outer" id="course_outer" value="{{$exemption->course_name_outer}}"  style="width: 150px">
            @endif
        </div>

        <div class="form-group {{{ $errors->has('agency_id') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="agency_id" class="rlbl">{{{ Lang::get('admin/exemption/table.agency_name').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <select name="agency_id" id="agency_id" style="width: 150px">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    @foreach  ($agencys as $agency)
                        <option value="{{{$agency->id}}}">{{{$agency->agency_name}}}</option>
                    @endforeach
                </select>
            @else
                <select name="agency_id" id="agency_id" style="width: 150px">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    @foreach  ($agencys as $agency)
                        <option value="{{{$agency->id}}}" @if ($agency->id == $exemption->agency_id) selected="selected"  @endif>{{{$agency->agency_name}}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="form-group {{{ $errors->has('certification_year') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="certification_year" class="rlbl">{{{ Lang::get('admin/exemption/table.certification_year').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <input type="text" name="certification_year" id="certification_year" value=""  style="width: 150px">
            @else
                <input type="text" name="certification_year" id="certification_year" value="{{$exemption->certification_year}}"  style="width: 150px">
            @endif
        </div>

        <div class="form-group {{{ $errors->has('credit_outer') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="credit_outer" class="rlbl">{{{ Lang::get('admin/exemption/table.credit_outer').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}<br>（例如：3）&nbsp;&nbsp;&nbsp;&nbsp;</label>
            @if ($mode == 'create')
                <input type="text" name="credit_outer" id="credit_outer" value=""  style="width: 150px">
            @else
                <input type="text" name="credit_outer" id="credit_outer" value="{{$exemption->credit_outer}}"  style="width: 150px">
            @endif
        </div>

        <div class="form-group {{{ $errors->has('exemption_type_id') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="exemption_type_id" class="rlbl">{{{ Lang::get('admin/exemption/table.exemption_type_id').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <select name="exemption_type_id" id="exemption_type_id" style="width: 150px">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    @foreach  ($types as $type)
                        <option value="{{{$type->id}}}">{{{$type->exemption_type_name}}}</option>
                    @endforeach
                </select>
            @else
                <select name="exemption_type_id" id="exemption_type_id" style="width: 150px">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    @foreach  ($types as $type)
                        <option value="{{{$type->id}}}" @if ($type->id == $exemption->exemption_type_id) selected="selected"  @endif>{{{$type->exemption_type_name}}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="form-group {{{ $errors->has('classification_outer') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="classification_outer" class="rlbl">{{{ Lang::get('admin/exemption/table.classification_outer').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <select name="classification_outer" id="classification_outer"  style="width: 150px;">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    <option value="14">专科</option>
                    <option value="12">本科</option>
                </select>
            @else
                <select name="classification_outer" id="classification_outer"  style="width: 150px;">
                    <option value="">{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                    <option value="14"  @if ($exemption->classification_outer == 14) selected="selected"  @endif>专科</option>
                    <option value="12"  @if ($exemption->classification_outer == 12)  selected="selected"  @endif>本科</option>
                </select>
            @endif
        </div>

        <div class="form-group {{{ $errors->has('score') ? 'error' : '' }}}" style="width: 700px;text-align: right">
            <label for="score" class="rlbl">{{{ Lang::get('admin/exemption/table.score').'&nbsp;&nbsp;(*)&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <input type="text" name="score" id="score" value=""  style="width: 150px">
            @else
                <input type="text" name="score" id="score" value="{{$exemption->score}}"  style="width: 150px">
            @endif
        </div>

        <div class="form-group {{{ $errors->has('remark') ? 'error' : '' }}}"  align="center">
            <label for="remark" class="rlbl">{{{ Lang::get('admin/exemption/table.remark').'&nbsp;&nbsp;&nbsp;&nbsp;' }}}</label>
            @if ($mode == 'create')
                <input type="text" name="remark" id="remark" value=""  style="width: 400px;height: 30px;">
            @else
                <input type="text" name="remark" id="remark" value="{{$exemption->remark}}"  style="width: 400px;height: 30px;">
            @endif
        </div>
        <div align="center">
            <button type="submit" id="btnSave" name="btnSave" >{{{ Lang::get('admin/exemption/table.save') }}}</button>
        </div>
     </form>

@stop

@section('style')
    <style>
        .rlbl{
            text-align: right;
            width: 150px;
        }

    </style>
@stop

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

         $(document).ready(
                 function() {
                     $("#form").submit(function () {
                         var major_outer = $("#major_outer").val();
                         var course_outer = $("#course_outer").val();
                         var agency_id = $("#agency_id").val();
                         var certification_year = $("#certification_year").val();
                         var credit_outer = $("#credit_outer").val();
                         var exemption_type_id = $("#exemption_type_id").val();
                         var classification_outer = $("#classification_outer").val();
                         var score = $("#score").val();
                         var remark = $("#remark").val();

                         if (major_outer == "") {
                             alert("请选择系统外专业名称！");
                             $("#major_outer").focus();
                             return false;
                         }

                         if (course_outer == "") {
                             alert("请输入系统外课程名称！");
                             $("#course_outer").focus();
                             return false;
                         } else if (course_outer.search(/(^(?!_)(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                                 alert("只能输入文字、数字、括号、下划线！");
                                 $("#course_outer").focus();
                                 return false;
                         }

                         if (agency_id == "") {
                             alert("请选择颁证单位！");
                             $("#agency_id").focus();
                             return false;
                         }

                         if (certification_year == "") {
                             alert("请输入颁证年份！");
                             $("#certification_year").focus();
                             return false;
                         } else if ((certification_year.length !=4) || (certification_year.search(/(^(?!_)(?!.*?_$)[0-9]+$)/) == -1)) {
                             alert("请输入4位数的年份");
                             $("#certification_year").focus();
                             return false;
                         }

                         if (credit_outer == "") {
                             alert("请输入外课程学分！");
                             $("#credit_outer").focus();
                             return false;
                         } else if ((credit_outer.length !=1) || (credit_outer.search(/(^(?!_)(?!.*?_$)[0-9]+$)/) == -1)) {
                             alert("请输入1位数的学分");
                             $("#credit_outer").focus();
                             return false;
                         }


                         if (exemption_type_id == "") {
                             alert("请选择免修类型！");
                             $("#exemption_type_id").focus();
                             return false;
                         }

                         if (classification_outer == "") {
                             alert("请选择外课程层次！");
                             $("#classification_outer").focus();
                             return false;
                         }

                         if (score == "") {
                             alert("请输入成绩代码！");
                             $("#score").focus();
                             return false;
                         } else if (score.search(/(^(?!_)(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1) {
                             alert("只能输入文字、数字、下划线！");
                             $("#score").focus();
                             return false;
                         }

                        return true;
                     });
                 })
    </script>
@stop