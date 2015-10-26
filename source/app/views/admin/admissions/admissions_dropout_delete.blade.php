@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="@if (isset($dropout)){{ URL::to('admin/admissions/delete_dropout?student_id='.$dropout->id) }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        @if (!empty($dropout))
        <input type="hidden" id="student_id" name="student_id" value="{{$dropout->id}}"/>
        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls" align="center">
                确定删除{{$dropout->studentno}}的退学申请吗？
                <br>
                <br>
                <button type="submit" class="btn btn-danger" id="btnSubmit" value="1">{{{ Lang::get('general.delete') }}}</button>
                <button type="button" class="btn-cancel close_popup" onclick="closeWin()">{{{ Lang::get('general.cancel') }}}</button>
            </div>
        </div>
        @else
            {{Lang::get('admin/admissions/messages.no_dropout_records')}}
         @endif
                <!-- ./ form actions -->
    </form>
@stop

{{-- Scripts --}}
@section('scripts')
    <script language="text/javascript">
        function closeWin()
        {
            window.open('','_self','');
            window.close();
        }
    </script>
    @stop