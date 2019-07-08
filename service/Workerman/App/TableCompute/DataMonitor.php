<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/3/4
 * Time: 10:17
 */
namespace Workerman\App\TableCompute;
use Workerman\App\EventInterface as event;
use Workerman\Lib\Timer;
require_once __DIR__.'/../../vendor/autoload.php';


class DataMonitor implements event
{
    public $db = null;
    public function __construct(){
        if (is_null($this->db))
        {
//            require_once __DIR__ . '/../../Config/database.php';
//            $this->db = new \Workerman\MySQL\Connection(
//                $db['default']['hostname'],
//                $db['default']['port'],
//                $db['default']['username'],
//                $db['default']['password'],
//                $db['default']['database']
//            );
            $this->db = new \Workerman\MySQL\Connection(
                'localhost',
                3306,
                'root',
                'root',
                'iq_math'
            );
        }
    }
    public function onWorkerStart($worker){}
    public function onConnect($connection){}
    public function onMessage($connection, $data)
    {
        global $worker;
        $info = (array)json_decode($data);
        if (!isset($connection->uid))
        {
            $connection->uid = $info['openid'];
            $worker->uidConnections[$info['room_id']][$info['openid']] = $connection;
        }

        $res = $info;
        switch ($info['type'])
        {
            case 'new_room':
                $worker->uidConnections[$connection->uid][$info['openid']] = $connection;
                $res['other_is_answer'] = false;
                $res['other_answer'] = '';
                $res['is_join'] = 0;
                sendMessageByUid(['room_id'=>$info['room_id'],'uid'=>$info['openid']],$res);
                break;
            case 'join_room':
                $worker->uidConnections[$info['room_id']][$info['openid']] = $connection;
                $temp_2 = $temp_1 = [];
                if ($info['room_id'] != $info['openid'])
                {
                    $res['is_join'] = 1;
                    //  房主的信息
                    $temp1 = $this->db->select('nickName,avatarUrl,openid')
                        ->from('iq_user')
                        ->where('openid = :openid')
                        ->bindValues(['openid'=>$info['room_id']])
                        ->query();
                    if ($temp1){
                        foreach ($temp1 as $v)
                        {
                            $temp_1 = $v;
                            break;
                        }
                    }
                    //  被邀请者的信息
                    $temp2 = $this->db->select('nickName,avatarUrl,openid')
                        ->from('iq_user')
                        ->where('openid = :openid')
                        ->bindValues(['openid'=>$info['openid']])
                        ->query();
                    if ($temp2){
                        foreach ($temp2 as $v)
                        {
                            $temp_2 = $v;
                            break;
                        }
                    }
                }else{
                    $res['is_join'] = 0;
                }
                // 向两个用户发送游戏准备的信息
                foreach ($worker->uidConnections[$info['room_id']] as $k => $v)
                {
                    //状态字   防止同时回答时生产的bug
                    $worker->uidConnections[$info['room_id']][$k]->ans_status = '1';
                    if ($k == $info['openid']){  //被邀请者
                        $join = $res;       //使用新的变量存放，不然多次send有bug
                        $join['other_info'] = $temp_1;       //   other为房主
                        sendMessageByUid(['room_id'=>$info['room_id'],'uid'=>$k],$join);
                    }else{
                        $join = $res;
                        $join['other_info'] = $temp_2;       //  other为被邀请者
                        $join['openid'] = $k;
                        sendMessageByUid(['room_id'=>$info['room_id'],'uid'=>$k],$join);
                    }
                }
                break;
            case 'get_question':
                $num = $this->db->select('count(*) as num')
                    ->from('iq_question')
                    ->column();
                $id = mt_rand(1,$num[0]);
                $data = $this->db->select('*')
                    ->from('iq_question')
                    ->where('id = :id')
                    ->bindValues(['id'=>$id])
                    ->query();
                $temp = [];
                foreach ($data as $k =>$v)
                {
                    $temp = [
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
                    $temp['answer'][$v['answer']]['right'] = $v['answer'];
                    break;
                }
//                $res['question'] = $temp;
                foreach ($worker->uidConnections[$info['room_id']] as $k => $v)
                {
                    $worker->uidConnections[$info['room_id']][$k]->ans_status = '1';
                    $question = $res;
                    $question['openid'] = $k;
                    $question['question'] = $temp;
                    $question['status'] = 'new';
                    sendMessageByUid(['room_id'=>$info['room_id'],'uid'=>$k],$question);
                }
                break;
            case 'answer':
                foreach ($worker->uidConnections[$info['room_id']] as $k => $v)
                {
                    if($k != $info['openid'])
                    {
                        $answer = $res;
                        $answer['openid'] = $k;
                        $answer['status'] = $worker->uidConnections[$info['room_id']][$k]->ans_status;
                        sendMessageByUid(['room_id'=>$info['room_id'],'uid'=>$k],$answer);
                    }
                    $worker->uidConnections[$info['room_id']][$k]->ans_status = '2';
                }
                break;
            case 'game_over':
                $worker->uidConnections[$info['room_id']][$info['openid']]->true_num = $info['true_num'];
                $worker->uidConnections[$info['room_id']][$info['openid']]->score = $info['score'];
                break;
            case 'insert':
                $openid_1 = $info['openid'];
                foreach ($worker->uidConnections[$info['room_id']] as $k => $v)
                {
                    if ($k != $info['openid']){
                        $openid_2 = $k;
                    }
                }
                $this->db->insert('iq_battle_record')
                    ->cols([
                        'openid_1' => $openid_1,
                        'openid_2' => $openid_2,
                        'true_1' => isset($worker->uidConnections[$info['room_id']][$openid_1]->true_num) ? $worker->uidConnections[$info['room_id']][$openid_1]->true_num : 0,
                        'true_2' => isset($worker->uidConnections[$info['room_id']][$openid_2]->true_num) ? $worker->uidConnections[$info['room_id']][$openid_2]->true_num : 0,
                        'score_1' => isset($worker->uidConnections[$info['room_id']][$openid_1]->score) ? $worker->uidConnections[$info['room_id']][$openid_1]->score : 0,
                        'score_2' => isset($worker->uidConnections[$info['room_id']][$openid_2]->score) ? $worker->uidConnections[$info['room_id']][$openid_2]->score : 0,
                        'status' => 0
                    ])
                    ->query();
//                unset($worker->uidConnections[$info['room_id']]);
                break;
        }
    }

    public function onClose($connection)
    {
        global $worker;
        if (isset($connection->uid))
        {
            unset($worker->uidConnections[$connection->uid]);
        }
    }
    public function onWorkerStop(){}
}

