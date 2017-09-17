#!/usr/bin/env python
# -*- coding: utf-8 -*-
import hashlib
import json
import os
import requests
from time import time
#get token
class Token:
    #load local log. recorded easemob return access token
    def loadFile(self):
        return self.getToken()
#        fo = open("token.config", "a+")
#        line = fo.read()
#        
#        if line == '':
#            return self.getToken()
#        else:
#            arr = json.loads(line)
#            #if expired then get a new one or return the local record
#            if time() > int(arr['exp']):
#                return self.getToken()
#            else:
#                return arr['access_token']
#        fo.close()

    #requert token from easemob
    def getToken(self):
        payload = {'grant_type': 'client_credentials', 'client_id': 'YXA6w3d4QBkPEee-QzuDy_oqyw', 'client_secret': 'YXA6OeNXOKJx-_O2Nk_c1q3DFw6oBzY'}
        r = requests.post('https://a1.easemob.com/1155170329178961/ym/token', json.dumps(payload))
        jsonRequest = r.json()
        jsonRequest['exp'] = int(r.json()['expires_in']) + int(time())
        return jsonRequest['access_token']
#        if self.writeData(json.dumps(jsonRequest)) == True:
#            return jsonRequest['access_token']
    #write to local file
#    def writeData(self, data):
#        fo = open("token.config", "w+")
#        fo.write(data)
#        fo.close()
#        return True
#auto mulit reg AC
class autoReg:
    #run reg
    def regUser(self, userid, username):
        headers = {"Content-Type":"application/json", "Authorization":"Bearer " + Token().loadFile()}
        payload = {'username':userid, 'password':hashlib.md5(userid).hexdigest(), 'nickname':username}
        r = requests.post('https://a1.easemob.com/1155170329178961/ym/users', json.dumps(payload), headers=headers)
        data = r.json()
        #if error. then log the record
        if data.has_key('error'):
            return False
        else:
            print 'easemob注册成功'
            return True
class checkUserEasemob:
    def check(self, userid, username):
        headers = {"Authorization":"Bearer " + Token().loadFile()}
        r = requests.get('https://a1.easemob.com/1155170329178961/ym/users/' + userid, headers=headers)
        data = r.json()
        print 'easemob'
        print data
        if data.has_key('error'):
            if autoReg().regUser(userid, username):
                return True
            else:
                return False
        else:
            print 'easemob有了'
            return True
class renameEasemob:
    def check(self, userid, username):
        headers = {"Authorization":"Bearer " + Token().loadFile()}
        body = {"nickname": username}
        r = requests.put('https://a1.easemob.com/1155170329178961/ym/users/' + userid, data=json.dumps(body), headers=headers)
        data = r.json()
        print 'easemob'
        print data
#        if data.has_key('error'):
#            if autoReg().regUser(userid, username):
#                return True
#            else:
#                return False
#        else:
#            print 'easemob有了'
#            return True
