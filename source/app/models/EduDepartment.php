<?php
/**
 *
 * @package
 * @copyright
 * @author    Max Kan <max_kan@hotmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class EduDepartment extends Eloquent
{
    protected $table = 'department_info';

    private $rules = array (
        'name' => 'required',
        'code' => 'required|digits:5'
    );
    
    private $errors;
    
    public function validate($data)
    {
        $messages = array(
            'name.required' => '请输入部门名称',
            'code.required' => '请输入部门代码',
            'code.digits' => '部门代码为5位数字'
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