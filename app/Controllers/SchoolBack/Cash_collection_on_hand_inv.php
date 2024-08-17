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

class Cash_collection_on_hand_inv extends BaseController {
    
    // Collected payments on the dashboard
    public function create_n_edit_cash_invoice(){
        session_write_close();
        
        $data                   = $this->data;
        $data['title']          = myLang('Cash In Hand Collection','হাতে নগদ সংগ্রহ');
        $data['pageTitle']      = myLang('Cash In Hand Collection','হাতে নগদ সংগ্রহ') . anchor('admin/admission/student/list','<i class="fas fa-user-graduate"></i> ' . myLang('Select another student','অন্য শিক্ষার্থী নির্বাচন করুন'),['class'=>'ml-1 btn btn-info btn-sm float-right']);
        $data['loadedPage']     = 'cash_in_hand_collections';
        
        
        
        $studentRow = service('StudentsModel')
                ->select('student_u_id,student_u_mobile_own,student_u_mobile_father,student_u_mobile_mother,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last,student_u_father_name,student_u_mother_name,student_u_addr_country,student_u_addr_state,student_u_addr_district,student_u_addr_thana,student_u_addr_post_office,student_u_addr_zip_code,student_u_addr_village,student_u_addr_road_house_no')
                ->find(intval($this->request->getGet('student_uid')));
        if( ! is_object($studentRow)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Invalid student ID. Please insert correct student ID.','danger'));
        }
        
        /* Student is making payment for which class? Find classes to select from. */
        $userCLasses = service('CoursesClassesStudentsMappingModel')
                ->withDeleted()
                ->where('scm_u_id', intval($this->request->getGet('student_uid')))
                ->select('fcs_id,fcs_title,scm_id,scm_u_id,scm_session_year,scm_c_roll,scm_class_id,scm_status')
                ->join('classes_and_semesters','classes_and_semesters.fcs_id = courses_classes_students_mapping.scm_class_id','left')
                ->paginate(25,'student_class_list');
        
        $processedUserCLasses = [];
        foreach($userCLasses as $scmRow){
            $clsObj = service('ClassesAndSemestersModel')->withDeleted()->get_single_class_with_parent_label(intval($scmRow->fcs_id));
            $cls = is_object($clsObj) ? $clsObj->title : 'No class';
            $processedUserCLasses[ $scmRow->scm_id ] = "$cls [$scmRow->fcs_id] - Session: $scmRow->scm_session_year, Roll: $scmRow->scm_c_roll, SCM ID: $scmRow->scm_id, SCM UID: $scmRow->scm_u_id - " . (isset(get_student_class_status()[$scmRow->scm_status]) ? get_student_class_status()[$scmRow->scm_status]: '');
        }
        $data['userCLasses']    = $processedUserCLasses;
        $data['studentRow']     = $studentRow;
        
        
        /* CAUTON: Never place these processing at the top of this function. We need verified UID, SCM ids.*/
        $from_submit_hc = $this->create_or_update_hand_cash_invoice($studentRow->student_u_id,$processedUserCLasses);
        if(is_object($from_submit_hc)){ return $from_submit_hc; }elseif(is_array($from_submit_hc)){ $data = array_merge($data, $from_submit_hc);}
        
        // Paid invoice or invoice of other school can not be updated
        $data['hcUpdateOldInvoice']     = service('HandCashCollectionsModel')->withDeleted()->where('hc_is_paid','0')->find(intval($this->request->getGet('update_hc_id'))); 
        $_SERVER['usSG_studentRow'] = $studentRow;
        $data['hc_student_history']     = service('HandCashCollectionsModel')
                ->withDeleted()
                ->select('hc_id,hc_amt_total,hc_is_paid,hc_updated_at,scm_u_id')
                ->join('courses_classes_students_mapping','courses_classes_students_mapping.scm_id = hc_scm_id','LEFT')
                ->whereIn('hc_scm_id',function($d){
                    return $d->select("courses_classes_students_mapping.scm_id")->where("courses_classes_students_mapping.scm_u_id",$_SERVER['usSG_studentRow']->student_u_id)->from('courses_classes_students_mapping');
                })
                ->orderBy('hc_is_paid','ASC') // Show unpaid invoices at the top to edit
                ->orderBy('hc_updated_at','DESC')
                ->paginate(8,'hc_student_history_pgr'); // Show invoice of this student
        $data['hc_student_history_pg']  = service('HandCashCollectionsModel')->pager->links('hc_student_history_pgr');
                       
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/payments/hand-cash-invoice-editor', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
    private function create_or_update_hand_cash_invoice( int $student_id, array $scm_ids  ){
        if($this->request->getPost('hc_submit') !== 'yes'){ return []; }; // First time page load. Form not submitted.
        
        $amt_fields     = ['hc_amt_salary','hc_amt_electricity_fee','hc_amt_ict_fee','hc_amt_welcome_fee','hc_amt_farewell_fee','hc_amt_girls_guides_fee','hc_amt_printing_fee','hc_amt_sports_fee','hc_amt_lab_fee','hc_amt_teacher_welfare_fee','hc_amt_milad_puja_fee','hc_amt_development_fee','hc_amt_poverty_fund_fee','hc_amt_reading_room_fee','hc_amt_cultural_program','hc_amt_garden_fee','hc_amt_common_room_fee','hc_amt_session_fee','hc_amt_id_fee'];
        $save_hc_data   = [];

        $total_amount_int = 0;
        foreach( $amt_fields as $table_col ){
            $amount_int = intval($this->request->getPost( 'frm_' . $table_col));
            $save_hc_data[$table_col] = ( $amount_int < 1 ) ? '' : $amount_int; // Place empty string to permit empty value in db
            $total_amount_int = $total_amount_int + $amount_int;
        }
        
        $save_hc_data['hc_salary_months_txt']   = $this->request->getPost( 'frm_hc_salary_months_txt'); 
        $save_hc_data['sr_is_paid']             = '0'; // Will be marked as paid later
        $save_hc_data['hc_amt_total']           = $total_amount_int;
        
        $scm_id_from_form = intval($this->request->getPost('hc_class_scm_id'));
        if(in_array($scm_id_from_form, array_keys( $scm_ids ))){
            $save_hc_data['hc_scm_id'] = $scm_id_from_form;
        }else{
            return ['display_msg' => get_display_msg('Imvalid SCM ID found.','danger')];
        }
        
        // Are we trying ro updating previous row? Confirm if we have the right permission to update this row
        $updateRowHC = intval($this->request->getPost('updating_hc_row'));
        $updateRowHCobj = service('HandCashCollectionsModel')->withDeleted()->where('hc_is_paid','0')->find($updateRowHC); 
        if($updateRowHC > 0 AND ! is_object($updateRowHCobj)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/create/inv?student_uid={$student_id}&update_hc_id={$updateRowHC}"))->with('display_msg',get_display_msg('You have tried to update an invoice, but you have no right to update this invoice. Invoice might be changed.','danger'));
        }
        
        
        if(is_object($updateRowHCobj)){
            $upd = service('HandCashCollectionsModel')->withDeleted()
                ->where('hc_id', $updateRowHCobj->hc_id )
                // Add additional security laryer, as it might update other persons
                ->where('hc_is_paid', '0') // You can not change information of paid invoices
                ->set($save_hc_data) // Marking as paid
                ->limit(1,0)
                ->update();
            if($upd){
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$updateRowHCobj->hc_id}"))->with('display_msg',get_display_msg('This HC invoice updated successfully.','success'));
            }else{
                $errors = service('HandCashCollectionsModel')->errors();
                $erStr = (is_array( $errors ) AND count($errors) > 0 ) ? ' ERRORS: ' . implode(', ', $errors) : '';
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/pg/cash/in/hand/collection/create/inv?student_uid={$student_id}&update_hc_id={$updateRowHCobj->hc_id}"))->with('display_msg',get_display_msg('Failed to update invoice. Please try again. It might be true that this invoice has been changed by other person.' . $erStr,'danger'));
            }
        }else{
            $insert = service('HandCashCollectionsModel')->insert($save_hc_data);
            if($insert){
                $insertID    = service('HandCashCollectionsModel')->insertID();
                $display_msg = get_display_msg(myLang('New invoice has been created Successfully. New Invoice ID:','ইনভয়েস তৈরি করা হয়েছে। যার নতুন আইডি নম্বর হল: : ') . esc($insertID),'success');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$insertID}"))->with('display_msg',$display_msg);
            }else{
                $insertErrors = service('HandCashCollectionsModel')->errors();

                $errMsg = (is_array($insertErrors) AND count($insertErrors) > 0) ? implode(', ', $insertErrors) : 'Unknow error';
                @session_start(); // Reverse session_write_close?
                return redirect()->to( (string)current_url(true) )->with('display_msg',get_display_msg($errMsg,'danger'));
            }
        }
        
    } /* EOM */
    
    
} /* EOC */
