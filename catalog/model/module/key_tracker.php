<?php
class ModelModuleKeyTracker extends Model {
    public function log($payload = array()) {
        if (!$this->config->get('key_tracker_status')) {
            return false;
        }

        $data = is_array($payload) ? $payload : array();

        $key_hash = isset($data['key_hash']) ? trim((string)$data['key_hash']) : '';
        $provider = isset($data['provider']) ? trim((string)$data['provider']) : '';

        if ($key_hash === '') {
            return array('ok' => 0, 'error' => 'key_hash is required');
        }

        if (!preg_match('/^[a-f0-9]{32}$/', $key_hash)) {
            return array('ok' => 0, 'error' => 'key_hash must be a 32-character lowercase hex string');
        }

        if ($provider === '') {
            return array('ok' => 0, 'error' => 'provider is required');
        }

        $subsystem = isset($data['subsystem']) ? strtolower(trim((string)$data['subsystem'])) : '';
        if ($subsystem === '' || strlen($subsystem) > 50) {
            return false;
        }

        $device_id = isset($data['device_id']) ? trim((string)$data['device_id']) : '';
        if ($subsystem !== 'app' && $subsystem !== 'pos') {
            $device_id = '';
        }

        $event_time = gmdate('Y-m-d H:i:s');

        $this->log->write('key_tracker.log.request subsystem=' . $subsystem . ' provider=' . $provider);

        $sql = "INSERT INTO `" . DB_PREFIX . "etmax_key_log` SET ";
        $sql .= "`key_hash` = '" . $this->db->escape($key_hash) . "', ";
        $sql .= "`subsystem` = '" . $this->db->escape($subsystem) . "', ";
        $sql .= "`event_time` = '" . $this->db->escape($event_time) . "', ";
        $sql .= "`provider` = '" . $this->db->escape($provider) . "', ";

        if ($device_id === '') {
            $sql .= "`device_id` = NULL";
        } else {
            $sql .= "`device_id` = '" . $this->db->escape($device_id) . "'";
        }

        $this->db->query($sql);
        $event_id = (int)$this->db->getLastId();

        if ($event_id < 1) {
            $this->log->write('key_tracker.log.failed subsystem=' . $subsystem . ' provider=' . $provider);
            return array('ok' => 0, 'error' => 'failed to write key log event');
        }

        $this->log->write('key_tracker.log.success event_id=' . $event_id);
        return array('ok' => 1, 'event_id' => $event_id);
    }
}
?>
