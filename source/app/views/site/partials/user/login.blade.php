<form class="form-horizontal" method="POST" action="{{ URL::to('user/login') }}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <fieldset>
        <div class="form-group">
            <label class="col-md-2 control-label" for="email">{{ Lang::get('confide::confide.username') }}</label>
            <div class="col-md-10">
                <input class="form-control" tabindex="1" placeholder="{{ Lang::get('confide::confide.username') }}" type="text" name="email" id="email" value="{{ Input::old('email') }}" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label" for="password">
                {{ Lang::get('confide::confide.password') }}
            </label>
            <div class="col-md-10">
                <input class="form-control" tabindex="2" placeholder="{{ Lang::get('confide::confide.password') }}" type="password" name="password" id="password" maxlength="255">
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-2 control-label" for="captcha">{{{ Lang::get('user/user.captcha') }}}</label>
            <div class="col-md-10">
            <p>{{ HTML::image(Captcha::img(), 'Captcha image',array('id'=>'captcha')) }}&nbsp;<a onclick="refreshCaptcha();">刷新</a></p>
            {{ Form::text('captcha','', array('class'=>'form-control','tabindex'=>'3')) }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <div class="checkbox">
                    <label for="remember">{{ Lang::get('confide::confide.login.remember') }}
                        <input type="hidden" name="remember" value="0">
                        <input tabindex="4" type="checkbox" name="remember" id="remember" value="1">
                    </label>
                </div>
            </div>
        </div>

        @if ( Session::get('error') )
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <button tabindex="5" type="submit" class="btn btn-primary">{{ Lang::get('confide::confide.login.submit') }}</button>
            </div>
        </div>
    </fieldset>
</form>
<script>
function refreshCaptcha() {
$('#captcha').attr('src',$('#captcha').attr('src'));
}
</script>
