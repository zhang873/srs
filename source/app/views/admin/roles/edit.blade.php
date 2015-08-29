@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')


	{{-- Edit Role Form --}}
	<form class="form-horizontal" method="post" action="" autocomplete="off">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->

			<input class="form-control" type="hidden" name="name" id="name" value="{{{ Input::old('name', $role->name) }}}" />
			{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
					
			<!-- Permissions tab -->
				<div class="form-group">
					<?php $counter = 1;?>
					@foreach ($permissions as $permission)
					<label>
						<input type="hidden" id="permissions[{{{ $permission['id'] }}}]" name="permissions[{{{ $permission['id'] }}}]" value="0" />
						<input type="checkbox" id="permissions[{{{ $permission['id'] }}}]" name="permissions[{{{ $permission['id'] }}}]" value="1"{{{ (isset($permission['checked']) && $permission['checked'] == true ? ' checked="checked"' : '')}}} />
						{{{ $counter }}} - {{{ $permission['display_name'] }}}
					</label><br/>
					<?php $counter += 1;?>
					@endforeach
				</div>
			<!-- ./ permissions tab -->
		</div>
		<!-- ./ tabs content -->

		<!-- Form Actions -->
		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
				<button type="reset" class="btn btn-default">{{{ Lang::get('admin/users/table.reset') }}}</button>
				<button type="submit" class="btn btn-success">{{{ Lang::get('admin/users/table.ok') }}}</button>
			</div>
		</div>
		<!-- ./ form actions -->
	</form>
@stop
