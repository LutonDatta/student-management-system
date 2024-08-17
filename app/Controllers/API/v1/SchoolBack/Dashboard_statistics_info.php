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

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

/**
 * Filter will be applied from rooter. Currently we do not have any security restriction.
 */
class Dashboard_statistics_info extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Users_Model';
    protected $format    = 'json';

    
    
    public function total_books_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count = service('LibraryItemsModel')->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
    
    public function total_books_quantity_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count = service('LibraryItemsQuantitiesModel')
                ->join('library_items','library_items.bk_id = lq_bk_id','LEFT')
                ->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
    
    public function total_classes_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $t      = service('ClassesAndSemestersModel')->getTableName(true);
        $sql    = "SELECT COUNT( $t.fcs_id ) AS CTX FROM $t WHERE {$t}.fcs_id NOT IN ( SELECT {$t}.fcs_parent FROM $t WHERE {$t}.fcs_parent IS NOT NULL )";       
        $num    = service('ClassesAndSemestersModel')->query($sql)->getRow();
        
        return $this->respond(['count' => number_format($num->CTX)]);
    } // EOM
    
    public function total_courses_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count = service('CoursesModel')->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
    
    public function total_total_student_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count =  service('StudentsModel')->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
    
    public function total_invoice_paid_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count = service('HandCashCollectionsModel')->where('hc_is_paid','1')->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
   
    public function total_invoice_un_paid_count(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        $count = service('HandCashCollectionsModel')->where('hc_is_paid','0')->countAllResults();
        return $this->respond(['count' => number_format($count)]);
    } // EOM
   
    
} // EOC
