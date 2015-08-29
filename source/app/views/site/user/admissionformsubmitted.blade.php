@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('user/user.admission') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
<h1>{{ Lang::get('user/user.admission') }}</h1>
</div>
<p>{{ Lang::get('user/user.admission_submitted_title') }}</p>
<p>{{ Lang::get('user/user.admission_submitted_id', array('id' => $admission['admissionid'])) }}</p>
<p>{{ Lang::get('user/user.admission_submitted_message') }}</p>
<p><a href="{{ URL::to('/user/main') }}">{{ Lang::get('user/user.admission_submitted_return') }}</a></p>
@stop
