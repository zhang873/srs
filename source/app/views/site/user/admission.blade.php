@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('user/user.admission') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
<h1>{{{ $title }}}</h1>
</div>
<form class="form-horizontal" method="post" action="{{ URL::to('/admin/student/' . $user->id . '/admission') }}"  autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->
    <!-- General tab -->
    <div class="tab-pane active" id="tab-general">     
 		<!-- fullname -->
		<div
			class="form-group {{{ $errors->has('fullname') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="fullname"><span style="color: red">*</span>{{Lang::get('user/user.fullname')}}</label>
			<div class="col-md-10">
				{{ Form::text('fullname', Input::old('fullname'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{$errors->first('fullname', '<span class="help-inline">:message</span>')}}
			</div>
		</div>
		<!-- ./ fullname -->
		<!-- gender -->
		<div class="form-group {{{ $errors->has('gender') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="gender"><span style="color: red">*</span>{{
				Lang::get('user/user.gender') }}</label>
			<div class="col-md-10">
				{{ Form::select('gender', array(
					'm' => Lang::get('user/user.gender_m'), 
					'f' => Lang::get('user/user.gender_f')
				),'',array('class' => 'form-control'))}}
			</div>
		</div>
		<!-- ./ gender -->
		<!-- politicalstatus -->
		<div
			class="form-group {{{ $errors->has('politicalstatus') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="politicalstatus">{{
				Lang::get('user/user.politicalstatus') }}</label>
			<div class="col-md-10">
				{{ Form::select('politicalstatus', array(
					'01' => Lang::get('user/user.politicalstatus_01'), 
					'02' => Lang::get('user/user.politicalstatus_02'), 
					'03' => Lang::get('user/user.politicalstatus_03'), 
					'04' => Lang::get('user/user.politicalstatus_04'), 
					'05' => Lang::get('user/user.politicalstatus_05'), 
					'06' => Lang::get('user/user.politicalstatus_06'), 
					'07' => Lang::get('user/user.politicalstatus_07'), 
					'08' => Lang::get('user/user.politicalstatus_08'), 
					'09' => Lang::get('user/user.politicalstatus_09'), 
					'10' => Lang::get('user/user.politicalstatus_10'), 
					'11' => Lang::get('user/user.politicalstatus_11'), 
					'12' => Lang::get('user/user.politicalstatus_12'), 
					'13' => Lang::get('user/user.politicalstatus_13'), 
				),'',array('class' => 'form-control'))}}			
			</div>
		</div>
		<!-- ./ politicalstatus -->
		<!-- idtype -->
		<div class="form-group {{{ $errors->has('idtype') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="idtype"><span style="color: red">*</span>{{
				Lang::get('user/user.idtype') }}</label>
			<div class="col-md-10">
				{{ Form::select('idtype', array(
					'01' => Lang::get('user/user.idtype_01'), 
					'02' => Lang::get('user/user.idtype_02'), 
					'03' => Lang::get('user/user.idtype_03'), 
					'04' => Lang::get('user/user.idtype_04'), 
					'05' => Lang::get('user/user.idtype_05'), 
				),'',array('class' => 'form-control'))}}			
			</div>
		</div>
		<!-- ./ idtype -->
		<!-- idnumber -->
		<div
			class="form-group {{{ $errors->has('idnumber') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="idnumber"><span style="color: red">*</span>{{
				Lang::get('user/user.idnumber') }}</label>
			<div class="col-md-10">
				{{ Form::text('idnumber', Input::old('idnumber'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('idnumber', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ idnumber -->
		<!-- dateofbirth -->
		<link rel="stylesheet" href="/assets/css/datepicker3.css">
		<div
			class="form-group {{{ $errors->has('dateofbirth') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="dateofbirth"><span style="color: red">*</span>{{
				Lang::get('user/user.dateofbirth') }}</label>
			<div class="col-md-10">
				{{ Form::text('dateofbirth', Input::old('dateofbirth'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('dateofbirth', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		
		<!-- ./ dateofbirth -->
<!-- nationgroup --><div class="form-group {{{ $errors->has('nationgroup') ? 'error' : '' }}}"><label class="col-md-2 control-label" for="nationgroup">{{{ Lang::get('user/user.nationgroup') }}}</label><div class="col-md-10">{{ Form::select('nationgroup', array(
'01' => Lang::get('table.nationgroup_01'), 
'02' => Lang::get('table.nationgroup_02'), 
'03' => Lang::get('table.nationgroup_03'), 
'04' => Lang::get('table.nationgroup_04'), 
'05' => Lang::get('table.nationgroup_05'), 
'06' => Lang::get('table.nationgroup_06'), 
'07' => Lang::get('table.nationgroup_07'), 
'08' => Lang::get('table.nationgroup_08'), 
'09' => Lang::get('table.nationgroup_09'), 
'10' => Lang::get('table.nationgroup_10'), 
'11' => Lang::get('table.nationgroup_11'), 
'12' => Lang::get('table.nationgroup_12'), 
'13' => Lang::get('table.nationgroup_13'), 
'14' => Lang::get('table.nationgroup_14'), 
'15' => Lang::get('table.nationgroup_15'), 
'16' => Lang::get('table.nationgroup_16'), 
'17' => Lang::get('table.nationgroup_17'), 
'18' => Lang::get('table.nationgroup_18'), 
'19' => Lang::get('table.nationgroup_19'), 
'20' => Lang::get('table.nationgroup_20'), 
'21' => Lang::get('table.nationgroup_21'), 
'22' => Lang::get('table.nationgroup_22'), 
'23' => Lang::get('table.nationgroup_23'), 
'24' => Lang::get('table.nationgroup_24'), 
'25' => Lang::get('table.nationgroup_25'), 
'26' => Lang::get('table.nationgroup_26'), 
'27' => Lang::get('table.nationgroup_27'), 
'28' => Lang::get('table.nationgroup_28'), 
'29' => Lang::get('table.nationgroup_29'), 
'30' => Lang::get('table.nationgroup_30'), 
'31' => Lang::get('table.nationgroup_31'), 
'32' => Lang::get('table.nationgroup_32'), 
'33' => Lang::get('table.nationgroup_33'), 
'34' => Lang::get('table.nationgroup_34'), 
'35' => Lang::get('table.nationgroup_35'), 
'36' => Lang::get('table.nationgroup_36'), 
'37' => Lang::get('table.nationgroup_37'), 
'38' => Lang::get('table.nationgroup_38'), 
'39' => Lang::get('table.nationgroup_39'), 
'40' => Lang::get('table.nationgroup_40'), 
'41' => Lang::get('table.nationgroup_41'), 
'42' => Lang::get('table.nationgroup_42'), 
'43' => Lang::get('table.nationgroup_43'), 
'44' => Lang::get('table.nationgroup_44'), 
'45' => Lang::get('table.nationgroup_45'), 
'46' => Lang::get('table.nationgroup_46'), 
'47' => Lang::get('table.nationgroup_47'), 
'48' => Lang::get('table.nationgroup_48'), 
'49' => Lang::get('table.nationgroup_49'), 
'50' => Lang::get('table.nationgroup_50'), 
'51' => Lang::get('table.nationgroup_51'), 
'52' => Lang::get('table.nationgroup_52'), 
'53' => Lang::get('table.nationgroup_53'), 
'54' => Lang::get('table.nationgroup_54'), 
'55' => Lang::get('table.nationgroup_55'), 
'56' => Lang::get('table.nationgroup_56'), 
'97' => Lang::get('table.nationgroup_97'), 
'98' => Lang::get('table.nationgroup_98'), 
),'',array('class' => 'form-control'))}}</div></div><!-- ./ nationgroup -->
		<!-- occupation -->
		<div
			class="form-group {{{ $errors->has('occupation') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="occupation">{{
				Lang::get('user/user.occupation') }}</label>
			<div class="col-md-10">
				{{ Form::select('occupation', array(
					'01' => Lang::get('user/user.occupation_01'), 
					'02' => Lang::get('user/user.occupation_02'), 
					'03' => Lang::get('user/user.occupation_03'), 
					'04' => Lang::get('user/user.occupation_04'), 
					'05' => Lang::get('user/user.occupation_05'), 
					'06' => Lang::get('user/user.occupation_06'), 
					'07' => Lang::get('user/user.occupation_07'), 
					'08' => Lang::get('user/user.occupation_08'), 
					'09' => Lang::get('user/user.occupation_09'), 
				),'',array('class' => 'form-control'))}}			
			</div>
		</div>
		<!-- ./ occupation -->
		<!-- maritalstatus -->
		<div
			class="form-group {{{ $errors->has('maritalstatus') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="maritalstatus">{{
				Lang::get('user/user.maritalstatus') }}</label>
			<div class="col-md-10">
				{{ Form::select('maritalstatus', array(
					'01' => Lang::get('user/user.maritalstatus_01'), 
					'02' => Lang::get('user/user.maritalstatus_02'), 
					'03' => Lang::get('user/user.maritalstatus_03'), 
				),'',array('class' => 'form-control'))}}			
			</div>
		</div>
		<!-- ./ maritalstatus -->
		<!-- hukou -->
		<div class="form-group {{{ $errors->has('hukou') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="hukou">{{
				Lang::get('user/user.hukou') }}</label>
			<div class="col-md-10">
				{{ Form::select('hukou', array(
					'01' => Lang::get('user/user.hukou_01'), 
					'02' => Lang::get('user/user.hukou_02'), 
				),'',array('class' => 'form-control'))}}				
			</div>
		</div>
		<!-- ./ hukou -->
		<!-- jiguan -->
		<div class="form-group {{{ $errors->has('jiguan') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="jiguan">{{
				Lang::get('user/user.jiguan') }}</label>
			<div class="col-md-10">
				{{ Form::text('jiguan', Input::old('jiguan'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('jiguan', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ jiguan -->
		<!-- hometown -->
		<div
			class="form-group {{{ $errors->has('hometown') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="hometown">{{
				Lang::get('user/user.hometown') }}</label>
			<div class="col-md-10">
				{{ Form::text('hometown', Input::old('hometown'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('hometown', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ hometown -->
		<!-- mobile -->
		<div class="form-group {{{ $errors->has('mobile') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="mobile"><span style="color: red">*</span>{{
				Lang::get('user/user.mobile') }}</label>
			<div class="col-md-10">
				{{ Form::text('mobile', Input::old('mobile'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ mobile -->
		<!-- phone -->
		<div class="form-group {{{ $errors->has('phone') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="phone">{{
				Lang::get('user/user.phone') }}</label>
			<div class="col-md-10">
				{{ Form::text('phone', Input::old('phone'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('phone', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ phone -->
		<!-- address -->
		<div class="form-group {{{ $errors->has('address') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="address">{{
				Lang::get('user/user.address') }}</label>
			<div class="col-md-10">
				{{ Form::text('address', Input::old('address'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('address', '<span class="help-inline">:message</span>') }}
			</div>
		</div>
		<!-- ./ address -->
		<!-- postcode -->
		<div
			class="form-group {{{ $errors->has('postcode') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="postcode">{{
				Lang::get('user/user.postcode') }}</label>
			<div class="col-md-10">
				{{ Form::text('postcode', Input::old('postcode'),array('class' => 'form-control','maxlength'=>'255')) }}
				{{ $errors->first('postcode', '<span class="help-inline">:message</span>')}}
			</div>
		</div>
		<!-- ./ postcode -->
		
		<!-- campuscode -->
		<div
			class="form-group {{{ $errors->has('campuscode') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="campuscode"><span style="color: red">*</span>{{
				Lang::get('user/user.campuscode') }}</label>
			<div class="col-md-10">
			<select id="campuscode" name="campuscode" class="form-control">
			<option value="{{{ $campus->id }}}">{{{ $campus->name }}}</option>
			</select>	
			</div>
		</div>
		<!-- ./ campuscode -->
		
		<!-- program -->
		<div class="form-group {{{ $errors->has('program') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="program"><span style="color: red">*</span>{{
				Lang::get('user/user.program') }}</label>
			<div class="col-md-10">
				{{ Form::select('program', array(
				    '0' => Lang::get('general.pleaseselect'),
					'12' => Lang::get('user/user.program_01'), 
					'14' => Lang::get('user/user.program_02'), 
					'03' => Lang::get('user/user.program_03'), 
				),'',array('class' => 'form-control', 'id' => 'program'))}}			
			</div>
		</div>
		<!-- ./ program -->
		
		<!-- programcode -->
		<div
			class="form-group {{{ $errors->has('programcode') ? 'error' : '' }}}">
			<label class="col-md-2 control-label" for="programcode"><span style="color: red">*</span>{{
				Lang::get('user/user.programcode') }}</label>
			<div class="col-md-10">
				<select id="programcode" name="programcode" class="form-control">
				</select>	
			</div>
		</div>
		<!-- ./ programcode -->
    </div>

    <!-- Disclaimer and Form Actions-->
    <div class="form-control-feedback">
        <div class="col-md-offset-2 col-md-10">
        	<p>{{ Lang::get('user/user.disclaimer_title') }}</p>
    		<p>{{ Lang::get('user/user.disclaimer_body') }}</p>
            <p>{{ Form::checkbox('agree') }}{{ Lang::get('user/user.agree') }}<br/>{{ $errors->first('agree', '<span class="help-inline">:message</span>')}}</p>
            <p><button type="submit" class="btn btn-success">{{ Lang::get('user/user.submit') }}</button></p>
        </div>
    </div>
    <!-- ./ Disclaimer and Form Actions-->
</form>
@stop
@section('scripts')
<script type="text/javascript" src="/assets/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-datepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("[name='dateofbirth']").datepicker({ language: 'zh-CN' });
		$('#campuscode').change(function(){
			if ($(this).val() == 0) {
				var program = $('#programcode');
				program.empty();
			} else {
			$.get("{{ url('admin/api/dropdown')}}",
				{ campuscode: $(this).val(),
				  programtype: $('#program').val()
				},
				function(data) {
					var program = $('#programcode');
					program.empty();
					$.each(data, function(index, element) {
						program.append("<option value='"+ element.id +"'>" + element.name + "</option>");
					});
				});
			}
		});
		$('#program').change(function(){
			$.get("{{ url('admin/api/dropdown')}}",
				{ campuscode: $('#campuscode').val(),
				  programtype: $(this).val()
				},
				function(data) {
					var program = $('#programcode');
					program.empty();
					$.each(data, function(index, element) {
						program.append("<option value='"+ element.id +"'>" + element.name + "</option>");
					});
				});
		});
	});
</script>
@stop
