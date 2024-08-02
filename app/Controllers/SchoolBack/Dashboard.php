<?php namespace App\Controllers\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */


class Dashboard extends BaseController {
    
    /**
     * Show related statistics to the institution owners/teachers/students etc
     */
    public function statistics(){
        $data               = $this->data; // We have some pre populated data here
        $data['pageTitle']  = lang('Admin_menu.dashboard'). ' - SSMS';
        $data['title']      = lang('Admin_menu.dashboard'). ' - SSMS';
        $data['loadedPage'] = 'dashboard';  // Used to automatically expand submenu and add active class 
        
        /**
         | First find list of schools where this user is already joined. One user may join more then
         | one school. He can be teacher of a kindergarten and student of a high school. 
         | 
         | Find school ids where
         |      - User is admin (in a role)
         |      - Student
         |      - Applied to admit (may be not authorized)
         | In these case mentioned above, redirect user to one of the schools. Otherwise ask him if 
         | he need to create a school for him.
         */
        
        $applied_to_schools = service('CoursesClassesStudentsMappingModel') /* user applied to school */
                ->where('scm_u_id', service('AuthLibrary')->getLoggedInUserID())
                ->limit(1)
                ->orderBy('scm_updated_at','DESC')
                ->findColumn('scm_id'); // NULL or indexed array of column values
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/dashboard/statistics', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    
} // EOC
