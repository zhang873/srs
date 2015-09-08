<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class TeachingPlanModule extends Eloquent
{
    protected $table = 'teaching_plan_module';

    private $rules = array (
        'teaching_plan_id' => 'required|digits:7',
        'module_id' => 'required',
        'credit' => 'required',
        'min_credit' => 'required'
    );
    
    private $errors;
    
    public function validate($data)
    {
        $messages = array(
            'teaching_plan_id.required' => '请输入教学计划编号',
            'teaching_plan_id.digits' => '教学计划编号为7位数字',
            'module_id.required' => '请输入模块ID',
            'credit.required' => '请输入学分',
            'min_credit.required' => '请输入模块最低学分'
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