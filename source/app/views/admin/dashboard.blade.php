@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('admin/admin.admin_functions') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
    This is main dash board
</div>

@if (Entrust::hasRole('admin'))
<h3>{{ Lang::get('admin/admin.function_group_admin') }}</h3>
<div>

    @if (Entrust::can('manage_users'))
        <a href="{{{ URL::to('admin/users') }}}">{{{ Lang::get('admin/admin.manageusers') }}}</a>&nbsp;
    @endif
    @if (Entrust::can('manage_roles'))
        <a href="{{{ URL::to('admin/roles') }}}">{{{ Lang::get('admin/admin.manageroles') }}}</a>&nbsp;
    @endif
</div>
jkfajfkjdakfjd
<a href="{{{ URL::to('admin/edu_department') }}}">YYYY</a>&nbsp;
@endif
@stop