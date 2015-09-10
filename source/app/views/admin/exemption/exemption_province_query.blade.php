@extends('admin.layouts.default')

{{-- Web site Title --}}

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}

            <div class="pull-right">
                <a href="{{{ URL::to('admin/exemption_province/select_semester') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span>{{{ Lang::get('admin/exemption/title.input_student') }}}</a>
            </div>
        </h3>
    </div>

   {{-- choose input form --}}
<form method="post" action="{{URL::to('admin/exemption_province/query_exemption_province')}}">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" id="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

    <table id="query_exemption" align="center" class="table">
        <thead>
        <tr>
            <td colspan="4"  align="center">{{{ Lang::get('admin/exemption/title.choose_query') }}}</td>
        </tr>
        <tr>
            <td class="pull-right">{{{ Lang::get('admin/exemption/table.student_id') }}}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="student_id"></td>
            <td class="pull-right">{{{ Lang::get('admin/exemption/table.student_name') }}}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="student_name"></td>
        </tr>
        <tr>
            <td class="pull-right">起始时间（例<br>如：2015年春季)&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="start_year">
                    <option>请选择</option>
                    @for ($i=1990;$i<2050;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <select name="start_semester">
                    <option>请选择</option>
                    <option value="0">秋季</option>
                    <option value="1">春季</option>

                </select>
            </td>

            <td class="pull-right">{{{ Lang::get('admin/exemption/table.final_result') }}}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="final_result" id="final_result">
                    <option value="3">全部</option>
                    <option value="0">不通过</option>
                    <option value="1">通过</option>
                    <option value="2">未审核</option>

                </select>
            </td>
        </tr>
        <tr>
            <td class="pull-right">至终止时间（例<br>如：2015年春季)&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="terminal_year">
                    <option>请选择</option>
                    @for ($i=1990;$i<2050;$i++)
                        <option value="{{{$i}}}">{{$i}}</option>
                    @endfor
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <select name="terminal_semester">
                    <option>请选择</option>
                    <option value="0">秋季</option>
                    <option value="1">春季</option>

                </select>
            </td>
            <td class="pull-right">{{{ Lang::get('admin/exemption/table.major_classification') }}}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="major_classification" id="major_classification">
                    <option value="2">全部</option>
                    <option value="14">专科</option>
                    <option value="13">本科</option>

                </select>
            </td>
        </tr>
        <tr>
            <td class="pull-right">{{{ Lang::get('admin/exemption/table.student_classification') }}}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="student_classification">
                    <option value="2">全部</option>
                    <option value="14">专科</option>
                    <option value="13">本科</option>
                </select>
            </td>
            <td class="pull-right"></td>
            <td></td>
        </tr>
        </thead>
    </table>
    <div  align="center">
        <button id="btnSubmit" type="submit" name="state" value="2" class="btn btn-small btn-info" >查询</button>
    </div>
</form>
   <br>

    <div align="center">
        {{{ Lang::get('admin/exemption/title.exemption_info') }}}
    </div>
    <br>
    <form method="post" action="{{URL::to('admin/exemption_province/index')}}">
        <table id="exemption" class="table table-striped table-hover table-bordered"  align="center">
            <thead>
            <tr>
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
                <th>{{{ Lang::get('admin/exemption/table.multi-results') }}}
                    <label id="checkAll"><a href="#" >全选</a></label>
                    <br>
                    <label id="checkNo"><a href="#">全否</a></label>
                </th>
            </tr>
            </thead>
            @foreach ($exemptions as $exemption)
                <tr>
                    @if(is_null($exemption))
                        <td colspan="20">数据库中无相应的记录</td>
                    @else
                        <td style="font-size: 8px;">{{ $exemption->student_id }}</td>
                        <td>{{ $exemption->student_name }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $exemption->course_inside }}</td>
                        <td>@if( $exemption->classification==14) 专科  @elseif  ( $exemption->classification==12) 本科 @endif</td>
                        <td>{{ $exemption->credit }}</td>
                        <td>{{ $exemption->application_year }}<br>
                            @if ($exemption->application_semester==0) 秋季 @else春季@endif </td>
                        <td>{{ $exemption->type_name }}</td>
                        <td>{{ $exemption->major_name_outer }}</td>
                        <td>{{ $exemption->course_name_outer }}</td>
                        <td>@if( $exemption->classification_outer==14) 专科  @elseif  ( $exemption->classification_outer==12) 本科 @endif</td>
                        <td>{{ $exemption->credit_outer }}</td>
                        <td>{{ $exemption->certification_year }}</td>
                        <td>{{ $exemption->agency_name }}</td>
                        <td>@if ( $exemption->final_result == 0) 不通过  @elseif  ( $exemption->final_result == 1) 通过 @elseif ( $exemption->final_result == 2) <label>未审核</label>@endif</td>
                        <td>{{ $exemption->failure_cause }}</td>
                        <td>
                            @if ($exemption->final_result == 1)
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/nopass' ) }}}" class="text-danger iframe">{{{ Lang::get('general.nopass') }}}</a>
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/nocheck' ) }}}" class="iframe">{{{ Lang::get('general.nocheck') }}}</a>
                            @elseif ($exemption->final_result == 2)
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/pass' ) }}}" class="text-success iframe">{{{ Lang::get('general.pass') }}}</a><br>
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/nopass' ) }}}" class="text-danger iframe">{{{ Lang::get('general.nopass') }}}</a>
                            @else
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/pass' ) }}}" class="text-success iframe">{{{ Lang::get('general.pass') }}}</a><br>
                                <a href="{{{ URL::to('admin/exemption_province/' . $exemption->id . '/nocheck' ) }}}" class="iframe">{{{ Lang::get('general.nocheck') }}}</a>

                            @endif
                        </td>
                        <td><input type = "checkbox" name = "checkItem[]" id= "checkItem" value="{{ $exemption->id }}"></td>
                     @endif
                </tr>
            @endforeach
        </table>
        <div align="center">
            <button id="btnPass" name="state" value="1" type="submit" class="btn btn-small btn-info" >通过</button>
        </div>
        </form>
    <div id="show">
        <input type="hidden" id="btnValue" value="2" />
    </div>
@stop



@section('styles')

    <style>
        .rlbl{
            text-align:right;
            width:160px;
        }
        .error {color: #FF0000;}
    </style>
@stop


{{-- Scripts --}}
@section('scripts')

<script type="text/javascript">

    $(document).ready(function() {
        oTable = $('#exemption1').dataTable({
            "searching": false,
            "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "{{{ Lang::get('admin/exemption/table.records_per_page') }}} _MENU_",
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
            "ajax": {
                "url": "{{ URL::to('admin/exemption_province/data') }}",
                "data": function (d) {
                    var fields = $(' :checked').serializeArray();
                    if (fields.length > 0) {
                        d["checkitem[]"] = new Array();
                        $.each(fields, function (n, value) {
                            d["checkitem[]"].push(n.value);
                        });
                    }
                    d["student_id"] = $('#student_id').val();
                    d["student_name"] = $('#student_name').val();
                    d["major_classification"] = $('#major_classification').val();
                    d["final_result"] = $('#final_result').val();
                    d["start_year"] = $('#start_year').val();
                    d["start_semester"] = $('#start_semester').val();
                    d["terminal_year"] = $('#terminal_year').val();
                    d["terminal_semester"] = $('#terminal_semester').val();
                    d["student_classification"] = $('#student_classification').val();
                    d["state"] = $('#btnValue').val();
                    d["_token"] = $('#_token').val();
                }

            },

            "aaSorting": [[1, 'asc']]
        });

            $("#btnQuery").click(function () {
                $("#btnValue").val(2);
                oTable.fnReloadAjax();
            });
            $("#btnPass").click(function () {
                $("#btnValue").val(1);
                oTable.fnReloadAjax();
            });
        $(function(){
            $('#checkAll').click(function () {
                //
                $(':checkbox').prop("checked", true);
            });
            $('#checkNo').click(function () {

                $(':checkbox').prop("checked", false);
            });
        })

    });

</script>
@stop