@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <br>
    <br>
    <div align="center">
            {{{ Lang::get('admin/exemption/title.input_score_stuinfo') }}}
    </div>
    <br>
   {{-- choose input form --}}
    <form id="form" class="form-horizontal" method="post" action="{{ URL::to('admin/unified_exam/index') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

        <!-- ./ csrf token -->

        <table id="users" class="table table-striped table-hover table-bordered" align="center" style="width: 800px">
            <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.major') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.school') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/unified_exam/table.major_classification')}}}  </th>
            </tr>
                <tr>
                    <td class="col-md-2">{{ $exemption->student_id}}<input type="hidden" name="student_id" value="{{{$exemption->id}}}" /></td>
                    <td class="col-md-2">{{ $exemption->student_name }}</td>
                    <td class="col-md-2">{{ $exemption->major_name }}</td>
                    <td class="col-md-2">{{ $exemption->campus_name }}</td>
                    <td class="col-md-2">@if( $exemption->major_classification==14) 专科  @elseif  ( $exemption->major_classification==12) 本科 @endif</td>
                </tr>

            </thead>

        </table>
<br><br><br>
        <div align="center">{{{ Lang::get('admin/unified_exam/title.choose_query') }}}</div>
        <br>
        <table id="select_subject" class="table table-striped table-hover" align="center" style="width: 800px">
            <thead>
            <tr>
                <td class="col-md-2">
                        {{{ Lang::get('admin/unified_exam/table.unified_exam_course') }}}
                </td>
                <td class="col-md-2">
                    <select name="subject" id="subject">
                        <option value="">{{{ Lang::get('admin/unified_exam/table.pleaseselect') }}}</option>
                        @foreach  ($subjects as $subject)
                            <option value="{{$subject->id }}">{{$subject->subject}}</option>
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
                            <option value="{{$cause->id }}">{{$cause->cause}}</option>
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
                        var subject = $("#subject").val();
                        var cause = $("#cause").val();

                        if (subject == "") {
                            alert("请选择免统考科目！");
                            return false;
                        }

                        if (cause == "") {
                            alert("请选择免统考原因！");
                            return false;
                        }

                        return true;
                    });
                })
</script>
@stop