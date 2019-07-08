<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/4/5
 * Time: 17:06
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Record extends MY_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('questions_model');
        $this->load->helper('url_helper');
    }

    public function set_record()
    {
        $this->db = $this->load->database('default',true);
        $data = [
            'openid' => $this->get_value('openid'),
            'score' => $this->get_value('score'),
            'sure_num' => $this->get_value('sure_num'),
        ];
        $data['accuracy_rate'] = bcdiv($data['sure_num'],5,2) * 100;
        unset($data['sure_num']);
        $this->db->insert('iq_math_record',$data);
        $this->output();
    }
}