<?php namespace App\Controllers\User;



class Admission_pictures extends BaseController{
        
    public function upload_student_thumb_by_teacher(){
        /* Uploading image might take a lot of time. Allow other process to take place in the mean time. */
        session_write_close(); 
        
        // Request from ImagePicker must be AJAX, or it has action as get parameter
        if( ! ( $this->request->getGet('action') OR $this->request->isAJAX() ) ){
            return $this->response->setJSON(['error'=>'Only JSON allowed.']);
        }
        
        // Create new ImgPicker instance. Auto activate actions. 
        // We have json encoded data to return or image based on requests
        return (new \App\Libraries\Users_thumb_upload_by_teacher_library($this->request, $this->response))->initialize(); 
        
    } /* EOM */
    
    
    public function upload_student_sign_by_teacher(){
        /* Uploading image might take a lot of time. Allow other process to take place in the mean time. */
        session_write_close(); 
        
        // Request from ImagePicker must be AJAX, or it has action as get parameter
        if( ! ( $this->request->getGet('action') OR $this->request->isAJAX() ) ){
            return $this->response->setJSON(['error'=>'Only JSON allowed.']);
        }
        
        // Create new ImgPicker instance. Auto activate actions. 
        // We have json encoded data to return or image based on requests
        return (new \App\Libraries\Users_sign_upload_by_teacher_library($this->request, $this->response))->initialize(); 
        
    } /* EOM */
    

} /* EOC */
