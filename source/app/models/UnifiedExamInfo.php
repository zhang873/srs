<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class UnifiedExamInfo extends Eloquent
{
    protected $table = 'unified_exam_info';
    protected $primarykey = 'unified_exam_id';
    private $rules = array (
        'student_id' => 'required|int',
        'registration_semester' => 'required|int',
        'registration_year' => 'required|numeric:4',
        'unified_exam_course_id' => 'required|Integer:1',
        'unified_exam_type_id' => 'required|int',
        'failure_cause' => 'required',
        'final_result' => 'required'
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