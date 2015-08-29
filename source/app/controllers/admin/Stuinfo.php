<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Stuinfo extends Eloquent
{
    protected $table = 'stuinfo';
    public function Selection()
    {
        return $this->hasMany("App\Selection");
    }
    private $rules = array (
        'student_id' => 'required|num',
        'student_name' => 'required|alpha',
        'major' => 'required',
        'campus' => 'required',
        'classification ' => 'required'
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