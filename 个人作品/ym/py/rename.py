#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import sys
from sys import argv
sys.path.append('./autoregeasemob.py')
sys.path.append('./autoregnetease.py')
from autoregeasemob import renameEasemob
from autoregnetease import renameNetease
script, userid, username = argv

if __name__ == "__main__":
    renameEasemob().check('t%s' % userid, username)
    renameNetease().check('t%s' % userid, username)