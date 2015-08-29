@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('user/user.login') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1>{{ Lang::get('user/user.login_title') }}</h1>
	<h1>{{ Lang::get('user/user.login') }}</h1>
</div>
{{ Confide::makeLoginForm()->render() }}
@stop
