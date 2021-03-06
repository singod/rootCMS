<?php

// +----------------------------------------------------------------------
// | rootCMS 站点配置
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rootcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Config extends AdminBase {

    private $Config = null;

    protected function _initialize() {
        parent::_initialize();
        $this->Config =  model('common/Config');
        $configList = $this->Config->column("varname,value");
        $this->assign('Site', $configList);
    }

    //网站基本设置
    public function index() {
        if (IS_POST) {
            if ($this->Config->saveConfig(input("post."))) {
                $this->success("更新成功！");
            } else {
                $error = $this->Config->getError();
                $this->error($error ? $error : "配置更新失败！");
            }
        } else {
            //首页模板
            $filepath = TEMPLATE_PATH ."index" . DS . (empty(self::$Cache["Config"]['theme']) ? 'Default' : self::$Cache["Config"]['theme']) . '/content/Index/';
            $indextp = str_replace($filepath, '', glob($filepath . 'index*'));
            //URL规则
            $Urlrules = sys_cache('Urlrules');
            $IndexURL = array();
            $TagURL = array();
            foreach ($Urlrules as $k => $v) {
                if ($v['file'] == 'tags') {
                    $TagURL[$v['urlruleid']] = $v['example'];
                }
                if ($v['module'] == 'content' && $v['file'] == 'index') {
                    $IndexURL[$v['ishtml']][$v['urlruleid']] = $v['example'];
                }
            }
            $this->assign('TagURL', $TagURL);
            $this->assign('IndexURL', $IndexURL);
            $this->assign('indextp', $indextp);
            $this->display();
        }
    }

    //邮箱参数
    public function mail() {
        $this->display();
    }
    
    public function cfgsave(){
        if(IS_POST){
            $this->index();
        }
    }

    //附件参数
    public function attach() {
        if (IS_POST) {
            $this->index();
        } else {
            $path = EXTEND_PATH . 'libs/driver/attachment/';
            $dirverList = glob($path . '*');
            $lang = array(
                'Local' => '本地存储驱动',
                'Ftp'   => 'FTP远程附件驱动',
                'Qiniu' => '七牛云存储驱动',
            );
            foreach ($dirverList as $k => $rs) {
                unset($dirverList[$k]);
                $dirverName = str_replace(array($path, '.class.php'), '', $rs);
                $dirverList[$dirverName] = $lang[$dirverName]? : $dirverName;
            }
            $this->assign('dirverList', $dirverList);
            $this->display();
        }
    }
    
    //高级配置
    public function addition() {
        $addition = include CONF_PATH . 'extend' . CONF_EXT;
        if (empty($addition) || !is_array($addition)) {
            $addition = array();
        }
        $this->assign("addition", $addition);
        $this->display();
    }
    
    //高级配置的保存
    public function additionsave(){
        if (IS_POST) {
            if ($this->Config->addition($_POST)) {
                $this->success("修改成功，请及时更新缓存！",url("admin/Config/addition"));
            } else {
                $error = $this->Config->getError();
                $this->error($error ? $error : "高级配置更新失败！",url("admin/Config/addition"));
            }
        }
    }

    //扩展配置
    public function extend() {
        
        $db = db('ConfigField');
        $extendList = $db->order(array('fid' => 'DESC'))->select();
        $this->assign('extendList', $extendList);
        $this->display();
    }
    
    public function extendsave(){
        if (IS_POST) {
            $action = input('post.action');

            if ($action == 'add') {
                //添加扩展项
                $data = array(
                    'fieldname' => trim(input('post.fieldname')),
                    'type' => trim(input('post.type')),
                    'setting' => input('post.setting/a') ,
                    config("TOKEN_NAME") => input('post.' . config("TOKEN_NAME")),
                );
                if ($this->Config->extendAdd($data) !== false) {
                    $this->success('扩展配置项添加成功！',  url('admin/Config/extend'));
                    return true;
                } else {
                    $error = $this->Config->getError();
                    $this->error($error ? $error : '添加失败！');
                }
            } else {
                //更新扩展项配置
                if ($this->Config->saveExtendConfig($_POST)) {
                    $this->success("更新成功！",  url('admin/Config/extend'));
                } else {
                    $error = $this->Config->getError();
                    $this->error($error ? $error : "配置更新失败！",  url('admin/Config/extend'));
                }
            }
        }else{
            $this->error("配置更新失败！",  url('admin/Config/extend'));
        }
    }

    public function extenddel(){
        $action = input('action');
        $db = db('ConfigField');
        if ($action) {
            if ($action == 'delete') {
                $fid = input('fid', 0, 'intval');
                if ($this->Config->extendDel($fid)) {
                    sys_cache('Config', NULL);
                    $this->success("扩展配置项删除成功！",  url('admin/Config/extend'));
                } else {
                    $error = $this->Config->getError();
                    $this->error($error ? $error : "扩展配置项删除失败！");
                }
            }
        }else{
            $this->error("扩展配置项删除失败！");
        }
    }
}
