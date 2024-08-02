<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use CodeIgniter\Honeypot\Exceptions\HoneypotException;

/**
 * Auth_filter use functionalities from Auth library
 */
class Auth_filter implements FilterInterface {

	
	public function before(RequestInterface $request, $arguments = NULL){
                        
            if( ! service('AuthLibrary')->isUserLoggedIn()){
                if( ! $request->isAJAX() ){
                    
                    log_message('info','Auth filter redirects as user is not logged in.');
                    
                    // Get URI object and meke it URL, othrewise we will have no query string, as a result we
                    //  will face wrong URL with out query string for few pages.
                    $url_with_query = (string)current_url(TRUE); 
                    
                    return redirect()
                            ->to(base_url('user/login?redirect_to=' . urlencode($url_with_query)))
                            ->with('display_msg', get_display_msg(lang('Rw.not_logged_in_please_login_now'),'danger'));
                }
                
                log_message('info','Auth filter exception: User is not logged in.');
                
                throw new \Exception('Authorization failed. You must be logged in. Your browser may failed to send cookie.', 401);
            }
	}

	
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = NULL){
		// Do nothing
	}

}
