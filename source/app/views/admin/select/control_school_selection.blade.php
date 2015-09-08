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
	</div>

	<table id="school_selection" class="table table-striped table-hover">
		<thead>
			<tr>
                <th class="col-md-2">{{{ Lang::get('admin/select/table.mid') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/select/table.school_name') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/select/table.school_id') }}}</th>
				<th class="col-md-2">{{{ Lang::get('admin/select/table.selection') }}}<br>
				    <a href="javascript:void(0)" id="selectAll">{{{ Lang::get('admin/select/table.selection_all') }}}</a>&nbsp;
				    <a href="javascript:void(0)" id="selectNone">{{{ Lang::get('admin/select/table.selection_none') }}}</a></th>
				<th class="col-md-2">{{{ Lang::get('admin/select/table.authority') }}}</th>

			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div align="center">
        <button id="btnSave" name="state" value="1" type="submit" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/select/title.save_state') }}}</button>
    </div>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop


@section('styles')

<style>
    .myLink{
        background-color:transparent;
        border-top:0px;
        border-left:0px;
        border-right:0px;
        border-bottom:1px solid #0000ff;
        color: #0000ff;
    }

    .width1{
        width:80px;
    }
    .width2{
        width:100px;
    }
    .col-md-05 {
        width: 5%;
    }

</style>
@stop
{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
		var oTable;
		$(document).ready(function() {
			oTable = $('#school_selection').dataTable( {
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
		        "ajax": {
                    "url": "{{ URL::to('admin/select/control_school_selection_data') }}",
                    "data": function ( d ) {
                        var fields = $('#school_selection :checked').serializeArray();
                        if (fields.length > 0) {
                            d["checkitem[]"] = new Array();
                            $.each(fields, function(n, value){
                                d["checkitem[]"].push(value.value);
                            });
                        }
                        d["type"]=$('#btnValue').val();
                    }
                },
		        "fnDrawCallback": function ( oSettings ) {

                    if ( oSettings.bSorted || oSettings.bFiltered || oSettings.bDrawing)
                    {
                        for ( var i=0, iLen=oSettings.aiDisplayMaster.length ; i<iLen ; i++ )
                        {
                            var counter = oSettings._iDisplayStart + i + 1;
                            $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( counter );
                        }
                    }
	     		},
	     		"aaSorting": [ [0,'asc'] ],
	     		"columnDefs":[{"orderable":false,"targets":3}]
			});
			$("#btnSave").click(function(){
                $("#btnValue").val(1);
                oTable.fnReloadAjax();
            });
            $("#selectAll").click(function(){
                $("#school_selection input:checkbox").each(function(){
                    $(this).prop("checked",true);
                });
            });
            $("#selectNone").click(function(){
                $("#school_selection input:checkbox").each(function(){
                    $(this).prop("checked",false);
                });
            });
		});
	</script>
@stop