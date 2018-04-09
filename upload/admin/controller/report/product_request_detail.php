<?php
class ControllerReportProductRequestDetail extends Controller {
	public function index() {
		$this->load->language('report/product_request');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/product_request', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_customer'),
			'href' => $this->url->link('report/product_request_detail', 'token=' . $this->session->data['token'] . $url, true)
		);

		$this->load->model('report/product_request');
		$this->load->model('catalog/product');

		$data['requests'] = array();

		$filter_data = array(
			'email'	=> $this->request->get['email'],
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$request_total = $this->model_report_product_request->customerDetailTotals($filter_data);
		$results	     = $this->model_report_product_request->customerDetail($filter_data);

		foreach ($results as $result) {

			$product = $this->model_catalog_product->getProduct($result['product_id']);
			$data['requests'][] = array(
				'name' => $product['name'],
				'ip'	 => $result['confirming_ip'],
				'date' => date($this->language->get('date_format_short'), strtotime($result['date'])).' '.date($this->language->get('time_format'), strtotime($result['date']))
			);
		}

		$Pagination        = new Pagination();
		$Pagination->total = $request_total;
		$Pagination->page  = $page;
		$Pagination->limit = $this->config->get('config_limit_admin');
		$Pagination->url   = $this->url->link('report/product_request_detail', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['Pagination'] = $Pagination->render();
		$data['Results']    = sprintf($this->language->get('text_pagination'), ($request_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($request_total - $this->config->get('config_limit_admin'))) ? $request_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $request_total, ceil($request_total / $this->config->get('config_limit_admin')));

		$data['heading_title']         = $this->language->get('heading_title');

		$data['text_list_customer']    = sprintf($this->language->get('text_list_customer'),$this->db->escape($this->request->get['email']));
		$data['text_customers']        = $this->language->get('text_customers');
		$data['text_no_results']       = $this->language->get('text_no_results');
		$data['text_confirm']          = $this->language->get('text_confirm');
		$data['text_unconfirmed']      = $this->language->get('text_unconfirmed');
		$data['text_unconfirmed_info'] = $this->language->get('text_unconfirmed_info');

		$data['column_name']           = $this->language->get('column_name');
		$data['column_email']          = $this->language->get('column_email');
		$data['column_ip']             = $this->language->get('column_ip');
		$data['column_date']           = $this->language->get('column_date');
		$data['column_action']         = $this->language->get('column_action');

		$data['token']                 = $this->session->data['token'];

		$url = '';

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/product_request_detail', $data));
	}
}
