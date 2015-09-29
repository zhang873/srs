@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	<div class="page-header" align="center">
		<h3>
			{{{ $title }}}
		</h3>
	</div>

	<table id="school" class="table table-striped table-hover text-center table-bordered" width="60%" align="center">
		<thead>
			<tr>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.punish_cause') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.code') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.action') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
    <br>
    <br>
    <div align="center">
        <a href="{{{ URL::to('admin/admissions/define_punish_cause') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/table.create_a_new_punish_cause') }}}</a>
    </div>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
		var oTable;
		$(document).ready(function() {
				oTable = $('#school').dataTable( {
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('admin/admissions/data_punish_cause') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	     		},
	     		"aaSorting": [ [1,'asc'] ]
			});
		});
	</script>
@stop

