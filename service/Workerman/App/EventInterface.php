<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/3/4
 * Time: 10:45
 */
namespace Workerman\App;
interface EventInterface
{
    /**
     * 处理数据
     * @return mixed
     */
    public function onWorkerStart($worker);
    public function onConnect($connection);
    public function onMessage($connection, $data);
    public function onClose($connection);
    public function onWorkerStop();
}