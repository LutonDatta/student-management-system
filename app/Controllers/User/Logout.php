<?php namespace App\Controllers\User;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

class Logout extends BaseController {
    
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        /**
         * When session is no longer needed close it and allow other 
         * request of the same browser(JSON request) load data faster.
         * CUTION: Use @session_start() before redirect()->with() to show display messages.
         */
        session_write_close(); 
    }

    /**
     * Destroy session and redirect to home page.
     * @return null
     */
    public function logout(){                
        session()->destroy();

        /*
	|--------------------------------------------------------------------------
	| DISPLAY MESSAGE WILL NOT WORK HERE AS SESSION HAS ALREADY BEEN DESTROYED
	|--------------------------------------------------------------------------
	| After destroying session object, in this same request, we can not show
	| redirect()->with() function.
	*/
        @session_start(); // Reverse session_write_close?
        return redirect()->to(base_url('user/login'))->with('display_msg', get_display_msg('You have successfully logged out.')); // Send to login page
    }
    
} // class end
