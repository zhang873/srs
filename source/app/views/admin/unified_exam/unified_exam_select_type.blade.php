@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/unified_exam/table.select_student_input') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="form" class="form-horizontal" method="post" action="{{ URL::to('admin/unified_exam/select_student') }}" autocomplete="off">

        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

           <!-- ./ csrf token -->

        <table id="select_subject" class="table table-striped table-hover" align="center" style="width:500px">
            <thead>
            <tr>
                <td colspan="4" class="col-md-2"> <h4>{{{ Lang::get('admin/unified_exam/title.choose_unified_type') }}}</h4></td>
            </tr>
            <tr>
                <td class="col-md-2">
                    {{{ Lang::get('admin/unified_exam/table.choose_type') }}}
                </td>
                <td class="col-md-2">
                    <select name="type" id="type">
                        <option value="">{{{ Lang::get('admin/unified_exam/table.pleaseselect') }}}</option>
                        @foreach  ($types as $type)
                            <option value="{{$type->id }}">{{$type->type}}</option>/
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center" style=""><input type="submit" id="savedata" name="savedata" value=" {{{ Lang::get('admin/exemption/table.ok') }}}" onsubmit="mysave()"> </td>
            </tr>
            </thead>
        </table>
    </form>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">

        $(document).ready(
                function() {
                    $("#form").submit(function () {
                        var type = $("#type").val();

                        if (type == "") {
                            alert("请选择免统考类型！");
                            $("#type").focus();
                            return false;
                        }

                        return true;
                    });
                }
        )
    </script>
@stop