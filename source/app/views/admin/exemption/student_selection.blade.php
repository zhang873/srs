@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.choose_course') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{ URL::to('admin/exemption/insert_exemption') }}" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="selection" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major_classification') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.credit') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.selection_status') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.select') }}}</th>
            </tr>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td class="col-md-2">{{ $exemption->course_id}} {{Form::hidden('student_id',$exemption->student_id)}}</td>
                    <td class="col-md-2">{{ $exemption->course_name }}</td>
                    <td class="col-md-2">{{ $exemption->classification }}</td>
                    <td class="col-md-2">{{ $exemption->credit}}</td>
                    <td class="col-md-2">@if ($exemption->selection_status==1) {{{Lang::get('admin/exemption/table.yes')}}} @elseif ($exemption->selection_status==0) {{{Lang::get('admin/exemption/table.no')}}}  @endif</td>
                    <td class="col-md-2">@if ($exemption->selection_status==1) <input type="radio" name="course_id" value="{{{$exemption->course_id}}}">  @endif</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" align="center"><input type="submit" value="  {{{ Lang::get('admin/exemption/table.input_score') }}}"> </td>

            </tr>
            </thead>

        </table>
     </form>
@stop