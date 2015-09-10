@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
    </div>
    <form id="form" class="form-horizontal" method="post"  action="" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <input type="hidden" name="id" id="id" value="{{$id}}">
        <input type="hidden" name="status" id="id" value="{{$admission->status}}">
        <table id="admissions" class="table table-striped table-hover table-bordered" align="center"  style="width: 900px">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.student_type') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.major') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
                <th>{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            </tr>
            </thead>
            <tr>
                <td>{{$admission->fullname}}</td>
                <td>{{$admission->studentno}}</td>
                <td>@if ($admission->program == 12)
                        本科
                    @elseif ($admission->program == 14)
                        专科
                    @endif
                </td>
                <td>@if ($admission->type == 12)
                        本科
                    @elseif ($admission->type == 14)
                        专科
                    @endif</td>
                <td>{{$admission->major_name}}</td>
                <td>{{$admission->admissionyear}}</td>
                <td>
                    @if ($admission->admissionsemester == '01')
                        春季
                    @elseif ($admission->admissionsemester == '02')
                        秋季
                    @endif
                </td>
                <td>@if ($admission->status == 6)
                        毕业
                    @endif
                </td>
            </tr>
        </table>
        <br>
        <div align="center">
            @if ($admission_recovery->count == 0)
                {{Lang::get('admin/admissions/messages.no_recovery_records')}}
            @else
                <br>
                <br>
                <table id="recovery_table" class="table table-striped table-hover table-bordered" align="center"  style="width: 900px">
                    <thead>
                    <tr>
                        <th>{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                        <th>{{{ Lang::get('admin/admissions/table.application_year') }}}</th>
                        <th>{{{ Lang::get('admin/admissions/table.application_semester') }}}</th>
                        <th>{{{ Lang::get('admin/admissions/table.province_final_result') }}}</th>
                        <th>{{{ Lang::get('admin/admissions/table.province_final_device') }}}</th>
                    </tr>
                    </thead>
                    <tr>
                        <td>{{$admission_recovery->studentno}}</td>
                        <td>{{$admission_recovery->recovery_year}}</td>
                        <td>
                            @if ($admission_recovery->recovery_semester == '01')
                                春季
                            @elseif ($admission_recovery->recovery_semester == '02')
                                秋季
                            @endif
                        </td>
                        <td>@if ($admission_recovery->approval_result == 0)
                                未审核
                             @elseif ($admission_recovery->approval_result == 1)
                                同意
                            @elseif ($admission_recovery->approval_result == 2)
                                不同意
                            @endif
                        </td>
                    </tr>
                </table>
            @endif
        </div>
        <br>
        <br>
        <table id="application_recovery" class="table table-striped table-hover table-bordered" align="center" style="width: 900px">
            <tr>
                <td colspan="4">申请恢复学籍</td>
            </tr>
            <tr>
                <td>恢复学籍年度</td>
                <td>
                    <select name="recovery_year" id="recovery_year" style="width:150px;">
                        <option value="">请选择</option>
                        @for ($i=2000;$i<2025;$i++)
                            <option value="{{{$i}}}">{{$i}}</option>
                        @endfor
                    </select>
                </td>
                <td align="center"><h4>恢复学籍学期</h4></td>
                <td>
                    <select name="recovery_semester" id="recovery_semester" style="width:150px;">
                        <option value="">请选择</option>
                        <option value="02">秋季</option>
                        <option value="01">春季</option>
                    </select>
                </td>
            </tr>
        </table>
        <br>
        <div align="center">

            <button id="btnSave" name="state" value="2" class="btn btn-small btn-info" type="submit">
                {{{ Lang::get('admin/admissions/table.ok') }}}</button>

        </div>
    </form>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {
            $('#form').submit(function () {
                var recovery_year = $('#recovery_year').val();
                var recovery_semester = $('#recovery_semester').val();
                if (recovery_year == "") {
                    alert('请选择恢复学籍年度！');
                    $('#recovery_year').focus();
                    return false;
                }
                if (recovery_semester == "") {
                    alert('请选择恢复学籍学期！');
                    $('#recovery_semester').focus();
                    return false;
                }
                return true;
            });
        });

    </script>
@stop