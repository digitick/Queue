<?php

namespace Digitick\PhpAmqpLib\Connection;

use PhpAmqpLib\Connection\AMQPStreamConnection as BaseAMQPStreamConnection;

/**
 * Class AMQPStreamConnection
 */
class AMQPStreamConnection extends BaseAMQPStreamConnection
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     * @param bool   $enabled
     */
    public function __construct($host, $port, $user, $password, $vhost = '/', $enabled = true)
    {
        // enable or disable connection
        $this->enabled = (bool) $enabled;
        parent::__construct($host, $port, $user, $password, $vhost);
    }

    /**
     * Should the connection be attempted during construction?
     *
     * @return bool
     */
    public function connectOnConstruct()
    {
        if (false === $this->enabled) {
            return false;
        }

        return parent::connectOnConstruct();
    }
}
