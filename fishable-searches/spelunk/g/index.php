<!DOCTYPE html>
<html>
  <head>
    <title>SPELUNK! multiplayer/online ARENA</title>
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
      // to do
      // ✔ crosshairs
      // ✔ bullets / riccochets
      // ✔ levels
      // ✔ AI players (practice mode)
      // ✔ scoring
      // ✔ HUD / info
      // ✔ level selection (by game creator / round winner)
      // ✔ disallow control of players @ cam view
      // ✔ re-select level / exit (for practice mode)
      // * arena integration (multiplayer mode)
      //   ✔ player death-splosions @ remote screens (vs. lerp-to-respawn)
      //   ✔ topo crosshair -> correct player
      //   ✔ key/mouse action @ other-player-cams does flashMessage
      //   ✔ cameras fixed (alt views have wrong orientation, e.g., also need info)
      //   ✔ sync random-seed level configs
      //   ✔ hotkeys legend
      //   ✔ add hotkeys to practice version
      //   ✔ players proper spawn location @ level change
      //   ✔ game link button
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
        oX=oY=oZ=0
        Rl=0, Pt=0, Yw=0
        if(!t){
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

          R=R2=(Rl,Pt,Yw,m)=>{
            M=Math
            X-=oX
            Y-=oY
            Z-=oZ
            A=M.atan2
            H=M.hypot
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
          }
          
          R3=(Rl,Pt,Yw,m)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
          }
          
          R4=(Rl,Pt,Yw,m)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            if(m){
              X+=0
              Y+=0
              Z+=m
            }
          }
          Q4=()=>[X/Z*700, Y/Z*700]
          
          Q=()=>[c.width/2+X/Z*700, c.height/2+Y/Z*700]

          I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0

          Rn = Math.random
          async function loadOBJ(url, scale, tx, ty, tz, rl, pt, yw) {
            let res
            await fetch(url, res => res).then(data=>data.text()).then(data=>{
              let X, Y, Z, ax, ay, az, maxY
              let R2 = (Rl,Pt,Yw,m) => {
                M=Math
                A=M.atan2
                H=M.hypot
                X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
                Z=C(p)*d
                Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
                Z=C(p)*d
                X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
                Y=C(p)*d
              }
              a=[]
              data.split("\nv ").map(v=>{
                a=[...a, v.split("\n")[0]]
              })
              a=a.filter((v,i)=>i).map(v=>[...v.split(' ').map(n=>(+n.replace("\n", '')))])
              ax=ay=az=0
              a.map(v=>{
                v[1]*=-1
                ax+=v[0]
                ay+=v[1]
                az+=v[2]
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
                v[1]=Y
                v[2]=Z
              })
              maxY=-6e6
              a.map(v=>{
                if(v[1]>maxY)maxY=v[1]
              })
              a.map(v=>{
                //v[1]-=maxY-oY
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
            
            let animation = {
              name             ,
              speed            ,
              frameCt:        0,
              fileList:      '',
              curFrame:       0,
              loopRangeStart: 0,
              loopRangeEnd:   0,
              hasLoop:    false,
              looping:    false,
              frameData:     [],
              loaded:     false,
              active:      true,
            }
            
            fetch(`${rootURL}/${name}/fileList.json`).then(v => v.json()).then(data => {
              animation.fileList = data.fileList
              if(animation.fileList.hasLoop){
                animation.hasLoop = true
                //animation.looping = true
                animation.loopRangeStart = animation.fileList.loopRangeStart
                animation.loopRangeEnd = animation.fileList.loopRangeEnd
              }
              for(let i=0; i<+animation.fileList.fileCount; i++){
                let file = `${rootURL}/${name}/${animation.fileList.fileName}${i}.${animation.fileList.suffix}`
                loadOBJ(file, size, X,Y,Z, rl,pt,yw).then(el=>{
                  animation.frameData[i] = el
                  animation.frameCt++
                  if(animation.frameCt == +animation.fileList.fileCount) {
                    console.log(`loaded animation: ${name}`)
                    animation.loaded = true
                    animations = [...animations, animation]
                  }
                })
              }
            })
            return name
          }
          
          drawAnimation = (animation_name, scol='', fcol='', lineWidth=2, glowing=true, overrideGlobalAlpha=1, player) => {
            //let idx
            //players.map((v,i) => {
            //  if(player.id == v.id) idx = i
            //})
            //player = players[idx]
            tx = player.lerpX
            ty = player.lerpY + playerSize
            tz = player.lerpZ
            trl = player.rl
            tpt = player.pt
            tyw = player.yw
            vx = player.vx
            vy = player.vy
            vz = player.vz
            let animation = animations.filter(el => animation_name == el.name)// && el.loaded && el.active)
            if(animation.length){
              animation = animation[0]
              if(animation_name == 'back_flip_male') {
                animation.speed = 1
                animation.looping = false
              }else if((d=Math.hypot(vx, vz))>.05) {
                animation.speed =  d/4
                animation.looping = true
              }else{
                player.animation.curFrame = 0
                animation.looping = false
              }
              if(animation_name == 'flip') animation.speed = 3
              player.animation.curFrame+=animation.speed
              if(animation.hasLoop && animation.looping){
                player.animation.curFrame %= Math.min(animation.loopRangeEnd, animation.frameCt)
                if(player.animation.curFrame < 1) player.animation.curFrame = Math.max(0, animation.loopRangeStart)
              }else{
                player.animation.curFrame %= animation.frameCt
              }
              animation.frameData[player.animation.curFrame|0].map((v, i) => {
                x.beginPath()
                v.map(q=>{
                  X = q[0]
                  Y = q[1]
                  Z = q[2]
                  R3(0, 0, player.lerpyw)
                  X+=tx
                  Y+=ty
                  Z+=tz
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                })
                stroke(scol, fcol, lineWidth, glowing, overrideGlobalAlpha)
              })
            }
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

          lineFaceI = (X1, Y1, Z1, X2, Y2, Z2, facet, autoFlipNormals=false, showNormals=false) => {
            let X_, Y_, Z_, d, m, l_,K,J,L,p
            let I_=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
            let M=Math, A=M.atan2, H=M.hypot
            let R_ = (Rl,Pt,Yw,m)=>{
              X_=S(p=A(X_,Y_)+Rl)*(d=H(X_,Y_)),Y_=C(p)*d,X_=S(p=A(X_,Z_)+Yw)*(d=H(X_,Z_)),Z_=C(p)*d,Y_=S(p=A(Y_,Z_)+Pt)*(d=H(Y_,Z_)),Z_=C(p)*d
              if(m){ X_+=oX,Y_+=oY,Z_+=oZ }
            }
            let R2_ = (Rl,Pt,Yw,m)=>{
              X_-=oX
              Y_-=oY
              Z_-=oZ
              A=M.atan2
              H=M.hypot
              X_=S(p=A(X_,Z_)+Yw)*(d=H(X_,Z_))
              Z_=C(p)*d
              Y_=S(p=A(Y_,Z_)+Pt)*(d=H(Y_,Z_))
              Z_=C(p)*d
              X_=S(p=A(X_,Y_)+Rl)*(d=H(X_,Y_))
              Y_=C(p)*d
            }
            Q_=()=>[c.width/2+X_/Z_*600, c.height/2+Y_/Z_*600]
            let rotSwitch = m =>{
              switch(m){
                case 0: R_(0,0,Math.PI/2); break
                case 1: R_(0,Math.PI/2,0); break
                case 2: R_(Math.PI/2,0,Math.PI/2); break
              }        
            }
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
            if(showNormals){
              x.beginPath()
              X_ = X1_, Y_ = Y1_, Z_ = Z1_
              R2_(Rl,Pt,Yw,1)
              if(Z_>0) x.lineTo(...Q_())
              X_ = ax + crs[0] * 10
              Y_ = ay + crs[1] * 10
              Z_ = az + crs[2] * 10
              R2_(Rl,Pt,Yw,1)
              if(Z_>0) x.lineTo(...Q_())
              x.lineWidth = 5
              x.strokeStyle='#f008'
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
              //a = [...a, b]
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
            //a = [...a, b]
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

          Cube2 = size => {
            for(CB=[],j=6;j--;){
              for(b=[],i=4;i--;)b=[...b,[(a=[S(p=Math.PI*2/4*i+Math.PI/4),C(p),2**.5/2])[j%3]*(l=j<3?size/1.5:-size/1.5),a[(j+1)%3]*l/1,a[(j+2)%3]*l]]
              if(j!=4)CB=[...CB,b]
            }
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
              x.closePath()
              if(od) x.globalAlpha = .2*oga
              x.strokeStyle = scol
              x.lineWidth = Math.min(100,100*lwo/Z)
              if(od) x.stroke()
              x.lineWidth /= 4
              x.globalAlpha = 1*oga
              x.stroke()
            }
            if(fcol){
              x.globalAlpha = 1*oga
              x.fillStyle = fcol
              x.fill()
            }
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
          subDividedPlatform     = (size, subs, sphereize = 0) => subbed(subs, size, sphereize, Cube2(size))

          Rn = Math.random

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

          G_ = 1e5, iSTc = 3e3
          ST = Array(iSTc).fill().map(v=>{
            X = (Rn()-.5)*G_
            Y = (Rn()-.5)*G_
            Z = (Rn()-.5)*G_
            return [X,Y,Z]
          })

          burst = new Image()
          burst.src = "/games_shared_assets/burst.png"

          levels = new Image()
          levels.src = "/games_shared_assets/spelunk_levels.png"

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
          
          rand = () => {
            return ((randSeed+(randCt++)) ** 3.1/99) % 1
          }

          spawnShape = (X, Y, Z, shapeType, size, subs, convexity) => {
            let base_shape = eval(`${shapeType}(shpls = ${size}, ${subs}, ${convexity})`)
            let maximus = -6e6
            let minimus = 6e6
            base_shape.map(v => {
              v.map(q=>{
                X2 = q[0]
                Y2 = q[1]
                Z2 = q[2]
                d = Math.hypot(q[1], q[2])
                p = Math.atan2(Y2,Z2)// - Math.PI*1.5
                q[1] = S(p) * d
                q[2] = C(p) * d

                X2 = q[0]
                Y2 = q[1]
                Z2 = q[2]
                d = Math.hypot(q[0], q[2])
                p = Math.atan2(X2,Z2)// + Math.atan2(X2,Z2) + Math.PI
                q[0] = S(p) * d
                q[2] = C(p) * d
                d = Math.hypot(q[0], q[1], q[2])
                if(d>maximus) maximus = d
                if(d<minimus) minimus = d
              })
            })
            shapes = [...shapes, [X, Y, Z, base_shape, shapeType, shpls, maximus*1.1, minimus/1.1, JSON.parse(JSON.stringify(base_shape))]]
          }

          genTunnels = (cullTunnelsByShapes=false) => {

            originX = originY = originZ = null
            let skipCapOnPrev = tunnels.length - 1
            let ls = segLen*(tunLen-2)/2
            origins = []
            for(i=tunSections; i--;){
              p_ = Math.PI/tunSections* i + 0 * (i%2 ? .25 : .25)
              a = []
              tx = S(p=p_)*ls
              ty = 0
              tz = C(p)*ls
              tyv=0
              for(let j = 0; j<tunLen;j++){
                tyv += (rand()-.5) * vsquirrel
                if(j==tunLen/2-1|0 && originX == null){
                  originX = tx
                  originY = ty
                  originZ = tz
                }
                a = [...a, [tx, ty, tz]]
                tx -= S(p+=(rand()-.5)*squirreliness) * segLen
                ty -= S(tyv+0*2)*4
                tz -= C(p) * segLen
                tyv /= 1.05
              }
              tunnels = [...tunnels, a]
              p_ += Math.PI/tunSections
              origins = [...origins, ...JSON.parse(JSON.stringify(tunnels))]
            }

            let ret = []
            let caps = []
            tunnels.map((v, i_) => {
              v.map((v2, i2) => {
                if(i2 && i2<v.length-1){
                  tx0 = v[i2+1][0]
                  ty0 = v[i2+1][1]
                  tz0 = v[i2+1][2]
                  tx1 = v2[0]
                  ty1 = v2[1]
                  tz1 = v2[2]
                  tx2 = v[i2-1][0]
                  ty2 = v[i2-1][1]
                  tz2 = v[i2-1][2]
                  
                  /*X = (tx1 + tx2) / 2
                  Y = (ty1 + ty2) / 2
                  Z = (tz1 + tz2) / 2
                  R(Rl,Pt,Yw,1)
                  if(Z>0){
                    x.textAlign = 'center'
                    x.font = (fs=500/Z)+'px Courier Prime'
                    x.fillStyle = '#fff'
                    x.fillText(i2-1,...Q())
                  }*/

                  p1a = Math.atan2(tx1-tx0, tz1-tz0)
                  p2a = -Math.acos((ty1-ty0)/(Math.hypot(tx1-tx0, ty1-ty0, tz1-tz0)+.001)) + Math.PI/2
                  p1b = Math.atan2(tx2-tx1, tz2-tz1)
                  p2b = -Math.acos((ty2-ty1)/(Math.hypot(tx2-tx1, ty2-ty1, tz2-tz1)+.001)) + Math.PI/2

                  /*x.beginPath()
                  X = tx1
                  Y = ty1
                  Z = tz1
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                  X = tx2
                  Y = ty2
                  Z = tz2
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                  stroke('#f004', '', 5, false) */
                
                  if(capTunnels && i_ > skipCapOnPrev && (i2==1 || i2 == v.length-2)) b = []
                  for(i=segSd;i--;){
                    segLs1_ = segLs + (flairTunnelEnds ? (segLs*10 / (1+((v.length-2-i2+10)**5/50000)) + segLs*10 / (1+((8+i2)**5/50000))) : 0)
                    segLs2_ = segLs + (flairTunnelEnds ? (segLs*10 / (1+((v.length-2-i2+9)**5/50000)) + segLs*10 / (1+((9+i2)**5/50000))) : 0)
                    if(capTunnels && i_ > skipCapOnPrev && (i2 == 1 || i2 == v.length-2)){
                      if(i2 ==1){
                        X = S(p=Math.PI*2/segSd*i+Math.PI/segSd) * segLs1_
                        Y = C(p)*segLs1_
                        Z = 0
                        R3(0,p2b,p1b)
                        X += tx2
                        Y += ty2
                        Z += tz2
                      }else{
                        X = S(p=Math.PI*2/segSd*i+Math.PI/segSd) * segLs2_
                        Y = C(p)*segLs2_
                        Z = 0
                        R3(0,p2a,p1a)
                        X += tx1
                        Y += ty1
                        Z += tz1
                      }
                      b = [...b, [X,Y,Z,1]]
                    }
                    a = []
                    x.beginPath()
                    X = S(p=Math.PI*2/segSd*i+Math.PI/segSd) * segLs2_
                    Y = C(p)*segLs2_
                    Z = 0
                    R3(0,p2a,p1a)
                    X += tx1
                    Y += ty1
                    Z += tz1
                    cullCt = 0
                    if(!cull(i_,cullTunnelsByShapes)) cullCt++, a = [...a, [X,Y,Z,0]]
                    R(Rl, Pt, Yw, 1)
                    if(Z>0) x.lineTo(...Q())
                    X = S(p=Math.PI*2/segSd*i+Math.PI/segSd) * segLs1_
                    Y = C(p)*segLs1_
                    Z = 0
                    R3(0,p2b,p1b)
                    X += tx2
                    Y += ty2
                    Z += tz2
                    if(!cull(i_,cullTunnelsByShapes)) cullCt++, a = [...a, [X,Y,Z,0]]
                    R(Rl, Pt, Yw, 1)
                    if(Z>0) x.lineTo(...Q())
                    X = S(p=Math.PI*2/segSd*(i+1)+Math.PI/segSd) * segLs1_
                    Y = C(p)*segLs1_
                    Z = 0
                    R3(0,p2b,p1b)
                    X += tx2
                    Y += ty2
                    Z += tz2
                    if(!cull(i_,cullTunnelsByShapes)) cullCt++, a = [...a, [X,Y,Z,0]]
                    R(Rl, Pt, Yw, 1)
                    if(Z>0) x.lineTo(...Q())
                    X = S(p=Math.PI*2/segSd*(i+1)+Math.PI/segSd) * segLs2_
                    Y = C(p)*segLs2_
                    Z = 0
                    R3(0,p2a,p1a)
                    X += tx1
                    Y += ty1
                    Z += tz1
                    if(!cull(i_,cullTunnelsByShapes)) cullCt++, a = [...a, [X,Y,Z,0]]
                    if(cullCt>2){
                      ret = [...ret, [a, i_]]
                    }
                  }
                  if(capTunnels && i_ > skipCapOnPrev && (i2==1 || i2 == v.length-2)){
                    caps = [...caps, [b, i_]]
                  }
                }
              })
            })
            if(capTunnels) ret = [...ret, ...caps]
            
            shapes.map((shp, idx) => {
              shp[8].map(v=>{
                v.map(q=>{
                  X = shp[0] + q[0]
                  Y = shp[1] + q[1]
                  Z = shp[2] + q[2]
                  cullShape(X,Y,Z,idx)
                })
              })
            })
            
            shapes = shapes.map(shp => {
              shp[3] = shp[3].map(v=>{
                return typeof v != 'undefined' && v.filter(q=>!q[3])
              })
              shp[3] = shp[3].filter(v => v.length>2)
              return shp
            })
            
            return ret
          }

          genLoopTunnel = (tx, ty, tz, rad, squirreliness=0, vsquirrel=0, capTunnels=false) => {
            tTunnels = []
            let tunSections = 1
            let ls = rad
            tx_ = tx
            ty_ = ty
            tz_ = tz
            for(i=tunSections; i--;){
              p_ = 0
              a = []
              tx = tx_ + S(p=p_)*ls
              ty = ty_
              tz = tz_ + C(p)*ls
              tyv=0
              let sd = Math.PI*2*rad/segLen-1|0
              for(let j = 0; j<sd+3;j++){
                tyv += (rand()-.5) * vsquirrel
                if(j) a = [...a, [tx, ty, tz]]
                tx = S(p=Math.PI*2/sd*j)*ls
                ty -= S(tyv+0*2)*4
                tz = C(p)*ls
                tyv /= 1.05
              }
              tTunnels = [...tTunnels, a]
              p_ += Math.PI/tunSections
            }
            
            return tTunnels
          }
          
          addPlayers = playerData => {
            playerData.score = 0
            Players = [...Players, {playerData}]
            PlayerCount++
            PlayerInit(Players.length-1, playerData.id)
          }

          spawnFlashNotice = (text, col)=>{
            flashNotices = [...flashNotices, [text, col, 1]]
            flashNotices = flashNotices.filter((v,i)=>i>flashNotices.length-3)
          }

          spawnCam = player => {
            X = player.X
            Z = player.Z - camDist
            Y = player.Y
            return {
              X, Y, Z
            }
          }
          
          spawnPlayer = (X=0, Y=0, Z=0, uid=0, idx) =>{
            let p_ = Math.atan2(X,Z)
            let player = {
              X,
              Y,
              Z,
              id:                +uid,
              vx:                0,
              vy:                0,
              vz:                0,
              yw:                p_+Math.PI,
              pt:                0,
              rl:                0,
              idx:               +idx,
              rlv:               0,
              ptv:               0,
              ywv:               0,
              keys:              Array(256).fill(0),
              name:              users.filter(user => +user.id == +uid)[0].name,
              lerpX:             X,
              lerpY:             Y,
              lerpZ:             Z,
              lerprl:            0,
              lerppt:            0,
              lerpyw:            0,
              score:             0,
              speed:             0,
              health:            1,
              mbutton:           Array(3).fill(false),
              buttons:           [],
              jumping:           false,
              grounded:          false,
              shooting:          false,
              keyTimers:         Array(256).fill(0),
              animation:         {
                                   curFrame: 0,
                                 },
              distanceTravelled: 0,
            }
            player.cam = spawnCam(player)
            return player
          }
          
          masterInit = () => {
            playerSize_         = 50
            mv                  = 1.5
            rv                  = .033
            hov                 = false
            cams                = []
            grav                = .5
            boost               = .1
            level               = -1
            animX               = 0
            animY               = -playerSize_/1.25
            animZ               = 0//-playerSize/2.5
            animRl              = 0
            animPt              = 0//Math.PI/2
            animYw              = Math.PI/2 + Math.PI
            scores              = []
            sparks              = []
            randCt              = 0
            accelr              = 1
            camDist             = .01
            bullets             = []
            flashes             = []
            hotkeyX             = 0
            buttons             = []
            camMode             = 1
            players             = []
            iSparkv             = 5
            mbutton             = Array(3).fill(false),
            animSize            = 50/4.5
            newLevel            = -1
            showInfo            = true
            randSeed            = 1e4 + Math.random()* 1e4 |0
            maxSpeed            = 24
            iBulletv            = 32
            topoZoom            = 5e3
            showTopo            = true
            recRandCt           = -1
            mousedown           = false
            splosions           = []
            showstars           = true
            iSplosionv          = 50
            lerpFactor          = 8
            jumpHeight          = 20
            maxCamDist          = 100
            PlayerCount         = 0
            iBulletFreq         = 3
            recRandSeed         = -1
            falloffDist         = 1500
            camSelected         = 0
            bulletDamage        = .2
            bigSplosions        = []
            crosshairSel        = 2
            flashNotices        = []
            showCrosshair       = true
            buttonsLoaded       = false
            levelSelected       = false
            movingTunnels       = false
            pointerLocked       = false
            iSparkReflectv      = iSparkv / 6
            camFollowSpeed      = 10
            accellr = accelm    = 2
            flymodeCollisons    = false
            keyTimerInterval    = 1/60*5 // .25 sec
            sendDeathSplosions  = []
            player_animations   = {
              walk: loadAnimation('walking_male',   animSize, animX,animY,animZ,animRl,animPt,animYw, 0),
              flip: loadAnimation('back_flip_male', animSize, animX,animY,animZ,animRl,animPt,animYw, 0),
            }
            hotkeysModalVisible = false

            Players                    = []
          }
          await masterInit()
          
          
          PlayerInit = (idx, id) => { // called initially & when a player dies
            if(typeof origins != 'undefined'){
              l1 = Rn()*origins.length|0
              l2 = origins[l1].length*Rn()|0
              origx = origins[l1][l2][0]+2.5
              origy = origins[l1][l2][1]-2
              origz = origins[l1][l2][2]+2.5
            }else{
              origx = origy = origz = 0
            }
            let newPlayer = spawnPlayer(origx, origy, origz, id, players.length)
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

          cull = (i_, cullTunnelsByShapes) => {
            let ret = false
            tunnels.map((n, j_) => {
              if(!ret){
                n.map((q, j) => {
                  if(!ret && j && j_!=i_){
                    tx1_ = q[0]
                    ty1_ = q[1]
                    tz1_ = q[2]
                    tx2_ = n[j-1][0]
                    ty2_ = n[j-1][1]
                    tz2_ = n[j-1][2]
                    d1 = Math.hypot((tx1_+tx2_)/2-X,(ty1_+ty2_)/2-Y,(tz1_+tz2_)/2-Z)
                    if(d1<segLs/(flairTunnelEnds && level>1 ?.75:1.2)) ret = true
                  }
                })
              }
            })
            shapes.map((shp, idx) => {
              d = Math.hypot(X-shp[0], Y-shp[1], Z-shp[2])
              if(d < shp[6] && d > shp[7]){
                shp[8].map((v, i) => {
                  v.map((q, j)=>{
                    if(!shapes[idx][8][i][j][3]){
                      ax = q[0] + shp[0]
                      ay = q[1] + shp[1]
                      az = q[2] + shp[2]
                      d1 = Math.hypot(ax-X, ay-Y, az-Z)
                      if(cullTunnelsByShapes) {
                        if(d1<segLs*1.25) ret = true
                      }
                      if(typeof shapes[idx][3][i] != 'undefined' && d1<segLs*1.25) {
                        shapes[idx][3][i][j][3] = true
                      }
                    }
                  })
                })
              }
            })
            return ret
          }
          
          cullShape = (X, Y, Z, idx) => {
            let ret = false
            shapes.map((shp, idx2) => {
              if(idx != idx2){
                d = Math.hypot(X-shp[0], Y-shp[1], Z-shp[2])
                if(d < shp[6] && d > shp[7]){
                  shp[8].map((v, i) => {
                    v.map((q, j) => {
                      ax = -q[0] + shp[0] //- q[0]
                      ay = -q[1] + shp[1] //- q[1]
                      az = -q[2] + shp[2] //- q[2]
                      d1 = Math.hypot(ax-X, ay-Y, az-Z)
                      if(d1<segLs/1.5) {
                        ret = true
                        shapes[idx][3][i][j][3] = true
                        //shapes[idx2][3][i][j][3] = true
                      }
                    })
                  })
                }
              }
            })
            return ret
          }
          
          loadLevel = level => {
            /*
            do{
              iPlayersc = prompt('how many AI players [0-9]?')
              if(iPlayersc == null) {
                tsel = -1
                mousedown = false
                return false
              }else{
                tsel = -1
                mousedown = false
              }
            }while((+iPlayersc)<0 || (+iPlayersc) > 9);
            iPlayersc++
            */
            tunnels       = []
            switch(level){
              case 0: 
                shapes        = []
                spawnShape (0, 0, -800, 'subDividedCube', 500, 3, .5)
                spawnShape (-800, 0, 0, 'subDividedCube', 500, 3, .5)
                spawnShape (0, 0, 800, 'subDividedCube', 500, 3, .5)
                spawnShape (800, 0, 0, 'subDividedCube', 500, 3, .5)
                flairTunnelEnds = true
                capTunnels    = false
                randCt        = 0
                colTunMargin  = .66
                colShapeMargin= 1
                segLs         = 40
                segSd         = 6
                tunSections   = 2
                segLen        = 20
                tunLen        = 56
                topoZoom      = 5e3
                squirreliness = 0
                vsquirrel     = 0
                playerSize    = 50
                topoZoom      = 4500
                tunnels = genTunnels()
                break
              case 1: 
                shapes        = []
                spawnShape (0, -200, 0, 'subDividedCube', 1000, 4, 1)
                flairTunnelEnds = true
                capTunnels    = false
                randCt        = 0
                topoZoom      = 1e4
                colTunMargin  = .66
                colShapeMargin= .75
                segLs         = 80
                segSd         = 5
                tunSections   = 3
                segLen        = 60
                tunLen        = 45
                playerSize    = 50
                squirreliness = .55
                vsquirrel     = 5
                topoZoom      = 7000
                tunnels = genTunnels(true)
                originX = originZ = 10
                break
              case 2: 
                shapes        = []
                spawnShape (0, 0, -500, 'subDividedCube', 500, 3, .5)
                spawnShape (0, 0, 500, 'subDividedCube', 500, 3, .5)
                //spawnShape (0, 0, -800, 'subDividedCube', 500, 3, .5)
                //spawnShape (-800, 0, 0, 'subDividedCube', 500, 3, .5)
                //spawnShape (0, 0, 800, 'subDividedCube', 500, 3, .5)
                //spawnShape (800, 0, 0, 'subDividedCube', 500, 3, .5)
                flairTunnelEnds = false
                capTunnels    = true
                randCt        = 0
                colTunMargin  = .66
                colShapeMargin= .8
                topoZoom      = 5e3
                segLs         = 60
                segSd         = 8
                tunSections   = 2
                segLen        = 60
                tunLen        = 50
                playerSize    = 40
                squirreliness = 0
                vsquirrel     = 0
                topoZoom      = 6e3
                tunnels = genLoopTunnel(0,0,0,900,0,0,false) /* pos, rad */
                tunnels = genTunnels(true)
                break
              case 3: 
                shapes        = []
                spawnShape (0, 0, 0, 'subDividedPlatform', 1800, 4, .33)
                shapes = shapes.map(v=>{
                  maxd=-6e6
                  v[3].map(v=>{
                    v.map(q=>{
                      if(q[1]>maxd)maxd=q[1]
                      q[1]-=880
                    })
                  })
                  v[3] = v[3].filter(v=>{
                    return !v.filter(q=>q[1]<maxd-1200).length
                  })
                  return v
                })
                flairTunnelEnds = false
                capTunnels    = false
                randCt        = 0
                colTunMargin  = .7
                colShapeMargin= 1.5
                segLs         = 60
                segSd         = 6
                tunSections   = 3
                segLen        = 60
                tunLen        = 20
                playerSize    = 50
                squirreliness = 0
                topoZoom      = 5e3
                vsquirrel     = 0
                topoZoom      = 6e3
                tunnels = genLoopTunnel(0,0,0,350,0,0,false)
                tunnels = genTunnels(true)
                originX = originZ = 10
                break
              case 4:
                shapes        = []
                spawnShape (0, 0, 0, 'subDividedCube', 1500, 4, -.66)
                //spawnShape (0, 0, -800, 'subDividedCube', 500, 3, .5)
                //spawnShape (-800, 0, 0, 'subDividedCube', 500, 3, .5)
                //spawnShape (0, 0, 800, 'subDividedCube', 500, 3, .5)
                //spawnShape (800, 0, 0, 'subDividedCube', 500, 3, .5)
                flairTunnelEnds = true
                capTunnels    = false
                randCt        = 0
                tunnels       = []
                colTunMargin  = .9
                colShapeMargin= 1
                segLs         = 50
                segSd         = 6
                tunSections   = 2
                playerSize    = 40
                topoZoom      = 5e3
                segLen        = 75
                tunLen        = 50
                squirreliness = .25
                vsquirrel     = 2
                topoZoom      = 7500
                movingTunnels = false
                tunnels       = []
                tunnels = genTunnels(true)
                break
              case 5: 
                shapes        = []
                for(let m=5;m--;) spawnShape (0, 0, -600*2.5+(m+.5)*600, 'subDividedCube', 550, 3, .5)
                /*for(let m=3;m--;) spawnShape (0, 0, -600*1.5+(m+.5)*600, 'subDividedCube', 550, 3, .5)
                for(let m=3;m--;) {
                  if(m!=1) spawnShape (-600*1.5+(m+.5)*600, 0, 0, 'subDividedCube', 550, 3, .5)
                }*/
                flairTunnelEnds = false
                capTunnels    = false
                randCt        = 0
                colTunMargin  = .66
                colShapeMargin= .75
                segLs         = 70
                segSd         = 6
                playerSize    = 50
                tunSections   = 1
                topoZoom      = 5e3
                segLen        = 70
                tunLen        = 20
                squirreliness = 0
                vsquirrel     = 0
                topoZoom      = 6e3
                tunnels       = []
                //tunnels = genLoopTunnel(0,0,0,1e3,0,0,false) /* pos, rad */
                tunnels = genTunnels(true)
                originX = originZ = 10
                break
              default:
                genTunnels()
                break
            }
            
            players.map(player => {
              respawnPlayer(player)
            })
            
            return true
          }

          document.onpointerlockchange = e => {
            pointerLocked = document.pointerLockElement == c
          }

          mx=my=0
          c.onmousemove = e => {
            if(players.length){
              curPlayer = players[camSelected]
              hov = false
              if(pointerLocked){
                if(camSelected == 0){
                  curPlayer.ywv += rv * accelr * e.movementX/9
                  curPlayer.ptv += rv * 3 * e.movementY/9
                }
              }else{
                rect = c.getBoundingClientRect()
                mx = (e.pageX-rect.x)/c.clientWidth*c.width
                my = (e.pageY-rect.y)/c.clientHeight*c.height
                buttons.map(button=>{
                  if(button.hover){
                    hov = true
                  }
                })
              }
            }else{
              rect = c.getBoundingClientRect()
              mx = (e.pageX-rect.x)/c.clientWidth*c.width
              my = (e.pageY-rect.y)/c.clientHeight*c.height
              buttons.map(button=>{
                if(button.hover){
                  hov = true
                }
              })
            }
            if(showInfo){
              let ofx = hotkeysModalVisible ? 450 : 0
              X1 = ofx-450
              Y1 = 5
              X2 = X1 + 500
              Y2 = Y1 + 27*14.5
              if(mx >= X1 && mx <= X2 && my >= Y1 && my <= Y2){
                c.style.cursor = 'pointer'
              }else{
                c.style.cursor = 'unset'
              }
            }
          }
          
          c.onmouseup = e => {
            e.preventDefault()
            e.stopPropagation()
            if(e.button == 0) mousedown = false
            if(players.length){
              curPlayer = players[camSelected]
              c.focus()
              curPlayer.mbutton[e.button] = false
            }
          }
         
          c.onmousedown = e => {
            e.preventDefault()
            e.stopPropagation()
            if(e.button == 0 && !levelSelected) mousedown = true
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
                c.requestPointerLock()
              }
            }
            if(showInfo && e.button == 0){
              let ofx = hotkeysModalVisible ? 450 : 0
              X1 = ofx-450
              Y1 = 5
              X2 = X1 + 500
              Y2 = Y1 + 27*14.5
              if(mx >= X1 && mx <= X2 && my >= Y1 && my <= Y2){
                hotkeysModalVisible = !hotkeysModalVisible
              }
            }
          }
          c.focus()

          c.onkeydown = e => {
            e.preventDefault()
            e.stopPropagation()
            if(players.length) players[0].keys[e.keyCode] = true
          }

          c.onkeyup = e => {
            e.preventDefault()
            e.stopPropagation()
            if(players.length) players[0].keys[e.keyCode] = false
          }

          left = player =>{
            if(player.keys[18]){
              if(player.grounded && !player.flymode){
                vx  = S(-player.yw+Math.PI/2) * C(-player.rl) * mv * accelm
                vy  = -S(-player.rl) * mv * accelm
                vz  = C(-player.yw+Math.PI/2) * C(-player.rl) * mv * accelm
                player.vx -= vx
                //if(flymode) oYv += vy
                player.vz -= vz
              }
            }else{
              player.ywv-=rv * accelr
            }
          }
          up = player =>{
            player.ptv-=rv * accelr
          }
          right = player =>{
            if(player.keys[18]){
              if(player.grounded || player.flymode){
                vx  = S(-player.yw+Math.PI/2) * C(-player.rl) * mv * accelm 
                vy  = -S(-player.rl) * mv * accelm
                vz  = C(-player.yw+Math.PI/2) * C(-player.rl) * mv * accelm
                player.vx += vx
                //if(flymode) oYv -= vy
                player.vz += vz
              }
            }else{
              player.ywv+=rv * accelr
            }
          }
          down = player =>{
            player.ptv+=rv * accelr
          }
          akey = player =>{
            if(player.flymode){
              vx  = -S(player.yw+Math.PI/2) * C(player.rl) * mv * accelm
              vy  = S(player.rl) * mv * accelm
              vz  = -C(player.yw+Math.PI/2) * C(player.rl) * mv * accelm
              player.vx += vx
              player.vy += vy
              player.vz += vz
            }else if(player.grounded){
              vx  = -S(player.yw+Math.PI/2) * mv * accelm
              vy  = 0
              vz  = -C(player.yw+Math.PI/2) * mv * accelm
              player.vx += vx
              player.vz += vz
            }
          }
          wkey = player =>{
            if(player.flymode){
              vx  = -S(player.yw) * C(player.pt) * mv * accelm
              vy  = -S(player.pt) * mv * accelm
              vz  = -C(player.yw) * C(player.pt) * mv * accelm
              player.vx -= vx
              player.vy -= vy
              player.vz -= vz
            }else if(player.grounded){
              vx  = -S(player.yw) * mv * accelm
              vy  = 0
              vz  = -C(player.yw) * mv * accelm
              player.vx -= vx
              player.vz -= vz
            }
          }
          dkey = player =>{
            if(player.flymode){
              vx  = -S(player.yw+Math.PI/2) * C(player.rl) * mv * accelm
              vy  = S(player.rl) * mv * accelm
              vz  = -C(player.yw+Math.PI/2) * C(player.rl) * mv * accelm
              player.vx -= vx
              player.vy -= vy
              player.vz -= vz
            }else if(player.grounded){
              vx  = -S(player.yw+Math.PI/2) * mv * accelm
              vy  = 0
              vz  = -C(player.yw+Math.PI/2) * mv * accelm
              player.vx -= vx
              player.vz -= vz
            }
          }
          skey = player =>{
            if(player.flymode){
              vx  = S(player.yw) * C(player.pt) * mv * accelm
              vy  = S(player.pt) * mv * accelm
              vz  = C(player.yw) * C(player.pt) * mv * accelm
              player.vx -= vx
              player.vy -= vy
              player.vz -= vz
            }else if(player.grounded){
              vx  = S(player.yw) * mv * accelm
              vy  = 0
              vz  = C(player.yw) * mv * accelm
              player.vx -= vx
              player.vz -= vz
            }
          }
          pgup = player => {
            if(player.flymode){
              vx  = -S(player.yw) * C(player.pt+Math.PI/2) * mv * accelm
              vy  = -S(player.pt+Math.PI/2) * mv * accelm
              vz  = -C(player.yw) * C(player.pt+Math.PI/2) * mv * accelm
              player.vx += vx
              player.vy += vy
              player.vz += vz
            }      
          }
          pgdn = player => {
            if(player.flymode){
              vx  = S(player.yw) * C(player.pt+Math.PI/2) * mv * accelm
              vy  = S(player.pt+Math.PI/2) * mv * accelm
              vz  = C(player.yw) * C(player.pt+Math.PI/2) * mv * accelm
              player.vx += vx
              player.vy += vy
              player.vz += vz
            }      
          }
          ctrl = player => {
            player.shooting = true
          }

          leftButton = player =>{
            if(c.style.cursor == 'pointer') return
            if(!player.idx){
              if(player.flymode){
                vx  = -S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
                vy  = -S(-Pt+Math.PI/2) * mv * accelm
                vz  = -C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
                player.vx -= vx
                player.vy -= vy
                player.vz -= vz
              }else{
                player.shooting = true
              }
            }else{
              spawnFlashNotice(`this is ${players.filter((v,idx)=>idx==camSelected)[0].name}'s camera!`, '#8408')
              spawnFlashNotice(`hit '1' for your camera!`, '#8408')
            }
          }

          rightButton = spacebar = player =>{
            if(!player.idx){
              if(player.flymode){
                vx  = -S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
                vy  = -S(-Pt+Math.PI/2) * mv * accelm
                vz  = -C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
                player.vx += vx
                player.vy += vy
                player.vz += vz
              }else if(player.grounded && player.keyTimers[32] < t){
                player.jumping = true
                player.keyTimers[32] = t+.25
                //player.vx*=4
                //player.vz*=4
                player.vy = -jumpHeight
              }
            }else{
              spawnFlashNotice(`this is ${players.filter((v,idx)=>idx==camSelected)[0].name}'s camera!`, '#8408')
              spawnFlashNotice(`hit '1' for your camera!`, '#8408')
            }
          }

          doKeys = (player, idx) => {
            accelr = accelm = 1
            if(!idx) {
              if(player.keys[16]){
                accelm = 2.125 * (player.flymode ? 2 : 1)
                accelr = 1.5
              }
              player.shooting = false
            }
            player.keys.map((v,i) => {
              if(v){
                if(!idx){
                  switch(i){
                    case 49: if(players.length)
                      camSelected = 0;
                      camSelHasChanged=true
                    break
                    case  67:
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval*2
                        if(showCrosshair && crosshairSel<crosshairImgs.length-1){
                          crosshairSel++
                        }else{
                          crosshairSel=0
                          showCrosshair = !showCrosshair
                        }
                      }
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
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval * 4
                        hotkeysModalVisible = !hotkeysModalVisible
                      }
                    break
                    case  84:
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval
                        showDash = !showDash
                      }
                      break
                    case  67:
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval
                        if(showCrosshair && crosshairSel<crosshairImgs.length-1){
                          crosshairSel++
                        }else{
                          crosshairSel=0
                          showCrosshair = !showCrosshair
                        }
                      }
                      break
                    case 77:
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval
                        //camMode++
                        //camMode%=2
                        toggleMenu(false)
                      }
                      break
                    case 16:
                      if(player.grounded){
                        player.vx += S(player.yw) * .25 * boost * (player.forward ? 1 : -1)
                        player.vz += C(player.yw) * .25 * boost * (player.forward ? 1 : -1)
                      }
                      break
                    case 70:
                      if(player.keyTimers[i] < t){
                        player.keyTimers[i] = t+keyTimerInterval*8
                        player.flymode = !player.flymode
                        if(player.flymode){
                          spawnFlashNotice('flymode enabled', '#fff8')
                        }else{
                          spawnFlashNotice('flymode off', '#fff8')
                        }
                      }
                      break
                    case 65: akey(player); break
                    case 87: wkey(player); break
                    case 68: dkey(player); break
                    case 83: skey(player); break
                    case 32: spacebar(player); break
                    case 37: left(player); break
                    case 38: up(player); break
                    case 39: right(player); break
                    case 40: down(player); break
                    case 17: ctrl(player); break
                    case 33: pgup(player); break
                    case 34: pgdn(player); break
                  }
                }else{
                  spawnFlashNotice(`this is ${players.filter((v, idx)=> idx==camSelected)[0].name}'s camera!`, '#8408')
                  spawnFlashNotice(`hit '1' for your camera!`, '#8408')
                }
              }
            })
          }

          window.onload = () => {
            c.focus()
          }
          
          createLevel = lvl => {
            newLevel = level = lvl
            if(loadLevel(lvl)){
              levelSelected = true
              setTimeout(()=>{
                newLevel = -1
              }, 8000)
              /*
              players = Array(iPlayersc).fill().map((v, i) => {
                l1 = Rn()*origins.length|0
                l2 = origins[l1].length*Rn()|0
                X = origins[l1][l2][0]+2.5
                Y = origins[l1][l2][1]-2
                Z = origins[l1][l2][2]+2.5
                newPlayer = spawnPlayer(X,Y,Z,i) 
                newPlayer.flymode = false
                return newPlayer
              })
              */
            }else{
              tsel = -1
            }
          }
          
          //createLevel(level)

          collisions = (X1,Y1,Z1,X2,Y2,Z2) => {
            let mind1 = 6e6
            let mind2 = 6e6
            let ret = false
            let l
            ax = (X1+X2) / 2
            ay = (Y1+Y2) / 2
            az = (Z1+Z2) / 2
            tunnels.map((v, i) => {
              ax = (v[0][0][0] + v[0][2][0])/2
              ay = (v[0][0][1] + v[0][2][1])/2
              az = (v[0][0][2] + v[0][2][2])/2
              if((d=Math.hypot(X2-ax,Y2-ay,Z2-az))<segLs*1.25 * colTunMargin * (v[0][0][3]?2:1)){
                if((l = lineFaceI(X1, Y1, Z1, X2, Y2, Z2, v[0], true, false))){
                  if(d<mind1) {
                    mind1 = d
                    ret = l
                  }
                }
              }
            })

            if(!ret) shapes.map((shp, i) => {
              tx = shp[0]
              ty = shp[1]
              tz = shp[2]
              shp[3].map(v => {
                ax = tx + (v[0][0] + v[2][0])/2
                ay = ty + (v[0][1] + v[2][1])/2
                az = tz + (v[0][2] + v[2][2])/2
                if((d=Math.hypot(X2-ax,Y2-ay,Z2-az))<segLs*1.35 * colShapeMargin){
                  a = []
                  v.map(q=> a = [...a,[ q[0]+tx, q[1]+ty, q[2]+tz]])
                  if((l = lineFaceI(X1, Y1, Z1, X2, Y2, Z2, a, true, false))){
                    if(d<mind2) {
                      mind2 = d
                      ret = l
                    }
                  }
                }
              })
            })
            return ret
          }
          
          playerInit = (idx=0) => {
            X = originX
            Y = originY-playerSize/2
            Z = originZ
            players[idx] = spawnPlayer(X,Y,Z,idx) 
            players[idx].flymode = false
          }
          
          spawnFlash = (X, Y, Z) => {
            flashes = [...flashes, [X,Y,Z,1]]
          }

          spawnSplosion = (X, Y, Z) => {
            for(let m = 50; m--;){
              v = (.1+(Rn()**2*.9))*iSplosionv
              vx = S(p1=Math.PI*2*Rn())*S(p2=Rn()<.5?Math.PI/2*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5) * v
              vy = C(p2) * v
              vz = C(p1) * S(p2) * v
              splosions = [...splosions, [X,Y,Z,vx,vy,vz,1]]
            }
          }

          spawnBigSplosion = (X, Y, Z) => {
            for(let m = 150; m--;){
              v = (.1+(Rn()**2*.9))*iSplosionv*2
              vx = S(p1=Math.PI*2*Rn())*S(p2=Rn()<.5?Math.PI/2*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5) * v
              vy = C(p2) * v
              vz = C(p1) * S(p2) * v
              bigSplosions = [...bigSplosions, [X,Y,Z,vx,vy,vz,1]]
            }
          }

          spawnSparks = (X, Y, Z) => {
            for(let m = 10; m--;){
              v = Rn()**.5*iSparkv
              vx = S(p1=Math.PI*2*Rn())*S(p2=Rn()<.5?Math.PI/2*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5) * v
              vy = C(p2) * v
              vz = C(p1) * S(p2) * v
              sparks = [...sparks, [X,Y,Z,vx,vy,vz,1]]
            }
          }

          spawnBullet = player => {
            
            d = Math.hypot(player.vx, player.vy, player.vz)
            X = 0
            Y = 0
            Z = iBulletv + d
            R3(player.lerprl, player.lerppt, player.lerpyw)
            vx = X
            vy = Y
            vz = Z

            X = 0
            Y = 0
            Z = 1
            R3(player.rl, player.pt, player.yw)
            if(player.idx){
              X += player.lerpX
              Y += player.lerpY
              Z += player.lerpZ
            }else{
              X += player.X
              Y += player.Y
              Z += player.Z
            }
            
            bullets = [...bullets, [X, Y, Z, vx, vy, vz, 1, player.id]]
          }
          
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
          
          drawPlayer = player => {
            let w, h, fs
            X = tx = player.lerpX
            Y = ty = player.lerpY
            Z = tz = player.lerpZ
            vx = player.vx
            vy = player.vy
            vz = player.vz
            
            //player.yw = Math.atan2(player.vx,player.vz)
            
            rl = player.lerprl
            pt = 0//player.lerppt
            yw = player.lerpyw
            
            R(Rl,Pt,Yw,1)
            if(Z>0){
              s = Math.min(1e4,1e4/Z)
              l = Q()
              //x.drawImage(burst, l[0]-s/2,l[1]-s/2,s,s)
              x.font = (fs=Math.max(1e4/Z,16))+'px Courier Prime'
              x.fillStyle = '#fff'
              x.textAlign = 'center'
              x.fillText(player.name, l[0], l[1]-fs*5)
              x.fillText(`score ${player.score}`, l[0], l[1]-fs*2.5)
              x.strokeStyle = '#fa4'
              x.lineWidth = Math.min(10, Math.max(2, fs/10))
              w = 6
              h = 1
              x.strokeRect(l[0]-fs*w/2,l[1]-fs*h/2-fs*4,fs*w,fs*h)
              x.fillStyle = '#0f4'
              x.fillRect(l[0]-fs*w/2,l[1]-fs*h/2-fs*4,fs*w*player.health,fs*h)
            }
            
            ty+=playerSize
            jumpanim = player.jumping&&!player.grounded
            drawAnimation(jumpanim ? player_animations.flip:player_animations.walk, scol='', fcol='#fff8', lineWidth=2, glowing=true, overrideGlobalAlpha=1, player)
          }
          
          respawnPlayer = player => {
            l1 = Rn()*origins.length|0
            l2 = origins[l1].length*Rn()|0
            origx = origins[l1][l2][0]+2.5
            origy = origins[l1][l2][1]-2
            origz = origins[l1][l2][2]+2.5
            player.X = player.lerpX = origx //originX
            player.Y = player.lerpY = origy -playerSize/2 //originY-playerSize/2
            player.Z = player.lerpZ = origz //originZ
            player.vx = player.vy = player.vz = 0
            player.flymode = false
            player.vx =                 0
            player.vy =                 0
            player.vz =                 0
            player.yw =                 p_+Math.PI
            player.pt =                 0
            player.rl =                 0
            player.rlv =                0
            player.ptv =                0
            player.ywv =                0
            player.health =             1
            player.shooting =           false
          }
          
          renderButton = (text, X, Y, tooltip = '', callback='', typ='rectangle', col1='#0ff8', col2='#2088', fs=36) => {
            render = (text == '🔗' && showInfo) ||
                     (text == 'EXIT LEVEL' && showInfo) ||
                     (showInfo && tooltip == '  hide menu') || (tooltip == '  show menu' && !showInfo)
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

          toggleMenu = (releasePointerLock=true) =>{
            showInfo = !showInfo
            if(showInfo) {
              if(releasePointerLock && document.pointerLockElement == c) document.exitPointerLock()
            } else {
              if(document.pointerLockElement != c) c.requestPointerLock()
            }
          }
          
          lineOfSight = (playera, playerb) => {
            X1_ = playera.X
            Y1_ = playera.Y
            Z1_ = playera.Z
            X2_ = playerb.X
            Y2_ = playerb.Y
            Z2_ = playerb.Z
            d = Math.hypot(X2_-X1_, Y2_-Y1_, Z2_-Z1_)
            stps = d/20|0
            let ret = false
            let l
            for(let j = stps+1; j--&&!ret;){
              let mind1 = 6e6
              let mind2 = 6e6
              X1 = X1_+(X2_-X1)/stps*j
              Y1 = Y1_+(Y2_-Y1)/stps*j
              Z1 = Z1_+(Z2_-Z1)/stps*j
              X2 = X1_+(X2_-X1)/stps*(j+1)
              Y2 = Y1_+(Y2_-Y1)/stps*(j+1)
              Z2 = Z1_+(Z2_-Z1)/stps*(j+1)
              ax = (X1+X2) / 2
              ay = (Y1+Y2) / 2
              az = (Z1+Z2) / 2
              if(!ret) tunnels.map((v, i) => {
                ax = (v[0][0][0] + v[0][2][0])/2
                ay = (v[0][0][1] + v[0][2][1])/2
                az = (v[0][0][2] + v[0][2][2])/2
                if((d=Math.hypot(X2-ax,Y2-ay,Z2-az))<segLs*1 * colTunMargin * (v[0][0][3]?2:1)){
                  if((l = lineFaceI(X1, Y1, Z1, X2, Y2, Z2, v[0], true, false))){
                    if(d<mind1) {
                      mind1 = d
                      ret = l
                    }
                  }
                }
              })

              if(!ret) shapes.map((shp, i) => {
                tx = shp[0]
                ty = shp[1]
                tz = shp[2]
                shp[3].map(v => {
                  ax = tx + (v[0][0] + v[2][0])/2
                  ay = ty + (v[0][1] + v[2][1])/2
                  az = tz + (v[0][2] + v[2][2])/2
                  if((d=Math.hypot(X2-ax,Y2-ay,Z2-az))<segLs*1.125 * colShapeMargin){
                    a = []
                    v.map(q=> a = [...a,[ q[0]+tx, q[1]+ty, q[2]+tz]])
                    if((l = lineFaceI(X1, Y1, Z1, X2, Y2, Z2, a, true, false))){
                      if(d<mind2) {
                        mind2 = d
                        ret = l
                      }
                    }
                  }
                })
              })
            }
            return !ret
          }
          
          levelMenu = () => {
            levelSelected = false
          }
        }

        if(movingTunnels) {
          tunnels       = []
          tunnels = genTunnels()
        }


        x.globalAlpha = 1
        x.fillStyle='#0006'
        x.fillRect(0,0,c.width,c.height)
        x.lineJoin = x.lineCap = 'roud'

        if(levelSelected && typeof players != 'undefined' && players.length){

          curPlayer = players[camSelected]
          
          switch(camMode){
            case 0:
              d = Math.hypot(curPlayer.vx, curPlayer.vz)
              cd = Math.min(maxCamDist, camDist + d/2)
              dx = curPlayer.cam.X - curPlayer.X
              dy = curPlayer.cam.Y - curPlayer.Y
              dz = curPlayer.cam.Z - curPlayer.Z
              d = Math.hypot(dx,dy,dz)
              nx = S(t/4)*cd
              nz = C(t/4)*cd
              X = dx/d*cd-nx + curPlayer.X
              Z = dz/d*cd-nz + curPlayer.Z
              Y = Math.min(100, dy/d*cd-cd/2  + curPlayer.Y)
              tgtx = X
              tgty = Y
              tgtz = Z
              curPlayer.cam.X -= (curPlayer.cam.X - tgtx)/camFollowSpeed
              curPlayer.cam.Y -= (curPlayer.cam.Y - tgty)/camFollowSpeed
              curPlayer.cam.Z -= (curPlayer.cam.Z - tgtz)/camFollowSpeed
              oX = curPlayer.cam.lerpX
              oZ = curPlayer.cam.lerpZ
              oY = curPlayer.cam.lerpY
              Pt = -Math.acos((oY-curPlayer.Y) / (Math.hypot(oX-curPlayer.X,oY-curPlayer.Y,oZ-curPlayer.Z)+.001))+Math.PI/2
              Yw = -Math.atan2(curPlayer.X-oX,curPlayer.Z-oZ)
              Rl = 0
              break
            case 1:
              X = 0
              Y = 0
              Z = 25
              if(camSelected){
                R3(0,curPlayer.lerppt,curPlayer.lerpyw)
                X_ = X
                Y_ = -Y
                Z_ = Z
                oX = curPlayer.lerpX// += (curPlayer.X - curPlayer.lerpX) / lerpFactor
                oY = curPlayer.lerpY// += (curPlayer.Y - curPlayer.lerpY) / lerpFactor
                oZ = curPlayer.lerpZ// += ((curPlayer.Z-.1) - curPlayer.lerpZ) / lerpFactor
                Pt = -Math.acos(Y_ / (Math.hypot(X_,Y_,Z_)+.001))+Math.PI/2
                Yw = -Math.atan2(X_,Z_)
                Rl = -curPlayer.lerprl
                vx = curPlayer.vx
                vy = curPlayer.vy
                vz = curPlayer.vz
                
                //curPlayer.yw = Math.atan2(curPlayer.vx,curPlayer.vz)
              }else{
                R3(0,curPlayer.pt,curPlayer.yw)
                X_ = X
                Y_ = -Y
                Z_ = Z
                oX = curPlayer.X  // += (curPlayer.X - curPlayer.X) / Factor
                oY = curPlayer.lerpY// += (curPlayer.Y - curPlayer.Y) / Factor
                oZ = curPlayer.Z// += ((curPlayer.Z-.1) - curPlayer.Z) / Factor
                Pt = -Math.acos(Y_ / (Math.hypot(X_,Y_,Z_)+.001))+Math.PI/2
                Yw = -Math.atan2(X_,Z_)
                Rl = -curPlayer.rl
              }
              break
          }
          
          if(showstars) ST.map(v=>{
            X = v[0]
            Y = v[1]
            Z = v[2]
            R(Rl,Pt,Yw,1)
            if(Z>0){
              if((x.globalAlpha = Math.min(1,(Z/5e3)**2))>.1){
                s = Math.min(1e3, 4e5/Z)
                x.fillStyle = '#ffffff04'
                l = Q()
                x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=5
                x.fillStyle = '#fffa'
                x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
            }
          })

          x.globalAlpha = 1

          splosions = splosions.filter(splosion=>splosion[6]>.25)
          splosions.map(splosion => {
            X = splosion[0] += splosion[3]
            Y = splosion[1] += splosion[4] += grav /2
            Z = splosion[2] += splosion[5]
            splosion[3] /=1.125
            splosion[4] /=1.125
            splosion[5] /=1.125
            R(Rl,Pt,Yw,1)
            if(Z>0){
              l = Q()
              s = Math.min(1e4, 2e5/Z*splosion[6]**1.5)
              x.fillStyle = '#ff000006'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3
              x.fillStyle = '#ff880010'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3.5
              x.fillStyle = '#ffffffff'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            }
            splosion[6] -= .02
          })

          zSortPolygons = []
          tunnels.map((v, i) => {
            ax=ay=az=0
            v[0].map(q => {
              ax += q[0]
              ay += q[1]
              az += q[2]
            })
            X = ax /= v[0].length
            Y = ay /= v[0].length
            Z = az /= v[0].length
            R(Rl, Pt, Yw, 1)
            if(Z>0) {
              zSortPolygons = [...zSortPolygons, [Math.hypot(X,Y,Z), v[0], v[1]]]
            }
          })
          
          shapes.map((shp, idx) => {
            tx = shp[0]
            ty = shp[1]
            tz = shp[2]
            shp[3].map(v => {
              ax=ay=az=0
              v.map(q => {
                ax += q[0]
                ay += q[1]
                az += q[2]
              })
              X = tx + (ax /= v.length)
              Y = ty + (ay /= v.length)
              Z = tz + (az /= v.length)
              R(Rl, Pt, Yw, 1)
              if(Z>0) {
                a = []
                v.map(q=> a = [...a,[ q[0]+tx, q[1]+ty, q[2]+tz]])
                zSortPolygons = [...zSortPolygons, [Math.hypot(X,Y,Z), a, tunSections+idx]]
              }
            })
          })

          zSortPolygons.sort((a,b)=>b[0]-a[0])
          els = tunSections + shapes.length
          zSortPolygons.map((v_,i) => {
            v = v_[1]
            x.beginPath()
            d1 = 0
            ax=ay=az=ct = 0
            v.map(q=>{
              X = q[0]
              Y = q[1]
              Z = q[2]
              R(Rl, Pt, Yw, 1)
              if(Z>0) {
                x.lineTo(...Q())
                ax += X
                ay += Y
                az += Z
                ct++
              }
            })
            ax /= ct
            ay /= ct
            az /= ct
            d1 = Math.hypot(ax,ay,az)
            if(d1<falloffDist){
              alpha1 = .5/(1+d1**4/4e8)
              alpha2 = .7 /(1+d1**2/1e5)
              col1 = alpha1 >.05 ? `hsla(${360/els*v_[2]},40%,50%,${alpha1})` : ''
              col2 = `hsla(${360/els*v_[2]},80%,40%,${alpha2})`
              stroke(col1, col2, 30, true)
            }
          })

          players.map((player, idx) => {
            player.idx = idx
            doKeys(player, idx)
            if(player.mbutton[0]) leftButton(player)
            if(player.mbutton[2]) rightButton(player)
          
            if(player.shooting && !(((t*60|0)%iBulletFreq))) spawnBullet(player)
            
            player.lerpX += (player.X - player.lerpX) / lerpFactor
            player.lerpY += (player.Y - player.lerpY) / 3
            player.lerpZ += (player.Z - player.lerpZ) / lerpFactor
            
            player.lerprl += (player.rl - player.lerprl) / lerpFactor
            player.lerppt += (player.pt - player.lerppt) / lerpFactor
            player.lerpyw += (player.yw - player.lerpyw) / lerpFactor

            /*while(player.yw > Math.PI*4) player.yw-=Math.PI*8
            while(player.yw < -Math.PI*4) player.yw+=Math.PI*8
            while(player.pt > Math.PI*4) player.pt-=Math.PI*8
            while(player.pt < -Math.PI*4) player.pt+=Math.PI*8
            while(player.rl > Math.PI*4) player.rl-=Math.PI*8
            while(player.rl < -Math.PI*4) player.rl+=Math.PI*8
            */
            
            player.rl += player.rlv
            player.pt += player.ptv
            player.yw += player.ywv
            if(player.pt>Math.PI/2){
              player.pt = Math.PI/2
              player.ptv = 0
            }
            if(player.pt<-Math.PI/2){
              player.pt = -Math.PI/2
              player.ptv = 0
            }
            player.rlv/=1.5
            player.ptv/=1.5
            player.ywv/=1.5
            
            if(player.flymode){
              player.vx /=1.2
              player.vy /=1.2
              player.vz /=1.2
            }
            
            coldist = playerSize/2
            ocg = player.grounded
            player.grounded = false
            if(!player.flymode || flymodeCollisons) for(let i=6; i--;){
              let skip = false
              switch(i){
                case 0:
                  if(player.vx<=0) skip = true
                  X = coldist, Y = 0, Z = 0
                  break
                case 1:
                  if(player.vy<=0) skip = true
                  X = 0, Y = coldist*2, Z = 0
                  break
                case 2:
                  if(player.vz<=0) skip = true
                  X = 0, Y = 0, Z = coldist
                  break
                case 3:
                  if(player.vx>=0) skip = true
                  X = -coldist, Y = 0, Z = 0
                  break
                case 4:
                  if(player.vy>=0) skip = true
                  X = 0, Y = -coldist, Z = 0
                  break
                case 5:
                  if(player.vz>=0) skip = true
                  X = 0, Y = 0, Z = -coldist
                  break
              }
              if(!skip){
                X1 = player.X  //+ player.vx*4
                Y1 = player.Y  //+ player.vy*4
                Z1 = player.Z  //+ player.vz*4
                X2 = X1+X
                Y2 = Y1+Y
                Z2 = Z1+Z
                if((l = collisions(X1, Y1, Z1, X2, Y2, Z2))){
                  if(!player.flymode) {
                    ref = reflect([player.vx, player.vy, player.vz], l[1])
                    if(i==0 || i==2 || i==3 || i==5){
                      if(!(player.keys[65] || player.keys[87] || player.keys[68] || player.keys[83])){
                        player.vx/=2.5
                        player.vz/=2.5
                        player.vx = ref[0]
                        player.vz = ref[2]
                      }else{
                        player.vx = ref[0]
                        player.vz = ref[2]
                      }
                    }
                    player.vy = ref[1]/2
                  }
                }
                if(i==1 && l) {
                  if(player.vy>=0) {
                    if(player.jumping){
                      //player.animation.curFrame = 0
                    }
                  }
                  player.Y = l[0][1]-playerSize/1.1
                  player.vy *= player.vy > 0 ? -.25 : .25
                  player.grounded = true
                  player.jumping = false
                }
              }
            }

            if(!idx){
              if(player.grounded){
                player.vy *= player.vy>0 ? -.1 : .1
              }else{
                if(!player.flymode) player.vy += grav
              }
              
              if(!player.flymode){
                d1 = Math.hypot(player.vx,player.vy,player.vz)
                d2 = Math.min(maxSpeed, d1)
                player.vx /=d1
                player.vz /=d1
                player.vx *=d2
                player.vz *=d2
              }
              
              if(!idx){
                player.X += player.vx
                player.Y += player.vy
                player.Z += player.vz
              }

              if(player.Y > 1e3) {
                if(!player.id) spawnFlashNotice('falling death!')
                sendDeathSplosions = [...sendDeathSplosions, 
                  [player.X, player.Y, player.Z]
                ]
                spawnFlash(player.X, player.Y, player.Z)
                spawnBigSplosion(player.X, player.Y, player.Z)
                respawnPlayer(player)
              }

              if(player.grounded && !player.flymode){
                if(!(player.keys[65] || player.keys[87] || player.keys[68] || player.keys[83])){
                  player.vx /= 2
                  player.vz /= 2
                }else{
                  player.vx /= 1.2
                  player.vz /= 1.2
                }
                player.rlv /= 1.5
                player.ptv /= 1.5
                player.ywv /= 1.5
              }else{
                player.rl += player.rlv/1.4
                player.pt += player.ptv/1.4
                player.yw += player.ywv/1.4
              }
            }
            if(camSelected != idx) drawPlayer(player)
            if(!scores.filter(q=>+q.id==+player.id).length){
              scores = [...scores, {id: +player.id, score: 0}]
            }else{
              if(player.score > scores.filter(q=>+q.id==+player.id)[0].score){
                scores.filter(q=>+q.id==+player.id)[0].score = player.score
              }
            }
          })

          //AI STUFF
          if(0) players.map((player, idx) => {
            let mind = 6e6, tgt = -1
            if(players.length>1){
              players.map((player2, idx2) => {
                if(idx != idx2){
                  X1 = player.X
                  Y1 = player.Y
                  Z1 = player.Z
                  X2 = player2.X
                  Y2 = player2.Y
                  Z2 = player2.Z
                  if((d=Math.hypot(X2-X,Y2-Y,Z2-Z))<mind){
                    mind = d
                    tgt = idx2
                  }
                }
              })
            }
            if(player.id) {
              player.keys[32] = false
              if(Rn()<.01) player.keys[32] = true, player.animation.curFrame = 10
              if(tgt != -1){
                p1a = player.lerpyw
                p2a = player.lerppt
                X1 = player.X
                Y1 = player.Y
                Z1 = player.Z
                X2 = players[tgt].X
                Y2 = players[tgt].Y
                Z2 = players[tgt].Z
                p1b = Math.atan2(X2-X1,Z2-Z1)
                p2b = -Math.acos((Y2-Y1) / (Math.hypot(X2-X1,Y2-Y1,Z2-Z1)+.001)) + Math.PI/2
                if(p2b<p2a){
                  player.keys[38]=true
                  player.keys[40]=false
                }else{
                  player.keys[38]=false
                  player.keys[40]=true
                }
                if(Math.abs(p1a-p1b)>Math.PI){
                  if(p1a>p1b){
                    p1b+=Math.PI*2
                  }else{
                    p1a+=Math.PI*2
                  }
                }
                if(p1a>p1b){
                  player.keys[37]=true
                  player.keys[39]=false
                }else{
                  player.keys[37]=false
                  player.keys[39]=true
                }
                
                if(Rn()<.1) player.keys[17] = false
                if(Rn()<.1 && lineOfSight(player, players[tgt])) player.keys[17] = true
              }
              
              
              player.keys[87] = false
              if(Rn()<.85) player.keys[87] = true
              if(camSelected != idx) drawPlayer(player)
            }
          })

          sparks = sparks.filter(spark=>spark[6]>.25)
          sparks.map(spark => {
            X1 = spark[0]
            Y1 = spark[1]
            Z1 = spark[2]
            X2 = X1 + spark[3] * 2
            Y2 = Y1 + spark[4] * 6
            Z2 = Z1 + spark[5] * 2
            if(0&&(l = collisions(X1,Y1,Z1,X2,Y2,Z2))){
              ref = reflect([spark[3], spark[4], spark[5]], l[1])
              spark[3] = ref[0]
              spark[4] = ref[1]
              spark[5] = ref[2]
            }
            X = spark[0] += spark[3]
            Y = spark[1] += spark[4] += grav /2
            Z = spark[2] += spark[5]
            spark[3] /=1.05
            spark[4] /=1.05
            spark[5] /=1.05
            R(Rl,Pt,Yw,1)
            if(Z>0){
              l = Q()
              s = Math.min(1e4, 1e5/Z*spark[6]**1.5)
              x.fillStyle = '#ff000006'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3
              x.fillStyle = '#ff880010'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3.5
              x.fillStyle = '#ffffffff'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            }
            spark[6] -= .02
          })

          bigSplosions = bigSplosions.filter(bigSplosion=>bigSplosion[6]>.25)
          bigSplosions.map(bigSplosion => {
            X = bigSplosion[0] += bigSplosion[3]
            Y = bigSplosion[1] += bigSplosion[4] += grav /2
            Z = bigSplosion[2] += bigSplosion[5]
            bigSplosion[3] /=1.125
            bigSplosion[4] /=1.125
            bigSplosion[5] /=1.125
            R(Rl,Pt,Yw,1)
            if(Z>0){
              l = Q()
              s = Math.min(1e4, 2e5/Z*bigSplosion[6]**1.5)
              x.fillStyle = '#ff000006'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3
              x.fillStyle = '#ff880010'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              s/=3.5
              x.fillStyle = '#ffffffff'
              x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            }
            bigSplosion[6] -= .02
          })

          olc = x.lineJoin

          bullets = bullets.filter(bullet=>{
            if(bullet[6]>.1){
              return true
            }else{
              spawnSplosion(...bullet)
              spawnFlash(...bullet)
              return false
            }
          })
          
          bullets = bullets.map(bullet => {
            X1 = bullet[0] - bullet[3]*1
            Y1 = bullet[1] - bullet[4]*1
            Z1 = bullet[2] - bullet[5]*1
            X2 = X1 + bullet[3]*2
            Y2 = Y1 + bullet[4]*2
            Z2 = Z1 + bullet[5]*2
            if(l = collisions(X1,Y1,Z1,X2,Y2,Z2)){
              tx = l[0][0]+l[1][0] * 2
              ty = l[0][1]+l[1][1] * 2
              tz = l[0][2]+l[1][2] * 2
              if(Rn()<.3) spawnSparks(tx,ty,tz)

              ref = reflect([bullet[3], bullet[4], bullet[5]], l[1])

              bullet[3] = ref[0]
              bullet[4] = ref[1]
              bullet[5] = ref[2]
            }
            X = bullet[0] += bullet[3]
            Y = bullet[1] += bullet[4]
            Z = bullet[2] += bullet[5]
            
            players.map((player, idx) => {
              if(player.id != bullet[7]){
                X2 = player.X
                Y2 = player.Y
                Z2 = player.Z
                if((d = Math.hypot(X2-X,Y2-Y,Z2-Z))<playerSize){
                  player.health -= bulletDamage
                  if(player.health <= 0){
                    if(+player.id == +userID) {
                      players.filter(player=>player.id==bullet[7])[0].score++
                      scores.filter(q=>+q.id == +bullet[7])[0].score++
                    }
                    sendDeathSplosions = [...sendDeathSplosions, 
                      [bullet[0], bullet[1], bullet[2]]
                    ]
                    spawnBigSplosion(...bullet)
                    spawnFlash(...bullet)
                    respawnPlayer(player)
                  }else{
                    spawnSplosion(...bullet)
                  }
                  bullet[6] = 0
                }
              }      
            })
            
            if(bullet[6]){
              R(Rl,Pt,Yw,1)
              if(Z>0){
                l = Q()
                s = Math.min(1e4, 1e5/Z)
                //x.drawImage(burst,l[0]-s/2/1.00,l[1]-s/2/1.00,s,s)
                x.drawImage(starImgs[0].img,l[0]-s/2/1.00,l[1]-s/2/1.00,s,s)
                s/=4
                x.drawImage(starImgs[4].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
              }
              bullet[6] -= .015
            }
            return bullet
          })

          flashes = flashes.filter(flash=>flash[3]>.25)
          flashes.map(flash => {
            X = flash[0]
            Y = flash[1]
            Z = flash[2]
            R(Rl,Pt,Yw,1)
            if(Z>0){
              l = Q()
              s = Math.min(1e4, 1e6/Z*flash[3]**1.5)
              x.fillStyle = '#ff000006'
              x.drawImage(starImgs[0].img,l[0]-s/2,l[1]-s/2,s,s)
              s/=2
              x.drawImage(starImgs[1+Rn()*8|0].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
            }
            flash[3] -= .05
          })
          
          if(showCrosshair){
            x.globalAlpha = .2
            s=800
            x.drawImage(crosshairImgs[crosshairSel].img,c.width/2-s/2,c.height/2-s/2,s,s)
            x.globalAlpha = 1
            x.lineJoin = x.lineCap = olc
            //x.lineCap = x.lineJoin = 'round'
          }

          if(showTopo){
            let ls_ = 400
            let margin = 20
            x.fillStyle = '#000e'
            x.strokeStyle ='#208c'
            x.lineWidth = 20
            x.strokeRect(tx_ = c.width-ls_-margin, ty_ = margin,ls_,ls_/1.5)
            x.fillRect(tx_ = c.width-ls_-margin, ty_ = margin,ls_,ls_/1.5)
            tx_+=ls_/2
            ty_+=ls_/2/1.6
            shapes.map((shp, idx) => {
              v_ = tunSections+idx
              tx = shp[0]
              ty = shp[1]
              tz = shp[2]
              shp[3].map(v => {
                x.beginPath()
                v.map(q=>{
                  X = q[0] + tx
                  Y = q[1] + ty
                  Z = q[2] + tz
                  R4(0, -.66, t/3, topoZoom)
                  l = Q4()
                  if(Z>0) x.lineTo(l[0]+tx_, l[1]+ty_)
                })
                alpha1 = 0
                alpha2 = .75
                col1 = ''//alpha1 >.05 ? `hsla(${360/els*v_},40%,50%,${alpha1})` : ''
                col2 = `hsla(${360/els*v_},80%,40%,${alpha2})`
                stroke(col1, col2, 30, true)
              })
            })
            tunnels.map((v, idx) => {
              ax=ay=az=0
              x.beginPath()
              v[0].map(q => {
                X = q[0]
                Y = q[1]
                Z = q[2]
                R4(0, -.66, t/3, topoZoom)
                l = Q4()
                if(Z>0) x.lineTo(l[0]+tx_, l[1]+ty_)
              })
              alpha1 = 0
              alpha2 = .75
              col1 = ''//alpha1 >.05 ? `hsla(${360/els*v[1]},40%,50%,${alpha1})` : ''
              col2 = `hsla(${360/els*v[1]},80%,40%,${alpha2})`
              stroke(col1, col2, 30, true)
            })
            
            
            bullets.map(bullet=>{
              X = bullet[0]
              Y = bullet[1]
              Z = bullet[2]
              R4(0, -.66, t/3, topoZoom)
              if(Z>0){
                l=Q4()
                s = Math.min(1e3, 8e4/Z)
                x.fillStyle = '#fdfe'
                x.drawImage(burst,l[0]-s/2+tx_, l[1]-s/2+ty_,s,s)
              }
            })

            players.map((player, idx)=>{
              X = player.lerpX
              Y = player.lerpY
              Z = player.lerpZ
              R4(0, -.66, t/3, topoZoom)
              if(Z>0){
                l=Q4()
                s = Math.min(1e3, 5e5/Z)
                x.drawImage(starImgs[0].img,(lx = l[0])-s/2+tx_, (ly = l[1])-s/2+ty_,s,s)
              }
              if(idx == camSelected){
                x.strokeStyle = '#8fcc'
                x.lineWidth = 2
                x.beginPath()
                X = lx + tx_
                Y = margin
                x.lineTo(X,Y)
                X = lx + tx_
                Y = margin + ls_/1.5
                x.lineTo(X,Y)
                x.stroke()
                x.beginPath()
                X = tx_-ls_/2
                Y = ly + ty_
                x.lineTo(X,Y)
                X = tx_ + ls_/2
                Y = ly + ty_
                x.lineTo(X,Y)
                x.stroke()
              }
            })
          }
          
          menuWidth = 150
          menux = -menuWidth
          bct = 0  // must appear before 1st button (for callbacks/ clickability)
          if(showInfo){
            
            ofy_ = 1070
            let ofx = hotkeysModalVisible ? (hotkeyX=Math.min(450,(hotkeyX+=hotkeyX/2+10))) : (hotkeyX=Math.max(0,(hotkeyX-=hotkeyX/2)))
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
            Y = c.height - ofy_ + 70 + fs/1.5
            x.fillStyle = '#f0f'
            x.fillText('H', X, Y)
            x.fillText('O', X, Y+=fs)
            x.fillText('T', X, Y+=fs)
            x.fillText('K', X, Y+=fs*2)
            x.fillText('E', X, Y+=fs)
            x.fillText('Y', X, Y+=fs)
            x.fillText('S', X, Y+=fs)
            
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
                x[textFunc]('Arrows    - look', X+ofx2, ofy2+Y)
                x[textFunc]('mouse     - look', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('SPACE     - jump', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('W/A/S/D   - move', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('SHIFT     - speed boost', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('CTRL      - fire guns', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('M         - info toggle', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('0-9       - player cams', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('C         - crosshairs', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('F         - fly mode', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('⤷PGUP/PGDN- move vert.', X+ofx2, ofy2+(Y+=fs))
                x[textFunc]('H         - hotkeys', X+ofx2, ofy2+(Y+=fs))
              }
            }
            olc = x.lineJoin

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
            x.strokeText(`   cam`,c.width-725,c.height-120)
            x.strokeText(` score`,c.width-725,c.height-120+fs*1)
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
            x.fillRect(c.width-510,c.height-50, 480*players[camSelected].health,32)
            x.strokeStyle = '#fff'
            x.lineWidth = 5
            x.strokeRect(c.width-510,c.height-50, 480,32)
          }
          
          X = c.width - 690
          Y = c.height - 132
          renderButton('m⇩', X, Y, '  hide menu', 'toggleMenu()', 'rectangle', '#0ff8', '#2088', 40)

          X = c.width - 55
          Y = c.height - 50
          renderButton('m⇧', X, Y, '  show menu', 'toggleMenu()', 'rectangle', '#0ff8', '#2088', 64)

          X = 460
          Y = c.height-(1+players.length)*fs+24
          renderButton('EXIT LEVEL', X, Y, '  go back to level menu', 'levelMenu()', 'rectangle', '#0ff8', '#2088', 32)

          X = 50
          Y = c.height-(1+players.length)*(fs*1.75)+24
          renderButton('🔗', X, Y, '  copy shareable link to this game-in-progress', 'fullCopy()', 'rectangle', '#0ff8', '#2088', 64)

          flashNotices = flashNotices.filter(v=>v[2]>0)
          if(flashNotices.length){
            x.fillStyle = flashNotices[l=flashNotices.length-1][1]
            x.globalAlpha = flashNotices[l][2]
            x.textAlign = 'center'
            x.fillRect(0,0,c.width,c.height)
            x.fillStyle = '#fff'
            x.font = (fs=60)+'px Courier Prime'
            x.fillText(flashNotices[l][0],c.width/2, c.height/1.6 - fs)
            flashNotices[l][2]-=.05
          }

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
        }else{
          if(showstars) ST.map(v=>{
            X = v[0]
            Y = v[1]
            Z = v[2]-=200
            if(v[2]<-G_/2) v[2]+=G_
            R(Rl,Pt,Yw,1)
            if(Z>0){
              if((x.globalAlpha = Math.min(1,(Z/5e3)**2))>.1){
                s = Math.min(1e3, 4e5/Z)
                x.fillStyle = '#ffffff04'
                l = Q()
                x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=5
                x.fillStyle = '#fffa'
                x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
            }
          })
          x.globalAlpha = 1
          x.lineCap = x.lineJoin = 'round'
          x.drawImage(levels,c.width/2-levels.width,c.height/2-levels.height+100,levels.width*2,levels.height*2)
          x.textAlign = 'center'
          x.font = (fs = 200) + 'px Courier Prime'
          X = c.width/2
          Y = 200
          t_ = S(t/2)*500
          x.strokeStyle = `hsla(${t_},99%,50%,.2)`
          x.lineWidth = 60
          x.strokeText('CHOOSE A LEVEL',X,Y)
          x.strokeStyle = `hsla(${t_+100},99%,50%,.4)`
          x.lineWidth = 30
          x.strokeText('CHOOSE A LEVEL',X,Y)
          x.fillStyle = `hsla(${t_+200},99%,50%,1)`
          x.fillText('CHOOSE A LEVEL',X,Y)
          x.font = (fs = 400) + 'px Courier Prime'
          x.fillStyle = '#fff8'
          w = 279*2
          h = 199*2
          c.style.cursor = 'unset'
          tsel = -1
          for(i=6;i--;){
            X = c.width/2+((i%3)-1)*w
            Y = c.height/2+((i/3|0)-1)*h + 100
            x.fillText(i+1,X,Y+fs/1.3)
            X-=128*2
            Y+=13*2
            w_=257*2,h_=175*2
            //x.strokeStyle = '#f008'
            //x.strokeRect(X,Y,w_,h_)
            if(mx>X && mx<X+w_ && my>Y && my<Y+h_){
              c.style.cursor = 'pointer'
              tsel = i
            }
          }
          if(tsel != -1 && mousedown){
            //newLevel = tsel
            level = tsel
            createLevel (tsel)
          }
          x.lineCap = x.lineJoin = 'roud'
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
                  X: player.X,
                  Y: player.Y,
                  Z: player.Z,
                  rl: player.rl,
                  pt: player.pt,
                  yw: player.yw,
                  //rlv: player.rlv,
                  //ptv: player.ptv,
                  //ywv: player.ywv,
                  randSeed,
                  randCt,
                  vx: player.vx,
                  vy: player.vy,
                  vz: player.vz,
                  id: userID,
                  jumping: player.jumping,
                  //drift: player.drift,
                  health: player.health,
                  //curGun: player.curGun,
                  //camMode: player.camMode,
                  shooting: player.shooting,
                  //poweredUp: player.poweredUp,
                  name: player.name,
                  
                  deathSplosions: JSON.parse(JSON.stringify(sendDeathSplosions)),
                  level: newLevel,
                }
                individualPlayerData['playerData'] = sendPlayer
              }
              
              // clear, having sent
              setTimeout(()=>{sendDeathSplosions = []}, 3000)
              
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
                            switch(key2){
                              //case 'Y': omit = true; break
                              //case 'vy': omit = true; break
                              //case 'pt': omit = true; break;
                              //case 'rl': omit = true; break
                            }
                            if(!omit) {
                              if(key2 == 'score'){
                                if(+matchingPlayer[key2] < +val2) matchingPlayer[key2] = val2
                              }else{
                                switch(key2){
                                  case 'health':
                                    matchingPlayer.health = +val2
                                  break
                                  case 'X':
                                    matchingPlayer.X = val2
                                  break
                                  case 'Y':
                                    matchingPlayer.Y = val2
                                  break
                                  case 'Z':
                                    matchingPlayer.Z = val2
                                  break
                                  case 'deathSplosions':
                                    val2.map(loc => {
                                      spawnBigSplosion(...loc)
                                      spawnFlash(...loc)
                                    })
                                  break
                                  case 'shooting':
                                    matchingPlayer.shooting = val2
                                  break
                                  case 'jumping':
                                    matchingPlayer.jumping = val2
                                  break
                                  case 'vx':
                                    matchingPlayer.vx = val2
                                  break
                                  case 'vy':
                                    matchingPlayer.vy = val2
                                  break
                                  case 'vz':
                                    matchingPlayer.vz = val2
                                  break
                                  case 'rl':
                                    matchingPlayer.rl = val2
                                  break
                                  case 'pt':
                                    matchingPlayer.pt = val2
                                  break
                                  case 'yw':
                                    matchingPlayer.yw = val2
                                  break
                                  case 'randSeed':
                                    recRandSeed = val2
                                  break
                                  case 'randCt':
                                    recRandCt = val2
                                  break
                                  case 'level':
                                    if(+val2 != -1 && newLevel == -1 && (level == -1 || levelSelected) && val2 != level) {
                                      setTimeout(()=>{
                                        randSeed = recRandSeed
                                        randCt   = recRandCt
                                        createLevel(+val2)
                                      },0)
                                    }
                                  break
                                  default:
                                    //if(+matchingPlayer.id != +userID) matchingPlayer[key2] = val2
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
            //console.log('recData', recData)
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