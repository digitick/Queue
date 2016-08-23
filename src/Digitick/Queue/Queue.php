<?php

namespace Digitick\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Queue
 * @package Digitick\Queue
 */
class Queue
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;

    /**
     * @var array
     */
    private $queues = [];

    /**
     * @var string
     */
    private $exchange_name;

    /**
     * Queue constructor.
     * @param AMQPStreamConnection $connection
     * @param $exchange_name
     * @param string $exchange_type
     */
    public function __construct(AMQPStreamConnection $connection, $exchange_name, $exchange_type = 'direct')
    {
        $this->exchange_name = $exchange_name;
        $this->connection = $connection;
        if ($this->connection->isConnected()) {
            $this->channel = $connection->channel();
            $this->channel->exchange_declare($exchange_name, $exchange_type, false, false, false);
        }
    }

    /**
     * @param $name
     * @param $routing_key
     */
    public function addQueue($name, $routing_key)
    {
        if ($this->channel && !array_key_exists($name, $this->queues)) {
            $this->channel->queue_declare($name, false, true, false, false);
            $this->channel->queue_bind($name, $this->exchange_name, $routing_key);
            $this->queues[$name] = true;
        }
    }

    /**
     * @param $msg
     * @param null $routing_key
     * @return bool
     */
    public function send($msg, $routing_key = null)
    {
        if ($this->channel) {
            $msg = new AMQPMessage($msg);
            $this->channel->basic_publish($msg, $this->exchange_name, $routing_key);

            return true;
        }

        return false;
    }

    /**
     * Close channels and the connection
     */
    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
