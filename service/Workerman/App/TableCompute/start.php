<?php
namespace Workerman\App\TableCompute;
use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\App\TableCompute\DataMonitor as dm;
//require_once __DIR__ . '../../../..//Workerman/Autoloader.php';
require_once __DIR__ . '/../../Autoloader.php';
//require_once __DIR__.'/../../Config/config.php';

$worker = new Worker("websocket://0.0.0.0:8111");
$worker->uidConnections = array();
$worker->count = 1;
$dm = new dm();
$worker->onWorkerStart = [$dm, 'onWorkerStart'];
$worker->onMessage = [$dm , 'onMessage'];
$worker->onClose = [$dm, 'onClose'];



function sendMessageByUid($info, $message, $code=0, $mess='ok')
{
    global $worker;
    if(isset($worker->uidConnections[$info['room_id']][$info['uid']]))
    {
        $data = [
            'code' => $code,
            'data' => $message,
            'mess' => $mess
        ];
        $connection = $worker->uidConnections[$info['room_id']][$info['uid']];
        $connection->send(json_encode($data));
    }
}

// 运行worker
Worker::runAll();
