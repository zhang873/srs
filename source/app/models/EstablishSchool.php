<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class EstablishSchool extends Eloquent
{
    protected $table = 'course_establish_school';
    //public $timestamps = false;
    private $rules = array (
        'id' => 'required',
        'year' => 'required',
        'semester' => 'required',
        'course_id' => 'required|digits:5'

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