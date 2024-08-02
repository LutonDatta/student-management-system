<?php namespace App\Libraries;

use App\Models\Users_Model;
use App\Models\Users_fields_Model;

/**
 * This is a test librarey. We can use this library as
 * use <?php namespace App\Libraries\Auth;
 */
class Auth_library {
    
    /**
     * Logged in user must have ID greater then 0
     * @var int 
     */
    protected $user_id  = 0;

    /**
     * User role object.
     * @var object
     */
    protected $user_role = null;

    /**
     * Is user logged in?
     * @var boolean
     */
    protected $isLoggedIn = false;
    
    protected $manyUsersFullNames = [];


    public function __construct() {
        $this->isLoggedIn       = boolval(session('logged_in'));
        $this->user_id          = intval(session('student_u_id'));
        $this->user_email       = intval(session('u_email'));
    }
    
    public function getSession(){
        return session();
    }
    
    public function isLoggedIn(){
        return $this->isLoggedIn;
    }
    
    public function isUserLoggedIn(){
        return $this->isLoggedIn;
    }
    
    public function getLoggedInUserID(){
        return $this->user_id;
    }
    
    public function getLoggedInUserEmail(){
        return $this->user_email;
    }
    
    public function getUserFullName_fromObj( object $user, string $default_name = '' ){
        $name = trim(implode(' ', [
            (strlen($user->student_u_name_initial) > 0) ?  get_name_initials($user->student_u_name_initial) : '',
            $user->student_u_name_first,
            $user->student_u_name_middle,
            $user->student_u_name_last
        ]));
        return (strlen($name) > 0) ? trim($name) : $default_name; // Name not set?
    } /* EOM */
    
} // EOC
