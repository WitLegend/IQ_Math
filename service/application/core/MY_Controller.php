<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    const RET_CODE_OK = 0;

    public static $ret_code = array(
        self::RET_CODE_OK => "OK",
    );


    public function post_value($field, $default = NULL)
    {
        $input = $this->input;

        if ($input->post($field) || $input->post($field) === '0' || $input->post($field) === 0 || $input->post($field) === '') {
            return $input->post($field);
        } else {
            return $default;
        }
    }

    public function get_value($field, $default = NULL)
    {
        $input = $this->input;

        if ($input->get($field) || $input->get($field) == '0') {
            return $input->get($field);
        } else {
            return $default;
        }
    }

    public function check_field($fields = array(), $from = 'post')
    {

        $form_validation = $this->form_validation;

        if (!is_array($fields)) {
            throw new QH_Exception("", QH_Exception::RET_CODE_INVALID_ATTRIBUTE);
        }

        if ($from === 'get') {
            $data = array();
            foreach ($fields as $f) {
                $data[$f['field']] = $this->input->get($f['field']);
            }
            $form_validation->set_data($data);
        }

        $form_validation->set_rules($fields);
        if ($form_validation->run() == FALSE) {
            throw new QH_Exception("", QH_Exception::RET_CODE_INVALID_ATTRIBUTE, $form_validation->error_array());
        }
    }

    public function output($data = array(), $addition_attr = array())
    {
        if ($this->input->get('debug') == 1) {
            return '';
        }
        $output = $this->output;
        $normal_data = array(
            'code' => self::RET_CODE_OK,
            'msg' => self::$ret_code[self::RET_CODE_OK],
            'data' => $data
        );
        $ret_data = array_merge($normal_data, $addition_attr);

        //TODO:跨域的临时解决方案
        $output->set_header("Access-Control-Allow-Origin:*");
        $output->set_content_type('application/json');
//        $output->set_output(json_encode($ret_data, JSON_PRETTY_PRINT));
        $result = json_encode($ret_data);
        if (!$result)
        {
            $result = json_encode(
                [
                    'code' => 1,
                    'msg' => '输出数据有误,无法转换成json',
                    'data' => []
                ]
            );
        }
        $output->set_output($result);
    }
}