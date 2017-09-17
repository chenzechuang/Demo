/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function hideMenu(appId,timestamp,nonceStr,signature,title,name,url,f_imgurl,q_imgurl){
                wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: appId, // 必填，公众号的唯一标识
                timestamp: timestamp, // 必填，生成签名的时间戳
                nonceStr: nonceStr, // 必填，生成签名的随机串
                signature:signature, // 必填，签名，见附录1
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'hideMenuItems'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });

            wx.ready(function () {
                wx.onMenuShareTimeline({
                    title: title, // 分享标题
                    link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    desc: name,
                    imgUrl: q_imgurl, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    },
                    trigger: function (res) {
//                            alert("分享到朋友圈按钮点击");        
                    },
                    fail: function (res) {
//                            alert(JSON.stringify(res));
                    }
                });

                wx.onMenuShareAppMessage({
                    title: title, // 分享标题
                    desc:  name, // 分享描述
                    link: url, // 分享链接
                    imgUrl: f_imgurl,
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.hideMenuItems({
                    menuList: ["menuItem:copyUrl", "menuItem:openWithQQBrowser", "menuItem:openWithSafari", "menuItem:share:email"] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
                });
            });
}