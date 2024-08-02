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


class Daily_attendance_viewer extends BaseController {
    
    /**
     * Add/update sessions, classes, batches, morning/evening shifts etc.
     */
    public function view_attendance(){
        $data  = $this->data; // We have some pre populated data here
        
        $data['pageTitle']      = lang('Dba.dba');
        $data['title']          = lang('Dba.dba');
        $data['loadedPage']     = 'dab_view'; // Used to automatically expand submenu and add active class 
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic/daily-attendance-viewer', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
} // EOC


