<?php

function __($message, $params = array())
{
    if (function_exists("gettext")) {
        $return = gettext($message);
    } else {
        $return = $message;
    }
    if (count($params)) {
        foreach ($params as $key => $value) {
            $return = str_replace("#{" . $key . "}", $value, $return);
        }
    }

    return $return;
}

/**
 * 基础数据类型转字符串
 * @param  mixed $mixed 基础数据
 * @return string
 */
function mixed2string($mixed)
{
    if (is_bool($mixed)) {
        return $mixed ? 'true' : 'false';
    }

    if (is_numeric($mixed) || is_string($mixed)) {
        return $mixed;
    }

    if (is_null($mixed)) return 'null';

    if (is_object($mixed) || is_array($mixed) || is_resource($mixed)) {
        return serialize($mixed);
    }

    return 'unknown';
}