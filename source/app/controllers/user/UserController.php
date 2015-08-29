<?php

class UserController extends BaseController {

    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * Inject the models.
     * @param User $user
     * @param UserRepository $userRepo
     */
    public function __construct(User $user, UserRepository $userRepo)
    {
        parent::__construct();
        $this->user = $user;
        $this->userRepo = $userRepo;
    }

    /**
     * Users settings page
     *
     * @return View
     */
    public function getIndex()
    {
        list($user,$redirect) = $this->user->checkAuthAndRedirect('user');
        if($redirect){return $redirect;}

        // Show the page
        return View::make('site/user/index', compact('user'));
    }

    /**
     * Stores new user
     *
     */
    public function postIndex()
    {
    	Input::flash();
    	
        $user = $this->userRepo->signup(Input::all());

        if (!$user) {
        	//captcha error
        	$error = Lang::get('user/user.captcha_error');
        	
        	return Redirect::to('user/create')
        		->withInput(Input::only('email'))
        		->with('error', $error);
        } elseif ($user->id) {
            if (Config::get('confide::signup_email')) {
                Mail::queueOn(
                    Config::get('confide::email_queue'),
                    Config::get('confide::email_account_confirmation'),
                    compact('user'),
                    function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->username)
                            ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    }
                );
            }

            return Redirect::to('user/login')
                ->with('success', Lang::get('user/user.user_account_created'));
        } else {
            $error = $user->errors()->all(':message');

            return Redirect::to('user/create')
                ->withInput(Input::except('password'))
                ->with('error', $error);
        }

    }

    /**
     * Edits a user
     * @var User
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(User $user)
    {
        $oldUser = clone $user;

        $password = Input::get('password');
        $passwordConfirmation = Input::get('password_confirmation');

        if (!empty($password)) {
            if ($password != $passwordConfirmation) {
                // Redirect to the new user page
                $error = Lang::get('admin/users/messages.password_does_not_match');
                return Redirect::to('user')
                    ->with('error', $error);
            } else {
                $user->password = $password;
                $user->password_confirmation = $passwordConfirmation;
                if ($this->userRepo->save($user)) {
                	return Redirect::to('user')
                	->with( 'success', Lang::get('user/user.user_account_updated') );
                } else {
                	$error = $user->errors()->all(':message');
                	return Redirect::to('user')
                	->withInput(Input::except('password', 'password_confirmation'))
                	->with('error', $error);
                }
            }
        } else {
        	$error = Lang::get('admin/users/messages.password_required');
        	return Redirect::to('user')
        	->withInput(Input::except('password', 'password_confirmation'))
        	->with('error', $error);
        }
    }

    /**
     * Displays the form for user creation
     *
     */
    public function getCreate()
    {
        return View::make('site/user/create');
    }


    /**
     * Displays the login form
     *
     */
    public function getLogin()
    {
        $user = Auth::user();
        if(!empty($user->id)){
        	// normal user / applicant should be logged out automatically
        	Confide::logout();
        	return Redirect::to('/');
        }
        return View::make('site/user/login');
    }

    /**
     * Attempt to do login
     *
     */
    public function postLogin()
    {
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($this->userRepo->login($input)) {
        	if (Entrust::hasRole('admin') || Entrust::hasRole('staff') || Entrust::hasRole('teacher')) // roles array seems to be useless
        	{
            	return Redirect::intended('admin');
        	} else {
        		Confide::logout();
        		return Redirect::to('/');
        	}
        } else {
            if ($this->userRepo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($this->userRepo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } elseif (!$this->userRepo->isValidCaptcha()) {
            	$err_msg = Lang::get('user/user.captcha_error');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::to('user/login')
                ->withInput(Input::only('email'))
                ->with('error', $err_msg);
        }

    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getConfirm($code)
    {
        if ( Confide::confirm( $code ) )
        {
            return Redirect::to('user/login')
                ->with( 'notice', Lang::get('confide::confide.alerts.confirmation') );
        }
        else
        {
            return Redirect::to('user/login')
                ->with( 'error', Lang::get('confide::confide.alerts.wrong_confirmation') );
        }
    }

    /**
     * Displays the forgot password form
     *
     */
    public function getForgot()
    {
        return View::make('site/user/forgot');
    }

    /**
     * Attempt to reset password with given email
     *
     */
    public function postForgotPassword()
    {
    	$rules =  array('captcha' => array('required', 'captcha'));
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$notice_msg = Lang::get('user/user.captcha_error');
    		return Redirect::to('user/forgot')
    			->with('error', $notice_msg);
    	} else if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::to('user/forgot')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::to('user/forgot')
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Shows the change password form with the given token
     *
     */
    public function getReset( $token )
    {

        return View::make('site/user/reset')
            ->with('token',$token);
    }


    /**
     * Attempt change password of the user
     *
     */
    public function postReset()
    {

        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($this->userRepo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::to('user/login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::to('user/reset/'.$input['token'])
                ->withInput()
                ->with('error', $error_msg);
        }

    }

    /**
     * Log the user out of the application.
     *
     */
    public function getLogout()
    {
        Confide::logout();

        return Redirect::to('/');
    }

    /**
     * Get user's profile
     * @param $username
     * @return mixed
     */
    public function getProfile($username)
    {
        $userModel = new User;
        $user = $userModel->getUserByUsername($username);

        // Check if the user exists
        if (is_null($user))
        {
            return App::abort(404);
        }

        return View::make('site/user/profile', compact('user'));
    }

    public function getMain()
    {
        list($user,$redirect) = User::checkAuthAndRedirect('user/main');
        if($redirect){return $redirect;}

        $isSubmitted = Admission::where('user_id','=',$user->id)->first();
        return View::make('site/user/profile', compact('isSubmitted'));
    }

    /**
     * Process a dumb redirect.
     * @param $url1
     * @param $url2
     * @param $url3
     * @return string
     */
    public function processRedirect($url1,$url2,$url3)
    {
        $redirect = '';
        if( ! empty( $url1 ) )
        {
            $redirect = $url1;
            $redirect .= (empty($url2)? '' : '/' . $url2);
            $redirect .= (empty($url3)? '' : '/' . $url3);
        }
        return $redirect;
    }
    
    /**
     * Displays the admission form
     *
     */
    public function getAdmission()
    {
    	$user = Auth::user();
    	if(!empty($user->id)){
    		//check if user has submitted admission form already, if yes, redirect back to the form
    		$isSubmitted = Admission::where('user_id','=',$user->id)->first();
    		if ($isSubmitted) {
    			return Redirect::to('/user/main')
    			->withInput(Input::except('password'))
    			->withErrors(Lang::get('user/user.admission_confirmation_body',array('id'=>$isSubmitted->id)));
    		}
    		return View::make('site/user/admission', compact('user'));
    	}

    	return View::make('site/user/login');
    }
    
    /**
     * Stores new admission
     *
     */
    public function postAdmission($user)
    {
    	//check if user has submitted admission form already, if yes, redirect back to the form
    	$isSubmitted = Admission::where('user_id','=',$user->id)->first();
    	if ($isSubmitted) {
	    	return Redirect::to('/user/main')
	    	->withInput(Input::except('password'))
	    	->withErrors(Lang::get('user/user.admission_confirmation_body',array('id'=>$isSubmitted->id)));
    	}
    	
    	//check if the inputs are valid, if yes, save and email the no. , or else redirect back to the form
    	$rules = array (
    			'fullname' => 'required',
    			'idnumber' => 'required',
    			'dateofbirth' => 'required|date|before:"now"',
    			'nationgroup' => 'required',
    			'mobile' => 'required',
    			'agree' => 'required',
    			'postcode' => 'numeric'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	
    	if ($validator->fails()) {
    		return Redirect::to('/user/admission')
    		->withInput(Input::except('password'))
    		->withErrors($validator);
    	} else {
    		// store
    		$admission = new Admission;
    		$admission->user_id = $user->id;
			$admission->fullname = Input::get('fullname');
			$admission->gender = Input::get('gender');
			$admission->politicalstatus = Input::get('politicalstatus');
			$admission->idtype = Input::get('idtype');
			$admission->idnumber = Input::get('idnumber');
			$admission->dateofbirth = Input::get('dateofbirth');
			$admission->nationgroup = Input::get('nationgroup');
			$admission->occupation = Input::get('occupation');
			$admission->maritalstatus = Input::get('maritalstatus');
			$admission->hukou = Input::get('hukou');
			$admission->jiguan = Input::get('jiguan');
			$admission->hometown = Input::get('hometown');
			$admission->mobile = Input::get('mobile');
			$admission->phone = Input::get('phone');
			$admission->address = Input::get('address');
			$admission->postcode = Input::get('postcode');
			$admission->program = Input::get('program');
			$admission->programcode = Input::get('programcode');
			$admission->campuscode = Input::get('campuscode');
    		$admission->save();
    		
    		//generate admissionid
    		$campussysid = DB::table('campuses')
    		                      ->select('sysid')
    		                      ->where('id', $admission->campuscode)
    		                      ->first();
    		if (!is_null($campussysid)) {
    		    $admission->admissionid = strftime('%Y%m%d').Config::get('customsettings.semester')
    		                              .$campussysid->sysid.str_pad($admission->id, 5, "0", STR_PAD_LEFT);
    		} else {
    		    //should not happen
    		    $admission->admissionid = strftime('%Y%m%d').Config::get('customsettings.semester')
    		                              .str_pad($admission->id, 5, "0", STR_PAD_LEFT);
    		}
    		$admission->save();
    		
    		Mail::queueOn(
	    		'admission_email_queue',
	    		'emails.admission.confirmation',
	    		compact('admission'),
	    		function ($message) use ($user,$admission) {
	    			$message
	    			->to($user->email, $admission->username)
	    			->subject(Lang::get('user/user.admission_confirmation_subject'));
	    		}
    		);
    	
    		// redirect
    		return View::make('site/user/admissionformsubmitted', compact('admission'));
    	}
    }
    
    public function missingMethod($parameters = array())
    {
    	var_dump($parameters);
    }
}
