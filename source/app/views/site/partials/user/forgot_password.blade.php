<form method="POST" action="{{ URL::to('user/forgot-password') }}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">

    <div class="form-group">
        <label for="email">{{{ Lang::get('confide::confide.e_mail') }}}</label>
        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
    </div>
    
    <div class="form-group">
		<label for="captcha">{{{ Lang::get('user/user.captcha') }}}</label>
		<p>{{ HTML::image(Captcha::img(), 'Captcha image',array('id'=>'captcha')) }}&nbsp;<a onclick="refreshCaptcha();">刷新</a></p>
        <p>{{ Form::text('captcha','', array('class'=>'form-control')) }}</p>
    </div>
    
    <div class="form-group">
    	<input class="btn btn-default" type="submit" value="{{{ Lang::get('confide::confide.forgot.submit') }}}">
    </div>

    @if (Session::get('error'))
        <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
    @endif

    @if (Session::get('notice'))
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif
</form>
<script>
function refreshCaptcha() {
$('#captcha').attr('src',$('#captcha').attr('src'));
}
</script>