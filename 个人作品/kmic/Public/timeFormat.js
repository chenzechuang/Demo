//JavaScript函数：
    
    function Appendzero(obj) {
        if (obj.length = 1) return "0" + obj; 
        else return obj;
    }
    function getDate(dateTimeStamp) {
        var date = new Date(dateTimeStamp);
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var time = month + '-' +day;
        return time;
    }
    
    function getDateDiff(dateTimeStamp, time){
        if (time != undefined) {
            var dateTimeStamp = dateTimeStamp;
            var now = new Date().getTime();
            var diffValue = now - dateTimeStamp*1000;
        } else {
            var modify_time = dateTimeStamp;
            var dateTimeStamp = Date.parse(dateTimeStamp.replace(/-/gi,"/"));
            var now = new Date().getTime();
            var diffValue = now - dateTimeStamp;
        }
        

        
        var minute = 1000 * 60;
        var hour = minute * 60;
        var day = hour * 24;
        var halfamonth = day * 15;
        var month = day * 30;
        
        
        if(diffValue < 0){
         //若日期不符则弹出窗口告之
         //alert("结束日期不能小于开始日期！");
         }
        var monthC =diffValue/month;
        var weekC =diffValue/(7*day);
        var dayC =diffValue/day;
        var hourC =diffValue/hour;
        var minC =diffValue/minute;
        if(dayC >= 1){
            if (time != undefined) {
                result = time.substring(5,10);
            } else {
                result = modify_time.substring(5,10);
            }
            
        }
        else if(hourC>=1){
            result=parseInt(hourC) +"小时前";
        }
        else if(minC>=1){
            result=parseInt(minC) +"分钟前";
        } else {
            result="刚刚";
        }
        return result;
    }

    function transTimeline(timeline) {
        var time = new Date();
        time.setTime(timeline * 1000);
        time = getDateDiff(timeline,time.toLocaleDateString());
        return time;
    }


    