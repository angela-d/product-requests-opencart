<?php
class ControllerProductNotify extends Controller {

	public function index() {
		$this->load->language('extension/module/product_request');
		$this->load->model('catalog/product');

		if (isset($this->request->get['confirm'])) {
			$token = $this->db->escape($this->request->get['confirm']);

			$getPid = $this->db->query("SELECT product_id,email FROM ".DB_PREFIX."product_notification WHERE token ='".$token."'");
				if ($getPid->num_rows){
					$pId		      = $getPid->row['product_id'];
					$email		    = $getPid->row['email'];
					$product_info = $this->model_catalog_product->getProduct($pId);

					//record the confirmation
					$this->db->query("UPDATE ".DB_PREFIX."product_notification SET token = NULL, confirmed='1', confirming_ip = '".$this->db->escape($this->request->server['REMOTE_ADDR'])."', date = NOW() WHERE token ='".$token."'");

					//update the activity log
					$jsonData = json_encode(array('product_id' => $pId, 'name' => $email, 'product_name' => $product_info['name']));
					$activity = $this->db->query("INSERT INTO " . DB_PREFIX . "customer_activity SET customer_id = '0', `key` = 'notify', `data` = '" . $this->db->escape($jsonData) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}

		} else {
			$token = '';
		}

		if (isset($pId)){
			$this->document->setTitle($product_info['meta_title'].' '.$this->language->get('confirmation_request_title'));
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_stock']        = $this->language->get('text_stock');
			$data['thanks_confirm']    = $this->language->get('text_thanks_notify');
			$data['thanks_body']       = sprintf($this->language->get('text_thanks_body'),$email,$product_info['name']);
			$data['heading_title']     = $product_info['name'];

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => sprintf($this->language->get('text_confirm_notify'),$product_info['name'])
			);

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($product_info['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
				);
			}


		}else{
			$this->document->setTitle($this->language->get('confirmation_error'));
			$data['thanks_confirm'] = $this->language->get('confirmation_not_found');
			$data['thanks_body']    = $this->language->get('confirmation_exist');
			$data['heading_title']  = '';

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('confirmation_request_title')
			);

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
		}

			$data['column_left']    = $this->load->controller('common/column_left');
			$data['column_right']   = $this->load->controller('common/column_right');
			$data['content_top']    = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer']         = $this->load->controller('common/footer');
			$data['header']         = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('product/notify', $data));

		}
	}
