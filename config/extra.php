<?php

/**
 * 定义版本目录命名规则(正则表达式)
 */
define('DBV_REVISIONS_NAME_RULE', '/^([a-zA-Z0-9]){1}([a-zA-Z0-9]|_|\.|-)+$/');

/**
 * 定义SQL执行状态和日志的存放路径
 */
define('DBV_REVISIONS_SQL_RUN_STATUS', DBV_META_PATH);
define('DBV_REVISIONS_SQL_RUN_LOG', DBV_ROOT_PATH . DS . 'log');
