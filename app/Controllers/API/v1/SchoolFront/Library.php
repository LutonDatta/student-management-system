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
class Library extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Courses_Model';
    protected $format    = 'json';
    
    protected $helpers = ['text'];

    public function view_public_book_data(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $r2 = array( /* JSON response */
            'has_error'     => true,
            'errors'        => [],
            'results' => array( /* ['id' => 1, 'text' => 'No user found' */ ),
            'pagination' => array( 'total' => 0, 'more' => false, /* No next page */ ),
        );
        
        $model = service('LibraryItemsModel');
        
        $txt            = trim($this->request->getPost('inputTxt')); // User input might be email/name/NID/birthID etc
        $pageNumReq     = intval($this->request->getPost('pageNumber'));    // Which page we are viewing?
        $pageNumber     = $pageNumReq > 0 ? $pageNumReq : 1;                // Page number can not be 0
        $itemsPerPage   = 5; // Per page items
        $offset         = ($pageNumber * $itemsPerPage) - $itemsPerPage;
        
        $bld = $model->select("bk_id,bk_title,bk_code");
        if( filter_var( $txt, FILTER_VALIDATE_INT )){
            $bld->where('bk_id', $txt); /* It can be user ID */
        }else{
            // Without groupStart previous where clouse will not work
            $bld->groupStart();
                $bld->like('bk_title', $txt); /* name */
                $bld->orLike('bk_code', $txt);
                $bld->orLike('bk_excerpt', $txt);
            $bld->groupEnd();
        }
        
        $total = $bld->countAllResults(false); /* Count before retrieving result */
        if( $total > $itemsPerPage ){
            $r2['pagination']['more'] = true;
            $r2['pagination']['total'] = $total;
        }
        
        $rows = $bld->limit($itemsPerPage, $offset)->get()->getResult();
        foreach( $rows as $usr ){
            
            $r2['results'][] = array(
                'id' => $usr->bk_id,
                'text' => esc($usr->bk_title) . ' (' . $usr->bk_code . ')['.$usr->bk_id.']'
            );
        }
        return $this->respond($r2);
    } /* EOM */
    
    public function view_public_book_quantity(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $r2 = array( /* JSON response */
            'has_error'     => true,
            'errors'        => [],
            'results' => array( /* ['id' => 1, 'text' => 'No user found' */ ),
            'pagination' => array( 'total' => 0, 'more' => false, /* No next page */ ),
        );
        $db = \Config\Database::connect();
        
        
        $txt = trim($this->request->getPost('inputTxt')); // User input might be email/name/NID/birthID etc
        $pageNumReq     = intval($this->request->getPost('pageNumber'));    // Which page we are viewing?
        $pageNumber     = $pageNumReq > 0 ? $pageNumReq : 1;                // Page number can not be 0
        $itemsPerPage   = 5; // Per page items
        $offset         = ($pageNumber * $itemsPerPage) - $itemsPerPage;
        
        $q = 'library_items_quantities';$b = 'library_items';
        $bld = service('LibraryItemsQuantitiesModel')
                ->join($b,"$b.bk_id = $q.lq_bk_id")
                ->select("$q.lq_id,$q.lq_serial_number,$b.bk_code,$b.bk_title");
        $bld->where('lq_serial_number', $txt); /* Version Serial Number */
        $bld->where('lq_bk_id', intval($this->request->getPost('inputItemID'))); /* Book/ Item ID */
        
        $total = $bld->countAllResults(false); /* Count before retrieving result */
        if( $total > $itemsPerPage ){
            $r2['pagination']['more'] = true;
            $r2['pagination']['total'] = $total;
        }
        
        $rows = $bld->limit($itemsPerPage, $offset)->get()->getResult();
        foreach( $rows as $usr ){
            
            $r2['results'][] = array(
                'id' => $usr->lq_id,
                'text' => $usr->bk_code .'-' .$usr->lq_serial_number . '['.esc($usr->bk_title).']'
            );
        }
        return $this->respond($r2);
    } /* EOM */
}/* EOC */
