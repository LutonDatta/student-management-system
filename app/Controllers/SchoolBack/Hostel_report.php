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


class Hostel_report extends BaseController {
    
    public function occupied_seats(){
        $data                       = $this->data; // We have some pre populated data here
        $data['pageTitle']          = 'Hostel Rooms Distribution';
        $data['title']              = 'Hostel Rooms Distribution';
        $data['loadedPage']         = 'hostel_report';// Used to automatically expand submenu and add active class 
                              
        $data['hostelRooms']    = service('HostelAndRoomsModel')->get_hostel_room_with_parent_label_with_pagination(true,'hos_report');
        $data['hostelRoomsPgr'] = service('HostelAndRoomsModel')->pager->links('hos_report');
                
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/hostel/rooms-report', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
} // End class


