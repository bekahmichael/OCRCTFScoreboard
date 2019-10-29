<?php

use yii\helpers\VarDumper;

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = false)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

/**
 * Print value.
 *
 * @param mixed $var value for print.
 * @param boolean $stop true if need stop execute app.
 */
function _d($var, $stop = false)
{
    VarDumper::dump($var, 10);
    if ($stop) {
        exit;
    }
}

/**
 * Print value with highlight.
 *
 * @param mixed $var value for print.
 * @param boolean $stop true if need stop execute app.
 */
function _dd($var, $stop = false)
{
    VarDumper::dump($var, 10, true);
    if ($stop) {
        exit;
    }
}

/**
 * Send data to local TCP socket which opened under websocket
 * @param array $data
 */
function pushDataToWebsocketWorker(array $data)
{
    $localSocket = 'tcp://127.0.0.1:'.env('LOCAL_TCP_PORT',1234);
    $error = '';
    try {
        $instance = @stream_socket_client($localSocket, $errno, $errStr, 30);

        if (!$instance) {
            $error .= "$errStr ($errno)";
        } else {
            fwrite($instance, json_encode($data));
            fclose($instance);
        }
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    if($error) {
        Yii::error($error);
    }
}