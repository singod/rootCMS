<?php

// +----------------------------------------------------------------------
// | rootCMS 自定义页面
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rootcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace Template\Controller;

use Common\Controller\AdminBase;

class CustompageController extends AdminBase {

    //显示自定义页面列表 
    public function index() {
        $this->basePage('Customtemp', '', array('tempid' => 'DESC'));
    }

    //增加自定义页面 
    public function add() {
        if (IS_POST) {
            $model =  model('Template/Customtemp');
            if ($model->create($_POST)) {
                $tempid = $model->add();
                if ($tempid) {
                    $this->Html->createHtml((int) $tempid);
                    $this->success("添加自定义页面成功！",  url('Custompage/index'));
                } else {
                    $this->error("添加自定义页面失败！");
                }
            } else {
                $this->error($model->getError());
            }
        } else {
            $this->display();
        }
    }

    //删除自定义页面 
    public function delete() {
        $tempid = input('get.tempid');
        $model =  model('Template/Customtemp');
        $r = $model->where(array("tempid" => $tempid))->find();
        if ($r) {
            if ($r['tempname'] && $r['temppath']) {
                unlink(SITE_PATH . $r['temppath'] . $r['tempname']);
            }
            $status = $model->delete();
            if ($status !== false) {
                $this->success('删除成功！');
            } else {
                $this->error('删除失败！');
            }
        } else {
            $this->error('需要删除的自定义页面不存在！');
        }
    }

    //编辑自定义页面 
    public function edit() {
        $model =  model('Template/Customtemp');
        if (IS_POST) {
            if ($model->create($_POST)) {
                $tempid = input('post.tempid');
                $status = $model->where(array("tempid" => $tempid))->save();
                if (false !== $status) {
                    $status = $this->Html->createHtml((int) $tempid);
                    $this->success("自定义页面编辑成功！",  url("Custompage/index"));
                } else {
                    $this->error("编辑自定义页面失败！");
                }
            } else {
                $this->error($model->getError());
            }
        } else {
            $tempid = input('get.tempid');
            $r = $model->where(array("tempid" => $tempid))->find();
            if (empty($r)) {
                $this->error("需要编辑的自定义页面不存在！");
            }
            $r['temptext'] = \Input::forTarea($r['temptext']);
            $this->assign($r);
            $this->display();
        }
    }

    //生成自定义页面 
    public function createhtml() {
        $model =  model('Template/Customtemp');
        if (IS_POST) {
            $tempid = $_POST['tempid'];
            foreach ($tempid as $id) {
                $this->Html->createHtml((int) $id);
            }
            $this->success('更新完成！',  url('Custompage/index'));
        } else {
            if (isset($_GET['tempid'])) {
                $tempid = input('get.tempid');
                $r = $model->where(array("tempid" => $tempid))->find();
                if ($r) {
                    if ($this->Html->createHtml($r)) {
                        $this->success("更新完成！",  url("Custompage/index"));
                    } else {
                        $this->error("更新失败！",  url("Custompage/index"));
                    }
                } else {
                    $this->error("该自定义页面不存在！",  url("Custompage/index"));
                }
            } else {
                //更新全部
                $r = $model->select();
                foreach ($r as $k => $v) {
                    $this->Html->createHtml($v);
                }
                $this->success('更新完成！',  url("Custompage/index"));
            }
        }
    }

}
