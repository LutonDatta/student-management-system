<?php namespace App\Controllers\API\v1\SchoolFront;

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


class Users extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Users_Model';
    protected $format    = 'json';
    
    public function render_users(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $txt        = trim($this->request->getPost('inputTxt')); // User input might be email/name/NID/birthID/montherName/fatherName etc
        $builder    = service('UserStudentsModel')->select('student_u_id,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last,student_u_father_name,student_u_mother_name');        
        if( filter_var( $txt, FILTER_VALIDATE_EMAIL ) ){
            $builder->where('student_u_email_own', $txt );              /* User full email */
        }elseif( filter_var( $txt, FILTER_VALIDATE_INT )){
            if( strlen( $txt ) < 10 ){
                $builder->where('student_u_id', $txt);              /* It can be user ID */
            } else{
                $builder->like('student_u_nid_no', $txt)->orLike('student_u_birth_reg_no', $txt);   /* It can be NID/Birth */
            }
        }else{
            $builder->like('student_u_name_first', $txt);
            $builder->orLike('student_u_name_middle', $txt);
            $builder->orLike('student_u_name_last', $txt);
            $builder->orLike('student_u_father_name', $txt); /* Can be searched by father or mother name */
            $builder->orLike('student_u_mother_name', $txt);
        }
        $rows   = $builder->paginate(5, 'api_get_users');
        $nodes  = [];
        foreach( $rows as $usr ){
            $name = trim(get_name_initials($usr->student_u_name_initial) . ' ' . $usr->student_u_name_first . ' ' . $usr->student_u_name_middle . ' ' .$usr->student_u_name_last);
            $nodes[] = array(
                'name'  => strlen($name) > 0 ? esc($name) : 'No name set',
                'id'    => $usr->student_u_id,
                'thumb' => $usr->student_u_id, // Profile picture
                'fat'   => strlen($usr->student_u_father_name) > 0 ? esc($usr->student_u_father_name) : 'No father name found',// Send father and mother name also
                'mot'   => strlen($usr->student_u_mother_name) > 0 ? esc($usr->student_u_mother_name) : 'No mother name found',
            );
        }
        return $this->respond($nodes);
    } /* EOM */
} /* EOC */
