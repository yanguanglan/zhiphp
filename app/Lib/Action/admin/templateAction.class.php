<?php

class templateAction extends backendAction
{
    /**
     * 模板列表
     */
    public function index() {
        $config_file = CONF_PATH . 'home/config.php';
        $config = include $config_file;        
        if(IS_POST){
            foreach($_REQUEST['tpl'] as $key=>$val){
                if (!empty($_FILES['site_logo']['name'][$key])) {                        
                    move_uploaded_file($_FILES["site_logo"]["tmp_name"][$key],APP_PATH. "TPL/home/$val/public/images/logo.jpg");
                }    
            }
            if ($dirname = $this->_post('dirname', 'trim')) {
                //写入配置文件
                $config['DEFAULT_THEME'] = $dirname;
                file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
                $obj_dir = new Dir;
                is_dir(CACHE_PATH.'home/') && $obj_dir->delDir(CACHE_PATH.'home/');
                @unlink(RUNTIME_FILE);
            }
            $this->success(L('operation_success'),U(MODULE_NAME.'/'.ACTION_NAME));        
        }else{
            $tpl_dir = TMPL_PATH.'home/';
            $opdir = dir($tpl_dir);
            $template_list = array();
            while (false !== ($entry = $opdir->read())) {
                if ($entry{0} == '.') {
                    continue;
                }
                if (!is_file($tpl_dir . $entry . '/info.php')) {
                    continue;
                }
                $info = include_once($tpl_dir . $entry . '/info.php');
                $info['preview'] = TMPL_PATH . 'home/' . $entry . '/preview.gif';
                $info['dirname'] = $entry;
                $template_list[$entry] = $info;
            }
            $this->assign('template_list',$template_list);
            //当前使用的模板
            $this->assign('def_tpl', $config['DEFAULT_THEME']);
            $this->display();            
        }
    }
}