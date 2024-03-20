#!/bin/bash
cd /home/whitehotrobot/render_efx.git
rm -rf /home/whitehotrobot/nameBasedRouting/h
mkdir /home/whitehotrobot/nameBasedRouting/h
cp /home/whitehotrobot/render_efx.git/. /home/whitehotrobot/nameBasedRouting/h/. -r
cd /home/whitehotrobot/nameBasedRouting
chmod 777 . -R
git add .
git commit -m 'sync'
cat ~/github_token
git push origin main
cd /home/whitehotrobot/render_efx.git
