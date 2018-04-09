<?php
class ModelReportProductRequest extends Model {
	public function purgeCheck() {
		$this->db->query("DELETE FROM ".DB_PREFIX."product_notification WHERE confirmed = '0' && date < DATE_SUB(NOW(),INTERVAL 7 DAY)");
	}

	public function checkRequests($product_id){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_notification WHERE product_id = '" . (int)$product_id . "' && confirmed ='1'");

		return $query->row['total'];
	}

	public function getRequests($data = array()) {
		$sql = "SELECT id,product_id,email,date, count(product_id) AS totalReq FROM `" . DB_PREFIX . "product_notification` WHERE confirmed ='1'";

		$sql .= " GROUP BY product_id ORDER BY totalReq DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalRequests($data = array()) {
		$query = $this->db->query("SELECT COUNT(DISTINCT product_id) AS total FROM `" . DB_PREFIX . "product_notification` WHERE confirmed ='1'");

		return $query->row['total'];
	}

	public function getTotalCustRequests($data = array()) {
		$query = $this->db->query("SELECT COUNT(DISTINCT email) AS total FROM `" . DB_PREFIX . "product_notification` WHERE confirmed ='1'");

		return $query->row['total'];
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT id,product_id,email,date, count(email) AS totalReq FROM `" . DB_PREFIX . "product_notification` WHERE confirmed ='1'";

		$sql .= " GROUP BY email ORDER BY totalReq DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function customerDetailTotals($data = array()) {
		$sql = "SELECT COUNT(product_id) AS total FROM `" . DB_PREFIX . "product_notification` WHERE email ='".$this->db->escape($data['email'])."'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function customerDetail($data = array()) {
		$sql = "SELECT product_id,email,confirming_ip,date FROM `" . DB_PREFIX . "product_notification` WHERE email='".$this->db->escape($data['email'])."'";

		$sql .= " ORDER BY date DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function unconfirmed() {
		$query = $this->db->query("SELECT COUNT(email) AS total FROM `" . DB_PREFIX . "product_notification` WHERE confirmed ='0'");

		return $query->row['total'];
	}

	public function sendNotifications($product_id) {
		if ($this->request->server['HTTPS']) {
			$base = HTTPS_SERVER;
		} else {
			$base = HTTP_SERVER;
		}

		$pId				= (int)$product_id;
		$pQuery			= 'product_id='.$pId;
		$product		= $this->getProduct($pId);
		$url_alias	= $this->db->query("SELECT `keyword` FROM ".DB_PREFIX."url_alias WHERE `query` = '".$pQuery."'");
		$customers	= $this->db->query("SELECT email,confirming_ip,date,language_code FROM ".DB_PREFIX."product_notification WHERE product_id = '".$pId."' && confirmed ='1'");

		foreach($customers->rows as $cust){
			$language = new Language($cust['language_code']); // obtained at the time of signup
			$language->load($cust['language_code']);
			$language->load('extension/module/product_request');

			$text  = sprintf($language->get('instock_notification_greeting'),$product['name'])."\n\n";
			$text .= $language->get('instock_notification_body') . "\n";

			// seo friendly or stock urls?
			if ($this->config->get('config_seo_url')){
				$text .= $base . $url_alias->row['keyword']. "\n\n";
			}	else{
				$text .= $base . 'index.php?route=product/product&product_id='.$pId;
			}

			$text .= sprintf($language->get('instock_notification_signoff'),$cust['confirming_ip'],date($this->language->get('date_format_long'), strtotime($cust['date']))) . "\n\n";
			$text .= $language->get('prod_notify_signature') . "\n\n";
			$text .= $this->config->get('config_owner') . "\n";
			$text .= $base . "\n";

			$mail = new Mail();
			$mail->protocol      = $this->config->get('config_mail_protocol');
			$mail->parameter     = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($cust['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject($product['name'] . $language->get('at_signifier') . $this->config->get('config_name'));
			$mail->setText($text);
			$mail->send();
			$this->log->write($cust['email']);
		}

		// remove the registered request, so we don't send them another reminder in the future
		$query = $this->db->query("DELETE FROM " . DB_PREFIX . "product_notification WHERE product_id = '".$pId."' && confirmed ='1'");
	}
}
