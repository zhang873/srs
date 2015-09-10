@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/exemption/title.choose_stuinfo') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="choose_input" class="form-horizontal" method="post" action="{{URL::to('admin/exemption/create') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="ids" value="{{$exemption->id}}" id="ids" >
        <!-- ./ csrf token -->
<div>
        <table id="users" class="table table-striped table-hover" align="center">
            <thead>
                <tr>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_id') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.student_name') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.school') }}}</th>
                    <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major_classification')}}}  </th>
                </tr>
            </thead>
                <tr>
                    <td class="col-md-2">{{ $exemption->student_id}}</td>
                    <td class="col-md-2">{{ $exemption->student_name }}</td>
                    <td class="col-md-2">{{ $exemption->major_name }}</td>
                    <td class="col-md-2">{{ $exemption->campus_name }}</td>
                    <td class="col-md-2">
                                @if ($exemption->major_classification == 12)
                					本科
                				@elseif ($exemption->major_classification == 14)
                					专科
                                @endif
                </tr>
            <tr>
                <td colspan="5" align="center"><button type="button" value="2" id="btnBrowse">{{{ Lang::get('admin/exemption/table.browse_selection') }}}</button> </td>
            </tr>
        </table>
</div>
        <br>
        <br>
    <div id="show_selection">
    <table id="selection" class="table table-striped table-hover table-bordered" align="center" style="width:70%;text-align: center">
        <thead>
        <tr>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_id') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.course_name') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.major_classification') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.credit') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.selection_status') }}}</th>
            <th class="col-md-2">{{{ Lang::get('admin/exemption/table.select') }}}</th>
        </tr>
        </thead>
            @if (!empty($selections))
                 @foreach($selections as $selection)
                    <tr>
                        <td>{{$selection->course_id}}</td>
                        <td>{{$selection->course_name}}</td>
                        <td>@if ($selection->major_classification == 12)
                                本科
                            @elseif ($selection->major_classification == 14)
                                专科
                            @endif
                        </td>
                        <td>{{$selection->credit}}</td>
                        <td> @if ($selection->selection_status == 1)
                                是
                            @elseif ($selection->selection_status == 0)
                                否
                            @endif</td>
                        <td align="center"><input type = "radio" name = "id[]" id= "id" value="{{{ $selection->course_id }}}"></td>
                    </tr>
                 @endforeach
        </table>
        <div align="center">
            <button id="btnInputScore" name="btnInputScore" value="2" class="btn btn-small btn-info" type="submit" >{{{ Lang::get('admin/exemption/table.input_score') }}}</button>
        </div>
            @else
                <tr><td colspan="6">{{Lang::get('admin/exemption/table.no_selection_record')}}</td></tr>
        </table>
                <div align="center">
                    <button id="btnInputScore" name="btnInputScore" value="2" class="btn btn-small btn-info" type="submit" disabled="disabled">{{{ Lang::get('admin/exemption/table.input_score') }}}</button> &nbsp;&nbsp;
                    <button id="btnBack" name="btnBack" value="3" class="btn btn-small btn-info" type="button" onclick="window.location.href='{{URL::to('admin/exemption/input_student')}}'">{{{ Lang::get('admin/exemption/table.go_back') }}}</button>
                </div>
        @endif
    </div>
    </form>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        function check() {
            var n = $( "input:checked" ).length;
            if (n==0) {
                alert('请选择课程');
                $('#id').focus();
                return false;
            }
            return true;
        }
        $(document).ready(function () {
            $('#show_selection').hide();
            $('#btnBrowse').click(function(){
                $('#show_selection').show();
            });
            $("#btnInputScore").click(function () {
                if (check()) {
                    $.post("{{URL::to('admin/exemption/create') }}",{id:$('#id').val()});
                    return true;
                }else{
                    return false;
                }
            });
        });
        </script>
    @stop