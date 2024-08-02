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


class Academic extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Classes_and_semesters_Model';
    protected $format    = 'json';
    
    public function render_classes(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $parent_id = intval($this->request->getGetPost('parent_id'));
        $parent_id = $parent_id > 0 ? $parent_id : NULL; // Value should be NULL or INT greater than 0
        
        $rows = service('ClassesAndSemestersModel')
                ->select('fcs_title,fcs_id')
                ->where('fcs_parent', $parent_id)
                ->findAll(25,0);
        $nodes = [];
        foreach( $rows as $rw ){
            $childrens_count= service('ClassesAndSemestersModel')
                                ->where('fcs_parent',$rw->fcs_id)
                                ->countAllResults();
            $has_children   = ( $childrens_count > 0);
            $nodes[] = [
                'text'      => $rw->fcs_title . " [{$rw->fcs_id}]",
                'id'        => $rw->fcs_id,
                'icon'      => 'glyphicon glyphicon-grain',
                'children'  => $has_children,
            ];
        }
        return $this->respond($nodes);
    }
}
