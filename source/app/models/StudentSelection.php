<?php

class StudentSelection extends Eloquent
{
    protected $table = 'student_selection';

    private $rules = array (
        'student_id' => 'required',
        'course_id' => 'required'
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