<!DOCTYPE html>
<html>
  <head>
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
      .replay{
        z-index: 100;
        display: none;
        border: none;
        background: #284c;
        font-family: Courier Prime;
        color: #fff;
        text-shadow: 1px 1px 1px #000;
        position: absolute;
        left: 50%;
        font-size: 24px;
        top: 50%;
        cursor: pointer;
        border-radius: 10px;
        padding: 5px;
        min-width: 200px;
        transform: translate(-50%, -50%);
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
    <button onclick="restart()" class="replay">play again</button>
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
        if(!t){
          window.onload = () => {
            c.focus()
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
          
          R=R2=(Rl,Pt,Yw,m)=>{
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
              X+=oX
              Y+=oY
              Z+=oZ
            }
          }
          Q=()=>[c.width/2+X/Z*700,c.height/2+Y/Z*700]
          I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
          
          Rn = Math.random
          async function loadOBJ(url, scale, tx, ty, tz, rl, pt, yw) {
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
              x.lineWidth = Math.min(1000,100*lwo/Z)
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

          G_ = 100000, iSTc = 1e4
          ST = Array(iSTc).fill().map(v=>{
            X = (Rn()-.5)*G_
            Y = (Rn()-.5)*G_
            Z = (Rn()-.5)*G_
            return [X,Y,Z]
          })

          burst = new Image()
          burst.src = "/games_shared_assets/burst.png"

          showstars = false


          cl = 7
          rw = 13
          sp = 1
          frame = Array(rw*cl).fill().map((v, i) => {
            X = ((i%cl)-cl/2+.5)*sp
            Y = ((i/cl|0)-rw/2+.5)*sp
            Z = 0
            return [X,Y,Z]
          })

          w = cl*sp-.5
          h = rw*sp-.5
          outerFrame = Array(4).fill().map((v, i) => {
            X = ((i%2)-2/2+.5)*w
            Y = ((i/2|0)-2/2+.5)*h
            Z = 0
            return [X,Y,Z]
          })
          
          puyosLoaded = false
          puyos = Array(5).fill().map((v,i) => {
            el = {img: new Image(), loaded: false}
            el.img.onload = () => {
              puyos[i].loaded = true
              if(puyos.filter(q=>q.loaded).length == 5) puyosLoaded = true
            }
            el.img.src = `/games_shared_assets/puyo${String.fromCharCode(65+i)}.png`
            return el
          })

          movTestLeft = () => {
            if(!validB(testB2) || isOjama(testB2)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(testB2[0].length>1){
              lx1 = Math.round(testB2[0][0][0] - 6 * sp)
              ly1 = Math.round(testB2[0][0][1] + 5 * sp)
              lx2 = Math.round(testB2[0][1][0] - 6 * sp)
              ly2 = Math.round(testB2[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1>lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1>0 && (idx1<0||testAR[idx1-1]==-1) && !(lx2==lx1-sp && ly2==ly1)) lx1=testB2[0][0][0] -= sp, success=true
              if(lx2>0 && (idx2<0||testAR[idx2-1]==-1) && !(lx1==lx2-sp && ly1==ly2)) testB2[0][1][0] -= sp, success=true
            }else{
              lx1 = Math.round(testB2[0][0][0] - 6 * sp)
              ly1 = Math.round(testB2[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1>0 && (idx1<0||testAR[idx1-1]==-1)) testB2[0][0][0] -= sp, success=true
            }
            return success
          }
          
          movTestRight = () => {
            if(!validB(testB2) || isOjama(testB2)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(testB2[0].length>1){
              lx1 = Math.round(testB2[0][0][0] - 6 * sp)
              ly1 = Math.round(testB2[0][0][1] + 5 * sp)
              lx2 = Math.round(testB2[0][1][0] - 6 * sp)
              ly2 = Math.round(testB2[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1<lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1<5 && (idx1<0||testAR[idx1+1]==-1) && !(lx2==lx1+sp && ly2==ly1)) lx1=testB2[0][0][0] += sp, success=true
              if(lx2<5 && (idx2<0||testAR[idx2+1]==-1) && !(lx1==lx2+sp && ly1==ly2)) testB2[0][1][0] += sp, success=true
            }else{
              lx1 = Math.round(testB2[0][0][0] - 6 * sp)
              ly1 = Math.round(testB2[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1<5 && (idx1<0||testAR[idx1+1]==-1)) testB2[0][0][0] += sp, success=true
            }
            return success
          }

          tryTestRot = (lx, ly) => {
            let lx1, ly1, idx
            lx1 = Math.round(lx - 6 * sp)
            ly1 = Math.round(ly + 5 * sp)
            idx = Math.round(ly1*6+lx1)
            return lx1>-1 && lx1<6 && idx<72 && (idx<0 || testAR[idx]==-1)
          }
          
          rotTestRight = () => {
            if(!validB(testB2) || isOjama(testB2)) return
            let success=false, ml=false, mr=false
            if(testB2[0].length>1){
              cx1 = testB2[0][0][0]
              cy1 = testB2[0][0][1]
              cx2 = testB2[0][1][0]
              cy2 = testB2[0][1][1]
              
              lx1 = Math.round(cx1 - 6 * sp)
              lx2 = Math.round(cx2 - 6 * sp)
              if(lx1==lx2 && lx1==5 && cy2<cy1) ml=movTestLeft(), cx1--, cx2--
              if(lx1==lx2 && lx1==0 && cy2>cy1) mr=movTestRight(), cx1++, cx2++
              
              p = Math.atan2(cx2-cx1,cy2-cy1)-Math.PI/2
              tx = cx1 + S(p) * sp
              ty = cy1 + C(p) * sp
              if(!(tx==cx1&&ty==cy1) && (success=tryTestRot(tx,ty))){
                testB2[0][1][0] = cx1 + S(p) * sp
                testB2[0][1][1] = cy1 + C(p) * sp
              }
              if(!success && ml) movTestRight()
              if(!success && mr) movTestLeft()
            }
          }

          tryB2Rot = (lx, ly) => {
            let lx1, ly1, idx
            lx1 = Math.round(lx - 6 * sp)
            ly1 = Math.round(ly + 5 * sp)
            idx = Math.round(ly1*6+lx1)
            return lx1>-1 && lx1<6 && idx<72 && (idx<0 || P2[idx]==-1)
          }
          
          rotB2Right = () => {
            if(!validB(B2) || isOjama(B2)) return
            let success=false, ml=false, mr=false
            if(B2[0].length>1){
              cx1 = B2[0][0][0]
              cy1 = B2[0][0][1]
              cx2 = B2[0][1][0]
              cy2 = B2[0][1][1]
              
              lx1 = Math.round(cx1 - 6 * sp)
              lx2 = Math.round(cx2 - 6 * sp)
              if(lx1==lx2 && lx1==5 && cy2<cy1) ml=movB2Left(), cx1--, cx2--
              if(lx1==lx2 && lx1==0 && cy2>cy1) mr=movB2Right(), cx1++, cx2++
              
              p = Math.atan2(cx2-cx1,cy2-cy1)-Math.PI/2
              tx = cx1 + S(p) * sp
              ty = cy1 + C(p) * sp
              if(!(tx==cx1&&ty==cy1) && (success=tryB2Rot(tx,ty))){
                B2[0][1][0] = cx1 + S(p) * sp
                B2[0][1][1] = cy1 + C(p) * sp
              }
              if(!success && ml) movB2Right()
              if(!success && mr) movB2Left()
            }
          }

          tryRot = (lx, ly) => {
            let lx1, ly1, idx
            lx1 = Math.round(lx + 10 * sp)
            ly1 = Math.round(ly + 5 * sp)
            idx = Math.round(ly1*6+lx1)
            return lx1>-1 && lx1<6 && idx<72 && (idx<0 || P1[idx]==-1)
          }
          
          rotRight = () => {
            if(!validB(B1) || isOjama(B1)) return
            let success=false, ml=false, mr=false
            if(B1[0].length>1){
              cx1 = B1[0][0][0]
              cy1 = B1[0][0][1]
              cx2 = B1[0][1][0]
              cy2 = B1[0][1][1]
              
              lx1 = Math.round(cx1 + 10 * sp)
              lx2 = Math.round(cx2 + 10 * sp)
              if(lx1==lx2 && lx1==5 && cy2<cy1) ml=movLeft(), cx1--, cx2--
              if(lx1==lx2 && lx1==0 && cy2>cy1) mr=movRight(), cx1++, cx2++
              
              p = Math.atan2(cx2-cx1,cy2-cy1)-Math.PI/2
              tx = cx1 + S(p) * sp
              ty = cy1 + C(p) * sp
              if(!(tx==cx1&&ty==cy1) && (success=tryRot(tx,ty))){
                B1[0][1][0] = cx1 + S(p) * sp
                B1[0][1][1] = cy1 + C(p) * sp
              }
              if(!success && ml) movRight()
              if(!success && mr) movLeft()
            }
          }

          movB2Left = () => {
            if(!validB(B2) || isOjama(B2)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(B2[0].length>1){
              lx1 = Math.round(B2[0][0][0] - 6 * sp)
              ly1 = Math.round(B2[0][0][1] + 5 * sp)
              lx2 = Math.round(B2[0][1][0] - 6 * sp)
              ly2 = Math.round(B2[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1>lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1>0 && (idx1<0||P2[idx1-1]==-1) && !(lx2==lx1-sp && ly2==ly1)) lx1=B2[0][0][0] -= sp, success=true
              if(lx2>0 && (idx2<0||P2[idx2-1]==-1) && !(lx1==lx2-sp && ly1==ly2)) B2[0][1][0] -= sp, success=true
            }else{
              lx1 = Math.round(B2[0][0][0] - 6 * sp)
              ly1 = Math.round(B2[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1>0 && (idx1<0||P2[idx1-1]==-1)) B2[0][0][0] -= sp, success=true
            }
            return success
          }
          
          movB2Right = () => {
            if(!validB(B2) || isOjama(B2)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(B2[0].length>1){
              lx1 = Math.round(B2[0][0][0] - 6 * sp)
              ly1 = Math.round(B2[0][0][1] + 5 * sp)
              lx2 = Math.round(B2[0][1][0] - 6 * sp)
              ly2 = Math.round(B2[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1<lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1<5 && (idx1<0||P2[idx1+1]==-1) && !(lx2==lx1+sp && ly2==ly1)) lx1=B2[0][0][0] += sp, success=true
              if(lx2<5 && (idx2<0||P2[idx2+1]==-1) && !(lx1==lx2+sp && ly1==ly2)) B2[0][1][0] += sp, success=true
            }else{
              lx1 = Math.round(B2[0][0][0] - 6 * sp)
              ly1 = Math.round(B2[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1<5 && (idx1<0||P2[idx1+1]==-1)) B2[0][0][0] += sp, success=true
            }
            return success
          }
          
          movLeft = () => {
            if(!validB(B1) || isOjama(B1)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(B1[0].length>1){
              lx1 = Math.round(B1[0][0][0] + 10 * sp)
              ly1 = Math.round(B1[0][0][1] + 5 * sp)
              lx2 = Math.round(B1[0][1][0] + 10 * sp)
              ly2 = Math.round(B1[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1>lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1>0 && (idx1<0||P1[idx1-1]==-1) && !(lx2==lx1-sp && ly2==ly1)) lx1=B1[0][0][0] -= sp, success=true
              if(lx2>0 && (idx2<0||P1[idx2-1]==-1) && !(lx1==lx2-sp && ly1==ly2)) B1[0][1][0] -= sp, success=true
            }else{
              lx1 = Math.round(B1[0][0][0] + 10 * sp)
              ly1 = Math.round(B1[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1>0 && (idx1<0||P1[idx1-1]==-1)) B1[0][0][0] -= sp, success=true
            }
            return success
          }
          
          movRight = () => {
            if(!validB(B1) || isOjama(B1)) return
            let lx1,ly1,lx2,ly2,idx1,idx2,success=false
            if(B1[0].length>1){
              lx1 = Math.round(B1[0][0][0] + 10 * sp)
              ly1 = Math.round(B1[0][0][1] + 5 * sp)
              lx2 = Math.round(B1[0][1][0] + 10 * sp)
              ly2 = Math.round(B1[0][1][1] + 5 * sp)
              idx1 = Math.round(ly1*6+lx1)
              idx2 = Math.round(ly2*6+lx2)
              if(lx1<lx2){
                tlx  = lx1, tly  = ly1, tidx = idx1
                lx1  = lx2, ly1  = ly2, idx1 = idx2
                lx2  = tlx, ly2  = tly, idx2 = tidx
              }
              if(lx1<5 && (idx1<0||P1[idx1+1]==-1) && !(lx2==lx1+sp && ly2==ly1)) lx1=B1[0][0][0] += sp, success=true
              if(lx2<5 && (idx2<0||P1[idx2+1]==-1) && !(lx1==lx2+sp && ly1==ly2)) B1[0][1][0] += sp, success=true
            }else{
              lx1 = Math.round(B1[0][0][0] + 10 * sp)
              ly1 = Math.round(B1[0][0][1] + 6 * sp)
              idx1 = Math.round(ly1*6+lx1)
              if(lx1<5 && (idx1<0||P1[idx1+1]==-1)) B1[0][0][0] += sp, success=true
            }
            return success
          }
          
          c.onkeydown = e => {
            e.preventDefault()
            e.stopPropagation()
            keys[e.keyCode] = true
          }
          
          c.onkeyup = e => {
            e.preventDefault()
            e.stopPropagation()
            if(e.keyCode !=32 || !quickDrop) keys[e.keyCode] = false
            keyTimers[e.keyCode] = 0
          }
          
          doKeys = () => {
            if(deathTimer && deathTimer>t)return
            keys.map((key, idx) => {
              if(key && keyTimers[idx]<=t){
                if(!B1alive || !B2alive){
                  setTimeout(()=>{restart()},50)
                }else{
                  switch(idx){
                    case 37:
                      keyTimers[idx] = t + keyPressPolyfillTimer/2
                      movLeft()
                      break
                    case 32:
                      if(keys.filter(v=>v).length == 1){
                        keyTimers[idx] = t + keyPressPolyfillTimer/30
                        quickDrop = true
                        dropB1()
                      }else{
                        keys = Array(256).fill(false)
                      }
                      break
                    case 38:
                      keyTimers[idx] = t + keyPressPolyfillTimer
                      rotRight()
                      break
                    case 39:
                      keyTimers[idx] = t + keyPressPolyfillTimer/2
                      movRight()
                      break
                    case 40:
                      keyTimers[idx] = t + keyPressPolyfillTimer/4
                      if(!isOjama(B1)) dropB1()
                      break
                  }
                }
              }
            })
          }
          
          genPiece = side => {
            if(!side){
              if(deathTimer) return
              if(P1[1]!=-1 || P1[2]!=-1 || P1[3]!=-1 || P1[4]!=-1){
                B1alive = false
                deathTimer = t + 2
                if(gameInPlay) score2++
                gameInPlay = false
                return
              }
              B1pieceQueued = false
            }else{
              if(P2[1]!=-1 || P2[2]!=-1 || P2[3]!=-1 || P2[4]!=-1){
                B2alive = false
                if(gameInPlay) score1++
                gameInPlay = false
                return
              }
              B2pieceQueued = false
            }
            let ofx = (sp * 8 + sp/2) * (side ? 1 : -1)
            let ofy = sp/2 - sp * 9
            let ofx2 = ofy2 = 0
            if(side && P2Ojama){
              newPiece = Array(P2Ojama).fill().map((v, i) => {
                ofx2+=1+(Rn()*2|0)
                if(ofx2>5) ofy2--
                ofx2%=6
                X = 0 + ofx - ofx2 + 3
                Y = -i + ofy + ofy2
                Z = 0
                id = 4
                return [X, Y, Z, id, X, Y, Z, 1]
              })
              P2Ojama = 0
            }else if(!side && P1Ojama){
              newPiece = Array(P1Ojama).fill().map((v, i) => {
                ofx2+=1+(Rn()*2|0)
                if(ofx2>5) ofy2--
                ofx2%=6
                X = 0 + ofx + ofx2 - 2
                Y = -i + ofy + ofy2
                Z = 0
                id = 4
                return [X, Y, Z, id, X, Y, Z, 1]
              })
              P1Ojama = 0
            }else{
              ofx = (sp * 8 + sp/2) * (side ? 1 : -1) -1 + (Rn()*4|0)
              newPiece = Array(2).fill().map((v, i) => {
                X = 0 + ofx
                Y = (i?-1:0) + ofy
                Z = 0
                id = Rn()*4|0
                return [X, Y, Z, id, X, Y, Z, 1]
              })
            }
            return newPiece
          }
          
          PlayerInit = idx => { // called initially & when a player dies
            Players[idx].score1         = score1
            Players[idx].score2         = score2
            Players[idx].totalPcs1      = totalPcs1
            Players[idx].totalPcs2      = totalPcs2
            Players[idx].P1Ojama        = P1Ojama
            Players[idx].P2Ojama        = P2Ojama
            Players[idx].B1alive        = B1alive
            Players[idx].B2alive        = B2alive
            //Players[idx].gameInPlay     = gameInPlay
            Players[idx].spawnSparksCmd = spawnSparksCmd
            Players[idx].B1             = B1
            Players[idx].P1             = P1
            Players[idx].B2             = []
            Players[idx].P2             = []
          }


          addPlayers = playerData => {
            PlayerLs = 1
            playerData.score = 0
            Players = [...Players, {playerData}]
            PlayerCount++
            PlayerInit(Players.length-1)
          }

          masterInit = () => {
            showBoxIds                 = true
            spawnSparksCmd             = []
            PlayerCount                = 0
            Players                    = []
            mx                         = 0
            my                         = 0
            score1                     = 0
            score2                     = 0
            totalPcs1                  = 0
            sliders                    = []
            player1Name                = playerName
            player2Name                = users.filter(v=>v.id!=userID)[0].name
            totalPcs2                  = 0
            AISpeed                    = 70 // 0-100...
            B1rensaTally               = 0
            B2rensaTally               = 0
            B1rensaChainLength         = 0
            B2rensaChainLength         = 0
            testCount                  = 0
            AIMoveSelected             = false
            B1                         = []
            B2                         = []
            sparks                     = []
            keys                       = Array(256).fill(false)
            keyTimers                  = Array(256).fill(0)
            B1RensaChainInProgress     = false
            B2RensaChainInProgress     = false
            B1RensaOffsets             = Array(6*14).fill(0)
            B2RensaOffsets             = Array(6*14).fill(0)
            quickDrop                  = false
            pieceQueued                = false
            B2pieceQueued              = false
            testB2RensaChainInProgress = false
            testB2pieceQueued          = false
            testB2alive                = true
            B1alive                    = true
            B2alive                    = true
            deathTimer                 = 0
            P1Ojama                    = 0
            P2Ojama                    = 0
            P1                         = Array(6*14).fill(-1)
            P2                         = Array(6*14).fill(-1)
            gameInPlay                 = true
            dropFreqMod                = 50 // 0-100...
            dropFreq                   = 5+60-60*(dropFreqMod/100)|0
            keyPressPolyfillTimer      = 1/60*10
          }
          masterInit()

          recurse = (side, idx, id, depth) => {
            if(id==4) return
            if(memo.indexOf(idx) != -1 || idx < 0 || idx > 71 || (side?P2:P1)[idx]==-1) return
            memo = [...memo, idx]
            if((side?P2:P1)[idx] == id) {
              count++
              cull = [...cull, idx]
              X = idx%6
              Y = idx/6|0
              let lidx = X>0?Y*6+X-1:-1
              let uidx = Y>0?(Y-1)*6+X:-1
              let ridx = X<5?Y*6+X+1:-1
              let didx = Y<11?(Y+1)*6+X:-1
              recurse(side, lidx, id, depth+1)
              recurse(side, uidx, id, depth+1)
              recurse(side, ridx, id, depth+1)
              recurse(side, didx, id, depth+1)
            }
          }
          
          testRecurse = (idx, id, depth) => {
            if(id==4) return
            if(memo.indexOf(idx) != -1 || idx < 0 || idx > 71 || testAR[idx]==-1) return
            memo = [...memo, idx]
            if(testAR[idx] == id) {
              testCount++
              cull = [...cull, idx]
              X = idx%6
              Y = idx/6|0
              let lidx = X>0?Y*6+X-1:-1
              let uidx = Y>0?(Y-1)*6+X:-1
              let ridx = X<5?Y*6+X+1:-1
              let didx = Y<11?(Y+1)*6+X:-1
              testRecurse(lidx, id, depth+1)
              testRecurse(uidx, id, depth+1)
              testRecurse(ridx, id, depth+1)
              testRecurse(didx, id, depth+1)
            }
          }

          spawnSparks = (X,Y,Z, pushToRemote = true) => {
            if(pushToRemote) spawnSparksCmd = [...spawnSparksCmd, [X,Y,Z]]
            let p, q
            for(let m=20;m--;){
              let vel = Rn()**.5/9
              let vx = S(p=Math.PI*2*Rn()) * S(q=Rn()<.5?Math.PI*Rn()**.5/2:Math.PI-Math.PI*Rn()**.5/2) * vel
              let vy = C(p) * S(q) * vel
              let vz = C(q) * vel
              sparks = [...sparks, [X,Y,Z,vx,vy,vz,1]]
            }
          }
          
          checkCompletion = side => {
            JSON.parse(JSON.stringify(side?P2:P1)).map((v,i) => {
              if(v!=-1) {
                cull = []
                memo = []
                count = 0
                recurse(side, i, v, 0)
                if(count>3){
                  if(side){
                    B2rensaChainLength++
                    totalPcs2 += cull.length
                    if(!B2RensaChainInProgress) {
                      B2rensaTally = Math.max(1, ((cull.length/2 * B2rensaChainLength) / 1 ) | 0)
                    }else{
                      B2rensaTally += Math.max(1, ((cull.length/2 * B2rensaChainLength) / 1 ) | 0)
                    }
                    B2RensaChainInProgress = true
                    P2 = P2.map((v,i) => {
                      return cull.indexOf(i) == -1 ? v : -1
                    })
                    JSON.parse(JSON.stringify(P2)).map((v,i) => {
                      if(cull.indexOf(i) != -1){
                        let X = i%6
                        let Y = i/6|0
                        let lidx = X>0?Y*6+X-1:-1
                        let uidx = Y>0?(Y-1)*6+X:-1
                        let ridx = X<5?Y*6+X+1:-1
                        let didx = Y<11?(Y+1)*6+X:-1
                        if(lidx!=-1 && lidx<72 && P2[lidx] == 4) P2[lidx] = -1
                        if(uidx!=-1 && uidx<72 && P2[uidx] == 4) P2[uidx] = -1
                        if(ridx!=-1 && ridx<72 && P2[ridx] == 4) P2[ridx] = -1
                        if(didx!=-1 && didx<72 && P2[didx] == 4) P2[didx] = -1
                        X += 8 * (side ? 1 : -1) - 2.5
                        Y -= 5.5
                        spawnSparks(X,Y,0)
                      }
                    })
                  }else{
                    B1rensaChainLength++
                    totalPcs1 += cull.length
                    if(!B1RensaChainInProgress) {
                      B1rensaTally = Math.max(1, ((cull.length/2 * B1rensaChainLength) / 1 ) | 0)
                    }else{
                      B1rensaTally += Math.max(1, ((cull.length/2 * B1rensaChainLength) / 1 ) | 0)
                    }
                    B1RensaChainInProgress = true
                    P1 = P1.map((v,i) => {
                      return cull.indexOf(i) == -1 ? v : -1
                    })
                    JSON.parse(JSON.stringify(P1)).map((v,i) => {
                      if(cull.indexOf(i) != -1){
                        let X = i%6
                        let Y = i/6|0
                        let lidx = X>0?Y*6+X-1:-1
                        let uidx = Y>0?(Y-1)*6+X:-1
                        let ridx = X<5?Y*6+X+1:-1
                        let didx = Y<11?(Y+1)*6+X:-1
                        if(lidx!=-1 && lidx<72 && P1[lidx] == 4) P1[lidx] = -1
                        if(uidx!=-1 && uidx<72 && P1[uidx] == 4) P1[uidx] = -1
                        if(ridx!=-1 && ridx<72 && P1[ridx] == 4) P1[ridx] = -1
                        if(didx!=-1 && didx<72 && P1[didx] == 4) P1[didx] = -1
                        X += 8 * (side ? 1 : -1) - 2.5
                        Y -= 5.5
                        spawnSparks(X,Y,0)
                      }
                    })
                  }
                  cull.map(v=>{
                    ofx = 8 * (side ? 1 : -1)
                    X = ((v%6)-2.5) * sp + ofx
                    Y = ((v/6|0)-5.5) * sp
                    Z = 0
                    spawnSparks(X,Y,Z)
                  })
                }
              }
            })
          }
          
          checkTestCompletion = () => {
            JSON.parse(JSON.stringify(testAR)).map((v,i) => {
              if(v!=-1) {
                cull = []
                memo = []
                testCount = 0
                testRecurse(i, v, 0)
                if(testCount>3){
                  testB2RensaChainInProgress = true
                  //testAR = testAR.map((v,i) => {
                  //  return cull.indexOf(i) == -1 ? v : -1
                  //})
                }
              }
            })
          }

          dropB1 = () => {
            if(!B1alive) return
            for(N=2;N--;){
              if(validB(B1)) B1.map(v=>{
                v.map(q=>{
                  lx = Math.round(q[0] + 10 * sp)
                  ly = Math.round(q[1] + 6 * sp)
                  idx = Math.round(ly*6+lx)
                  if(idx>0 && idx<72){
                    if(P1[idx]!=-1){
                      P1[idx-6]=q[3]
                      q[7]=0
                    }
                  }
                  if(idx>0 && idx>=72){
                    P1[idx-6]=q[3]
                    q[7]=0
                  }
                })
              })
            }
            B1 = B1.map(v=>{
              v = v.filter(q=>q[7])
              return v
            })
            B1 = B1.filter(v=>v.length)
            if(!B1.length && !B1pieceQueued) {
              checkCompletion(0)
              if(!B1RensaChainInProgress){
                B1pieceQueued = true
                quickDrop = keys[32] = false
                setTimeout(()=>{
                  B1 = [genPiece(0)]
                },100)
              }
            }
            if(B1.length && B1alive) {
              B1.map(v=>{
                v.map(q=>{
                  q[1]++
                })
              })
            }
          }
          
          dropB2 = () => {
            if(!B2alive) return
            for(N=2;N--;){
              if(validB(B2)) B2.map(v=>{
                v.map(q=>{
                  lx = Math.round(q[0] - 6 * sp)
                  ly = Math.round(q[1] + 6 * sp)
                  idx = Math.round(ly*6+lx)
                  if(idx>0 && idx<72){
                    if(P2[idx]!=-1){
                      P2[idx-6]=q[3]
                      q[7]=0
                    }
                  }
                  if(idx>0 && idx>=72){
                    P2[idx-6]=q[3]
                    q[7]=0
                  }
                })
              })
            }
            B2 = B2.map(v=>{
              v = v.filter(q=>q[7])
              return v
            })
            B2 = B2.filter(v=>v.length)
            if(!B2.length && !B2pieceQueued) {
              checkCompletion(1)
              if(!B2RensaChainInProgress){
                B2pieceQueued = true
                //quickDrop = keys[32] = false
                setTimeout(()=>{
                  B2 = [genPiece(1)]
                  AIMoveSelected = false
                },100)
              }
            }
            if(B2.length && B2alive) {
              B2.map(v=>{
                v.map(q=>{
                  q[1]++
                })
              })
            }
          }

          dropTestB2 = () => {
            if(!B2alive) return false
            let dropping = true
            for(let N=2;N--;){
              testB2.map(v=>{
                v.map(q=>{
                  lx = Math.round(q[0] - 6 * sp)
                  ly = Math.round(q[1] + 6 * sp)
                  idx = Math.round(ly*6+lx)
                  if(idx>0 && idx<72+6){
                    if(testAR[idx]!=-1){
                      testAR[idx-6]=q[3]
                      q[7]=0
                    }
                  }
                  if(idx>0 && idx>=72){
                    testAR[idx-6]=q[3]
                    q[7]=0
                  }
                })
              })
            }
            testB2 = testB2.map(v=>{
              v = v.filter(q=>q[7])
              return v
            })
            testB2 = testB2.filter(v=>v.length)
            if(!testB2.length){// && !testB2pieceQueued) {
              dropping = false
              checkTestCompletion(1)
              //if(!testB2RensaChainInProgress){
                //testB2pieceQueued = true
              //  setTimeout(()=>{
              //    testB2 = [genPiece(1)]
              //  },0)
              //}
            }
            if(testB2.length && testB2alive) {
              testB2.map(v=>{
                v.map(q=>{
                  q[1]++
                })
              })
            }
            return dropping
          }
          
          doLose = side => {
            for(let N=2;N--;){
              let m = side + 2
              ofx = (m+N)%2 ? -8:8
              cl_ = m<2 ? cl : 2;
              ct=0;
              outerFrame.map((v, i) => {
                if(i && i%cl_ && (i/cl_|0)){
                  x.beginPath()
                  l = i
                  X = f[l][0] + ofx
                  Y = f[l][1]
                  Z = f[l][2]
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                  l = i-1
                  X = f[l][0] + ofx
                  Y = f[l][1]
                  Z = f[l][2]
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                  l = i-1-cl_
                  X = f[l][0] + ofx
                  Y = f[l][1]
                  Z = f[l][2]
                  R(Rl,Pt,Yw,1)
                  if(Z>0) x.lineTo(...Q())
                  l = i-cl_
                  X = f[l][0] + ofx
                  Y = f[l][1]
                  Z = frame[l][2]
                  R(Rl,Pt,Yw,1)
                  if(Z>0){
                    l = Q()
                    x.lineTo(...l)
                    if(side==2){
                      col1 = '#ff06'
                      col2 = '#aa08'
                    }else{
                      col1 = N?'#0f06':'#f006'
                      col2 = N?'#0818':'#8018'
                    }
                    stroke(col1, col2, 4, true)
                  }
                  ct++
                }
              })
              X = ofx+.25
              Y = -1
              Z = 0
              R(Rl,Pt,Yw,1)
              if(Z>0){
                l = Q()
                if(side==2){
                  x.fillStyle = `hsla(${90},99%,${300+C(t*5+.25)*250}%,${.5+C(t*10)/2})`
                }else{
                  x.fillStyle = N?`hsla(${100},99%,${300+C(t*5+.25)*250}%,${.5+C(t*10)/2})`:`hsla(${0},99%,${300+C(t*5+.25)*250}%,${.5+C(t*10)/2})`
                }
                x.textAlign = 'center'
                x.font = (fs=800/Z)+'px Courier Prime'
                x.fillText(side==2?'TIE!':(N?'WIN!':'LOSE!'), l[0]-fs/4, l[1]-fs*2)
                x.fillText('GAME OVER', l[0]-fs/4, l[1])
                x.font = (fs=700/Z)+'px Courier Prime'
                x.fillStyle = `#fff`
                x.fillText('hit a key', l[0]-fs/4, l[1]+fs*2)
                x.font = (fs=600/Z)+'px Courier Prime'
                x.fillText('to continue', l[0]-fs/4, l[1]+fs*3.5)
              }
            }
          }

          restart = () => {
            setTimeout(()=>{
              quickDrop = false
              pieceQueued = false
              deathTimer = 0
              P1Ojama = 0
              P2Ojama = 0
              B1RensaChainInProgress = false
              B2RensaChainInProgress = false
              B1RensaOffsets = Array(6*14).fill(0)
              B2RensaOffsets = Array(6*14).fill(0)
              B1 = [genPiece(0)]
              //B2 = [genPiece(1)]
              B2 = []
              P1 = Array(6*14).fill(-1)
              P2 = Array(6*14).fill(-1)
              keys = Array(256).fill(false)
              keyTimers = Array(256).fill(0)
              gameInPlay = true
              B1alive = true
              B2alive = true
            }, 100)
          }

          beginGame = () => {
            B1 = [...B1, genPiece(0)]
            //B2 = [...B2, genPiece(1)]
            B2 = []
          }
          beginGame()
          
          rensaChainFoundVal = 20
          doAI = () => {
            if(!gameInPlay || !B2alive) return
            let dropping, tC
            if(validB(B2) && !isOjama(B2) && B2[0].length>1){
              ret = []
              for(let j = 4; j--;){
                for(let i=6;i--;){
                  scenarioScore = 0
                  testB2alive                = true
                  testB2RensaChainInProgress = false
                  tbr                        = false
                  testB2pieceQueued          = false
                  testB2 = JSON.parse(JSON.stringify(B2))
                  testAR = JSON.parse(JSON.stringify(P2))
                  ofx = Math.max(testB2[0][0][0]-5, testB2[0][1][0]-5)-.5
                  testB2[0][0][0]-=ofx
                  testB2[0][1][0]-=ofx
                  testB2[0][0][0]+=i
                  testB2[0][1][0]+=i
                  for(let m=j; m--;) rotTestRight()
                  tC=0
                  do{
                    if(dropping = dropTestB2()){
                      tC += testCount
                      vertIncentive = 0
                      if(testB2.length) testB2.map(v=>{
                        v.map(q=>{
                          vertIncentive += q[1]
                        })
                      })
                    }
                    if(testB2RensaChainInProgress) tbr = true
                  }while(dropping);
                  scenarioScore += tC
                  scenarioScore += vertIncentive
                  scenarioScore += cull.length*5
                  if(tbr) scenarioScore+=rensaChainFoundVal * cull.length
                  ret = [...ret, [scenarioScore, j, i, ofx, tC, cull.length*5, vertIncentive, tbr*cull.length]]
                }
              }
              let move = ret.sort((a,b)=>b[0]-a[0])[0]
              //console.log('testCount', move[4])
              //console.log('cull.length', move[5])
              //console.log('vertIncentive', move[6])
              //console.log('tbr', move[7])
              B2[0][0][0]-=move[3]
              B2[0][1][0]-=move[3]
              B2[0][0][0]+=move[2]
              B2[0][1][0]+=move[2]
              for(let m=move[1]; m--;) rotB2Right()
              
              AIMoveSelected = true
            }
          }
          
          isOjama = ar => {
            let ret = false
            ar.map(v=>{
              if(typeof v != 'undefined' && v != null){
                v.map(q=>{
                  if(q[3]==4) ret = true
                })
              }
            })
            return ret
          }
          
          validB = ar => {
            let ret = false
            if(typeof ar == 'object' && ar[0] != null){
              ret = true
              ar.map(v=>{
                if(typeof v == 'undefined') ret = false
              })
            }
            return ret
          }

          c.onmousedown = e => {
            let rect = c.getBoundingClientRect()
            mx = (e.pageX - rect.left)/c.clientWidth*c.width
            my = (e.pageY - rect.top)/c.clientHeight*c.height
            if(sliders.length){
              sliders.map(slider=>{
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * eval(slider.valVariable)
                Y = slider.posY
                s = slider.height/2
                d = Math.hypot(X-mx,Y-my)
                if(d<s && e.button == 0){
                  slider.sliding = true
                  slider.tmx = mx
                  slider.tmy = my
                }
              })
            }
          }
          
          c.onmouseup = e => {
            sliders.map(slider=>{
              slider.sliding = false
            })
          }
          
          c.onmousemove = e => {
            e.preventDefault()
            e.stopPropagation()
            let rect = c.getBoundingClientRect()
            mx = (e.pageX - rect.left)/c.clientWidth*c.width
            my = (e.pageY - rect.top)/c.clientHeight*c.height
            
            if(sliders.length){
              c.style.cursor = 'unset'
              sliders.map(slider=>{
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * eval(slider.valVariable)
                Y = slider.posY
                s = slider.height/2
                d = Math.hypot(X-mx,Y-my)
                if(d<s){
                  c.style.cursor = 'pointer'
                }
                if(slider.sliding){
                  if(slider.style == 'horizontal'){
                    dx = (mx-slider.tmx)/slider.width*(slider.max-slider.min)
                    eval(slider.valVariable + ' += dx')
                    slider.tmx = mx
                    slider.tmy = my
                    eval(slider.valVariable + ' = Math.min(slider.max,Math.max(slider.min,'+slider.valVariable+'))')
                    slider.captionVar = Math.round(eval(slider.valVariable)) + '%'
                    dropFreq = 5+60-60*(dropFreqMod/100)|0
                  }else{
                  }
                }
              })
            }
          }
          
          
          sliders = [...sliders,
            {
              caption: 'MY SPEED',
              style: 'horizontal',   // vertical/horizontal
              posX: c.width/2,
              posY: c.height-100,
              width: 400,
              height: 40,
              min: 0,
              max: 100,
              majorStep: 25,
              minorStep: 5,
              tickColor: '#0f8a',
              backgroundColor: '#40f4',
              selectorColor: '#fff',
              valVariable: 'dropFreqMod',
              padding: 75,
              textColor: '#f2a',
              fontSize: 32,
              captionVar: dropFreqMod + '%',
              sliding: false,
              tmx: 0,
              tmy: 0,
            }
          ]
          
          drawSlider = slider => {
            if(slider.style == 'horizontal'){
              x.fillStyle = slider.backgroundColor
              X = slider.posX - slider.width/2 - slider.padding/2
              Y = slider.posY - slider.height/2 - slider.padding/2
              w = slider.width + slider.padding
              h = slider.height + slider.padding
              x.fillRect(X,Y,w,h)
            }
            for(let i = slider.min; i<slider.max+1; i+=slider.minorStep){
              if(slider.style == 'horizontal'){
                x.beginPath()
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * i
                Y = slider.posY - slider.height/4
                x.lineTo(X,Y)
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * i
                Y = slider.posY + slider.height/4
                x.lineTo(X,Y)
                Z = 1
                stroke(slider.tickColor,'',.1, true)
              }else{
              }
            }
            for(let i = slider.min; i<slider.max+1; i+=slider.majorStep){
              if(slider.style == 'horizontal'){
                x.beginPath()
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * i
                Y = slider.posY - slider.height/2
                x.lineTo(X,Y)
                X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * i
                Y = slider.posY + slider.height/2
                x.lineTo(X,Y)
                Z = 1
                stroke(slider.tickColor,'',.1, true)
                x.fillStyle = slider.textColor
                x.textAlign = 'center'
                x.font = (slider.fontSize) + "px Courier Prime"
                x.fillText(i,X,Y+slider.height/2)
              }else{
              }
            }
            if(slider.style == 'horizontal'){
              x.beginPath()
              X = slider.posX - slider.width/2
              Y = slider.posY
              x.lineTo(X,Y)
              X = slider.posX + slider.width/2
              Y = slider.posY
              x.lineTo(X,Y)
              stroke(slider.tickColor,'',.1, true)
            }
            x.fillStyle = slider.textColor
            x.textAlign = 'left'
            x.font = (slider.fontSize*1.5) + "px Courier Prime"
            x.fillText(slider.caption + ' ' + slider.captionVar,slider.posX-slider.width/2,Y-slider.height/2-slider.fontSize/3)
            X = slider.posX - slider.width/2 + slider.width/(slider.max - slider.min) * eval(slider.valVariable)
            Y = slider.posY
            s = slider.height*1.5
            x.drawImage(burst,X-s/2,Y-s/2,s,s)
          }
        }
        
        oX=0, oY=0, oZ=10.5
        //Rl=0, Pt=S(t/4)/5, Yw=C(t/4)/5
        Rl=0, Pt=0, Yw=0
        
        x.globalAlpha = 1
        x.fillStyle='#101C'
        x.fillRect(0,0,c.width,c.height)
        x.lineJoin = x.lineCap = 'round'
        
        doKeys()
        
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
        x.textAlign = 'center'
        for(m=0;m<4;m++) {
          ofx = m%2 ? -8:8
          cl_ = m<2 ? cl : 2;
          ct=0;
          (f=m<2?frame:outerFrame).map((v, i) => {
            if(i && i%cl_ && (i/cl_|0)){
              x.beginPath()
              l = i
              X = f[l][0] + ofx
              Y = f[l][1]
              Z = f[l][2]
              R(Rl,Pt,Yw,1)
              if(Z>0) x.lineTo(...Q())
              l = i-1
              X = f[l][0] + ofx
              Y = f[l][1]
              Z = f[l][2]
              R(Rl,Pt,Yw,1)
              if(Z>0) x.lineTo(...Q())
              l = i-1-cl_
              X = f[l][0] + ofx
              Y = f[l][1]
              Z = f[l][2]
              R(Rl,Pt,Yw,1)
              if(Z>0) x.lineTo(...Q())
              l = i-cl_
              X = f[l][0] + ofx
              Y = f[l][1]
              Z = frame[l][2]
              R(Rl,Pt,Yw,1)
              if(Z>0){
                l = Q()
                x.lineTo(...l)
                stroke(m<2 ? '#0af3' : '#f088', m<2?'#0af1':'', m<2?1.5:4, true)
                if(showBoxIds&&m<2){
                  x.fillStyle = '#fff4'
                  x.font = (fs=400/Z)+'px Courier Prime'
                  x.fillText(ct, l[0]-fs/1.2, l[1]+fs*1.25)
                }
              }
              ct++
            }
          })
        }

        X = 0
        Y = -6
        Z = 0
        R(Rl,Pt,Yw,1)
        x.font = (fs=1000/Z)+"px Courier Prime"
        x.fillStyle = '#fff'
        x.textAlign = 'center'
        l = Q()
        x.fillText('puyo puyo',l[0],l[1]+fs/2.5)

        X = -4
        Y = -4
        Z = 0
        R(Rl,Pt,Yw,1)
        x.font = (fs=640/Z)+"px Courier Prime"
        x.textAlign = 'left'
        l = Q()
        x.fillText('◄ '+player1Name,l[0],l[1]+fs/2.5+fs)
        x.fillText('games won: '+score1,l[0],l[1]+fs/2.5+fs*2)
        x.fillText('total pcs: '+totalPcs1,l[0],l[1]+fs/2.5+fs*3)

        X = 4
        Y = 1
        Z = 0
        R(Rl,Pt,Yw,1)
        x.textAlign = 'right'
        l = Q()
        x.fillText(player2Name + ' ►',l[0],l[1]+fs/2.5+fs)
        x.fillText('games won: '+score2,l[0],l[1]+fs/2.5+fs*2)
        x.fillText('total pcs: '+totalPcs2,l[0],l[1]+fs/2.5+fs*3)
        
        if(gameInPlay && !((t*60|0)%(dropFreq/8|0))) {
          if(isOjama(B1)) dropB1()
          if(isOjama(B2)) dropB2()
        }
        if(gameInPlay && !((t*60|0)%dropFreq)) {
          if(!keys[40])  dropB1()
          if(0) dropB2()
        }
        
        
        if(gameInPlay) for(let j=2;j--;){
          syncSpeed = !j?(isOjama(B2)?.5+AISpeed/200*3:.5+AISpeed/200):(isOjama(B1)?1:Math.max(.4,1/(1+dropFreq/10)*1.2)*((keys[32]&&j)?3.5:(keys[40]?1.75:keys[38]?1.5:1)))
          ar = j?B1:B2
          if(ar.filter(v=>v).length) ar.map((v,i) => {
            for(m=v.length;m--;){
              X1 = v[m][0]
              Y1 = v[m][1]
              Z1 = v[m][2]
              X2 = v[m][4]
              Y2 = v[m][5]
              Z2 = v[m][6]
              vx = X1-X2
              vy = Y1-Y2
              vz = Z1-Z2
              d1 = Math.hypot(vx,vy,vz)+.001
              d2 = Math.min(syncSpeed, d1)
              vx /= d1
              vy /= d1
              vz /= d1
              vx *= d2
              vy *= d2
              vz *= d2
              X = v[m][4] += vx * syncSpeed
              Y = v[m][5] += vy * syncSpeed
              Z = v[m][6] += vz * syncSpeed
              R(Rl,Pt,Yw,1)
              if(Z>0){
                s = 650/Z
                l = Q()
                x.drawImage(puyos[v[m][3]].img,l[0]-s/2,l[1]-s/2,s,s)
              }
            }
          })
        }
        
        cuml = 0
        grav = .1
        for(let j=2;j--;){
          let ofx = 8 * (j?1:-1)
          let ofy = 0, cuml=0;
          rcip = j?B2RensaChainInProgress:B1RensaChainInProgress;
          for(i=(n=JSON.parse(JSON.stringify(j?P2:P1))).length;i--;){
            v=n[i]
            if(i<72 && v!=-1){
              l = 0
              dropping = false
              if(i<72 && rcip){
                for(m=i+6;m<72;m+=6) if((j?P2:P1)[m]==-1) dropping = true
              }
              if(dropping){
                l=(j?B2RensaOffsets:B1RensaOffsets)[i]+=grav
                if(l>=sp){
                  (j?B2RensaOffsets:B1RensaOffsets)[i] = 0;
                  if(j){
                    P2[i] = -1
                    P2[i+6] = v
                  }else{
                    P1[i] = -1
                    P1[i+6] = v
                  }
                }
              }else{
                if(j){
                  B2RensaOffsets[i] = 0
                }else{
                  B1RensaOffsets[i] = 0
                }
              }
              X = ((i%6)-2.5) * sp + ofx
              Y = ((i/6|0)-5.5) * sp + ofy + l
              Z = 0
              cuml += l
              R(Rl,Pt,Yw,1)
              if(Z>0){
                s = 650/Z
                l = Q()
                x.drawImage(puyos[v].img,l[0]-s/2,l[1]-s/2,s,s)
              }
            }
          }
          if(rcip){
            if(cuml<.1){
              if(j){
                B2RensaChainInProgress = false
                B2rensaChainLength = 0
                B2RensaOffsets = Array(6*14).fill(0)
                P1Ojama += B2rensaTally
              }else{
                B1RensaChainInProgress = false
                B1rensaChainLength = 0
                B1RensaOffsets = Array(6*14).fill(0)
                P2Ojama += B1rensaTally
              }
            }
          }
        }
        
        if(!B2alive && B1alive) doLose(0)
        if(!B1alive && B2alive) doLose(1)
        if(!B2alive && !B1alive) doLose(2)


        sparks = sparks.filter(v=>v[6]>0)
        sparks.map(v=>{
          X = v[0] += v[3]
          Y = v[1] += v[4]
          Z = v[2] += v[5]
          R(Rl,Pt,Yw,1)
          if(Z>0){
            l = Q()
            s = Math.min(1e3,2e3/Z*v[6])
            x.fillStyle = '#ff000005'
            x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            s/=3
            x.fillStyle = '#ff880020'
            x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
            s/=3
            x.fillStyle = '#ffffffff'
            x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
          }
          v[6]-=.05
        })
        
        if(0 && !AIMoveSelected) doAI()
        if(0 && gameInPlay && Rn()<AISpeed/100) dropB2()
        
        sliders.map(slider=>{
          drawSlider(slider)
        })
        
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
        if((none = typeof users == 'undefined') || users.length<2){
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
        users.map((user, idx) => {
          if((typeof Players != 'undefined') &&
             (l=Players.filter(v=>v.playerData.id == user.id).length)){
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
              //if(typeof score != 'undefined') {
              //  AI.score = score
              //  AI.playerData.score = score
              //  individualPlayerData['score'] = score
              //}
              
              if(typeof B1 != 'undefined'){
                if(B1.length){
                  individualPlayerData['B1'] = B1
                }
              }
              if(typeof P1 != 'undefined'){
                if(P1.length){
                  individualPlayerData['P1'] = P1
                }
              }
              
              if(typeof P2Ojama != 'undefined') {
                individualPlayerData['P2Ojama'] = P2Ojama
                P2Ojama = 0
              }


              //if(typeof score1 != 'undefined') individualPlayerData['score1'] = score1
              if(typeof score2 != 'undefined') individualPlayerData['score2'] = score2
              if(typeof totalPcs1 != 'undefined') individualPlayerData['totalPcs1'] = totalPcs1
              if(typeof totalPcs2 != 'undefined') individualPlayerData['totalPcs2'] = totalPcs2

              if(typeof B1alive != 'undefined') individualPlayerData['B1alive'] = B1alive
              if(typeof spawnSparksCmd != 'undefined') {
                individualPlayerData['spawnSparksCmd'] = JSON.parse(JSON.stringify(spawnSparksCmd))
                spawnSparksCmd = []
              }
              //if(typeof gameInPlay != 'undefined') individualPlayerData['gameInPlay'] = gameInPlay
              
              //if(typeof moves != 'undefined') individualPlayerData['moves'] = moves
              //if(typeof lastWinnerWasOp != 'undefined' && lastWinnerWasOp != -1) individualPlayerData['lastWinnerWasOp'] = lastWinnerWasOp
            }else{
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    
                    // straight mapping of incoming data <-> players

                    case 'score2': if(typeof el[key] != 'undefined') score1 = el[key]; break;
                    case 'totalPcs1': if(typeof el[key] != 'undefined') totalPcs2 = el[key]; break;
                    case 'B1':
                      if(typeof el[key] != 'undefined'){
                        console.log('b2', el[key])
                        cB2 = JSON.parse(JSON.stringify(B2))
                        B2 = el[key]
                        if(validB(B2)){
                          B2.map((n, m) => {
                            n.map((v, i) => {
                              if(typeof v != 'undefined' && v.length){
                                v[0] += 16
                                v[4] += 16
                                if(typeof cB2[m]    !== 'undefined' &&
                                   typeof cB2[m][i] !== 'undefined' && v[5]>=cB2[m][i][5]) {
                                     v[4] = cB2[m][i][4]
                                     v[5] = cB2[m][i][5]
                                     v[6] = cB2[m][i][6]
                                }
                              }
                            })
                          })
                        }
                      }
                    break;
                    case 'P1':
                      if(typeof el[key] != 'undefined'){
                        P2 = el[key]
                      }
                    break;
                    
                    case 'P2Ojama': if(typeof el[key] != 'undefined') P1Ojama += el[key]; break;
                    case 'B1alive': if(typeof el[key] != 'undefined') B2alive = el[key]; break;
                    //case 'gameInPlay': if(typeof el[key] != 'undefined') gameInPlay = el[key]; break;
                    case 'spawnSparksCmd': if(typeof el[key] != 'undefined') {
                      el[key].map(v=>{
                        v[0] += 16
                        spawnSparks(...v, 0)
                      })
                      break;
                    }
                  }
                })
              }
            }
          })
          for(i=0;i<Players.length;i++) if(Players[i]?.playerData?.id == userID) ofidx = i
        }
      }

      recData              = []
      lastWinnerWasOp = -1
      opIsX = true
      ofidx                = 0
      users                = []
      userID               = ''
      gameConnected        = false
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
            console.log('lastWinnerWasOp', lastWinnerWasOp)
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
                setInterval(()=>{sync()}, pollFreq = 333)  //ms
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
            console.log(data)
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
