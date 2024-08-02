<?php namespace App\Libraries;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */


/**
 * This library created to work with Premium software thumb-taker image/avatar uploader purchased paying $17 at 03/08/2020.
 * 
 * This library acts based on JS ajax calls. So we do not need to process any non-ajax GET or POST requests. Accept get/post 
 * requests from controller and call this library from there. This ImagePicker sends request to the same endpoint for image
 * upload, crop and preview. So accept all kind of requests to the same controller/url.
 * 
 * JS script send requests to upload/preview/edit image like the following format: 
 * Upload (POST): upload_avatar.php
 *      Request:
 *                  Content-Disposition: form-data; name="action" value="upload"
 *                  Content-Disposition: form-data; name="file"; filename="ben-white-qDY9ahp0Mto-unsplash.jpg" Content-Type: image/jpeg value=image-data
 *      Response: {
 *              "name":"~avatar.jpg",
 *              "type":"jpg",
 *              "size":1732571,
 *              "url":"files\/~avatar.jpg",
 *              "width":6016,
 *              "height":4016,
 *              "versions":{
 *                  "avatar":{
 *                      "url":"files\/~avatar-avatar.jpg",
 *                      "width":200,
 *                      "height":200
 *                      }
 *                  }
 *              }
 * 
 * Preview (GET) : upload_avatar.php?action=preview&file=~avatar.jpg&width=800&data[key]=value&rand=1596505762308
 *              Here 'file' is file name we just uploaded. Returns image with  Content-Type: image/jpeg
 * 
 * In preview CROP:
 *      Request: Content-Type: application/x-www-form-urlencoded; with data
 *                      action=crop
 *                      &image=~avatar.jpg
 *                      &coords%5Bx%5D=601.6
 *                      &coords%5By%5D=652.2802547770701
 *                      &coords%5Bx2%5D=3008
 *                      &coords%5By2%5D=3056.7643312101914
 *                      &coords%5Bw%5D=2406.4
 *                      &coords%5Bh%5D=2404.484076433121
 *                      &rotate=0
 *                      &data%5Bkey%5D=value
 *      Response:  {
 *          "name":"avatar.jpg",
 *          "type":"jpg",
 *          "url":"files\/avatar.jpg",
 *          "width":6016,"height":4016,
 *          "versions":{
 *              "avatar":{
 *                  "url":"files\/avatar-avatar.jpg",
 *                  "width":200,"height":200
 *              }
 *          }
 *      }
 * Preview after CROP (GET): (image name appended at the site url)
 *      http://localhost/thumb-taker/files/avatar-avatar.jpg?1596506392078
 */


class Users_sign_photo_local_file_storage_library{
    /**
     * @var array
     */
    protected $options;

    protected $errorMessages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded or file size is too large',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'gd' => 'PHP GD library is NOT installed on your web server',
        'post_max_size'     => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size'     => 'File is too big',
        'min_file_size'     => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_width'     => 'Image exceeds maximum width of ',
        'min_width'     => 'Image requires a minimum width of ',
        'max_height'    => 'Image exceeds maximum height of ',
        'min_height'    => 'Image requires a minimum height of ',
        'upload_failed' => 'Failed to upload the file',
        'move_failed'   => 'Failed to upload the file',
        'invalid_image' => 'Invalid image',
        'image_resize'  => 'Failed to resize image',
        'not_exists'    => 'Failed to load the image'
    );

    /**
     * 
     * @param object $request
     * @param object $response
     * @param object $db
     * @param int $user_id Avater must have user id associated with file name. For which user we are uploading thumbnail?
     */
    public function __construct( object $request, object $response ){
        $this->request      = $request;
        $this->response     = $response;
                        
        $this->options = array(
            // Upload directory url
            'upload_url'        => school_url('arf/sign/photo/'), // Attachment ID will be added here to see the image
            
            // Accepted file types
            'accept_file_types' => implode('|', get_allowed_image_extensions()), // 'png|jpg|jpeg|gif', // Accepted file types
                
            // File size restrictions (in bytes)
            'max_file_size'     => 1024 * 1024 * 2, // 2 MB
            'min_file_size'     => 1,
            
            // Image resolution restrictions (in px)
            'max_width'         => null, 
            'max_height'        => null,
            'min_width'         => 1,
            'min_height'        => 1,  
            
            // Auto orient image based on EXIF data
            'auto_orient'       => true, 
        );
        
    } // EOM
    
    

    /**
     * Initialize upload and crop actions.
     */
    public function initialize(){
        if ( ! extension_loaded('gd') || ! function_exists('gd_info') ) {
            $error = $this->getErrorMessage('gd');
            log_message('critical', 'We need GD to upload and store users avatar.'. $error);
            return $this->generateResponse( ['error' => $error] );
        }
        
        $action = service('request')->getGetPost('action');
        $method = $action.'Action';
        
        $available_actions = array(
            'load', // Display image, no authorization/login needed
            'crop',
            'upload', 
            'preview', // Authorization needed as it is for crop preview
            
        );
        
        if( isset($_SERVER['CONTENT_LENGTH']) && intval($_SERVER['CONTENT_LENGTH']) > 0 && count($_POST)===0){
            return $this->generateResponse( ['error' => "Your file is too large. Please upload smaller image. PHP discarded POST data because of request exceeding post_max_size. Image should be 1280x300px and less then 800kb."] );
        }
        
        if( ! in_array($action, $available_actions)){
            return $this->generateResponse( ['error' => "Invalid action. Image should be 300x300px and less then 100kb."] );
        }
        
        if (method_exists($this, $method)){
            return $this->{$method}();
        }
        return $this->generateResponse( ['error' => 'Not a valid action found.'] );
    } // EOM

    
    /**
     * Load means image URL and related image data to the browser. For it NO authorization/login needed.
     * Then browser will load image using returned URL.
     */
    protected function loadAction(){
        $defaultImage           = new \stdClass();
//        $defaultImage->name     = 'profile-pic-boy.png';
//        $defaultImage->type     = 'png';
//        $defaultImage->url      = cdn_url('default-images/profile-pic-boy.png');
//        $defaultImage->width    = 300;
//        $defaultImage->height   = 300;
                        
        $user = service('UserStudentsModel')->select('u_sign_photo_id')->find(service('AuthLibrary')->getLoggedInUserID());
        if( !is_object($user)) { return $this->generateResponse($defaultImage); }
        $att = service('AttachmentsSignModel')->find(intval($user->u_sign_photo_id));
        if( ! is_object($att) ) {return $this->generateResponse($defaultImage); }
        
        $filepath = service('AttachmentsSignModel')->get_attachment_full_path( $att );
        
        if(file_exists($filepath) AND ! is_dir($filepath)){
            // Requested image exists in file storage. (Image might not exists if file not upload properly)
            $image = new \stdClass();
            $image->name = $att->ats_id . '.' . $att->ats_ext;
            $image->type = $att->ats_ext;
            $image->url  = school_url("arf/sign/photo/{$att->ats_id}.{$att->ats_ext}");
            list($image->width, $image->height) = @getimagesize($filepath);
            return $this->generateResponse($image);
        }else{
            return $this->generateResponse($defaultImage);
        }
    } // EOM

    /**
     * Preview action.
     *
     * @return void
     */
    protected function previewAction(){
        $user = service('UserStudentsModel')->select('u_sign_photo_id')->find(service('AuthLibrary')->getLoggedInUserID());
        if( !is_object($user)) { return $this->generateResponse(['error'=>'Invalid User ID.']); }
        $att = service('AttachmentsSignModel')->find(intval($user->u_sign_photo_id));
        if( ! is_object($att) ) {return $this->generateResponse(['error'=>'No avatar found. Please upload.']); }                 
        $filepath = service('AttachmentsSignModel')->get_attachment_full_path( $att );
        if( ! (file_exists($filepath) AND !is_dir($filepath))){
            return $this->generateResponse(['error'=>'Error in previewAction - Local File.']);
        }
        
        return $this->response
                ->setHeader('Content-Length', filesize($filepath))
                ->setHeader('Content-type', $att->ats_type) // Should be like: image/jpeg
                ->download($filepath,null);
    } // EOM


    /**
     * Upload action.
     *
     * @return void
     */
    protected function uploadAction(){
        $upload = isset($_FILES['file']) ? $_FILES['file'] : null;

        $file = $this->handleFileUpload(
            @$upload['tmp_name'],
            @$upload['name'] == 'blob' ? md5(mt_rand()).'.jpg' : @$upload['name'],
            @$upload['size'],
            @$upload['error']
        );
        return $this->generateResponse($file);
    }

    /**
     * Handle file upload.
     *
     * @param  string  $uploaded_file
     * @param  string  $name
     * @param  integer $size
     * @param  integer $error
     * @return stdClass
     */
    protected function handleFileUpload($uploaded_file, $name, $size, $error){
        // Save image as attachment and record as avatar to the database
        $user = service('UserStudentsModel')->select('u_sign_photo_id')->find(service('AuthLibrary')->getLoggedInUserID());
        if( !is_object($user)) { 
             // User ID must exists as we already verified user. User might enter wrong user id
            return ['error'=>'Working user ID not found error.'];
        }
        $fileObj        = new \CodeIgniter\Files\File($uploaded_file);
        $image          = new \stdClass();
        $image->name    = $name;
        $image->type    =  pathinfo(strtolower($name), PATHINFO_EXTENSION);
        $image->size    = $this->fixIntOverflow(intval($size));
        list($image->width, $image->height) = @getimagesize($uploaded_file);
        $data = [
            'ats_ext'        => $image->type,
            'ats_type'       => $fileObj->getMimeType(),
            'ats_size'       => ceil( $image->size / 1024 ),  // 250.880 -> 250
        ];
        
        $attLib = service('AttachmentsSignModel');
        $att    = service('AttachmentsSignModel')->find(intval($user->u_sign_photo_id));
        if( ! is_object( $att ) ){
            $attID7 = $attLib->get_attachment_id( $data );
            $this->upload_attachment_path   = $attLib->get_attachment_path($attID7) . DIRECTORY_SEPARATOR;
            $this->upload_attachment_id     = $attID7;
        }else{
            $overwritePreviousAvatar = true;
            /* We already have attachment ID and path. Use it to overwrite previous avatar */
            $this->upload_attachment_path   = $attLib->get_attachment_path($att->ats_id) . DIRECTORY_SEPARATOR;
            $this->upload_attachment_id     = $att->ats_id;
            // We do not need to use db connection as we will upload image to cloud storage only.
            // Close connection to save resourse and allow other more users to connect to the db.
        }
        $image->name = $this->upload_attachment_id . '.' . $image->type;
        $image->path = $this->upload_attachment_path . $image->name;
        $image->url = school_url("arf/sign/photo/{$this->upload_attachment_id}");
        
        if (!$this->validate($uploaded_file, $image, $error)) {
            unset($image->path);
            return $image; // Will show some error message like: $image->error = 'Some error message.';
        }
        
        try{
            $this->request->getFile('file')->move( 
                WRITEPATH . $this->upload_attachment_path,  // Destination dir
                $image->name,           // New name
                true                    // Overwrite existing file
            ); // Thorw exception if failed
            if( ! empty($overwritePreviousAvatar)){
                // Update extension like jpg/png or image type, it can be different in different uploads
                service('AttachmentsSignModel')->update($this->upload_attachment_id, $data);
            }
            // File uploaded now crop automatically
            $filePathImg = WRITEPATH . $this->upload_attachment_path . $image->name;
            $process = \Config\Services::image()->withFile($filePathImg)
                ->reorient()                // Fix mobile orientation
                ->fit(300, 80, 'center')    // Crop + Resize = 2 actions
                ->save($filePathImg);
        }catch(Exception $e){
            $image->error = $e->getMessage();
            unset($image->path);
            return $image;
        }
        if( ! is_object($att) ){ 
            /* Update row if there is a new ID. That means update only at the first time. Because we will not change attachment ID.
             * We will just update image in the same ID. */
            service('UserStudentsModel')->update(service('AuthLibrary')->getLoggedInUserID(),['u_sign_photo_id'=>$this->upload_attachment_id]);
        }
        
        unset($image->path);
        return $image;
    }

    /**
     * Crop action.
     *
     * @return void
     */
    protected function cropAction(){
        $user = service('UserStudentsModel')->select('u_sign_photo_id')->find(service('AuthLibrary')->getLoggedInUserID());
        if( !is_object($user)) { return $this->generateResponse(['error'=>'Invalid User ID.']); }
        $att = service('AttachmentsSignModel')->find(intval($user->u_sign_photo_id));
        if( ! is_object($att) ) {return $this->generateResponse(['error'=>'No avatar found. Please upload.']); }
        
        $image          = new \stdClass();
        $image->name    = $att->ats_id . '.' . $att->ats_ext;
        $image->type    = $att->ats_ext;
        $image->url     = school_url('arf/sign/photo/' . $att->ats_id  );

        $filepath = service('AttachmentsSignModel')->get_attachment_full_path( $att );
        
        if( ! (file_exists($filepath) AND !is_dir($filepath))){
            return $this->generateResponse(array('error'=> 'Local file does not exists.' ));
        }
                
        if (!preg_match('/.('. implode('|', get_allowed_image_extensions()) .')+$/i', $image->name)) {
            return $this->generateResponse(array('error'=> 'Not accepted file type.' ));
        }
        list($image->width, $image->height) = @getimagesize($filepath);
        
        try {
            $process = \Config\Services::image()->withFile($filepath)
                ->reorient()                // Fix mobile orientation
                ->fit(300, 80, 'center')    // Crop + Resize = 2 actions
                ->save($filepath);
            
            return $this->generateResponse($image);
        }catch (CodeIgniter\Images\ImageException $e){
            return $this->generateResponse(array('error'=>  $e->getMessage()));
        }

        $fileObj = new \CodeIgniter\Files\File($filepath);
        service('AttachmentsSignModel')->limit(1)->update($att->ats_id, [ 'ats_size' => ceil( $fileObj->getSize() / 1024 ) ]);
        
        unset($image->path);
        // Generate json response
        return $this->generateResponse($image);
    }

    /**
     * Validate uploaded file.
     *
     * @param  string   $uploaded_file
     * @param  stdClass $name
     * @param  string   $error
     * @return boolean
     */
    protected function validate($uploaded_file, $file, $error){
        if (!$uploaded_file) {
            $file->error = $this->getErrorMessage(4);
            return false;
        }

        if ($error) {
            $file->error = $this->getErrorMessage($error);
            return false;
        }

        $content_length = $this->fixIntOverflow(intval($_SERVER['CONTENT_LENGTH']));
        $post_max_size  = $this->getConfigBytes(ini_get('post_max_size'));

        if ($post_max_size && $content_length > $post_max_size) {
            $file->error = $this->getErrorMessage('post_max_size');
            return false;
        }

        if ($this->options['max_file_size'] && $file->size > $this->options['max_file_size']) {
            $file->error = $this->getErrorMessage('max_file_size');
            return false;
        }

        if ($this->options['min_file_size'] && $file->size < $this->options['min_file_size']) {
            $file->error = $this->getErrorMessage('min_file_size');
            return false;
        }

        if (!preg_match('/.('. implode('|', get_allowed_image_extensions()) .')+$/i', $file->name)) {
            $file->error = $this->getErrorMessage('accept_file_types');
            return false;
        }

        if (empty($file->width) || empty($file->height)) {
            $file->error = $this->getErrorMessage('invalid_image');
            return false;
        }

        $max_width  = @$this->options['max_width'];
        $max_height = @$this->options['max_height'];
        $min_width  = @$this->options['min_width'];
        $min_height = @$this->options['min_height'];

        if ($max_width || $max_height || $min_width || $min_height) {
            if ($max_width && $file->width > $max_width) {
                $file->error = $this->getErrorMessage('max_width').$max_width.'px';
                return false;
            }

            if ($max_height && $file->height > $max_height) {
                $file->error = $this->getErrorMessage('max_height').$max_height.'px';
                return false;
            }

            if ($min_width && $file->width < $min_width) {
                $file->error = $this->getErrorMessage('min_width').$min_width.'px';
                return false;
            }

            if ($min_height && $file->height < $min_height) {
                $file->error = $this->getErrorMessage('min_height').$min_height.'px';
                return false;
            }
        }

        return true;
    } // EOM



    


    /**
     * Get file extension.
     *
     * @param  string $filename
     * @return string
     */
    public function getFileExtension($filename){
        return pathinfo(strtolower($filename), PATHINFO_EXTENSION);
    }

    /**
     * Generate json response.
     *
     * @param  mixed $response
     * @return string
     */
    public function generateResponse($response){
        return json_encode($response);
    }

    /**
     * Get error message.
     *
     * @param  string $error
     * @return string
     */
    public function getErrorMessage($error){
        return isset($this->errorMessages[$error]) ? $this->errorMessages[$error] : $error;
    }

    /**
     * Resize image.
     *
     * @param  string       $src_path  Source image path
     * @param  string|null  $dst_path  Destination image path
     * @param  integer      $src_x     x-coordinate of source point
     * @param  integer      $src_y     y-coordinate of source point
     * @param  integer      $dst_w     Destination width
     * @param  integer      $dst_h     Destination height
     * @param  integer      $src_w     Source width
     * @param  integer      $src_h     Source height
     * @return bool
     */
    public function resizeImage($src_path, $dst_path = null, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        $src_x = ceil($src_x);
        $src_y = ceil($src_y);
        $dst_w = ceil($dst_w);
        $dst_h = ceil($dst_h);
        $src_w = ceil($src_w);
        $src_h = ceil($src_h);

        $dst_path  = ($dst_path) ? $dst_path : $src_path;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);
        $extension = $this->getFileExtension($src_path);

        if (!$dst_image) {
            return false;
        }

        switch ($extension) {
            case 'gif':
                $src_image = imagecreatefromgif($src_path);
                break;
            case 'jpeg':
            case 'jpg':
                $src_image = imagecreatefromjpeg($src_path);
                break;
            case 'png':
                imagealphablending($dst_image, false);
                imagesavealpha($dst_image, true);
                $src_image = imagecreatefrompng($src_path);
                @imagealphablending($src_image, true);
                break;
        }

        if (isset($src_image) && !$src_image) {
            return false;
        }

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
            return false;
        }

        switch ($extension) {
            case 'gif':
                return imagegif($dst_image, $dst_path);
                break;
            case 'jpeg':
            case 'jpg':
                return imagejpeg($dst_image, $dst_path);
                break;
            case 'png':
                return imagepng($dst_image, $dst_path);
                break;
        }
    }

    /**
     * Rotate image.
     *
     * @param  string  $src_path
     * @param  integer $angle
     * @return void
     */
    public function rotateImage($src_path, $angle){
        $type = $this->getFileExtension($src_path);

        switch ($type) {
            case 'gif':
                $source = imagecreatefromgif($src_path);
                break;
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($src_path);
                break;
            case 'png':
                $source = imagecreatefrompng($src_path);
                break;
        }

        $image = imagerotate($source, $angle, 0);

        switch ($type) {
            case 'gif':
                imagegif($image, $src_path);
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, $src_path);
                break;
            case 'png':
                imagepng($image, $src_path);
                break;
        }

        imagedestroy($source);
        imagedestroy($image);
    }

    /**
     * Orient image based on EXIF orientation data.
     *
     * @param  string $filepath
     * @return void
     */
    protected function orientImage($filepath){
        if (!preg_match('/\.(jpe?g)$/i', $filepath)) {
            return;
        }

        if (!function_exists('exif_read_data')) {
            return;
        }

        $exif = @exif_read_data($filepath);

        if (!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 3: $angle = 180; break;
                case 6: $angle = -90; break;
                case 8: $angle = 90; break;
            }

            if (isset($angle)) {
                $this->rotateImage($filepath, $angle);
            }
        }
    }

    protected function upcountName($name){
        return preg_replace_callback( '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/', array($this, 'upcountNameCallback'), $name, 1 );
    }

    protected function upcountNameCallback($matches){
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';

        return ' ('.$index.')'.$ext;
    }

    protected function getConfigBytes($val){
        $val  = trim($val);
        $last = strtolower($val[strlen($val)-1]);

        switch ($last) {
            case 'g':
                $val = (int) $val * 1024;
            case 'm':
                $val = (int) $val * 1024;
            case 'k':
                $val = (int) $val * 1024;
        }

        return $this->fixIntOverflow($val);
    }

    protected function fixIntOverflow($size)
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }

        return $size;
    }
} // EOC

