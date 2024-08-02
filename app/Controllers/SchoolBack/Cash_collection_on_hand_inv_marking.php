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

class Cash_collection_on_hand_inv_marking extends BaseController {
    
    
    
    public function mark_hand_cash_invoice_as_paid(){
        session_write_close();
        
        
        $data                   = $this->data;
        $data['title']          = myLang('Hand Cash Invoice mark as paid','ইনভয়েস পরিশোধিত হয়েছে মর্মে নিশ্চিত করুন');
        $data['pageTitle']      = myLang('Hand Cash Invoice mark as paid','ইনভয়েস পরিশোধিত হয়েছে মর্মে নিশ্চিত করুন');
        $data['pageTitleHideOnPrint']  = true; // We need to hide title in few pages, not on all pages.
        $data['loadedPage']     = 'cash_in_hand_collections';
        
                
        $from_submit_hc = $this->mark_inv_as_paid();
        if(is_object($from_submit_hc)){ return $from_submit_hc; }elseif(is_array($from_submit_hc)){ $data = array_merge($data, $from_submit_hc);}
        
        $from_submit_hc = $this->mark_inv_as_un_paid();
        if(is_object($from_submit_hc)){ return $from_submit_hc; }elseif(is_array($from_submit_hc)){ $data = array_merge($data, $from_submit_hc);}
        
        
        
        
        $hcRow = service('HandCashCollectionsModel')->withDeleted()->find(intval($this->request->getGetPost('hc_invoice_id')));        
        if( ! is_object($hcRow)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg(myLang('Invalid Hand Cash Invoice ID found.','ভুল ইনভয়েস নম্বর পাওয়া গেছে। অনুগ্রহ করে যাচাই করে দেখুন।'),'danger'));
        }
        
        $data['title'] .= ' - ' . esc($hcRow->hc_id); // Add INV number, helps getting different names at the time of printing

                
        /* Student is making payment for which class? Find classes to select from. */
        $associatedClass = service('CoursesClassesStudentsMappingModel')
                ->withDeleted() // Show notice if class deleted after creating invoice
                ->select('fcs_id,fcs_title,scm_id,scm_u_id,scm_session_year,scm_c_roll,scm_class_id,scm_status,scm_deleted_at')
                ->join('classes_and_semesters','classes_and_semesters.fcs_id = courses_classes_students_mapping.scm_class_id','left')
                ->find(intval($hcRow->hc_scm_id));

        $classData = service('ClassesAndSemestersModel')->withDeleted()->get_single_class_with_parent_label(intval($associatedClass->fcs_id));
        
        $data['studentRow'] = service('UserStudentsModel')->find(intval($associatedClass->scm_u_id));
        $data['hcRow']      = $hcRow;
        $data['clsData']    = (object) array_merge( (array) $associatedClass, (array) $classData );
                       
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/payments/hand-cash-invoice-marking', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
    private function mark_inv_as_paid(){
        if( $this->request->getPost('hc_marking_submit') !== 'mark_as_paid' ) return [];
        $r = service('HandCashCollectionsModel')->withDeleted()->find( intval($this->request->getPost('mark_this_invoice_id')) );
        
        if( !is_object($r)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Invalid HC ID or this HC ID is not associated with this school.','danger'));
        }
        
        if($r->hc_is_paid){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('This HC invoice is already marked as paid.','danger'));
        }
        
        
        $upd = service('HandCashCollectionsModel')->withDeleted()
                ->where('hc_id', $r->hc_id )
                ->where('hc_is_paid', '0')
                ->set(['hc_is_paid' => '1']) // Marking as paid
                ->limit(1,0)
                ->update();
        if($upd){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('This HC invoice marked as paid successfully.','success'));
        }else{
            $errors = service('HandCashCollectionsModel')->errors();
            $erStr = (is_array( $errors ) AND count($errors) > 0 ) ? ' ERRORS: ' . implode(', ', $errors) : '';
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('Failed to mark. Please try again. ' . $erStr,'danger'));
        }
    } /* EOM */
    
    private function mark_inv_as_un_paid(){
        if( $this->request->getPost('hc_marking_submit') !== 'mark_as_unpaid' ) return [];
        $r = service('HandCashCollectionsModel')->withDeleted()->find( intval($this->request->getPost('mark_this_invoice_id')) );
        
        if( !is_object($r)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection"))->with('display_msg',get_display_msg('Invalid HC ID or this HC ID is not associated with this school.','danger'));
        }
        
        if( ! $r->hc_is_paid){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('This HC invoice is UNPAID already.','danger'));
        }
        
        // By mistake, if a person mark an invoice as paid, he can mark it unpaid again in the same date. 
        // After marking an invoice paid, and if the date elapsed he can not mark it as unpaid. 18 HOURS
        if( (strtotime($r->hc_updated_at) + (18 * 60 * 60 )) < time() ){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg(myLang('You can not change payment status as 18 hours or more have been passed this invoice marked as paid.','পরিশোধিত হয়েছে কিনা তা আর পরিবর্তন করতে পারবেন না কেননা এই ইনভয়েসটি পরিশোধিত হয়েছে মর্মে নিশ্চিত করেছিলেন ১৮ ঘন্টা বা তার থেকে বেশি সময় আগে। '),'danger'));
        }
        
        
        $upd = service('HandCashCollectionsModel')->withDeleted()
                ->where('hc_id', $r->hc_id )
                // Add additional security laryer, as it might update other persons
                ->where('hc_is_paid', '1')
                ->set(['hc_is_paid' => '0']) // Marking as paid
                ->limit(1,0)
                ->update();
        if($upd){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('This HC invoice marked as UNPAID successfully.','success'));
        }else{
            $errors = service('HandCashCollectionsModel')->errors();
            $erStr = (is_array( $errors ) AND count($errors) > 0 ) ? ' ERRORS: ' . implode(', ', $errors) : '';
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$r->hc_id}"))->with('display_msg',get_display_msg('Failed to mark. Please try again. ' . $erStr,'danger'));
        }
    } /* EOM */
    
} /* EOC */
