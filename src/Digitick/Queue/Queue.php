<?php

namespace Digitick\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    private $connection;
    private $channel;
    private $queues = [];
    private $exchange_name;

    public function __construct(AMQPStreamConnection $connection, $exchange_name, $exchange_type = 'direct')
    {
        $this->exchange_name = $exchange_name;
        $this->connection = $connection;
        if ($this->connection->isConnected()) {
            $this->channel = $connection->channel();
            $this->channel->exchange_declare($exchange_name, $exchange_type, false, false, false);
        }
    }

    public function addQueue($name, $routing_key)
    {
        if ($this->channel && !array_key_exists($name, $this->queues)) {
            $this->channel->queue_declare($name, false, true, false, false);
            $this->channel->queue_bind($name, $this->exchange_name, $routing_key);
            $this->queues[$name] = true;
        }
    }

    public function send($msg, $routing_key = null)
    {
        if ($this->channel) {
            $msg = new AMQPMessage($msg);
            $this->channel->basic_publish($msg, $this->exchange_name, $routing_key);

            return true;
        }

        return false;
    }
}
