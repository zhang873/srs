@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.choose_stuinfo') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{ URL::to('admin/exemption/student_selection') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="users" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.school') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major_classification')}}}  </th>
            </tr>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td class="col-md-2">{{ $exemption->student_id}} {{Form::hidden('student_id',$exemption->student_id)}}</td>
                    <td class="col-md-2">{{ $exemption->student_name }}</td>
                    <td class="col-md-2">{{ $exemption->major }}</td>
                    <td class="col-md-2">{{ $exemption->campus }}</td>
                    <td class="col-md-2">{{ $exemption->classification }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" align="center"><input type="submit" value="  {{{ Lang::get('admin/exemption/table.browse_selection') }}}"> </td>

            </tr>
            </thead>

        </table>
     </form>
@stop