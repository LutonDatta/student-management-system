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


class Hostel extends BaseController {
    
    /**
     * Add/update sessions, classes, batches, morning/evening shifts etc.
     */
    public function rooms_setup(){
        $data                       = $this->data; // We have some pre populated data here
        $data['pageTitle']          = 'Hostel Rooms Setup';
        $data['title']              = 'Hostel Rooms Setup';
        $data['loadedPage']         = 'hostel_buildings_n_rooms_editor';// Used to automatically expand submenu and add active class 
        $data['allItems']           = service('HostelAndRoomsModel')->withDeleted()->get_hostel_room_with_parent_label_with_pagination(false,'all_cls_semesters');
        $data['allItemsPager']      = service('HostelAndRoomsModel')->pager->links('all_cls_semesters');
        $data['parentItemsLabel']   = service('HostelAndRoomsModel')->get_hostel_room_with_parent_label_for_dropdown( false, 'all_cls_drpdwn', 25, false );
        $data['parentItemsLabPg']   = service('HostelAndRoomsModel')->pager->links('all_cls_drpdwn');
        
        
        $deleteRow = $this->delete_requested_class_faculties();
        if(is_object($deleteRow)){ return $deleteRow; }else{ $data = array_merge($data, $deleteRow ); }
        
        $updateId                   = intval($this->request->getGet('edit_id'));
        
        $saveForm = $this->save_form($updateId);
        if(is_object($saveForm)){ return $saveForm; }else{ $data = array_merge($data, $saveForm ); }
        
        $updateId                   = intval(isset($data['insertID']) ? $data['insertID'] : $updateId);
        
        $data['formTitle']          = $updateId < 1 ? 'Add New Room, Floor or Building' : 'You are updating';
        $data['submit_form_to']     = base_url('admin/hostel/rooms' . ($updateId > 0 ? '?edit_id=' . $updateId : ''));
        $data['updateItemData']     = service('HostelAndRoomsModel')->withDeleted()->find($updateId);
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/hostel/rooms-setup', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    /**
     * Delete one class/faculty based on request.
     * @return string
     */
    private function delete_requested_class_faculties(){
        if($this->request->getPost('acasetupClsFcl_delete') !== 'yes') return []; 
        
        $item = intval($this->request->getGet('edit_id'));
        if( service('HostelAndRoomsModel')->has_child_item_of_faculty( $item )){
            return ['display_msg' => get_display_msg('You can not delete this parent item without deleting child items under it.','danger')];
        }
        
//        $class_distributed = service('CoursesClassesMappingModel')
//                ->where('ccm_class_id' , $item)
////                ->withDeleted()
//                ->first();
//        if(is_object($class_distributed)){
//            $url = anchor("admin/academic/course/distribution?class_id={$class_distributed->ccm_class_id}&cls_wise_class_session={$class_distributed->ccm_year_session}", 'Check Here', ['class'=>'btn btn-info']);
//            return ['display_msg' => get_display_msg("You can not delete this this class because courses are attached to this class. To delete this class remove them first. $url" ,'danger')];
//        }
        
        $dl = service('HostelAndRoomsModel')->delete_permanently($item); // Object or false on failure
        if( ! $dl ){
            return ['display_msg' => get_display_msg('Unable to delete, please try again.','danger')];
        }
        @session_start(); // Reverse session_write_close?
        return redirect()->to(base_url('admin/hostel/rooms'))->with('display_msg', get_display_msg(lang('Rw.successfully_deleted')));
    }
    
    
    
    private function save_form( int $update_id = 0){
        if($this->request->getPost('acasetupClsFac_submit') !== 'yes') return []; $data_sa = [];
        $parent_id = intval($this->request->getPost('acSeUp_parent')); // Can be 0 (if root item adding)
        
        if( $parent_id === $update_id AND $update_id > 0 ){
            return ['display_msg' => get_display_msg('Parent ID error. You can not select same item as parent.','danger')];
        }
        if( ! service('HostelAndRoomsModel')->is_this_hostel_room_id_can_be_parent( $parent_id ) ){ 
            // User Requested to use an id as parent item, 5th level item can not be parent item
            return ['display_msg' => get_display_msg('Wrong parent item selected. Maximum depth reached or ID not exists in database.','danger')];
        }
        
        $parent_id = $parent_id > 0 ? $parent_id : NULL; // 0 is not accepted, foreign key will throw error.
            
        $save_data = [
            'hos_parent'            => $parent_id, 
            'hos_title'             => strip_tags(strval($this->request->getPost('acSeUp_title'))),
            'hos_excerpt'           => strip_tags(strval($this->request->getPost('acSeUp_excerpt'))), 
            'hos_capacity'          => intval($this->request->getPost('hos_capacity')), 
        ];
        
        if( $update_id > 0 ){ 
            $update = service('HostelAndRoomsModel')->update($update_id,$save_data);
            if($update ){
                $anchorUpdate = anchor("admin/hostel/rooms?edit_id=$update_id`",'Update again',['class'=>'btn btn-info btn-sm']);
                $display_msg = get_display_msg( "Class (ID:{$update_id}) Updated Successfully. $anchorUpdate",'success');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/hostel/rooms"))->with('display_msg',$display_msg);
            }else{
                $errorsXt = implode(', ', service('HostelAndRoomsModel')->errors());
                $data_sa['display_msg'] = get_display_msg(lang('Rw.unable_to_update_wrong_info') . $errorsXt,'danger');
            }
        }else{     
            $save = service('HostelAndRoomsModel')->insert($save_data);
            if( $save ){ /* Saved successfully */
                $insID    = service('HostelAndRoomsModel')->insertID();
                                
                $anchorUpdate = anchor("admin/hostel/rooms?edit_id=$insID",myLang('Update','পরিবর্তন করুন'),['class'=>'btn btn-info btn-sm']);
                $display_msg = get_display_msg(myLang('Saved ok. New item has been added Successfully. New ID:','সফলভাবে সংরক্ষণ করা হয়েছে। নতুন তথ্য যুক্ত করা হয়েছে যার নতুন আইডি নম্বর হল: ') . esc($insID) ." $anchorUpdate",'success');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/hostel/rooms"))->with('display_msg',$display_msg);
            }else{
                // Array if application error occurs, these errors can be shown. 
                // If DB sensative error occurs it will generate string error (hide these errors as it expose column name, foreign key id etc),
                // in dev mode CI will throw error.
                //$dbErr = service('HostelAndRoomsModel')->errors(); 
                $errorsXt = implode(', ', service('HostelAndRoomsModel')->errors());
                $data_sa['display_msg'] = get_display_msg(myLang('Unable to save data. Errors: ','তথ্য সংরক্ষণ করা সম্ভব হয়নি। ভুলসমূহ: ') . $errorsXt,'danger');
            }
        }
        $data_sa['errors'] = service('HostelAndRoomsModel')->errors();
        return $data_sa;
    }


} // End class

