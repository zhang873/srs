@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('user/user.profile') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>{{ Lang::get('user/user.items') }}</th>
        <th>{{ Lang::get('user/user.status') }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        @if($isSubmitted)
        	<td>{{ Lang::get('user/user.admission') }}</td>
        	<td>{{ Lang::get('user/user.complete') }}&nbsp;{{ Lang::get('user/user.admission_confirmation_subject') }}:{{ $isSubmitted->admissionid }}</td>
        @else
        	<td><a href="{{URL::to('user/admission')}}">{{ Lang::get('user/user.admission') }}</a></td>
        	<td>{{ Lang::get('user/user.incomplete') }}</td>
        @endif
    </tr>
    </tbody>
</table>
@stop
