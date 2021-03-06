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
            <br>
    </div>

	<table id="school" class="table table-striped table-hover text-center table-bordered" width="60%" align="center">
		<thead>
			<tr>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.reward_level') }}}</th>
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
        <a href="{{{ URL::to('admin/admissions/define_reward') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/admissions/table.add_a_reward_level') }}}</a>
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
		        "sAjaxSource": "{{ URL::to('admin/admissions/data_reward_index') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	     		},
	     		"aaSorting": [ [1,'asc'] ]
			});
		});
	</script>
@stop

