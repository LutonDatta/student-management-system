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

use CodeIgniter\Controller;

class BaseController extends Controller
{

	protected $helpers = ['text', 'form', 'html', 'url'];

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
            // Do Not Edit This Line
            parent::initController($request, $response, $logger);

            $this->data     = [];
                
	} // EOM
        
} // EOC
