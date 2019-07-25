<?php

namespace Ekreative\HealthCheckBundle\DependencyInjection;

use Predis\Client;

class PredisFactory
{
    /**
     * @param string $parameters
     * @param string $options
     *
     * @return Client
     */
    public static function get($parameters = null, $options = null)
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            if ($severity & error_reporting()) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }
        });

        $redis = new Client($parameters, $options);
        try {
            $redis->connect();
        } catch (\Predis\Connection\ConnectionException $e) {
        } catch (\Predis\ClientException $e) {
        } catch (\ErrorException $e) {
        }

        restore_error_handler();

        return $redis;
    }
}
