<?php

class AdminUsersController extends AdminController {


    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * Role Model
     * @var Role
     */
    protected $role;

    /**
     * Permission Model
     * @var Permission
     */
    protected $permission;

    /**
     * Inject the models.
     * @param User $user
     * @param Role $role
     * @param Permission $permission
     */
    public function __construct(User $user, Role $role, Permission $permission)
    {
        parent::__construct();
        $this->user = $user;
        $this->role = $role;
        $this->permission = $permission;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/users/title.user_management');

        // Grab all the users
        $users = $this->user;

        // Show the page
        return View::make('admin/users/index', compact('users', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate()
    {
        // All roles
        $roles = $this->role->all();

        // Get all the available permissions
        $permissions = $this->permission->all();
               
        // Selected roles
        $selectedRoles = Input::old('roles', array());

        // Selected permissions
        $selectedPermissions = Input::old('permissions', array());

		// Title
		$title = Lang::get('admin/users/title.create_a_new_user');

		// Mode
		$mode = 'create';

		// Show the page
		return View::make('admin/users/create_edit', compact('roles', 'permissions', 'selectedRoles', 'selectedPermissions', 'title', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postCreate()
    {
        $this->user->username = Input::get( 'username' );
        //$this->user->email = Input::get( 'email' );
        $this->user->password = Input::get( 'password' );

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $this->user->password_confirmation = Input::get( 'password_confirmation' );

        // Generate a random confirmation code
        $this->user->confirmation_code = md5(uniqid(mt_rand(), true));

        if (Input::get('confirm')) {
            $this->user->confirmed = true;
        }

        // Permissions are currently tied to roles. Can't do this yet.
        //$user->permissions = $user->roles()->preparePermissionsForSave(Input::get( 'permissions' ));

        // Save if valid. Password field will be hashed before save
        $this->user->save();

        if ( $this->user->id ) {
            // Save roles. Handles updating.
            $this->user->saveRoles(Input::get( 'roles' ));

            // Redirect to the new user page
            return Redirect::to('admin/users/' . $this->user->id . '/edit')
                ->with('success', Lang::get('admin/users/messages.create.success'));

        } else {

            // Get validation errors (see Ardent package)
            $error = $this->user->errors()->all();

            return Redirect::to('admin/users/create')
                ->withInput(Input::except('password'))
                ->with( 'error', $error );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $user
     * @return Response
     */
    public function getShow($user)
    {
        // redirect to the frontend
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $user
     * @return Response
     */
    public function getEdit($user)
    {
        if ( $user->id )
        {
            $roles = $this->role->all();

            $permissions = $this->permission->all();
            
            // Title
        	$title = Lang::get('admin/users/title.user_update');
        	// mode
        	$mode = 'edit';

        	return View::make('admin/users/create_edit', compact('user', 'roles', 'permissions', 'title', 'mode'));
        }
        else
        {
            return Redirect::to('admin/users')->with('error', Lang::get('admin/users/messages.does_not_exist'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param User $user
     * @return Response
     */
    public function postEdit($user)
    {        
        $oldUser = clone $user;
        $user->username = Input::get( 'username' );
        $user->email = Input::get( 'email' );
        $user->confirmed = Input::get( 'confirm' );

        $password = Input::get( 'password' );
        $passwordConfirmation = Input::get( 'password_confirmation' );

        if(!empty($password)) {
            if($password === $passwordConfirmation) {
                $user->password = $password;
                // The password confirmation will be removed from model
                // before saving. This field will be used in Ardent's
                // auto validation.
                $user->password_confirmation = $passwordConfirmation;
            } else {
                // Redirect to the new user page
                return Redirect::to('admin/users/' . $user->id . '/edit')->with('error', Lang::get('admin/users/messages.password_does_not_match'));
            }
        }
        
        $rules = array (
        		'password' => 'required',
				'password' => 'min:8'
        );
        $validator = Validator::make ( Input::all (), $rules );
        	
        if ($validator->fails ())
        	return Redirect::to ( 'admin/users/' . $user->id . '/edit' )->withInput ()->withErrors ( $validator );
            
        if($user->confirmed == null) {
            $user->confirmed = $oldUser->confirmed;
        }

        if ($user->save()) {
            // Save roles. Handles updating.
            $user->saveRoles(Input::get( 'roles' ));
            
            // handle campus relationship
            if (Input::get( 'campusid' ) > 0) {
                $originalcampus = Campus::where('userID', '=', $user->id)->first();
                $admin = DB::table('users')->where('username', '=', 'admin')->first();
                if ($campus = Campus::find(Input::get( 'campusid' ))) {
                    if ($campus->userID == $admin->id) {
                        $campus->userID = $user->id;
                        $campus->save();
                        
                        //retreive original campus relationship to admin
                        $originalcampus->userID = $admin->id;
                        $originalcampus->save();
                    }
                }
            }
            
        } else {
            return Redirect::to('admin/users/' . $user->id . '/edit')
                ->with('error', Lang::get('admin/users/messages.edit.error'));
        }

        // Get validation errors (see Ardent package)
        $error = $user->errors()->all();

        if(empty($error)) {
            // Redirect to the new user page
            return Redirect::to('admin/users/' . $user->id . '/edit')->with('success', Lang::get('admin/users/messages.edit.success'));
        } else {
            return Redirect::to('admin/users/' . $user->id . '/edit')->with('error', Lang::get('admin/users/messages.edit.error'));
        }
    }

    /**
     * Remove user page.
     *
     * @param $user
     * @return Response
     */
    public function getDelete($user)
    {
        // Title
        $title = Lang::get('admin/users/title.user_delete');

        // Show the page
        return View::make('admin/users/delete', compact('user', 'title'));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param $user
     * @return Response
     */
    public function postDelete($user)
    {
        // Check if we are not trying to delete ourselves
        if ($user->id === Confide::user()->id)
        {
            // Redirect to the user management page
            return Redirect::to('admin/users')->with('error', Lang::get('admin/users/messages.delete.impossible'));
        }

        AssignedRoles::where('user_id', $user->id)->delete();

        // retreive campus relation to admin
        $admin = DB::table('users')->where('username', '=', 'admin')->first();
        if ($campus = Campus::where('userID', '=', $user->id)->first()) {
            $campus->userID = $admin->id;
            $campus->save();
        }
        
        $id = $user->id;
        $user->delete();        
        
        // Was the comment post deleted?
        $user = User::find($id);
        if ( empty($user) )
        {
            // TODO needs to delete all of that user's content
            return Redirect::to('admin/users')->with('success', Lang::get('admin/users/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the user
            return Redirect::to('admin/users')->with('error', Lang::get('admin/users/messages.delete.error'));
        }
    }

    /**
     * Show a list of all the users formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData()
    {
        
        $users = User::leftjoin('assigned_roles', 'assigned_roles.user_id', '=', 'users.id')
                    ->leftjoin('roles', 'roles.id', '=', 'assigned_roles.role_id')
                    ->where('roles.name', '<>', 'applicant')
                    ->select(array('users.id', 'users.username', 'roles.name as rolename', 'users.confirmed','users.created_at'));
        
        return Datatables::of($users)        
                            ->edit_column('rolename','@if($rolename == \'admin\')
                                                	管理员
                                            @elseif($rolename == \'staff\')
                                                	教务员
                            				@elseif($rolename == \'teacher\')
                                                	教师
                            				@else
                            						{{{ $rolename }}}
                                            @endif')
                            ->edit_column('confirmed','@if($confirmed)
                                                	是
                                            @else
                                                	否
                                            @endif')
                            ->add_column('actions', '<a href="{{{ URL::to(\'admin/users/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-default">{{{ Lang::get(\'button.edit\') }}}</a>
                                                    @if($username != \'admin\')
                                                        <a href="{{{ URL::to(\'admin/users/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger">{{{ Lang::get(\'button.delete\') }}}</a>
                                                    @endif
                                ')                    
                            ->remove_column('id')
                            ->make();
    }
}
