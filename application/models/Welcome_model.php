<?php
/**
 * 
 */
class Welcome_model extends CI_Model
{
	var $table = "tbl_employees";
	var $select_column = array("origin","employee_code","employee_name","email","mobile","designation","roles","photo");
	var $order_column = array("origin","employee_code","employee_name","email","mobile","designation","roles","photo");

	public function make_query(){
		$this->db->select($this->select_column);
		$this->db->from($this->table);
		if (isset($_POST['search']['value'])) {
			$this->db->like('employee_name',$_POST['search']['value']);
			$this->db->or_like('roles',$_POST['search']['value']);
		}
		if (isset($POST['order'])) {
			$this->db->order_by($this->order_column[$_POST['order']['0']['column']],$_POST['order']['0']['dir']);
		}
		else{
			$this->db->order_by('origin','DESC');
		}
	}

	public function make_datatables(){
		$this->make_query();
		if ($_POST['length']!=-1) {
			$this->db->limit($_POST['length'],$_POST['start']);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function get_filtered_data(){
		$this->make_query();
		$query=$this->db->get();
		return $query->num_rows();
	}

	public function get_all_data(){
		$this->db->select('*');
		$this->db->from($this->table);
		return $this->db->count_all_results();

	}

	public function insert_employee($data){
		$this->db->insert('tbl_employees',$data);
	}

	public function fetch_single_user_data($origin){
		$this->db->where('origin',$origin);
		return $this->db->get('tbl_employees')->result_array();
	}

	public function update_employee($data,$origin){
		$this->db->where('origin',$origin);
		$this->db->update('tbl_employees',$data);
	}

	public function delete_employee($origin){
		$this->db->where('origin',$origin);
		return $this->db->delete('tbl_employees');
	}
	
}
?>