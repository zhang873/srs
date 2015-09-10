@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.input_no_pass_cause') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="nopass" class="form-horizontal" method="post" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <div align="center">{{{Lang::get('admin/exemption/title.exemption_info')}}}</div>
        <table id="exemption" class="table table-striped table-hover table-bordered" align="center">
            <thead>
            <tr>
                <th>{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.major_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.campus') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_inside') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_classification') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.credit') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.exemption_year') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.exemption_type_id') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.major_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.course_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.classification_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.credit_outer') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.certification_year') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.agency_name') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.final_results') }}}</th>
                <th>{{{ Lang::get('admin/exemption/table.remark') }}}</th>
            </tr>
            </thead>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td>{{ $exemption->student_id }}<input type="hidden" id="exemption_id" value="{{{$exemption->id}}}"> </td>
                    <td>{{ $exemption->student_name }}</td>
                    <td>{{ $exemption->major_name }}</td>
                    <td>{{ $exemption->campus_name }}</td>
                    <td>{{ $exemption->course_name }}</td>
                    <td>@if( $exemption->classification==14) <label>专科</label>  @elseif  ( $exemption->classification==12) <label>专科</label> @endif</td>
                    <td>{{ $exemption->credit }}</td>
                    <td>{{ $exemption->application_year }}</td>
                    <td>{{ $exemption->exemption_type_name }}</td>
                    <td>{{ $exemption->major_name_outer }}</td>
                    <td>{{ $exemption->course_name_outer }}</td>
                    <td>@if( $exemption->classification_outer==14) <label>专科</label>  @elseif  ( $exemption->classification_outer==12) <label>本科</label> @endif</td>
                    <td>{{ $exemption->credit_outer }}</td>
                    <td>{{ $exemption->certification_year }}</td>
                    <td>{{ $exemption->agency_name }}</td>
                    <td>@if ( $exemption->final_result == 0) <label>不通过</label>  @elseif  ( $exemption->final_result == 1) <label>通过</label> @elseif ( $exemption->final_result == 2) <label>未审核</label>@endif</td>
                    <td>{{ $exemption->failure_cause }}</td>
                </tr>
            @endforeach
        </table>
        <table class="table table-striped table-hover">
            <tr>
                <td>审核不通过原因</td>
                <td><input type="text" class="text-info text-primary" style="width: 100%" id="failure_cause" name="failure_cause"></td>
                <td><input type="submit" class="btn btn-info" value="{{{Lang::get('admin/exemption/table.nopass')}}}"></td>
                <td><input type="button" class="btn btn-info" id="cancel" name="cancel" value="取消" onclick="javascript:window.location.href='/admin/exemption/exemption_province'"></td>


            </tr>
        </table>

    </form>
    @stop

        {{-- Scripts --}}
@section('scripts')
            <script type="text/javascript">
                $( "#nopass1" ).submit(function( event ) {
                    event.preventDefault();
                    var $form = $( this ),
                            data = $form.serialize(),
                            url = $form.attr( "action" );
                    var posting = $.post( url, { formData: data } );
                    posting.done(function( data ) {
                        if(data.fail) {
                            $.each(data.errors, function( index, value ) {
                                var errorDiv = '#'+index+'_error';
                                $(errorDiv).addClass('required');
                                $(errorDiv).empty().append(value);
                            });
                            $('#successMessage').empty();
                        }
                        if(data.success) {
                            $('.register').fadeOut(); //hiding Reg form
                            var successContent = '<div class="message"><h3>Registration Completed Successfully</h3><h4>Please Login With the Following Details</h4><div class="userDetails"><p><span>Email:</span>'+data.email+'</p><p><span>Password:********</span></p></div></div>';
                            $('#successMessage').html(successContent);
                        } //success
                    }); //done
            </script>
@stop