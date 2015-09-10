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
    <form method="post" action="">
    <div align="center">
        {{$school->school_name}}分校中的教学点
    </div>
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <input type="hidden" id="id" value="{{$id}}">
    <input id="selectedCampuses" name="selectedCampuses" type="hidden" value="" />
	<table id="school" class="table table-striped table-hover text-center table-bordered" width="60%" align="center">
		<thead>
			<tr>
                <th></th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.campus_code') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.campus_name') }}}</th>
                <th class="col-md-2 text-center">{{{ Lang::get('admin/admissions/table.action') }}}</th>
			</tr>
		</thead>

	</table>
    <br>
    <br>
    <div align="center">
        <button type="submit" class="btn btn-small btn-info iframe" id="btnOK">{{{ Lang::get('general.ok') }}}</button>
    </div>
</form>
@stop



{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

            function countCheckedBoxes() {
                var n = $( "input:checked" ).length;
                if (n>0) {
                    $("#btnOK").attr("disabled", false);
                    for (i=0;i<$( "input:checked" ).length;i++) {
                        if (i==0) {
                            $("#selectedCampuses").val($( "input:checked" )[i].value);
                        } else {
                            $("#selectedCampuses").val($("#selectedCampuses").val() + ',' + $( "input:checked" )[i].value);
                        }
                    }
                } else {
                    $("#btnOK").attr("disabled", true);
                }
            }
            var oTable;
            $(document).ready(function() {
                oTable = $('#school').dataTable({
                    "searching": false,
                    "ordering": false,
                    "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                        "sLengthMenu": "{{{ Lang::get('admin/admissions/table.records_per_page') }}} _MENU_",
                        "sProcessing": "正在加载中......",
                        "sZeroRecords": "没有数据！",
                        "sEmptyTable": "表中无数据存在！",
                        "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
                        "sInfoEmpty": "显示0到0条记录",
                        "sInfoFiltered": "数据表中共为 _MAX_ 条记录",
                        "oPaginate": {
                            "sFirst": "首页",
                            "sPrevious": "上一页",
                            "sNext": "下一页",
                            "sLast": "末页"
                        }
                    },
                    "bFilter": true,
                    "bProcessing": true,
                    "bServerSide": true,
                    "bAutoWidth": true,
                    "sAjaxSource": "{{ URL::to('admin/admissions/data_school_add_campus') }}",
                    /*    "ajax": {
                     "url": "{ URL::to('admin/admissions/school_add_campus') }}}",
                     "data": function ( d ) {
                     d["selectedCampuses"]= $('#selectedCampuses').val();
                     d["id"]= $('#id').val();
                     }
                     },
                     */
                    "aaSorting": [[0, 'asc']]
                });
                /*    $.ajax({
                 type: "post",
                 url: "{URL::to('admin/admissions/school_add_campus')}}}",
                 data: "selectedCampuses=" + $('#selectedCampuses').val() + "&" + "id="+$('#id').val() //要传的值
                 });
                 */
                /*        oTable.on('draw', function() {
                    $('#checkItem').onclick(countCheckedBoxes());
                });
                 */

                $("#btnOK").click(function () {
                    countCheckedBoxes();
                    $('#form').submit();
                });

            });
    </script>
@stop

