@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}

            <div class="pull-right">
                <a href="{{{ URL::to('admin/exemption/input_student') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/exemption/title.input_student') }}}</a>
            </div>
        </h3>
    </div>

   {{-- choose input form --}}
    <form id="input_course" class="form-horizontal" method="post" action="{{ URL::to('admin/exemption/query_list') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <table id="query_exemption" class="table table-striped table-hover" align="center" width="500px">
            <thead>
            <tr>
                <td colspan="4" class="col-md-2">{{{ Lang::get('admin/exemption/title.choose_query') }}}</td>
            </tr>
            <tr>
                <td class="col-md-2">{{{ Lang::get('admin/exemption/table.student_id') }}}</td>
                <td class="col-md-2"><input type="text" name="student_id"></td>
                <td>{{{ Lang::get('admin/exemption/table.student_name') }}}</td>
                <td><input type="text" name="student_name"></td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.major_classification') }}}</td>
                <td class="col-md-2">
                    <select name="major_classification" id="major_classification">
                        <option value="2">全部</option>
                        <option value="0">专科</option>
                        <option value="1">本科</option>

                    </select>
                </td>
                <td>{{{ Lang::get('admin/exemption/table.final_result') }}}</td>
                <td>
                    <select name="final_result" id="final_result">
                        <option value="3">全部</option>
                        <option value="0">不通过</option>
                        <option value="1">通过</option>
                        <option value="2">未审核</option>

                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.input_year') }}}</td>
                <td><input type="text" name="input_year"></td>
                <td>{{{ Lang::get('admin/exemption/table.input_semester') }}}</td>
                <td class="col-md-2">
                    <select name="input_semester">
                        <option value="2">全部</option>
                        <option value="0">秋季</option>
                        <option value="1">春季</option>

                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.major_inside') }}}</td>
                <td class="col-md-2">
                    <select name="major_inside">
                        <option value="2">全部</option>

                    </select>
                </td>
                <td>{{{ Lang::get('admin/exemption/table.student_type') }}}</td>
                <td>
                    <select name="student_type">
                        <option value="2">全部</option>
                        <option value="0">专科</option>
                        <option value="1">本科</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{{ Lang::get('admin/exemption/table.remark') }}}</td>
                <td colspan="3"><input type="text" name="remark" style="width: 600px;height: 30px;border-bottom-color:#616161"></td>
            </tr>
            <tr>
                <td colspan="4" align="center"><input type="submit" value="  {{{ Lang::get('admin/exemption/table.query') }}}"> </td>
            </tr>
            </thead>
        </table>
    </form>
   <br>
   <br>
   <br>
        <table id="exemption_list" class="table table-striped table-hover" align="center">
            <thead>
            <tr>
                <td colspan="19"  class="col-md-2" align="center">{{{ Lang::get('admin/exemption/title.exemption_info') }}}</td>
            </tr>
            <tr>
                <th>{{{ Lang::get('admin/exemption/table.class_id') }}}</th>
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
                <th>{{{ Lang::get('admin/exemption/table.action') }}}</th>
            </tr>
            @foreach ($exemptions as $exemption)
                <tr>
                    <td></td>
                    <td>{{ $exemption->student_id }}</td>
                    <td>{{ $exemption->student_name }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $exemption->course_inside }}</td>
                    <td>{{ $exemption->classification }}</td>
                    <td>{{ $exemption->credit }}</td>
                    <td>{{ $exemption->application_year }}</td>
                    <td>{{ $exemption->exemption_type_id }}</td>
                    <td>{{ $exemption->major_name_outer }}</td>
                    <td>{{ $exemption->course_name_outer }}</td>
                    <td>{{ $exemption->classification_outer }}</td>
                    <td>{{ $exemption->credit_outer }}</td>
                    <td>{{ $exemption->certification_year }}</td>
                    <td>{{ $exemption->agency_name }}</td>
                    <td>{{ $exemption->final_result }}</td>
                    <td>{{ $exemption->remark }}</td>
                    <td>
                        <a href="{{{ URL::to('admin/exemption/' . $exemption_id . '/edit' ) }}}" class="iframe btn btn-xs">{{{ Lang::get('button.edit') }}}</a>
                        <a href="{{{ URL::to('admin/exemption/' . $exemption_id . '/delete' ) }}}" class="iframe btn btn-xs">{{{ Lang::get('button.delete') }}}</a>
                    </td>
                </tr>
            @endforeach
            </thead>
            <tbody>
            </tbody>
        </table>

@stop

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
            oTable = $('#exemption').dataTable( {
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bAutoWidth":  true,
                "sAjaxSource": "{{ URL::to('admin/exemption/data') }}",
                "fnDrawCallback": function ( oSettings ) {
                    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                    if ( oSettings.bSorted || oSettings.bFiltered || oSettings.bDrawing)
                    {
                        for ( var i=0, iLen=oSettings.aiDisplayMaster.length ; i<iLen ; i++ )
                        {
                            var counter = oSettings._iDisplayStart + i + 1;
                            $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( counter );
                        }
                    }
                },
                "aaSorting": [ [0,'asc'] ]
            });
        });
    </script>
@stop
