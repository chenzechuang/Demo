#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import os
import subprocess
import sys
from sys import argv
import threading
import time
from urllib import unquote
import urllib 
import requests
script, aid, origin, callback = argv
from qiniu import Auth, put_file, etag, urlsafe_base64_encode
import qiniu.config
#需要填写你的 Access Key 和 Secret Key
access_key = 'q68uV3Z2NKYMq3cu7X_ZGreZgGUM9P22gwFC1H3M'
secret_key = 'rGsAS6rKE1MD7Z6wKQLgLjN-GHRiSvVYCrORc1jd'
#构建鉴权对象
q = Auth(access_key, secret_key)
#要上传的空间
bucket_name = 'ym-resource'
#当前名
current = time.time()
#7牛url
qnUrl = "http://opgljb0gg.bkt.clouddn.com/"
#后缀
suffix = '.mp3'
def upload(originName, filename):
    #上传到七牛后保存的文件名
    key = filename + suffix;
    #生成上传 Token，可以指定过期时间等
    token = q.upload_token(bucket_name, key, 3600)
    #要上传文件的本地路径
    localfile = "./Uploads/" + originName + suffix
    ret, info = put_file(token, key, localfile)
    assert ret['key'] == key
    assert ret['hash'] == etag(localfile)
#    上传完成删除已经转换文件
    os.unlink("./Uploads/" +key)
    return ret['key']
tryTime = 0
def callbackAction(dic):
    global tryTime
    #callback
    urlOldCallback = unquote(callback)
    urlCallback = urlOldCallback.replace('&amp;', '&')
    r = requests.post(urlCallback, dic)
    print r.status_code
    #如果不成功
    if r.status_code != requests.codes.ok and tryTime < 3:
        print tryTime
        tryTime += 1
        time.sleep(5)
        callbackAction(dic)
    
uploadNames = {}
def executeCmd():
    def runCmd(args):
        strcmd = ''
        if args == 0:
#            strcmd = "ffmpeg -ss 0 -i ./Uploads/" + str(current) + ".mp3 -t 15 -c:a aac -b:a 64k ./Uploads/" + str(current) + "_lite.m4a"
            strcmd = "ffmpeg -i ./Uploads/" + str(current) + ".mp3 -t 15 -codec:a libmp3lame -q:a 8 ./Uploads/" + str(current) + "_lite" +suffix
        elif args == 1:
#            strcmd = "ffmpeg -i ./Uploads/" + str(current) + ".mp3 -c:a aac -b:a 64k ./Uploads/" + str(current) + ".m4a"
            strcmd = "ffmpeg -i ./Uploads/" + str(current) + ".mp3 -codec:a libmp3lame -q:a 8 ./Uploads/" + str(current) + "_full" +suffix
        process = subprocess.Popen(strcmd, shell=True)
        process.wait()
        print "done"

#       开始上传
        
        if args == 0:
            uploadName = upload(str(current) + "_lite", str(current)+ "_lite")
            uploadNames['lite'] = qnUrl+uploadName
        elif args == 1:
            uploadName = upload(str(current) + "_full", str(current) + "_full")
            uploadNames['full']=qnUrl+uploadName
        if len(uploadNames)==2:
            #删除原文件 
            os.unlink("./Uploads/" + str(current) + ".mp3")
#            上传完毕 够了2个 然后call back
            uploadNames['aid']=aid
            print uploadNames
            #callback
            callbackAction(uploadNames)
            
    try:
#        上传二个文件
        thread = threading.Thread(target=runCmd, args=(0, ))
        thread.start()
        thread = threading.Thread(target=runCmd, args=(1,))
        thread.start()
    except Exception, ex:
        print 'Exception: %s' % str(ex)
        
def downloadMp3():
    urlOld = unquote(origin)
    url = urlOld.replace('&amp;', '&')
    urllib.urlretrieve (url, "./Uploads/"+str(current)+".mp3")
    print url
    executeCmd()
##处理音频
#executeCmd()
##下载
downloadMp3()