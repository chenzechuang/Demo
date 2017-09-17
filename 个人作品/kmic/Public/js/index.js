

/* 城市选择 */ 

function cityPage(city) {

    if (city == '北京市' || city == '上海市' || city == '天津市' || city == '重庆市') {
        where['area'] = 'p||'+ city;
    } else if (city == '全国') {
        where = [];
    } else if (city != null && city.indexOf('省') != -1) {
        where['area'] = 'p||'+ city;
    }else {
        where['area'] = 'c||'+ city;
    }   
    $('.list').html('');
    getPage(page);
}

function citySelect(event, that) {
    event.preventDefault();
    page = 0;
    sessionStorage.setItem("page", page);
    var element = that.nodeName;

    if (element.toLowerCase() == 'dd') {
        city = $(that).html();
        $(".picker-box").hide();
        $(".pro-picker").show();
        $('.city').val(city);
    } else if (element.toLowerCase() == 'li') {
        if ($(that).index() == 1) {
            city = $('.select-pro').html() + '省';
            $('.city').val(city);
        } else {
            city = $('.city').val();
        }
    }

    sessionStorage.setItem("city", city);
    cityPage(city);
}

/* 下拉加载 */

function loaddata() { 
    //下拉加载
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());

    //浏览器的高度加上滚动条的高度 
    //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
    if ($(document).height() <= totalheight) {      
        //加载数据
        if (isLoading == 0 && isEnd != 1) {
            page++;
            getPage(page,where);
        }
    } 
}

/* 列表返回 */

function ifBack() {
    page = sessionStorage.getItem("page");
    var _list = sessionStorage.getItem("list");
    var _offset = sessionStorage.getItem("offsetTop");
    var _city = sessionStorage.getItem("city");
    $('.city').val(_city);
    cityPage(_city);

    $('.list').html(_list);
    $(document).scrollTop(_offset); 
}

/**
 * 通告
 */

$(function() {
    //限制300字
    var more_info = "";
    var more_info_len = null;
    $('.more-info textarea').keyup(function(event) {
        more_info = $(this).val().length;
        more_info_len = $(this).next().find('span');

        more_info_len.text(more_info);
        if (more_info > 300) {
            more_info_len.text(300);
            $(this).val($(this).val().substring(0, 300));
        }
    });
});

/* 删麦主 */

function deleteUserData(event, id){
    if(confirm("确定要删除数据吗？")) {
        $.ajax({
            type: 'GET',
            url: "?m=web&a=deleteUserData",
            data: {'id':id},
            success: function(result){
                alert('删除成功');
                _list = sessionStorage.getItem("list");
                var removeItem = $('.list').find('#l'+id)[0].outerHTML;
                var _list = _list.replace(removeItem, '');
                sessionStorage.setItem("list", _list);
                window.location.href=window.location.href+"&uncache="+123*Math.random();
            },
            dataType: 'json'
        });
    }
    event.stopPropagation();
}
/* 删通告 */

function deleteData(event, id){
    if(confirm("确定要删除数据吗？")) {
        $.ajax({
            type: 'GET',
            url: "?m=web&a=deleteData",
            data: {'id':id},
            success: function(result){
                alert('删除成功');
                _list = sessionStorage.getItem("list");
                var removeItem = $('.list').find('#l'+id)[0].outerHTML;
                var _list = _list.replace(removeItem, '');
                sessionStorage.setItem("list", _list);
                window.location.href=window.location.href+"&uncache="+123*Math.random();
            },
            dataType: 'json'
        });
    }
    event.stopPropagation();

}
/**
 * 发通告
 */

function newGoods(openid) {
    $.ajax({
        type: 'GET',
        url: "?m=web&a=goods&action=new&openid=" + openid,
        success: function(result){
            //console.log(result);
            if (result.code == 1) {
                if(confirm(result.msg)) {
                    location.href= '?m=Center&c=profile&a=member&openid=' + openid;
                }
            
            } else {
                location.href = '?m=web&c=index&a=goodsNew&openid=' + openid;
            }
        },
        beforeSend: function(){
        },
        dataType: 'json'
    });
}

/**
 * 筛选
 */

$(function() {
    $('.filter').click(function(event) {
        $(this).children().slideDown();
        $('.bg').show();
       
    });

    $('.filter-close').click(function(event) {
        $('.filter-content').slideUp();
        $('.bg').hide();
        return false;

    });

    $('.filter-item li').click(function(event) {
        $(this).toggleClass('selected')
            .siblings().removeClass('selected');
    });

    $('.filter-menu .reset').click(function(event) {
        $('.filter-item li').removeClass('selected');
    });

    $('.filter-menu .confirm').click(function(event) {
         
        page = 0;

        if ($('.sex').length > 0) {
            var sex = $('.sex .selected').index();
            if (sex == 2) {
                sex = -1;
            }
            where['sex'] = sex;
        } 
        
        if ($('.artist-sex').length > 0) {
            var artist_sex = $('.artist-sex .selected').index();
            if (artist_sex == 0) {
                sex = 1;
            } else if (artist_sex == 1)  {
                sex = 2;
            } else {
                sex = "";
            }
            where['sex'] = sex;
        }

        if ($('.size').length > 0) {
            var size = $('.size .selected').index();
            if (size == 0) {
                size = 1;
            } else if (size == 1)  {
                size = 2;
            } else if (size == 2)  {
                size = 3;
            } else if (size == 3)  {
                size = 0;
            } else {
                size = "";
            }
            where['size'] = size;
        }    


        if ($('.price').length > 0) {
            var price_index = $('.price .selected').index();
            var price = '';
            switch(price_index) {
                case 0:
                price = '1';
                break;

            case 1:
            price = '1-3';
            break;

            case 2:
            price = '3-10';
            break;

                case 3:
                price = '0';
                break;
            }
        } else if ($('.goods-price').length > 0) {
            var price_index = $('.goods-price .selected').index();
            var price = '';
            switch(price_index) {
                case 0:
                price = '1';
                break;

                case 1:
                price = '1-3';
                break;

                case 2:
                price = '3-10';
                break;
            }
        }
        where['price'] = price;

        if ($('.goods-new').length > 0) {
            var g_new = $('.goods-new .selected').index();
            if (g_new == 0) {
                g_new = '10';
            } else if (g_new == 1)  {
                g_new = '9-10';
            } else if (g_new == 2)  {
                g_new = '8-9';
            } else if (g_new == 3)  {
                g_new = '0';
            } else {
                g_new = "";
            }
            where['new'] = g_new;
        } 
        
        $('.list').html('');
        getPage();

        $('.filter-content').slideUp();
        $('.bg').hide();
        return false;
    });
});

$(function() {
    $(".form-item li").click(function() {
        $('#activity_type_val').val($(this).text());
        $(this).not('.input-budget').not('.add').toggleClass('selected');
        if (this.className == 'input-budget') {
            $(this).siblings().find('a').removeClass('selected');
        }
        $(this).siblings().removeClass('selected');

        if ($(this).parent().attr('id') == 'sex') {
            switch($(this).index()) {
                case 0:
                $('#sex').val('0');
                break;
                case 1:
               $('#sex').val('1');
                break;
                case 2:
                    $('#sex').val('-1');
                    break;
            }
        }

        if ($(this).parent().attr('id') == 'size') {
            switch($(this).index()) {
                case 0:
                    $('#size').val('1');
                    break;
                case 1:
                    $('#size').val('2');
                    break;
                case 2:
                    $('#size').val('3');
                    break;
                case 3:
                    $('#size').val('0');
                    break;
            }
        }
    });

    $('#number a').click(function(event) {
        var number = $(this).siblings('input').val();
        if ($(this).index() == 1) {
            number--;
            if (number == 0) {
                return;
            }
            $(this).siblings('input').val(number);
        }
        if ($(this).index() == 3) {
            number++;
            $(this).siblings('input').val(number);
        }

    });
    var flag = 0;
    $('#budget a').click(function(event) {
        if (flag == 0) {
            $(this).addClass('selected');
            $(this).siblings('input').prop('readonly', true);
            $(this).siblings('input').val('自报');
            flag = 1;
        } else {
            $(this).removeClass('selected');
            $(this).siblings('input').prop('readonly', false);
            $(this).siblings('input').val('');
            flag = 0;
        }
        
    });
});

/**
 * 通告详情
 */

function announcementPage(openid, aid, mine) {
    if (mine != undefined) {
        window.location.href = "?m=web&a=announcementPage&openid="+ openid +"&annid=" + aid + "&mine=mine"; 
    } else {
        window.location.href = "?m=web&a=announcementPage&openid="+ openid +"&annid=" + aid; 
    }
}


/**
 * 麦主详情
 */

function artistDetail(openid, uid, mine) {

    if (mine != undefined) {
        window.location.href = "?m=web&a=artistPage&openid="+ openid +"&uid=" + uid + "&mine=mine"; 
    } else {
        window.location.href = "?m=web&a=artistPage&openid="+ openid +"&uid=" + uid;
    }
	
}

/**
 * 物品详情
 */

function goodsDetails(openid, gid, mine) {
    if (mine != undefined) {
        window.location.href = "?m=web&a=goodsPage&openid="+ openid +"&gid=" + gid + "&mine=mine"; 
    } else {
        window.location.href = "?m=web&a=goodsPage&openid="+ openid +"&gid=" + gid;
    }
}



//报价
function open_f(event,openid,title,price,time,id,type,nickname,headimgurl,status){
    // ga('send', 'event', 'ann', 'offerOpen', id);
    var sendingTitle = title;
    var selectedID = id;
    if (price == 0) {
        $('.f_windown1 p').html('您的报价 (单位/元)');
        $('.f_windown1 input').show();
        $('.f_windown1 .sent').html('立即报名');
    } else {
        $('.f_windown1 p').html('确定报名吗？');
        $('.f_windown1 input').hide();
        $('.f_windown1 .sent').html('确定报名');
    }
    $('.f_windown1').css('display','block');
    $('.bg').css('display','block');

    $('#f_close1').click(function(){
        $('.f_windown1').hide();
        $('.bg').hide();
    });	

    $('.sent').click(function(){

        if (price == 0) {
            price = $("input[name='price']").val();
        }

        if ( $("input[name='price']").is(":visible") && $("input[name='price']").val()=='') {
            alert('请输入一个价钱');
            return;
        }
        // ga('send', 'event', 'ann', 'offerSent', $("input[name='price']").val());
        $.ajax({
            type: 'GET',
            url: "?m=web&action=offer&openid=" + openid,
            data: {'price':price,'aid':selectedID,'title':sendingTitle,'time':time},
            success: function(result){
                $('.show').fadeOut();
                //console.log(result);
                if (result.code == 1) {
                    alert(result.msg);
                } else if (result.code == 2) {
                    if(confirm(result.msg)) {
                        location.href= '?m=Center&c=profile&a=member&openid=' + openid;
                    }
                } else if (result.code == 3) {
                    if(confirm(result.msg)) {
                        location.href = '?m=Center&c=Profile&a=setMessage&openid=' + openid;
                    }
                } else {
                    alert('报名成功，请等待对方与您联系。');
                    location.href = '?m=web&c=index&openid=' + openid;
                }
                $('.f_windown1').css('display','none');
                $('.bg').css('display','none');
            },
            beforeSend: function() {
                $('.show').fadeIn();
            },
            dataType: 'json'
        }); 

        return false;
    });
    event.stopPropagation();
}


/**
 * 用户信息加载
 */

function loadMessage(uid) {

    $('#f_close1').click(function(){
        $('.f_windown1').hide();
        $('.bg').hide();
    });

    var text = $('.text');
    var audio = $('#mp3Btn')[0];
    var picNum = 0;

    $.ajax({
        type: 'GET',
        url: "?m=Web&a=artistDetails",
        data: {'uid':uid},
        complete: function(result){
        //console.log('complete:function');
        },
        success: function(result){
            var activity_box = $('.info-activity');
            console.log(result);

           
            //头部
            if (result.sex == 1) {
                $('.artist-name').html('<img src="./Public/img/icon/male@2x.png">' + result.name);
            } else {
                $('.artist-name').html('<img src="./Public/img/icon/female@2x.png">' + result.name);
            }
            
            $('.head-img').attr({
                'src': './Public/img/img_cover.png',
                'style': 'background: url(\''+ result.headimgurl +'\') no-repeat center/cover;'
            });
            
            if (result.userDetail) {
                if (result.city) {
                    $('.artist-id').html(result.city + ' | 麦麦号 : ' + result.userDetail.uid);    
                } else {
                    $('.artist-id').html('麦麦号 : ' + result.userDetail.uid);    
                }
                
            }
            //照片展示
            if (result.additional.length > 0) {
                var picId = "";
                $.each(result.additional, function(i,val){
                    //形象照片修改
                    $('.draw-pics').prepend('<li id="'+picNum+'"><div class="delete" onclick="deletePic(\''+val.value+'\','+picNum+','+val.id+')"></div><img class="artist-img" src="'+val.value+'"></li>');  
                    picId += ","+val.id;
                    picNum++;
                    if (picNum >= 9) {
                        $('.add').css('display','none');
                    }

                    //形象展示
                    $('#Gallery').append('<li><a href="'+ val.value + '"  data-imagelightbox="e" class="picItem">\n\
                            <img class=\"art-img lazy_img\" src="./Public/img/img_cover.png" style="background: url(\''+ val.value + '\') no-repeat center/cover;" alt="照片展示"></a></li>');   
                    
                });
                $("#picID2").val(picId);
                // $('#artist_img img').fsgallery();
                if ($('#Gallery').length > 0) {
                    (function(window, $, PhotoSwipe){
                        var options = {};
                        $("#Gallery a").photoSwipe(options);
                    }(window, window.jQuery, window.Code.PhotoSwipe));
                    
                    $.mCustomScrollbar.defaults.theme="light-2"; 
                    
                    $("#Gallery").mCustomScrollbar({
                        axis:"x",
                        advanced:{autoExpandHorizontalScroll:true}
                    });
                }
                
            } else {
                $('.info-img').remove();
            }
            
            //个人信息
            if (result.userDetail) {
                $('.stature').html(result.userDetail.stature + 'cm');
                $('.experience').html(result.userDetail.experience);
                $('.organization').html(result.userDetail.organization);
                $('.school').html(result.userDetail.school);
                $('.honour').html(result.userDetail.honour);
                $('.info').html(result.userDetail.info);
                $('.price').html('期望酬劳：' + result.userDetail.price)
                $('.signup').append('<button type="button" class="btn email_button" onclick="toUser(\''+result.userDetail.id+'\',\''+result.id+'\',\''+result.fromuid+'\');">留言</button>');
            }

            //没有信息的栏目隐藏
            $.each(text, function(index, val) {
                if (val.innerHTML == "") {
                    text.eq(index).parent().hide().next().remove();
                }
            });

            if (result.userDetail.stature == "0") {
                $('.stature').parent().remove();
            }

            if ($('.info-message div:visible').length == 0) {
                $('.info-message').remove();
            }

            /*//声音
            if (result.artistVoice) {
                audio.src = result.artistVoice.value;
                $('.voice a').click(function(event) {
                    if(audio.paused){ 
                        //如果当前是暂停状态
                        $(this).css({"background":"url('./Public/img/icon/icon_playnow@2x.png') no-repeat left center / contain"});
                        console.log('play');
                        audio.play(); //播放
                        return false;
                    } else {
                        //当前是播放状态
                        $(this).css({"background":"url('./Public/img/icon/icon_play@2x.png') no-repeat left center / contain"});
                        console.log('pause');
                        audio.pause(); //暂停
                        return false;
                    }
                });
            } else { //没有声音隐藏
            */
                $('.info-voice').prev().remove();
                $('.info-voice').remove();
            // }

            //没有活动经历隐藏
            if (result.userActivity.length == 0) {
                activity_box.prev().remove();
                activity_box.remove();

            } else {
                $.each(result.userActivity, function(i,val) {
                    OutputData(activity_box, i, val);

                }); 
                if ($(".user-activity").length == 0) {
                    for (var i = 0; i < result.userActivity.length; i++) {
                        var x = "#Gallery"+i;
                        if ($(x).find("a").length > 0) {
                            (function(window, $, PhotoSwipe){
                                var options = {};
                                $(x).find("a").not('.a_video').photoSwipe(options);
                            }(window, window.jQuery, window.Code.PhotoSwipe));
                            
                            $.mCustomScrollbar.defaults.theme="light-2"; 
                            
                            $(x).mCustomScrollbar({
                                axis:"x",
                                advanced:{autoExpandHorizontalScroll:true}
                            });
                        }
                        
                    }
                }
                

            }
            
        }
    });

    function OutputData(activity_box, i, val) {
        //活动时间、名字
        if (val.time) {
            var time = val.time.split('-');
            time = time.join('/');
        }   

        if (val.video != "") {
            var activity_video = "";
            activity_video += "<li><a href=\""+ val.video + "\" class=\"a_video picItem\">\n";;
            activity_video += "<img class=\"art-img\" src=\"./Public/img/btn_playvideo@3x.png\"  alt=\"\"></a></li>";
        }   
    
        var strVar = "";    
            strVar += "<div class=\"activity\" id=\"" + val.id + "\">\n";
            strVar += "<div class=\"activity-title\">\n";
            strVar += "<i><\/i><span class=\"name\">"+ val.title +"<\/span><span class=\"time\">"+ time +"<\/span><span class=\"pull-right change\">修改<b class=\"fa fa-chevron-right\"></b></span>";
            strVar += "<\/div>\n";
            
        if (val.img) {
            strVar += "<div class=\"activity-img\">\n";
            strVar += "<ul id=\"Gallery"+i+"\" class=\"gallery\">\n";
            if (activity_video) {
                strVar += activity_video;   
            }
            for (var j = 0; j < val.img.length; j++) {
                strVar += "<li><a id=\""+ val.img[j].id +"\" href=\""+ val.img[j].value + "\"  data-imagelightbox=\"e\" class=\"picItem\">\n";
                strVar += "<img class=\"art-img lazy_img\" src=\"./Public/img/img_cover.png\" style=\"background: url(\'"+ val.img[j].value + "\') no-repeat center/cover;\" alt=\"照片展示\"></a></li>";
            }
            strVar += "<\/ul>\n";
            strVar += "<\/div>\n";
        }
            strVar += "<\/div>\n";
        activity_box.prepend(strVar);

        
    }
}

/**
 * 删除图片
 */

function deletePic(pic,num,picID){
    $('.delete-menu').show();
    $('.bg').show();

    $('.delete-confirm').click(function(event) {
        $('#'+num).remove();
        $.ajax({
            cache: true,
            type: "POST",
            url:"?m=web&a=profiles",
            data:{'delete':pic,'type':2},// 序列号formid
            dataType: 'json',
            error: function(request) {

            },
            success: function(data) {
                var D_picID = $("#picID").val();
                var D_picID1 = $("#picID1").val();
                var D_picID2 = $("#picID2").val();
                if (D_picID != null) {
                    picID = ","+picID;
                    D_picID = D_picID.replace(picID,"");           
                    $("#picID").val(D_picID) 
                }
                
                if (D_picID1 != null) {
                    picID = ","+picID;
                    D_picID1 = D_picID1.replace(picID,"");           
                    $("#picID1").val(D_picID1) 
                } 

                if (D_picID2 != null) {
                    picID = ","+picID;
                    D_picID2 = D_picID2.replace(picID,"");           
                    $("#picID2").val(D_picID2) 
                }                 
            }
            
        });
        
        $('.delete-menu').hide();
        $('.bg').hide();

    });

    $('.delete-cancel').click(function(event) {
        $('.delete-menu').hide();
        $('.bg').hide();
    });
    
} 

/**
 * 留言
 */

function more(title, id, uid, fromuid){
    $(".f_windown1").css('display','block');
    $(".bg").css('display','block');
    $('.message').html("");
    $('.send-message').click(function() {
        if ($(this).siblings('.message').text() == '') {
            alert('信息不能为空');
            return;
        }
        ga('send', 'event', 'artist', 'message', id);
        $.ajax({
            type: 'POST',
            url: "?m=web&a=messages",
            data: {'title':title,'id':id,'fromuid':fromuid,'touid':uid,'msg':$(this).siblings('.message').text(),'action':'focus'},
            success: function(result){
                if (result.error==0) {
                    alert('发送成功');
                    $(".f_windown1").css('display','none');
                    $(".bg").css('display','none');
                    location.reload();

                }else{
                    alert(result.msg);
                }
            },
            dataType: 'json'
        });    
    return false;      
        
    });  
}

function toUser(id, uid, fromuid){
    $(".f_windown1").css('display','block');
    $(".bg").css('display','block');
    $('.message').html("");
    $('.send-message').click(function() {
        if ($(this).siblings('.message').text() == '') {
            alert('信息不能为空');
            return;
        }
        ga('send', 'event', 'artist', 'message', id);
        $.ajax({
            type: 'POST',
            url: "?m=web&a=messages",
            data: {'id':id,'fromuid':fromuid,'touid':uid,'msg':$(this).siblings('.message').text(),'action':'send'},
            success: function(result){
                if (result.error==0) {
                    alert('发送成功');
                    $(".f_windown1").css('display','none');
                    $(".bg").css('display','none');
                    location.reload();

                }else{
                    alert(result.msg);
                }
            },
            dataType: 'json'
        });    
    return false;      
        
    });  
}

/**
 * 提升排名
 */
function updataOnlineTime(uid) {
    $.ajax({
        type: 'GET',
        url: "?m=center&c=profile&a=updataonlitime&uid="+uid,
        success: function(result) {
            if (result == "1") {
                $('.f_windown').show();
                $('.bg').show();
                $('#f_close').click(function(){
                    $('.f_windown').hide();
                    $('.bg').hide();
                });
            } else if (result == "2") {
                alert('您今天已经提升过排名了！明天再来吧！')
            } else if (result == "0") {
                alert('请先补全你的资料！');
            } else {
                alert('连接超时····！');
            }
        },
        dataType: 'json'
    });
}  

/**
 * 我 
 */

$(function() {
    $('#f_close1').click(function(){
        $('.f_windown1').hide();
        $('.bg').hide();
    });
    
	$('.user-data-show').click(function() {
		$('.user-data-menu').slideDown();
		$('#bg').show();
	});
	$('.user-data-hide').click(function() {
		$('.user-data-menu').slideUp();
		$('#bg').hide();
	})
});

/**
 * 录音 
 */

$(function() {
    $('.renew-record').click(function() {
        $('.recordVoice').show();
    });

    $('.recordVoice .back').click(function() {
        $('.recordVoice').hide();
    })
});

/**
 * 
 */

$(function() {

    $('.add-experience').click(function() {
        $('.add-activity').show();
        $('.artist-detail').hide();
    });

    $('.add-activity .back').click(function() {
        $('.add-activity').hide();
        $('.artist-detail').show();
    });

    $('.set-activity .back').click(function() {
        $('.set-activity').hide();
        $('.artist-detail').show();
    });

    $('.delete-activity').click(function(event) {
        $('.bg').show();
        $('.f_windown').show();
        
    });
});

// 会员价格
$(function() {
    $('.member-item-btn').click(function(event) {
        $(this).addClass('selected')
            .siblings().removeClass('selected');
        var m_month = $(this).index();
        switch (m_month) {
            case 1:
            $('.total-money span').text(10 * m_month);
            $('.payoff').attr('id', '0');
            break;

            case 2:
            $('.total-money span').text(10 * (m_month + 1) - 2);
            $('.payoff').attr('id', '1');

            break;

            case 3:
            $('.total-money span').text(10 * (m_month + 3) - 5);
            $('.payoff').attr('id', '2');
            break;
        }
    
    });
});

/* 编码 */

function html_decode(str) {   
    var s = "";   
    if (str.length == 0) return "";   
    s = str.replace(/&gt;/g, "&");   
    s = s.replace(/&lt;/g, "<");   
    s = s.replace(/&gt;/g, ">");   
    s = s.replace(/&nbsp;/g, " ");   
    s = s.replace(/&#39;/g, "\'");   
    s = s.replace(/&quot;/g, "\"");   
    s = s.replace(/<br>/g, "\n");   
    return s;   
}