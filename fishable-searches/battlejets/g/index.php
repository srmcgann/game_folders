<!DOCTYPE html>
<html>
  <head>
    <title>Battle Jets! multiplayer/online ARENA</title>
    <style>
      /* latin-ext */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(/games_shared_assets/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
      }
      /* latin */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(/games_shared_assets/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
      }
      body,html{
        background: #000;
        margin: 0;
        color: #fff;
        height: 100vh;
        overflow: hidden;
        font-family: Courier Prime;
      }
      #c{
        background:#000;
        display: block;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
      }
      #c:focus{
        outline: none;
      }
      #regFrame{
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000;
        display: none;
      }
      #launchModal{
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000;
        display: none;
        padding: 50px;
      }
      #launchStatus{
        color: #0f8;
      }
      .buttons{
        border: none;
        border-radius: 5px;
        background: #4f88;
        color: #fff;
        padding: 3px;
        min-width: 200px;
        cursor: pointer;
        font-family: Courier Prime;
      }
      .copyButton{
        display: inline-block;
        width: 30px;
        height: 30px;
        background-image: url(/games_shared_assets/clippy.png);
        cursor: pointer;
        z-index: 500;
        background-size: 90% 90%;
        left: calc(50% + 180px);
        background-position: center center;
        background-repeat: no-repeat;
        border: none;
        background-color: #8fcc;
        margin: 5px;
        border-radius: 5px;
        vertical-align: middle;
      }
      #copyConfirmation{
        display: none;
        position: absolute;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        background: #012d;
        color: #8ff;
        opacity: 1;
        text-shadow: 0 0 5px #fff;
        font-size: 46px;
        text-align: center;
        z-index: 1000;
      }
      #innerCopied{
        position: absolute;
        top: 50%;
        width: 100%;
        z-index: 1020;
        text-align: center;
        transform: translate(0, -50%) scale(2.0, 1);
      }
      .resultLink{
        text-decoration: none;
        color: #fff;
        background: #4f86;
        padding: 10px;
        display: inline-block;
      }
      #resultDiv{
        position: absolute;
        margin-top: 50px;
        left: 50%;
        transform: translate(-50%);
      }
    </style>
  </head>
  <body>
    <div id="copyConfirmation"><div id="innerCopied">COPIED!</div></div>
    <canvas id="c" tabindex=0></canvas>
    <iframe id="regFrame"></iframe>
    <div id="launchModal">
      GAME IS LIVE!<br><br>
      <div id="gameLink"></div>
      <br><br><br><br>
      ...awaiting players...<br>
      <div id="launchStatus"></div>
    </div>
    <script>
      /*
      //  to-do
      //    ✔ camera modes
      //    ✔ HUD/info (cockpit mode) [needs gyroscope/altimeter]
      //    ✔ guns/missiles
      //    ✔ sea level / crash scenarios
      //    ✔ rangefinder
      //    ✔  heatseaking missiles
      //    ✔  AIs
      //    ✔  hotkeys
      //    ✔  mute button
      //    ✔  rear-view cam
      //    ✔  scores
      //    *  arena integration 
      //
      */

      c = document.querySelector('#c')
      c.width = 1920
      c.height = 1080
      x = c.getContext('2d')
      C = Math.cos
      S = Math.sin
      t = 0
      T = Math.tan

      rsz=window.onresize=()=>{
        setTimeout(()=>{
          if(document.body.clientWidth > document.body.clientHeight*1.77777778){
            c.style.height = '100vh'
            setTimeout(()=>c.style.width = c.clientHeight*1.77777778+'px',0)
          }else{
            c.style.width = '100vw'
            setTimeout(()=>c.style.height =     c.clientWidth/1.77777778 + 'px',0)
          }
        },0)
      }
      rsz()

      async function Draw(){
        if(!t){
          oX=oY=oZ=Rl=Pt=Yw=0

          buffer = document.createElement('canvas')
          buffer.width = 1920
          buffer.height = 1080
          bctx = buffer.getContext('2d')
          
          Rn = Math.random

          reflect = (a, n) => {
            let d1 = Math.hypot(...a)+.0001
            let d2 = Math.hypot(...n)+.0001
            a[0]/=d1
            a[1]/=d1
            a[2]/=d1
            n[0]/=d2
            n[1]/=d2
            n[2]/=d2
            let dot = -a[0]*n[0] + -a[1]*n[1] + -a[2]*n[2]
            let rx = -a[0] - 2 * n[0] * dot
            let ry = -a[1] - 2 * n[1] * dot
            let rz = -a[2] - 2 * n[2] * dot
            return [-rx*d1, -ry*d1, -rz*d1]
          }

          spawnTunnel = (
              tx, ty, tz,
              rw, cl, sp=1, rad=.5,
              theta1=0, theta2=0,
              theta1ModFreq = 0,
              theta1ModMag  = 0,
              theta2ModFreq = 0,
              theta2ModMag  = 0,
              theta1Offset  = 0,
              theta2Offset  = 0,
              radModFreq    = 0,
              radModMag     = 0,
              radModOffset  = 0,
              showLine=false
            ) => {
            let X_ = X = tx
            let Y_ = Y = ty
            let Z_ = Z = tz
            let ret = []
            let p2a, p2, p2a1, ls
            if(showLine) x.beginPath()
            for(let i=cl+1; i--;){
              let p1 = theta1 + C(Math.PI*2/cl*i*theta1ModFreq + theta1Offset) * theta1ModMag
              let p2 = theta2 + C(Math.PI*2/cl*i*theta2ModFreq + theta2Offset) * theta2ModMag
              let p2a1 = theta2 + C(Math.PI*2/cl*(i+1)*theta2ModFreq + theta2Offset) * theta2ModMag
              let lsa  = rad + C(Math.PI*2/cl*i*radModFreq + radModOffset) * rad /2 *radModMag
              let lsb  = rad + C(Math.PI*2/cl*(i+1)*radModFreq + radModOffset) * rad /2 * radModMag
              if(i==cl){
                p2a = p2
                ls = lsa
              }else if(i==0){
                p2a = p2a1
                ls  = lsb
              }else{
                p2a = (p2 + p2a1)/2
                ls = (lsa+lsb)/2
              }
              let a = []
              for(let j=rw+1;j--;){
                p=Math.PI*2/rw*j + Math.PI/rw
                X = S(p) * ls
                Y = 0
                Z = C(p) * ls
                R(-p2a+Math.PI/2,0,0)
                R(0,0,-p1)
                a = [...a, [X+X_, Y+Y_, Z+Z_]]
              }
              
              ret = [...ret, a]

              if(showLine) {
                X = X_
                Y = Y_
                Z = Z_
                R(Rl,Pt,Yw,1)
                if(Z>0) x.lineTo(...Q())
              }
            
              vx = C(p1) * C(p2) * sp
              vy = S(p2) * sp
              vz = S(p1) * C(p2) * sp
              X_ += vx
              Y_ += vy
              Z_ += vz
            }
            if(showLine) stroke('#f00', '', 2, false)
            a = []
            ret.map((v, i) => {
              if(i){
                let s1 = ret[i]
                let s2 = ret[i-1]
                for(let j = rw;j--;){
                  b = []
                  let l1_ = (j+0)%rw
                  let l2_ = (j+1)%rw
                  X = s1[l1_][0]
                  Y = s1[l1_][1]
                  Z = s1[l1_][2]
                  b = [...b, [X,Y,Z]]
                  X = s1[l2_][0]
                  Y = s1[l2_][1]
                  Z = s1[l2_][2]
                  b = [...b, [X,Y,Z]]
                  X = s2[l2_][0]
                  Y = s2[l2_][1]
                  Z = s2[l2_][2]
                  b = [...b, [X,Y,Z]]
                  X = s2[l1_][0]
                  Y = s2[l1_][1]
                  Z = s2[l1_][2]
                  b = [...b, [X,Y,Z]]
                  a = [...a, b]
                }
              }
            })
            return a
          }
          
          HSVFromRGB = (R, G, B) => {
            let R_=R/256
            let G_=G/256
            let B_=B/256
            let Cmin = Math.min(R_,G_,B_)
            let Cmax = Math.max(R_,G_,B_)
            let val = Cmax //(Cmax+Cmin) / 2
            let delta = Cmax-Cmin
            let sat = Cmax ? delta / Cmax: 0
            let min=Math.min(R,G,B)
            let max=Math.max(R,G,B)
            let hue = 0
            if(delta){
              if(R>=G && R>=B) hue = (G-B)/(max-min)
              if(G>=R && G>=B) hue = 2+(B-R)/(max-min)
              if(B>=G && B>=R) hue = 4+(R-G)/(max-min)
            }
            hue*=60
            while(hue<0) hue+=360;
            while(hue>=360) hue-=360;
            return [hue, sat, val]
          }

          RGBFromHSV = (H, S, V) => {
            while(H<0) H+=360;
            while(H>=360) H-=360;
            let C = V*S
            let X = C * (1-Math.abs((H/60)%2-1))
            let m = V-C
            let R_, G_, B_
            if(H>=0 && H < 60)    R_=C, G_=X, B_=0
            if(H>=60 && H < 120)  R_=X, G_=C, B_=0
            if(H>=120 && H < 180) R_=0, G_=C, B_=X
            if(H>=180 && H < 240) R_=0, G_=X, B_=C
            if(H>=240 && H < 300) R_=X, G_=0, B_=C
            if(H>=300 && H < 360) R_=C, G_=0, B_=X
            let R = (R_+m)*256
            let G = (G_+m)*256
            let B = (B_+m)*256
            return [R,G,B]
          }

          R=(Rl,Pt,Yw,m)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            if(m){
              X-=oX
              Y-=oY
              Z-=oZ
            }
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            //if(m){
            //  X+=oX
            //  Y+=oY
            //  Z+=oZ
            //}
          }
          
          R2=(Rl,Pt,Yw,m=false)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            if(m){
              X-=oX
              Y-=oY
              Z-=oZ
            }
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            //if(m){
            //  X+=oX
            //  Y+=oY
            //  Z+=oZ
            //}
          }

          R3=(Rl,Pt1,Pt2,Yw,m=false)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            if(m){
              X-=oX
              Y-=oY
              Z-=oZ
            }
            Y=S(p=A(Y,Z)+Pt1)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            Y=S(p=A(Y,Z)+Pt2)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            //if(m){
            //  X+=oX
            //  Y+=oY
            //  Z+=oZ
            //}
          }
          Q=()=>{
            fac = k__ ? 250 : 500
            return [c.width/2+X/Z*fac,c.height/2+Y/Z*fac]
          }
          I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0

          async function loadOBJ(url, scale, tx, ty, tz, rl, pt, yw, recenter=true) {
            let res
            await fetch(url, res => res).then(data=>data.text()).then(data=>{
              a=[]
              data.split("\nv ").map(v=>{
                a=[...a, v.split("\n")[0]]
              })
              a=a.filter((v,i)=>i).map(v=>[...v.split(' ').map(n=>(+n.replace("\n", '')))])
              ax=ay=az=0
              a.map(v=>{
                v[1]*=-1
                if(recenter){
                  ax+=v[0]
                  ay+=v[1]
                  az+=v[2]
                }
              })
              ax/=a.length
              ay/=a.length
              az/=a.length
              a.map(v=>{
                X=(v[0]-ax)*scale
                Y=(v[1]-ay)*scale
                Z=(v[2]-az)*scale
                R2(rl,pt,yw,0)
                v[0]=X
                v[1]=Y * (url.indexOf('bug')!=-1?2:1)
                v[2]=Z
              })
              maxY=-6e6
              a.map(v=>{
                if(v[1]>maxY)maxY=v[1]
              })
              a.map(v=>{
                v[1]-=maxY-oY
                v[0]+=tx
                v[1]+=ty
                v[2]+=tz
              })

              b=[]
              data.split("\nf ").map(v=>{
                b=[...b, v.split("\n")[0]]
              })
              b.shift()
              b=b.map(v=>v.split(' '))
              b=b.map(v=>{
                v=v.map(q=>{
                  return +q.split('/')[0]
                })
                v=v.filter(q=>q)
                return v
              })

              res=[]
              b.map(v=>{
                e=[]
                v.map(q=>{
                  e=[...e, a[q-1]]
                })
                e = e.filter(q=>q)
                res=[...res, e]
              })
            })
            return res
          }

          function loadAnimation(name, size, X, Y, Z, rl, pt, yw, speed=1) {
            
            let rootURL = 'https://srmcgann.github.io/animations'
            if(typeof animations == 'undefined') animations = []
            if(typeof animationFrameData == 'undefined') animationFrameData = []
            if(typeof animationsCT == 'undefined') animationsCT = 0
            
            let animation = {
              name                  ,
              speed                 ,
              frameCt:             0,
              fileList:           '',
              curFrame:            0,
              loopRangeStart:      0,
              loopRangeEnd:        0,
              hasLoop:         false,
              looping:         false,
              //frameData:        [],
              loaded:          false,
              active:           true,
              idx:     animationsCT++
            }
            
            fetch(`${rootURL}/${name}/fileList.json`).then(v => v.json()).then(data => {
              animation.fileList = data.fileList
              if(animation.fileList.hasLoop){
                animation.hasLoop = true
                animation.looping = true
                animation.loopRangeStart = animation.fileList.loopRangeStart
                animation.loopRangeEnd = animation.fileList.loopRangeEnd
              }
              let fd = Array(+animation.fileList.fileCount)
              for(let i=0; i<+animation.fileList.fileCount; i++){
                let file = `${rootURL}/${name}/${animation.fileList.fileName}${i+(name.indexOf('tree')!=-1?1:0)}.${animation.fileList.suffix}`
                loadOBJ(file, size, X,Y,Z, rl,pt,yw, false).then(el => {
                  fd[i] = el
                  animation.frameCt++
                  if(animation.frameCt == +animation.fileList.fileCount) {
                    console.log(`loaded animation: ${name}`)
                    console.log('animation: ', animation)
                    animation.loaded = true
                    animations = [...animations, animation]
                    animationFrameData = [...animationFrameData, fd]
                    if(animations.length == 4) {
                      landScapeLoaded = true
                      loadLandscape()
                    }
                  }
                })
              }
            })
            return name
          }
          
          drawAnimation = (ox,oy,oz,animation, scol='#8888', fcol='', lineWidth=2, glowing=true, overrideGlobalAlpha=1, speed=1, scale=1,normal=false, theta=0) => {
            animation.curFrame += animation.speed * speed
            if(0&&animation.hasLoop && animation.looping){
              animation.curFrame %= Math.min(animation.loopRangeEnd, animation.frameCt)
              if(animation.curFrame < 1) animation.curFrame = Math.max(0, animation.loopRangeStart)
            }else{
              animation.curFrame %= animation.frameCt
            };
            (l=animationFrameData[animation.idx])[Math.min(animation.curFrame|0,l.length-2)].map((v, i) => {
              x.beginPath()
              v.map(q=>{
                X = q[0] * scale
                Y = q[1] * scale
                Z = q[2] * scale
                if(normal){
                  let nx1 = normal[0]
                  let ny1 = normal[1]
                  let nz1 = normal[2]
                  let nx2 = normal[3]
                  let ny2 = normal[4]
                  let nz2 = normal[5]
                  let yw = Math.atan2(nx2-nx1, nz2-nz1) 
                  let pt = -Math.acos((ny2-ny1)/(Math.hypot(nx2-nx1, ny2-ny1, nz2-nz1)+.001)) + Math.PI
                  R(0,0,-yw + theta)
                  R(0, pt,0)
                  R(0,0,yw)
                }
                X+=ox
                Y+=oy
                Z+=oz
                R(Rl,Pt,Yw,1)
                if(Z>0) x.lineTo(...Q())
              })
              stroke(scol, fcol, lineWidth, glowing, overrideGlobalAlpha)
            })
          }
          
          geoSphere = (mx, my, mz, iBc, size) => {
            let collapse=0
            let B=Array(iBc).fill().map(v=>{
              X = Rn()-.5
              Y = Rn()-.5
              Z = Rn()-.5
              return  [X,Y,Z]
            })
            for(let m=200;m--;){
              B.map((v,i)=>{
                X = v[0]
                Y = v[1]
                Z = v[2]
                B.map((q,j)=>{
                  if(j!=i){
                    X2=q[0]
                    Y2=q[1]
                    Z2=q[2]
                    d=1+(Math.hypot(X-X2,Y-Y2,Z-Z2)*(3+iBc/40)*3)**4
                    X+=(X-X2)*99/d
                    Y+=(Y-Y2)*99/d
                    Z+=(Z-Z2)*99/d
                  }
                })
                d=Math.hypot(X,Y,Z)
                v[0]=X/d
                v[1]=Y/d
                v[2]=Z/d
                if(collapse){
                  d=25+Math.hypot(X,Y,Z)
                  v[0]=(X-X/d)/1.1
                  v[1]=(Y-Y/d)/1.1         
                  v[2]=(Z-Z/d)/1.1
                }
              })
            }
            mind = 6e6
            B.map((v,i)=>{
              X1 = v[0]
              Y1 = v[1]
              Z1 = v[2]
              B.map((q,j)=>{
                X2 = q[0]
                Y2 = q[1]
                Z2 = q[2]
                if(i!=j){
                  d = Math.hypot(a=X1-X2, b=Y1-Y2, e=Z1-Z2)
                  if(d<mind) mind = d
                }
              })
            })
            a = []
            B.map((v,i)=>{
              X1 = v[0]
              Y1 = v[1]
              Z1 = v[2]
              B.map((q,j)=>{
                X2 = q[0]
                Y2 = q[1]
                Z2 = q[2]
                if(i!=j){
                  d = Math.hypot(X1-X2, Y1-Y2, Z1-Z2)
                  if(d<mind*2){
                    if(!a.filter(q=>q[0]==X2&&q[1]==Y2&&q[2]==Z2&&q[3]==X1&&q[4]==Y1&&q[5]==Z1).length) a = [...a, [X1*size,Y1*size,Z1*size,X2*size,Y2*size,Z2*size]]
                  }
                }
              })
            })
            B.map(v=>{
              v[0]*=size
              v[1]*=size
              v[2]*=size
              v[0]+=mx
              v[1]+=my
              v[2]+=mz
            })
            return [mx, my, mz, size, B, a]
          }

          Normal = (facet, autoFlipNormals=false, X1=0, Y1=0, Z1=0) => {
            let ax = 0, ay = 0, az = 0
            facet.map(q_=>{ ax += q_[0], ay += q_[1], az += q_[2] })
            ax /= facet.length, ay /= facet.length, az /= facet.length
            let b1 = facet[2][0]-facet[1][0], b2 = facet[2][1]-facet[1][1], b3 = facet[2][2]-facet[1][2]
            let c1 = facet[1][0]-facet[0][0], c2 = facet[1][1]-facet[0][1], c3 = facet[1][2]-facet[0][2]
            let crs = [b2*c3-b3*c2,b3*c1-b1*c3,b1*c2-b2*c1]
            d = Math.hypot(...crs)+.001
            let nls = 1 //normal line length
            crs = crs.map(q=>q/d*nls)
            let X1_ = ax, Y1_ = ay, Z1_ = az
            let flip = 1
            if(autoFlipNormals){
              let d1_ = Math.hypot(X1_-X1,Y1_-Y1,Z1_-Z1)
              let d2_ = Math.hypot(X1-(ax + crs[0]/99),Y1-(ay + crs[1]/99),Z1-(az + crs[2]/99))
              flip = d2_>d1_?-1:1
            }
            let X2_ = ax + (crs[0]*=flip), Y2_ = ay + (crs[1]*=flip), Z2_ = az + (crs[2]*=flip)
            return [X1_, Y1_, Z1_, X2_, Y2_, Z2_]
          }

          lineFaceI = (X1, Y1, Z1, X2, Y2, Z2, facet, autoFlipNormals=false, showNormals=false) => {
            let X_, Y_, Z_, d, m, l_,K,J,L,p
            let I_=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
            let Q_=()=>[c.width/2+X_/Z_*600,c.height/2+Y_/Z_*600]
            let R_ = (Rl,Pt,Yw,m)=>{
              let M=Math, A=M.atan2, H=M.hypot
              X_=S(p=A(X_,Y_)+Rl)*(d=H(X_,Y_)),Y_=C(p)*d,X_=S(p=A(X_,Z_)+Yw)*(d=H(X_,Z_)),Z_=C(p)*d,Y_=S(p=A(Y_,Z_)+Pt)*(d=H(Y_,Z_)),Z_=C(p)*d
              if(m){ X_+=oX,Y_+=oY,Z_+=oZ }
            }
            let rotSwitch = m =>{
              switch(m){
                case 0: R_(0,0,Math.PI/2); break
                case 1: R_(0,Math.PI/2,0); break
                case 2: R_(Math.PI/2,0,Math.PI/2); break
              }        
            }
            let [X1_, Y1_, Z1_, X2_, Y2_, Z2_] = Normal(facet, autoFlipNormals, X1, Y1, Z1)
            if(showNormals){
              x.beginPath()
              X_ = X1_, Y_ = Y1_, Z_ = Z1_
              R_(Rl,Pt,Yw,1)
              if(Z_>0) x.lineTo(...Q_())
              X_ = X2_, Y_ = Y2_, Z_ = Z2_
              R_(Rl,Pt,Yw,1)
              if(Z_>0) x.lineTo(...Q_())
              x.lineWidth = 5
              x.strokeStyle='#f004'
              x.stroke()
            }

            let p1_ = Math.atan2(X2_-X1_,Z2_-Z1_)
            let p2_ = -(Math.acos((Y2_-Y1_)/(Math.hypot(X2_-X1_,Y2_-Y1_,Z2_-Z1_)+.001))+Math.PI/2)
            let isc = false, iscs = [false,false,false]
            X_ = X1, Y_ = Y1, Z_ = Z1
            R_(0,-p2_,-p1_)
            let rx_ = X_, ry_ = Y_, rz_ = Z_
            for(let m=3;m--;){
              if(isc === false){
                X_ = rx_, Y_ = ry_, Z_ = rz_
                rotSwitch(m)
                X1_ = X_, Y1_ = Y_, Z1_ = Z_ = 5, X_ = X2, Y_ = Y2, Z_ = Z2
                R_(0,-p2_,-p1_)
                rotSwitch(m)
                X2_ = X_, Y2_ = Y_, Z2_ = Z_
                facet.map((q_,j_)=>{
                  if(isc === false){
                    let l = j_
                    X_ = facet[l][0], Y_ = facet[l][1], Z_ = facet[l][2]
                    R_(0,-p2_,-p1_)
                    rotSwitch(m)
                    let X3_=X_, Y3_=Y_, Z3_=Z_
                    l = (j_+1)%facet.length
                    X_ = facet[l][0], Y_ = facet[l][1], Z_ = facet[l][2]
                    R_(0,-p2_,-p1_)
                    rotSwitch(m)
                    let X4_ = X_, Y4_ = Y_, Z4_ = Z_
                    if(l_=I_(X1_,Y1_,X2_,Y2_,X3_,Y3_,X4_,Y4_)) iscs[m] = l_
                  }
                })
              }
            }
            if(iscs.filter(v=>v!==false).length==3){
              let iscx = iscs[1][0], iscy = iscs[0][1], iscz = iscs[0][0]
              let pointInPoly = true
              ax=0, ay=0, az=0
              facet.map((q_, j_)=>{ ax+=q_[0], ay+=q_[1], az+=q_[2] })
              ax/=facet.length, ay/=facet.length, az/=facet.length
              X_ = ax, Y_ = ay, Z_ = az
              R_(0,-p2_,-p1_)
              X1_ = X_, Y1_ = Y_, Z1_ = Z_
              X2_ = iscx, Y2_ = iscy, Z2_ = iscz
              facet.map((q_,j_)=>{
                if(pointInPoly){
                  let l = j_
                  X_ = facet[l][0], Y_ = facet[l][1], Z_ = facet[l][2]
                  R_(0,-p2_,-p1_)
                  let X3_ = X_, Y3_ = Y_, Z3_ = Z_
                  l = (j_+1)%facet.length
                  X_ = facet[l][0], Y_ = facet[l][1], Z_ = facet[l][2]
                  R_(0,-p2_,-p1_)
                  let X4_ = X_, Y4_ = Y_, Z4_ = Z_
                  if(I_(X1_,Y1_,X2_,Y2_,X3_,Y3_,X4_,Y4_)) pointInPoly = false
                }
              })
              if(pointInPoly){
                X_ = iscx, Y_ = iscy, Z_ = iscz
                R_(0,p2_,0)
                R_(0,0,p1_)
                isc = [[X_,Y_,Z_], [crs[0],crs[1],crs[2]]]
              }
            }
            return isc
          }

          TruncatedOctahedron = ls => {
            let shp = [], a = []
            mind = 6e6
            for(let i=6;i--;){
              X = S(p=Math.PI*2/6*i+Math.PI/6)*ls
              Y = C(p)*ls
              Z = 0
              if(Y<mind) mind = Y
              a = [...a, [X, Y, Z]]
            }
            let theta = .6154797086703867
            a.map(v=>{
              X = v[0]
              Y = v[1] - mind
              Z = v[2]
              R(0,theta,0)
              v[0] = X
              v[1] = Y
              v[2] = Z+1.5
            })
            b = JSON.parse(JSON.stringify(a)).map(v=>{
              v[1] *= -1
              return v
            })
            shp = [...shp, a, b]
            e = JSON.parse(JSON.stringify(shp)).map(v=>{
              v.map(q=>{
                X = q[0]
                Y = q[1]
                Z = q[2]
                R(0,0,Math.PI)
                q[0] = X
                q[1] = Y
                q[2] = Z
              })
              return v
            })
            shp = [...shp, ...e]
            e = JSON.parse(JSON.stringify(shp)).map(v=>{
              v.map(q=>{
                X = q[0]
                Y = q[1]
                Z = q[2]
                R(0,0,Math.PI/2)
                q[0] = X
                q[1] = Y
                q[2] = Z
              })
              return v
            })
            shp = [...shp, ...e]

            coords = [
              [[3,1],[4,3],[4,4],[3,2]],
              [[3,4],[3,3],[2,4],[6,2]],
              [[1,4],[0,3],[0,4],[4,2]],
              [[1,1],[1,2],[6,4],[7,3]],
              [[3,5],[7,5],[1,5],[3,0]],
              [[2,5],[6,5],[0,5],[4,5]]
            ]
            a = []
            coords.map(v=>{
              b = []
              v.map(q=>{
                X = shp[q[0]][q[1]][0]
                Y = shp[q[0]][q[1]][1]
                Z = shp[q[0]][q[1]][2]
                b = [...b, [X,Y,Z]]
              })
              a = [...a, b]
            })
            shp = [...shp, ...a]
            return shp.map(v=>{
              v.map(q=>{
                q[0]/=3
                q[1]/=3
                q[2]/=3
                q[0]*=ls
                q[1]*=ls
                q[2]*=ls
              })
              return v
            })
          }

          Cylinder = (rw,cl,ls1,ls2) => {
            let a = []
            for(let i=rw;i--;){
              let b = []
              for(let j=cl;j--;){
                X = S(p=Math.PI*2/cl*j) * ls1
                Y = (1/rw*i-.5)*ls2
                Z = C(p) * ls1
                b = [...b, [X,Y,Z]]
              }
              a = [...a, b]
              for(let j=cl;j--;){
                b = []
                X = S(p=Math.PI*2/cl*j) * ls1
                Y = (1/rw*i-.5)*ls2
                Z = C(p) * ls1
                b = [...b, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*(j+1)) * ls1
                Y = (1/rw*i-.5)*ls2
                Z = C(p) * ls1
                b = [...b, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*(j+1)) * ls1
                Y = (1/rw*(i+1)-.5)*ls2
                Z = C(p) * ls1
                b = [...b, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*j) * ls1
                Y = (1/rw*(i+1)-.5)*ls2
                Z = C(p) * ls1
                b = [...b, [X,Y,Z]]
                a = [...a, b]
              }
            }
            b = []
            for(let j=cl;j--;){
              X = S(p=Math.PI*2/cl*j) * ls1
              Y = ls2/2
              Z = C(p) * ls1
              b = [...b, [X,Y,Z]]
            }
            a = [...a, b]
            return a
          }

          Tetrahedron = size => {
            ret = []
            a = []
            let h = size/1.4142/1.25
            for(i=3;i--;){
              X = S(p=Math.PI*2/3*i) * size/1.25
              Y = C(p) * size/1.25
              Z = h
              a = [...a, [X,Y,Z]]
            }
            ret = [...ret, a]
            for(j=3;j--;){
              a = []
              X = 0
              Y = 0
              Z = -h
              a = [...a, [X,Y,Z]]
              X = S(p=Math.PI*2/3*j) * size/1.25
              Y = C(p) * size/1.25
              Z = h
              a = [...a, [X,Y,Z]]
              X = S(p=Math.PI*2/3*(j+1)) * size/1.25
              Y = C(p) * size/1.25
              Z = h
              a = [...a, [X,Y,Z]]
              ret = [...ret, a]
            }
            ax=ay=az=ct=0
            ret.map(v=>{
              v.map(q=>{
                ax+=q[0]
                ay+=q[1]
                az+=q[2]
                ct++
              })
            })
            ax/=ct
            ay/=ct
            az/=ct
            ret.map(v=>{
              v.map(q=>{
                q[0]-=ax
                q[1]-=ay
                q[2]-=az
              })
            })
            return ret
          }

          Cube = size => {
            for(CB=[],j=6;j--;CB=[...CB,b])for(b=[],i=4;i--;)b=[...b,[(a=[S(p=Math.PI*2/4*i+Math.PI/4),C(p),2**.5/2])[j%3]*(l=j<3?size/1.5:-size/1.5),a[(j+1)%3]*l,a[(j+2)%3]*l]]
            return CB
          }

          Octahedron = size => {
            ret = []
            let h = size/1.25
            for(j=8;j--;){
              a = []
              X = 0
              Y = 0
              Z = h * (j<4?-1:1)
              a = [...a, [X,Y,Z]]
              X = S(p=Math.PI*2/4*j) * size/1.25
              Y = C(p) * size/1.25
              Z = 0
              a = [...a, [X,Y,Z]]
              X = S(p=Math.PI*2/4*(j+1)) * size/1.25
              Y = C(p) * size/1.25
              Z = 0
              a = [...a, [X,Y,Z]]
              ret = [...ret, a]
            }
            return ret      
          }

          Dodecahedron = size => {
            ret = []
            a = []
            mind = -6e6
            for(i=5;i--;){
              X=S(p=Math.PI*2/5*i + Math.PI/5)
              Y=C(p)
              Z=0
              if(Y>mind) mind=Y
              a = [...a, [X,Y,Z]]
            }
            a.map(v=>{
              X = v[0]
              Y = v[1]-=mind
              Z = v[2]
              R(0, .553573, 0)
              v[0] = X
              v[1] = Y
              v[2] = Z
            })
            b = JSON.parse(JSON.stringify(a))
            b.map(v=>{
              v[1] *= -1
            })
            ret = [...ret, a, b]
            mind = -6e6
            ret.map(v=>{
              v.map(q=>{
                X = q[0]
                Y = q[1]
                Z = q[2]
                if(Z>mind)mind = Z
              })
            })
            d1=Math.hypot(ret[0][0][0]-ret[0][1][0],ret[0][0][1]-ret[0][1][1],ret[0][0][2]-ret[0][1][2])
            ret.map(v=>{
              v.map(q=>{
                q[2]-=mind+d1/2
              })
            })
            b = JSON.parse(JSON.stringify(ret))
            b.map(v=>{
              v.map(q=>{
                q[2]*=-1
              })
            })
            ret = [...ret, ...b]
            b = JSON.parse(JSON.stringify(ret))
            b.map(v=>{
              v.map(q=>{
                X = q[0]
                Y = q[1]
                Z = q[2]
                R(0,0,Math.PI/2)
                R(0,Math.PI/2,0)
                q[0] = X
                q[1] = Y
                q[2] = Z
              })
            })
            e = JSON.parse(JSON.stringify(ret))
            e.map(v=>{
              v.map(q=>{
                X = q[0]
                Y = q[1]
                Z = q[2]
                R(0,0,Math.PI/2)
                R(Math.PI/2,0,0)
                q[0] = X
                q[1] = Y
                q[2] = Z
              })
            })
            ret = [...ret, ...b, ...e]
            ret.map(v=>{
              v.map(q=>{
                q[0] *= size/2
                q[1] *= size/2
                q[2] *= size/2
              })
            })
            return ret
          }

          Icosahedron = size => {
            ret = []
            let B = [
              [[0,3],[1,0],[2,2]],
              [[0,3],[1,0],[1,3]],
              [[0,3],[2,3],[1,3]],
              [[0,2],[2,1],[1,0]],
              [[0,2],[1,3],[1,0]],
              [[0,2],[1,3],[2,0]],
              [[0,3],[2,2],[0,0]],
              [[1,0],[2,2],[2,1]],
              [[1,1],[2,2],[2,1]],
              [[1,1],[2,2],[0,0]],
              [[1,1],[2,1],[0,1]],
              [[0,2],[2,1],[0,1]],
              [[2,0],[1,2],[2,3]],
              [[0,0],[0,3],[2,3]],
              [[1,3],[2,0],[2,3]],
              [[2,3],[0,0],[1,2]],
              [[1,2],[2,0],[0,1]],
              [[0,0],[1,2],[1,1]],
              [[0,1],[1,2],[1,1]],
              [[0,2],[2,0],[0,1]],
            ]
            for(p=[1,1],i=38;i--;)p=[...p,p[l=p.length-1]+p[l-1]]
            phi = p[l]/p[l-1]
            a = [
              [-phi,-1,0],
              [phi,-1,0],
              [phi,1,0],
              [-phi,1,0],
            ]
            for(j=3;j--;ret=[...ret, b])for(b=[],i=4;i--;) b = [...b, [a[i][j],a[i][(j+1)%3],a[i][(j+2)%3]]]
            ret.map(v=>{
              v.map(q=>{
                q[0]*=size/2.25
                q[1]*=size/2.25
                q[2]*=size/2.25
              })
            })
            cp = JSON.parse(JSON.stringify(ret))
            out=[]
            a = []
            B.map(v=>{
              idx1a = v[0][0]
              idx2a = v[1][0]
              idx3a = v[2][0]
              idx1b = v[0][1]
              idx2b = v[1][1]
              idx3b = v[2][1]
              a = [...a, [cp[idx1a][idx1b],cp[idx2a][idx2b],cp[idx3a][idx3b]]]
            })
            out = [...out, ...a]
            return out
          }

          stroke = (scol, fcol, lwo=1, od=true, oga=1) => {
            if(scol){
              context.closePath()
              if(od) x.globalAlpha = .2*oga
              context.strokeStyle = scol
              context.lineWidth = Math.min(1000,100*lwo/Z)
              if(od) x.stroke()
              context.lineWidth /= 4
              context.globalAlpha = 1*oga
              context.stroke()
            }
            if(fcol){
              context.globalAlpha = 1*oga
              context.fillStyle = fcol
              context.fill()
            }
            x.globalAlpha = 1
          }

          bezTo = (X1,Y1,Z1,X2,Y2,Z2,col1,col2,lw=1,dual=true,horizontal=true) =>  {
            if(horizontal){
              Xa = X1 + (X2-X1)/3*2
              Ya = Y1
              Za = Z1 + (Z2-Z1)/3*2
              Xb = X1 + (X2-X1)/3*1
              Yb = Y2
              Zb = Z1 + (Z2-Z1)/3*2
            }else{
              Xa = X1
              Ya = Y1 + (Y2-Y1)/3*2
              Za = Z1 + (Z2-Z1)/3*2
              Xb = X2
              Yb = Y1 + (Y2-Y1)/3*1
              Zb = Z1 + (Z2-Z1)/3*2
            }
            x.beginPath()
            X = X1
            Y = Y1
            Z = Z1
            R(Rl,Pt,Yw,1)
            if(Z>0) x.moveTo(...Q())
            X = Xa
            Y = Ya
            Z = Za
            R(Rl,Pt,Yw,1)
            if(Z>0) l1 = Q()
            X = Xb
            Y = Yb
            Z = Zb
            R(Rl,Pt,Yw,1)
            if(Z>0) l2 = Q()
            X = X2
            Y = Y2
            Z = Z2
            R(Rl,Pt,Yw,1)
            if(Z>0) x.bezierCurveTo(...l1, ...l2, ...Q())
            stroke(col1, col2, lw,dual)
          }
          
          subbed = (subs, size, sphereize, shape) => {
            for(let m=subs; m--;){
              base = shape
              shape = []
              base.map(v=>{
                l = 0
                X1 = v[l][0]
                Y1 = v[l][1]
                Z1 = v[l][2]
                l = 1
                X2 = v[l][0]
                Y2 = v[l][1]
                Z2 = v[l][2]
                l = 2
                X3 = v[l][0]
                Y3 = v[l][1]
                Z3 = v[l][2]
                if(v.length > 3){
                  l = 3
                  X4 = v[l][0]
                  Y4 = v[l][1]
                  Z4 = v[l][2]
                  if(v.length > 4){
                    l = 4
                    X5 = v[l][0]
                    Y5 = v[l][1]
                    Z5 = v[l][2]
                  }
                }
                mx1 = (X1+X2)/2
                my1 = (Y1+Y2)/2
                mz1 = (Z1+Z2)/2
                mx2 = (X2+X3)/2
                my2 = (Y2+Y3)/2
                mz2 = (Z2+Z3)/2
                a = []
                switch(v.length){
                  case 3:
                    mx3 = (X3+X1)/2
                    my3 = (Y3+Y1)/2
                    mz3 = (Z3+Z1)/2
                    X = X1, Y = Y1, Z = Z1, a = [...a, [X,Y,Z]]
                    X = mx1, Y = my1, Z = mz1, a = [...a, [X,Y,Z]]
                    X = mx3, Y = my3, Z = mz3, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = mx1, Y = my1, Z = mz1, a = [...a, [X,Y,Z]]
                    X = X2, Y = Y2, Z = Z2, a = [...a, [X,Y,Z]]
                    X = mx2, Y = my2, Z = mz2, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = mx3, Y = my3, Z = mz3, a = [...a, [X,Y,Z]]
                    X = mx2, Y = my2, Z = mz2, a = [...a, [X,Y,Z]]
                    X = X3, Y = Y3, Z = Z3, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = mx1, Y = my1, Z = mz1, a = [...a, [X,Y,Z]]
                    X = mx2, Y = my2, Z = mz2, a = [...a, [X,Y,Z]]
                    X = mx3, Y = my3, Z = mz3, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    break
                  case 4:
                    mx3 = (X3+X4)/2
                    my3 = (Y3+Y4)/2
                    mz3 = (Z3+Z4)/2
                    mx4 = (X4+X1)/2
                    my4 = (Y4+Y1)/2
                    mz4 = (Z4+Z1)/2
                    cx = (X1+X2+X3+X4)/4
                    cy = (Y1+Y2+Y3+Y4)/4
                    cz = (Z1+Z2+Z3+Z4)/4
                    X = X1, Y = Y1, Z = Z1, a = [...a, [X,Y,Z]]
                    X = mx1, Y = my1, Z = mz1, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    X = mx4, Y = my4, Z = mz4, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = mx1, Y = my1, Z = mz1, a = [...a, [X,Y,Z]]
                    X = X2, Y = Y2, Z = Z2, a = [...a, [X,Y,Z]]
                    X = mx2, Y = my2, Z = mz2, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    X = mx2, Y = my2, Z = mz2, a = [...a, [X,Y,Z]]
                    X = X3, Y = Y3, Z = Z3, a = [...a, [X,Y,Z]]
                    X = mx3, Y = my3, Z = mz3, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = mx4, Y = my4, Z = mz4, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    X = mx3, Y = my3, Z = mz3, a = [...a, [X,Y,Z]]
                    X = X4, Y = Y4, Z = Z4, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    break
                  case 5:
                    cx = (X1+X2+X3+X4+X5)/5
                    cy = (Y1+Y2+Y3+Y4+Y5)/5
                    cz = (Z1+Z2+Z3+Z4+Z5)/5
                    mx3 = (X3+X4)/2
                    my3 = (Y3+Y4)/2
                    mz3 = (Z3+Z4)/2
                    mx4 = (X4+X5)/2
                    my4 = (Y4+Y5)/2
                    mz4 = (Z4+Z5)/2
                    mx5 = (X5+X1)/2
                    my5 = (Y5+Y1)/2
                    mz5 = (Z5+Z1)/2
                    X = X1, Y = Y1, Z = Z1, a = [...a, [X,Y,Z]]
                    X = X2, Y = Y2, Z = Z2, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = X2, Y = Y2, Z = Z2, a = [...a, [X,Y,Z]]
                    X = X3, Y = Y3, Z = Z3, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = X3, Y = Y3, Z = Z3, a = [...a, [X,Y,Z]]
                    X = X4, Y = Y4, Z = Z4, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = X4, Y = Y4, Z = Z4, a = [...a, [X,Y,Z]]
                    X = X5, Y = Y5, Z = Z5, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    X = X5, Y = Y5, Z = Z5, a = [...a, [X,Y,Z]]
                    X = X1, Y = Y1, Z = Z1, a = [...a, [X,Y,Z]]
                    X = cx, Y = cy, Z = cz, a = [...a, [X,Y,Z]]
                    shape = [...shape, a]
                    a = []
                    break
                }
              })
            }
            if(sphereize){
              ip1 = sphereize
              ip2 = 1-sphereize
              shape = shape.map(v=>{
                v = v.map(q=>{
                  X = q[0]
                  Y = q[1]
                  Z = q[2]
                  d = Math.hypot(X,Y,Z)
                  X /= d
                  Y /= d
                  Z /= d
                  X *= size*.75*ip1 + d*ip2
                  Y *= size*.75*ip1 + d*ip2
                  Z *= size*.75*ip1 + d*ip2
                  return [X,Y,Z]
                })
                return v
              })
            }
            return shape
          }

          subDividedIcosahedron  = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Icosahedron(size))
          subDividedTetrahedron  = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Tetrahedron(size))
          subDividedOctahedron   = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Octahedron(size))
          subDividedCube         = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Cube(size))
          subDividedDodecahedron = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Dodecahedron(size))

          LsystemRecurse = (size, splits, p1, p2, stem, theta, LsystemReduction, twistFactor) => {
            if(size < .25) return
            let X1 = stem[0]
            let Y1 = stem[1]
            let Z1 = stem[2]
            let X2 = stem[3]
            let Y2 = stem[4]
            let Z2 = stem[5]
            let p1a = Math.atan2(X2-X1,Z2-Z1)
            let p2a = -Math.acos((Y2-Y1)/(Math.hypot(X2-X1,Y2-Y1,Z2-Z1)+.0001))+Math.PI
            size/=LsystemReduction
            for(let i=splits;i--;){
              X = 0
              Y = -size
              Z = 0
              R(0, theta, 0)
              R(0, 0, Math.PI*2/splits*i+twistFactor)
              R(0, p2a, 0)
              R(0, 0, p1a+twistFactor)
              X+=X2
              Y+=Y2
              Z+=Z2
              let newStem = [X2, Y2, Z2, X, Y, Z]
              Lshp = [...Lshp, newStem]
              LsystemRecurse(size, splits, p1+Math.PI*2/splits*i+twistFactor, p2+theta, newStem, theta, LsystemReduction, twistFactor)
            }
          }
          DrawLsystem = shp => {
            shp.map(v=>{
              x.beginPath()
              X = v[0]
              Y = v[1]
              Z = v[2]
              R(Rl,Pt,Yw,1)
              if(Z>0)x.lineTo(...Q())
              X = v[3]
              Y = v[4]
              Z = v[5]
              R(Rl,Pt,Yw,1)
              if(Z>0)x.lineTo(...Q())
              lwo = Math.hypot(v[0]-v[3],v[1]-v[4],v[2]-v[5])*4
              stroke('#0f82','',lwo)
            })

          }
          Lsystem = (size, splits, theta, LsystemReduction, twistFactor) => {
            Lshp = []
            stem = [0,0,0,0,-size,0]
            Lshp = [...Lshp, stem]
            LsystemRecurse(size, splits, 0, 0, stem, theta, LsystemReduction, twistFactor)
            Lshp.map(v=>{
              v[1]+=size*1.5
              v[4]+=size*1.5
            })
            return Lshp
          }

          Sphere = (ls, rw, cl) => {
            a = []
            ls/=1.25
            for(j = rw; j--;){
              for(i = cl; i--;){
                b = []
                X = S(p = Math.PI*2/cl*i) * S(q = Math.PI/rw*j) * ls
                Y = C(q) * ls
                Z = C(p) * S(q) * ls
                b = [...b, [X,Y,Z]]
                X = S(p = Math.PI*2/cl*(i+1)) * S(q = Math.PI/rw*j) * ls
                Y = C(q) * ls
                Z = C(p) * S(q) * ls
                b = [...b, [X,Y,Z]]
                X = S(p = Math.PI*2/cl*(i+1)) * S(q = Math.PI/rw*(j+1)) * ls
                Y = C(q) * ls
                Z = C(p) * S(q) * ls
                b = [...b, [X,Y,Z]]
                X = S(p = Math.PI*2/cl*i) * S(q = Math.PI/rw*(j+1)) * ls
                Y = C(q) * ls
                Z = C(p) * S(q) * ls
                b = [...b, [X,Y,Z]]
                a = [...a, b]
              }
            }
            return a
          }

          Torus = (rw, cl, ls1, ls2, parts=1, twists=0, part_spacing=1.5) => {
            let ret = [], tx=0, ty=0, tz=0, prl1 = 0, p2a = 0
            let tx1 = 0, ty1 = 0, tz1 = 0, prl2 = 0, p2b = 0, tx2 = 0, ty2 = 0, tz2 = 0
            for(let m=parts;m--;){
              avgs = Array(rw).fill().map(v=>[0,0,0])
              for(j=rw;j--;)for(let i = cl;i--;){
                if(parts>1){
                  ls3 = ls1*part_spacing
                  X = S(p=Math.PI*2/parts*m) * ls3
                  Y = C(p) * ls3
                  Z = 0
                  R(prl1 = Math.PI*2/rw*(j-1)*twists,0,0)
                  tx1 = X
                  ty1 = Y 
                  tz1 = Z
                  R(0, 0, Math.PI*2/rw*(j-1))
                  ax1 = X
                  ay1 = Y
                  az1 = Z
                  X = S(p=Math.PI*2/parts*m) * ls3
                  Y = C(p) * ls3
                  Z = 0
                  R(prl2 = Math.PI*2/rw*(j)*twists,0,0)
                  tx2 = X
                  ty2 = Y
                  tz2 = Z
                  R(0, 0, Math.PI*2/rw*j)
                  ax2 = X
                  ay2 = Y
                  az2 = Z
                  p1a = Math.atan2(ax2-ax1,az2-az1)
                  p2a = Math.PI/2+Math.acos((ay2-ay1)/(Math.hypot(ax2-ax1,ay2-ay1,az2-az1)+.001))

                  X = S(p=Math.PI*2/parts*m) * ls3
                  Y = C(p) * ls3
                  Z = 0
                  R(Math.PI*2/rw*(j)*twists,0,0)
                  tx1b = X
                  ty1b = Y
                  tz1b = Z
                  R(0, 0, Math.PI*2/rw*j)
                  ax1b = X
                  ay1b = Y
                  az1b = Z
                  X = S(p=Math.PI*2/parts*m) * ls3
                  Y = C(p) * ls3
                  Z = 0
                  R(Math.PI*2/rw*(j+1)*twists,0,0)
                  tx2b = X
                  ty2b = Y
                  tz2b = Z
                  R(0, 0, Math.PI*2/rw*(j+1))
                  ax2b = X
                  ay2b = Y
                  az2b = Z
                  p1b = Math.atan2(ax2b-ax1b,az2b-az1b)
                  p2b = Math.PI/2+Math.acos((ay2b-ay1b)/(Math.hypot(ax2b-ax1b,ay2b-ay1b,az2b-az1b)+.001))
                }
                a = []
                X = S(p=Math.PI*2/cl*i) * ls1
                Y = C(p) * ls1
                Z = 0
                //R(0,0,-p1a)
                R(prl1,p2a,0)
                X += ls2 + tx1, Y += ty1, Z += tz1
                R(0, 0, Math.PI*2/rw*j)
                a = [...a, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*(i+1)) * ls1
                Y = C(p) * ls1
                Z = 0
                //R(0,0,-p1a)
                R(prl1,p2a,0)
                X += ls2 + tx1, Y += ty1, Z += tz1
                R(0, 0, Math.PI*2/rw*j)
                a = [...a, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*(i+1)) * ls1
                Y = C(p) * ls1
                Z = 0
                //R(0,0,-p1b)
                R(prl2,p2b,0)
                X += ls2 + tx2, Y += ty2, Z += tz2
                R(0, 0, Math.PI*2/rw*(j+1))
                a = [...a, [X,Y,Z]]
                X = S(p=Math.PI*2/cl*i) * ls1
                Y = C(p) * ls1
                Z = 0
                //R(0,0,-p1b)
                R(prl2,p2b,0)
                X += ls2 + tx2, Y += ty2, Z += tz2
                R(0, 0, Math.PI*2/rw*(j+1))
                a = [...a, [X,Y,Z]]
                ret = [...ret, a]
              }
            }
            return ret
          }

          spawnPlayer = uid => {
            return {
              X           : uid==2?0:(uid==1?-100:(uid?100:0)),
              Y           : -10000,
              Z           : uid?150:0,
              vx          : 0,
              statusMSG   : 'no alarms',
              defaultMSG  : 'no alarms',
              name        : users.filter(user => +user.id == +uid)[0].name,
              score       : 0,
              lerpToX     : 0,
              lerpToY     : 0,
              lerpToZ     : 0,
              lerpToRl    : 0,
              lerpToPt    : 0,
              lerpToYw    : 0,
              lerpToRlv   : 0,
              lerpToPtv   : 0,
              lerpToYwv   : 0,
              lerpToVx    : 0,
              lerpToVy    : 0,
              lerpToVz    : 0,
              buttons     : [],
              vy          : 0,
              vz          : 0,
              rl          : 0,
              pt          : 0,
              yw          : 0,
              rlv         : 0,
              ptv         : 0,
              id          : uid,
              ranges      : [],
              ywv         : 0,
              rlv_        : 0,
              ptv_        : 0,
              ywv_        : 0,
              altitude    : 300,
              shooting    : 0,
              speed       : uid ? 50 : 20,
              cameraMode  : 1,
              health      : 1,
              alarm       : 0,
              fuel        : 1,
              afterburner : 0,
              machTimer   : 1,
              showTrails  : false,
              trails      : [],
              alive       : true,
              mbutton     : Array(3).fill(false),
              keys        : Array(128).fill(false),
              keyTimers   : Array(128).fill(0)
            }
          }
          
          masterInit = () => {
            players               = []
            missiles              = []
            smoke                 = []
            sounds                = {
              splode: new Audio(),
              splode2: new Audio(),
              missile: new Audio(),
              jet: new Audio(),
              alarm: new Audio(),
              metal1: new Audio(),
              metal2: new Audio(),
              metal3: new Audio(),
              metal4: new Audio(),
              metal5: new Audio(),
            }
            sounds.splode.src     = '/games_shared_assets/splode.mp3'
            sounds.splode2.src    = '/games_shared_assets/splode2.mp3'
            sounds.missile.src    = '/games_shared_assets/missile.wav'
            sounds.jet.src        = '/games_shared_assets/jet.mp3'
            sounds.alarm.src      = '/games_shared_assets/alarm.mp3'
            sounds.metal1.src     = '/games_shared_assets/metal1.ogg'
            sounds.metal2.src     = '/games_shared_assets/metal2.ogg'
            sounds.metal3.src     = '/games_shared_assets/metal3.ogg'
            sounds.metal4.src     = '/games_shared_assets/metal4.ogg'
            sounds.metal5.src     = '/games_shared_assets/metal5.ogg'
            sounds.splode.volume  = 1
            sounds.splode2.volume = 1
            sounds.missile.volume = .1
            sounds.jet.volume     = .1
            sounds.alarm.volume   = .05
            sounds.metal1.volume  = .1
            sounds.metal2.volume  = .1
            sounds.metal3.volume  = .1
            sounds.metal4.volume  = .1
            sounds.metal5.volume  = .1
            keyTimerInterval      = .1
            lerpFactor            = 20
            playSounds            = false
            pointerLocked         = false
            missileTimerInterval  = .1
            minSpeed              = 20
            maxSpeed              = 200
            falloffDist           = 250
            mx = my               = 0
            missileSpeed          = maxSpeed*.66
            crosshairSel          = 0
            showRangeFinder       = true
            buttons               = []
            missileDamage         = .5
            flashNotices          = []
            showHUD               = true
            buttonsLoaded         = false
            showInfo              = true
            time                  = 'night'
            hotkeysModalVisible   = false
            hotkeyX               = 0
            cameraModes           = 2
            homing                = .1
            rotateSkySphere       = true
            hov                   = false
            mv                    = .1
            fuelDepletionRate     = .00025
            soundSpeed            = 761 // mph
            rv                    = .05
            accel                 = 2
            showCrosshair         = true
            camSelected           = 0
            mbutton               = Array(3).fill(false),
            sounds.jet.loop       = true
            scores                = []
            PlayerCount           = 0
            sendDeathSplosions    = []
            sendMissileSplosions  = []
            Players               = []
          }
          masterInit()

          PlayerInit = (idx, id) => { // called initially & when a player dies
            let newPlayer = spawnPlayer(id)
            Players[idx].player = newPlayer
            Players[idx].scores = []
            if(!players.filter(v=>+v.id==+id).length){
              players=[...players, newPlayer]
            }else{
              players.filter(v=>+v.id==+id)[0] = Players[idx].player
            }
            
            if(+players[0].id != +userID){
              tplayers = []
              JSON.parse(JSON.stringify(players)).map((player, idx) => {
                if(+player.id == +userID){
                  nplayer = player
                }else{
                  tplayers = [...tplayers, player]
                }
              })
              players = [nplayer, ...tplayers]
            }
          }
          
          
          addPlayers = playerData => {
            playerData.score = 0
            Players = [...Players, {playerData}]
            PlayerCount++
            PlayerInit(Players.length-1, playerData.id)
          }
          
          document.body.onload = () =>{
            c.focus()
          }

          crosshairsLoaded = false, crosshairImgs = [{loaded: false}]
          crosshairImgs = Array(3).fill().map((v,i) => {
            let a = {img: new Image(), loaded: false}
            a.img.onload = () => {
              a.loaded = true
              setTimeout(()=>{
                if(crosshairImgs.filter(v=>v.loaded).length == 3) crosshairsLoaded = true
              }, 0)
            }
            a.img.src = `/games_shared_assets/crosshair${i+1}.png`
            return a
          })
        
          c.onmousemove = e => {
            if(players.length){
              curPlayer = players[camSelected]
              hov = false
              rect = c.getBoundingClientRect()
              mx = (e.pageX-rect.x)/c.clientWidth*c.width
              my = (e.pageY-rect.y)/c.clientHeight*c.height
              buttons.map(button=>{
                if(button.hover){
                  hov = true
                }
              })
            }
            //if(showInfo){
              let ofx = hotkeysModalVisible ? 450 : 0
              X1 = ofx-450
              Y1 = 5 + 670 - (1+players.length)*(32*1.75)
              X2 = X1 + 500
              Y2 = Y1 + 27*14.5
              if(mx >= X1 && mx <= X2 && my >= Y1 && my <= Y2){
                c.style.cursor = 'pointer'
              }else{
                c.style.cursor = 'unset'
              }
            //}
          }
          
          c.onmouseup = e => {
            e.preventDefault()
            e.stopPropagation()
            if(e.button == 0) mousedown = false
            if(players.length){
              //curPlayer = players[camSelected]
              //c.focus()
              curPlayer.mbutton[e.button] = false
            }
          }
         
          c.onmousedown = e => {
            e.preventDefault()
            e.stopPropagation()
            if(e.button == 0) mousedown = true
            if(players.length){
              curPlayer = players[camSelected]
              //c.requestFullscreen()
              curPlayer.mbutton[e.button] = true

              let hov = false
              if(!pointerLocked){
                if(e.button == 0){
                  buttons.map(button=>{
                    if(button.hover){
                      hov = true
                      if(button.visible) eval(button.callback)
                    }
                  })
                }
              }
              if(!hov) {
                //c.requestPointerLock()
              }
            }
            if(e.button == 0){
              let ofx = hotkeysModalVisible ? 450 : 0
              X1 = ofx-450
              Y1 = 5 + 670 - (1+players.length)*(32*1.75)
              X2 = X1 + 500
              Y2 = Y1 + 27*14.5
              if(mx >= X1 && mx <= X2 && my >= Y1 && my <= Y2){
                hotkeysModalVisible = !hotkeysModalVisible
              }
            }
          }      
        
          window.onkeydown = () => {
            c.focus()
          }
          
          c.onkeydown = e => {
            e.preventDefault()
            e.stopPropagation()
            if(typeof jetSoundPlaying == 'undefined'){
              jetSoundPlaying = true
              if(playSounds) sounds.jet.play()
            }
            if(players.length) players[0].keys[e.keyCode] = true
          }

          c.onkeyup = e => {
            e.preventDefault()
            e.stopPropagation()
            if(players.length) players[0].keys[e.keyCode] = false
          }

          playSound = sound => {
            if(!playSounds) return
            let snd = sound.cloneNode()
            sndName = (l=snd.src.split('/'))[l.length-1].split('.')
            switch(sndName){
              case 'splode'  : snd.volume = 1;break
              case 'splode2' : snd.volume = 1;break
              case 'missile' : snd.volume = .05;break
              case 'jet'     : snd.volume = .1;break
              case 'alarm'   : snd.volume = .1;break
              case 'metal1'  : snd.volume = .1;break
              case 'metal2'  : snd.volume = .1;break
              case 'metal3'  : snd.volume = .1;break
              case 'metal4'  : snd.volume = .1;break
              case 'metal5'  : snd.volume = .1;break
            }
            snd.volume = .1
            snd.play()
          }

          fireMissile = (player, idx) => {
            if(idx == camSelected) playSound(sounds.missile)
            rl = player.rl
            pt = player.pt + player.ptv * 6
            yw = player.yw + player.ywv * 2
            X = 0
            Y = 0
            Z = vs = (missileSpeed/2 + player.speed*2) / 2.5
            R2(rl,pt,yw)
            vx = X
            vy = Y
            vz = Z
            X = player.X + vx
            Y = player.Y + 4 + vy
            Z = player.Z + vz

            vx = vy = vz = 0
            missiles = [...missiles, [X,Y,Z,vx,vy,vz,rl,pt,yw,1,player.id,vs]]
          }

          doKeys = (player, idx_) => {
            let l
            player.keys.map((val, idx) => {
              if(val){
                switch(idx){
                  case 49: if(players.length>0)
                    camSelected = 0;
                    camSelHasChanged=true
                  break
                  case 50: if(players.length>1)
                    camSelected = 1;
                    camSelHasChanged=true
                  break
                  case 51: if(players.length>2)
                    camSelected = 2;
                    camSelHasChanged=true
                  break
                  case 52: if(players.length>3)
                    camSelected = 3;
                    camSelHasChanged=true
                  break
                  case 53: if(players.length>4)
                    camSelected = 4;
                    camSelHasChanged=true
                  break
                  case 54: if(players.length>5)
                    camSelected = 5;
                    camSelHasChanged=true
                  break
                  case 55: if(players.length>6)
                    camSelected = 6;
                    camSelHasChanged=true
                  break
                  case 56: if(players.length>7)
                    camSelected = 7;
                    camSelHasChanged=true
                  break
                  case 57: if(players.length>8)
                    camSelected = 8;
                    camSelHasChanged=true
                  break
                  case 48: if(players.length>9)
                    camSelected = 9;
                    camSelHasChanged=true
                  break
                  case 72:
                    if(player.keyTimers[idx] < t){
                      player.keyTimers[idx] = t+keyTimerInterval /2
                      hotkeysModalVisible = !hotkeysModalVisible
                    }
                  break
                  case 73:
                    if(player.keyTimers[idx] < t){
                      player.keyTimers[idx] = t+keyTimerInterval/2
                      toggleMenu(false)
                    }
                  break
                  case 17: // shoot
                    if(player.keyTimers[idx]<t){
                      player.keyTimers[idx] = t+missileTimerInterval 
                      fireMissile(player, idx_)
                    }
                  break
                  case 37: //turn left
                    if(player.keyTimers[idx]<t){
                      //player.keyTimers[idx] = t+keyTimerInterval
                      //player.rlv -= rv / ((l=player.rl)<-.5?1+(l-.5)*1e3:1)
                      player.ywv_ += (-rv/4 - player.ywv_)/4
                    }
                  break
                  case 38: //nose down
                    if(player.keyTimers[idx]<t){
                      //player.keyTimers[idx] = t+keyTimerInterval
                      player.ptv_ += (rv / (3+(player.pt-0)*4) - player.ptv_)/16
                    }
                  break
                  case 39: //turn right
                    if(player.keyTimers[idx]<t){
                      //player.keyTimers[idx] = t+keyTimerInterval
                      //player.rlv += rv / ((l=player.rl)>.5?1+(l-.5)*1e3:1)
                     player.ywv_ += (rv /4 - player.ywv_)/4
                    }
                  break
                  case 40: // nose up
                    if(player.keyTimers[idx]<t){
                      //player.keyTimers[idx] = t+keyTimerInterval
                      player.ptv_ += (-rv / (3+(-player.pt-0)*4) - player.ptv_)/16
                    }
                  break
                  case 20:  //accel
                    //if(player.keyTimers[idx]<t){
                      player.speed += accel
                      player.speed = Math.min(maxSpeed, player.speed)
                      player.afterburner = player.speed/maxSpeed
                  break
                  case 16:  //decel
                    //if(player.keyTimers[idx]<t){
                      //player.keyTimers[idx] = t+keyTimerInterval
                      player.speed -= accel * 4
                      player.speed = Math.max(minSpeed, player.speed)
                      player.afterburner = player.speed/maxSpeed
                    //}
                  break
                  case 77:
                    if(player.keyTimers[idx]<t){
                      player.keyTimers[idx] = t+keyTimerInterval
                      player.cameraMode++
                      player.cameraMode %= cameraModes
                    }
                  break
                  case 84:
                    if(player.keyTimers[idx]<t){
                      player.keyTimers[idx] = t+keyTimerInterval
                      player.showTrails = !player.showTrails
                    }
                  break
                  case  67:
                    if(player.keyTimers[idx] < t){
                      player.keyTimers[idx] = t+keyTimerInterval
                      if(showCrosshair && crosshairSel<crosshairImgs.length-1){
                        crosshairSel++
                      }else{
                        crosshairSel=0
                        showCrosshair = !showCrosshair
                      }
                    }
                    break
                }
              }
            })
          }

          G_ = 1e5, iSTc = 1e3
          ST = Array(iSTc).fill().map(v=>{
            X = (Rn()-.5)*G_
            Y = (-Rn()/2)*G_ + G_/10
            Z = (Rn()-.5)*G_
            return [X,Y,Z]
          })


          cloud = new Image()
          cloud.src = '/games_shared_assets/cloud.png'

          starsLoaded = false, starImgs = [{loaded: false}]
          starImgs = Array(9).fill().map((v,i) => {
            let a = {img: new Image(), loaded: false}
            a.img.onload = () => {
              a.loaded = true
              setTimeout(()=>{
                if(starImgs.filter(v=>v.loaded).length == 9) starsLoaded = true
              }, 0)
            }
            a.img.src = `/games_shared_assets/star${i+1}.png`
            return a
          })

          showstars = true

          showclouds  = true
          landG       = 4000
          
          //skySphere = subDividedDodecahedron(1e4, 4, 1)
          skySphere = subDividedDodecahedron(1e4, 4, 1)
          
          jet = await loadOBJ("/games_shared_assets/jet_lowpoly.obj", .2, 0, 1, 0, 0, 0, 0, recenter=true)
          missile_model = await loadOBJ('/games_shared_assets/missile_lowpoly.obj', 5, 0,0,0,0,0,0)

          drawRotatedImage = (img,tx,ty,w,h,theta)=>{
            context.save()
            context.translate(tx,ty)
            context.rotate(theta)
            context.drawImage(img,-w/2,-h/2,w,h)
            context.restore()
          }

          sparks = []
          iSparkv = 1
          spawnSplosion = player =>{
            if(player.cameraMode == 1){
              X = 0
              Y = 0
              Z = -10 - player.speed/10
              R2(player.rl,player.pt*6,player.yw)
              tx = X
              ty = Y
              tz = Z
            }else{
              tx = ty = tz = 0
            }
            X = player.X - tx
            Y = player.Y - ty
            Z = player.Z - tz
            for(m=1e3;m--;){
              let v = iSparkv/4 + iSparkv*.75 * Rn()**2
              vx = S(p=Math.PI*2*Rn()) * S(q=Rn()<.5 ? Math.PI/2 * Rn()**.5 : Math.PI-Math.PI/2 * Rn()**.5) * v
              vy = C(q) * v
              vz = C(p) * S(q) * v
              sparks = [...sparks, [X,Y,Z,vx,vy,vz,1,player.id]] 
              smoke = [...smoke, [vx*50,vy*50,vz*50,1]] 
            }
          }

          spawnMissileSplosion = missile =>{
            let player = players[0]
            if(player.cameraMode == 1){
              X = 0
              Y = 0
              Z = -10 - player.speed/10
              R2(player.rl,player.pt*6,player.yw)
              tx = X
              ty = Y
              tz = Z
            }else{
              tx = ty = tz = 0
            }
            X = missile[0] - tx
            Y = missile[1] - ty
            Z = missile[2] - tz
            for(m=50;m--;){
              let v = (iSparkv/2 + iSparkv*.5 * Rn()**2)*10
              vx = S(p=Math.PI*2*Rn()) * S(q=Rn()<.5 ? Math.PI/2 * Rn()**.5 : Math.PI-Math.PI/2 * Rn()**.5) * v
              vy = C(q) * v
              vz = C(p) * S(q) * v
              sparks = [...sparks, [X,Y,Z,vx,vy,vz,1,-1]] 
            }
          }
          
          killPlayer = (player, deathReason, idx) => {
            sendDeathSplosions = [...sendDeathSplosions, 
                  [player.X, player.Y, player.Z]
                ]
            player.speed = 0
            player.alive = false
            player.ranges = []
            spawnSplosion(player)
            if(deathReason && idx == camSelected){
              playSound(sounds.splode)
              playSound(sounds.splode2)
              sounds.alarm.paused = true
              setTimeout(() => {
                spawnFlashNotice(deathReason, '#800')
                spawnFlashNotice('YOU DIED!', '#800')
                spawnFlashNotice('.....respawning.....', '#0f4')
              }, 2000)
            }else{
              playSound(sounds.splode)
            }
          }
          
          spawnFlashNotice = (text, col)=>{
            flashNotices = [...flashNotices, [text, col, 1]]
          }

          respawnPlayer = (player, idx) => {
            player.X = idx==1?-20:(player.id?20:0),
            player.Y = -10000
            player.Z = idx?50:0,
            player.vx = 0
            player.vy = 0
            player.vz = 0
            player.statusMSG = player.defaulMSG
            lerpToX   = 0
            lerpToY   = 0
            lerpToZ   = 0
            lerpToRl  = 0
            lerpToPt  = 0
            lerpToYw  = 0
            lerpToRlv = 0
            lerpToPtv = 0
            lerpToYwv = 0
            lerpToVx  = 0
            lerpToVy  = 0
            lerpToVz  = 0
            player.rl = 0
            player.pt = 0
            player.yw = 0
            player.rlv = 0
            player.ptv = 0
            player.ywv = 0
            player.rlv_ = 0
            player.ptv_ = 0
            player.ywv_ = 0
            player.speed = 20
            player.health = 1
            player.altitude = 300
            player.shooting = false
            player.ranges = []
            player.alarm = 0
            player.fuel = 1
            player.afterburner = 0
            player.trails = []
            player.alive = true
          }
          
          drawThruster = (ox, oy, oz, tx_, ty_, tz_, rl, pt1, pt2, yw, length=1, size=1) => {
            let shps = [], tx, ty, tz
            let iShpc = 6, ct, col1, col2
            for(let i=iShpc;i--;){
              let ls = (.5+i/4)**2
              X = 0
              Y = ls*4 * length*size
              Z = 0
              R(0,Math.PI/2,0)
              R3(rl, pt1, pt2, yw)
              tx = X
              ty = Y
              tz = Z
              ct = 0
              shps = [...shps, [tx,ty,tz,Cylinder(4,6,3*ls,hgt=8*ls).map(v=>{
                v.map(q=>{
                  X = q[0]
                  Y = q[1]*length
                  Z = q[2]
                  p = Math.atan2(X,Z)
                  d = Math.hypot(X,Z)
                  rad = (1+C(Math.PI/1.25/hgt*Y+Math.PI/3)) * (1+d/4)/2
                  X = S(p) * rad
                  Z = C(p) * rad
                  R(0,Math.PI/2,0)
                  X *= size
                  Y *= size
                  Z *= size
                  X += ox
                  Y += oy
                  Z += oz
                  R3(rl, pt1, pt2, yw)
                  q[0] = X + tx_
                  q[1] = Y + ty_
                  q[2] = Z + tz_
                })
                ct++
                return v
              })]]
            }
            shps.map((shp, idx) => {
              tx = shp[0]
              ty = shp[1]
              tz = shp[2]
              shp[3].map(v=>{
                context.beginPath()
                v.map(q=>{
                  X = q[0] + tx
                  Y = q[1] + ty
                  Z = q[2] + tz
                  R(Rl,Pt,Yw,1)
                  if(Z>0) context.lineTo(...Q())
                })
                col1 = ''
                col2 = `hsla(${Math.max(0,-20+360/shps.length*idx/2.5)},99%,${Math.max(50,25+(100/shps.length*idx)**2/80)}%,${.5/shps.length*(.5+idx)})`
                stroke(col1,col2,.5, true)
              })
            })
          }
          
          doLerp = (player, idx) => {
            player.X += (player.lerpToX - player.X) / lerpFactor
            player.Y += (player.lerpToY - player.Y) / lerpFactor
            player.Z += (player.lerpToZ - player.Z) / lerpFactor
            player.rl += (player.lerpToRl - player.rl) / lerpFactor
            player.rlv += (player.lerpToRlv - player.rlv) / lerpFactor
            player.yw += (player.lerpToYw - player.yw) / lerpFactor
            player.ywv += (player.lerpToYwv - player.ywv) / lerpFactor
            player.pt += (player.lerpToPt - player.pt) / lerpFactor
            player.ptv += (player.lerpToPtv - player.ptv) / lerpFactor
            player.vx += (player.lerpToVx - player.vx) / lerpFactor
            player.vy += (player.lerpToVx - player.vy) / lerpFactor
            player.vz += (player.lerpToVz - player.vz) / lerpFactor
          }

          doAI = player => {
            /*
            case 16:  //decel
            case 9:  //accel
            case 40: // nose up
            case 39: //turn right
            case 38: //nose down
            case 37: //turn left
            case 17: // shoot
            */
            
            player.keys
            //if(Rn()<.02) player.keys[40] = false
            //if(Rn()<.02) player.keys[38] = false
            //if(Rn()<.02) player.keys[37] = false
            //if(Rn()<.02) player.keys[39] = false
            if(Rn()<.2) player.keys[16] = false
            if(Rn()<.1) player.keys[20] = false
            //if(Rn()<.25) player.keys[40] = true
            //if(Rn()<.25) player.keys[38] = true
            //if(Rn()<.25) player.keys[37] = true
            //if(Rn()<.25) player.keys[39] = true
            if(Rn()<.075) player.keys[16] = true
            if(Rn()<.1 && player.speed < maxSpeed/5) player.keys[20] = true
            
            mind = 6e6
            tgtIdx = -1
            X1 = player.X
            Y1 = player.Y
            Z1 = player.Z
            players.map((player2,idx2) => {
              if(player2.id !=player.id){
                X2 = player2.X
                Y2 = player2.Y
                Z2 = player2.Z
                if((d = Math.hypot(X2-X1, Y2-Y1, Z2-Z1)) < mind){
                  mind = d
                  tgtIdx = idx2
                }
              }
            })
            if(tgtIdx != -1){
              rl = player.rl
              pt = player.pt
              yw = -player.yw

              tgtPlayer = players[tgtIdx]
              X2 = tgtPlayer.X
              Y2 = tgtPlayer.Y
              Z2 = tgtPlayer.Z

              p1 = -Math.atan2(X2-X1, Z2-Z1)
              
              //p1 = Math.atan2(X2-X1, Z2-Z1)
              p2 = -Math.acos((Y2-Y1) / (Math.hypot(X2-X1, Y2-Y1, Z2-Z1)+.001)) + Math.PI/2
              while(Math.abs(p1-yw)>Math.PI){
                if(p1<yw){
                  p1 += Math.PI*2
                }else{
                  p1 -= Math.PI*2
                }
              }
              
              if(p1-yw > 0 ){
                player.keys[37] = true
                player.keys[39] = false
              }else{
                player.keys[39] = true
                player.keys[37] = false
              }
              
              if(p2-pt > 0){
                player.keys[38] = true
                player.keys[40] = false
              }else{
                player.keys[40] = true
                player.keys[38] = false
              }
            }
          }
          
          HUD = new Image()
          HUD.src = '/games_shared_assets/HUD.png'
          
          troubleHUD = new Image()
          troubleHUD.src = '/games_shared_assets/trouble_HUD.png'
          
          greenLED = new Image()
          greenLED.src = '/games_shared_assets/green_dot.png'

          redLED = new Image()
          redLED.src = '/games_shared_assets/red_dot.png'
          
          clearMask = new Image()
          clearMask.src = '/games_shared_assets/clearMask.png'
          
          applyClearMask = () => {
            bctx.save()
            bctx.translate(buffer.width,0)
            bctx.scale(-1, 1)
            bctx.drawImage(buffer,0,-buffer.height/3.5,buffer.width,buffer.height)
            bctx.restore()
            bctx.globalCompositeOperation = 'destination-out'
            bctx.drawImage(clearMask, 0,0,buffer.width,buffer.height)
            bctx.globalCompositeOperation = 'source-over'
          }
          
          renderButton = (text, X, Y, tooltip = '', callback='', typ='rectangle', col1='#0ff8', col2='#2088', fs=36) => {
            render = (text == '🔗' && showInfo) ||
                     (text == 'EXIT LEVEL' && showInfo) ||
                     (showInfo && tooltip == '  hide menu') || (tooltip == '  show menu' && !showInfo) ||
                     (playSounds && tooltip == '  mute audio') || (tooltip == '  unmute audio' && !playSounds)
            x.globalAlpha = 1
            if(render) {
              x.beginPath()
              x.fillStyle = '#4f8c'
            }
            let X1, Y1, X2, Y2
            x.font = fs + 'px Courier Prime'
            let margin = 2
            let w = x.measureText(text).width + margin*2
            let h = fs + margin*2
            X1=X-w/2,Y1=Y-h/2
            if(render || !buttonsLoaded){
              if(render){
                //c.style.cursor = 'unset'
                switch(typ){
                  case 'rectangle':
                    x.lineTo(X1,Y1)
                    x.lineTo(X+w/2,Y-h/2)
                    x.lineTo(X+w/2,Y+h/2)
                    x.lineTo(X-w/2,Y+h/2)
                  break
                  case 'circle':
                  break
                }
                Z = 30
                stroke(col1, col2, 5, true)
              }
            }
            
            X2=X1+w
            Y2=Y1+h
            if(mx>X1 && mx<X2 && my>Y1 && my<Y2){
              if(buttonsLoaded){
                buttons[bct].hover = true
              }else{
                buttons=[...buttons, {callback,X1,Y1,X2,Y2,hover:true,tooltip,visible: false}]
              }
              c.style.cursor = 'pointer'
            }else{
              if(buttonsLoaded){
                buttons[bct].hover = false
              }else{
                buttons=[...buttons, {callback,X1,Y1,X2,Y2,hover:false,tooltip,visible: false}]
              }
            }
            if(render){
              ota = x.textAlign
              x.textAlign = 'center'
              x.fillStyle = '#fff'
              x.fillText(text, X, Y+fs/3.2)
              x.textAlign = ota
            }
            if(render){
              buttons[bct].visible = true
            }else{
              buttons[bct].visible = false
            }
            bct++
          }

          toggleAudio = () =>{
            playSounds = !playSounds
            if(!playSounds){
              sounds.jet.volume = 0
            }else{
              sounds.jet.volume = .1
              sounds.jet.play()
            }
          }
          
          toggleMenu = (releasePointerLock=true) =>{
            showInfo = !showInfo
            if(showInfo) {
              if(releasePointerLock && document.pointerLockElement == c) document.exitPointerLock()
            } else {
              //if(document.pointerLockElement != c) c.requestPointerLock()
            }
          }
        }
        
        curPlayer = players[camSelected]
        
        let vws = !players.length ? 0 : (curPlayer.alive && curPlayer.cameraMode ? 2 : 1)
        for(k__=0; k__<vws; k__++){
          if(k__) bctx.clearRect(0, 0, buffer.width, buffer.height)
          switch(curPlayer.cameraMode){
            case 0:
              X = 0
              Y = 0
              Z = -12 - curPlayer.speed/5
              R2(curPlayer.rl,curPlayer.pt*6,curPlayer.yw)
              oX = curPlayer.X + X
              oY = curPlayer.Y + Y
              oZ = curPlayer.Z + Z
              Rl = curPlayer.ywv*8 // * (k__?-1:1)
              Pt = -curPlayer.pt*6 * (k__?-1:1)
              Yw = (k__?Math.PI:0)-curPlayer.yw
            break
            case 1:
              X = oX = curPlayer.X
              Y = oY = curPlayer.Y
              Z = oZ = curPlayer.Z
              //R2(0,-curPlayer.pt*4,curPlayer.yw+curPlayer.ywv)
              //oX = X + curPlayer.X
              //oY = -Y*.5 + curPlayer.Y + curPlayer.vy/2 * (1+curPlayer.speed/80)
              //oZ = Z + curPlayer.Z
              Rl = -curPlayer.rl*1.25 * (k__?-1:1)
              Pt = -(curPlayer.pt/6+curPlayer.ptv)*6 * (k__?-1:1)
              Yw = (k__?Math.PI:0)-curPlayer.yw
            break
          }

          context = k__ ? bctx : x
          context.globalAlpha = 1
          context.fillStyle='#0004'
          context.fillRect(0,0,c.width,c.height)
          context.lineJoin = context.lineCap = 'roud'
          
          margin = .01
          // interpolate skySphere colors between 'night' & 'day'
          ip1 = Math.max(0,Math.min(2, (.3+(C(t/8-(time=='night'?Math.PI:0))))*20))
          ip2 = 1-ip1
          skySphere.map(v => {
            ax=ay=az=0
            v.map(q=>{
              X=q[0]
              Y=q[1]
              Z=q[2]
              if(!k__ && rotateSkySphere) R(0,-.005,0)
              ax+=q[0] = X
              ay+=q[1] = Y
              az+=q[2] = Z
            })
            X = ax /= v.length
            Y = ay /= v.length
            Z = az /= v.length
            R(Rl,Pt,Yw)
            if(Z>1e4/3.5){//k__ ? 1e4/8 : 1e4/2.8){
              context.beginPath()
              v.map(q => {
                X = q[0] + (q[0]-ax) * margin
                Y = q[1] + (q[1]-ay) * margin
                Z = q[2] + (q[2]-az) * margin
                R(Rl,Pt,Yw)
                if(Z>0) context.lineTo(...Q())
              })
              col1 = ''
              
              if(ay<1e4/9){ // top
                RGBa = RGBFromHSV(-160-ay/1e4*50, 1, (60+ay/1e4*40)/100) //day
                RGBb = RGBFromHSV(-80+ay/1e4*140, 1, (20+ay/1e4/2*60)/100)  //night
              }else{        // bottom
                RGBa = RGBFromHSV(ay/1e4*140, 1, (10+ay/1e4*50)/100)//day
                RGBb = RGBFromHSV(180-ay/1e4*140, 1, (10+ay/1e4*20)/100)//night
              }
              
              
              red   = (RGBa[0] * ip1 + RGBb[0] * ip2) |0
              green = (RGBa[1] * ip1 + RGBb[1] * ip2) |0
              blue  = (RGBa[2] * ip1 + RGBb[2] * ip2) |0
              
              col2 = `rgba(${red},${green},${blue},1)`
              stroke(col1, col2)
            }
          })

          if(showstars||showclouds) {
            ct = 0
            ST.map((v, i) => {
              tx_ = X = v[0]
              ty_ = Y = v[1]
              tz_ = Z = v[2]
              do{
                if(oX-X > landG){
                  X += landG*2
                }
                if(oX-X < -landG){
                  tx = X -= landG*2
                }
              }while(Math.abs(oX-X)>landG);
              v[0] = X
              do{
                if(oY-Y > landG){
                  Y += landG*2
                }
                if(oY-Y < -landG){
                  ty = Y -= landG*2
                }
              }while(Math.abs(oY-Y)>landG);
              v[1] = Y
              do{
                if(oZ-Z > landG){
                  Z += landG*2
                }
                if(oZ-Z < -landG){
                  tz = Z -= landG*2
                }
              }while(Math.abs(oZ-Z)>landG);
              v[2] = Z
              R(Rl,Pt,Yw,1)
              if(Z>0){
                d_ = Math.hypot(X,Y,Z)
                if((context.globalAlpha = Math.min(1,(d_/4000)**2*10 / (1+d_**8/1e27)))>.05){
                  if(!(i%4)){
                    if(showclouds && !(i%600)){
                      context.globalAlpha /= 1.5
                      for(let n = 3; n--;){
                        X = (((1e3+((i+n)%1e3))**3.1%1)-.5) * 1400 + tx_
                        Y = (((2e3+((i+n)%1e3))**3.1%1)-.5) * 1400 + ty_
                        Z = (((3e3+((i+n)%1e3))**3.1%1)-.5) * 1400 + tz_
                        R(Rl,Pt,Yw,1)
                        if(Z>0){
                          l = Q()
                          s = Math.min(2500, 5e5/Z**1.25) * 8
                          drawRotatedImage(cloud,l[0],l[1],s*2,s*1,-Rl)
                        }
                      }
                    }else{
                      l = Q()
                      s = Math.min(1e4, 1e5/Z)
                      if(showstars && starsLoaded) {
                        starIdx = 1+(ct%8)
                        context.drawImage(starImgs[starIdx].img,l[0]-s/1.06,l[1]-s/1.06,s*2,s*2)
                      }
                    }
                  }else if(showstars){
                    l = Q()
                    s = Math.min(1e4, 5e4/Z)
                    context.fillStyle = '#ffffff06'
                    context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=6
                    context.fillStyle = '#fffc'
                    context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  }
                }
              }
              ct+=!(i%4) && (i%15) ? 1:0
            })
          }
          
          context.globalAlpha = 1

          if(!k__){
            smoke = smoke.filter(v=>v[3]>0)
            smoke.map(v => {
              X = v[0]
              Y = v[1]
              Z = v[2]
              R(Rl,Pt,Yw,1)
              if(Z>0){
                l = Q()
                s = Math.min(600,1e4/Z*v[3])
                //context.fillStyle = '#88888806'
                //context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=1.5
                context.fillStyle = '#88888812'
                context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=6
                context.fillStyle = '#00000020'
                context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
              v[3] -=.005
            })
          }

          missiles = missiles.filter(v => {
            if(v[9]<=0){
              spawnMissileSplosion(v)
              return false
            }
            return true
          })
          col1 = ``
          RGBa = RGBFromHSV(-40, .9, 1)  //day
          RGBb = RGBFromHSV(0-20, 0, 1)  //night
          red   = (RGBa[0] * ip1 + RGBb[0] * ip2) |0
          green = (RGBa[1] * ip1 + RGBb[1] * ip2) |0
          blue  = (RGBa[2] * ip1 + RGBb[2] * ip2) |0
          col2 = `rgba(${red},${green},${blue},.3)`
          missiles.map(missile => {
            X = 0
            Y = 0
            Z = missile[11]
            R2(missile[6],missile[7],missile[8])
            missile[3] = X
            missile[4] = Y
            missile[5] = Z
            tx_ = missile[0]
            ty_ = missile[1]
            tz_ = missile[2]
            X1_ = tx = missile[0] += missile[3] * (l=k__? 0 : 1)
            Y1_ = ty = missile[1] += missile[4] * l
            Z1_ = tz = missile[2] += missile[5] * l
            if(!k__){
              X = 
              Y = -2
              Z = 50
              R2(missile[6],missile[7],missile[8])
              X1_ = X += tx_
              Y1_ = Y += ty_
              Z1_ = Z += tz_
              R(Rl,Pt,Yw,1)
              if(Z>0){
                l = Q()
                s = Math.min(1e3,1e4/Z * (1+Rn()*2))
                context.fillStyle = '#ff000020'
                context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                context.fillStyle = '#ff880040'
                context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                context.fillStyle = '#ffffffff'
                context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
              let steps = missileSpeed / 8 | 0
              for(let m = steps; m--;){
                X = tx_ + (tx_-tx)/steps*m + (Rn()-.5) * 4
                Y = ty_ + (ty_-ty)/steps*m + (Rn()-.5) * 4
                Z = tz_ + (tz_-tz)/steps*m + (Rn()-.5) * 4
                smoke = [...smoke, [X,Y,Z,1]]
              }
              let mind = 6e6
              tgtIdx = -1
              players.map((player, idx) => {
                if(player.id != missile[10] && player.alive){
                  X2 = player.X
                  Y2 = player.Y
                  Z2 = player.Z
                  hitFound = false
                  for(let m = steps; m--;){
                    if(!hitFound){
                      tx2 = tx_ + (tx_-tx)/steps*m
                      ty2 = ty_ + (ty_-ty)/steps*m
                      tz2 = tz_ + (tz_-tz)/steps*m
                      d = Math.hypot(X2-tx2,Y2-ty2,Z2-tz2)
                      if(d<mind){
                        mind = d
                        tgtIdx = idx
                      }
                      if(d<10){
                        missile[9] = 0
                        hitFound = true
                        player.health -= missileDamage / 2 + (Rn() * missileDamage/2)
                        shotBy = (shooter = players.filter(v=>v.id == missile[10])[0]).name
                        if(player.health <= 0) shooter.score++
                        if(idx == camSelected){
                          if(player.health <= 0 && player.alive){
                            if(idx == camSelected){
                              killPlayer(player, `FRAGGED BY ${shotBy}`, idx)
                            }else{
                              //killPlayer(player, ``, idx)
                              // handled @ remote for multiplayer
                            }
                          }else{
                            spawnFlashNotice('>>> MISSILE DAMAGE <<<', '#F00')
                            playSound(sounds[`metal${1+Rn()*5|0}`])
                          }
                        }else{
                          if(player.health <= 0){
                            spawnFlashNotice(`YOU FRAGGED ${player.name}`, '#FF0')
                          }else{
                            if(!flashNotices.length) spawnFlashNotice(`YOU HIT ${player.name}!`, '#0FF')
                          }
                        }
                      }
                    }
                  }
                }
              })
            }
            if(missile[9]>0){
              if(!k__){
                if(tgtIdx != -1){
                  rl = missile[6]
                  pt = missile[7]
                  yw = -missile[8]
                  tgtPlayer = players[tgtIdx]
                  X2 = tgtPlayer.X
                  Y2 = tgtPlayer.Y
                  Z2 = tgtPlayer.Z
                  p1 = -Math.atan2(X2-X1_, Z2-Z1_)
                  p2 = -Math.acos((Y2-Y1_) / (Math.hypot(X2-X1_, Y2-Y1_, Z2-Z1_)+.001)) + Math.PI/2
                  while(Math.abs(p1-yw)>Math.PI){
                    if(p1<yw){
                      p1 += Math.PI*2
                    }else{
                      p1 -= Math.PI*2
                    }
                  }
                  missile[8] -= Math.min(homing,Math.max(-homing, p1-yw))
                  missile[7] += Math.min(homing*2,Math.max(-homing*2, p2-pt))
                }
              }
              missile_model.map(v=>{
                context.beginPath()
                v.map(q=>{
                  X = q[0]
                  Y = q[1]
                  Z = q[2]
                  R2(missile[6],missile[7],missile[8])
                  X += tx
                  Y += ty
                  Z += tz
                  R(Rl,Pt,Yw,1)
                  if(Z>0) context.lineTo(...Q())
                })
                stroke(col1, col2)
              })
              if(!k__) missile[9] -= .02
            }
          })

          sparks = sparks.filter(v=>v[6]>0)
          sparks.map((v, i) => {
            X = v[0] += v[3] * (l = k__ ? 0 : 1)
            Y = v[1] += v[4] * l
            Z = v[2] += v[5] * l
            R(Rl,Pt,Yw,1)
            if(Z>0){
              l = Q()
              s = Math.min(1e3,1e4/Z*v[6] * (v[7]==-1?20:4))
              //context.fillStyle = (v[7]==-1? '#00ffff06' : '#ff000006')
              //context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3
              context.fillStyle = (v[7]==-1? `hsla(${(1e3+(1e3+i)**3.1%1)*150-50+260},99%,50%,.06)` : `hsla(${(1e3+(1e3+i)**3.1%1)*50-330},99%,50%,.1`)
              context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=7
              context.fillStyle = '#ffffffff'
              context.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            }
            v[6] -=.0025 * (v[7]==-1?2:1)
          })
          
          col1 = ``
          RGBa = RGBFromHSV(-40, .9, 1)  //day
          RGBb = RGBFromHSV(0-20, 0, 1)  //night
          
          red   = (RGBa[0] * ip1 + RGBb[0] * ip2) |0
          green = (RGBa[1] * ip1 + RGBb[1] * ip2) |0
          blue  = (RGBa[2] * ip1 + RGBb[2] * ip2) |0
          
          col2_ = `rgba(${red},${green},${blue},.3)`
          
          //sounds.alarm.loop = true
          if(players[camSelected].alarm && players[camSelected].alive){
            if(playSounds) sounds.alarm.play()
          }else{
            sounds.alarm.paused = true
          }
          
          players.map((player, idx) => {
            if(player.alive){
              
              if(!k__){
                //if(idx && Rn()<.01) fireMissile(player, idx)
              
                player.fuel -= player.speed / maxSpeed * fuelDepletionRate
                player.alarm = 0
                player.statusMSG = ''
                if(player.health < 4/11) {
                  player.statusMSG += 'HEALTH!'
                  player.alarm += (11/4)/11
                  if(player.health < 0) {
                    killPlayer(player, 'FRAGGED BY THE ENEMY!', idx)
                  }
                }
                if(player.fuel < 4/11) {
                  player.statusMSG += (player.statusMSG ? ', ' : '') + 'FUEL!'
                  player.alarm += (11/4)/11
                  if(player.fuel < 0) {
                    killPlayer(player, 'YOU CAN\'T FLY WITHOUT FUEL!', idx)
                  }
                }
                if(player.altitude < 100) {
                  player.statusMSG += (player.statusMSG ? ', ' : '') + 'ALTITUDE!'
                  player.alarm += (11/4)/11
                  if(player.altitude <= 0) {
                    killPlayer(player, 'YOU CAN\'T FLY UNDERGROUND!', idx)
                  }
                }
                proxWarn = false
                minProx = 6e6
                player.ranges.map((v, i) => {
                  if(i){
                    if(v < 100 && v<minProx) {
                      proxWarn = true
                      minProx = v
                      otherPlayer = i
                    }
                  }
                })
                if(proxWarn){
                  player.statusMSG += (player.statusMSG ? ', ' : '') + 'PROXIMITY!'
                  player.alarm += (11/4)/11
                  if(minProx <= 5) {
                    killPlayer(players[otherPlayer], '', idx)
                    let pname = players[otherPlayer].name
                    killPlayer(player, `YOU CRASHED INTO ${pname}!`)
                  }
                }
                if((player.speed-minSpeed) / (maxSpeed-minSpeed) >= 7.66/11){
                  player.machTimer -= 1/60/3
                  if(player.machTimer <= 0){
                    killPlayer(player, `ENGINES BLEW UP!`, idx)
                  }else{
                    player.statusMSG += (player.statusMSG ? ', ' : '') + 'MACH!'
                    player.alarm += (11/4)/11
                  }
                }else{
                  player.machTimer = 1
                }
                
                if(!player.statusMSG) player.statusMSG = player.defaultMSG
                
                if(!(player.keys[38] || player.keys[40])){
                  player.ptv_ /= 1.15
                }
                if(!(player.keys[37] || player.keys[39])){
                  player.ywv_ /= 1.15
                }
                
                if(idx == 0) doKeys(player, idx)
              }

            
              tx = player.X
              ty = player.Y
              tz = player.Z
              pt1 = player.ptv_ <=0 ? player.pt*1+player.ptv_*50 : -player.pt*1+player.ptv_*2
              pt2 = player.ptv_ > 0 ? (player.pt*1+player.ptv_*30) : 0
              if(player.cameraMode == 0 || idx){
                cont = true
                if(idx){
                  X1 = player.X
                  Y1 = player.Y
                  Z1 = player.Z
                  X2 = players[0].X
                  Y2 = players[0].Y
                  Z2 = players[0].Z
                  d_ = Math.hypot(X2-X1, Y2-Y1, Z2-Z1)
                  if(d_>falloffDist) cont = false
                }
                if(cont){
                  player.trails = player.trails.filter(trail => trail[2]>0)
                  player.trails.map(trail=>{
                    tx_ = trail[0][0] += trail[0][3] * (l=k__?0:1)
                    ty_ = trail[0][1] += trail[0][4] * l
                    tz_ = trail[0][2] += trail[0][5] * l
                    trail[1].map((v, i) => {
                      context.beginPath()
                      v.map(q=>{
                        X = q[0]
                        Y = q[1]
                        Z = q[2]
                        R2(trail[0][6], trail[0][7]*1.25+trail[0][13]*40, trail[0][8]+trail[0][9]*8)
                        X += tx_
                        Y += ty_
                        Z += tz_
                        R(Rl,Pt,Yw,1)
                        if(Z>0) context.lineTo(...Q())
                      })
                      col2_ = `hsla(${trail[2]*360+t*50*player.speed},99%,${Math.max(50,100-(1-trail[2])*60)}%,${trail[2]/1.5})`
                      stroke('',col2_,1,false)
                    })
                    if(!k__){
                      trail[0][3]/=1.001
                      trail[0][4]/=1.001
                      trail[0][5]/=1.001
                      trail[2] -= .1
                    }
                  })
                  if(!k__ && player.showTrails) player.trails = [...player.trails, [[player.X,player.Y,player.Z,player.vx,player.vy/4,player.vz,player.rl,player.pt,player.yw,player.rlv,player.ptv,player.ywv,player.rlv_,player.ptv_,player.ywv_], jet.filter((q, j) => {
                    return ((99+(j+player.trails.length))**3.1)%1<.125
                  }), 1]]
                  context.globalAlpha = 1
                  jet.map((v, i) => {
                    context.beginPath()
                    v.map(q=>{
                      X = q[0]
                      Y = q[1]
                      Z = q[2]
                      rl = players[camSelected].cameraMode  && camSelected == idx? player.rl + player.rlv: player.rl
                      R3(rl, pt1, pt2, player.yw)
                      X += tx
                      Y += ty
                      Z += tz
                      R(Rl,Pt,Yw,1)
                      if(Z>0) context.lineTo(...Q())
                    })
                    col1 = '' //!(i%3) ? col2 : ''
                    stroke(col1,col2_,1,false)
                  })
                  if(!k__){
                    s2 = (1.25+player.afterburner*1)/6 * (player.keys[20] ? 2 : 1) * (player.keys[16] ? .5 : 1)
                    s1 = (1+S(t*600))*s2
                    drawThruster(-.8, .2, -4.5, player.X,player.Y,player.Z,player.rl, pt1, pt2, player.yw,s1*2,s2)
                    drawThruster(.8, .2, -4.5, player.X,player.Y,player.Z,player.rl, pt1, pt2, player.yw,s1*2,s2)
                  }
                }
              }
              
              if(!k__){
                player.rlv /=1.05
                player.ptv /=1.05
                player.ywv /=1.05
                player.vx /=1.02
                player.vy /=1.02
                player.vz /=1.02
                player.ywv += player.ywv_ * Math.min(1,(.1+player.speed/1e4)) * 1.5
                player.ptv += player.ptv_ * Math.min(1,(.3+player.speed/1e3)) * 1.5
                
                player.rl = -player.ywv*30  * ((1+player.ptv*(player.ptv<0?2:-2)))
                player.rl += player.rlv
                player.pt = player.ptv*1.5
                player.yw += player.ywv*(1 + player.speed/200)
              }
              
              X = 0
              Y = 0
              Z = player.speed /8
              pt1 = player.ptv_ <=0 ? player.pt : 0
              pt2 = player.ptv_ > 0 ? player.pt : 0
              R3(player.rl, pt1, pt2, player.yw)
              
              tx_ = player.X
              ty_ = player.Y
              tz_ = player.Z
              player.vx = X
              player.vy = Y * 8
              player.vz = Z
              
              if(idx) doLerp(player)
              
              if(!k__){
                tx = player.X += player.vx * (l = k__ ? 0 : 1)
                ty = player.Y += player.vy * l
                tz = player.Z += player.vz * l
                let steps = player.speed / 40 | 0
                for(let m = steps; m--;){
                  X = tx_ + (tx_-tx)/steps*m + (Rn()-.5) * 6 - player.vx/8
                  Y = ty_ + (ty_-ty)/steps*m + (Rn()-.5) * 6 + 2 - player.vy/8
                  Z = tz_ + (tz_-tz)/steps*m + (Rn()-.5) * 6 - player.vz/8
                  smoke = [...smoke, [X,Y,Z,2]]
                }
              }          

              if(showRangeFinder && idx == camSelected && players.length>1){
                context.textAlign = 'center'
                col1 = '#f002'
                col2 = ''
                X1 = player.X
                Y1 = player.Y
                Z1 = player.Z
                q_=ls=0
                players.map((player2, idx2) => {
                  if(player2.alive){
                    if(idx2 != idx){
                      X = X2 = player2.X
                      Y = Y2 = player2.Y
                      Z = Z2 = player2.Z
                      X = X2
                      Y = Y2
                      Z = Z2
                      rl = Rl
                      d_ = Math.hypot(X2-X1, Y2-Y1, Z2-Z1)
                      R(Rl,Pt,Yw,1)
                      if(Z>2){
                        l_ = Q()
                        q_ = Math.atan2(l_[0] - c.width/2, l_[1] - c.height/2)
                        context.beginPath()
                        ls=Math.min(400 , Math.hypot(l_[0] - c.width/2, l_[1] - c.height/2))
                        context.arc(c.width/2, c.height/2, ls, 0, 7)
                        Z = 2
                        stroke(col1,col2, Math.min(10, ls/50), false)
                        context.globalAlpha = .6
                        context.font = (fs=Math.min(50, Math.max(20, 5e3/(1+d_/4)))) + 'px Courier Prime'
                        context.fillStyle = '#FFF'
                        context.lineWidth = fs/5
                        context.strokeStyle = '#40f8'
                        context.strokeText(player2.name, l_[0], l_[1]-fs*2+fs/3)
                        context.strokeText('RNG ' + Math.round(d_), l_[0], l_[1]-fs+fs/3)
                        context.strokeText('ALT ' + player2.altitude, l_[0], l_[1]+fs/3)
                        context.strokeText('SPD ' + player2.speed, l_[0], l_[1]+fs+fs/3)
                        context.fillText(player2.name, l_[0], l_[1]-fs*2+fs/3)
                        context.fillText('RNG ' + Math.round(d_), l_[0], l_[1]-fs+fs/3)
                        context.fillText('ALT ' + player2.altitude, l_[0], l_[1]+fs/3)
                        context.fillText('SPD ' + player2.speed, l_[0], l_[1]+fs+fs/3)
                        player.ranges[idx2] = d_
                      }
                      context.beginPath()
                      p_ = q_// + rl
                      ls2 = Math.min(100, Math.max(20, 5e3/(1+d_/4)))
                      tx = c.width/2 + S(p_) * ls
                      ty = c.height/2 + C(p_) * ls
                      ax = ay = 0
                      for(let j=3;j--;){
                        ax += X = S(p=Math.PI*2/3*j + p_ +Math.PI/1.5) * ls2 + tx
                        ay += Y = C(p) * ls2 + ty
                        context.lineTo(X,Y)
                      }
                      ax /= 3
                      ay /= 3
                      Z = 2
                      stroke('#0f8a','#0f82',.25,false)

                      X = X2
                      Y = Y2
                      Z = Z2
                      R(Rl,Pt,Yw,1)
                      if(Z>2){
                        context.lineTo(...l_)
                        Z = 2
                        stroke('#f004','',1, false)
                        context.beginPath()
                        context.arc(...l_, 50,0,7)
                        stroke('#f004','',1, false)
                      }
                    }
                  }
                })
                context.textAlign = 'left'
              }

              if(!k__){
                mind = 6e6
                sd = 40
                for(i=sd; i;i-=2){
                  ofy_ = -Math.floor((player.Y/2000-1-i/40))*40 +(0 + sd-i)
                  if(ofy_<mind) {
                    mind = ofy_
                    player.altitude = ofy_ + sd/2
                  }
                }
                

                if(idx == camSelected){
                  if(showHUD && player.cameraMode == 1) {
                    context.drawImage((player.alarm && (t*10|0)%2) ? troubleHUD : HUD,0,0,c.width,c.height)
                    ofx2 = 266.5
                    ofy2 = 40
                    ls2 = 40.05
                    sp2 = 7.17
                    context.strokeStyle = '#fff8'

                    ofx = 261.5
                    ofy = 35
                    ls = 50.05
                    sp = -2.89
                    context.globalAlpha = 1
                      
                    //health
                    context.lineWidth = 1
                    rng = player.health * 11
                    for(i=0;i<10;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      if((i+1)<=rng){
                        context.fillStyle = rng > 4 ? '#084' : '#800'
                        context.fillRect(X,Y,ls2,ls2)
                        X = ofx + (i%10)*(ls+sp)
                        Y = ofy + (i/10|0) * (ls+sp)*.975
                        context.drawImage(rng > 4 ? greenLED : redLED, X,Y,ls,ls)
                      }else{
                        context.fillStyle = '#000'
                        context.fillRect(X,Y,ls2,ls2)
                      }
                    }

                    //fuel
                    rng = player.fuel * 11
                    for(i=10;i<20;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      if(((i-10)+1)<=rng){
                        context.fillStyle = rng > 4 ? '#084' : '#800'
                        context.fillRect(X,Y,ls2,ls2)
                        X = ofx + (i%10)*(ls+sp)
                        Y = ofy + (i/10|0) * (ls+sp)*.975
                        context.drawImage(rng > 4 ? greenLED : redLED, X,Y,ls,ls)
                      }else{
                        context.fillStyle = '#000'
                        context.fillRect(X,Y,ls2,ls2)
                      }
                    }

                    for(i=0;i<20;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      context.strokeRect(X,Y,ls2,ls2)
                    }

                    ofx2 = c.width/2 +222
                    ofy2 = 40
                    ls2 = 40.05
                    sp2 = 7.17
                    context.strokeStyle = '#fff8'

                    ofx = c.width/2 + 217
                    ofy = 35
                    ls = 50.05
                    sp = -2.89
                    context.globalAlpha = 1
                    
                    //speed
                    rng = player.speed / maxSpeed * 11
                    for(i=0;i<10;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      if((i+1)<=rng){
                        context.fillStyle = i>6 ? '#800' : '#084'
                        context.fillRect(X,Y,ls2,ls2)
                        X = ofx + (i%10)*(ls+sp)
                        Y = ofy + (i/10|0) * (ls+sp)*.975
                        context.drawImage(i>6?redLED:greenLED, X,Y,ls,ls)
                      }else{
                        context.fillStyle = '#000'
                        context.fillRect(X,Y,ls2,ls2)
                      }
                    }

                    //alarm
                    //rng = player.alarm * 11
                    for(i=10;i<20;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      if(player.alarm && (i+1)%11<=(t*60|0)%11){
                        context.fillStyle = '#800'
                        context.fillRect(X,Y,ls2,ls2)
                        X = ofx + (i%10)*(ls+sp)
                        Y = ofy + (i/10|0) * (ls+sp)*.975
                        context.drawImage(redLED, X,Y,ls,ls)
                      }else{
                        context.fillStyle = '#000'
                        context.fillRect(X,Y,ls2,ls2)
                      }
                    }

                    for(i=0;i<20;i++){
                      X = ofx2 + (i%10)*(ls2+sp2)
                      Y = ofy2 + (i/10|0) * (ls2+sp2)*.975
                      context.strokeRect(X,Y,ls2,ls2)
                    }
                    
                    i=20
                    X = ofx2 + (i%10)*(ls2+sp2)
                    Y = ofy2 + (i/10|0) * (ls2+sp2)*.97
                    context.fillStyle = player.alarm ? '#400' : '#042'
                    context.fillRect(X,Y,ls2*11.6,ls2-2)
                    context.strokeRect(X,Y,ls2*11.6,ls2-2)
                    context.font = (fs = 34) + 'px Courier Prime'
                    context.fillStyle = '#888'
                    context.fillText(player.alarm ? 'ALARM!' : 'STATUS', X+=5, Y+fs*.85)
                    context.fillStyle = player.alarm ? ((t*20|0)%2?'#f44':'#ff0') : '#4f8'
                    context.font = (fs = 26) + 'px Courier Prime'
                    context.fillText(player.statusMSG, X+132, Y+fs*1)
                    

                    if(showCrosshair && crosshairsLoaded){
                      context.globalAlpha = (crosshairSel == 1 ? .25 : .5)
                      s=500
                      context.drawImage(crosshairImgs[crosshairSel].img,c.width/2-s/2,c.height/2-s/2,s,s)
                      context.globalAlpha = 1
                    }
                  }
                  
                  ls     = 105
                  margin = 10
                  olc = context.lineCap
                  
                  // speedometer
                  margin += 15
                  Z = 3
                  col    = '#4ff'
                  context.beginPath()
                  context.arc(margin+ls,margin+ls,ls,0,7)
                  stroke(col,'#4f82',.25)
                  sd     = 10
                  opi    = -Math.PI*2/10
                  context.textAlign = 'center'
                  for(i=sd+1;i--;){
                    context.font = (fs = 16) + 'px Courier Prime'
                    context.fillStyle = '#fff'
                    X = ls+margin+S(p=Math.PI*2/(sd+2)*-i+opi)*ls*1.15
                    Y = ls+margin+C(p)*ls*1.15
                    context.fillText(i*(soundSpeed/10|0),X,Y+fs/3)
                  }
                  sd     = 100
                  for(i=sd+1;i--;){
                    context.beginPath()
                    f = !(i%10)?.75:(!(i%5)?.85:.95)
                    X = ls+margin+S(p=Math.PI*2/(sd+20)*-i+opi)*(ls*f)
                    Y = ls+margin+C(p)*(ls*f)
                    context.lineTo(X,Y)
                    X = ls+margin+S(p=Math.PI*2/(sd+20)*-i+opi)*ls
                    Y = ls+margin+C(p)*ls
                    context.lineTo(X,Y)
                    Z=3
                    col2 = i < sd * (7.25/10) ? col : '#f44'
                    stroke(col2,'',.2,true)
                  }
                  fs = 40
                  context.beginPath()
                  context.lineTo(margin+ls,margin+ls)
                  X = ls+margin+S(p=Math.PI*2/(sd+2)*-(player.speed/maxSpeed*Math.PI*27)+opi)*(ls*.8)
                  Y = ls+margin+C(p)*(ls*.8)
                  context.lineTo(X,Y)
                  stroke('#f04','',1,true)
                  margin = 50
                  margin -=15
                  context.beginPath()
                  context.lineTo(margin/4,ls*2+margin*2)
                  context.lineTo(margin/4+ls*1.5,ls*2+margin*2)
                  context.lineTo(margin/4+ls*1.5,ls*2+margin*3)
                  context.lineTo(margin/4,ls*2+margin*3)
                  Z=3
                  context.textAlign = 'center'
                  stroke(col,'#4f82',.2,true)
                  context.fillStyle = '#fff'
                  context.font = (fs = 40) + 'px Courier Prime'
                  context.fillText((Math.round(player.speed/maxSpeed*soundSpeed)),margin*1.3,margin*3+ls*2-fs/8)
                  context.textAlign = 'right'
                  context.fillText('MPH ',margin+ls*1.45,margin*3+ls*2-fs/8)
                  context.lineJoin = context.lineCap = olc
                  context.textAlign = 'left'
                  
                  tx = X = c.width - 139
                  ty = Y = 131
                  Z = 3
                  ls = 100
                  context.beginPath()
                  context.arc(X,Y,ls,0,7)
                  stroke('#0f08','#0008')
                  context.beginPath()
                  X = S(p=-player.rl * 1.25 + Math.PI/2) * ls
                  Y = C(p) * ls 
                  context.lineTo(tx-X,ty-Y)
                  context.lineTo(tx+X,ty+Y)
                  stroke('#f008','',.5)
                  
                  sd = 40
                  context.textAlign = 'left'
                  context.font = (fs = 32) + 'px Courier Prime'
                  mind = 6e6
                  for(i=sd; i;i-=2){
                    p_ = (i + 1e4) - player.Y / 50
                    s = !(i%10) ? 20 : 5
                    X = tx - s
                    Y = ty - ls + ((ls/sd * p_ * 2)%(ls*2))

                    p = Math.atan2(X-tx, Y-ty) -player.rl*1.25
                    d = Math.hypot(X-tx, Y-ty)
                    X = tx + S(p) * d
                    Y = ty + C(p) * d

                    ofy_ = -Math.floor((player.Y/2000-1-i/40))*40 +(0 + sd-i)
                    if(ofy_<mind) {
                      mind = ofy_
                      player.altitude = ofy_ + sd/2
                    }
                    if(!(i%10)){
                      context.fillStyle = '#fff'
                      
                      context.save()
                      context.translate(X, Y-fs/4)
                      context.rotate(player.rl*1.25)
                      context.globalAlpha = 1 / (1+Math.abs((ty - ls + ((ls/sd * p_ * 2)%(ls*2)))-ty)**3/1e5)
                      context.fillText(ofy_, 50, fs/2)
                      context.restore()
                    }
                    context.beginPath()            
                    X = tx - s
                    Y = ty - ls + ((ls/sd * p_ * 2)%(ls*2))
                    p = Math.atan2(X-tx, Y-ty) -player.rl*1.25
                    d = Math.hypot(X-tx, Y-ty)
                    X = tx + S(p) * d
                    Y = ty + C(p) * d
                    Z = 3
                    context.lineTo(X,Y)
                    X = tx + s
                    Y = ty - ls + ((ls/sd * p_ * 2)%(ls*2))
                    p = Math.atan2(X-tx, Y-ty) -player.rl*1.25
                    d = Math.hypot(X-tx, Y-ty)
                    X = tx + S(p) * d
                    Y = ty + C(p) * d
                    Z = 3
                    context.lineTo(X,Y)
                    stroke('#f008','',.5)
                  }
                }
                if(!scores.filter(q=>+q.id==+player.id).length){
                  scores = [...scores, {id: +player.id, score: 0}]
                }else{
                  if(player.score > scores.filter(q=>+q.id==+player.id)[0].score){
                    scores.filter(q=>+q.id==+player.id)[0].score = player.score
                  }
                }
              }
            }else{
              if(!k__){
                if(!sparks.filter(v=>v[7]==player.id).length && !flashNotices.length) {
                  respawnPlayer(player, idx)
                }
              }
            }
          })

          if(k__){
            applyClearMask()
            x.drawImage(buffer,c.width,0,-c.width,c.height)
          }
          
      
          menuWidth = 150
          menux = -menuWidth
          bct = 0  // must appear before 1st button (for callbacks/ clickability)
          ofy_ = 620+(players.length-2.5)*(32*1.75)
          ofx = hotkeysModalVisible ? (hotkeyX=Math.min(450,(hotkeyX+=hotkeyX/2+10))) : (hotkeyX=Math.max(0,(hotkeyX-=hotkeyX/2)))
          x.beginPath()
          x.lineTo(ofx-500, c.height - ofy_)
          for(i=0;i<28;i++){
            X = Math.min(50, S(Math.PI/27*i)**.5*80)+ofx
            Y = c.height - ofy_ + i*14.5
            x.lineTo(X, Y)
          }
          x.lineTo(ofx-500, c.height - ofy_ + 27*14.5)
          x.textAlign = 'left'
          Z=10
          stroke('#0ff8','#024a',2,true)//stroke('#f40','#422c',2,true)
          x.font = (fs=40) + 'px Courier Prime'
          X = 10 + ofx
          fs/=1.25
          Y = c.height - ofy_ + 40 + fs/1.5
          x.fillStyle = '#f0f'
          x.fillText('H', X, Y)
          x.fillText('O', X, Y+=fs*1.25)
          x.fillText('T', X, Y+=fs*1.25)
          x.fillText('K', X, Y+=fs*2.5)
          x.fillText('E', X, Y+=fs*1.25)
          x.fillText('Y', X, Y+=fs*1.25)
          x.fillText('S', X, Y+=fs*1.25)
          
          if(hotkeysModalVisible){
            X-=460
            x.strokeStyle = '#40fa'
            x.lineWidth = 4
            x.fillStyle = '#8ff'
            x.font = (fs=32) + 'px Courier Prime'
            for(m=2;m--;){
              Y = c.height - ofy_ + fs/1.25
              ofx2 = m?4:2
              ofy2= m?2:0
              textFunc = m ? 'strokeText' : 'fillText'
              x[textFunc](' Arrows  - navigate', X+ofx2, ofy2+Y)
              x[textFunc](' CAPS    - accelerate', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' SHIFT   - decelerate', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' CTRL    - fire guns', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' M       - toggle view', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' I       - info toggle', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' 0-9     - player cams', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' C       - crosshairs', X+ofx2, ofy2+(Y+=fs*1.3))
              x[textFunc](' H       - hotkeys', X+ofx2, ofy2+(Y+=fs*1.3))
            }
          }
          olc = x.lineJoin

          if(showInfo){
            x.font = (fs=50)+'px Courier Prime'
            x.beginPath()
            X = -50
            Y = c.height-10 - (players.length+1) * fs
            x.lineTo(X,Y)
            X += 600
            for(i=10;i--;){
              X -= S(p=Math.PI/20*i+Math.PI) * 5
              Y -= C(p) * 4
              x.lineTo(X,Y)
            }
            Y = c.height-32
            for(i=10;i--;){
              X -= S(p=Math.PI/20*i+Math.PI*1.5) * 5
              Y += C(p) * 4
              x.lineTo(X,Y)
            }
            //Y = c.height-10
            //x.lineTo(X,Y)
            X = c.width - 775
            Y = c.height -10
            x.lineTo(X,Y)
            for(i=10;i--;){
              X -= S(p=Math.PI/20*i+Math.PI) * 5
              Y += C(p) * 4
              x.lineTo(X,Y)
            }
            Y-= 105
            x.lineTo(X,Y)
            for(i=10;i--;){
              X += S(p=Math.PI/20*i+Math.PI/2) * 5
              Y += C(p) * 5
              x.lineTo(X,Y)
            }
            X = c.width + 50
            x.lineTo(X,Y)
            Y+= 500
            x.lineTo(X,Y)
            X = -50
            x.lineTo(X,Y)
            Z = 15
            stroke('#0ff8','#024a',2,true)
            
            x.textAlign = 'left'
            x.lineWidth = 4
            x.strokeStyle = '#000a'
            x.strokeText(`leaderboard`,10,c.height-(1+players.length)*fs+32)
            x.fillStyle = '#f08'
            x.fillText(`leaderboard`,10-2,c.height-(1+players.length)*fs+32-2)
            let scores = JSON.parse(JSON.stringify(players)).sort((a, b) => b.score-a.score)
            x.fillStyle = '#fff'
            scores.map((player, i) => {
              x.fillText(`${player.score} ${player.name}`,10,c.height-(1+players.length)*fs+(i+1)*fs+32)
            })
            
            
            x.lineWidth = 4
            x.fillStyle = '#f08'
            x.strokeText(` score`,c.width-725,c.height-120+fs*1)
            x.strokeText(`   cam`,c.width-725,c.height-120)
            x.strokeText(`health `,c.width-725,c.height-120+fs*2)
            //x.fillStyle = '#f08'
            x.fillText(`   cam`,c.width-725-2,c.height-120-2)
            x.fillText(` score`,c.width-725-2,c.height-120+fs*1-2)
            x.fillText(`health `,c.width-725-2,c.height-120+fs*2-2)
            x.lineWidth = 10
            x.fillStyle = '#fff'
            x.fillText(`       ${players[camSelected].name}`,c.width-725,c.height-120)
            x.fillText(`       ${players[camSelected].score}`,c.width-725,c.height-120+fs*1)
            x.fillStyle = '#0f8'
            x.fillRect(c.width-510,c.height-50, Math.max(0, 480*players[camSelected].health),32)
            x.strokeStyle = '#fff'
            x.lineWidth = 5
            x.strokeRect(c.width-510,c.height-50, 480,32)
          }
          
          X = c.width - 690
          Y = c.height - 132
          renderButton('I⇩', X, Y, '  hide menu', 'toggleMenu()', 'rectangle', '#0ff8', '#2088', 40)

          X = c.width - 55
          Y = c.height - 50
          renderButton('I⇧', X, Y, '  show menu', 'toggleMenu()', 'rectangle', '#0ff8', '#2088', 64)
          renderButton('🔇', c.width/2, 45, '  mute audio', 'toggleAudio()', 'rectangle', '#0ff8', '#2088', 64)
          renderButton('🔊', c.width/2, 45, '  unmute audio', 'toggleAudio()', 'rectangle', '#0ff8', '#2088', 64)

          X = c.width/2
          Y = 120
          renderButton('🔗', X, Y, '  copy shareable link to this game-in-progress', 'fullCopy()', 'rectangle', '#0ff8', '#2088', 64)
          
          if(!pointerLocked){ // tooltips
            buttons.map(button=>{
              if(button.hover && button.visible){
                let fs
                let margin = 8
                x.font = (fs=24) + 'px Courier Prime'
                X = mx + 5
                let w = x.measureText(button.tooltip).width + margin*2
                let h =  fs + margin * 2
                if(X+w > c.width) w*=-1
                if(my+h > c.height) h*=-1
                x.textAlign = 'left'
                x.fillStyle = '#040d'
                x.fillRect(X,my,w,h)
                x.fillStyle = '#0ff'
                x.fillText(button.tooltip,X+(w<0?w:0)+fs/2-2, my+(h<0?h:0)+fs*1.125)
              }
            })
          }
          buttonsLoaded = true
        }


        flashNotices = flashNotices.filter(v=>v[2]>0)
        if(flashNotices.length){
          x.fillStyle = flashNotices[l=flashNotices.length-1][1]
          x.globalAlpha = flashNotices[l][2]
          x.textAlign = 'center'
          x.fillRect(0,0,c.width,c.height)
          x.fillStyle = '#fff'
          x.font = (fs=60)+'px Courier Prime'
          x.fillText(flashNotices[l][0],c.width/2, c.height/1.6 - fs)
          flashNotices[l][2]-=.025
        }

        x.globalAlpha = 1

        t+=1/60
        requestAnimationFrame(Draw)
      }

      alphaToDec = val => {
        let pow=0
        let res=0
        let cur, mul
        while(val!=''){
          cur=val[val.length-1]
          val=val.substring(0,val.length-1)
          mul=cur.charCodeAt(0)<58?cur:cur.charCodeAt(0)-(cur.charCodeAt(0)>96?87:29)
          res+=mul*(62**pow)
          pow++
        }
        return res
      }

      regFrame = document.querySelector('#regFrame')
      launchModal = document.querySelector('#launchModal')
      launchStatus = document.querySelector('#launchStatus')
      gameLink = document.querySelector('#gameLink')

      launch = () => {
        let none = false
        if(0){//&&(none = typeof users == 'undefined') || users.length<2){
          alert("this game requires at least one other player to join!\n\nCurrent users joined: " + (none ? 0 : users.length))
          return
        }
        launchModal.style.display = 'none'
        launched = true
        Draw()
      }

      doJoined = jid => {
        regFrame.style.display = 'none'
        regFrame.src = ''
        userID = +jid
        sync()
      }

      fullSync = false
      individualPlayerData = {}
      syncPlayerData = users => {
        
        // spelunk! stuff 
        
        if(typeof players != 'undefined' && players.length){
          let keep = []
          users.map(v=>{
            players.map((player, idx) => {
              if(+v.id == player.id) keep=[...keep, idx]
            })
          })
          players = players.filter((player, idx) => {
            let ret = !!keep.filter(v=>v==idx).length
            if(!ret){
              camSelected = 0
              spawnFlashNotice(player.name + ' has left the arena...', '#00f')
            }
            return ret
          })
        }
        
        /*********************/
        
        users.map((user, idx) => {
          if((typeof Players != 'undefined') &&
             (l=Players.filter(v=>v.playerData.id == user.id)).length){
            l[0] = user
            fullSync = true
          }else if(launched && t){
            addPlayers(user)
          }
        })
        
        if(launched){
          Players = Players.filter((v, i) => {
            if(!users.filter(q=>q.id==v.playerData.id).length){
              cams = cams.filter((cam, idx) => idx != i)
            }
            return users.filter(q=>q.id==v.playerData.id).length
          })
          iCamsc = Players.length
          Players.map((AI, idx) => {
            if(AI.playerData.id == userID){
              individualPlayerData['id'] = userID
              individualPlayerData['name'] = AI.playerData.name
              individualPlayerData['time'] = AI.playerData.time
              
              
              if(typeof players == 'object' && players.length) {
                let player = players[0]
                let sendPlayer = {
                  id: userID,
                  X: player.X,
                  Y: player.Y,
                  Z: player.Z,
                  rl: player.rl,
                  pt: player.pt,
                  yw: player.yw,
                  rlv: player.rlv,
                  ptv: player.ptv,
                  ywv: player.ywv,
                  vx: player.vx,
                  vy: player.vy,
                  vz: player.vz,
                  name: player.name,
                  health: player.health,
                  speed: player.speed,
                  shooting: player.keys[17] ? 1 : 0,
                  deathSplosions: JSON.parse(JSON.stringify(sendDeathSplosions)),
                  missileSplosions: JSON.parse(JSON.stringify(sendMissileSplosions)),
                }
                individualPlayerData['playerData'] = sendPlayer
              }
              
              // clear, having sent
              setTimeout(()=>{sendDeathSplosions = []}, 1000)
              setTimeout(()=>{sendMissileSplosions = []}, 1000)
              
              if(typeof scores == 'object' && scores.length){
                individualPlayerData['scores'] = scores
              }

            }else{
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    // straight mapping of incoming data <-> players
                    case 'playerData':
                      if(typeof el[key] != 'undefined' && +el[key].id != +userID){
                        let incomingPlayer = el[key]
                        if(players.filter(v=>+v.id == +el[key].id).length){
                          let matchingPlayer = players.filter(v=>+v.id == +el[key].id)[0]
                          let id = +el[key].id
                          Object.entries(incomingPlayer).forEach(([key2, val2]) => {
                            omit = false
                            //switch(key2){
                              //case 'Y': omit = true; break
                              //case 'vy': omit = true; break
                              //case 'pt': omit = true; break;
                              //case 'rl': omit = true; break
                            //}
                            if(!omit) {
                              if(key2 == 'score'){
                                if(+matchingPlayer[key2] < +val2) matchingPlayer[key2] = val2
                              }else{
                                switch(key2){
                                  case 'deathSplosions':
                                    val2.map(loc => {
                                      spawnSplosion(...loc)
                                    })
                                  break
                                  case 'missileSplosions':
                                    val2.map(loc => {
                                      spawnMissileSplosion(...loc)
                                    })
                                  break
                                  case 'health':
                                    matchingPlayer.health = val2
                                  break
                                  case 'speed':
                                    matchingPlayer.speed = val2
                                  break
                                  case 'X':
                                    matchingPlayer.lerpToX = val2
                                  break
                                  case 'Y':
                                    matchingPlayer.lerpToY = val2
                                  break
                                  case 'Z':
                                    matchingPlayer.lerpToZ = val2
                                  break
                                  case 'shooting':
                                    if(!!(+val2)){
                                      if(matchingPlayer.keyTimers[17]<t){
                                        matchingPlayer.keyTimers[17] = t+missileTimerInterval 
                                        let idx_
                                        players.map((v, i) => {
                                          if(+v.id == +matchingPlayer.id) idx_ = i
                                        })
                                        fireMissile(matchingPlayer, idx_)
                                      }
                                    }
                                  break
                                  case 'vx':
                                    matchingPlayer.lerpToVx = val2
                                  break
                                  case 'vy':
                                    matchingPlayer.lerpToVy = val2
                                  break
                                  case 'vz':
                                    matchingPlayer.lerpToVz = val2
                                  break
                                  case 'rl':
                                    matchingPlayer.lerpToRl = val2
                                  break
                                  case 'pt':
                                    matchingPlayer.lerpToPt = val2
                                  break
                                  case 'yw':
                                    matchingPlayer.lerpToYw = val2
                                  break
                                  case 'rlv':
                                    matchingPlayer.lerpToRlv = val2
                                  break
                                  case 'ptv':
                                    matchingPlayer.lerpToPtv = val2
                                  break
                                  case 'ywv':
                                    matchingPlayer.lerpToYwv = val2
                                  break
                                }
                              }
                            }
                          })
                        }
                      }
                    break;
                    case 'scores':
                      if(typeof el[key] != 'undefined'){
                        el[key].map(score=>{
                          players.map(player=>{
                            if(+player.id == +score.id && score.score > player.score) player.score = score.score
                          })
                        })
                      }
                    break
                  }
                })
              }
            }
          })
        }
      }

      recData              = []
      opIsX = true
      ofidx                = 0
      users                = []
      userID               = ''
      gameConnected        = false
      Players              = []
      playerName           = ''
      sync = () => {
        let sendData = {
          gameID,
          userID,
          individualPlayerData,
          //collected: 0
        }
        fetch('sync.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res=>res.json()).then(data=>{
          if(data[0]){
            recData = data[1]
            if(data[3] && userID != gmid){
              individualPlayerData = recData.players[data[3]]
            }
            users = []
            Object.entries(recData.players).forEach(([key,val]) => {
              val.id = key
              users = [...users, val]
            })
            
            syncPlayerData(users)
            
            if(userID) playerName = recData.players[data[3]]['name']
            if(data[2]){ //needs reg
              regFrame.style.display = 'block'
              regFrame.src = `reg.php?g=${gameSlug}&gmid=${gmid}` 
            }else{
              if(!gameConnected){
                setInterval(()=>{sync()}, pollFreq = 500)  //ms
                gameConnected = true
              }
              if(!launched){
                launchStatus.innerHTML = ''
                users.map(user=>{
                  launchStatus.innerHTML      += user.name
                  launchStatus.innerHTML      += ` joined...`
                  if(user.id == gmid){
                    launchStatus.innerHTML    += ` [game master]`
                  }
                  launchStatus.innerHTML      += `<br>`
                })
                launchStatus.innerHTML      += `<br>`.repeat(4)
                launchButton = document.createElement('button')
                launchButton.innerHTML = 'launch!'
                launchButton.className = 'buttons'
                launchButton.onclick = () =>{ launch() }
                launchStatus.appendChild(launchButton)
                if(gameLink.innerHTML == ''){
                  launchModal.style.display = 'block'
                  resultLink = document.createElement('div')
                  resultLink.className = 'resultLink'
                  if(pchoice){
                    resultLink.innerHTML = location.href.split(pchoice+userID).join('')
                  }else{
                    resultLink.innerHTML = location.href
                  }
                  gameLink.appendChild(resultLink)
                  copyButton = document.createElement('button')
                  copyButton.title = "copy link to clipboard"
                  copyButton.className = 'copyButton'
                  copyButton.onclick = () => { copy() }
                  gameLink.appendChild(copyButton)
                }
              }
            }
          }else{
            console.log('error! crap')
          }
        })
      }

      fullCopy = () => {
        launchButton = document.createElement('button')
        launchButton.innerHTML = 'launch!'
        launchButton.className = 'buttons'
        launchButton.onclick = () =>{ launch() }
        launchStatus.appendChild(launchButton)
        gameLink.innerHTML = ''
        launchModal.style.display = 'block'
        resultLink = document.createElement('div')
        resultLink.className = 'resultLink'
        if(location.href.indexOf('&p=')!=-1){
          resultLink.innerHTML = location.href.split('&p='+userID).join('')
        }else{
          resultLink.innerHTML = location.href
        }
        gameLink.appendChild(resultLink)
        copyButton = document.createElement('button')
        copyButton.className = 'copyButton'
        gameLink.appendChild(copyButton)
        copy()
        launchModal.style.display = 'none'
        setTimeout(()=>{
          mbutton = mbutton.map(v=>false)
        },0)
      }

      copy = () => {
        var range = document.createRange()
        range.selectNode(document.querySelectorAll('.resultLink')[0])
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
        document.execCommand("copy")
        window.getSelection().removeAllRanges()
        let el = document.querySelector('#copyConfirmation')
        el.style.display = 'block';
        el.style.opacity = 1
        reduceOpacity = () => {
          if(+el.style.opacity > 0){
            el.style.opacity -= .02 * (launched ? 4 : 1)
            if(+el.style.opacity<.1){
              el.style.opacity = 1
              el.style.display = 'none'
            }else{
              setTimeout(()=>{
                reduceOpacity()
              }, 10)
            }
          }
        }
        setTimeout(()=>{reduceOpacity()}, 250)
      }
      
      userID = launched = pchoice = false
      if(location.href.indexOf('gmid=') !== -1){
        href = location.href
        if(href.indexOf('?g=') !== -1) gameSlug = href.split('?g=')[1].split('&')[0]
        if(href.indexOf('&g=') !== -1) gameSlug = href.split('&g=')[1].split('&')[0]
        if(href.indexOf('?gmid=') !== -1) gmid = href.split('?gmid=')[1].split('&')[0]
        if(href.indexOf('&gmid=') !== -1) gmid = href.split('&gmid=')[1].split('&')[0]
        if(href.indexOf('?p=') !== -1) userID = href.split(pchoice='?p=')[1].split('&')[0]
        if(href.indexOf('&p=') !== -1) userID = href.split(pchoice='&p=')[1].split('&')[0]
        gameID = alphaToDec(gameSlug)
        if(gameID) sync(gameID)

        if(userID == gmid){
          turnID = 0
        }else{
          turnID = 1
        }
      }
    </script>
  </body>
</html> 