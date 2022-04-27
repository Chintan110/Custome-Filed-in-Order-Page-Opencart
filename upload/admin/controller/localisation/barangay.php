<?php
class ControllerLocalisationBarangay extends Controller {
	private $error = array();
	public function index() {
        $this->load->language('localisation/barangay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('localisation/barangay');
		$this->getList();
    }
    public function add() {
		$this->load->language('localisation/barangay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('localisation/barangay');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {         
			foreach ($this->request->post['week'] as $key => $value) {
				$week .= $value . ', ';
			}
			$this->request->post['week'] = $week;
			$this->model_localisation_barangay->addBarangay($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}
	public function edit() {
		$this->load->language('localisation/barangay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('localisation/barangay');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			foreach ($this->request->post['week'] as $key => $value) {
				$week .= $value . ', ';
			}
			$this->request->post['week'] = $week;
			$this->model_localisation_barangay->editBarangay($this->request->get['barangay_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}
	public function delete() {
		$this->load->language('localisation/barangay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('localisation/barangay');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $barangay_id) {
				$this->model_localisation_barangay->deleteBarangay($barangay_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
	}
	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'c.name';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		$data['add'] = $this->url->link('localisation/barangay/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('localisation/barangay/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['barangays'] = array();
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$barangay_total = $this->model_localisation_barangay->getTotalBarangays();
		$results = $this->model_localisation_barangay->getBarangays($filter_data);
		foreach ($results as $result) {
			if($result['extension_status'] == 1)
				$extension_status = 'Enabled';
			else
				$extension_status = 'Disabled';
			$data['barangays'][] = array(
				'barangay_id' => $result['barangay_id'],
				'city'  => $result['city'],
				'name'    => $result['name'] . (($result['barangay_id'] == $this->config->get('config_barangay_id')) ? $this->language->get('text_default') : null),
				'code'    => $result['code'],
				'extension_status'    => $extension_status,
				'edit'    => $this->url->link('localisation/barangay/edit', 'user_token=' . $this->session->data['user_token'] . '&barangay_id=' . $result['barangay_id'] . $url, true)
			);
		}
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$data['sort_country'] = $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
		$data['sort_name'] = $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . '&sort=z.name' . $url, true);
		$data['sort_code'] = $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . '&sort=z.code' . $url, true);
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		$pagination = new Pagination();
		$pagination->total = $barangay_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($barangay_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($barangay_total - $this->config->get('config_limit_admin'))) ? $barangay_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $barangay_total, ceil($barangay_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('localisation/barangay_list', $data));
	}
	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['barangay_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		if (!isset($this->request->get['barangay_id'])) {
			$data['action'] = $this->url->link('localisation/barangay/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('localisation/barangay/edit', 'user_token=' . $this->session->data['user_token'] . '&barangay_id=' . $this->request->get['barangay_id'] . $url, true);
		}
		$data['cancel'] = $this->url->link('localisation/barangay', 'user_token=' . $this->session->data['user_token'] . $url, true);
		if (isset($this->request->get['barangay_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$barangay_info = $this->model_localisation_barangay->getBarangay($this->request->get['barangay_id']);
		}
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($barangay_info)) {
			$data['status'] = $barangay_info['status'];
		} else {
			$data['status'] = '1';
		}
		if (isset($this->request->post['extension_status'])) {
			$data['extension_status'] = $this->request->post['extension_status'];
		} elseif (!empty($barangay_info)) {
			$data['extension_status'] = $barangay_info['extension_status'];
		} else {
			$data['extension_status'] = '0';
		}
		if (isset($this->request->post['free_shipping_status'])) {
			$data['free_shipping_status'] = $this->request->post['free_shipping_status'];
		} elseif (!empty($barangay_info)) {
			$data['free_shipping_status'] = $barangay_info['free_shipping_status'];
		} else {
			$data['free_shipping_status'] = '0';
		}
		if (isset($this->request->post['flat_shipping'])) {
			$data['flat_shipping'] = $this->request->post['flat_shipping'];
		} elseif (!empty($barangay_info)) {
			$data['flat_shipping'] = $barangay_info['flat_shipping'];
		} else {
			$data['flat_shipping'] = '';
		}
		if (isset($this->request->post['week'])) {
			$data['week'] = $this->request->post['week'];
		} elseif (!empty($barangay_info)) {
			$data['week'] = array_filter(explode(', ', $barangay_info['week']));
		} else {
			$data['week'] = '';
		}
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($barangay_info)) {
			$data['name'] = $barangay_info['name'];
		} else {
			$data['name'] = '';
		}
		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} elseif (!empty($barangay_info)) {
			$data['code'] = $barangay_info['code'];
		} else {
			$data['code'] = '';
		}
		if (isset($this->request->post['city_id'])) {
			$data['city_id'] = $this->request->post['city_id'];
		} elseif (!empty($barangay_info)) {
			$data['city_id'] = $barangay_info['city_id'];
		} else {
			$data['city_id'] = 0;
		}
		$data['citys'] = $this->model_localisation_barangay->getcity();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('localisation/barangay_form', $data));
	}
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/zone')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		return !$this->error;
	}
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/zone')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		$this->load->model('setting/store');
		$this->load->model('customer/customer');
		$this->load->model('localisation/geo_zone');
		foreach ($this->request->post['selected'] as $city_id) {
			if ($this->config->get('config_region_id') == $region_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}
			$store_total = $this->model_setting_store->getTotalStoresByZoneId($region_id);
			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
			$address_total = $this->model_customer_customer->getTotalAddressesByZoneId($region_id);
			if ($address_total) {
				$this->error['warning'] = sprintf($this->language->get('error_address'), $address_total);
			}
			$zone_to_geo_zone_total = $this->model_localisation_geo_zone->getTotalZoneToGeoZoneByZoneId($region_id);
			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf($this->language->get('error_zone_to_geo_zone'), $zone_to_geo_zone_total);
			}
		}
		return !$this->error;
	}
}
