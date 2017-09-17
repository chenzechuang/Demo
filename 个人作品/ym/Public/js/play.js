var unlockPw = ["你是最胖的","睡尼玛弊起来嗨","我的前任是王八蛋","求尼玛老公抱","你是猪吗","早睡早起锻炼身体","希望明天不要下雨啦啦啦","求关注求包养","用声交换故事","3.141592653979 ","姑姑的哥哥的儿子叫什么","天文地理大象蚂蚁","红鲤鱼与绿鲤鱼与驴","四书五经蜡笔小新","爱我的请举手","老子是糖甜到忧伤","what are you弄啥咧","尼古拉斯赵四","解锁吧老铁","要宝宝举高高么么擦","行行行，你最帅","爸爸你说什么都是对的","你是我最爱的宝贝","算我求你了，给我听听吧","什么都别说了，我最爱你","爱你，我要大声说出来","我再也不装逼了我就想听你说了啥","你是电你是光你是唯一的神话","你是最幽默的老铁","你的freestyle在下佩服","你看我理你吗","你上课认真听了吗","你好厉害哦","最喜欢你了","人家不要嘛","今晚带你去看星星","有什么事，躺下再说吧","今晚月色好美啊","你是风儿我是沙","痴痴呆呆，坐在一张台","为什么要哭，是我不好吗","叫爸爸，快点","宝宝累了，要亲亲才能起来","你饿吗，我下面给你吃吧","我还小，不能听这些的","他还是个孩子，不要放过他","小芳是个性开放的女生","红鸡公尾巴灰,灰鸡公尾巴红","钓鱼要到岛上钓不到岛上钓不到","鸡蛋糍粑我也吃","来呀，快活呀，反正有大把时光","以后可以这样骂我，尽情骂","吃货是一个光荣的称号","伦家是靠脸吃饭的","你有freestyle吗","居然还有这种操作","这个玩起来好爽哦","你那么美说什么都是对的","说得好","你是最棒的","有道理","没想到你是这样的人","你那么帅说什么都是对的","我不说，就不说","好崇拜你啊","你为什么懂这么多","你坏坏","You bad bad"];
$(document).ready(function() {
    if ($('.unlockPw').length > 0) {
        var count = Math.floor((unlockPw.length * Math.random()));
        unlockPwNow = unlockPw[count];
        $('.unlockPw').html(unlockPwNow);
    }
});
var localId;
$(function (){
    var btn_text = $('.record').html();
    var isRecord = 0;
    var isPlaying = 0;
    var hasRecord = false;
    var i = 1;
    var timer = null;
    var recordArr = new Array();
    $('.record').on('touchstart', function () {
        if (hasRecord) {
            $('.record-chioce').hide();            
        }
        isRecord = 1;
        START = new Date().getTime();
        $(this).siblings('.recording').show();

        timer =  setInterval(function() {
            $('.recording img').attr('src', './Public/img/voice/voice_'+ i +'@2x.png');
            i++; 
            if (i == 4) {
                i = 0;
            }
        }, 1000);
        
        $(this).html('松开结束');
        wx.startRecord({
            success: function () {
                wx.onVoiceRecordEnd({
                    // 录音时间超过一分钟没有停止的时候会执行 complete 回调
                    complete: function (res) {
                        alert('最多只能录制一分钟');
                        localId = res.localId;
                      //  uploadluyin(localId, 60000);
                    }
                });
            },
            cancel: function () {
                alert('用户拒绝授权录音');
                return false;
            }
        });

    });
    
    var c = 0;  
    
    $('.preview-record').on('click', function () {
        
        //播放及暂停播放
        if (c == 0) {
            $('.preview-record a').addClass('selected');
            wx.playVoice({
                localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
            });
            c = c + 1;
        } else {
            $('.preview-record a').removeClass('selected');
            wx.pauseVoice({
                localId: localId  // 需要暂停的音频的本地ID，由stopRecord接口获得
            });
            c = 0;
        } 

        
    })



    
    $('.record').on('touchend', function () {
        $(this).siblings('.recording').hide();

        clearInterval(timer);
        i = 1;
        $('.recording img').attr('src', './Public/img/voice/voice_0@2x.png');

        END = new Date().getTime();
        //录音时间
        luyintime = END - START;
        if (luyintime < 2000) {
            END = 0;
            START = 0;
            wx.stopRecord({});
            alert('录音时间不能少于2秒');
            $('.record').html(btn_text);
            return false;
            //小于300ms，不录音
        } else {
            var count = parseInt(luyintime / 1000);
            $('.conmit_from').show();
            $(this).siblings('.record-chioce').show().find('.record-count').html(count);

            $(this).html('重新再说');
            hasRecord = true;
            wx.stopRecord({
                success: function (res) {
                    localId = res.localId;
                   // uploadluyin(localId, luyintime);
                    recordArr.push(localId);
                }
            });
        }
    });


    $('.send-record .cancel').click(function () {
        $('.record-chioce').hide();
        $('.record').html(btn_text);
        return false;
    });
    
/*

    $('.send-record .send').click(function () {
        $('.others-voice').append('<div class="others-voice-item">\n\
                <a href="javascript:;"><i class="fa fa-volume-up"></i>12"</a>\n\
                <img class="img-circle" src="images/2.jpg" alt="">\n\
            </div>')
        $('.record-chioce').hide();
        $('.record').html(btn_text);
    });
*/
});

function WxUpload(token,id,heardhurl) {

    $(".textTab").addClass("show")
    $(".textTabContent").text("上传音频中...");
    $(".record-chioce").css("display","none");
    var audioTime = $('.record-count').html();
    wx.uploadVoice({
        localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
        isShowProgressTips: 1, // 默认为1，显示进度提示
        success: function (res) {
            var timestamp = Date.parse(new Date());
            timestamp = timestamp / 1000;
            var serverId = res.serverId; // 返回音频的服务器端ID
            var link = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token="+token+"&media_id=" + serverId
            console.log(link);
            var theme = $(".themeID").val();
            var pub = $(".pub").val();
            var son = $(".son").val();
            if(pub!=0){
                pub = 1;
            }
            if(son){
                son = son;
            }else{
                son = 0;
            }
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: "?m=Play&c=index&a=pub",
                    data: {'link': link, 'id': id,'action':'WxUpload','pub':pub,'theme':theme,'create_time':timestamp,'audio_time':audioTime,'son':son}, // 序列号formid
                    dataType: 'json',
                    error: function (request) {
                        alert("上传失败！");
                       console.log(alert);
                    },
                    success: function (data) {
                        console.log(data);
                       // if (data.code == 0) {
                            $(".textTabContent").text("音频转码中...");
                                setTimeout(function () {
                                    $('.record').html("按住说话");
                                      if(son == 0){
                                           window.location.href="?m=Play&c=Index&a=checkAduio&confirm_id="+id+"&theme="+theme+"&create_time="+timestamp+"&son="+son+"&open="+id;
                                      }else{
                                           $('.others-voice').html("");
                                           $('.dropload-down').remove();
                                           msgLoad();                                       
                                           $('.footer-btn-vadio').addClass('hide')
                                           $(".textTab").removeClass('show');
                                          //  var hfAudio = '<div class="others-voice-item"><a onclick="WxAudio()"  href="javascript:;"><i class="fa fa-volume-up"></i> '+audioTime+'\'\'</a><img class="img-circle" src="'+heardhurl+'" alt=""></div>'; 
                                          //  $(".others-voice").append(hfAudio);
                                      } 
                                 //  
                                }, 2000)         
                     // }
//                                    
                    }
                });
        }
    });      
};


