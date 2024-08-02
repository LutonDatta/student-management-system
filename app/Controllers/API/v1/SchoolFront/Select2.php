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


/**
 * Show data required by select2. We might need to render many data to select2.
 * Some cases we might need to check for LOGIN to render SECURE data but in
 * some cases we may render data to unlogged/annonimous users.
 */
class Select2 extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Courses_Model';
    protected $format    = 'json';
    protected $helpers = ['text'];

    /**
     * Login not required.
     * Render user ID, Profile Picture and Identifiable full name of the user.
     * Called from : payment page to pay salary or other payments.
     */
    public function view_public_user_data(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $r2 = array( /* JSON response */
            'has_error'     => true,
            'errors'        => [],
            'results' => array( /* ['id' => 1, 'name' => 'No user found', 'url' => '#'] */ ),
            'pagination' => array( 'total' => 0, 'more' => false, /* No next page */ ),
        );
        $db = \Config\Database::connect();
        
        $txt = trim($this->request->getPost('inputTxt')); // User input might be email/name/NID/birthID etc
        $pageNumReq     = intval($this->request->getPost('pageNumber'));    // Which page we are viewing?
        $pageNumber     = $pageNumReq > 0 ? $pageNumReq : 1;                // Page number can not be 0
        $itemsPerPage   = 5; // Per page items
        $offset         = ($pageNumber * $itemsPerPage) - $itemsPerPage;
        
        $u = 'users'; 
        $m = 'users_meta';
        
        $bld = $db
                ->table($u)
                ->select("student_u_id,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last,student_u_father_name,student_u_mother_name")
                ->join('users_u_mobile',"users_u_mobile.umob_u_id = $u.student_u_id", 'LEFT')
                ->join('users_u_email',"users_u_email.uem_u_id = $u.student_u_id", 'LEFT');
        
        $atPosition = strpos( $txt, '@'); // @ posision can not be 0, shoud be more then 2
        $dotPosision = strpos($txt,'.'); // posision of . should be more then $atposision because it is placed after @
        if( $atPosition > 0 ){
            if( $dotPosision > $atPosition ) $bld->where('u_email', $txt ); // Full email (search exact email) - find single row
            else $bld->like('u_email', $txt);   // NOt complete email (search likely emails) - many rows
        }elseif( filter_var( $txt, FILTER_VALIDATE_INT )){
            if( strlen( $txt ) < 10 ){
                $bld->where('student_u_id', $txt); /* It can be user ID */
            } else{
                $bld->like('student_u_nid_no', $txt); /* It can be NID/Birth */
                $bld->orLike('student_u_birth_reg_no', $txt); /* It can be NID/Birth */
            }
        }else{
            $bld->like('student_u_name_initial', $txt); /* name */
            $bld->orLike('student_u_name_first', $txt);
            $bld->orLike('student_u_name_middle', $txt);
            $bld->orLike('student_u_name_last', $txt);
            $bld->orLike('student_u_father_name', $txt); /* Can be searched by father or mother name */
            $bld->orLike('student_u_mother_name', $txt);
        }
        
        $total = $bld->countAllResults(false); /* Count before retrieving result */
        if( $total > $itemsPerPage ){
            $r2['pagination']['more'] = true;
            $r2['pagination']['total'] = $total;
        }
        
        $rows = $bld->limit($itemsPerPage, $offset)->get()->getResult();
        foreach( $rows as $usr ){
            $nm = trim(get_name_initials($usr->student_u_name_initial) . ' ' . $usr->student_u_name_first . ' ' . $usr->student_u_name_middle . ' ' .$usr->student_u_name_last);
            
            $pic = object_public_url_free($db, get_school_user_meta( $db, $usr->student_u_id, 'profile_pic'));
            $r2['results'][] = array(
                'name' => strlen($nm) > 0 ? esc($nm) : 'User has not set his name',
                'id' => $usr->student_u_id,
                'url' => $pic, // Profile picture
                'fat' => strlen($usr->student_u_father_name) > 0 ? esc($usr->student_u_father_name) : 'User has not set his father name',// Send father and mother name also
                'mot' => strlen($usr->student_u_mother_name) > 0 ? esc($usr->student_u_mother_name) : 'User has not set his mother name',
            );
        }
        return $this->respond($r2);
    } /* EOC */
} /* EOC */
