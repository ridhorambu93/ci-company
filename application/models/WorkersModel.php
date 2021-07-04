<?php defined('BASEPATH') OR exit('No direct script access allowed');

class WorkersModel extends CI_Model {
	function __construct() {
		parent::__construct();

		$this->load->helper('response');
	}

	public $table = 'workers';
	public $view_table = 'view_workers';

	/**
	 *  getAll method
	 *  get all data
	 */
	public function getAll($data_temp = [])
	{
		$column		= $this->_getColumn($this->view_table);
		$protected	= ['id'];

		$sort				= ['ASC', 'DESC'];
		$clause				= ['order' => 'id', 'sort' => 'ASC', 'limit' => 10, 'page' => 1];
		$error				= [];
		$paging				= [];
		$condition			= [];
		$condition_like		= [];
		$condition_inset	= [];
		$condition_between	= [];

		$column_like = [
			'like_ref_number',
			'like_fullname',
			'like_email',
			'like_phone'
		];

		$column_inset = [
			'inset_language_ability_ids',
			'inset_language_ability_slug',
			'inset_cooking_ability_ids',
			'inset_cooking_ability_slug',
			'inset_skill_experience_ids',
			'inset_skill_experience_slug',
			'inset_work_experience_ids',
			'inset_work_experience_slug',
			'inset_ready_placement_ids',
			'inset_ready_placement_slug'
		];

		$column_date = [
			'create_date',
			'update_date'
		];

		$column_between = [
			'between_age'
		];

		if (!empty($data_temp) && is_array($data_temp)) {
			foreach ($data_temp as $key => $val) {
				if (in_array($key, $protected)) {
					return responseBadRequest();
				} else {
					if (!empty($val)) {
						if (in_array($key, $column_date)) {
							$clause[$key] = DateTime::createFromFormat('Y-m-d', $val);
							$error[$key] = DateTime::getLastErrors();
						} else {
							$clause[$key] = $val;
						}
					} else {
						if (in_array($key, ['is_active']) && $val === '0') {
							$clause[$key] = '\'0\'';
						}
					}
				}
			}
		}

		if ((!in_array($clause['order'], $column) && $clause['order'] !== 'rand()') || !is_numeric($clause['limit']) || !is_numeric($clause['page']) || !in_array(strtoupper($clause['sort']), $sort)) {
			return responseBadRequest();
		}

		if (!empty($error) && is_array($error)) {
			foreach ($error as $key => $val) {
				if ($val['warning_count'] > 0 || $val['val'] > 0) {
					return responseBadRequest('Invalid format column '.$key);
				}
			}
		}

		$this->db->select($column);

		if (!array_key_exists('is_active', $clause)) {
			$condition['is_active'] = 1;
		}

		foreach ($clause as $key => $val) {
			if (!empty($val)) {
				if (in_array($key, $column)) {
					if (in_array($key, $column_date)) {
						$condition[$key] = $val->format('Y-m-d');
					} else {
						$condition[$key] = $val;
					}
				} elseif (in_array($key, $column_like) && in_array(substr($key, strlen('like_')), $column)) {
					$condition_like[substr($key, strlen('like_'))] = $val;
				} elseif (in_array($key, $column_inset) && in_array(substr($key, strlen('inset_')), $column)) {
					$condition_inset[substr($key, strlen('inset_'))] = $val;
				} elseif (in_array($key, $column_between) && in_array(substr($key, strlen('between_')), $column)) {
					$condition_between[substr($key, strlen('between_'))] = $val;
				} elseif ($key == 'not_id' && is_numeric($val)) {
					$condition['id !='] = $val;
				}
			}
		}

		if (!empty($condition) && is_array($condition)) {
			$this->db->where($condition);
		}

		if (!empty($condition_like) && is_array($condition_like)) {
			$this->db->like($condition_like);
		}

		if (!empty($condition_inset) && is_array($condition_inset)) {
			foreach ($condition_inset as $key => $val) {
				if (!empty($val) && is_array($val)) {
					$term_inset = [];

					foreach ($val as $val) {
						$term_inset[] = 'FIND_IN_SET(' . $val . ', ' . $key . ')';
					}

					$term_inset = implode(' or ', $term_inset);

					$this->db->where($term_inset);
				} else {
					$this->db->where('FIND_IN_SET(' . $val . ', ' . $key . ')');
				}
			}
		}

		if (!empty($condition_between) && is_array($condition_between)) {
			foreach ($condition_between as $key => $val) {
				if (!empty($val) && is_array($val) && count($val) === 2) {
					if (!empty($val[0]) && !empty($val[1])) {
						$term_between = $key . ' BETWEEN ' . implode(' AND ', $val);

						$this->db->where($term_between);
					}
				}
			}
		}

		$offset = ($clause['limit'] * $clause['page']) - $clause['limit'];

		if (is_numeric($offset) && $offset >= 0) {
			$this->db->limit($clause['limit'], $offset);
		} else {
			$this->db->limit($clause['limit']);
		}

		$query	= $this->db->order_by($clause['order'], strtoupper($clause['sort']))->get($this->view_table);
		$result	= json_decode(json_encode($query->result()), true);
		$total	= $this->_getCount($this->view_table, $condition, $condition_like, $condition_inset, $condition_between);

		if (!empty($clause['limit'])) {
			$page_first		= 1;
			$page_last		= ceil($total / $clause['limit']);
			$page_current	= (int) $clause['page'];
			$page_next		= $page_current + 1;
			$page_previous	= $page_current - 1;

			$paging = [
				'current' => $page_current,
				'next' => ($page_next <= $page_last) ? $page_next : $page_current,
				'previous' => ($page_previous > 0) ? $page_previous : 1,
				'first' => $page_first,
				'last' => ($page_last > 0) ? $page_last : 1,
			];
		}

		return responseSuccess($result, $total, $paging);
	}

	/**
	 *  getDetail method
	 *  get detail data
	 */
	public function getDetail($id = null)
	{
		$column		= $this->_getColumn($this->view_table);
		$protected	= ['id'];

		if (empty($id)) {
			return responseBadRequest('Id is required');
		}

		if (!is_numeric($id)) {
			return responseBadRequest('Id is invalid');
		}

		$check = $this->_getCount($this->view_table, ['id' => $id]);

		if ($check == 0) {
			return responseNotFound();
		}

		$query = $this->db->select($column)->where(['id' => $id])->get($this->view_table);
		$result	= json_decode(json_encode($query->row()), true);

		return responseSuccess($result, $check);
	}

	/**
	 *  getDetailByRefNumber method
	 *  get detail data by ref number
	 */
	public function getDetailByRefNumber($ref_number = null)
	{
		$column		= $this->_getColumn($this->view_table);
		$protected	= ['id'];

		if (empty($ref_number)) {
			return responseBadRequest('Ref Number is required');
		}

		$check = $this->_getCount($this->view_table, ['ref_number' => $ref_number]);

		if ($check == 0) {
			return responseNotFound();
		}

		$query = $this->db->select($column)->where(['ref_number' => $ref_number])->get($this->view_table);
		$result	= json_decode(json_encode($query->row()), true);

		return responseSuccess($result, $check);
	}

	/**
	 *  insert method
	 *  insert new data
	 */
	public function insert($data_temp = [])
	{
		$column		= $this->_getColumn($this->table);
		$protected	= ['id'];
		$data		= [];

		if (!empty($data_temp) && is_array($data_temp)) {
			foreach ($data_temp as $key => $val) {
				if (!in_array($key, $column) || in_array($key, $protected)) {
					return responseBadRequest();
				} else {
					if (!empty($val)) {
						$data[$key] = $val;
					}
				}
			}
		}

		if (empty($data)) {
			return responseBadRequest('Empty data');
		}

		if (array_key_exists('ref_number', $data)) {
			$check = $this->_getCount($this->table, ['ref_number' => $data['ref_number']]);

			if ($check > 0) {
				return responseBadRequest('Ref Number already exist');
			}
		}

		if (array_key_exists('email', $data)) {
			$check = $this->_getCount($this->table, ['email' => $data['email']]);

			if ($check > 0) {
				return responseBadRequest('Email already exist');
			}
		}

		$inserted = $this->db->insert($this->table, $data);

		if ($inserted) {
			return responseSuccess(['id' => $this->db->insert_id()]);
		}

		return responseError();
	}

	/**
	 *  update method
	 *  update existing data by id
	 */
	public function update($data_temp = [], $id = null)
	{
		$column		= $this->_getColumn($this->table);
		$protected	= ['id'];
		$data		= [];

		if (empty($id)) {
			return responseBadRequest('Id is required');
		}

		if (!is_numeric($id)) {
			return responseBadRequest('Id is invalid');
		}

		if (!empty($data_temp) && is_array($data_temp)) {
			foreach ($data_temp as $key => $val) {
				if (!in_array($key, $column) || in_array($key, $protected)) {
					return responseBadRequest();
				} else {
					if (!empty($val)) {
						$data[$key] = $val;
					} else {
						if (in_array($key, ['is_active']) && $val === '0') {
							$data[$key] = '0';
						} elseif (in_array($key, ['placement_id']) && empty($val)) {
							$data[$key] = null;
						}
					}
				}
			}
		}

		if (empty($data)) {
			return responseBadRequest('Empty data');
		}

		$check = $this->_getCount($this->table, ['id' => $id]);

		if ($check == 0) {
			return responseNotFound();
		}

		if (array_key_exists('ref_number', $data)) {
			$check = $this->_getCount($this->table, ['ref_number' => $data['ref_number'], 'id !=' => $id]);

			if ($check > 0) {
				return responseBadRequest('Ref Number already exist');
			}
		}

		if (array_key_exists('email', $data)) {
			$check = $this->_getCount($this->table, ['email' => $data['email'], 'id !=' => $id]);

			if ($check > 0) {
				return responseBadRequest('Email already exist');
			}
		}

		$updated = $this->db->update($this->table, $data, ['id' => $id]);

		if ($updated) {
			return responseSuccess(['id' => $id]);
		}

		return responseError();
	}

	/**
	 *  delete method
	 *  delete existing data by id
	 */
	public function delete($id = null)
	{
		$column		= $this->_getColumn($this->table);
		$protected	= ['id'];

		if (empty($id)) {
			return responseBadRequest('Id is required');
		}

		if (!is_numeric($id)) {
			return responseBadRequest('Id is invalid');
		}

		$check = $this->_getCount($this->table, ['id' => $id]);

		if ($check == 0) {
			return responseNotFound();
		}

		$deleted = $this->db->where(['id' => $id])->delete($this->table);

		if ($deleted) {
			return responseSuccess(['id' => $id]);
		}

		return responseError();
	}

	/**
	 *  private _getColumn method
	 *  return array column
	 */
	private function _getColumn($table)
	{
		$result = $this->db->list_fields($table);

		return $result;
	}

	/**
	 *  private _getCount method
	 *  return interger
	 */
	public function _getCount($table = null, $condition = [], $condition_like = [], $condition_inset = [], $condition_between = [])
	{
		if (!empty($table)) {
			$this->db->from($table);

			if (!empty($condition) && is_array($condition)) {
				$this->db->where($condition);
			}

			if (!empty($condition_like) && is_array($condition_like)) {
				$this->db->like($condition_like);
			}

			if (!empty($condition_inset) && is_array($condition_inset)) {
				foreach ($condition_inset as $key => $val) {
					if (!empty($val) && is_array($val)) {
						$term_inset = [];
	
						foreach ($val as $val) {
							$term_inset[] = 'FIND_IN_SET(' . $val . ', ' . $key . ')';
						}
	
						$term_inset = implode(' or ', $term_inset);
	
						$this->db->where($term_inset);
					} else {
						$this->db->where('FIND_IN_SET(' . $val . ', ' . $key . ')');
					}
				}
			}

			if (!empty($condition_between) && is_array($condition_between)) {
				foreach ($condition_between as $key => $val) {
					if (!empty($val) && is_array($val) && count($val) === 2) {
						if (!empty($val[0]) && !empty($val[1])) {
							$term_between = $key . ' BETWEEN ' . implode(' AND ', $val);
	
							$this->db->where($term_between);
						}
					}
				}
			}

			return $this->db->count_all_results();
		}

		return 0;
	}

	/**
	 *  private _getDatatablesQuery method
	 *  return query
	 */
	private function _getDatatablesQuery() {
		$search	= ['ref_number', 'fullname', 'email'];
		$order	= ['id', 'ref_number', 'fullname', 'email', 'create_date', 'create_by', 'update_date', 'update_by', 'user_id'];

		$this->db->from($this->view_table)->where(['is_active' => 1]);

		$i = 0;

		foreach ($search as $item) {
			if ($_POST['search']['value']) {
				if ($i===0) {
					$this->db->group_start(); 
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($search) - 1 == $i) {
					$this->db->group_end();
				}
			}

			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		}
	}

	/**
	 *  getDatatables method
	 *  get all data for datatables
	 */
	public function getDatatables()
	{
		$this->_getDatatablesQuery();

		if ($_POST['length'] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		$result = $this->db->get()->result();

		return json_decode(json_encode($result), true);
	}

	public function countDatatablesFilter()
	{
		$this->_getDatatablesQuery();
		$result = $this->db->get()->num_rows();

		return $result;
	}
}