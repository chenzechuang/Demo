#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import sys
from sys import argv
sys.path.append('./autoregeasemob.py')
sys.path.append('./autoregnetease.py')
from autoregeasemob import checkUserEasemob
from autoregnetease import checkUserNetease
script, userid, username = argv

if __name__ == "__main__":
    checkUserEasemob().check('ym%s' % userid, username)
    checkUserNetease().check('ym%s' % userid, username)
