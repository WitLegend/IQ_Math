<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/4/15
 * Time: 21:30
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends MY_Controller{
    public function __construct()
    {
        parent::__construct();
//        $this->load->model('user_model');
        $this->load->helper('url_helper');
        $this->load->helper('url');
        $this->load->helper('cookie');
        $this->db = $this->load->database('default',true);
        if (get_cookie('username')){

        }else if ($this->uri->uri_string() == 'admin/login'){

        }else if ($this->uri->uri_string() == 'admin/user_login'){

        }else{
            $this->login();
        }
//        if ($this->uri->uri_string() != 'admin/login' or $this->uri->uri_string() != 'admin/user_login')
//        {
//            echo $this->uri->uri_string();exit;
//            if (get_cookie('username')){
//                $this->load->view('admin/templates/index');
//            }else{
////                $this->load->view('admin/templates/login');
//            }
//        }
    }

    //返回登陆页面
    public function login()
    {
        $this->load->view('admin/templates/login');
    }

    public function out_login()
    {
        delete_cookie('username');
        $this->output('ok');
    }

    //登陆操作
    public function user_login()
    {
        $username = $this->post_value('username');
        $password = $this->post_value('password');
        $user = $this->db->select('*')
            ->from('iq_admin_user')
            ->where('username',$username)
            ->get()->row_array();
        if ($user){
            if (md5($password) == $user['password']){
                set_cookie('username',$username,0);
                $this->output('ok');
            }else{
                $this->output('error');
            }
        }else{
            $this->output('error');
        }
    }

    //返回主页面
    public function index()
    {
        $this->load->view('admin/templates/index');
    }

    public function insert_question(){
        $question = $this->get_value('question');
        $a = $this->get_value('choice_a');
        $b = $this->get_value('choice_b');
        $c = $this->get_value('choice_c');
        $d = $this->get_value('choice_d');
        $answer = $this->get_value('ture_choice');
        $data = [
            'question' => $question,
            'a' => $a,
            'b' => $b,
            'c' => $c,
            'd' => $d,
            'answer' => $answer
        ];
        $this->db->insert('iq_question',$data);
        $this->output('ok');
    }

//    public function down_csv(){
//        $tableheader = array('问题内容', '选项a', '选项b', '选项c', '选项d','正确选项（a，b，c，d）');
//
//        /*输入到CSV文件 解决乱码问题*/
//        $html = "\xEF\xBB\xBF";
//
//        /*输出表头*/
//        foreach ($tableheader as $value) {
//            $html .= $value . "\t ,";
//        }
//        $html .= "\n";
//
//        /*输出CSV文件*/
//        header("Content-type:text/csv");
//        header("Content-Disposition:attachment; filename=题目导入模板.csv");
//        echo $html;
//        exit();
//    }

//    public function down_csv(){
//        ob_start();
//        $df = fopen("php://output", 'w');
//        $tableheader = array('问题内容', '选项a', '选项b', '选项c', '选项d','正确选项（a，b，c，d）');
//        fwrite($df,"\xEF\xBB\xBF");
//        fputcsv($df,$tableheader);
//        fclose($df);
//        // disable caching
//        $now = gmdate("D, d M Y H:i:s");
//        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
//        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
//        header("Last-Modified: {$now} GMT");
//
//        // force download
//        header("Content-Type: application/force-download");
//        header("Content-Type: application/octet-stream");
//        header("Content-Type: application/download");
//
//        // disposition / encoding on response body
//        header("Content-type:text/csv");
//        header("Content-Disposition: attachment;filename=题目导入模板.csv");
////        header("Content-Transfer-Encoding: binary");
//        echo ob_get_clean();
//    }

    //下载csv文件
    public function down_csv(){
        $header_data = array('问题内容', '选项a', '选项b', '选项c', '选项d','正确选项');

        header('Pragma:public');
        header('Content-Disposition: attachment;filename=题目导入模板.csv');
        header('Cache-Control: max-age=0');
        header("Content-Type: application/vnd.ms-excel; charset=GB2312");

        $fp = fopen('php://output', 'a');
        if (!empty($header_data))
        {
            foreach ($header_data as $key => $value)
            {
                $header_data[$key] = iconv('utf-8', 'gbk', $value);
            }
            fputcsv($fp, $header_data);
        }
        fclose($fp);
    }

    public function insert_question_by_csv(){
        $file = $_FILES['avatar'];
        if(strtolower(substr($file['name'], strrpos($file['name'], '.') + 1)) == 'csv')
        {
            $csv_file = fopen($file["tmp_name"], 'r');
            //  取出表头
            $head = fgetcsv($csv_file);
            //  取出剩下的数据
            if (count($head) == 6){
                $data = [];
                while ($csvList = fgetcsv($csv_file)){
                    foreach ($csvList as $kData => $vData) {
                        $data[$kData] = iconv('GB2312', 'UTF-8', $vData);   //将GBK转为UTF8放入数组
                    }
                    $in_data = [
                        'question' => $data[0],
                        'a' => $data[1],
                        'b' => $data[2],
                        'c' => $data[3],
                        'd' => $data[4],
                        'answer' => $data[5]
                    ];
                    $this->db->insert('iq_question',$in_data);
                }
                $this->output('ok');
            }else{
                $this->output('error');
            }
        }
    }

    public function add_user(){
        $username = $this->get_value('username');
        $password = $this->get_value('password');
        $data = [
            'username' => $username,
            'password' => md5($password)
        ];
        $this->db->insert('iq_admin_user',$data);
        $this->output('ok');
    }

    public function alt_password()
    {
        $username = get_cookie('username');
        $old = $this->post_value('old_password');
        $new = $this->post_value('new_password');
        $info = $this->db->select('*')
            ->from('iq_admin_user')
            ->where('username',$username)
            ->get()->row_array();
        if ($info['password'] == md5($old)){
            $this->db->update('iq_admin_user',['password' => md5($new)],['username' => $username]);
            $this->output('ok');
        }else{
            $this->output('error');
        }
    }

    public function publics()
    {
        ['name' => ['$nin' => ['kingmax','soul']]];
        $pub = new ReflectionClass($this);
        $res = [];
        foreach ($pub->getProperties(ReflectionProperty::IS_PUBLIC) as $item)
        {
            $res[$item->getName()] = $item->getValue($this);
        }

    }

    protected static $APPID = 2015063000000001;
    protected static $SALT = 1435660288;
    protected static $KEY = 12345678;
    public function translateOne($word)
    {
        global $APPID;
        global $SALT;
        $sign = $this->get_sign($word);
        $url = "http://api.fanyi.baidu.com/api/trans/vip/translate?q={$word}&from=en&to=zh&appid={$APPID}&salt={$SALT}&sign={$sign}";

        $cu = curl_init($url);
        curl_setopt($cu,CURLOPT_RETURNTRANSFER,1);
        $url_res = curl_exec($cu);
        curl_close($cu);
        $temp = json_decode($url_res,TRUE);

        $res = '';
        if (isset($temp['trans_result'])){
            if (isset($temp['trans_result']['dst'])){
                $res = $temp['trans_result']['dst'];
            }
        }
        return $res;
    }

    private function get_sign($work)
    {
        global $APPID;
        global $SALT;
        global $KEY;
        return md5($APPID.$work.$SALT.$KEY);
    }

    public function translateMany($words)
    {
        $res = [];
        foreach ($words as $v)
        {
            $res[] = $this->translateOne($v);
        }
        return $res;
    }
}