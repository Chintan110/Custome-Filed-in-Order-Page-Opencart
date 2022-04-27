<?php
class ModelLocalisationRegion extends Model {
	public function getRegion($region_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "region WHERE region_id = '" . (int)$region_id . "' AND status = '1'");

		return $query->row;
	}

	public function getRegionByZoneId($zone_id) {
		$region_data = $this->cache->get('region.' . (int)$zone_id);

		if (!$region_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "region WHERE zone_id = '" . (int)$zone_id . "' AND status = '1' ORDER BY name");

			$region_data = $query->rows;

			$this->cache->set('region.' . (int)$zone_id, $region_data);
		}

		return $region_data;
	}
}