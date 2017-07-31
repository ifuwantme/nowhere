<?php

class Basic_model extends CI_Model {

    public function __construct($table_name) {
        $this->load->database();
        $this->table_name = $table_name;
        $this->unique_fields = NULL;
    }

    public function delete_where($data) {
        $this->db->where($data);
        $this->db->delete($this->table_name);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table_name);
    }

    public function gets($ids,$order_by=NULL) {
        if (empty($ids)) {
            return array();
        }

        $this->db->from($this->table_name);
        $this->db->where_in('id', $ids);
        if($order_by) {
            $this->db->order_by($order_by);
        }
        return $this->db->get()->result();
    }

    public function get($id) {
        return $this->_get($this->table_name, $id);
    }

    protected function _get($table, $id) {
        $this->db->from($table);
        $this->db->where(array('id'=>$id));
        $objs = $this->db->get()->result();
        if (count($objs) > 0) {
            return $objs[0];
        } else {
            return NULL;
        }
    }

    public function get_list($limit=0, $offset=0, $order_by=NULL,$where = NULL) {
        if ($order_by) {
            $order_by = $order_by[0].'  '.$order_by[1];
        }
        $rs = $this->where($where,$limit,$order_by,$offset);
        return $rs;
    }

    public function by_ids($id_array) {
        if (count($id_array) == 0) {
            return array();
        }
        $this->db->from($this->table_name);
        $this->db->where_in('id', $id_array);
        return $this->db->get()->result();
    }

    public function by_flow_bus($flow_id) {
        $this->db->from($this->table_name);
        $this->db->where_in('flow_bus_id', $flow_id);
        return $this->db->get()->row();
    }


    public function count($where=NULL) {
        if (!empty($where)) {
            $this->db->from($this->table_name);
            foreach ($where as $key => $val) {
                $this->db->where($key,$val);
            }
            return $this->db->count_all_results();
            //$query = $this->db->get_where($this->table_name,$where);
            //return $query->num_rows();
        } else {

            $query = $this->db->get($this->table_name);
            return $query->num_rows();
        }

    }

    public function where_count($data = '', $limit=0, $order_by=NULL, $offset=0,$group_by='',$where_in = '',$filed='') {
        if ($filed) {
            $this->db->select($filed);
        }

        if ($data) {
            $this->db->where($data);
        }

        $this->db->from($this->table_name);


        if ($limit > 0) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        }

        if (!empty($where_in)) {
            foreach($where_in as $v){
                $this->db->where_in($v['filed'],$v['data']);
            }
        }

        if (!empty($group_by)) {
            foreach($group_by as $v){
                $this->db->group_by($v);
            }
        }
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function save($data) {
        return $this->_save($this->table_name, $data);
    }

    protected function _save($table, $data) {
        if (array_key_exists('id', $data) && $data['id']) {
            $id = $data['id'];
            $this->db->update($table, $data, array('id' => $id));
        } else {

            $id = NULL;

            if ($this->unique_fields) {
                $w = [];
                $full_unique = true;
                foreach ($this->unique_fields as $f) {
                    if (!array_key_exists($f, $data)) {
                        $full_unique = false;
                        break;
                    }

                    $w[$f] = $data[$f];
                }

                if ($full_unique) {
                    $obj = $this->where_one($w);
                    if ($obj) {
                        $id = $data['id'] = $obj->id;
                    }
                }
            }

            if ($id) {
                $this->db->update($table, $data, ['id'=>$id]);
            } else {
                $this->db->insert($table, $data);
                $id = $this->db->insert_id();
            }
        }
        return $this->_get($table, $id);
    }

    public function where($data = '', $limit=0, $order_by=NULL, $offset=0,$group_by='',$where_in = '',$filed='') {

        if ($filed) {
            $this->db->select($filed);
        }
        $this->db->from($this->table_name);

        if($data){
            $this->db->where($data);
        }

        if ($limit > 0) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        }

        if ($group_by) {
            if( gettype($group_by) == 'array' ){
                foreach($group_by as $v){
                    $this->db->group_by($v);
                }
            } else{ $this->db->group_by( $group_by ) ; }
        }

        if (!empty($where_in)) {
            foreach($where_in as $v){
                $this->db->where_in($v['filed'],$v['data']);
            }
        }



       return $this->db->get()->result();
    }

    public function where_one($data, $order_by=NULL) {
        $objs = $this->where($data, 1, $order_by);
        if (count($objs) > 0) {
            return $objs[0];
        } else {
            return NULL;
        }
    }

    /**
     * 获得分页的数据
     * @access public
     * @param String : $table; Num : $page; Num : $length; Array : $where; Array : $order_by
     * @return Array : $result;
     */
    public function get_page_list($page=0, $length=20, $where=NULL, $order_by=NULL) {
        if(!empty($where)){
            foreach ($where as $key => $val) {
                $this->db->where($key,$val);
            }
        }
        $this->db->from($this->table_name);
        $array['total'] = $this->db->count_all_results();
        $this->db->from($this->table_name);
        if (!$page) {
            $page = 1;
        }
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $length;
        if(!empty($where)){
            foreach ($where as $key => $val) {
                $this->db->where($key,$val);
            }
        }
        if(!empty($order_by)){
            $this->db->order_by($order_by[0],$order_by[1]);
        }
        $this->db->limit($length, $offset);
        $query = $this->db->get();
        $array['rows'] = $query->result();
        return $array;
    }
    public function get_or_create($where) {
        $result = $this->where_one($where);
        if (empty($result)) {
            $result = $this->save($where);
        }
        return $result;
    }

}
