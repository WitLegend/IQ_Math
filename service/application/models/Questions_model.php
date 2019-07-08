<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/4/3
 * Time: 16:16
 */
class Questions_model extends MY_Model{
    public function __construct()
    {
        parent::__construct();
//        $this->db = $this->load->database('default',true);
        $this->load->database();
    }
}