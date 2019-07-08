<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/4/6
 * Time: 16:14
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Rank extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('questions_model');
        $this->load->helper('url_helper');
        $this->db = $this->load->database('default',true);
    }

    public function get_rank_global()
    {
        $num = $this->get_value('loadNumber');
        $star = 0 + $num * 10;
        $res = $this->db->select('max(score) as score,max(avatarUrl) as avatarUrl,max(city) as city,max(nickName) as nickName')
            ->from('iq_math_record a')
            ->join('iq_user b','a.openid = b.openid')
            ->group_by('a.openid')
            ->order_by('max(score) desc')
            ->limit(10,$star)
            ->get()->result_array();
        $this->output($res);
    }

    public function get_rank_rate()
    {
        $num = $this->get_value('avgNumber');
        $star = 0 + $num * 10;
        $res = $this->db->select('round(avg(accuracy_rate),2) as score,max(avatarUrl) as avatarUrl,max(city) as city,max(nickName) as nickName')
            ->from('iq_math_record a')
            ->join('iq_user b','a.openid = b.openid')
            ->group_by('a.openid')
            ->order_by('avg(accuracy_rate) desc')
            ->limit(10,$star)
            ->get()->result_array();
        $this->output($res);
    }
}