<?php

class jky_itemModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '{%article_title_empty}'),
    );
    //关联关系
    protected $_link = array(
        'orig' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'jky_orig',
            'foreign_key' => 'orig_id',
        ),           
    );
}