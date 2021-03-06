<?php

// +----------------------------------------------------------------------
// | rootCMS 内容模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rootcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

namespace app\content\model;

use think\Model;

/**
 * ContentData模型，与content是关联模型
 *
 * @author wb
 */
class ContentData extends Model {
    
    //静态成品变量 保存全局实例
    static $_instance = NULL;
    
    //当前模型id
    public $modelid = "";
    
    protected  $parent = NULL;

    public function __construct($name="",$modelid=0) {
        if(is_array($name)|| is_object($name)){
            $this->data = $name;
            if(isset($this->data['modelid'])){
                $modelid = $this->data['modelid'];
            }elseif(isset($this->data['catid'])){
                $modelid = getCategory($this->data['catid'], "modelid");
            }
        }elseif(!empty($name)){
            $this->name = $name."_data";
        }else{
            exception("模型名称不能为空！");
        }
        if(!empty($modelid)){
            $this->modelid = $modelid;
            $modelCache = sys_cache("Model");
            if (empty($modelCache[$this->modelid])) {
                return false;
            }
            if(! $this->parent instanceof Content){
                $this->parent = \app\content\model\Content::getInstance($modelid);
            }
            //根据模型id重新赋值name
            $this->name = $modelCache[$this->modelid]['tablename']."_data";
            
            $this->connection = config("database");
            $this->table = config("database.prefix").$this->name;
            $this->class = get_class($this);
            $this->db()->name($this->name);
            $this->db()->table($this->table);
            $this->pk = $this->getPk($this->name);
        }
    }
    
    public function getRelation($id=0){
        $result = [];
        if($id>0){
            // 执行关联定义方法
            $result = $this->where("id", $id)->find()->toArray();
        }
        return $result;
    }
}
