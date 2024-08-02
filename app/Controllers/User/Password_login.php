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


class Password_login extends BaseController {
    
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
    }

    /**
     * Is there any magic login key? if exists let user login automatically. Otherwise show form to accept password. 
     * If form submitted try to verify password any let him login.
     * @param string $redirect_to Redirect user if destination address is specified. It is URL encoded. we need to decode before sending.
     */
    public function login(){
        $data                   = $this->data;
        $data['title']          = 'User Login';
        $data['showingPage']    = 'login'; // We may hide or change some template based on which page is being shown      
                
        
        if( service('AuthLibrary')->isUserLoggedIn()){            
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url('dashboard'))->with('display_msg', get_display_msg('You are already logged in.')); 
        }else{
            // Performs login using password and user ID or email.
            $password_login = $this->perform_form_login_using_password();
            if(is_object($password_login)){
                return $password_login; //  return statement redirect()->to() will work
            }else{
                $data = array_merge( $data, $password_login );
            }
        }
        
        echo view('user/head', $data);
        echo view('user/login_two_columns', $data );
        echo view('user/footer', $data);
    }
    
    
    
    private function perform_form_login_using_password( ){
        if($this->request->getPost('loginSubmitPost') !== 'yes' ) return []; else $data = []; 
            
        $userEmailOrID  = $this->request->getGetPost('login_email_or_id');
        $submited_pass  = $this->request->getPostGet('login_password');
        
        if( getSSMSAdminEmail() !== $userEmailOrID ){
            @session_start(); // We closed session already
            return redirect()->to(base_url('user/login'))->with('display_msg',get_display_msg('Invalid email address.','danger')); 
        }
        if(getSSMSAdminPassword() !== $submited_pass ){
            @session_start(); // We closed session already
            return redirect()->to(base_url('user/login'))->with('display_msg',get_display_msg('Invalid email password.','danger')); 
        }
                    
        (new \App\Libraries\Login_library())->storeLoginSessionData();
        
        @session_start(); // We closed session already
        return redirect()->to(base_url('dashboard'))->with('display_msg',get_display_msg('You have logged in successfully.','success')); 
    } // EOM
    
    
} // class end
