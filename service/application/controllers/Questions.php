<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2019/4/3
 * Time: 16:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Questions extends MY_Controller{


    public function get_questions()
    {
        $this->db = $this->load->database('default',true);
        $num = $this->db->select('count(*) as num')->from('iq_question')->get()->row_array()['num'];
        $id = mt_rand(1,$num);
        $data = $this->db
            ->select()->from('iq_question')->where('id',$id)->get()->result_array();
        $res = [];
        foreach ($data as $k =>$v)
        {
            $res = [
                'ask' => $v['question'],
                'answer' => [
                    'a' => [
                        'answer' => $v['a']
                    ],
                    'b' => [
                        'answer' => $v['b']
                    ],
                    'c' => [
                        'answer' => $v['c']
                    ],
                    'd' => [
                        'answer' => $v['d']
                    ],
                ],
            ];
            $res['answer'][$v['answer']]['right'] = $v['answer'];
        }
        $this->output($res);
    }
}