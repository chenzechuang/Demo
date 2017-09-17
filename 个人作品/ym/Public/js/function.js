     var click = 0;    
     var clickUid ='';
     var prevideo = '';
     var flag = 0;
    function clickListion(uid,create_time,that,event){
        if(click == 0 || clickUid != uid){
            $.ajax({
                cache: true,
                url: "index.php?m=api&c=article&a=listenclick",
                data: {
                    userid: uid,
                    create_time:create_time
                },
                type: "POST",
                dataType: 'json',
                error: function (request) {
                    alert('提交超时，请稍候再试！');
                },
                success: function (data) {
                    click = click + 1;
                    clickUid = uid;
                }
            });
        }

        if (prevideo != "" && $(that).next()[0] != prevideo) {
            var pid = prevideo.id;
            pid = pid.replace("player", "");
            $("#tempPlayer"+pid).remove();
            prevideo.pause();
            $(prevideo).prev().children().attr('src', './Public/img/dd_07.png');
            prevideo = $(that).next()[0];
        }

        if (flag == 0) {
            prevideo = $(that).next()[0];
            flag = 1;
        } 
        if (event != undefined) {
            event.stopPropagation();    
        }
        
    }
    
    function longAudio(id,urlLite,urlFull){

        var div = "player"+id;
        if(document.getElementById(div) == null){
            alert('音频转码中！！');
            return false;
        };
            //console.log(document.getElementById(""+div+"")+","+div);
        var Player = new YMPlayer({
            id: id,
            urlLite: urlLite,
            urlFull: urlFull,
            player: document.getElementById(div),
            playerControl: {'div': '#playerControl'+id,
                'playing': '<img  class="img-responsive" src="./Public/img/dd_07_b.png">',
                'paused': '<img class="img-responsive" src="./Public/img/dd_07.png">',
                'loading': '加载中'},
            progress: {'div': '#progress'+id,
                'css': 'background-color: red; display:none; height: 8px;'}
        }); 
        return false;
    }

    $("#checkStatus").click(function () {
        console.log(Player.status()); //返回 paused 时 就是暂停      返回 playing 时 就是播放
    })
    $("#stop").click(function () {
        Player.pause();
    })   
    
    
    function follow(type,userid,mcid) {
        $.ajax({
            cache: true,
            type: "POST",
            url: "index.php?m=api&c=users&a=followAdd&openid="+userid,
            data:{
                userid:userid,
                mcid:mcid,
            },
            error: function (request) {
                alert('提交超时，请稍候再试！');
            },
            success: function (data) {
                //      console.log(data);
                var data = eval('(' + data + ')');
                if (data.status == 0) {
                    $(".follow"+mcid+"").html('<img src =\"./Public/img/gz_06.png\">').attr('onclick', 'chfollow(\'1\','+ userid +', '+ mcid +')');
                }
            }
        })
    }


    function chfollow(type,userid,mcid) {
        $.ajax({
            cache: true,
            type: "POST",
            url: "index.php?m=api&c=users&a=followDel&openid="+userid,
            data:{
                userid:userid,
                mcid:mcid,
            },
            error: function (request) {
                alert('提交超时，请稍候再试！');
            },
            success: function (data) {
                //      console.log(data);
                var data = eval('(' + data + ')');
                if (data.status == 0) {
                    if(type == "1"){
                       $(".follow"+mcid+"").html('<img src =\"./Public/img/gz_03.png\">').attr('onclick', 'follow(\'1\','+ userid +', '+ mcid +')');
                    }
                }
            }
        })
    }  

    function formatSeconds(value) {
        var second = parseInt(value);// 秒
        var minute = 0;// 分
        var hours = 0;// 小时
        if(second > 60) {
            minute = parseInt(second/60);
            second = parseInt(second%60);
            if (minute > 60) {
                hours = parseInt(minute/60);
                minute = parseInt(minute%60);
            }
        }
        var result = "";
        result += parseInt(hours)+"时";
        result += parseInt(minute)+"分";
        return result;
    }   

    function tags(element, tags) {
        var tag = tags.split('|');
        var str = "";
        for (var i = 0, len = tag.length; i < len; i++) {
            str += "<li class=\"data-btn\">"+ tag[i] +"</li>"
        }
        $('.'+element).append(str);
    }  

    function DLapp(event) {
        event.stopPropagation();
        $('.f_windown').css('display','block');
        $('.bg').css('display','block');

        $('#f_close').click(function(){
            $('.f_windown').hide();
            $('.bg').hide();
        }); 

        $('.bg').click(function(){
            $('.f_windown').hide();
            $('.bg').hide();
        }); 
    }