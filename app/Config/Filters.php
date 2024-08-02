<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'auth'      => \App\Filters\Auth_filter::class, // Sometimes triggered from routes.php
	];

	// Always applied before every request
	public $globals = [
		'before' => [],
		'after'  => [],
	];

	public $methods = [];
        
	public $filters = [];
        
        public function __construct() {
            parent::__construct();
        }
}
