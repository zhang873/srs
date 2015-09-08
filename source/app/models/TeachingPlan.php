<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class TeachingPlan extends Eloquent
{
    protected $table = 'teaching_plan';

    private $rules = array (
        'code' => 'required|digits:7',
        'min_credit_graduation' => 'required',
        'max_credit_exemption' => 'required',
        'max_credit_semester' => 'required'
    );
    
    private $errors;
    
    public function validate($data)
    {
        $messages = array(
            'code.required' => '请输入教学计划编号',
            'code.digits' => '教学计划编号为7位数字',
            'min_credit_graduation.required' => '请输入毕业最低学分',
            'max_credit_exemption.required' => '请输入免修免考最高学分',
            'max_credit_semester.required' => '请输入新选课最高学分'
        );
        // make a new validator object
        $v = Validator::make($data, $this->rules, $messages);
        
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