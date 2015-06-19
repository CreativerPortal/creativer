<?php
namespace Creativer\FrontBundle\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * NativeRedisSessionStorage.
 *
 * Driver for the redis session save hadlers provided by the redis PHP extension.
 *
 * @see https://github.com/nicolasff/phpredis
 *
 * @author Andrej Hudec &lt;pulzarraider@gmail.com&gt;
 * @author Piotr Pelczar &lt;me@athlan.pl&gt;
 */
class NativeRedisSessionHandler extends NativeSessionHandler
{
    /**
     * Constructor.
     *
     * @param string $savePath Path of redis server.
     */
    public function __construct($savePath = "")
    {
        if (!extension_loaded('redis')) {
            die('PHP does not have "redis" session module registered');
        }

        if ("" === $savePath) {
            $savePath = ini_get('session.save_path');
        }

        if ("" === $savePath) {
            $savePath = "tcp://localhost:6379"; // guess path
        }

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $savePath);
    }
}