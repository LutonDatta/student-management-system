<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pager extends BaseConfig
{
	/*
	|--------------------------------------------------------------------------
	| Templates
	|--------------------------------------------------------------------------
	|
	| Pagination links are rendered out using views to configure their
	| appearance. This array contains aliases and the view names to
	| use when rendering the links.
	|
	| Within each view, the Pager object will be available as $pager,
	| and the desired group as $pagerGroup;
	|
	*/
	public $templates = [
                //'default_full'      => 'CodeIgniter\Pager\Views\default_full',
		'default_full'      => 'App\Views\Pager\default_full', /* CSS class added to separate active link */
		'default_simple'    => 'CodeIgniter\Pager\Views\default_simple',
		'default_head'      => 'CodeIgniter\Pager\Views\default_head',
		'school_front_sm'   => 'App\Views\Pager\pager-sm',
		'school_back'       => 'App\Views\SchoolBackViews\pager',
		'instAdmiInbx'      => 'App\Views\SchoolBackViews\contact_messages\pagination',
                'instAdmiGallery'   => 'App\Views\SchoolBackViews\gallery\pagination',
	];

	/*
	|--------------------------------------------------------------------------
	| Items Per Page
	|--------------------------------------------------------------------------
	|
	| The default number of results shown in a single page.
	|
	*/
	public $perPage = 20;
}
