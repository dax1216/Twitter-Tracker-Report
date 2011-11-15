<?php

    class Report_model extends CI_Model {

        public function __construct() {
            parent::__construct();
        }

        public function insert_report($data) {
            $this->db->insert('reports', $data);

            return $this->db->insert_id();
        }

        public function insert_account($data) {
            $this->db->insert('accounts', $data);
        }

        public function get_reports() {
            $result = $this->db->get('reports');

            return $result->result_array();
        }

        public function get_report($report_id) {
            $this->db->where('id', $report_id);
            $result = $this->db->get('reports');

            return $result->row_array();
        }

        public function get_incomplete_reports() {
            $this->db->where('status', 0);
            $this->db->order_by('date', 'asc');
            $this->db->limit(1);
            $result = $this->db->get('reports');
            
            return $result->row_array();
        }

        public function get_accounts($report_id, $status = null) {
            if(!is_null($status)) {
                $this->db->where('status', $status);
            }

            $this->db->where('report_id', $report_id);
            $this->db->limit(350);
            
            $result = $this->db->get('accounts')->result_array();

            //lock those records
            if(count($result) > 0) {
                foreach($result as $row) {
                    $this->db->where('id', $row['id']);
                    $this->db->update('accounts', array('status' => 2));
                }
            }

            return $result;
        }

        public function update_account($id, $data) {
            $this->db->where('id', $id);

            $this->db->update('accounts', $data);
        }

        public function update_report_status($report_id) {
            $this->db->where('id', $report_id);

            $this->db->update('reports', array('status' => 1));
        }

        public function get_main_accounts($report_id ) {
            $this->db->where('report_id', $report_id);
            $this->db->where('reference_id IS NULL');

            $result = $this->db->get('accounts');

            return $result->result_array();
        }

        public function get_followers($report_id, $reference_id) {
            $this->db->where('report_id', $report_id);
            $this->db->where('reference_id', $reference_id);

            $result = $this->db->get('accounts');

            return $result->result_array();
        }

        public function check_report_status($report_id) {
            $this->db->select('id');
            $this->db->where('status', 0);

            $result = $this->db->get('accounts');

            if($result->result_array()) {
                return false;
            } else {
                return true;
            }
        }

        public function remove_report($report_id) {
            $this->db->where('id', $report_id);
            $this->db->delete('reports');

            $this->db->where('report_id', $report_id);
            $this->db->delete('accounts');
        }

    }
?>