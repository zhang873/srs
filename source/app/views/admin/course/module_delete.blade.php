@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" class="form-horizontal" method="post" action="" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->

            <div class="controls">
                <button class="btn btn-default close_popup">{{{ Lang::get('general.cancel') }}}</button>
                <button type="submit" class="btn btn-danger">{{{ Lang::get('general.delete') }}}</button>
            </div>

        <!-- ./ form actions -->
    </form>
@stop