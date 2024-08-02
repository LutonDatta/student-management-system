<?php namespace App\Controllers\Platform;

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
 * Globally change language. Redirect to source from where the request is made.
 */
class Language extends BaseController {
    
    public function change(){ 
        $redirect_to        = urldecode($this->request->getGet('return_to'));
        $language_change_to = $this->request->getGet('lang_change_to');
        
        // Set language to cookie, we can not set in session as session library is not available in configuration file
        // even if we call default php $_SESSION it breaks login sessions, and force users to login again.
        if( in_array($language_change_to, config('App')->supportedLocales)){
            helper('cookie');
            set_cookie('local_lang', $language_change_to, 60 * 60 * 24 * 365 ); // Valid for one year
        }
        
        if(filter_var($redirect_to, FILTER_VALIDATE_URL)){
            $redirect_to_url = $redirect_to; // Redirect user from where he has come
        }else{
            $redirect_to_url = base_url(); // Fall Back as invalid URL found
        }
        
        $data                       = $this->data;
        $data['title']              = 'Wait as we are changing your language';
        $data['redirect_to_url']    = $redirect_to_url;
        
        echo view('platform/platform-head', $data );
        echo view('platform/lang-change-redirect', $data );
        echo view('platform/platform-footer', $data );
    }
   
}
