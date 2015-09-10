@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header" align="center">
        <h4>
            {{{ Lang::get('admin/unified_exam/title.edit_unified_exam_info') }}}
        </h4>
    </div>
    {{-- choose input form --}}
    <form id="form" class="form-horizontal" method="post" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

        <!-- ./ csrf token -->
    <table align="center" style="width: 600px"><tr><td>{{{ Lang::get('admin/unified_exam/table.student_info') }}}：</td></tr></table>
        <table id="users" class="table table-striped table-hover table-bordered" align="center" style="width: 600px">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_course') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_cause') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_year') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</th>
            </tr>
                <tr>
                    <td class="col-md-2">{{ $exemption->student_id}}</td>
                    <td class="col-md-2">{{ $exemption->student_name }}<input type="hidden" name="id" value="{{{$exemption->id}}}" /></td>
                    <td class="col-md-2">{{ $exemption->subject_name }}</td>
                    <td class="col-md-2">{{ $exemption->cause_name }}</td>
                    <td class="col-md-2">{{ $exemption->registration_year }}</td>
                    <td class="col-md-2">
                        @if ($exemption->registration_semester==0) 秋季 @elseif ($exemption->registration_semester==1) 春季 @endif
                    </td>
                </tr>
            </thead>

        </table>
        <br><br><br>
        <div align="center"><h5>{{{ Lang::get('admin/unified_exam/title.choose_edit_query') }}}</h5></div>
        <table id="select_subject" class="table table-striped table-hover" align="center" style="width: 500px">
            <thead>
            <tr>
                <td class="col-md-2">{{{ Lang::get('admin/exemption/table.input_year') }}}</td>
                <td class="col-md-2">
                    <select id="input_year" name="input_year">
                        <option value="全部">请选择</option>
                        @for ($i=2000;$i<=2025;$i++)
                            <option value="{{{$i}}}" @if ($i==$exemption->registration_year) selected="selected" @endif>{{{$i}}}</option>
                        @endfor
                    </select>
                </td>
                <td class="col-md-2">{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</td>
                <td class="col-md-2">
                    <select id="input_semester" name="input_semester">
                        <option value="全部">请选择</option>
                        <option value="0" @if ($exemption->registration_semester == 0) selected="selected" @endif>秋季</option>
                        <option value="1" @if ($exemption->registration_semester == 1) selected="selected" @endif>春季</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="col-md-2">
                    {{{ Lang::get('admin/unified_exam/table.unified_exam_course') }}}
                </td>
                <td class="col-md-2">
                    <select name="subject" id="subject">
                        <option value="">{{{ Lang::get('admin/unified_exam/table.pleaseselect') }}}</option>
                        @foreach  ($subjects as $subject)
                            <option value="{{$subject->id }}" @if ($subject->id ==$exemption->unified_exam_subject_id) selected="selected" @endif>{{$subject->subject}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    {{{ Lang::get('admin/unified_exam/table.unified_exam_cause') }}}
                </td>
                <td>
                    <select name="cause" id="cause">
                        <option value="">{{{ Lang::get('admin/unified_exam/table.pleaseselect') }}}</option>
                        @foreach  ($causes as $cause)
                            <option value="{{$cause->id }}" @if ($cause->id ==$exemption->unified_exam_cause_id) selected="selected" @endif>{{$cause->cause}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center"><button type="submit" id="btnSave" name="btnSave" >{{{ Lang::get('admin/unified_exam/table.save') }}}</button> </td>

            </tr>
        </table>
    </form>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(
                function() {
                    $("#form").submit(function () {
                        var input_year = $("#input_year").val();
                        var input_semester = $("#input_semester").val();
                        var subject = $("#subject").val();
                        var cause = $("#cause").val();

                        if (input_year == "") {
                            alert("请选择录入年度！");
                            return false;
                        }

                        if (input_semester == "") {
                            alert("请选择录入学期！");
                            return false;
                        }
                        if (subject == "") {
                            alert("请选择科目！");
                            return false;
                        }

                        if (cause == "") {
                            alert("请选择原因！");
                            return false;
                        }
                        return true;
                    });
                });
    </script>
@stop