#!/usr/bin/env python2
# -*- coding: utf-8 -*-
import os
import requests
from sys import argv
from urllib import unquote
import urllib 
import time
script, link, aid, pub, theme, son, create_time, audio_time = argv
urlOld = unquote(link)
url = urlOld.replace('&amp;', '&')
print url
def request(localName):
    payload = {'localname':localName,'link':url, 'id':aid, 'pub':pub, 'theme':theme, 'son':son, 'create_time':create_time, 'audio_time':audio_time}
    r = requests.post('http://ym.yuemai168.com/?m=Play&c=index&a=qiniuUpload', payload)
    return r.text
def download():
    fileName = time.time();
    urllib.urlretrieve(url, "./Uploads/%s.arm"%fileName)
    request("%s.arm"%fileName)
if __name__ == "__main__":
    print download()
