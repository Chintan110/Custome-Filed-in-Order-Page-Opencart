<?php
class ModelLocalisationCity extends Model {
	public function addCity($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "city SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', zone_id = '" . (int)$data['zone_id'] . "'");

		$this->cache->delete('city');
		
		return $this->db->getLastId();
	}

	public function editCity($city_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "city SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE city_id = '" . (int)$city_id . "'");

		$this->cache->delete('city');
	}

	public function deleteCity($city_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

		$this->cache->delete('city');
	}

	public function getCity($city_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

		return $query->row;
	}

	public function getCitys($data = array()) {
		$sql = "SELECT *, c.name, z.name AS zone, c.code AS code FROM " . DB_PREFIX . "city c LEFT JOIN " . DB_PREFIX . "zone z ON (c.zone_id = z.zone_id)";

		$sort_data = array(
			'z.name',
			'c.name',
			'c.code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.name";
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

	public function getCitysByCountryId($city_id) {
		$city_data = $this->cache->get('city.' . (int)$city_id);

		if (!$city_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "' AND status = '1' ORDER BY name");

			$city_data = $query->rows;

			$this->cache->set('city.' . (int)$city_id, $city_data);
		}

		return $city_data;
	}

	public function getTotalCitys() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city");

		return $query->row['total'];
	}

	public function getTotalCitysByCountryId($city_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

		return $query->row['total'];
	}

    public function getRegion() {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone ORDER BY name ASC");

        $country_data = $query->rows;
        return $country_data;
    }
}