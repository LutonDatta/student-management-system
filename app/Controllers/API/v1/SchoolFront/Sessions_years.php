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


class Sessions_years extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Courses_Model';
    protected $format    = 'json';
    
    public function render_sessions_years(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.

        // No pagination added for now. Will be updated later
        // Currently useing in backend attendance viewer. but might be available in front publicly
        $YrsSessView = service('CoursesClassesStudentsMappingModel')
                    ->distinct()->limit(100,0)
                    ->findColumn('scm_session_year'); // Null or indexed array of values
            
        
        $rows    = [];
        if($YrsSessView) foreach( $YrsSessView as $rw ){
            $rows[] = [
                'text'      => $rw,
                'id'        => $rw,
                'icon'      => 'glyphicon glyphicon-grain',
                'children'  => false,
            ];
        }
        return $this->respond($rows);
    } /* EOC */
} /* EOC */
