<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ExemptionInfo extends Eloquent
{
    protected $table = 'exemption_info';

    private $rules = array (
        'major_name_outer' => 'required',
        'course_name_outer' => 'required',
        'credit_outer' => 'required',
        'classification_outer' => 'required|Integer:1',
        'score' => 'required',
        'exemption_type_id' => 'required',
        'certification_year' => 'required',
        'agency_id' => 'required',
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