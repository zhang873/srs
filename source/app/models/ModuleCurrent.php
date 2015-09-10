<?php

class ModuleCurrent extends Eloquent
{
    protected $table = 'module_current';
    protected $primaryKey = 'module_id';
    private $rules = array (
        'current_year' => 'require',
        'current_semester' => 'required',
    );

    private $errors;

    public function validate($data)
    {
        // make a new validator object
        $v = Validator::make($data, $this->rules);

        // check for failure
        if ($v->fails())
        {
            // set errors and return false
            $this->errors = $v->errors();
            return false;
        }

        // validation pass
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}