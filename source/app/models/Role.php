<?php

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

    /**
     * Provide an array of strings that map to valid roles.
     * @param array $roles
     * @return stdClass
     */
    public function validateRoles( array $roles )
    {
        $user = Confide::user();
        $roleValidation = new stdClass();
        foreach( $roles as $role )
        {
            // Make sure theres a valid user, then check role.
            $roleValidation->$role = ( empty($user) ? false : $user->hasRole($role) );
        }
        return $roleValidation;
    }
    
    public function delete ()
    {
    	if (isset($this->users) && (count($this->users) > 0))
    		return false;
    	else
    		return parent::delete();
    }
}