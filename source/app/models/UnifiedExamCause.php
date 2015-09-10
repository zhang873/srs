<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class UnifiedCause extends Eloquent
{
    protected $table = 'unified_exam_cause';

    private $rules = array (
        'cause' => 'required',
        'code' => 'required|numeric:5',
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