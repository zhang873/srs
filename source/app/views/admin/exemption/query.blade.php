@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.input_score_stuinfo') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{URL::to('admin/exemption/input_require') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="users" class="table table-striped table-hover" width="66%">
            <thead>
            <tr>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                <th class="col-md-2">{{{ Lang::get('admin/exemption/table.input_flag') }}}</th>
            </tr>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td>{{ $exemption->student_id }} </td>
                    <td>{{ $exemption->student_name }}</td>
                    <td><input type = "radio" name = "student_id" value="{{ $exemption->student_id }}"></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" align="center"><input type="submit" value="  {{{ Lang::get('admin/exemption/table.input_score') }}}"> </td>

            </tr>
            </thead>

        </table>
     </form>

@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#stuinfo').dataTable( {
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_"
                },
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "{{ URL::to('admin/exemption/data') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                },
                "aaSorting": [ [0,'asc'] ]
            });
        });
    </script>
@stop