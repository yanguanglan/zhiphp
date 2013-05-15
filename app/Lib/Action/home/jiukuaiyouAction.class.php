<?php

class jiukuaiyouAction extends frontendAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->id = $this->_get('id', 'intval');
        $this->assign("new_cmt_list",$this->jky_comment_mod->relation(true)
            ->where("status=1 and (select count(i.id) from ".table('jky_item')." as i where i.status=1 and i.id=".table("jky_comment").".item_id )>0")
            ->order("id desc")->limit(5)->select());
        $this->assign("recommend_list",$this->jky_item_mod->where("is_recommend=1 and status=1")->order("ordid asc,id desc")->limit(5)->select());
    }
    
    public function index()
    {
        $this->_config_seo(array('title'=>'九块邮_'.C('pin_site_name')));
        $type=$this->_get('type','trim','all');                            
        
        $this->assign('c1',intval($_REQUEST['c1']));
        $this->assign('c2',intval($_REQUEST['c2']));
        
        $where = "status=1";
                                  
        for($i=1;$i<3;$i++){
            eval("\$cid=intval(\$_REQUEST['c$i']);");
            if($cid==0)continue;
            
            $where.=" and (select count(c.item_id) from ".table("jky_cate_re")." as c where c.cate_id=$cid and c.item_id=".table("jky_item").".id)>0 ";
        }
                
        $this->_assign_list($this->jky_item_mod, $where, 15, true);    
        
        $this->assign('type',$type);
        
        $this->assign('type_list',$this->jky_cate_mod->where("pid=1 and status=1")->order("ordid desc")->select());
        $this->assign('cat_list',$this->jky_cate_mod->where("pid=2 and status=1")->order("ordid desc")->select());
        
        $this->display();
    }
    public function detail()
    {
        $info=$this->jky_item_mod->where("id=".$this->id)->find();
        $info['state']=get_jky_state($info);
        $this->assign('info',$info);
        $this->comment_list($this->id);
        $this->assign('like_list',$this->jky_item_mod->where("id<".$this->id)->limit(4)->select());
        $this->_config_seo(array('title'=>$info['title'] .'_九块邮_'.C('pin_site_name')));
        $this->display();
    }
    public function go()
    {
        header("Location:".$this->jky_item_mod->where("id=" . $this->id)->getField("url"));
    }
    public function comment() {
        foreach ($_POST as $key=>$val) {
            $_POST[$key] = Input::deleteHtmlTags($val);
        }
        $data = array();
        $data['item_id'] = $this->_post('id', 'intval');
        !$data['item_id'] && $this->ajaxReturn(0, L('invalid_item'));
        $data['info'] = $this->_post('content', 'trim');
        !$data['info'] && $this->ajaxReturn(0, L('please_input') . L('comment_content'));
        //敏感词处理
        $check_result = D('badword')->check($data['info']);
        switch ($check_result['code']) {
            case 1: //禁用。直接返回
                $this->ajaxReturn(0, L('has_badword'));
                break;
            case 3: //需要审核
                $data['status'] = 0;
                break;
        }
        $data['info'] = $check_result['content'];
        $data['uid'] = $this->visitor->info['id'];
        $data['uname'] = $this->visitor->info['username'];
        $data['add_time']=time();
        $data['pid']=$this->_post('pid','intval');
        //验证商品        
        $item = $this->jky_item_mod->field('id')->where(array('id' => $data['item_id'], 'status' => '1'))->find();
        !$item && $this->ajaxReturn(0, L('invalid_item'));
        //写入评论
        if (false === $this->jky_comment_mod->create($data)) {
            $this->ajaxReturn(0, $this->jky_comment_mod->getError());
        }
        $comment_id = $this->jky_comment_mod->add(filter_data($data));
        if ($comment_id) {
            $tag_arg = array('uid'=>$this->visitor->info['id'], 
                'uname'=>$this->visitor->info['username'], 
                'action'=>'comment');
            tag('comment_end', $tag_arg); 
                        
            $to_id=$this->_post('to_id','intval');
            if($to_id>0){
                $this->message_mod->add(array(
                    'ftid'=>$data['uid'],
                    'from_id'=>$data['uid'],
                    'from_name'=>$data['uname'],
                    'to_id'=>$this->_post('to_id','intval'),
                    'to_name'=>$this->_post('to_name','trim'),
                    'add_time'=>time(),
                    'info'=>$data['info'],
                ));    
            }
            $this->assign('cmt_list', array(
                array(
                    'id'=>$comment_id,
                    'uid' => $data['uid'],
                    'uname' => $data['uname'],
                    'info' => $data['info'],
                    'add_time' => time(),
                    'digg'=>0,
                    'burn'=>0,
                    'quote'=>$this->jky_comment_mod->where(array('id'=>$data['pid']))->find(),
                    'user'=>$this->user_mod->where(array('id'=>$data['uid']))->find(),
                )
            ));
            $resp['html'] = $this->fetch('ajax_comment_list');
            $resp['total']=$this->jky_comment_mod->where(array('item_id' => $data['item_id']))->count('id');  
                                 
            $this->ajaxReturn(1, L('comment_success'), $resp);
        } else {
            $this->ajaxReturn(0, L('comment_failed'));
        }
    }    
    public function comment_list($id){
        if(empty($id)){
            $id = $this->_get('id', 'intval');    
        }        
        !$id && $this->ajaxReturn(0, L('invalid_item'));        
        $res = $this->jky_item_mod->where(array('id' => $id, 'status' => '1'))->count('id');        
        !$res && $this->ajaxReturn(0, L('invalid_item'));        
        $pagesize = 8;
        $map = array('item_id' => $id);
        $count = $this->jky_comment_mod->where($map)->count('id');
        $pager = $this->_pager($count, $pagesize,__ROOT__."index.php?m=".MODULE_NAME."&a=".ACTION_NAME."&id=$id" );
                
        $cmt_list = $this->jky_comment_mod->relation(true)
            ->where($map)->order('id DESC')
            ->limit($pager->firstRow . ',' . $pager->listRows)->select();
        $floor=$count-$pager->firstRow;            
        foreach($cmt_list as $key=>$val){
            $cmt_list[$key]['quote']=$this->jky_comment_mod->where(array('id'=>$val['pid']))->find();
            $cmt_list[$key]['floor']=$floor;
            $floor--;
        }
        $this->assign('cmt_list', $cmt_list);
        $data = array();
        $data['list'] = $this->fetch('ajax_comment_list');
        $data['page'] = $pager->fshow();
        $data['total']=$count;
        $this->assign('cmt_page',$data['page']);
        $this->assign('cmt_total',$data['total']); 
          
        if(IS_AJAX){                 
            $this->ajaxReturn(1,'',$data);    
        }
    }    
    public function digg_burn(){        
        $id=$this->_get('id','intval');
        $type=$this->_get('type','trim');
        
        if(in_array($type,array('digg','burn'))){        
            $this->post_comment_mod->where(array('id'=>$id))->setInc($type);
            $this->ajaxReturn(1,'',$this->post_comment_mod->where(array('id'=>$id))->getField($type));    
        }
    }
    protected function _parse_assign_list($list){        
        foreach($list as $key=>$val){
            $list[$key]['state']=get_jky_state($val);
        }
        return $list;
    }
}
