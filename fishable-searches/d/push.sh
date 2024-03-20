#!/bin/bash
cd /home/whitehotrobot/fishable-searches.000webhostapp.com.git
rm -rf /home/whitehotrobot/nameBasedRouting/d
mkdir /home/whitehotrobot/nameBasedRouting/d
cp /home/whitehotrobot/fishable-searches.000webhostapp.com.git/. /home/whitehot/ /home/whitehotrobot/nameBasedRouting/d/. -r
cd /home/whitehotrobot/nameBasedRouting
chmod 777 . -R
git add .
git commit -m 'sync'
cat ~/github_token
git push origin main
cd /home/whitehotrobot/fishable-searches.000webhostapp.com.git
