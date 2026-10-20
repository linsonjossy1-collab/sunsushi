<?php

class ModelDrivermanagementPettycash extends Model {

    public function ClockinPettycash($driver_id, $time_in, $time_out) {
        if ($time_out != 0) {
            $sql = ("SELECT  c.*,t.transaction_category FROM " . DB_PREFIX . "driver_petty_cash c LEFT JOIN " . DB_PREFIX . "driver_pettycash_transaction_type t ON t.transction_index=c.dpc_trans_type WHERE driver_id = '" . (int) $driver_id . "' AND (('" . $time_in . "'<= dpc_time) && (dpc_time<='" . $time_out . "'))");
        } else {
            $sql = ("SELECT  c.*,t.transaction_category FROM " . DB_PREFIX . "driver_petty_cash c LEFT JOIN " . DB_PREFIX . "driver_pettycash_transaction_type t ON t.transction_index=c.dpc_trans_type WHERE driver_id = '" . (int) $driver_id . "' AND ('" . $time_in . "'< dpc_time)");
        }
        $qry = $this->db->query($sql);
        return $qry->rows;
    }

    public function TotalClockinPettycash($driver_id, $time_in, $time_out) {
        if ($time_out != 0) {
            $qry = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND (('" . $time_in . "'<= dpc_time) && (dpc_time<='" . $time_out . "') AND dpc_trans_type!='R')");
        } else {
            $qry = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND ('" . $time_in . "'< dpc_time) AND dpc_trans_type!='R'");
        }
        return $qry->rows;
    }

    public function TodayClockin($driver_id) {
        $sql = ("SELECT driver_clock_in, driver_clock_out FROM " . DB_PREFIX . "driver_clock_inout dc  WHERE ((dc.driver_clock_in >= CURDATE()) AND dc.driver_id='" . $driver_id . "')");
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function LastClockin($driver_id) {
        $sql =("SELECT driver_clock_in,driver_clock_out FROM " . DB_PREFIX . "driver_clock_inout WHERE driver_id='" . (int) $driver_id . "' ORDER BY driver_clock_id DESC LIMIT 1");
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function addPettycash($driver_id, $trans_type, $pettycash_amount) {
        if ($trans_type == 'N') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time = NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
        } else {
            $type = $this->db->query("SELECT transaction_type FROM " . DB_PREFIX . "driver_pettycash_transaction_type WHERE transction_index='" . $trans_type . "'");
            $cash_type = $type->row;
            if ($cash_type['transaction_type'] == 'credit') {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time = NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "'");
            } else {                
                $time_in = $this->currentlogintime($driver_id);
                $blnc = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND dpc_time>= '" . $time_in['driver_clock_in'] . "' AND dpc_trans_type!='R'");
                $balance=$blnc->row;
                $cash_recvd = $this->db->query("SELECT  SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND dpc_time>= '" . $time_in['driver_clock_in'] . "' AND dpc_trans_type ='R'");
                $cash_received = $cash_recvd->row;
                $blnce = ($balance['credit']-$balance['debit']-$pettycash_amount);
                if($blnce < $cash_received['debit']){
                    $cash = $pettycash_amount-$cash_received['debit'];
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time = NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_dr = '" . $this->db->escape($cash) . "'");
                    $this->db->query("UPDATE " . DB_PREFIX . "driver_petty_cash SET dpc_trans_type = 'D' WHERE dpc_trans_type = 'R' AND driver_id = '" . (int) $driver_id . "' AND dpc_time>= '" . $time_in['driver_clock_in'] . "'");
                } else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time = NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
                }
            }
        }
        return $this->db->getLastId();
    }

    public function getTransactionCategories() {
        $transaction = $this->db->query("SELECT transaction_category,transction_index FROM " . DB_PREFIX . "driver_pettycash_transaction_type WHERE transction_index!='O' AND transction_index!='T' AND transction_index!='B' AND transction_index!='F'");
        return $transaction->rows;
    }

    public function currentlogintime($driver_id) {
        $timein = $this->db->query("SELECT driver_clock_in FROM " . DB_PREFIX . "driver_clock_inout  dc  WHERE dc.driver_id='" . $driver_id . "' AND  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL");
        return $timein->row;
    }

    public function getDriverPettycashDetails($driver_id) {
        $time_in = $this->currentlogintime($driver_id);
        $qry = $this->db->query("SELECT  c.*,t.transaction_category FROM " . DB_PREFIX . "driver_petty_cash c LEFT JOIN " . DB_PREFIX . "driver_pettycash_transaction_type t ON t.transction_index=c.dpc_trans_type WHERE driver_id = '" . (int) $driver_id . "' AND dpc_time>= '" . $time_in['driver_clock_in'] . "'");
        return $qry->rows;
    }

    public function getDriverPettycashTotals($driver_id) {
        $time_in = $this->currentlogintime($driver_id);
        $qry = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND dpc_time>= '" . $time_in['driver_clock_in'] . "' AND dpc_trans_type!='R'");
        return $qry->rows;
    }

    public function getDrivers() {
        $sql = "SELECT driver_id,driver_name,driver_mobile FROM " . DB_PREFIX . "driver r ORDER BY r.driver_name ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getclockedindrivers() {
        $sql = ("SELECT dc.driver_id, d.driver_name FROM " . DB_PREFIX . "driver_clock_inout dc LEFT JOIN " . DB_PREFIX . "driver d ON (d.driver_id=dc.driver_id) WHERE (dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL)");
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function confirmPettycash($dpc_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "driver_petty_cash SET dpc_trans_type = 'D' WHERE dpc_id = '" . (int) $dpc_id . "'");
    }

}
