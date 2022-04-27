<?php
class ModelLocalisationBarangay extends Model {
	public function getBarangay($barangay_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "barangay WHERE barangay_id = '" . (int)$barangay_id . "' AND status = '1'");

		return $query->row;
	}

	public function getBarangayByCityId($city_id) {
		$barangay_data = $this->cache->get('barangay.' . (int)$city_id);

		if (!$barangay_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "barangay WHERE city_id = '" . (int)$city_id . "' AND status = '1' ORDER BY name");

			$barangay_data = $query->rows;

			$this->cache->set('barangay.' . (int)$city_id, $barangay_data);
		}

		return $barangay_data;
	}
}