<?php
class ModelLocalisationBarangay extends Model {
	public function addBarangay($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "barangay SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', city_id = '" . (int)$data['city_id'] . "', extension_status = '" . $this->db->escape($data['extension_status']) . "', free_shipping_status = '" . $this->db->escape($data['free_shipping_status']) . "', flat_shipping = '" . $this->db->escape($data['flat_shipping']) . "', week = '" . $this->db->escape($data['week']) . "'");
		$this->cache->delete('barangay');
		return $this->db->getLastId();
	}
	public function editBarangay($barangay_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "barangay SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', city_id = '" . (int)$data['city_id'] . "', extension_status = '" . $this->db->escape($data['extension_status']) . "', free_shipping_status = '" . $this->db->escape($data['free_shipping_status']) . "', flat_shipping = '" . $this->db->escape($data['flat_shipping']) . "', week = '" . $this->db->escape($data['week']) . "' WHERE barangay_id = '" . (int)$barangay_id . "'");
		$this->cache->delete('barangay');
	}
	public function deleteBarangay($barangay_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "barangay WHERE barangay_id = '" . (int)$barangay_id . "'");
		$this->cache->delete('barangay');
	}
	public function getBarangay($barangay_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "barangay WHERE barangay_id = '" . (int)$barangay_id . "'");
		return $query->row;
	}
	public function getBarangays($data = array()) {
		$sql = "SELECT *, b.name, c.name AS city, b.code AS code FROM " . DB_PREFIX . "barangay b LEFT JOIN " . DB_PREFIX . "city c ON (b.city_id = c.city_id)";
		$sort_data = array(
			'c.name',
			'b.name',
			'b.code'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.name";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
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
	public function getBarangaysByCountryId($barangay_id) {
		$barangay_data = $this->cache->get('barangay.' . (int)$barangay_id);
		if (!$barangay_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "barangay WHERE barangay_id = '" . (int)$barangay_id . "' AND status = '1' ORDER BY name");
			$barangay_data = $query->rows;
			$this->cache->set('barangay.' . (int)$barangay_id, $barangay_data);
		}
		return $barangay_data;
	}
	public function getTotalBarangays() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "barangay");
		return $query->row['total'];
	}
	public function getTotalBarangaysByCountryId($barangay_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "barangay WHERE barangay_id = '" . (int)$barangay_id . "'");
		return $query->row['total'];
	}
    public function getcity() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city ORDER BY name ASC");
        $country_data = $query->rows;
        return $country_data;
    }
}