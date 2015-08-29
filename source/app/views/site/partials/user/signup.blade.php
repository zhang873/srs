<form method="POST" action="{{{ URL::to('user') }}}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
    <fieldset>
        <div class="form-group">
            <label for="email">{{{ Lang::get('confide::confide.e_mail') }}} <small>{{ Lang::get('confide::confide.signup.confirmation_required') }}</small></label>
            <input class="form-control" placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}" maxlength="100">
        </div>
        <div class="form-group">
            <label for="password">{{{ Lang::get('confide::confide.password') }}}</label>
            <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password" maxlength="255">
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{{ Lang::get('confide::confide.password_confirmation') }}}</label>
            <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password_confirmation') }}}" type="password" name="password_confirmation" id="password_confirmation" maxlength="255">
        </div>
        
        <div class="form-group">
            <label for="captcha">{{{ Lang::get('user/user.captcha') }}}</label>
            <p>{{ HTML::image(Captcha::img(), 'Captcha image',array('id'=>'captcha')) }}&nbsp;<a onclick="refreshCaptcha();">刷新</a></p>
            <p>{{ Form::text('captcha','', array('class'=>'form-control')) }}</p>
        </div>

        <div class="form-actions form-group">
          <button type="submit" class="btn btn-primary">{{{ Lang::get('confide::confide.signup.submit') }}}</button>
        </div>

    </fieldset>
</form>
<script>
function refreshCaptcha() {
$('#captcha').attr('src',$('#captcha').attr('src'));
}
</script>