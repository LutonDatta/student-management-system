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

class Cash_collection_on_hand extends BaseController {
    
    // Collected payments on the dashboard
    public function money_received_by_teachers_from_students_on_hand(){
        session_write_close();
        
        $data                   = $this->data;
        $data['title']          = myLang('Cash In Hand Collection','হাতে নগদ সংগ্রহ');
        $data['pageTitle']      = myLang('Cash In Hand Collection','হাতে নগদ সংগ্রহ');
        $data['loadedPage']     = 'cash_in_hand_collections';
        
        // Send to trash, SOP will delete permanently periodically
        $from_student_obj       = $this->move_to_trash_unpaid_invoices(); 
        if(is_object($from_student_obj)){ return $from_student_obj; }
        
        $from_student_obj       = $this->un_trash_unpaid_invoices(); 
        if(is_object($from_student_obj)){ return $from_student_obj; }
        
        $from_student_obj       = $this->delete_trash_invoices_after_7days(); 
        if(is_object($from_student_obj)){ return $from_student_obj; }
        
        
        $from_student_obj       = $this->process_submitted_post_request_to_get_student_id(); // Teacher is collecting money/tution fee from this student
        if(is_object($from_student_obj)){ return $from_student_obj; }
        
        $data   = array_merge( $data, $this->filteredHcInvModel()); // Show some latest collection invoices, filter if needed
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/payments/collect-student-uid-mob-em', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
    private function delete_trash_invoices_after_7days(){
        $move_to_trash_hc = intval($this->request->getPost('delete_permanently_trash_unpaid_hc_inv_submit'));
        if( $move_to_trash_hc < 1 ) return false; 
        
        // is this user trtin to delete invoice of other school?
        $obj = service('HandCashCollectionsModel')->select('hc_id,hc_deleted_at')->withDeleted()->find($move_to_trash_hc);
        if( ! is_object($obj) ){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to trash invoice. Invalid HC ID found.','danger'));
        }
        
        
        // For data safety, as we don't have authorization method, no student can be deleted immeiately, need 7 days. SOP will remove deleted rows periodically to keep database freash.
        if( (time() - (7*24*60*60)) < strtotime($obj->hc_deleted_at) ){
            $waitTimeSec = strtotime($obj->hc_deleted_at) + (7*24*60*60) - time();
            $waitTimeMin = round($waitTimeSec / 60);
            $waitTimeHour = round($waitTimeMin / 60);
            $waitTimeDay = round($waitTimeHour / 24);
            $waitString = ($waitTimeDay > 0) ? "$waitTimeDay days" : ( ($waitTimeHour > 0) ? "$waitTimeHour hours" : "$waitTimeMin minutes" );
            $msg = get_display_msg("For data safety, you can not delete data immediately. Please wait $waitString and then delete permanently.",'danger');
            $msgBn = get_display_msg("আপনার তথ্য নিরাপত্তা নিশ্চিত করার জন্য, আপনি তাৎক্ষণিক ভাবে কোন ইনভয়েস স্থায়ী ভাবে মুছে ফেলতে পারবেন না। স্থায়ী ভাবে মুছে ফেলতে অনুগ্রহ করে $waitString অপেক্ষা করুন।",'danger');
            @session_start();
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',myLang($msg,$msgBn));
        }
        
                
        $trash = service('HandCashCollectionsModel')->withDeleted()->where('hc_deleted_at < ',date('Y-m-d H:i:s',strtotime('-7 days')))->delete($move_to_trash_hc, true);
        @session_start(); // Reverse session_write_close?
        if($trash){
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection?is_paid=0"))->with('display_msg',get_display_msg('Invoice moved to trash successfully. Please delete this invoice permanently after 7 days.','success'));
        }else{
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to trash invoice.','danger'));
        }
    } // EOM
    private function move_to_trash_unpaid_invoices(){
        $move_to_trash_hc = intval($this->request->getPost('trash_unpaid_hc_inv_submit'));
        if( $move_to_trash_hc < 1 ) return false; 
        
        // is this user trtin to delete invoice of other school?
        $obj = service('HandCashCollectionsModel')->select('hc_id')->find($move_to_trash_hc);
        if( ! is_object($obj) ){
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to trash invoice. Invalid HC ID found.','danger'));
        }
                
        $trash = service('HandCashCollectionsModel')->delete($move_to_trash_hc);
        @session_start(); // Reverse session_write_close?
        if($trash){
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection?is_paid=0"))->with('display_msg',get_display_msg('Invoice moved to trash successfully. Please delete this invoice permanently after 7 days.','success'));
        }else{
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to trash invoice.','danger'));
        }
    } // EOM
    
    private function un_trash_unpaid_invoices(){
        
        $move_to_trash_hc = intval($this->request->getPost('un_trash_unpaid_hc_inv_submit'));
        if( $move_to_trash_hc < 1 ) return false; 
        
        // is this user trtin to delete invoice of other school?
        $obj = service('HandCashCollectionsModel')->select('hc_id')->withDeleted()->find($move_to_trash_hc);
        if( ! is_object($obj) ){
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to Un-trash invoice. Invalid HC ID found.','danger'));
        }
                
        $trash = service('HandCashCollectionsModel')->set(['hc_deleted_at'=>NULL])->update($move_to_trash_hc);
        @session_start(); // Reverse session_write_close?
        if($trash){
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection?is_paid=0"))->with('display_msg',get_display_msg('Invoice Un-trashed successfully.','success'));
        }else{
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Failed to Un-trash invoice.','danger'));
        }
    } // EOM
    
    
    private function process_submitted_post_request_to_get_student_id(){
        $mob_em_uid         = intval($this->request->getPost('mobile_email_uidx')); // Student UID

        if($mob_em_uid > 0){
            $mappingRow = service('StudentsModel')->select('student_u_id')->find($mob_em_uid);
            if(is_object($mappingRow)){
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/pg/cash/in/hand/collection/create/inv?student_uid=$mappingRow->student_u_id"));
            }else{
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('You have provided invalid information. Please insert  user ID number.','danger'));
            }
        }
    } /* EOM */
    
    
    
    
    private function filteredHcInvModel(){
        $student_id     = intval($this->request->getGet('student_id'));
        $rows_number    = intval($this->request->getGet('rows_number')); // number of rows to be retrieved
        $is_paid        = $this->request->getGet('is_paid'); // Value can be 0, 1 or empty string, or null
        
        // set_value do not work with _GET method, paginate() do not work with post method, so show these values 
        // to the form so that these values are available in the next page load to the form for filter capability.
        $data['fsv'] = [
            'sid'   => $student_id,
            'ism'   => $rows_number,
            'isp'   => $is_paid,
            'trs'   => $this->request->getGet('timeHcRangeStart'),
            'tre'   => $this->request->getGet('timeHcRangeEnd'),
        ];
        
        $is_paid    = in_array($is_paid, ['0','1','all']) ? $is_paid : 'all'; // Default: show all paid or unpaid invoices
        $bldr       = service('HandCashCollectionsModel')
                        ->withDeleted()
                        ->orderBy('hc_is_paid','ASC') // Allow admin to find unpaid invoices at the recent history page to mark paid easily
                        ->orderBy('hc_updated_at','DESC')
                        ->time_filter($this->request);
        
        if(in_array($is_paid, ['0','1'])) $bldr->where('hc_is_paid', $is_paid);  // if $is_paid is 'all' then it will be ignored to grabe all paid and unpaid values
        
        $showRowsNumber         = $rows_number > 40 ? 40 : ($rows_number < 5 ? 5 : $rows_number); // Maximum number or rows will be showin is 40, min 5
        $data['hc_history']     = $bldr->paginate($showRowsNumber,'hc_history'); 
        $data['hc_history_pg']  = service('HandCashCollectionsModel')->pager->links('hc_history');
        return $data;
    } /* EOM */
    
} /* EOC */
