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
        <div class="pull-right">
            <a href="{{{ URL::to('admin/exemption/approve_exemption') }}}"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_province') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/exemption/exemption_type') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_type') }}}</a>&nbsp;&nbsp;
            <a href="{{{ URL::to('admin/exemption/exemption_agency') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.exemption_agency') }}}</a>&nbsp;&nbsp;
        </div>
	</div>

	<table id="majoroutor" class="table table-striped table-hover text-center table-bordered" width="50%" align="center">
		<thead>
			<tr>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/exemption/table.name') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/exemption/table.sysid') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/exemption/table.action') }}}</th>

			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
    <br>
    <br>
    <div align="center">
        <a href="{{{ URL::to('admin/exemption/data_add_exemption_major_outer') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/exemption/table.create_a_new_major_outer') }}}</a>
    </div>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
		var oTable;
		$(document).ready(function() {
				oTable = $('#majoroutor').dataTable( {
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('admin/exemption/data_exemption_major_outer') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	     		},
	     		"aaSorting": [ [1,'asc'] ]
			});
		});
	</script>
@stop

