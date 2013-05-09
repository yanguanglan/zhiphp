<?php

class jky_commentModel extends RelationModel
{
    protected $_auto = array (array('add_time','time',1,'function'));
    //关联关系
    protected $_link = array(
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
        ),        
        'item'=> array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'jky_item',
            'foreign_key' => 'item_id',
        ),   
    );
    /**
     * 新增评论更新商品评论数和评论缓存字段
     */
    protected function _after_insert($data,$options) {
        $jky_item_mod = D('jky_item');
        $jky_item_mod->where(array('id'=>$data['item_id']))->setInc('comments');
    }
}