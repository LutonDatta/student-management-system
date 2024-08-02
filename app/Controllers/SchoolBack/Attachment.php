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


class Attachment extends BaseController {
    
    /**
     * Upload file, processed from a single URL.
     * CAUTON: Only allow single file upload.
     */
    public function upload_file(){        
        /* Only logged in users can upload */
        if( ! service('AuthLibrary')->isUserLoggedIn() ){
            $this->response->setStatusCode(401,'Unauthorized access. You are not allowed to upload files, please login.');
            return $this->response->setJSON(['error'=>'Please login to upload file.']);
        }
        
        $requestData                = $this->request->getPost('data');
        $typeofUpload               = $this->request->getPost('action');
        
        if(is_array($requestData) AND isset($requestData['file_uploading_by']) AND $requestData['file_uploading_by'] == 'imgPicker' AND $typeofUpload == 'upload' ){
            // Uploading image sliders by Image picker
            // Everything will be returned what this function is returned
            // Response of this function might be different from general upload
            return $this->process_image_upload_by_imgPicker_upload(); 
        }
        if(is_array($requestData) AND isset($requestData['file_uploading_by']) AND $requestData['file_uploading_by'] == 'imgPicker' AND $typeofUpload == 'crop' ){
            // Image picker trying to crop sending request to the same url
            return $this->process_image_upload_by_imgPicker_crop(); 
        }
        
        
        // Is it uploading by dropzone?
        $this->is_d_up = $this->request->getPost('file_uploading_by')=='dropzone' ? true : false; 
        
        // Upload single file
        $file_single = $this->request->getFile('uploadMe_Single');
        $save_result = $this->store_file( $file_single );
        return $this->response->setJSON($save_result);
    }
    
    
    /**
     * Store a file
     */
    private function store_file( $file ){
        $return = array( // Default values, Data will be overwritten later
            'success'       => 0,   // 1 for success & 0 for failure
            'success_msg'   => '',  // Simple upload error/success message 
            'status_code'   => 400,  // Set 200 if successfully uploaded
            'file' => array(
                'url'       => '#', // Publicly accessable url (i.e. Image can be viewed after uploading)
            ),
            'at_id' => 0,   // New attachment ID, we just inserted to the table (inset id)
        );
           
        if(is_null($file)){
            $errMsg                 = 'Something wrong with this file. (Null value found). May be file size is too high.';
            $return['success_msg']  = $errMsg;
            // For image picker Js lib to show error in ajax if image size is greater then upload_max_filesize
            $return['error']        = $errMsg;   
        }elseif (! $file->isValid()){
            $return['success_msg'] = $file->getErrorString().'('.$file->getError().')';
        }elseif(intval(str_replace(',','',$file->getSize() / (1024 * 1024 ))) > 5 ){ // 5MB allowed
            $return['success_msg'] = 'File is too large. Maximum allowed file size is 5 MB';
        }elseif($file->getSizeByUnit('kb') <= 0 ){ // txt files might be less then 1 kilobyte
            $return['success_msg'] = 'Corrupted file detedted.';
        }elseif(! $file->guessExtension()){
            $return['success_msg'] = 'Invalid file type.';
        }elseif(! in_array($file->guessExtension(), get_allowed_file_extensions('all')) ){
            $return['success_msg'] = 'Unsupported file type detected.';
        }else{            
            $attachmentuu = array(
                        'at_size'       => ceil( $file->getSizeByUnit('kb') ), 
                        'at_type'       => $file->getClientMimeType(),      // Example: image/png
                        'at_ext'        => $file->guessExtension(),
                    );
            
            $attID          = service('AttachmentsModel')->get_new_attachment_id($attachmentuu);
            $save_to_path   = service('AttachmentsModel')->get_attachment_path($attID) . DIRECTORY_SEPARATOR;
            $objectName     = "{$attID}." . $file->guessExtension(); // Unique name for file with extension

            if(get_fileUploadStorage() === 'localFileStorage'){
                try{
                    // Destination dir, New name, Overwrite existing file
                    $file->move( WRITEPATH . $save_to_path, $objectName, true);
                    $this->resize_and_reduce_image_size_for_fast_page_load( WRITEPATH . $save_to_path.$objectName, $attID );
                }catch(Exception $e){
                    return ['error'=> $e->getMessage() ];
                }
            }else{
                try{
                    $bucket = service('Google_Cloud_Storage_StorageClient');
                    $bucket->upload(fopen($file->getTempName(), 'r'),['name'=> $save_to_path . $objectName ]);
                }catch(Exception $e){
                    return ['success_msg'=> $e->getMessage() ];
                }
            }
            
            $return['status_code']  = 200;
            $return['success']      = 1;
            $return['success_msg']  = 'Successfully uploaded';
            $return['file']['url']  = base_url("arf/$attID");
            $return['at_id']        = $attID;
        }
        
        if($this->is_d_up AND $return['status_code'] >= 400 ){
            // For dropzone uploader we need to set up status code to show error (more then 400)
            $this->response->setStatusCode($return['status_code'],$return['success_msg']);
            return [$return['success_msg']];
        }
        return $return; // at_id in this array will be used to allow this attachment to edit info
    } /* EOM */
    
    /**
     * When we upload files/images, we can not change files but we can use and update image dimension to use in our posts.
     * @param string $fileFullPath If this path is a valid image resize and make it usable to load/view from post and gallery.
     */
    private function resize_and_reduce_image_size_for_fast_page_load( $fileFullPath = null, $attID = null ){
        try{
            // Just resize/compress large files if it is image only
            $imD = \Config\Services::image()->withFile($fileFullPath)->getProperties(true); // Arror or Exception
            $imW = intval($imD['width']); $imH = intval($imD['height']);
            // No image should be greater then 1280x700px, as higher dimensation image throw out of memory error
            if($imW > 1280){ $remW = floor($imW / 1279); $imW = ceil($imW / $remW);  }
            if($imH > 700){ $remH = floor($imH / 699); $imH = ceil($imH / $remH);  }
            \Config\Services::image()->withFile($fileFullPath)->reorient()->resize($imW - 1, $imH - 1, true)->save($fileFullPath);
            
            $fileSize = filesize($fileFullPath); // False or int size in byte
            if($fileSize){
                service('AttachmentsModel')->limit(1)->update($attID, [ 'at_size'=> ceil($fileSize / 1024) ]);
            }
        }catch(Exception $e){
            // If file is not a valid image, it will throw exception, do nothing.
        }
    } /* EOM */
    
    
    private function process_image_upload_by_imgPicker_upload(){
        /* imgPicker error response is different - Unloagged user error - slider must be uploaded from backend */
        if( ! service('AuthLibrary')->isUserLoggedIn() ){
            $this->response->setStatusCode(401,'Unauthorized access. You are not allowed to upload files, please login.');
            return $this->response->setJSON(['error'=>'You are not logged in. Please login to upload file.']);
        }
        $file = $this->request->getFile('file');
        
        if ( ! is_object($file) ){
            /**
             * If no file is submitted by the browser, we will have null value here.
             */
            return $this->response->setJSON(['error'=>'No file submitted. Please submit a file.']);
        }
        
        if ( ! $file->isValid()){
            /*  
             *  Sand error message from server. We might have the following errors:
                The file exceeds your upload_max_filesize ini directive.
                The file exceeds the upload limit defined in your form.
                The file was only partially uploaded.
                No file was uploaded.
                The file could not be written on disk.
                File could not be uploaded: missing temporary directory.
                File upload was stopped by a PHP extension.
             */
            $serverError = $file->getErrorString().'('.$file->getError().')';
            return $this->response->setJSON(['error' => "Invalid file: $serverError"]);
        }
        
        $file_ext = $file->guessExtension();
        if(! in_array( $file_ext, get_allowed_file_extensions('image'))){
            return $this->response->setJSON(['error' => "We do not allow this file extension: $file_ext"]);
        }
        
        if( $file->hasMoved() ){
            /*
                We might have the following errors: 
                the file has already been moved
                the file did not upload successfully
                the file move operation fails (eg. improper permissions)
             */
            return $this->response->setJSON(['error' => "This file has already been moved."]);
        }
        $attachmentuu = array(
                'at_size'       => ceil( $file->getSize() / 1024 ),  // 250.880 -> 250
                'at_type'       => $file->getClientMimeType(), // Example: image/png
                'at_ext'        => $file->guessExtension()
            );

        $attID          = service('AttachmentsModel')->get_new_attachment_id($attachmentuu);
        $save_to_path   = service('AttachmentsModel')->get_attachment_path($attID) . DIRECTORY_SEPARATOR;
        $objectName     = "{$attID}." . $file->guessExtension(); // Unique name for file with extension
        $filepath       = $save_to_path . $objectName;

        if(get_fileUploadStorage() === 'localFileStorage'){ /* Store to local storage if we are not using GCP*/
            try{
                // Destination dir, New name, Overwrite existing file
                $file->move( WRITEPATH . $save_to_path, $objectName, true);
                $image = \Config\Services::image()->withFile(WRITEPATH . $save_to_path.$objectName)->getFile()->getProperties(true);
            }catch(Exception $e){
                return $this->response->setJSON(['error' => $e->getMessage()]);
            }
        }else{
                $bucket = service('Google_Cloud_Storage_StorageClient');
                try{
                    $bucket->upload(fopen($file->getTempName(), 'r'),['name'=> $filepath]);
                }catch(Exception $e){
                    return $this->response->setJSON(['error' => $e->getMessage()]);
                }
                $ae_tmp_file = "/tmp/".rand(1000,9999)."-".$objectName;

                $object = $bucket->object($filepath);
                if( $object->exists()){
                    $object->downloadToFile($ae_tmp_file); // Copy to app engine temp directory to edit file
                }else{
                    return $this->response->setJSON(['error' => "Object not exists. May be failed to upload."]);
                }     
                $image = \Config\Services::image()->withFile($ae_tmp_file)->getFile()->getProperties(true);
        }
        return $this->response->setJSON([
            'new_attachment_id' => $attID,
            'imagePreviewUrl'   => base_url("arf/$attID"),
            'name'              => $attID,   // Used to load/preview/delete this file
            'width'             => $image['width'], // Width and height required for Jcrop
            'height'            => $image['height']
        ]);
    }
    
    private function process_image_upload_by_imgPicker_crop(){
        /* imgPicker error response is different - Unloagged user error - slider must be uploaded from backend */
        if( ! service('AuthLibrary')->isUserLoggedIn() ){
            $this->response->setStatusCode(401,'Unauthorized access. You are not allowed to upload files, please login.');
            return $this->response->setJSON(['error'=>'You are not logged in. Please login to upload file.']);
        }
        $at_id  = intval($this->request->getPost('image'));
        $attachment     = service('AttachmentsModel')->find($at_id);
        if( ! is_object( $attachment ) ) {
            return $this->response->setJSON(['error'=>'Invalid image ID found. We can not find this image.']);
        }
        $coords = $this->request->getPost('coords');
        if( ! is_array( $coords ) ) {
            return $this->response->setJSON(['error'=>'You must select a part of image to crop.']);
        }
        $x = (is_array( $coords ) AND isset($coords['x']) ) ? intval($coords['x']) : 0;
        $y = (is_array( $coords ) AND isset($coords['y']) ) ? intval($coords['y']) : 0;
        $h = (is_array( $coords ) AND isset($coords['h']) ) ? intval($coords['h']) : 0;
        $w = (is_array( $coords ) AND isset($coords['w']) ) ? intval($coords['w']) : 0;
        
        $save_to_path   = service('AttachmentsModel')->get_attachment_path($at_id) . DIRECTORY_SEPARATOR;
        $objectName     = "{$at_id}." . $attachment->at_ext; // Unique name for file with extension
        $filepath       = $save_to_path . $objectName;
        $ae_tmp_file    = "/tmp/".rand(1000,9999)."-".$objectName;
        
        $bucket = service('Google_Cloud_Storage_StorageClient');
        $object = $bucket->object($filepath);
        if( $object->exists()){
            $object->downloadToFile($ae_tmp_file); // Copy to app engine temp directory to edit file
            try {
                $new = \Config\Services::image()->withFile($ae_tmp_file)->crop($w, $h, $x, $y)->save($ae_tmp_file); // Overwrite previous image in temp image      
                $bucket->upload(fopen($ae_tmp_file, 'r'),['name'=> $filepath]); // Store new image in Google Cloud Storage
                return $this->response->setJSON(['cropped_attachment_id' => $at_id,]);
            }catch (CodeIgniter\Images\ImageException $e){
                return $this->response->setJSON(['error'=> $e->getMessage()]);
            }
        }else{
            return $this->response->setJSON(['error' => "Object not exists. May be failed to upload."]);
        }     
    }
    
} // EOC
