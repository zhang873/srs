@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ Lang::get('admin/admissions/title.input_no_pass_cause') }}}
        </h3>
    </div>
   {{-- choose input form --}}
    <form id="nopass" class="form-horizontal" method="post" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        <input type="hidden" name="id" id="id" value="{{$id}}">
        <table class="table table-striped table-hover">
            <tr>
                <td>{{{ Lang::get('admin/admissions/title.input_no_pass_cause') }}}</td>
                <td><input type="text" class="text-info text-primary" style="width: 100%" id="failure_cause" name="failure_cause"></td>
                <td><input type="submit" class="btn btn-info" value="{{{Lang::get('admin/admissions/table.nopass')}}}"></td>
                <td><input type="button" class="btn btn-info" id="cancel" name="cancel" value="取消" onclick="history.go(-1)"></td>


            </tr>
        </table>

    </form>
    @stop
