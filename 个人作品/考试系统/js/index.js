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
            qs_type = $(this).html();
            $('.shadow').show();
            $('.continue').attr('disabled', false);
            $('.previous').attr('disabled', false);
            $('.alterItem button').attr('disabled', false);
            $('.addBtnGroup button').show();
            $('.save').hide();

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
                    }
                }
                if (this.checked == false) {
                    //取消复选框时 含有该id时将id从全局变量中去除
                    if (selectQs.indexOf(qs_id) != -1) {
                        var index = selectQs.indexOf(qs_id);
                        selectQs.splice(index, 1);
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
    $('.content').on('click', '.qs-del', function (event) {
        delQs(this);
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
        var qs = $(this).find('.qs-content');
        if (qsId && index > qsId.length - 1) {
            qsId.push(null)
        }
        var qs_type = qs.find('.qs-type').html();
        console.log(qs_type);
        
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

        var qs_title = qs.find('.title-text').html();
        var qs_score = $(this).find('.score').val();
        var qs_dept = $(this).find('.dept').val();
        var qs_desc = $(this).find('.desc').val();
        var rightArr = [];
        var errorArr = [];
        var answerWord = "";
        var answerWord_len = 0;
        if (qs_type == 1 || qs_type == 2) {
            var rightOptions = qs.find('input:checked');
            $.each(rightOptions, function(index, val) {
                rightArr.push(($(this).next().html().split("、")[1]));
            });
            var errorOptions = qs.find('input').not(":checked");

            $.each(errorOptions, function(index, val) {
                errorArr.push(($(this).next().html().split("、")[1]));
            });
        }  else if (qs_type == 4) {
            $.each(qs.find('input'), function(index, val) {
                rightArr.push($(this).val())
            });
            answerWord_len = rightArr.length
            answerWord = rightArr.join("-&-")
        } else {
            answerWord = qs.find('input').val();
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
                "description": qs_dept,
                "score": qs_score,
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
                "description": qs_dept,
                "score": qs_score,
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
/*    window.onbeforeunload = function() {
        localStorage.clear();
        localStorage.setItem('index', doTest_index);
        localStorage.setItem('left_time', left_time);
    }*/

// 根据按钮类型填充弹窗
function addQs(type, length, arr, list) {
    var strVar = "";
    if (type == "单选" || type == "1") {
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

    } else if (type == "多选" || type == "2") {
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
    } else if (type == "填空" || type == "4") {
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

    } else if (type == "问答" || type == "5") {
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

// 添加选项
function addChoice(type) {
    if ($('.addOption tbody tr').length == 10) {
        layer.alert('最多设置10个选项', { icon: 5, anim: 6 });
        return;
    }
    if (type == "单选" || type == "1") {
        $('.addOption tbody').append("<tr class=\"choiceRow\"><td><input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\"></input></td><td><input type=\"radio\" name=\"radioBtn\" class=\"form-control\"></td></tr>");
    } else if (type == "多选" || type == "2") {
        $('.addOption tbody').append("<tr class=\"choiceRow\"><td><input type=\"text\" class=\"form-control choiceItem\" placeholder=\"选项文字\"></input></td><td><input type=\"checkbox\" name=\"checkBtn\" class=\"form-control\"></td></tr>");
    }
};

// 减少选项
function delChoice() {
    if ($('.addOption tbody tr').length == 2) {
        layer.alert('最少设置2个选项', { icon: 5, anim: 6 });
        return;
    }
    
    $('.addOption tbody tr:last').remove();
};

// 问题验证
function checkQs() {
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
function addOptions(type, index, title, info, correct, list, qsId) {
    
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
        test_desc = "";
        test_score = "";
        test_dept = "";

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
        if (type == "单选" || type == "判断") {
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
    test_title = '问题<span class="qs-index">' + question_len + '</span>：<span class="title-text">' + title + '</span> (<span class="qs-type">' + type + '</span>)';

    // 分数
    if (typeof info == "object") {
        test_score = info.score;
        test_dept = info.dept;
        test_desc = info.desc;
    } else {
        test_score = info;
    }

    // 选项
    var option_str = "";
    option_str += "     <input type=\"hidden\" class=\"score\" value=" + test_score + ">\n";
    option_str += "     <input type=\"hidden\" class=\"dept\" value=" + test_dept + ">\n";
    option_str += "     <input type=\"hidden\" class=\"desc\" value=" + test_desc + ">\n";
    option_str += "    <div class=\"qs-content\">\n";
    option_str += "     <p><span class=\"qs-title\">" + test_title + "\n";
    option_str += "     <\/span><span class=\"qs-score pull-right\">" + test_score + "分\n";
    option_str += "     <\/span><\/p>\n";
    if (type == "单选" || type == "判断") {
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
        console.log(correctAnswer);
        
        option_str += "<div class=\"inputGroup\">";
        if (Array.isArray(correctAnswer)) {
            for (var i = 0; i < correctAnswer.length; i++) {
                option_str += "<input type=\"text\" disabled class=\"form-control\" value=\"" + correctAnswer[i] + "\">"
            }
        } else {
            for (var i = 0; i < correctAnswer; i++) {
                option_str += "<input type=\"text\" disabled class=\"form-control\" value=\"" + correctAnswer[i] + "\">"
            }
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
    if (qsId) {
        option_str += " <div class=\"qs-control\">\n";
        option_str += "     <ul>\n";
        option_str += "        <li class=\"qs-del\">删除</li>\n";
        option_str += "        <li class=\"qs-edit\" onclick=\"window.location.href = 'questions.html'\">编辑题库</li>\n";
        option_str += "     </ul>\n";
        option_str += " <\/div>\n";
    }
    
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
