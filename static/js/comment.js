/**
 * @name 商品评论
 * @author andery@foxmail.com
 * @url http://www.ZhiPHP.com
 */
;(function($){
    $.ZhiPHP.comment = {
        settings: {
            container: '#J_comment_area',
            page_list: '#J_cmt_list',
            page_bar: '.J_cmt_page',
            pub_content: '#J_cmt_content',
            pub_btn: '#J_cmt_submit',
            cmt_total:'#J_cmt_total',
            digg_btn:'.J_digg',
            burn_btn:'.J_burn',
            at_btn:'.J_at',
            quote_btn:'.J_quote',
            quote_tip:'#J_quote_tip',
            m:'post',
            face:'.J_face',
            face_list:['还行','看棒','膜拜','工作','勾引','给力','不给力','不高兴','嘻嘻','开心','伤心','泪奔','愤愤','嘟嘟','崩溃','犯困','狂汗'
                    ,'鬼脸','生病','yy','一般般','得瑟','鄙视','晕眩','恶心','心动','无聊','糗','害羞','坚持','惊讶','囧','酷狗','贱笑'
                    ,'倒霉','委屈','疑问','嚎叫','拜拜','兔星星','春运','点炮','喜得贵子','红包','圣诞']
        },
        init: function(options){            
            options && $.extend($.ZhiPHP.comment.settings, options);
            if($(this.settings.container).size()>0){                
                $.ZhiPHP.comment.list();
                $.ZhiPHP.comment.publish();
                $.ZhiPHP.comment.init_face();    
                this.parse_face();                
            }            
        },
        //表情
        init_face:function(){
            var set=this.settings;
            var faceSel=this.settings.face;  
            var context=this;
            var html='  <div class="content">\
                            <div class="border">\
                                <div class="close"></div>\
                                <div class="face_title">\
                                    <span>默认</span>\
                                </div>\
                                <div class="list clearfix">';
            for(var i=0;i<this.settings.face_list.length;i++){
                html+='             <a><img src="'+def.url_prefix+'static/images/face/'+(i+1)+'.gif" title="'+this.settings.face_list[i]+'"/></a>';
            }
            html+='             </div>\
                            </div>\
                        </div>';
            $(this.settings.face).append(html);  
            
            $(faceSel+' .J_face_btn').click(function(){
                $(faceSel+' .content').show();
            });  
            $(faceSel+' .close').click(function(){
                $(faceSel+' .content').hide();
            });     
            $(faceSel+' .list img').click(function(){
                var oldContent=$(set.pub_content).val().replace(/\s/g,'');   
                var rangeData=context.getCursorPosition($(set.pub_content)[0]);                                                
                var newContent=oldContent.substring(0,rangeData.start)+"["+$(this).attr('title')+"]"+oldContent.substring(rangeData.end);   
                $(set.pub_content).val(newContent.substr(0,$(set.pub_content).attr("data-max")));     
                $(faceSel+' .content').hide();                
            });    
            $('body').click(function(e){
                var offset=$(faceSel+' .content').offset();
                offset.top=offset.top-30;
                offset.right=offset.left+$(faceSel+' .content').width()+30;
                offset.bottom=offset.top+$(faceSel+' .content').height();
                //console.log({x:e.pageX,y:e.pageY},offset,context.in_area({x:e.pageX,y:e.pageY},offset));
                
                if(!context.in_area({x:e.pageX,y:e.pageY},offset)){
                    $(faceSel+' .content').hide();
                }
            });                     
        },
        parse_face:function(){
            var face_list=this.settings.face_list;
            for(var i=0;i<face_list.length;i++){
               $(this.settings.page_list).html($(this.settings.page_list).html().replace("["+face_list[i]+"]",'<img src="'+def.url_prefix+'static/images/face/'+(i+1)+'.gif" title="'+this.settings.face_list[i]+'"/>'));                
            }
        },
        in_area:function(point,area){
            return point.x>=area.left&&point.x<=area.right&&point.y>=area.top&&point.y<=area.bottom;    
        },      
        //列表
        list: function(){
            var id = $(this.settings.container).attr('data-id');
            //this.load(PINER.root + '?m='+$.ZhiPHP.comment.settings.m+'&a=comment_list&id='+id);
            var s = $.ZhiPHP.comment.settings;
            $('li', $(s.page_list)).live({
                mouseover: function(){
                    $(this).addClass('hover');
                },
                mouseout: function(){
                    $(this).removeClass('hover');
                }
            });
            $('a', $(s.page_bar)).live('click', function(){
                var url = $(this).attr('href');
                $.ZhiPHP.comment.load(url);
                return false;
            });
            $(s.digg_btn).live('click',function(){
                
                var $this=$(this);
                var id=$this.attr("data-id");
                if(cookie_exist('digg_burn',id)){
                    $.ZhiPHP._tip({content:'已经顶(踩)了', status:false});
                    return;   
                }
                $.get('index.php?m='+s.m+'&a=digg_burn&type=digg',{id:id},function(data){
                    if(data.status>0){
                        $this.text('顶('+data.data+')');
                    }
                },'json');                                
            });
            $(s.burn_btn).live('click',function(){
                var $this=$(this);
                var id=$this.attr("data-id");
                if(cookie_exist('digg_burn',id)){
                    $.ZhiPHP._tip({content:'已经顶(踩)了', status:false});                    
                    return;   
                }
                $.get('index.php?m='+s.m+'&a=digg_burn&type=burn',{id:id},function(data){
                    if(data.status>0){
                        $this.text('踩('+data.data+')');
                    }
                },'json');                                
            });
            $(s.at_btn).live('click',function(){
                if(check_login()){
                    $(s.pub_content).val($(this).attr('data-at')+' ');
                    $(s.pub_content).attr({
                        'data-to_id':$(this).attr('data-to_id'),
                        'data-to_name':$(this).attr('data-to_name')
                    });
                    $.scrollTo('#J_messagebox',300);
                }
            });
            $(s.quote_btn).live('click',function(){
                if(check_login()){
                    var pid=$(this).attr("data-id");
                    $(s.quote_tip).html("引用\""+$(this).attr('data-uname')+"\":<a id='J_cancel_quote'>取消引用</a>");
                    $(s.pub_content).attr({
                        'data-pid':pid
                    });
                    $.scrollTo('#J_messagebox',300);    
                }
            });
            $('#J_cancel_quote').live('click',function(){
                $(s.quote_tip).html('');
                $(s.pub_content).attr({
                    'data-pid':0
                });
            });
        },
        load:function(url){
            var s = $.ZhiPHP.comment.settings;
            $.getJSON(url, function(result){
                if(result.status == 1){
                    $(s.page_list).html(result.data.list);
                    if($.trim(result.data.page).length>0){
                        $(s.page_bar).show().html(result.data.page);    
                    }else{
                        $(s.page_bar).hide();
                    }                    
                    $(s.cmt_total).html(result.data.total);
                    $.ZhiPHP.comment.parse_face();
                }else{
                    $.ZhiPHP.tip({content:result.msg, icon:'error'});
                }
            });  
        },
        //发表评论
        publish: function(){
            var s = $.ZhiPHP.comment.settings;
            $(s.pub_btn).live('click', function(){
                if(!$.ZhiPHP.dialog.islogin()){
                    $.ZhiPHP._tip({
            			content: "请登录再评论",
            			status: false
            		});
                    return;
                }
                var id = $(s.container).attr('data-id'),
                    dv = $(s.pub_content).attr('def-val'),
                    to_id=$(s.pub_content).attr('data-to_id'),
                    to_name=$(s.pub_content).attr('data-to_name'),
                    pid=$(s.pub_content).attr('data-pid'),
                    content = $(s.pub_content).val();
                if(content == dv){
                    $(s.pub_content).focus();
                    return false;
                }
                $.ajax({
                    url: PINER.root + '?m='+$.ZhiPHP.comment.settings.m+'&a=comment',
                    type: 'POST',
                    data: {
                        id: id,
                        content: content,
                        to_id:to_id,
                        to_name:to_name,
                        pid:pid
                    },
                    dataType: 'json',
                    success: function(result){
                        if(result.status == 1){
                            $(s.pub_content).val('');
                            $(s.page_list).prepend(result.data.html);
                            $(s.cmt_total).text(result.data.total);
                            $.scrollTo(s.page_list,300,{ offset:{ top:-100} });
                            $.ZhiPHP.comment.parse_face(); 
                        }else{
                            $.ZhiPHP.tip({content:result.msg, icon:'error'});
                        }
                    }
                });
            });
        },
        getCursorPosition:function(textarea) {
            var rangeData = {text: "", start: 0, end: 0 };
                textarea.focus();
            if (textarea.setSelectionRange) { // W3C
                rangeData.start= textarea.selectionStart;
                rangeData.end = textarea.selectionEnd;
                rangeData.text = (rangeData.start != rangeData.end) ? textarea.value.substring(rangeData.start, rangeData.end): "";
            } else if (document.selection) { // IE
                var i,
                    oS = document.selection.createRange(),
                    // Don't: oR = textarea.createTextRange()
                    oR = document.body.createTextRange();
                oR.moveToElementText(textarea);
        
                rangeData.text = oS.text;
                rangeData.bookmark = oS.getBookmark();
        
                // object.moveStart(sUnit [, iCount])
                // Return Value: Integer that returns the number of units moved.
                for (i = 0; oR.compareEndPoints('StartToStart', oS) < 0 && oS.moveStart("character", -1) !== 0; i ++) {
                    // Why? You can alert(textarea.value.length)
                    if (textarea.value.charAt(i) == '\n') {
                        i ++;
                    }
                }
                rangeData.start = i;
                rangeData.end = rangeData.text.length + rangeData.start;
            }
        
            return rangeData;
        }        
    };
    
})(jQuery);
