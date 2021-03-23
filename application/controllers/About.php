<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$this->template->set_template('layouts/front');

		if (!$this->session->has_userdata('site_lang')) {
			$this->session->set_userdata('site_lang', 'english');
		}
	}

	public function index()
	{
		$this->template->title = 'About Us';


		$this->template->content->view('templates/front/about');
		$this->template->publish();
	}
}
