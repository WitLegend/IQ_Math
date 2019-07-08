<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 数据库访问类
 */
class MY_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

//        $this->db = get_instance()->db;
//        $this->meta_db = get_instance()->meta_db;
//        $this->data_db = get_instance()->data_db;
        $this->load->driver('cache');
    }

    /**
     * 获取一条记录
     * @param string $file
     * @param array $condition
     * @param string $table
     * @return array
     */
    public function findOne($file='*',$condition=array(),$table ='')
    {
        $f_table = empty($table) ? $this->table : $table;
        $like = array();
        if (isset($condition['like']))
        {
            $like = empty($condition['like']) ? array() : $condition['like'];
            unset($condition['like']);
        }
        $this->db->select($file)
            ->from($f_table)
            ->where($condition);
        if (!empty($like))
        {
            $this->db->like($like);
        }
        $query = $this->db->limit(1)->get();//echo $this->db->last_query();
        $result = array();
        if ($query->num_rows()>0)
        {
            $result = $query->row_array();
        }
        return $result;
    }

    /**
     * 获取记录列表
     * @param string $file
     * @param array $condition
     * @param int $limit
     * @param int $offset
     * @param string $order
     * @param string $sort
     * @param string $table
     * @return array
     * @author hdh
     */
    public function findList($file='*',$condition=array(),$limit=0,$offset=0,$order='',$sort='DESC',$table='')
    {
        $tag = true;
        if($this->check_key($order) == false) $tag = false;
        $f_table = empty($table) ? $this->table : $table;
        $like = array();
        if (isset($condition['like']))
        {
            $like = empty($condition['like']) ? array() : $condition['like'];
            unset($condition['like']);
        }

        $in = array();

        if (isset($condition['in']))
        {
            $in = empty($condition['in']) ? array() : $condition['in'];
            unset($condition['in']);
        }

        $not_in = array();
        if (isset($condition['not_in']))
        {
            $not_in = empty($condition['not_in']) ? array() : $condition['not_in'];
            unset($condition['not_in']);
        }

        $key = '';

        if (isset($condition['keyword']))
        {
            $key = empty($condition['keyword']) ? '' :$condition['keyword'];
            unset($condition['keyword']);
        }

        if($tag)
        {		$this->db->select($file)
            ->from($f_table)
            ->where($condition);
            if (!empty($key))
            {
                $this->db->where($key);
            }
            if (!empty($in))
            {
                $this->db->where_in($in['column'],$in['data']);
            }
            if (!empty($not_in) && !empty($not_in['data']))
            {
                $this->db->where_not_in($not_in['column'],$not_in['data']);
            }
            if (!empty($like))
            {
                if (isset($like['point']))
                {
                    $this->db->like($like['title'],$like['data'],$like['point']);
                }
                else
                {
                    $this->db->like($like);
                }
            }
            if (!empty($limit))
            {
                $this->db->limit($limit,$offset);
            }

            if (!empty($order) and $order != 'merge')
            {
                $this->db->order_by($order,$sort);
            }
            if (!empty($order) and $order == 'merge')
            {
                $this->db->order_by($sort);
            }
            $query = $this->db->get();
            $result = array();
            if ($query->num_rows()>0)
            {
                $result = $query->result_array();
            }
        }
        else $result = [];

        return $result;
    }

    /**获取条件下表的记录条数(分页常用)
     * @param array $condition
     * @param string $table
     * @return int|bool mixed
     *
     * @author hdh
     */
    public function count_num($condition,$table='')
    {
        $like = array();
        $table = empty($table) ? $this->table : $table;
        if (isset($condition['like']))
        {
            $like = empty($condition['like']) ? array() : $condition['like'];
            unset($condition['like']);
        }
        $key = '';
        if (isset($condition['keyword']))
        {
            $key = empty($condition['keyword']) ? '' :$condition['keyword'];
            unset($condition['keyword']);
        }
        $in = array();

        if (isset($condition['in']))
        {
            $in = empty($condition['in']) ? array() : $condition['in'];
            unset($condition['in']);
        }
        $not_in = array();
        if (isset($condition['not_in']))
        {
            $not_in = empty($condition['not_in']) ? array() : $condition['not_in'];
            unset($condition['not_in']);
        }

        $this->db->from($table)
            ->where($condition);
        if (!empty($key))
        {
            $this->db->where($key);
        }
        if (!empty($in))
        {
            $this->db->where_in($in['column'],$in['data']);
        }
        if (!empty($not_in))
        {
            $this->db->where_not_in($not_in['column'],$not_in['data']);
        }

        if (!empty($like))
        {
            if (isset($like['point']))
            {
                $this->db->like($like['title'],$like['data'],$like['point']);
            }
            else
            {
                $this->db->like($like);
            }
        }
        return $this->db->count_all_results();
    }

    /**
     * 更新表记录
     * @param $condition
     * @param $data
     * @param $table
     * @return mixed
     */
    public function update($data,$condition, $table = '')
    {
        if(!empty($table))
        {
            return $this->db->update($table, $data,$condition);
        }
        else
        {
            if($this->table)
            {
                return $this->db->update($this->table,$data,$condition);
            }
            return $this->db->update($this->table_name,$data,$condition);
        }

    }
}