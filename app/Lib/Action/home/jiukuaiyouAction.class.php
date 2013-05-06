<?php

class jiukuaiyouAction extends frontendAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->id = $this->_get('id', 'intval');
        
    }
    public function index()
    {
        $where = "1";
        if ($cid = $this->_get('cid', 'intval'))
        {
            $where .= " and cid=$cid";
        }
        $this->_assign_list($this->jky_item_mod, $where, 15, true);
        $this->display();
    }
    public function detail()
    {
        $this->assign('info',$this->jky_item_mod->where("id=".$this->id)->find());
        $this->display();
    }
    public function go()
    {
        header("Location:".$this->jky_item_mod->where("id=" . $this->id)->getField("url"));
    }
}
