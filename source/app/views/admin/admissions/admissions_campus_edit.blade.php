@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
   {{-- choose input form --}}
    <form id="form" class="form-horizontal" method="post"  autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="exemption" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <td colspan="4" class="col-md-2">{{{ Lang::get('admin/exemption/title.input_score_course') }}}</td>
            </tr>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major_classification') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.credit') }}}</th>
            </tr>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td>{{ $exemption->course_id }}</td>
                    <td>{{ $exemption->course_name }} </td>
                    <td>{{ $exemption->classification }}</td>
                    <td>{{ $exemption->credit }}
                        <input type="hidden" name="student_id" value={{$exemption->student_id}}>
                        <input type="hidden" name="course_id" value={{$exemption->course_id}}>
                        <input type="hidden" name="course_name" value={{$exemption->course_name}}>
                        <input type="hidden" name="credit" value={{$exemption->credit}}>
                        <input type="hidden" name="classification" value={{$exemption->classification}}>
                    </td>
                </tr>
            @endforeach
            </thead>
            <tbody>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <table id="course_outer" class="table table-striped table-hover" align="center" width="600px">
            <thead>
            <tr>
                <td colspan="4" class="col-md-2">{{{ Lang::get('admin/exemption/title.input_course_name_outer') }}}</td>
            </tr>
            <tr>
                <td class="col-md-2">{{{ Lang::get('admin/exemption/table.major_outer').' （*）' }}}</td>
                <td class="col-md-2">
                    <select name="major_outer" id="major_outer">
                        <option>{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        @foreach  ($majors as $major)
                             <option value="{{{$major->id}}}">{{{$major->name}}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.course_outer').' （*）' }}}</td>
                <td><input type="text" name="course_outer" id="course_outer" value="" />
                    {{ $errors->first('course_outer', '<span class="help-inline">:message</span>') }}</td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.agency_name').' （*）' }}}</td>
                <td class="col-md-2">
                    <select name="agency_id" id="agency_id">
                        <option>{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        @foreach  ($agencys as $agency)
                            <option value="{{$agency->id}}">{{$agency->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.certification_year').' （*）' }}}</td>
                <td><input type="text" name="certification_year"></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.credit_outer').' （*）（例如：3）' }}}</td>
                <td><input type="text" name="credit_outer"></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.exemption_type_id').' （*）' }}}</td>
                <td class="col-md-2">
                    <select name="exemption_type_id">
                        <option>{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        @foreach  ($types as $type)
                            <option value="{{$type->id }}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.classification_outer').' （*）' }}}</td>
                <td class="col-md-2">
                    <select name="classification_outer">
                        <option>{{{ Lang::get('admin/exemption/table.pleaseselect') }}}</option>
                        <option value="14">专科</option>
                        <option value="13">本科</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.score').' （*）' }}}</td>
                <td><input type="text" name="score"></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.remark') }}}</td>
                <td><input type="text" name="remark" style="width: 600px;height: 30px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="center" style=""><input type="submit" id="savedata" name="savedata" value=" {{{ Lang::get('admin/exemption/table.save') }}}"> </td>
            </tr>
            </thead>
        </table>
     </form>
@stop
