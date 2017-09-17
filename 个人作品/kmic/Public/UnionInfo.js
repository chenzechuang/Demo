
    $.ajax({
        type: 'GET',
        url: "?m=web&a=saveUnionInfo&openid="+openid,
        dataType: 'html',
        success: function(result){
            console.log(result);

        },
        beforeSend: function(){

        }
    }); 