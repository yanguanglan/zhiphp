<?php 
return array (
  'URL_MODEL' => 0,
  'URL_HTML_SUFFIX' => '.html',
  'URL_PATHINFO_DEPR' => '-',
  'URL_ROUTER_ON' => true,
  'URL_ROUTE_RULES' => 
  array (    
    '/^go-(\d+).html$/' => 'index/go?id=:1',
    '/^detail-(\d+).html$/' => 'post/index?id=:1',
    '/^baoliao.html$/' => 'post/submit',
    '/^cate-(\d+).html$/' => 'post_cate/index?id=:1',
    '/^article-(\d+).html$/' => 'article/index?id=:1',
    '/^mall.html$/' => 'mall/index',
    '/^mall-(\d+).html$/' => 'mall/info?id=:1',
    '/^login.html$/' => 'user/login',
    '/^register.html$/' => 'user/register',
    '/^logout.html$/' => 'user/logout',
    
    '/^faq.html$/' => 'help/faq?cate_id=19',
    '/^fanli.html$/' => 'help/faq?cate_id=20',
    '/^flink.html$/' => 'help/flink',
    '/^about.html$/' => 'help/page?id=2',
    '/^contact.html$/' => 'help/page?id=3',
    '/^partner.html$/' => 'help/page?id=4',
    '/^weixin.html$/' => 'help/page?id=12',
    '/^friends.html$/' => 'help/page?id=23',
    //第三方登录
    '/^qq.html$/' => 'oauth/index?mod=qq',
	'/^qq-cb.html$/' => 'oauth/callback?mod=qq',
    '/^sina.html$/' => 'oauth/index?mod=sina',
	'/^sina-cb.html$/' => 'oauth/callback?mod=sina',
    '/^taobao.html$/' => 'oauth/index?mod=taobao',
	'/^taobao-cb.html$/' => 'oauth/callback?mod=taobao',
	
    //分页
    '/^index-p-(\d+).html$/' => 'index/index?p=:1',
    '/^cate-(\d+)-(\d+).html$/' => 'post_cate/index?id=:1&p=:2',
    //用户中心
    '/^user.html$/' => 'user/index',
    '/^profile.html$/' => 'user/profile',
    '/^bind.html$/' => 'user/bind',
    '/^binding.html$/' => 'user/binding',
    '/^password.html$/' => 'user/password',
    
    '/^comments.html$/' => 'user/comments',
    '/^comments-(\d+).html$/' => 'user/comments?p=:1',
    
    '/^favs.html$/' => 'user/favs',
    '/^favs-(\d+).html$/' => 'user/favs?p=:1',
    
    '/^baoliao-list.html$/' => 'user/baoliao',
    '/^baoliao-(\d+).html$/' => 'user/baoliao?type=:1',
    '/^baoliao-(\d+)-(\d+).html$/' => 'user/baoliao?type=:1&p=:2',
    
    '/^score_log.html$/' => 'user/score_log',
    '/^score_log-(\d+).html$/' => 'user/score_log?p=:1',
    
    '/^message.html$/' => 'user/message',
    '/^message-(\d+).html$/' => 'user/message?p=:1',
    
    '/^(\w+).html$/' => 'post/index?post_key=:1',
    '/^ad-(\d+).html$/' => 'advert/tgo?id=:1',
  ),
);