@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
	<!-- Tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-general" data-toggle="tab">{{{ Lang::get('admin/admin.general') }}}</a></li>
			<li><a href="#tab-permissions" data-toggle="tab">{{{ Lang::get('admin/admin.permission') }}}</a></li>
		</ul>
	<!-- ./ tabs -->

	{{-- Create Role Form --}}
	<form class="form-horizontal" method="post" action="" autocomplete="off">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->

		<!-- Tabs Content -->
		<div class="tab-content">
			<!-- Tab General -->
			<div class="tab-pane active" id="tab-general">
				<!-- Name -->
				<div class="form-group {{{ $errors->has('name') ? 'error' : '' }}}">
					<label class="col-md-2 control-label" for="name">{{{ Lang::get('admin/roles/table.name') }}}</label>
                    <div class="col-md-10">
    					<input class="form-control" type="text" name="name" id="name" value="{{{ Input::old('name') }}}" />
    					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
                    </div>
				</div>
				<!-- ./ name -->
			</div>
			<!-- ./ tab general -->

	        <!-- Permissions tab -->
	        <div class="tab-pane" id="tab-permissions">
                <div class="form-group">
                		<?php $counter = 1;?>
                    @foreach ($permissions as $permission)
                    <label>
                        <input class="control-label" type="hidden" id="permissions[{{{ $permission['id'] }}}]" name="permissions[{{{ $permission['id'] }}}]" value="0" />
                        <input type="checkbox" id="permissions[{{{ $permission['id'] }}}]" name="permissions[{{{ $permission['id'] }}}]" value="1"{{{ (isset($permission['checked']) && $permission['checked'] == true ? ' checked="checked"' : '')}}} />
                        {{{ $counter }}} - {{{ $permission['display_name'] }}}                        
                    </label><br/>
                    <?php $counter += 1;?>
                    @endforeach
                </div>
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
