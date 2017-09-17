const isMobile = /mobile/i.test(window.navigator.userAgent);
class YMPlayer {
    constructor(option) {
        //读取设定
        this.option = option;
        //添加一个临时播放器
        $('body').append('<audio id="tempPlayer' + this.option.id + '"></audio>');
        //赋值给临时播放器
        this.tempPlayer = document.getElementById('tempPlayer' + this.option.id);


        //添加一个进度色块
        $(this.option.progress.div).html('<div id="subprogress'+ this.option.id +'" style="' + this.option.progress.css + 'width: 0px;"></div>');
        this.audioStatus = 'stop';
        this.nowPlaying = 'tempPlayer';
        
        //实例化
        this.initPlayer(this.option.id);
        //


    }

    initPlayer(userid) {
        //加载完整地址

        this.option.player.src = this.option.urlFull;
        this.option.player.preload = "auto";//预下载
        this.option.player.load();

        this.audioStatus;

        //如果 是ios就手动播放
        if (checkOS() == 'ios') {
            //加载临时地址
            this.tempPlayer.src = this.option.urlLite;
            this.audioStatus = "paused";
            $(this.option.playerControl.div).html(this.option.playerControl.paused);
            //当前使用的播放器
            this.currentPlayer = this.tempPlayer;
        } else {
            //如果 是android就手动播放
//            this.tempPlayer.play();
            this.audioStatus = "paused";
            $(this.option.playerControl.div).html(this.option.playerControl.paused);
            //当前使用的播放器
            this.currentPlayer = this.option.player;
        }

        //是否开始播放 如果是 就不走 循环的方法
        let isPlayePlayer = false;

        //每50毫秒循环
        setInterval(() => {
            //为了不自动继续播放
            if (this.audioStatus == 'paused') {
                return;
            }
            if (checkOS() == 'ios') {
                //如果开始了加载
                if (this.option.player.buffered.length > 0) {
                    //显示加载中
                    // if (this.tempPlayer.buffered.end(0) < this.tempPlayer.duration) {
                    //     $(this.option.playerControl.div).html(this.option.playerControl.loading);
                    // }
                    //进度
                    let progressVal = this.currentPlayer.currentTime / this.option.player.duration * $(this.option.progress.div).width();
                    $('#subprogress'+userid+'').css('width', progressVal + 'px');
                    //当完整音频加载好了就把临时关了播完整音频
                    if (this.option.player.buffered.end(0) == this.option.player.duration && isPlayePlayer === false) {
                        console.log("转");
                        this.tempPlayer.pause();
                        this.option.player.currentTime = this.tempPlayer.currentTime;
                        this.option.player.play();
                        this.currentPlayer = this.option.player;
                        isPlayePlayer = true;
                        this.nowPlaying = 'player';
                    }
                }
            } else {
                //如果是android就别搞那么多事情了
                if (this.option.player.buffered.length > 0) {
                    //进度
                    let progressVal = this.currentPlayer.currentTime / this.option.player.duration * $(this.option.progress.div).width();
                    $('#subprogress'+userid+'').css('width', progressVal + 'px');
                }
            }

        }, 50);
        //监听播放按钮
        $(this.option.playerControl.div).click(function () {
            playOrPause();
        })
        //监听播放方法
        let isFirst = true;
        const playOrPause = () => {
            if (isFirst == true) {
                this.option.player.play();
            }
            if (checkOS() == 'ios') {
                if (this.audioStatus == 'playing') {
                    this.option.player.pause();
                    this.tempPlayer.pause();
                    this.audioStatus = "paused";
                    $(this.option.playerControl.div).html(this.option.playerControl.paused);
                } else {
                    if (this.nowPlaying == 'player') {
                        this.option.player.play();
                    } else {
                        this.tempPlayer.play();
                    }
                    $(this.option.playerControl.div).html(this.option.playerControl.playing);
                    this.audioStatus = "playing";
                }
            } else {
                if (this.audioStatus == 'playing') {
                    this.currentPlayer.pause();
                    this.audioStatus = "paused";
                    $(this.option.playerControl.div).html(this.option.playerControl.paused);
                } else {
                    this.currentPlayer.play();
                    $(this.option.playerControl.div).html(this.option.playerControl.playing);
                    this.audioStatus = "playing";
                }
            }

        }

        //监听
        this.option.player.addEventListener("playing", function () {
            playing();
        });
        this.option.player.addEventListener("pause", function () {
            paused();
        });
        const playing = () => {
            return this.audioStatus = "playing";
        };
        const paused = () => {
            return this.audioStatus = "paused";
        };
    }
    status() {
        return this.audioStatus;
    }

    pause() {
        if (this.audioStatus == 'playing') {
            this.option.player.pause();
            this.tempPlayer.pause();
            this.audioStatus = "paused";
            $(this.option.playerControl.div).html(this.option.playerControl.paused);
        }
    }
}

function checkOS() {
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isAndroid == true) {
        return 'android';
    } else if (isiOS == true) {
        return 'ios';
    } else {
        return 'unknow'
    }
}
