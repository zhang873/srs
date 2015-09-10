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
            <a href="{{{ URL::to('admin/admissions/group_define') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/groups/title.groups_create') }}}</a>
        </div>
        			
    </h3>
</div>

<table id="groupslist" class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.groups_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.groups_sysid') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.groups_program_rank') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.groups_program') }}}</th>            
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.student_qty') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/groups/table.created_at') }}}</th>
            <th class="col-md-2">{{{ Lang::get('table.actions') }}}</th>
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
    $(document).ready(function () {
        oTable = $('#groupslist').dataTable({
            "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "{{{ Lang::get('admin/groups/table.records_per_page') }}} _MENU_"
            },
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "{{ URL::to('admin/groups/get_groups_data') }}",
            "fnDrawCallback": function (oSettings) {
                $(".iframe").colorbox({iframe: true, width: "80%", height: "80%"});
            }
        });
    });
</script>
@stop