<?php

// +----------------------------------------------------------------------
// | rootCMS 计划任务卸载脚本
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rootcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace Cron\Uninstall;

use Libs\System\UninstallBase;

class Uninstall extends UninstallBase {

    //End
    public function end() {
        //移除Cron目录
        ShuipFCMS()->Dir->delDir(PROJECT_PATH . 'Cron/');
        return true;
    }

}
