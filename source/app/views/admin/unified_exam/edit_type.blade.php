@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/unified_exam/title.edit_unified_exam_info') }}}
        </h3>
    </div>
    {{-- choose input form --}}
    <form id="save_require" class="form-horizontal" method="post" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

        <!-- ./ csrf token -->

        <table id="users" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/unified_exam/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.unified_exam_type') }}}</th>
                     <th>{{{ Lang::get('admin/unified_exam/table.input_year') }}}</th>
                <th>{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</th>
            </tr>

                <tr>
                    <td class="col-md-2">{{ $exemption->studentno}}</td>
                    <td class="col-md-2">{{ $exemption->fullname }}<input type="hidden" name="id" value="{{{$exemption->id}}}" /></td>
                    <td class="col-md-2">{{ $exemption->type }}</td>
                    <td class="col-md-2">{{ $exemption->registration_year }}</td>
                    <td class="col-md-2">
                        @if ($exemption->registration_semester==0) 秋季 @elseif ($exemption->registration_semester==1) 春季 @endif
                    </td>
                </tr>


            </thead>

        </table>
        <br><br><br>
        <table id="select_subject" class="table table-striped table-hover" align="center" width="500px">
            <thead>
            <tr>
                <td colspan="4" class="col-md-2"> <h3>{{{ Lang::get('admin/unified_exam/title.choose_edit_query') }}}</h3></td>
            </tr>
            <tr>
                <td class="col-md-2">{{{ Lang::get('admin/exemption/table.input_year') }}}</td>
                <td class="col-md-2">
                    <select id="input_year" name="input_year">
                        <option value="">请选择</option>
                        @for ($i=2000;$i<=2025;$i++)
                            <option value="{{{$i}}}">{{{$i}}}</option>
                        @endfor
                    </select>
                </td>
                <td class="col-md-2">{{{ Lang::get('admin/unified_exam/table.input_semester') }}}</td>
                <td class="col-md-2">
                    <select id="input_semester" name="input_semester">
                        <option value="">请选择</option>
                        <option value="02">秋季</option>
                        <option value="01">春季</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    {{{ Lang::get('admin/unified_exam/table.unified_exam_type') }}}
                </td>
                <td colspan="3">
                    <select name="unified_exam_type">
                        <option>{{{ Lang::get('admin/unified_exam/table.pleaseselect') }}}</option>
                        @foreach  ($types as $type)
                            <option value="{{$type->id }}">{{$type->type}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center"><input type="submit" value="  {{{ Lang::get('admin/unified_exam/table.save') }}}"> </td>

            </tr>
        </table>
    </form>
@stop