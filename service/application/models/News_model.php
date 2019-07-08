<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/3/24
 * Time: 14:12
 */
class News_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
//        $this->db = $this->load->database('default',true);
        $this->load->database();
    }

    public function get_news($slug = FALSE)
    {
//        $this->db = $this->load->database('default',true);
        if ($slug === FALSE)
        {
//            $query = $this->db->get('news');
//            return $query->result_array();
            return $this->db->get('news')->result_array();
        }

        $query = $this->db->get_where('news', array('slug' => $slug));
        return $query->row_array();
    }
}