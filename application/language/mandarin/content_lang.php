<?php defined('BASEPATH') OR exit('No direct script access allowed');

$lang['front'] = [
	'topbar' => [
		'login'		=> '登录',
		'register'	=> '登记',
		'logout'	=> '登出'
	],
	'navbar' => [
		'home'		=> '家',
		'worker'	=> '雇员',
		'gallery'	=> '画廊',
		'dropdown'	=> '落下',
		'about'		=> '关于我们',
		'contact'	=> '联系我们'
	],
	'footer' => [
		'company' => [
			'desc'	=> ' 是一家招聘和安置公司，专门从事国外熟练的印度尼西亚劳动力.'
		],
		'link' => [
			'title'		=> '有用的链接',
			// 'home'		=> '家',
			// 'worker'	=> '雇员',
			// 'gallery'	=> '画廊',
			// 'dropdown'	=> '落下',
			// 'about'		=> '关于我们',
			// 'contact'	=> '联系我们'
		],
		'contact' => [
			'title'		=> '联系我们',
			'phone'		=> '电话',
			'email'		=> '电子邮件'
		],
	],
	'section_title' => [
		'advantages'	=> '我们的优势',
		'clients'		=> '我们的客户',
		'news'			=> '消息'
	],
	'page_contact' => [
		'info' => [
			'title'		=> '联络资料',
			'location'	=> '地点',
			'email'		=> '电子邮件',
			'phone'		=> '电话'
		],
		'message' => [
			'title'		=> '保持联系',
			'name'		=> '你的名字',
			'email'		=> '你的邮件',
			'subject'	=> '主题',
			'message'	=> '信息',
			'send'		=> '发信息'
		],
	],
	'page_login' => [
		'intro'		=> '登錄以開始您的會議',
		'username'	=> '用戶名或電子郵件',
		'password'	=> '密碼',
		'submit'	=> '登錄'
	],
	'page_register' => [
		'intro'				=> '註冊新會員',
		'fullname'			=> '全名',
		'email'				=> '電子郵件',
		'register_as'		=> '註冊為',
		'agency_location'	=> '代理商位置',
		'company'			=> '公司',
		'submit'			=> '登記'
	],
	'page_worker' => [
		'filter'		=> '篩選',
		'result'		=> '結果',
		'no_result'		=> '找不到結果',
		'attachment'	=> '附件',
		'profile'		=> '檔案信息',
		'contact'		=> '聯繫信息',
		'others'		=> '其他',
		'worker_data'	=> [
			'nik'					=> '身份證號碼',
			'fullname'				=> '全名',
			'gender'				=> '性別',
			'birth_place'			=> '出生地',
			'birth_date'			=> '出生日期',
			'age'					=> '年齡',
			'marital_status'		=> '婚姻狀況',
			'religion'				=> '宗教',
			'email'					=> '電子郵件',
			'phone'					=> '电话',
			'address'				=> '地址',
			'description'			=> '描述',
			'placement'				=> '現在放置',
			'experience'			=> '經驗',
			'oversea_experience'	=> '海外經驗',
			'ready_placement'		=> '準備放置'
		],
		'button'		=> [
			'search'		=> '搜索',
			'view_detail'	=> '查看詳細',
			'view_avatar'	=> '放大照片',
			'download_data'	=> '生物數據',
			'play_video'	=> '視頻'
		],
	],
	'button' => [
		'readmore'		=> '阅读更多',
		'contact'		=> '联系我们',
		'send_message'	=> '发信息'
	],
];

$lang['back'] = [];

$lang['message'] = [
	'error' => [
		'default'	=> '發生錯誤, 請重試',
		'auth'		=> '請先登錄',
		'login'		=> '必須輸入驗證碼',
		'captcha'	=> '無效的登錄憑證'
	],
	'success' => [
		'default'		=> '',
		'register'		=> '我们目前正在处理您的请求，请等待通过电子邮件进行的确认',
		'verification'	=> '驗證已發送, 請檢查您的電子郵件'
	],
];
