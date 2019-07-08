<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/3/24
 * Time: 13:13
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('url_helper');
    }

    public function index()
    {
        $data['news'] = $this->user_model->get_news();
        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/news', $data);
        $this->load->view('templates/footer');
    }

    public function login()
    {
        echo $this->input->get('da');exit;
        $res = $this->user_model->get_news();
        var_dump($res);
//        show_404();
    }

    public function view($slug = null)
    {
$list = [1,3,15,8,20,2];
$num = count($list);
for($i = 0;$i <= $num - 1;$i++){
    for ($j = $i + 1;$j <= $num;$j++){
        if ($list[$i] > $list[$j]){
            $temp = $list[$i];
            $list[$i] = $list[$j];
            $list[$j] = $temp;
        }
    }
}

        $data['news_item'] = $this->news_model->get_news($slug);

        if (empty($data['news_item']))
        {
            show_404();
        }

        $data['title'] = $data['news_item']['title'];

        $this->load->view('templates/header', $data);
        $this->load->view('news/view', $data);
        $this->load->view('templates/footer');
    }

    public function get_question()
    {
        $this->db = $this->load->database('default',true);
        $data = $this->db
            ->select()->from('iq_question')->get()->result_array();
        $this->output($data);
    }

    public function set_userinfo()
    {
        $userinfo = $this->get_value('userInfo');
        $userinfo = (array)json_decode($userinfo);
//        var_dump($userinfo);exit;
        $openid = $this->get_value('openid');
        if ($this->user_model->findOne('id',['openid' => $openid],'iq_user'))
        {
            $this->user_model->update($userinfo,['openid' => $openid],'iq_user');
        }else{
            $userinfo['openid'] = $openid;
            $this->db->insert('iq_user',$userinfo);
        }
        $this->output();
    }

    public function get_userinfo()
    {
        $openid = $this->get_value('openid');
        $userinfo = $this->user_model->findOne('*',['openid' => $openid],'iq_user');
        if ($userinfo){
            $this->output($userinfo);
        }else{
            $this->output();
        }
    }

    public function get_score()
    {
        $openid = $this->get_value('openid');
        $res = $this->db->select('max(score) as score')
            ->from('iq_math_record')
            ->where('openid',$openid)
            ->get()->row_array()['score'];
        $this->output($res);
    }
}