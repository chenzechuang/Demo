#!/usr/bin/env python
# -*- coding: utf-8 -*-
import binascii
import hashlib
import json
import os
import random
import requests
import time
"""run A cron for this script every min"""
#get token
AppKey = 'c8abf515c46e3a29154c45293aaebdb1'
Nonce = '';                    #随机字符串最大128个字符，也可以小于该数
charHex = '0123456789abcdef';
for i in range(0, 128):
    index = int(15 * random.random());
    Nonce = Nonce + charHex[index];
CurTime = int(time.time())
AppSecret = 'ec7810e413ed'
CheckSum = hashlib.sha1('%s%s%s' % (AppSecret, Nonce, str(CurTime))).hexdigest()

class autoReg:
    def check(self):
        print hashlib.md5('1').hexdigest()
    #run reg
    def regUser(self, userid, username):
        headers = {"Content-Type":"application/x-www-form-urlencoded;charset=utf-8",
        "AppKey":AppKey,
        "Nonce":Nonce,
        "CurTime":str(CurTime),
        "CheckSum":CheckSum}
        payload = {'accid':userid, 'name':username, 'token':hashlib.md5(userid).hexdigest()}
        r = requests.post('https://api.netease.im/nimserver/user/create.action', payload, headers=headers)
        data = r.json()
        #if error. then log the record
        if data['code'] != 200:
            return False
        else:
            print 'netease注册成功'
            return True
class checkUserNetease:
    def check(self, userid, username):
        headers = {"Content-Type":"application/x-www-form-urlencoded;charset=utf-8",
        "AppKey":AppKey,
        "Nonce":Nonce,
        "CurTime":str(CurTime),
        "CheckSum":CheckSum}
        payload = {'accids':json.dumps([userid])}
        r = requests.post('https://api.netease.im/nimserver/user/getUinfos.action', payload, headers=headers)
        data = r.json()
        print 'netease'
        print data
        if data['code'] != 200:
            if autoReg().regUser(userid, username):
                return True
            else:
                return False
        else:
            print 'netease有了'
            return True

class renameNetease:
    def check(self, userid, username):
        headers = {"Content-Type":"application/x-www-form-urlencoded;charset=utf-8",
        "AppKey":AppKey,
        "Nonce":Nonce,
        "CurTime":str(CurTime),
        "CheckSum":CheckSum}
        payload = {'accid':userid,'name':username}
        r = requests.post('https://api.netease.im/nimserver/user/updateUinfo.action', payload, headers=headers)
        data = r.json()
        print 'netease'
        print data
#        if data['code'] != 200:
#            if autoReg().regUser(userid, username):
#                return True
#            else:
#                return False
#        else:
#            print 'netease有了'
#            return True
        
#renameNetease().check('1','ling')