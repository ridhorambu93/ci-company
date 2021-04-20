<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Placements extends CI_Controller {
	function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Jakarta');
		setReferrer(current_url());

		if (!$this->session->has_userdata('AuthUser')) {
			$auth_error = 'Please login first';

			switch (sitelang()) {
				case 'indonesian':
					$auth_error = 'Silahkan masuk terlebih dahulu';
					break;
				case 'japanese':
					$auth_error = '最初にログインしてください';
					break;
				case 'korean':
					$auth_error = '먼저 로그인하시기 바랍니다';
					break;
				case 'mandarin':
					$auth_error = '請先登錄';
					break;
			}

			setFlashError($auth_error, 'auth');
			redirect('auth');
		}

		if ($this->session->userdata('AuthUser')['user_level_id'] != 1) {
			hasReferrer() == true ? redirect(Referrer(), 'refresh') : redirect(base_url(), 'refresh');
		}
		
		$this->template->set_template('layouts/back');
		$this->template->title = 'Placements';

		$this->load->library('user_agent');

		$this->load->model('PlacementsModel');
	}

	private $upload_errors = [];
	private $result = [];

	/**
	 *  index method
	 *  index page
	 */
	public function index()
	{
		$session	= $this->session->userdata('AuthUser');
		$params		= $this->input->get();
		$clause		= [];
		$total		= 0;

		$clause = [
			'limit'		=> 10,
			'page'		=> (array_key_exists('page', $params) && is_numeric($params['page'])) ? $params['page'] : 1,
			'like_name'	=> array_key_exists('name', $params) ? $params['name'] : '',
			'is_local'	=> array_key_exists('is_local', $params) ? $params['is_local'] : '',
			'order'		=> 'name',
			'sort'		=> 'asc'
		];

		$request = [
			'placements' => $this->PlacementsModel->getAll($clause)
		];

		foreach ($request as $key => $val) {
			$this->result[$key] = [];

			if (is_array($request[$key]) && array_key_exists('status', $request[$key])) {
				if ($request[$key]['status'] == 'success') {
					$this->result[$key] = $val['data'];

					if ($key == 'placements') {
						$total = $val['total_data'];
					}
				}
			}
		}

		$this->result['pagination'] = bs4pagination('admin/placements', $total, $clause['limit']);
		$this->result['no'] = (($clause['page'] * $clause['limit']) - $clause['limit']) + 1;
		
		$this->template->content->view('templates/back/Placements/index', $this->result);
		$this->template->publish();
	}

	/**
	 *  detail method
	 *  detail data, return json
	 */
	public function detail($id)
	{
		$session = $this->session->userdata('AuthUser');

		$this->result = [
			'status' => 'error',
			'message' => 'An error occurred, please try again.'
		];

		if ($this->input->is_ajax_request()) {
			if (empty($id) && !is_numeric($id)) {
				echo json_encode($this->result); exit();
			}

			$request = $this->PlacementsModel->getDetail($id);

			if ($request['status'] == 'success') {
				$this->result['status'] = 'success';
				$this->result['data'] = $request['data'];
				unset($this->result['message']);
			}

			echo json_encode($this->result); exit();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 *  create method
	 *  create data, return json
	 */
	public function create()
	{
		$session = $this->session->userdata('AuthUser');

		$this->result = [
			'status' => 'error',
			'message' => 'An error occurred, please try again.'
		];

		if ($this->input->is_ajax_request()) {
			$input = array_map('trim', $this->input->post());
			$file = true;

			if (!array_key_exists('is_local', $input)) {
				$input['is_local'] = '0';
			}

			$validate = $this->validate($file);

			$this->form_validation->set_rules($validate);
			$this->form_validation->set_error_delimiters('','');

			if ($this->form_validation->run() == false) {
				foreach ($input as $key => $val) {
					$this->result['error'][$key] = form_error($key);
				}

				echo json_encode($this->result); exit();
			}

			$data = [
				'name'				=> ucwords($input['name']),
				'is_local'			=> $input['is_local'],
				'create_user_id'	=> $session['id']
			];

			$data = array_map('strClean', $data);

			$request = $this->PlacementsModel->insert($data);

			if ($request['status'] == 'success') {
				$this->result['status'] = 'success';
				unset($this->result['message']);
				setFlashSuccess('Data successfully created.');
			}

			echo json_encode($this->result); exit();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 *  update method
	 *  update data, return json
	 */
	public function update($id)
	{
		$session = $this->session->userdata('AuthUser');

		$this->result = [
			'status' => 'error',
			'message' => 'An error occurred, please try again.'
		];

		if ($this->input->is_ajax_request()) {
			if (empty($id) && !is_numeric($id)) {
				echo json_encode($this->result); exit();
			}

			$input = array_map('trim', $this->input->post());
			$file = true;

			if (!array_key_exists('is_local', $input)) {
				$input['is_local'] = '0';
			}

			$validate = $this->validate($file);

			$this->form_validation->set_rules($validate);
			$this->form_validation->set_error_delimiters('','');

			if ($this->form_validation->run() == false) {
				foreach ($input as $key => $val) {
					$this->result['error'][$key] = form_error($key);
				}

				echo json_encode($this->result); exit();
			}

			$data = [
				'name'				=> ucwords($input['name']),
				'is_local'			=> $input['is_local'],
				'update_user_id'	=> $session['id']
			];

			$data = array_map('strClean', $data);

			$request = $this->PlacementsModel->update($data, $id);

			if ($request['status'] == 'success') {
				$this->result['status'] = 'success';
				unset($this->result['message']);
				setFlashSuccess('Data successfully updated.');
			}

			echo json_encode($this->result); exit();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 *  delete method
	 *  delete data, return json
	 */
	public function delete($id = null)
	{
		$session = $this->session->userdata('AuthUser');

		$this->result = [
			'status' => 'error',
			'message' => 'An error occurred, please try again.'
		];

		if ($this->input->is_ajax_request()) {
			if (empty($id) && !is_numeric($id)) {
				echo json_encode($this->result); exit();
			}

			$request = $this->PlacementsModel->delete($id);

			if ($request['status'] == 'success') {
				$this->result['status'] = 'success';
				unset($this->result['message']);
				setFlashSuccess('Data successfully deleted.');
			}

			echo json_encode($this->result); exit();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 *  validate method
	 *  validate data before action
	 */
	private function validate($file = false, $password = false, $id = 0)
	{
		$validate = [
			[
				'field' => 'name',
				'label' => 'name',
				'rules' => 'trim|required|max_length[100]|regexAlphaSpace|xss_clean'
			],
			[
				'field' => 'is_local',
				'label' => 'Is Local',
				'rules' => 'trim|max_length[1]|numeric|xss_clean'
			],
			
		];

		if ($file) {
			$validate[] = [
				'field' => 'picture',
				'label' => 'Picture',
				'rules' => 'trim|callback__errorFile|xss_clean'
			];
		}

		return $validate;
	}

	/**
	 *  _errorFile method
	 *  display file upload error
	 */
	public function _errorFile($str)
	{
		if (isset($this->upload_errors['file'])) {
			$this->form_validation->set_message('_errorFile', $this->upload_errors['file']);
			return false;
		}

		return true;
	}
}
