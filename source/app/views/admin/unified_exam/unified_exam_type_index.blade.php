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
            <a href="{{{ URL::to('admin/unified_exam/unified_exam_cause') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.unified_exam_cause') }}}</a>

            <a href="{{{ URL::to('admin/unified_exam/unified_exam_subject') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.unified_exam_subject') }}}</a>

            <a href="{{{ URL::to('admin/unified_exam/approve_unified_exam') }}}" ><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admin.unified_exam') }}}</a>
        </div>
        <br>
	</div>

	<table id="unfied_exam_type" class="table table-striped table-hover text-center table-bordered" width="50%" align="center">
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
        <a href="{{{ URL::to('admin/unified_exam/create_unified_exam_type') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/unified_exam/table.create_a_new_type') }}}</a>
    </div>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
		var oTable;
		$(document).ready(function() {
				oTable = $('#unfied_exam_type').dataTable( {
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "{{{ Lang::get('admin/users/table.records_per_page') }}} _MENU_"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('admin/unified_exam/data_unified_exam_type') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	     		},
	     		"aaSorting": [ [1,'asc'] ]
			});
		});
	</script>
@stop

