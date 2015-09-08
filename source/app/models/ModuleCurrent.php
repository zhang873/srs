<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ModuleCurrent extends Eloquent
{
    protected $table = 'module_current';
    //public $timestamps = false;
    protected $primaryKey = 'module_id';
    private $rules = array (
        'module_id' => 'required',
        'current_year' => 'required',
        'current_semester' => 'required'
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