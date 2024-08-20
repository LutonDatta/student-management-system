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


class Hostel extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Hostel_and_rooms_Model';
    protected $format    = 'json';
    
    public function render_rooms(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $parent_id = intval($this->request->getGetPost('parent_id'));
        $parent_id = $parent_id > 0 ? $parent_id : NULL; // Value should be NULL or INT greater than 0
        
        $rows = service('HostelAndRoomsModel')->select('hos_title,hos_id')->where('hos_parent', $parent_id)->findAll(25,0);
        $nodes = [];
        foreach( $rows as $rw ){
            $childrens_count= service('HostelAndRoomsModel')->where('hos_parent',$rw->hos_id)->countAllResults();
            $has_children   = ( $childrens_count > 0);
            $nodes[] = [
                'text'      => $rw->hos_title . " [{$rw->hos_id}]",
                'id'        => $rw->hos_id,
                'icon'      => 'glyphicon glyphicon-grain',
                'children'  => $has_children,
            ];
        }
        return $this->respond($nodes);
    } /* EOM */
    
} /* EOC */
