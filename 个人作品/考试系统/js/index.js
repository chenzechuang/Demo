var url = "http://192.168.18.205";
var qs_numberArr = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"];
var selectQs = [];
$(function() {

    // //菜单
    // $.ajax({
    //     url: url + '/exam-api/home/menu',
    //     type: 'get',
    //     dataType: 'json',
    //     success: function (data) {
    //         console.log(data);
    //     }
    // });

    $('.edit-container').on('mouseover mouseout', '.questions', function(event) {
        if (event.type == 'mouseover') {
            $(this).find('.qs-control').css('visibility', 'visible');
        } else if (event.type == 'mouseout') {
            $(this).find('.qs-control').css('visibility', 'hidden');
        }
    });

    var qs_type = "";

    $('.add-item').click(function(event) {
        $('.add-option').slideToggle();
    });

    // 添加问题
    $('.add-option .btn').click(function(event) {
        $('.add-option').hide();
        if (!$(this).is('.fromTestLib')) {
            $('.shadow').show();
            $('.continue').attr('disabled', false);
            $('.previous').attr('disabled', false);
            $('.alterItem button').attr('disabled', false);
            $('.addBtnGroup button').show();
            $('.save').hide();

            qs_type = $(this).html();
            var choice_list = addQs(qs_type);
            $('.choiceTitle').val("");
            $('.choiceScore').val("");
            $('.addOption').html("").append(choice_list);
        } else {

            // 题库
            $('#testLibModel').modal()
            getResources(1);
            selectQs = [];
            $('#testLibModel').on('change', '.selectLib', function(event) {
                var qs_id = this.id.substring(2);
                if (this.checked == true) {
                    //避免重复累计id （不含该id时进行累加）
                    if (selectQs.indexOf(qs_id) == -1) {
                        selectQs.push(qs_id);
                        selectQs.sort();
                        console.log(selectQs);
                    }
                }
                if (this.checked == false) {
                    //取消复选框时 含有该id时将id从全局变量中去除
                    if (selectQs.indexOf(qs_id) != -1) {
                        var index = selectQs.indexOf(qs_id);
                        selectQs.splice(index, 1);
                         console.log(selectQs);
                    }
                }
                return false;
            });
            $('.addLibQs').unbind('click').click(function(event) {
                var selectQs_str = []
                for (var i = 0; i < selectQs.length; i++) {
                    selectQs_str[i] = "qlist=" + selectQs[i];
                }
                var data = selectQs_str.join("&");
                var existQs = $('.edit-container .questions').length;
                $.ajax({
                    url: url + '/exam-api/question/list',
                    type: 'get',
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if ($('input[name="totalPoint"]').val() == '') {
                            var score = 0;
                        } else {
                            var score = parseInt($('input[name="totalPoint"]').val());    
                        }
                        
                        $.each(data, function(index, val) {
                            index = existQs + index;
                            var correctArr = [];
                            if (val.type == 1 || val.type == 2) {
                               for (var i = 0; i < val.rightOptions.length; i++) {
                                   correctArr.push(i);
                               } 
                            } else if (val.type == 4) {
                                correctArr =  val.answerWord.split("-&-");
                            }
                            
                            var answers = val.rightOptions.concat(val.errorOptions);
                            var options = "<div class=\"questions\">\n";
                            options_wrap = addOptions(val.type, index, val.name, val.score, correctArr, answers, val.id);
                            options += options_wrap;
                            options += "<\/div>";
                            $('.add').before(options);
                            $('.qs-options input').attr('disabled', true);
                            $('#testLibModel').modal('hide');
                            score += parseInt(val.score);
                        });

                        $('input[name="totalPoint"]').val(score);
                    }
                })
                return false;
            });
        }
        
        return false;
    });

    // 添加选项
    $('.addChoice').click(function(event) {
        if ($('.addOption tbody tr').length == 10) {
            layer.alert('最多设置10个选项', { icon: 5, anim: 6 });
            return;
        }
        if (qs_type == "单选") {
            $('.addOption tbody').append("<tr class=\"choiceRow\"><td><input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\"></input></td><td><input type=\"radio\" name=\"radioBtn\" class=\"form-control\"></td></tr>");
        } else if (qs_type == "多选") {
            $('.addOption tbody').append("<tr class=\"choiceRow\"><td><input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\"></input></td><td><input type=\"checkbox\" name=\"checkBtn\" class=\"form-control\"></td></tr>");
        }
    });

    // 减少选项
    $('.deleteChoice').click(function(event) {
        if ($('.addOption tbody tr').length == 2) {
            layer.alert('最少设置2个选项', { icon: 5, anim: 6 });
            return;
        }
        $('.addOption tbody tr:last').remove();
    });

    // 选项验证、添加问题到面板
    $('.addBtnGroup').on('click', 'button', function(event) {
        if (checkQs()) {
            // 添加问题到面板
            var totalScore = 0;
            // 继续添加
            if ($(this).is('.continue')) {
                var options = "<div class=\"questions\">\n";
                options_wrap = addOptions(qs_type);
                options += options_wrap;
                options += "<\/div>";
                $('.add').before(options);
                $(this).attr('disabled', true);
                $(this).next().attr('disabled', true);
                $('.alterItem button').attr('disabled', true);
                $('.add-option').show();
            }

            // 预览
            if ($(this).is('.previous')) {
                var options = "<div class=\"questions\">\n";
                options_wrap = addOptions(qs_type);
                options += options_wrap;
                options += "<\/div>";
                $('.add').before(options);
                $('.shadow').hide();
            }


            // 修改
            if ($(this).is('.save')) {
                var edit_options = "";
                var edit_index = sessionStorage.getItem("index");
                var edit_type = sessionStorage.getItem("type");
                edit_options = addOptions(edit_type, edit_index);
                $('.edit-container .questions').eq(edit_index).html("").append(edit_options);
                $('.shadow').hide();
            }

            $.each($('.qs-score'), function(index, val) {
                totalScore += parseInt($(this).html().split('分')[0]);
            });

            $('input[name="totalPoint"]').val(totalScore);
           
            return false;

            // $('.save').click(function() {
            //     if (checkQs()) {
            //         
            //     }
            //     return false;
            // });
        }
    });

    // 关闭弹窗
    $('.close-btn').click(function(event) {
        $('.shadow').hide();
    });

    
/*查看问卷数据
    $('.test-list').on('click', '.testData', function() {
        var that = this;
        $('.test-list').hide();
        $('.data-container').show();
        var list_item = $(that).parents('.test-item');
        var prev_index = list_item.index() - 1;

        $.ajax({
            url: '../question.json',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var result = data.tests[prev_index];
                $('.data-container h2').html(result.title);
                $('.data-container .questions').remove();
                $.each(result.test, function(index, val) {
                    console.log(val);
                    var options = "<div class=\"questions\">\n";
                    options_wrap = addOptions(val.type, index, val.question, val.correctAnswer, val.answers);
                    options += options_wrap;
                    options += "<\/div>";
                    $('.data-container .data').append(options);
                });
                $('.addTestAnother').hide();
            }
        });
        $('.publish').hide();
        var myChart = echarts.init($('#main')[0]);
        $.get('question.json').done(function (data) {
            // 填入数据
            myChart.setOption({
                title : {
                    text: '问题1答题情况',
                    x:'center',
                    y:'50'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                series: [{
                    // 根据名字对应到相应的系列
                    name: '答题情况',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    selectedMode: 'single',
                    data: data.tests[0].data,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowstartPageX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            });
        });
    });
*/


});

// 获取题库题目
function getResources(startPage) {
    $.ajax({
        url: url + '/exam-api/question/page',
        // url: 'data.json',
        type: 'get',
        dataType: 'json',
        data: {offset: startPage, size: 5},
        success: function(data) {
            console.log(data);
            var resources = data.resources;
            var strVar = "";
            for (var i = 0; i < resources.length; i++) {
                var qs_title = resources[i].name;
                var qs_type = resources[i].type;
                var qs_id = resources[i].id;
                switch(qs_type) {
                    case 1:
                        qs_type = "单选";
                        break;
                    case 2:
                        qs_type = "多选";
                        break;
                    case 4:
                        qs_type = "填空";
                        break;
                    case 5:
                        qs_type = "简答";
                        break;
                }
                var rightOptions = resources[i].rightOptions;
                var errorOptions = resources[i].errorOptions;
                
                strVar += "<div class=\"panel panel-default\">\n";
                strVar += " <div class=\"panel-heading\" role=\"tab\" id=\"heading"+ i +"\">\n";
                strVar += "     <h4 class=\"panel-title\">\n";
                strVar += "     <input type=\"checkbox\" name=\"selectLib\" class=\"selectLib\" id=\"qs"+ qs_id +"\">\n";
                strVar += "     <a role=\"button\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse"+ i +"\" aria-expanded=\"true\" aria-controls=\"collapse"+ i +"\">\n";
                strVar += qs_title+ " ("+ qs_type + ")";
                strVar += "     <\/a>\n";
                strVar += "     <\/h4>\n";
                strVar += " <\/div>\n";
                strVar += " <div id=\"collapse"+ i +"\" class=\"panel-collapse collapse\" role=\"tabpanel\" aria-labelledby=\"heading"+ i +"\">\n";
                strVar += "     <div class=\"panel-body\">\n";
                strVar += "         <div class=\"options\">\n";
                strVar += "             <h3>正确选项<\/h3>\n";
                strVar += "             <div>\n";
                for (var j = 0; j < rightOptions.length; j++) {
                    strVar += "<p>" + rightOptions[j] + "<\/p>\n";
                }
                strVar += "             <\/div>\n";
                strVar += "         <\/div>\n";
                strVar += "         <div class=\"options\">\n";
                strVar += "             <h3>其他选项<\/h3>\n";
                strVar += "             <div>\n";
                for (var j = 0; j < errorOptions.length; j++) {
                    strVar += "<p>" + errorOptions[j] + "<\/p>\n";
                }
                strVar += "             <\/div>\n";
                strVar += "         <\/div>\n";
                strVar += "     <\/div>\n";
                strVar += " <\/div>\n";
                strVar += "<\/div>\n";
            }
            $('#resources').empty();
            $('#resources').append(strVar);
            getChecked(selectQs);

            var startPage = data.startPage;
            var finalPage = data.finalPage;
            var pagination = "";
            pagination += "<ul class='pagination pager_cus'>";
            pagination = pagination + "<li><a>第 " + (startPage);
            pagination = pagination + " 页/共 " + finalPage + " 页</a></li>";
            pagination += "<li><a href='javascript:getResources(" + 1 + ");'>首页</a></li>";
            if (startPage > 1) {
                pagination += "<li><a href='javascript:getResources(" + (startPage - 1) + ");'>« 上一页</a></li>";
            }
            var start = startPage - 3;
            var end = startPage + 3;
            if (start < 0) {
                end = end - start;
            }
            if (end > finalPage) {
                end = finalPage;
                start = end - 7;
            }
            for (var j = start; j <= end; j++) {
                if (j > 0 && j < finalPage + 1) {
                    if (startPage == j) {
                        pagination += "<li class='active'><a href='javascript:getResources("+ j + ");'>" + j + "</a></li>";
                    } else {
                        pagination += "<li><a href='javascript:getResources(" + j + ");'>" + j + "</a></li>";
                    }
                }
            }
            if (startPage < finalPage) {
                pagination += "<li><a href='javascript:getResources(" + (startPage + 1) + ");'>下一页 »</a></li>";
            }
            pagination += "<li><a href='javascript:getResources(" + finalPage + ");'>尾页</a></li>";
            $('#pagination').empty();
            $('#pagination').append(pagination);
            $('#personAddModel').modal('show');
        }
    })
}

// // 获取问卷列表
// function getPage(startPage, ifTest) {
//     $.ajax({
//         url: url + '/exam-api/exam/page',
//         // url: 'data.json',
//         type: 'get',
//         dataType: 'json',
//         data: {offset: startPage, size: 5},
//         success: function(data) {
//             console.log(data);
//             if (data.totalRows > 0) {
//                 $('.test-list').show();
//                 $('#testHeader .addTestAnother').show();
//                 var resources = data.resources;
//                 var strVar = "";
//                 for (var i = 0; i < resources.length; i++) {
//                     var test_title = resources[i].name;
//                     var test_id = resources[i].id;
//                     var testTime = resources[i].testTime;
//                     var startTime = resources[i].startTime;
//                     var endTime = resources[i].endTime;
//                     var totalPoint = resources[i].totalPoint;
//                     var passPoint = resources[i].passPoint;
//                     var deptType = resources[i].deptType;
//                     var time = resources[i].finalUpdateTime;
                    
//                     strVar += "<div class=\"test-item\" id=\"test"+test_id+" \">\n";
//                     strVar += " <ul>\n";
//                     strVar += "     <li>" + test_title + "<\/li>\n";
//                     strVar += "     <li>" + deptType + "<\/li>\n";
//                     strVar += "     <li>"+ testTime +"分钟" +"<\/li>\n";
//                     strVar += "     <li>\n";
//                     if (ifTest) {
//                         strVar += "         <button class=\"btn btn-primary doTest\" onclick=\"doTest("+ test_id +")\">测试</button>\n";
//                     } else {
//                         strVar += "         <button class=\"btn btn-primary editTest\" onclick=\"editTest("+ test_id +")\">编辑<\/button>\n";
//                         strVar += "         <button class=\"btn btn-danger deleteTest\" onclick=\"deleteTest("+ test_id +")\">删除<\/button>\n";
//                         strVar += "         <button class=\"btn btn-info testData\" onclick=\"testData("+ test_id +")\">查看数据<\/button>\n";  
//                     }
//                     strVar += "     <\/li>\n";
//                     strVar += " <\/ul>\n";
//                     strVar += " <div class=\"otherInfo\">\n";
//                     strVar += "     <p>可答题时间："+ startTime +" 至 " + endTime + "<\/p>\n"
//                     strVar += " <\/div>\n";
//                     strVar += "<\/div>\n";
//                 }
//                 $('.test-content').empty().append(strVar);
//                 var startPage = data.startPage;
//                 var finalPage = data.finalPage;
//                 var pagination = "";
//                 pagination += "<ul class='pagination pager_cus pull-right pagination-lg'>";
//                 pagination = pagination + "<li><a>第 " + (startPage);
//                 pagination = pagination + " 页/共 " + finalPage + " 页</a></li>";
//                 pagination += "<li><a href='javascript:getPage(" + 1 + ");'>首页</a></li>";
//                 if (startPage > 1) {
//                     pagination += "<li><a href='javascript:getPage(" + (startPage - 1) + ");'>« 上一页</a></li>";
//                 }
//                 var start = startPage - 3;
//                 var end = startPage + 3;
//                 if (start < 0) {
//                     end = end - start;
//                 }
//                 if (end > finalPage) {
//                     end = finalPage;
//                     start = end - 7;
//                 }
//                 for (var j = start; j <= end; j++) {
//                     if (j > 0 && j < finalPage + 1) {
//                         if (startPage == j) {
//                             pagination += "<li class='active'><a href='javascript:getPage("+ j + ");'>" + j + "</a></li>";
//                         } else {
//                             pagination += "<li><a href='javascript:getPage(" + j + ");'>" + j + "</a></li>";
//                         }
//                     }
//                 }
//                 if (startPage < finalPage) {
//                     pagination += "<li><a href='javascript:getPage(" + (startPage + 1) + ");'>下一页 »</a></li>";
//                 }
//                 pagination += "<li><a href='javascript:getPage(" + finalPage + ");'>尾页</a></li>";
//                 $('.testNavigation').empty().append(pagination);;
//             } else {
//                 $('.addTest').show();
//                 $('.test-list').hide();
//                 $('#testHeader .addTestAnother').hide();
//             }
//         }
            
//     })
// }

// 新建问卷
function addTest(self) {
    $('.edit-container').slideDown();
    $('input[name="testTitle"]').val("");
    $('input[name="testDes"]').val("");
    $('input[name="testTime"]').val("");
    $('input[name="deptType"]').val("");
    $('.edit-container .questions').remove();
    $('.edit').hide();
    $('.publish').show();
    $('.test-list').hide();
    $('.publishBtn').click(function(event) {
        controlTest(self);
    });
}

// 编辑问卷
function editTest(id, self) {
    self.$confirm('你确定要编辑吗?', '提示', {
       confirmButtonText: '确定',
       cancelButtonText: '取消',
       type: 'warning'
    }).then(() => {
        $.ajax({
            url: '/exam-api/exam/' +id,
            type: 'get',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('.publish').hide();
                $('.edit').show();

                var examInfo = data.exam;
                var examQs = data.questionInfos;
                $('input[name="testTitle"]').val(examInfo.name);
                $('input[name="testDes"]').val(examInfo.description);
                $('input[name="deptType"]').val(examInfo.deptType);
                $('input[name="reservation"]').val(examInfo.startTime.substr(0, examInfo.startTime.length - 3) 
                        +" - "+ examInfo.endTime.substr(0, examInfo.endTime.length - 3));
                $('input[name="totalPoint"]').val(examInfo.totalPoint);
                $('input[name="passPoint"]').val(examInfo.passPoint);
                
                $('.test-list').hide();
                $('.edit-container').show();

                $('.edit-container .questions').remove();
                var qs = "";
                var qsId = [];
                var correctArr;
                var answers = [];
                $.each(examQs, function(index, val) {
                    qsId.push(val.id);
                    if (val.type == 1 || val.type == 2) {
                        correctArr = [];
                        for (var i = 0; i < val.rightOptions.length; i++) {
                            correctArr.push(i);
                        }
                        answers = val.rightOptions.concat(val.errorOptions);
                    } else if (val.type == 4) {
                        correctArr = val.answerWord.split("-&-");
                    } else {
                        correctArr = val.answerWord;
                    }
                   
                    qs += "<div class=\"questions\">\n";
                    options_wrap = addOptions(val.type, index, val.name, val.score, correctArr, answers, val.id);
                    qs += options_wrap;
                    qs += "<\/div>";
                    
                });
                $('.add').before(qs);
                $('.qs-options input').attr('disabled', true);
                
                $('.content').on('click','.qs-del', function(event) {
                    var qs_index = $(this).parents('.questions').index() - 1;
                    console.log(qs_index);
                    delQs(this);
                    
                    
                    qsId.splice(qs_index, 1);
                    console.log(qsId);
                    
                });

                    
                $('.edit').click(function() {
                    controlTest(self, examInfo.id, qsId);
                });
            }
        })
    }).catch(() => {
        self.$message({
            type: 'info',
            message: '已取消编辑'
        });          
    });
}

// 删除问题
function delQs(that) {
    var qs_index = 0;
    var qs = $(that).parents('.questions');
    qs_index = qs.index() - 1;
    var qs_len = $('.questions').length;
    var x = 0;
    if ($('.qs-index')[qs_index + 1]) {
        for (var i = qs_index + 1; i < qs_len; i++) {
            x = parseInt($('.qs-index').eq(i).html());
            $('.qs-index').eq(i).html(x - 1);
        }
    }
    $(that).parents('.questions').remove();
    $('input[name="totalPoint"]').val(parseInt($('input[name="totalPoint"]').val()) - parseInt($(that).parents('.questions').find('.qs-score').html().split('分')[0]));
}

function editQs(that) {
    var edit_question = $(that).parents('.edit-container .questions');

    var edit_index = edit_question.index() - 1;
    sessionStorage.setItem("index", edit_index);

    var edit_type = edit_question.find('.qs-type').html();
    sessionStorage.setItem("type", edit_type);

    var edit_score = edit_question.find('.qs-score').html().split('分')[0];
    var edit_title = edit_question.find('.title-text').html();
    var edit_length = edit_question.find('.qs-options').length;
    var right_list = edit_question.find('input:checked');
    var option_label = edit_question.find('label');

    $('.shadow').show();
    $('.continue').attr('disabled', false);
    $('.alterItem button').attr('disabled', false);
    $('.choiceTitle').val(edit_title);
    $('.choiceScore').val(edit_score);
    var rightIndex;
    if (edit_type == "单选" || edit_type == "多选") {
        rightIndex = [];
        var option_list = [];
        $.each(right_list, function (index, val) {
            rightIndex.push($(this).parent().index() - 1);
        });
        $.each(option_label, function (index, val) {
            option_list.push(this.innerHTML.split('、')[1]);
        });
        var choice_list = addQs(edit_type, edit_length, rightIndex, option_list);
    } else if (edit_type == "填空") {
        rightIndex = [];
        $.each(edit_question.find('input'), function (index, val) {
            rightIndex.push($(this).val());
        });
        var choice_list = addQs(edit_type, edit_length, rightIndex);
    }

    $('.addOption').html("").append(choice_list);

    $('.addBtnGroup button').hide();
    $('.save').show();
}

// 发布、修改问卷
function controlTest(self, id, qsId) {
    if ($('input[name="testTitle"]').val() == "") {
        layer.alert('请输入问卷标题', { icon: 5, anim: 6 });
        return false;
    }
    
    if ($('input[name="testDes"]').val() == "") {
        layer.alert('请添加至少两个问题', { icon: 5, anim: 6 });
        return false;
    }

    if ($('input[name="testTime"]').val() == "") {
        layer.alert('请输入答题时间', { icon: 5, anim: 6 });
        return false;
    }

    if ($('input[name="totalPoint"]').val() == "") {
        layer.alert('请输入问卷总分', { icon: 5, anim: 6 });
        return false;
    }

    if ($('input[name="passPoint"]').val() == "") {
        layer.alert('请输入及格分数', { icon: 5, anim: 6 });
        return false;
    }

    var that = this,
        timestamp = new Date().getTime(),
        startTime = new Date($('input[name="daterangepicker_start"]').val()).getTime(),
        endTime = new Date($('input[name="daterangepicker_end"]').val()).getTime();
    var questions = [];

    $.each($('.questions'), function(index, val) {
        if (qsId && index > qsId.length - 1) {
            qsId.push(null)
        }
        var qs_type = $(this).find('.qs-type').html();
        switch(qs_type) {
            case "单选":
                qs_type = 1;
                break;
            case "多选":
                qs_type = 2;
                break;
            case "填空":
                qs_type = 4;
                break;
            case "简答":
                qs_type = 5;
                break;
        }

        var qs_title = $(this).find('.title-text').html();
        var rightArr = [];
        var errorArr = [];
        var answerWord = "";
        var answerWord_len = 0;
        if (qs_type == 1 || qs_type == 2) {
            var rightOptions = $(this).find('input:checked');
            $.each(rightOptions, function(index, val) {
                rightArr.push(($(this).next().html().split("、")[1]));
            });
            var errorOptions = $(this).find('input').not(":checked");
            $.each(errorOptions, function(index, val) {
                errorArr.push(($(this).next().html().split("、")[1]));
            });
        }  else if (qs_type == 4) {
            $.each($(this).find('input'), function(index, val) {
                rightArr.push($(this).val())
            });
            answerWord_len = rightArr.length
            answerWord = rightArr.join("-&-")
        } else {
            answerWord = $(this).find('input').val();
        }
        if (id) {
            questions.push({
                "id": qsId[index],
                "name": qs_title,
                "createTime": null,
                "rightOptions": rightArr.join("-&-"),
                "errorOptions": errorArr.join("-&-"),
                "answerWord": answerWord,
                "completions": answerWord_len,
                "creatorId": null,
                "description": "",
                "score": 10,
                "type": qs_type
            });                   
        } else {
            questions.push({
                "id": null,
                "name": qs_title,
                "createTime": null,
                "rightOptions": rightArr.join("-&-"),
                "errorOptions": errorArr.join("-&-"),
                "answerWord": answerWord,
                "completions": answerWord_len,
                "creatorId": null,
                "description": "",
                "score": 10,
                "type": qs_type
                
            });
        }
    });
    var data = {
        "id": null,
        "name": $('input[name="testTitle"]').val(),
        "createTime": timestamp,
        "deptType": $('input[name="deptType"]').val(),
        "totalPoint": parseInt($('input[name="totalPoint"]').val()),
        "passPoint": parseInt($('input[name="passPoint"]').val()),
        "startTime": startTime,
        "endTime": endTime,
        "testTime": parseInt($('input[name="testTime"]').val()),
        "description": $('input[name="testDes"]').val(),
        "status": 1,
        "rid":0,
        "questions": questions
    };
    
    if (id) {
        data["id"] = id;
        self.$confirm('你确定要修改吗?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        }).then(() => {
            $.ajax({
                url: url + '/exam-api/exam/update',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function (result) {
                    self.$message({
                        type: 'success',
                        message: '修改成功!'
                    });
                    setTimeout(function() {
                        window.location.reload();
                    },500)
                    
                }
            });

        }).catch(() => {
            console.log(data);
            self.$message({
                type: 'info',
                message: '已取消修改'
            });          
        });
    } else {
        self.$confirm('你确定要发布吗?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        }).then(() => {
            $.ajax({
                url: url + '/exam-api/exam/addition',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function (result) {
                    self.$message({
                        type: 'success',
                        message: '发布成功!'
                    });
                    setTimeout(function() {
                        window.location.reload();
                    },500)
                    
                }
            });

        }).catch(() => {
            self.$message({
                type: 'info',
                message: '已取消发布'
            });          
        });
    }
}

// 删除问卷
function deleteTest(id) {
    layer.confirm('你确定要删除吗？', function(index) {
        $.ajax({
            url: url + '/exam-api/exam/'+ id +'/0',
            type: 'post',
            success: function (result) {
               window.location.reload();
            }
        });
        if ($('.test-list .test-item').length == 1) {
            $('.test-list').hide();
            $('.addTest').slideDown();
            $('#testHeader .addTestBtn').hide();
        }
        layer.close(index);
    });
}

// 做问卷
function doTest(id, self) {
    self.$confirm('你确定要测试吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
    }).then(() => {
        $.ajax({
            url: url + '/exam-api/exam/'+ id +'/open',
            type: 'get',
            success: function (result) {
                console.log(result);
                $('.test-container').show();
                $('.listContent').show();
                $('.timerContent').show();
                $('.test-list').hide();
                $('.test-container h2').html(result.examName);
                $('.test-container .questions').remove();
                var left_time = result.testTime * 60;
                var examPaperId = result.examPaperId;
                var btnGroup = "<div class=\"btn-group-vertical\" role=\"group\">"
                var options = "";
                var options_wrap = "";
                $.each(result.questionPapers, function(index, val) {
                    options += "<div class=\"questions\">\n";
                    if (val.type == 1 || val.type == 2) {
                        options_wrap = addOptions(val.type, index, val.title, val.score, [], val.options);
                    } else if (val.type == 4) {
                        options_wrap = addOptions(val.type, index, val.title, val.score, val.answerWord, val.options);
                    }
                    options += options_wrap;
                    options += "<\/div>";
                    
                    btnGroup += "<a href=\"javascript:;\" class=\"btn btn-default\">"+ (index+1) +"</a>"

                });

                $('.test-container .content').append(options);
                $('.test-container input').attr({
                    checked: false,
                    disabled: false,
                    value: ""
                });
                $('.test-container textarea').attr('disabled', false).html("");
                btnGroup += "<\/div>";
                $('.listContent').append(btnGroup);

                $('.questions input[type="checkbox"]').click(function() {
                    if (!$(this).parents('.questions').find(':checked').length == 1) {
                        return false;
                    }
                });

                $('.questions input,.questions textarea').change(function() {
                    if (val.type == 1 || val.type == 2) {
                        options_wrap = addOptions(val.type, index, val.title, val.score, [], val.options);
                    } else {
                        options_wrap = addOptions(val.type, index, val.title, val.score, val.answerWord, val.options);
                    }
                    $.ajax({
                        url: url + '/exam-api/paper/answer',
                        type: 'post',
                        data: {
                            "examPaperId": examPaperId,
                            "questionId": val.questionId,
                            "answer": $(this).val()
                        },
                        success: function (res) {
                            console.log(res);
                        }
                    })
                    var index = $(this).parents('.questions').index();
                    $('.listContent a').eq(index).addClass('btn-primary').removeClass('btn-default');
                });

                $('.listContent a').click(function() {
                    var a_index = $(this).index();
                    var b_top = $('.questions').eq(a_index).offset().top - 100;
                    $(window).scrollTop(b_top);
                })
                var t1 = setInterval(function() {
                    if (left_time == 0) {
                        clearInterval(t1);
                        console.log("时间到！");
                        return;
                    }
                    if ((left_time - 1) % 60 == 0) {
                        $('.timer').css('fontWeight', 'bold');
                    } else {
                        $('.timer').css('fontWeight', 'normal');
                    }

                    if ((left_time- 1) / 60 <= 5) {
                        $('.timer').css('fontSize', '16px');
                    }
                    left_time -= 1;
                    var minutes = checkTime(parseInt(left_time / 60, 10));//计算剩余的分钟
                    var seconds = checkTime(parseInt(left_time % 60, 10));//计算剩余的秒数
                    $('.timer').html('剩余时间：' + minutes + '分钟' + seconds + '秒');
                }, 1000);

                // 答题 - 提交问卷
                $('.submitBtn').click(function() {
                    var qs_item = $('.test-container .questions');
                    $.each(qs_item, function(index, val) {
                        var selectAnswer = [];
                        var checkAnswer = [];
                        var qs_type = $(this).find('.qs-type').html();
                        if (qs_type == "单选" || qs_type == "多选") {
                            var checklist = $(this).find('input:checked');
                            if (checklist.length !== 0) {
                                $.each(checklist, function(index, val) {
                                    checkAnswer.push($(this).parent().index() - 1);
                                });
                                selectAnswer.push(checkAnswer);
                            } else {
                                selectAnswer.push(-1);
                            }
                        } else {
                            selectAnswer.push($(this).find('input').val());
                        }
                        
                        localStorage.setItem('selectAnswer', selectAnswer);
                    });
                    
                    for (var i = 0; i < $('.questions').length; i++) {
                        var hasInput = $('.questions').eq(i);
                        var hasInput_type = hasInput.find('.qs-type').html();
                        if (hasInput_type == "单选" || hasInput_type == "多选") {
                            if (hasInput.find('input:checked').length == 0 ) {
                                layer.alert('请答完所有问题后再交卷', { icon: 5, anim: 6 });
                                return false;
                            }
                        } else if (hasInput.find('input').val() == "") {
                            layer.alert('请答完所有问题后再交卷', { icon: 5, anim: 6 });
                            return false;
                        }
                        
                    }
                })
            }
        });

    }).catch(() => {
        self.$message({
            type: 'info',
            message: '已取消测试'
        });          
    });

/*    window.onbeforeunload = function() {
        localStorage.clear();
        localStorage.setItem('index', doTest_index);
        localStorage.setItem('left_time', left_time);
    }*/
}

// 根据按钮类型填充弹窗
function addQs(type, length, arr, list) {
    var strVar = "";
    if (type == "单选") {
        strVar += "<caption>题目选项<\/caption>\n"
        strVar += "<thead>\n"
        strVar += "<tr>\n";
        strVar += " <th>选项文字<\/th>\n";
        strVar += " <th>正确答案<\/th>\n";
        strVar += "<\/tr>\n";
        strVar += "<\/thead>\n";
        strVar += "<tbody>\n"
        if (length) {
            for (var i = 0; i < length; i++) {
                strVar += "<tr class=\"choiceRow\">\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\" value=" + list[i] + ">\n";
                strVar += " <\/td>\n";
                strVar += " <td>\n";
                if ($.inArray(i, arr) != -1) {
                    strVar += "     <input type=\"radio\" name=\"radioBtn\" class=\"form-control\" checked>\n";
                } else {
                    strVar += "     <input type=\"radio\" name=\"radioBtn\" class=\"form-control\">\n";
                }
                strVar += " <\/td>\n";
                strVar += "<\/tr>\n";
            }
        } else {
            for (var i = 0; i < 4; i++) {
                strVar += "<tr class=\"choiceRow\">\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\">\n";
                strVar += " <\/td>\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"radio\" name=\"radioBtn\" class=\"form-control\">\n";
                strVar += " <\/td>\n";
                strVar += "<\/tr>\n";
            }
        }

        strVar += "<\/tbody>";
        $('.alterItem').show();

    } else if (type == "多选") {
        strVar += "<caption>题目选项<\/caption>\n"
        strVar += "<thead>\n"
        strVar += "<tr>\n";
        strVar += " <th>选项文字<\/th>\n";
        strVar += " <th>正确答案<\/th>\n";
        strVar += "<\/tr>\n";
        strVar += "<\/thead>\n";
        strVar += "<tbody>\n"
        if (length) {
            for (var i = 0; i < length; i++) {
                strVar += "<tr class=\"choiceRow\">\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\" value=" + list[i] + ">\n";
                strVar += " <\/td>\n";
                strVar += " <td>\n";
                if ($.inArray(i, arr) != -1) {
                    strVar += "     <input type=\"checkbox\" name=\"checkBtn\" class=\"form-control\" checked>\n";
                } else {
                    strVar += "     <input type=\"checkbox\" name=\"checkBtn\" class=\"form-control\">\n";
                }

                strVar += " <\/td>\n";
                strVar += "<\/tr>\n";
            }
        } else {
            for (var i = 0; i < 4; i++) {
                strVar += "<tr class=\"choiceRow\">\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\">\n";
                strVar += " <\/td>\n";
                strVar += " <td>\n";
                strVar += "     <input type=\"checkbox\" name=\"checkBtn\" class=\"form-control\">\n";
                strVar += " <\/td>\n";
                strVar += "<\/tr>\n";
            }
        }
        strVar += "<\/tbody>";
        $('.alterItem').show();
    } else if (type == "填空") {
        strVar += "<caption>题目答案(多个答案请用-&-分隔)<\/caption>\n"
        strVar += "<tbody>\n"
        strVar += "<tr class=\"choiceRow\">\n";
        strVar += " <td>\n";
        if (arr) {
            strVar += "     <input type=\"text\" class=\"form-control blankAnswer\" value=\"" + arr.join("-&-") + "\">\n";
        } else {
            strVar += "     <input type=\"text\" class=\"form-control blankAnswer\" placeholder=\"请输入答案\">\n";
        }
        strVar += " <\/td>\n";
        strVar += "<\/tr>\n";
        strVar += "<\/tbody>";
        $('.alterItem').hide();

    } else if (type == "问答") {
        strVar += "<caption>题目答案<\/caption>\n"
        strVar += "<tbody>\n"
        strVar += "<tr class=\"choiceRow\">\n";
        strVar += " <td>\n";
        strVar += "     <textarea type=\"text\" class=\"form-control QAAnswer\" placeholder=\"请输入答案\"><\/textarea>\n";
        strVar += " <\/td>\n";
        strVar += "<\/tr>\n";
        strVar += "<\/tbody>";
        $('.alterItem').hide();
    }
    return strVar;
}

// 问题验证
function checkQs() {
    if ($('.choiceTitle').val() == "") {
        layer.alert('问题标题不能为空', { icon: 5, anim: 6 });
        return false;
    }

    if ($('.choiceScore').val() == "") {
        layer.alert('问题分数不能为空', { icon: 5, anim: 6 });
        return false;
    }

    // 单选
    var radioItem = $('input[name="radioBtn"]');
    if (radioItem.length != 0 && !radioItem.is(':checked')) {
        layer.alert('请选中正确选项', { icon: 5, anim: 6 });
        return false;
    }

    // 多选
    var checkItem = $('input[name="checkBtn"]');
    var checkRight = $('input[name="checkBtn"]:checked');
    if (checkItem.length != 0 && checkRight.length < 2) {
        layer.alert('请至少选择两个正确选项', { icon: 5, anim: 6 });
        return false;
    }

    // 填空
    var blankItem = $('.blankAnswer');
    if (blankItem.length != 0 && blankItem.val() == "") {
        layer.alert('请输入答案', { icon: 5, anim: 6 });
        return false;
    }

    // 问答
    var qaItem = $('.QAAnswer');
    if (qaItem.length != 0 && qaItem.val() == "") {
        layer.alert('请输入答案', { icon: 5, anim: 6 });
        return false;
    }

    // 选项文字
    var text_len = 0;
    if ($('.choiceItem').length > 0) {
        $('.choiceItem').each(function() {
            if ($(this).val() == "") {
                text_len++;
            }
        });
        if (text_len > 0) {
            layer.alert('选项文字不能为空', { icon: 5, anim: 6 });
            return false;
        }
    }

    return true;
}

// 填充问题面板
function addOptions(type, index, title, score, correct, list, qsId) {
    switch(type) {
        case 1:
            type = "单选";
            break;
        case 2:
            type = "多选";
            break;
        case 3:
            type = "判断";
            break;
        case 4:
            type = "填空";
            break;
        case 5:
            type = "简答";
            break;
    }
    var question_len = 0,
        correctAnswer = [],
        test_title = "",
        test_score = "";

    // 序号
    if (index) {
        question_len = parseInt(index) + 1;
    } else {
        question_len = $('.edit-container .questions').length + 1;
    }

    // 正确选项
    if (correct) {
        correctAnswer = correct;
    } else {
        if (type == "单选") {
            var radiolist = $('input[name="radioBtn"]:checked').parents('.choiceRow');
            correctAnswer.push(radiolist.index());
        } else if (type == "多选") {
            var checklist = $('input[name="checkBtn"]:checked');
            $.each(checklist, function(index, val) {
                correctAnswer.push($(this).parents('.choiceRow').index());
            });
        } else if (type == "填空") {
            correctAnswer = $('.blankAnswer').val().split("-&-");
        }
    }

    // 标题
    if (title) {
        test_title = '问题<span class="qs-index">' + question_len + '</span>：<span class="title-text">' + title + '</span> (<span class="qs-type">' + type + '</span>)';
    } else {
        test_title = '问题<span class="qs-index">' + question_len + '</span>：<span class="title-text">' + $('.choiceTitle').val() + '</span> (<span class="qs-type">' + type + '</span>)';
    }

    // 分数
    if (score) {
        test_score =  score;
    } else {
        test_score = $('.choiceScore').val();
    }

    console.log(test_score)

    // 选项
    var option_str = "";
    option_str += "    <div class=\"qs-content\">\n";
    option_str += "     <p><span class=\"qs-title\">" + test_title + "\n";
    option_str += "     <\/span><span class=\"qs-score pull-right\">" + test_score + "分\n";
    option_str += "     <\/span><\/p>\n";
    if (type == "单选") {
        if (list) {
            for (var i = 0; i < list.length; i++) {

                option_str += "     <div class=\"qs-options\">\n";
                if ($.inArray(i, correctAnswer) != -1) {
                    option_str += "         <input type=\"radio\" name=\"qs" + question_len + "\" checked>\n";
                } else {
                    option_str += "         <input type=\"radio\" name=\"qs" + question_len + "\">\n";
                }

                option_str += "         <label>" + qs_numberArr[i] + "、" + list[i] + "<\/label>\n";
                option_str += "     <\/div>\n";
            }
        } else {
            for (var i = 0; i < $('.choiceRow').length; i++) {

                option_str += "     <div class=\"qs-options\">\n";
                if ($.inArray(i, correctAnswer) != -1) {
                    option_str += "         <input type=\"radio\" name=\"qs" + question_len + "\" checked disabled>\n";
                } else {
                    option_str += "         <input type=\"radio\" name=\"qs" + question_len + "\" disabled>\n";
                }

                option_str += "         <label>" + qs_numberArr[i] + "、" + $('.choiceRow').eq(i).find('.choiceItem').val() + "<\/label>\n";
                option_str += "     <\/div>\n";
            }
        }

    } else if (type == "多选") {
        if (list) {
            for (var i = 0; i < list.length; i++) {

                option_str += "     <div class=\"qs-options\">\n";
                if ($.inArray(i, correctAnswer) != -1) {
                    option_str += "         <input type=\"checkbox\" name=\"qs" + question_len + "\" checked>\n";
                } else {
                    option_str += "         <input type=\"checkbox\" name=\"qs" + question_len + "\">\n";
                }

                option_str += "         <label>" + qs_numberArr[i] + "、" + list[i] + "<\/label>\n";
                option_str += "     <\/div>\n";
            }
        } else {
            for (var i = 0; i < $('.choiceRow').length; i++) {

                option_str += "     <div class=\"qs-options\">\n";
                if ($.inArray(i, correctAnswer) != -1) {
                    option_str += "         <input type=\"checkbox\" name=\"qs" + question_len + "\" checked disabled>\n";
                } else {
                    option_str += "         <input type=\"checkbox\" name=\"qs" + question_len + "\" disabled>\n";
                }
                option_str += "         <label>" + qs_numberArr[i] + "、" + $('.choiceRow').eq(i).find('.choiceItem').val() + "<\/label>\n";
                option_str += "     <\/div>\n";
            }
        }


    } else if (type == "填空") {
        option_str += "<div class=\"inputGroup\">";
        for (var i = 0; i < correctAnswer.length; i++) {
            option_str += "<input type=\"text\" disabled class=\"form-control\" value=\"" + correctAnswer[i] + "\">"
        }
        option_str +=  "<\/div>";
        
    } else if (type == "问答") {
        if (correct) {
            option_str += "<textarea type=\"text\" disabled class=\"form-control\">" + correctAnswer + "</textarea>"
        } else {
            option_str += "<textarea type=\"text\" disabled class=\"form-control\">" + $('.QAAnswer').val() + "</textarea>"    
        }
    }
    option_str += " <\/div>\n";
    option_str += " <div class=\"qs-control\">\n";
    option_str += "     <ul>\n";
    if (qsId) {
        option_str += "        <li class=\"qs-del\">删除</li>\n";
        option_str += "        <li class=\"qs-edit\">编辑题库</li>\n";
    } else {
        option_str += "        <li class=\"qs-del\" onclick=\"delQs(this)\">删除</li>\n";
        option_str += "        <li class=\"qs-edit\" onclick=\"editQs(this)\">编辑</li>\n";
    }
    
    option_str += "     </ul>\n";
    option_str += " <\/div>\n";
    return option_str;
}

//将0-9的数字前面加上0，例1变为01
function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}


function getChecked(selectQs) {
    if (selectQs == "") {  
        return;  
    }  
    var oneches = document.getElementsByName("selectLib");  
    for (var i = 0; i < oneches.length; i++) {  
        //全局变量中含有id，则该复选框选中  
        if(contains(selectQs, oneches[i].id.substring(2))) {  
            oneches[i].checked = "checked";  
        }  
    }  
}

function contains(arr, ele) {  
    if (arr== "" ){  
        return;  
    }  
    /*若参数obj为字符串时，需要转换成数组*/  
    var i = arr.length;  
    while (i--) {  
        if (arr[i] == ele) {  
            return true;    
        }    
    }    
    return false;    
}  
