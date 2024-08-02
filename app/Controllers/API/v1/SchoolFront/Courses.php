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


class Courses extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Courses_Model';
    protected $format    = 'json';
    
    public function render_courses(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        // No pagination added for now. Will be updated later
        $rows       = service('CoursesModel')->select('co_title,co_code,co_id')->findAll(200,0);
        $courses    = [];
        foreach( $rows as $rw ){
            $courses[] = [
                'text'      => "$rw->co_title [$rw->co_id]" . ((strlen($rw->co_code) > 0) ? (' - ' . $rw->co_code) : ''),
                'id'        => $rw->co_id,
                'icon'      => 'glyphicon glyphicon-grain',
                'children'  => false,
            ];
        }
        return $this->respond($courses);
    }
}
