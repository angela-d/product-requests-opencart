<?php
class ControllerExtensionModuleProductRequest extends Controller {
	private $error = array();

  public function install() {
		$this->db->query("CREATE TABLE `" . DB_PREFIX . "product_notification` (
			`id` int(11) AUTO_INCREMENT PRIMARY KEY,
		  `product_id` int(11),
		  `email` varchar(200) NOT NULL,
		  `token` varchar(255) DEFAULT NULL,
		  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
		  `confirming_ip` varchar(40) NOT NULL,
		  `date` datetime NOT NULL,
			`language_code` varchar(11) NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;

			ALTER TABLE `" . DB_PREFIX . "product_notification`
			  ADD KEY `pid` (`product_id`);
		");

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/' . $this->request->get['extension']);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/' . $this->request->get['extension']);

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'report/product_request');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'report/product_request');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'report/product_request_detail');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'report/product_request_detail');
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_notification`;");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'product_request';");
	}

	public function index() {
		$this->load->language('extension/module/product_request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('product_request', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title']                = $this->language->get('heading_title');

		$data['text_edit']                    = $this->language->get('text_edit');
		$data['text_enabled']                 = $this->language->get('text_enabled');
		$data['text_disabled']                = $this->language->get('text_disabled');

		$data['entry_name']                   = $this->language->get('entry_name');
		$data['entry_product_requests']       = $this->language->get('entry_product_requests');
		$data['entry_status']                 = $this->language->get('entry_status');
		$data['help_product_request_status']  = $this->language->get('help_product_request_status');
		$data['entry_product_request_status'] = $this->language->get('entry_product_request_status');

		$data['button_save']                  = $this->language->get('button_save');
		$data['button_cancel']                = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/product_request', 'token=' . $this->session->data['token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/product_request', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		$data['action'] = $this->url->link('extension/module/product_request', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		$this->load->model('localisation/stock_status');

		$data['product_request_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['product_request_criteria'])) {
			$data['product_request_criteria'] = $this->request->post['product_request_criteria'];
		} elseif ($this->config->get('product_request_criteria')) {
			$data['product_request_criteria'] = $this->config->get('product_request_criteria');
		} else {
			$data['product_request_criteria'] = array();
		}

		if (isset($this->request->post['product_request_status'])) {
			$data['product_request_status'] = $this->request->post['product_request_status'];
		} elseif (!empty($this->config->get('product_request_status'))) {
			$data['product_request_status'] = $this->config->get('product_request_status');
		} else {
			$data['product_request_status'] = '';
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/product_request', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/product_request')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
