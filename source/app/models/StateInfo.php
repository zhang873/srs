<?php

class StateInfo extends Eloquent
{
    protected $table = 'state_info';

    private $rules = array (
        'id' => 'required',
        'campus_id' => 'required'
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