<?php
class ModelExtensionModuleProductRequest extends Model {
  public function getProduct($product_id) {
		$query = $this->db->query("SELECT pd.name AS name FROM " . DB_PREFIX . "product_description pd LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (pd.product_id = p2s.product_id) WHERE pd.product_id = '" . (int)$product_id . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

    return $query->row;
  }

  public function getStatus($status_name) {
    $query = $this->db->query("SELECT stock_status_id FROM " . DB_PREFIX . "stock_status WHERE name = '" . $status_name . "'");

    if ($this->config->get('product_request_status') && in_array($query->row['stock_status_id'], $this->config->get('product_request_criteria'))){
      return true;
    }
  }

  public function addNotifier($product_id, $data, $language_code) {
    if ($this->request->server['HTTPS']) {
			$base = HTTPS_SERVER;
		} else {
			$base = HTTP_SERVER;
		}

    $language = new Language($language_code); // obtained at the time of signup
    $language->load($language_code);
    $language->load('extension/module/product_request');

    $pId		    = (int)$product_id;
    $product	  = $this->getProduct($pId);
    $subscriber = $this->db->escape($data['notify']);
    $token		  = bin2hex(openssl_random_pseudo_bytes(12));

    $text  = sprintf($language->get('prod_notify_greeting'),$product['name'])."\n\n";
    $text .= $language->get('prod_notify_body') . "\n";
    $text .= $base .'index.php?route=product/notify&confirm='.$token . "\n\n";
    $text .= $language->get('prod_notify_signoff') . "\n\n";
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

    $mail->setTo($subscriber);
    $mail->setFrom($this->config->get('config_email'));
    $mail->setSender($this->config->get('config_name'));
    $mail->setSubject($product['name'] . $language->get('at_signifier') . $this->config->get('config_name'));
    $mail->setText($text);
    $mail->send();

    $query = $this->db->query("INSERT INTO " . DB_PREFIX . "product_notification SET product_id = '" . $pId . "', email = '" . $this->db->escape($data['notify']) . "', token = '" . $token . "', date = NOW(), language_code = '" . $this->db->escape($language_code) . "'");
  }
}
