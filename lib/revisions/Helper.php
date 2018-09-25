<?php

namespace DBV\Revisions;

class Helper
{
    /**
     * 校验命名是否符合规则
     * @param  String $name
     * @return boolean
     */
    public static function checkName($name)
    {
        return filter_var($name, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => DBV_REVISIONS_NAME_RULE
            ]
        ]) ? true : false;
    }
}
