<?php

// +----------------------------------------------------------------------
// | rootCMS URL规则管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rootcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace Content\Controller;

use Common\Controller\AdminBase;

class UrlruleController extends AdminBase {

    //初始化
    protected function _initialize() {
        parent::_initialize();
        //可用模块列表
        $Module = array(
            'content' => array(
                'module' => 'content',
                'name' => '内容模块',
            ),
        );
        foreach (cache('Module') as $r) {
            $Module[strtolower($r['module'])] = array(
                'module' => strtolower($r['module']),
                'name' => $r['modulename']
            );
        }
        $this->assign('Module', $Module);
    }

    //URL规则显示
    public function index() {
        $this->assign('info',  model('Content/Urlrule')->order(array('urlruleid' => 'DESC'))->select());
        $this->display();
    }

    //添加新规则
    public function add() {
        $this->baseAdd('Content/Urlrule', 'index');
    }

    //编辑规则
    public function edit() {
        $this->baseEdit('Content/Urlrule', 'index');
    }

    //删除规则
    public function delete() {
        $this->baseDelete('Content/Urlrule', 'index');
    }

}