<?php

namespace DBV\Revisions;

/**
 * SQL执行状态
 */
class RunStatus
{
    private $_revision;
    private $_revision_file;
    private $_status_file;
    private $_error;

    public function __construct($revision)
    {
        $this->_revision      = $revision;
        $this->_revision_file = DBV_REVISIONS_PATH.DS.$this->_revision;
        $this->_status_file   = DBV_META_PATH.DS.$this->_revision.'.php';
    }

    /**
     * 刷新执行状态
     * @return boolean
     */
    public function refresh()
    {
        if (!$this->_checkStatus(false)) return $this->create();

        $data = $this->get();
        if (!$data) return false;
        $data['modify_time'] = filemtime($this->_revision_file);
        return $this->_set($data);
    }

    /**
     * 生成执行状态
     * @return boolean
     */
    public function create()
    {
        if (!$this->_checkRevision()) return false;

        return $this->_set([
            'modify_time' => filemtime($this->_revision_file)
        ]);
    }

    public function modify(array $data = [])
    {
        if (!$this->_checkRevision()) return false;

        $tmp = $this->get();
        if (!$tmp) return false;

        $data = array_merge($tmp, $data);
        return $this->_set($data);
    }


    private function _checkStatus($isError = true)
    {
        if (!file_exists($this->_status_file)) {
            if ($isError) {
                $this->_error = __('Status file for revision #{revision} is not exists.', ['revision' => $this->_revision]);
            }
            return false;
        }

        return true;
    }

    private function _checkRevision($isError = true)
    {
        if (!file_exists($this->_revision_file)) {
            if ($isError) {
                $this->_error = __('Revision #{revision} is not exists.', ['revision' => $this->_revision]);
            }
            return false;
        }

        return true;
    }

    private function _set(array $data = [])
    {
        // key and default value
        $fields = [
            'status'          => false,
            'modify_time'     => '',
            'lastly_run_time' => ''
        ];
        $tmp = [];
        foreach ($fields as $k => $v) {
            $tmp[$k] = isset($data[$k]) ? $data[$k] : $v;
        }

        $str = '';
        $tab = str_repeat(' ', 4);// 4个空格缩进
        foreach ($tmp as $m => $n) {
            if (is_numeric($n) || is_bool($n) || is_null($n)) {
                $n = mixed2string($n);
            } else {
                $n = '\''.mixed2string($n).'\'';
            }
            $str .= "\n".$tab.'\''.$m.'\' => '.$n.',';
        }

        $content = '<?php return ['.$str."\n".'];';
        if (file_put_contents($this->_status_file, $content) === false) {
            $this->_error = __('Something error occurred,please try again.');
            return false;
        }

        return true;
    }

    /**
     * 获取执行状态
     * @return mixed
     */
    public function get()
    {
        if (!$this->_checkStatus()) return false;

        $data = include $this->_status_file;
        return $data;
    }

    public function getError()
    {
        return $this->_error;
    }
}