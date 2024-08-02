<?php namespace App\Controllers\API\v1\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

/**
 * Return students admission test rolls to the print view page. Get last SCM ID, currently showing in the page. 
 * and return other ids after it.
 */
class Change_roll extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Courses_classes_students_mapping_Model';
    protected $format    = 'json';
    
    public function update_roll_by_admin(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $req = [
            'new_roll'  => intval($this->request->getPost('new_roll')),
            'old_roll'  => intval($this->request->getPost('old_roll')),
            'scm_id'    => intval($this->request->getPost('scm_id')),
            'ok'        => true, // Successfully changed roll
            'error'     => '', // If failed to update roll, set value to ok=false
        ];
        
        $update = $this->model->where('scm_id',$req['scm_id'])->set('scm_c_roll',$req['new_roll'])->update($req['scm_id']);
        if( ! $update){
            $errs           = $this->model->errors();
            $req['ok']      = false;
            $req['error']   = implode(',',$errs);
        }
        return $this->respond($req);
    } /* EOM */
} /* EOC */
