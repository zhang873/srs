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

			<div class="pull-right">
				<a href="{{{ URL::to('admin/course/department_create') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/course/title.department_create') }}}</a>
			</div>
		</h3>
	</div>

	<table id="departments" class="table table-striped table-hover">
		<thead>
			<tr>
                <th class="col-md-2">{{{ Lang::get('admin/course/table.mid') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/course/table.department_name') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/course/table.department_code') }}}</th>
                <th class="col-md-2">{{{ Lang::get('table.actions') }}}</th>
{{--				<th class="col-md-2">{{{ Lang::get('table.actions') }}}</th>--}}
			</tr>
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
				oTable = $('#departments').dataTable( {
				"searching":false,
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "{{{ Lang::get('admin/course/table.records_per_page') }}} _MENU_",
                        "sInfo": "{{{ Lang::get('admin/course/table.records_sInfo') }}}",
                        "oPaginate": {
                        "sFirst": "{{{ Lang::get('admin/course/table.records_sFirst') }}}",
                        "sPrevious": "{{{ Lang::get('admin/course/table.records_sPrevious') }}}",
                        "sNext": "{{{ Lang::get('admin/course/table.records_sNext') }}}",
                        "sLast": "{{{ Lang::get('admin/course/table.records_sLast') }}}"
                        },
                        "sZeroRecords": "{{{ Lang::get('admin/course/table.records_sZeroRecords') }}}",
                        "sInfoEmpty": "{{{ Lang::get('admin/course/table.records_sInfoEmpty') }}}",
                        "sProcessing": "{{{ Lang::get('admin/course/table.records_processing') }}}"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('admin/course/department_data') }}",
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