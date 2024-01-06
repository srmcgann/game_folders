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
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
      }
      /* latin */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
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
        background-image: url(clippy.png);
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
          canvasRes = .25
          c.width = 1920*canvasRes
          c.height = 1080*canvasRes
          oX=oY=oZ=0
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

          M=Math
          A=M.atan2
          H=M.hypot

          R=R2=(Rl,Pt,Yw,m)=>{
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            X+=oX
            Y+=oY
            Z+=oZ
          }
          camR1=(Rl,Pt,Yw,oX=0,oY=0,oZ=0)=>{
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            X+=oX
            Y+=oY
            Z+=oZ
          }
          camR2=(Rl,Pt,Yw,oX=0,oY=0,oZ=0)=>{
            X+=oX
            Y+=oY
            Z+=oZ
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
          }
          perspective = 500 //450
          Q=()=>[c.width/2+X/Z*perspective*canvasRes,c.height/2+Y/Z*perspective*canvasRes]
          Q2=()=>[camBuffer.width/2+X/Z*perspective*canvasRes*2,camBuffer.height/2+Y/Z*perspective*canvasRes*2]
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
            for(let m=4;m--;){
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
              camBufferCtx.closePath()
              if(od) camBufferCtx.globalAlpha = .2*oga
              camBufferCtx.strokeStyle = scol
              camBufferCtx.lineWidth = Math.min(50,100*lwo/Z)
              if(od) camBufferCtx.stroke()
              camBufferCtx.lineWidth /= 4
              camBufferCtx.globalAlpha = 1*oga
              camBufferCtx.stroke()
            }
            if(fcol){
              camBufferCtx.globalAlpha = 1*oga
              camBufferCtx.fillStyle = fcol
              camBufferCtx.fill()
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

          G_ = 100000, iSTc = 1e3
          ST = Array(iSTc).fill().map(v=>{
            X = (Rn()-.5)*G_
            Y = (Rn()-.5)*G_
            Z = (Rn()-.5)*G_
            return [X,Y,Z]
          })

          burst = new Image()
          burst.src = "burst.png"

          starsLoaded = false, starImgs = [{loaded: false}]
          starImgs = Array(9).fill().map((v,i) => {
            let a = {img: new Image(), loaded: false}
            a.img.onload = () => {
              a.loaded = true
              setTimeout(()=>{
                if(starImgs.filter(v=>v.loaded).length == 9) starsLoaded = true
              }, 0)
            }
            a.img.src = `star${i+1}.png`
            return a
          })

          crosshair = new Image()
          crosshair.onload = () => {
            crosshairLoaded = true
          }
          crosshair.src = 'crosshair.png'
          
          grade = 2
          floor = (X, Z, lx=0, lz=0) => {
            ret = playerHeight - Math.hypot(lx, lz) * (1/grade) + (S(-X/10+t*2) + S(-Z/16+t)) * 5 //(S(-X/128+t/8) + S(-Z/64+t/4)) * 10
            return ret
          }

          init = () => { // called initially & when a player dies
            ot=-1
            objectiveTimer    = t + 10
            paused            = false
            health            = 1
            flymode           = false
            playerHeight      = 3
            jumpv = jumpvv    = 0
            lx_ = ly_ = lz_   = 0
            oXv = oYv = oZv   = 0
            Rlv = Ptv = Ywv   = 0
            oX = oZ           = 0
            jumpHeight        = -2.2
            oY                = floor(oX, oZ, oX, oZ) - playerHeight
            Rl = Pt = Yw      = 0
            grav              = .15
            grounded          = false
            hasTraction       = false
            keys              = Array(30).fill(0)
            based             = false
            alive             = true
            bgAlpha           = 0
            bgcol             = ''
            fl                = -floor(-oX,-oZ,-oX,-oZ)
          }
          init()

          PlayerInit = idx => { // called initially & when a player dies
            Players[idx].flymode          = false
            Players[idx].keys             = Array(30).fill(0)
            Players[idx].mbutton          = Array(3).fill(false)
            Players[idx].orbs             = []
            Players[idx].bases            = []
            Players[idx].mobile           = false
            Players[idx].accelr           = 0
            Players[idx].accelm           = 0
            Players[idx].jumpv            = 0
            Players[idx].jumpvv           = 0
            Players[idx].mv               = 0
            Players[idx].rv               = 0
            Players[idx].lx               = 0
            Players[idx].ly               = 0
            Players[idx].lz               = 0
            Players[idx].lx_              = 0
            Players[idx].ly_              = 0
            Players[idx].lz_              = 0
            Players[idx].kx               = 0
            Players[idx].ky               = 0
            Players[idx].kz               = 0
            Players[idx].health           = 1
            Players[idx].oXv              = 0
            Players[idx].oYv              = 0
            Players[idx].oZv              = 0
            Players[idx].Rlv              = 0
            Players[idx].Ptv              = 0
            Players[idx].Ywv              = 0
            Players[idx].toX              = 0
            Players[idx].toY              = 0
            Players[idx].toZ              = 0
            Players[idx].tRl              = 0
            Players[idx].tPt              = 0
            Players[idx].tYw              = 0
            Players[idx].oX               = S(p=Math.PI*2/PlayerCount*idx) * PlayerLs
            Players[idx].oZ               = C(p) * PlayerLs
            Players[idx].oY               = -floor(-Players[idx].oX, -Players[idx].oZ, -Players[idx].oX, -Players[idx].oZ) - playerHeight
            Players[idx].Rl               = 0
            Players[idx].Pt               = 0
            Players[idx].Yw               = p
            Players[idx].visited          = Array(3e3).fill(false)  // per AI
            Players[idx].jumpTimer        = 0
            Players[idx].playerShotTimer  = 0
            Players[idx].elevation        = 0
            Players[idx].grounded         = false
            Players[idx].hasTraction      = false
            Players[idx].based            = false
            Players[idx].alive            = true
            Players[idx].fl               = -floor(-Players[idx].oX,-Players[idx].oZ,-Players[idx].oX,-Players[idx].oZ)
            //console.log('****',Players[idx])
          }
          
          addPlayers = playerData => {
            PlayerLs = 1
            Players = [...Players, {score: 0, playerData}]
            PlayerCount++
            iCamsc = Players.length
            camsPersistent   = Array(iCamsc).fill().map(v=>{
              /* indices
              // 0 = shotTimer [float]
              // 1 = shooting [bool]
              */ 
              return [0, false]
            })
            PlayerInit(Players.length-1)
          }
          
          masterInit = () => { // called only initially
            cams             = []
            buttons          = []
            oldCams          = []
            hov              = false
            pointerLocked    = false
            crosshairLoaded  = false
            iCamsc           = 0
            buttonsLoaded    = false
            lerpFactor       = 1/10  //bigger val is faster tracking
            firstRun         = true
            camLength        = -10
            camFollowDist    = 166
            PlayerCount      = 0
            Players          = []
            camselected      = 0
            mv = rv          = .033
            alpha            = 0
            accellr = accelm = 1
            objectiveText    = 'collect the orbs'
            keymap           = Array(256).fill().map((key,idx)=>{
                                 key = null
                                 switch(idx){
                                   case 48: key = 0; break
                                   case 49: key = 1; break
                                   case 50: key = 2; break
                                   case 51: key = 3; break
                                   case 52: key = 4; break
                                   case 52: key = 5; break
                                   case 54: key = 6; break
                                   case 55: key = 7; break
                                   case 56: key = 8; break
                                   case 57: key = 9; break
                                   case 65: key = 10; break
                                   case 87: key = 11; break
                                   case 68: key = 12; break
                                   case 83: key = 13; break
                                   case 9:  key = 14; break
                                   case 16: key = 15; break
                                   case 17: key = 16; break
                                   case 18: key = 17; break
                                   case 80: key = 18; break
                                   case 66: key = 19; break
                                   case 67: key = 20; break
                                   case 77: key = 21; break
                                   case 84: key = 22; break
                                   case 37: key = 23; break
                                   case 38: key = 24; break
                                   case 39: key = 25; break
                                   case 40: key = 26; break
                                   case 32: key = 27; break
                                   case 33: key = 28; break
                                   case 34: key = 29; break
                                 }
                                 return key
                               })
                               /* legend
                                 0      = 0 / 48
                                 1      = 1 / 49
                                 2      = 2 / 50
                                 3      = 3 / 51
                                 4      = 4 / 52
                                 5      = 5 / 53
                                 6      = 6 / 54
                                 7      = 7 / 55
                                 8      = 8 / 56
                                 9      = 9 / 57
                                 A      = 10 / 65
                                 W      = 11 / 87
                                 D      = 12 / 68
                                 S      = 13 / 83
                                 TAB    = 14 / 9
                                 SHIFT  = 15 / 16
                                 CTRL   = 16 / 17
                                 ALT    = 17 / 18
                                 P      = 18 / 80
                                 B      = 19 / 66
                                 C      = 20 / 67
                                 M      = 21 / 77
                                 T      = 22 / 84
                                 LARR   = 23 / 37
                                 UARR   = 24 / 38
                                 RARR   = 25 / 39
                                 DARR   = 26 / 40
                                 SPC    = 27 / 32
                                 PGUP   = 28 / 33
                                 PGDN   = 29 / 34
                               */
            keys             = Array(30).fill(0)
            mbutton          = Array(3).fill(false)
            camMainScreen    = false
            showScores       = true
            playerSize       = 20
            camTgtIdx        = 0
            bulletDamage     = .1
            showcameras      = false
            showstars        = true
            showCamThumbs    = false
            showMenu         = false
            showTopo         = false
            showHealth       = true
            showPlayerStats  = true
            showCrosshair    = true
            showSkybox       = false
            visited          = Array(1e4).fill().map(v=>[false, alpha]) // env prop
            score    = 0
            camsPersistent   = Array(iCamsc).fill().map(v=>{
              /* indices
              // 0 = shotTimer [float]
              // 1 = shooting [bool]
              */ 
              return [0, false]
            })
          }
          masterInit()

          AIRightButton = Playerspacebar = idx =>{
            let AI = Players[idx]
            if(AI.flymode){
              vx  = -S(AI.Yw) * C(AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vy  = -S(AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vz  = -C(AI.Yw) * C(AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv -= vx
              AI.oYv -= vy
              AI.oZv -= vz
            }else if(AI.elevation == 0 || AI.jumpTimer<=t && AI.hasTraction){
              AI.jumpvv -= jumpHeight * (1+AI.accelm/2)
              AI.keys[27] = false
              AI.jumpTimer = t+.25
            }
          }

          AIleft = idx =>{
            AI = Players[idx]
            if(AI.keys[17]){
              if(AI.hasTraction || AI.flymode){
                vx  = S(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
                vy  = -S(-AI.Rl) * AI.mv * AI.accelm
                vz  = C(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
                AI.oXv += vx
                //if(AI.flymode) AI.oYv += vy
                AI.oZv += vz
              }
            }else{
              AI.Ywv+=AI.rv * AI.accelr
            }
          }
          AIup = idx =>{
            AI = Players[idx]
            AI.Ptv+=AI.rv * AI.accelr
          }
          AIright = idx =>{
            AI = Players[idx]
            if(AI.keys[17]){
              if(AI.hasTraction || AI.flymode){
                vx  = S(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
                vy  = -S(-AI.Rl) * AI.mv * AI.accelm
                vz  = C(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
                AI.oXv -= vx
                //if(AI.flymode) AI.oYv -= vy
                AI.oZv -= vz
              }
            }else{
              AI.Ywv-=rv * AI.accelr
            }
          }
          AIdown = idx =>{
            AI = Players[idx]
            AI.Ptv-=rv * AI.accelr
          }
          AIakey = idx =>{
            AI = Players[idx]
            if(AI.flymode){
              vx  = S(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
              vy  = -S(AI.Rl) * AI.mv * AI.accelm
              vz  = C(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oYv += vy
              AI.oZv += vz
            }else if(AI.hasTraction){
              vx  = S(-AI.Yw+Math.PI/2) * AI.mv * AI.accelm
              vy  = 0
              vz  = C(-AI.Yw+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oZv += vz
            }
          }
          AIwkey = idx =>{
            AI = Players[idx]
            if(AI.flymode){
              vx  = -S(-AI.Yw) * C(-AI.Pt) * AI.mv * AI.accelm
              vy  = S(AI.Pt) * AI.mv * AI.accelm
              vz  = -C(-AI.Yw) * C(-AI.Pt) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oYv += vy
              AI.oZv += vz
            }else if(AI.hasTraction){
              vx  = -S(-AI.Yw) * AI.mv * AI.accelm
              vy  = 0
              vz  = -C(-AI.Yw) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oZv += vz
            }
          }
          AIdkey = idx =>{
            AI = Players[idx]
            if(AI.flymode){
              vx  = -S(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
              vy  = S(AI.Rl) * AI.mv * AI.accelm
              vz  = -C(-AI.Yw+Math.PI/2) * C(-AI.Rl) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oYv += vy
              AI.oZv += vz
            }else if(AI.hasTraction){
              vx  = -S(-AI.Yw+Math.PI/2) * AI.mv * AI.accelm
              vy  = 0
              vz  = -C(-AI.Yw+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oZv += vz
            }
          }
          Playerskey = idx =>{
            AI = Players[idx]
            if(AI.flymode){
              vx  = S(-AI.Yw) * C(-AI.Pt) * AI.mv * AI.accelm
              vy  = -S(AI.Pt) * AI.mv * AI.accelm
              vz  = C(-AI.Yw) * C(-AI.Pt) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oYv += vy
              AI.oZv += vz
            }else if(AI.hasTraction){
              vx  = S(-AI.Yw) * AI.mv * AI.accelm
              vy  = 0
              vz  = C(-AI.Yw) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oZv += vz
            }
          }
          AIpgup = idx => {
            AI = Players[idx]
            if(AI.flymode){
              vx  = -S(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vy  = -S(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vz  = -C(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv -= vx
              AI.oYv -= vy
              AI.oZv -= vz
            }      
          }
          AIpgdn = idx => {
            AI = Players[idx]
            if(AI.flymode){
              vx  = S(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vy  = S(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vz  = C(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv -= vx
              if(AI.flymode) AI.oYv -= vy
              AI.oZv -= vz
            }      
          }

          AIleftButton = idx =>{
            AI = Players[idx]
            if(AI.flymode){
              vx  = -S(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vy  = -S(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              vz  = -C(-AI.Yw) * C(-AI.Pt+Math.PI/2) * AI.mv * AI.accelm
              AI.oXv += vx
              AI.oYv += vy
              AI.oZv += vz
            }else{
              shoot(true, idx)
            }
          }
          
          PlayerCtrl = idx =>{
            AI = Players[idx]
            shoot(true, idx)
          }
          
          document.onpointerlockchange = e => {
            pointerLocked = document.pointerLockElement == c
          }
          
          mx=my=0
          c.onmousemove = e => {
            hov = false
            if(pointerLocked){
              Ywv -= rv * accelr * e.movementX/9
              Ptv -= rv * accelr * e.movementY/9
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
          }
          
          c.onmouseup = e => {
            e.preventDefault()
            e.stopPropagation()
            c.focus()
            mbutton[e.button] = false
          }
         
          c.onmousedown = e => {
            e.preventDefault()
            e.stopPropagation()
            //c.requestFullscreen()
            mbutton[e.button] = true

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
            if(!hov) c.requestPointerLock()
          }
          //c.focus()
          
          c.onkeydown = e => {
            e.preventDefault()
            e.stopPropagation()
            keys[keymap[e.keyCode]] = 1
          }
          c.onkeyup = e => {
            e.preventDefault()
            e.stopPropagation()
            keys[keymap[e.keyCode]] = ''
            switch(e.keyCode){
              case 80: showTopo=!showTopo; break
              case 70: flymode=!flymode; break
              //case 76: camMainScreen = !camMainScreen; if(camMainScreen && cams.length>1) camselected = 2;  break
              case 9:
                paused=!paused;
                if(!paused) ot=-1
              break
              case 66: showCamThumbs=!showCamThumbs; break
              case 67: showcameras=!showcameras; break
              case 84: showCrosshair=!showCrosshair; break
              case 77: toggleMenu(); break
              case 27: if(showMenu && !paused) setTimeout(()=>{toggleMenu()},0); break
            }
          }
          left = () =>{
            if(keys[17]){
              if(hasTraction || flymode){
                vx  = S(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
                vy  = -S(-Rl) * mv * accelm
                vz  = C(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
                oXv += vx
                //if(flymode) oYv += vy
                oZv += vz
              }
            }else{
              Ywv+=rv * accelr
            }
          }
          up = () =>{
            Ptv+=rv * accelr
          }
          right = () =>{
            if(keys[17]){
              if(hasTraction || flymode){
                vx  = S(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
                vy  = -S(-Rl) * mv * accelm
                vz  = C(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
                oXv -= vx
                //if(flymode) oYv -= vy
                oZv -= vz
              }
            }else{
              Ywv-=rv * accelr
            }
          }
          down = () =>{
            Ptv-=rv * accelr
          }
          akey = () =>{
            if(flymode){
              vx  = S(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
              vy  = -S(Rl) * mv * accelm
              vz  = C(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
              oXv += vx
              oYv += vy
              oZv += vz
            }else if(hasTraction){
              vx  = S(-Yw+Math.PI/2) * mv * accelm
              vy  = 0
              vz  = C(-Yw+Math.PI/2) * mv * accelm
              oXv += vx
              oZv += vz
            }
          }
          wkey = () =>{
            if(flymode){
              vx  = -S(-Yw) * C(-Pt) * mv * accelm
              vy  = S(Pt) * mv * accelm
              vz  = -C(-Yw) * C(-Pt) * mv * accelm
              oXv += vx
              oYv += vy
              oZv += vz
            }else if(hasTraction){
              vx  = -S(-Yw) * mv * accelm
              vy  = 0
              vz  = -C(-Yw) * mv * accelm
              oXv += vx
              oZv += vz
            }
          }
          dkey = () =>{
            if(flymode){
              vx  = -S(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
              vy  = S(Rl) * mv * accelm
              vz  = -C(-Yw+Math.PI/2) * C(-Rl) * mv * accelm
              oXv += vx
              oYv += vy
              oZv += vz
            }else if(hasTraction){
              vx  = -S(-Yw+Math.PI/2) * mv * accelm
              vy  = 0
              vz  = -C(-Yw+Math.PI/2) * mv * accelm
              oXv += vx
              oZv += vz
            }
          }
          skey = () =>{
            if(flymode){
              vx  = S(-Yw) * C(-Pt) * mv * accelm
              vy  = -S(Pt) * mv * accelm
              vz  = C(-Yw) * C(-Pt) * mv * accelm
              oXv += vx
              oYv += vy
              oZv += vz
            }else if(hasTraction){
              vx  = S(-Yw) * mv * accelm
              vy  = 0
              vz  = C(-Yw) * mv * accelm
              oXv += vx
              oZv += vz
            }
          }
          pgup = () => {
            if(flymode){
              vx  = -S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              vy  = -S(-Pt+Math.PI/2) * mv * accelm
              vz  = -C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              oXv -= vx
              oYv -= vy
              oZv -= vz
            }      
          }
          pgdn = () => {
            if(flymode){
              vx  = S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              vy  = S(-Pt+Math.PI/2) * mv * accelm
              vz  = C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              oXv -= vx
              if(flymode) oYv -= vy
              oZv -= vz
            }      
          }
          
          shotFreq = 1/60*0
          iBulletv = 3
          shooting = false
          bullets = []
          playerShotTimer = 0
          shoot = (AI = false, idx) => {
            if(AI){
              AI = Players[idx]
              if(t>=AI.playerShotTimer){
                AI.playerShotTimer = t+shotFreq
                X = 0
                Y = 0
                Z = iBulletv
                camR1(-AI.Rl,-AI.Pt,-AI.Yw,0,0,0)
                vx = X
                vy = Y
                vz = Z
                X = -AI.oX + vx*5
                Y = -AI.oY + vy*5
                Z = -AI.oZ + vz*5
                fromCam = false
                bullets = [...bullets, [X, Y, Z, vx, vy, vz, 1, -1, idx, fromCam]]
              }
            }else{
              if(idx == -1){ //player
                if(t>=playerShotTimer){
                  playerShotTimer = t+shotFreq
                  X = 0
                  Y = 0
                  Z = iBulletv
                  camR1(-Rl,-Pt,-Yw,0,0,0)
                  vx = X
                  vy = Y
                  vz = Z
                  X = -oX + vx*5
                  Y = -oY + vy*5
                  Z = -oZ + vz*5
                  fromCam = false
                  bullets = [...bullets, [X, Y, Z, vx, vy, vz, 1, -1, idx, fromCam]]
                }
              } else {
                if(t>=camsPersistent[idx][0]){
                  camsPersistent[idx][0] = t+shotFreq
                  X = 0
                  Y = 0
                  Z = iBulletv
                  camR1(0,cams[idx][4],-cams[idx][5],0,0,0)
                  vx = X
                  vy = Y
                  vz = Z
                  X = cams[idx][0] - vx*camLength*1.02
                  Y = cams[idx][1] - vy*camLength*1.02
                  Z = cams[idx][2] - vz*camLength*1.02
                  fromCam = true
                  bullets = [...bullets, [X, Y, Z, vx, vy, vz, 1, idx, fromCam]]
                }
              }
            }
          }
          
          leftButton = () =>{
            if(flymode){
              vx  = -S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              vy  = -S(-Pt+Math.PI/2) * mv * accelm
              vz  = -C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              oXv += vx
              oYv += vy
              oZv += vz
            }else{
              if(!hov){
                shoot(false, -1)
                if(!pointerLocked){
                  keys[18] = mbutton[0] = false
                }
              }
            }
          }
          
          ctrl = () =>{
            shoot(false, -1)
            if(!pointerLocked){
              keys[18] = mbutton[0] = false
            }
          }

          jumpTimer = 0
          rightButton = spacebar = () =>{
            if(flymode){
              vx  = -S(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              vy  = -S(-Pt+Math.PI/2) * mv * accelm
              vz  = -C(-Yw) * C(-Pt+Math.PI/2) * mv * accelm
              oXv -= vx
              oYv -= vy
              oZv -= vz
            }else if(elevation == 0 || jumpTimer<=t && hasTraction){
              jumpvv -= jumpHeight * (1+accelm/2)
              keys[27] = false
              jumpTimer = t+.25
            }
          }
          
          doAI_keysim = () => {
            Players.map((AI, idx)=>{
              if(AI.alive){
                AI.accelr = AI.accelm = 1
                if(AI.keys[15]){
                  AI.accelm = 2.125
                  AI.accelr = 1.5
                }
                AI.rv    = .05
                AI.mv    = AI.flymode ? 5 : 2.5
                AI.keys.map((key, i)=>{
                  if(key) switch(i){
                    case 10: AIakey(idx); break
                    case 11: AIwkey(idx); break
                    case 12: AIdkey(idx); break
                    case 13: Playerskey(idx); break
                    case 27: Playerspacebar(idx); break
                    case 23: AIleft(idx); break
                    case 24: AIup(idx); break
                    case 25: AIright(idx); break
                    case 26: AIdown(idx); break
                    case 16: PlayerCtrl(idx); break
                    case 28: AIpgup(idx); break
                    case 29: AIpgdn(idx); break
                  }
                })
                if(AI.mbutton[0]) PlayerCtrl(idx)
              }
            })
          }
          
          doKeys = () => {
            accelr = accelm = 1
            if(keys[15]){
              accelm = 2.125
              accelr = 1.5
            }
            keys.map((key, i)=>{
              if(key) switch(i){
                case 0: camselected=0; break
                case 1: if(cams.length>0) camselected=1; break
                case 2: if(cams.length>1) camselected=2; break
                case 3: if(cams.length>2) camselected=3; break
                case 4: if(cams.length>3) camselected=4; break
                case 5: if(cams.length>4) camselected=5; break
                case 6: if(cams.length>5) camselected=6; break
                case 7: if(cams.length>6) camselected=7; break
                case 8: if(cams.length>7) camselected=8; break
                case 9: if(cams.length>8) camselected=9; break
                case 10: akey(); break
                case 11: wkey(); break
                case 12: dkey(); break
                case 13: skey(); break
                case 27: spacebar(); break
                case 23: left(); break
                case 24: up(); break
                case 25: right(); break
                case 26: down(); break
                case 16: ctrl(); break
                case 28: pgup(); break
                case 29: pgdn(); break
              }
            })
          }

          cl = 6
          rw = 1
          br = 6
          sp = 8
          grid = Array(cl*br*rw).fill().map((v, i) => {
            X = ((i%cl)-cl/2+.5)*sp
            Z = ((i/cl/rw|0)-br/2+.5)*sp
            Y = floor(X,Z,X,Z) - playerHeight * 4
            return [X,Y,Z]
          })
          
          resolution     = .15
          buffer         = document.createElement('canvas')
          buffer.width   = c.width*resolution | 0
          buffer.height  = c.height*resolution | 0
          bctx           = buffer.getContext('2d', {willReadFrequently: true})

          rg_cl = buffer.width
          rg_rw = buffer.height
          rg_br = 1
          rg_sp = 1
          rendergrid = Array(rg_cl*rg_br*rg_rw).fill().map((v, i) => {
            X = ((i%rg_cl)-rg_cl/2+.5)*rg_sp
            Y = ((i/rg_cl|0)-rg_rw/2+.5)*rg_sp
            Z = 0
            return [X, Y, Z]
          })
          
          kill = (AI=false, idx) => {
            if(AI){
              spawnFlash(-Players[idx].oX, -Players[idx].oY, -Players[idx].oZ)
              PlayerInit(idx)
            }else{
              spawnFlash(-oX, -oY, -oZ)
              init()
            }
          }
          
          fallingDeath = (AI=false, idx) => {
            if(AI){
              if(Players[idx].alive && !Players[idx].flymode){
                Players[idx].alive = false
                setTimeout(()=>{
                  kill(true, idx)
                }, 1000)
              }
            }else{
              if(alive && !flymode){
                alive = false
                bgcol = '#f00'
                setTimeout(()=>{
                  kill()
                },1000)
              }
            }
          }
          
          loadCams = () => {
            if(!firstRun) oldCams = JSON.parse(JSON.stringify(cams))
            cams = []
            let nm
            Array(iCamsc).fill().map((v,i) => {
              ls = camFollowDist
              if(firstRun || i>oldCams.length-1){
                X1 = S(p1=Math.PI*2/iCamsc*i+Math.PI/2 + Math.PI) * ls
                Y1 = -25
                Z1 = C(p1) * ls
              } else {
                X1 = oldCams[i][0]
                Y1 = oldCams[i][1]
                Z1 = oldCams[i][2]
              }
              switch(i){
                case 0:
                  a = S(p=Math.PI*2/iCamsc*i+t) * camFollowDist/1.5
                  e = C(p) * camFollowDist/1.5
                  d = Math.hypot(a,b=Y1-(-oY-camFollowDist/3),e)
                  a/=d
                  b/=d
                  e/=d
                  a*=camFollowDist
                  b*=camFollowDist
                  e*=camFollowDist
                  X1 += ((-oX+a) - X1)/50
                  Y1 += ((-oY-camFollowDist/3) - Y1)/25
                  Z1 += ((-oZ+e) - Z1)/50
                  p1 = Math.atan2(oX+X1, oZ+Z1) + C(t)/6 + Math.PI
                  p2 = -Math.PI/2+Math.acos((oY+Y1)/(Math.hypot(oX+X1, oY+Y1, oZ+Z1)+.001)) + S(t*4)/20
                  break
                default:
                  if(PlayerCount>0){
                    a = S(p=Math.PI*2/iCamsc*i+t) * camFollowDist/1.5
                    e = C(p) * camFollowDist/1.5
                    d = Math.hypot(a,b=Y1-(-oY-camFollowDist/3),e)
                    a/=d
                    b/=d
                    e/=d
                    a*=camFollowDist
                    b*=camFollowDist
                    e*=camFollowDist
                    l = (i+ofidx)%Players.length//Math.max(0,i-1)
                    tgx = Players[l].oX
                    tgy = Players[l].oY
                    tgz = Players[l].oZ
                    X1 += ((-tgx+a) - X1)/50
                    Y1 += ((-tgy-camFollowDist/3) - Y1)/25
                    Z1 += ((-tgz+e) - Z1)/50
                    p1 = Math.atan2(tgx+X1, tgz+Z1) + C(t)/6 + Math.PI
                    p2 = -Math.PI/2+Math.acos((tgy+Y1)/(Math.hypot(tgx+X1, tgy+Y1, tgz+Z1)+.001)) + S(t*4)/20
                  }
                  break
              }
              X2 = X1 - S(p1) * C(p2) * camLength
              Y2 = Y1 - S(p2) * camLength
              Z2 = Z1 - C(p1) * C(p2) * camLength
              let camBuffer = document.createElement('canvas')
              camBuffer.width = c.width
              camBuffer.height = c.height
              let camBufferCtx = camBuffer.getContext('2d')
              nm = Players[(i+ofidx)%Players.length]?.playerData?.name
              cams = [...cams, [X1, Y1, Z1, 0, p2, -p1, camBuffer, camBufferCtx, X2, Y2, Z2, nm]]
            })
            firstRun = false
          }
          
          renderButton = (text, X, Y, tooltip = '', callback='', typ='rectangle', col1='#0ff8', col2='#2088', fs=9) => {
            render = (menux>-menuWidth && tooltip!= 'show menu') || tooltip == 'copy game link' || (menux<0 && tooltip == 'show menu')
            if(render) {
              x.beginPath()
              x.fillStyle = '#4f8c'
            }
            x.font = fs + 'px Courier Prime'
            let margin = 2
            let w = x.measureText(text).width + margin*2
            let h = fs + margin*2
            X1=X-w/2,Y1=Y-h/2
            if(render || !buttonsLoaded){
              if(render){
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
                stroke(col1, col2, 1, true)
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

          menuWidth = 150
          menux = -menuWidth
          drawMenu = () => {
            x.textAlign = 'left'
            if(showMenu){
              if(menux<0){
                menux+=20
              }
            }else{
              if(menux>-menuWidth) menux-=20
            }
            x.beginPath()
            X = menux
            Y = 10
            x.lineTo(X, Y)
            sd = 50
            for(let i=0;i<sd;i++){
              blg = (i<sd/4 ? Math.min(25,Math.max(0, (S(Math.PI*2.25/sd*i+.72)*5.4)**2.25-14)) : 0)
              X = menuWidth + menux + blg
              Y = 10+(c.height-20)/sd*i
              x.lineTo(X, Y)
              blg = (i<sd/4 ? Math.min(25,Math.max(0, (S(Math.PI*2.25/sd*(i+1)+.72)*5.4)**2.25-14)) : 0)
              X = menuWidth + menux + blg
              Y = 10+(c.height-20)/sd*(i+1)
              x.lineTo(X, Y)
            }
            X = menux
            Y = c.height-10
            x.lineTo(X, Y)
            Z=10
            stroke('#0ff','#2088',1,true)
            
            X = menuWidth+menux+10
            Y = 40
            bct = 0  // must appear before 1st button (for callbacks/ clickability)
            renderButton('m', X, Y, 'show menu', 'toggleMenu()')
            renderButton('<<', X, Y, 'close menu', 'toggleMenu()')
            X = menux+75
            Y = 12
            renderButton('pause / unpause    [tab]', X, Y+=16, 'toggle paused / unpaused', 'paused=!paused')
            renderButton('menu                 [m]', X, Y+=16, 'toggle menu', 'toggleMenu()')
            renderButton('look    [arrows / mouse]', X, Y+=16, 'look around')
            renderButton('move              [WASD]', X, Y+=16, 'move')
            renderButton('choose cam    [num keys]', X, Y+=16, 'check on players')
            renderButton('restore player view  [0]', X, Y+=16, 'default camera', 'camselected=0')
            renderButton('jump  [space/rightMouse]', X, Y+=16, 'jump')
            renderButton('shoot   [ctrl/leftMouse]', X, Y+=16, 'shoot')
            renderButton('show/hide cam thumbs [b]', X, Y+=16, 'toggle camera previews')
            renderButton('topo map thumb       [P]', X, Y+=16, 'toggle topographical map')
            renderButton('flymode              [f]', X, Y+=16, 'toggle flight mode (zero gravity)')
            renderButton('show/hide crosshair  [t]', X, Y+=16, 'toggle scope')
            renderButton('show/hide cam rflctn.[c]', X, Y+=11, '[experimental]: see what cams see, on them', 'showCameras = !showCameras')
            renderButton('strafe         [alt+l/r]', X, Y+=16, 'lateral movement, if using arrow keys')
            renderButton('vertical     [pgup/pgdn]', X, Y+=16, 'vertical movement (only in flight mode)')
            renderButton('🔗', 50, 15, 'copy game link', 'fullCopy()', 'rectangle', '#0ff8', '#2088', 20)
          }
          
          toggleMenu = () =>{
            showMenu = !showMenu
            if(showMenu) {
              if(document.pointerLockElement == c) document.exitPointerLock()
            } else {
              if(document.pointerLockElement != c) c.requestPointerLock()
            }
          }
          
          plotCam = (frame=false, camidx) => {
            let X1
            let Y1
            let Z1
            let X2
            let Y2
            let Z2
            let X3
            let Y3
            let Z3
            let X4
            let Y4
            let Z4
            let renderFrame = showcameras //&& (camselected != camidx+1)
            a=[]
            if(renderFrame) camBufferCtx.beginPath()
            l = 0
            X1 = X = rendergrid[l][0]
            Y1 = Y = rendergrid[l][1]
            Z1 = Z = rendergrid[l][2]
            camR1(crl,cpt,cyw,cx2,cy2,cz2)
            a = [...a, [X,Y,Z]]
            if(renderFrame) {
              camFunc(Rl,Pt,Yw,oX,oY,oZ)
              if(Z>0) camBufferCtx.lineTo(...Qfunc())
            }
            l = rg_cl-1
            X2 = X = rendergrid[l][0]
            Y2 = Y = rendergrid[l][1]
            Z2 = Z = rendergrid[l][2]
            camR1(crl,cpt,cyw,cx2,cy2,cz2)
            a = [...a, [X,Y,Z]]
            if(renderFrame) {
              camFunc(Rl,Pt,Yw,oX,oY,oZ)
              if(Z>0) camBufferCtx.lineTo(...Qfunc())
            }
            l = rendergrid.length-1
            X3 = X = rendergrid[l][0]
            Y3 = Y = rendergrid[l][1]
            Z3 = Z = rendergrid[l][2]
            camR1(crl,cpt,cyw,cx2,cy2,cz2)
            a = [...a, [X,Y,Z]]
            if(renderFrame) {
              camFunc(Rl,Pt,Yw,oX,oY,oZ)
              if(Z>0) camBufferCtx.lineTo(...Qfunc())
            }
            l = rendergrid.length-rg_cl
            X4 = X = rendergrid[l][0]
            Y4 = Y = rendergrid[l][1]
            Z4 = Z = rendergrid[l][2]
            camR1(crl,cpt,cyw,cx2,cy2,cz2)
            a = [...a, [X,Y,Z]]
            if(renderFrame) {
              camFunc(Rl,Pt,Yw,oX,oY,oZ)
              if(Z>0) camBufferCtx.lineTo(...Qfunc())
              Z=10
            }
            
            //console.log('camselected',camselected)
            if(camselected != 0) cams.map((v,i) => {
              if(i!=camselected-1 && camidx==i){
                X = v[8]
                Y = v[9]
                Z = v[10]
                l = camselected-1
                //l = Math.max(0,camselected-2)
                cox_ = -cams[l][0]
                coy_ = -cams[l][1]
                coz_ = -cams[l][2]
                crl_ = -cams[l][3]
                cpt_ = -cams[l][4]
                cyw_ = cams[l][5]
                camFunc(crl_,cpt_,cyw_,cox_,coy_,coz_)
                if(Z>0) {
                  l = Qfunc()
                  s = Math.min(1e4, 20000/Z*canvasRes)
                  camBufferCtx.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                  s*=1.25
                  camBufferCtx.drawImage(starImgs[6].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
                  camBufferCtx.font = (fs=1e3/Z) + 'px Courier Prime'
                  camBufferCtx.fillStyle = '#4f8c'
                  camBufferCtx.fillText('cam: ' + cams[camidx][11], l[0],l[1]-fs)
                }
              }
            })      
            
            if(frame || !showcameras){
              camBufferCtx.globalAlpha = 1
              if(renderFrame) stroke('#40f2','#8002',.5,false)
              ax=ay=az=0
              a.map(q=>{
                ax+=q[0]
                ay+=q[1]
                az+=q[2]
              })
              ax /=4
              ay /=4
              az /=4
              bullets.map(q=>{
                X1_=q[0]
                Y1_=q[1]
                Z1_=q[2]
                if(Math.hypot(ax-X1_,ay-Y1_,az-Z1_)<20){
                  X2_=(X1_+q[3]*1)-q[3]*10
                  Y2_=(Y1_+q[4]*1)-q[4]*10
                  Z2_=(Z1_+q[5]*1)-q[5]*10
                  if((l=lineFaceI(X1_,Y1_,Z1_,X2_,Y2_,Z2_,a))){
                    q[6] = 0
                    spawnSparks(...l[0])
                  }
                }
              })
              X = cx = cx1
              Y = cy = cy1
              Z = cz = cz1
              if(camselected == 0){
                camFunc(Rl,Pt,Yw,oX,oY,oZ)
                if(Z>0){
                  camBufferCtx.textAlign = 'center'
                  l = Qfunc()
                  camBufferCtx.font = (fs=1e3/Z) + 'px Courier Prime'
                  camBufferCtx.fillStyle = '#4f8c'
                  camBufferCtx.fillText(cams[camidx][11], l[0],l[1]-fs)
                }
              }
              if(renderFrame) {
                
                if(0&&showcameras){
                  camBufferCtx.beginPath()
                  X = X1
                  Y = Y1
                  Z = Z1
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = X2
                  Y = Y2
                  Z = Z2
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = cx
                  Y = cy
                  Z = cz
                  //camR1(crl,cpt,cy2,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  Z=10
                  stroke('#40f2','#8002',.5,false)

                  camBufferCtx.beginPath()
                  X = X2
                  Y = Y2
                  Z = Z2
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = X3
                  Y = Y3
                  Z = Z3
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = cx
                  Y = cy
                  Z = cz
                  //camR1(crl,cpt,cy2,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  Z=10
                  stroke('#40f2','#8002',.5,false)

                  camBufferCtx.beginPath()
                  X = X3
                  Y = Y3
                  Z = Z3
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = X4
                  Y = Y4
                  Z = Z4
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = cx
                  Y = cy
                  Z = cz
                  //camR1(crl,cpt,cy2,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  Z=10
                  stroke('#40f2','#8002',.5,false)

                  camBufferCtx.beginPath()
                  X = X4
                  Y = Y4
                  Z = Z4
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = X1
                  Y = Y1
                  Z = Z1
                  camR1(crl,cpt,cyw,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  X = cx
                  Y = cy
                  Z = cz
                  //camR1(crl,cpt,cy2,cx2,cy2,cz2)
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0) camBufferCtx.lineTo(...Qfunc())
                  Z=10
                  stroke('#40f2','#8002',.5,false)
                  
                }
              }else{
                if(showcameras) stroke('','#1048')
              }
            }
          }
          
          processCams = () => {
            h = 150/1.77777777778
            cams.map((cam, idx) => {
              //if(camselected != idx+1 && !(showCamThumbs || showcameras)) return
              if(0&&Rn()<.1) camsPersistent[idx][1] = Rn()<.5 ? !camsPersistent[idx][1] : false
              //if(camsPersistent[idx][1]) shoot(false, idx)
              cx1 = cam[0]
              cy1 = cam[1]
              cz1 = cam[2]
              cx2 = cam[8]
              cy2 = cam[9]
              cz2 = cam[10]
              crl = cam[3]
              cpt = cam[4]
              cyw = -cam[5]
              if(showcameras){
                bctx.drawImage(cam[6], 0, 0, buffer.width, buffer.height)
                data = bctx.getImageData(0, 0, buffer.width, buffer.height)
              }
              ls = rg_sp*(.5**.5)
              if(0&&camselected==0) plotCam(false, idx)
                plotCam(true, idx)
              if(camselected==0){
                if(showcameras || showCamThumbs){
                  if(showcameras) rendergrid.map((v, i)=>{
                    l = data.data
                    k = i*4
                    red   = l[k+0]
                    green = l[k+1]
                    blue  = l[k+2]
                    alpha = l[k+3]
                    lum = (red+green+blue)/3
                    if(lum>3){
                      tx = v[0]
                      ty = v[1]
                      tz = v[2]
                      camBufferCtx.beginPath()
                      for(j=4;j--;){
                        X = tx + S(p=Math.PI*2/4*j+Math.PI/4)*ls
                        Y = ty + C(p)*ls
                        Z = 0
                        camR1(crl,cpt,cyw,cx2,cy2,cz2)
                        camFunc(Rl,Pt,Yw,oX,oY,oZ)
                        if(Z>0) camBufferCtx.lineTo(...Q())
                      }
                      col1 = ''
                      col2 = `rgba(${red},${green},${blue},.9)`
                      stroke(col1,col2)
                    }
                  })
                }
                if(!showcameras || camselected){
                  X = cx1
                  Y = cy1
                  Z = cz1
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0){
                    l = Q()
                    s = Math.min(1e4, 20000/Z*canvasRes)
                    camBufferCtx.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                    s*=1.25
                    camBufferCtx.drawImage(starImgs[6].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
                  }
                }
                
                //plotCam(true, idx)
                
                if(showCamThumbs && camselected == 0){
                  let ofx = -(idx/3|0) * 160
                  let margin = 5
                  camBufferCtx.globalAlpha = .85
                  camBufferCtx.textAlign = 'left'
                  camBufferCtx.strokeStyle ='#8fc4'
                  camBufferCtx.lineWidth   = 3
                  camBufferCtx.strokeRect(ofx + c.width-150-margin/2,margin/2 + (idx%3)*(h + margin),150,h)
                  camBufferCtx.drawImage(cam[6],ofx + c.width-150-margin/2, margin/2 + (idx%3)*(h + margin),150,h)
                  camBufferCtx.font = (fs = 12) + 'px Courier Prime'
                  camBufferCtx.fillStyle = '#fff'
                  camBufferCtx.fillText(cam[11], ofx + c.width-150, margin/2 + (idx%3)*(h + margin)+fs/1.5)
                  camBufferCtx.globalAlpha = 1
                }
              }else if(!showMenu && camselected == idx+1){
                camBufferCtx.textAlign = 'left'
                camBufferCtx.font = (fs=20) + 'px Courier Prime'
                camBufferCtx.fillStyle = '#4f8c'
                camBufferCtx.fillText('player cam: '+cam[11],fs*2,fs)
              }
            })
            if(showTopo && camselected == 0){
              let margin = 5
              camBufferCtx.globalAlpha = .85
              camBufferCtx.textAlign = 'left'
              camBufferCtx.strokeStyle ='#8fc4'
              camBufferCtx.lineWidth   = 3
              ypos = showCamThumbs ? cams.length : 0
              camBufferCtx.strokeRect(c.width-150-margin/2,margin/2 + ypos*(h + margin),150,h)
              camBufferCtx.drawImage(runTopo(oX/ksp*2,oY/ksp*2,oZ/ksp*2,Math.PI-Yw,-Math.PI/2+Pt/1.25-.5),c.width-150-margin/2,margin/2 + ypos*(h + margin),150,h)
              camBufferCtx.font = (fs = 12) + 'px Courier Prime'
              camBufferCtx.fillStyle = '#fff'
              camBufferCtx.fillText('topo map', c.width-150, margin/2 + ypos*(h + margin)+fs/1.5)
              camBufferCtx.globalAlpha = 1
            }
          }
          camselected = 0

          sparks = []
          iSparkv = .66
          spawnSparks = (X, Y, Z) => {
            for(m=50;m--;){
              v = iSparkv/2 + iSparkv / 2 * Rn()**.5
              vx = S(p=Math.PI*2*Rn())* S(q=Rn()<.5?Math.PI/2*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5) * v
              vy = C(q) * v
              vz = C(p) * S(q) * v
              sparks = [...sparks, [X, Y, Z, vx, vy, vz, 1]]
            }
          }

          flashes = []
          spawnFlash = (X,Y,Z) => {
            flashes = [...flashes, [X,Y,Z,1]]
          }

          mask = (testx, testy) => {
            let sp   = 1
            let cx   = 0
            let cy   = 0
            let ct   = 0
            let mode = 0
            let pivot = 1
            let ret = false
            for(let i=0; i<3e3; i++){
              if(ct==pivot-1){
                mode++
                pivot+=mode
              }
              ct++
              let ls = i
              let X = cx
              let Y = cy
              let Z = 0
              if(X==testx && Y==testy){
                ret = [true, i]
                break
              }
              switch(mode%4){
                case 1:
                  cx+=0
                  cy+=sp
                  break
                case 2:
                  cx-=sp
                  cy-=0
                  break
                case 3:
                  cx-=0
                  cy-=sp
                  break
                case 0:
                  cx+=sp
                  cy+=0
                  break
              }
            }
            return ret
          }
        
          topographicalMap = () => {
            let oX,oY,oZ
            let Rl,Pt,Yw,R,R2,X,Y,Z,d,Q,stroke,rw,cl,br,sp,ls,mode,pivot,ct
            let P,B,PX,PY,PZ,Ptheta,Pthetav,x,c,rv,mv,X1,Y1,Z1,X2,Y2,Z2,PXV,PYV,PZV
            let X3,Y3,Z3,X4,Y4,Z4,idxx,idxy,rate,midx,setup=false
            PX = PY = PZ = PXv = PYv = PZv = 0
            runTopo = (PX,PZ,PY,Ptheta,Pt) => {
              PY=0
              if(!setup){
                setup=true
                rate=82
                c = document.createElement('canvas')
                x = c.getContext('2d')
                c.width = 1920/4
                c.height = 1080/4
                Rl=0, Pt=1+Math.PI, Yw=0
                R=R2=(Rl,Pt,Yw,m)=>{
                  Z-=.75
                  X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
                  Z=C(p)*d
                  X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
                  Y=C(p)*d
                  Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
                  Z=C(p)*d
                  if(m){
                    X+=oX
                    Y+=oY
                    Z+=oZ
                  }
                }
                Q = () => [c.width/2+X/Z*700/4, c.height/2+Y/Z*700/4]

                stroke = (scol, fcol, lwo=1, od=true, oga=1) => {
                  if(scol){
                    x.closePath()
                    if(od) x.globalAlpha = .2*oga
                    x.strokeStyle = scol
                    x.lineWidth = Math.min(50,50*lwo/Z)
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

                rw=15
                cl=15
                sp=2
                B = Array(rw*cl).fill().map((v,i) => {
                  X = ((i%cl)-cl/2+.5)*sp
                  Y = ((i/cl|0)-rw/2+.5)*sp
                  Z = PZ
                  return [X,Y,Z]
                })

                P = Array(rw*cl).fill().map((v,i) => {
                  X = ((i%cl)-cl/2+.5)*sp
                  Y = ((i/cl|0)-rw/2+.5)*sp
                  Z = PZ
                  return [X,Y,Z]
                })
              }

              oX=0, oY=0, oZ=Math.min(6,Math.max(3,(.3-C(t))*10))

              x.globalAlpha = 1
              x.fillStyle='#000C'
              x.fillRect(0,0,c.width,c.height)
              x.lineJoin = x.lineCap = 'roud'
              
              ls=(sp*2**.5)/2

              B.map(v => {
                tx = v[0]
                ty = v[1]
                tz = v[2]
                while(tx-PX>15) tx = v[0]-=30
                while(tx-PX<-15) tx = v[0]+=30
                while(ty-PY>15) ty = v[1]-=30
                while(ty-PY<-15) ty = v[1]+=30

                x.beginPath()
                for(let j=4;j--;){
                  X = tx + S(p=Math.PI*2/4*j+Math.PI/4)*ls
                  Y = ty + C(p)*ls
                  Z = tz + v[2]
                  Z-=floor(X*ksp,Y*ksp,X*ksp,Y*ksp)/rate
                  X-=PX
                  Y-=PY
                  Z-=PZ
                  if(Math.hypot(X,Y)<10){
                    R(Rl,Pt,Yw,1)
                    if(Z>0) x.lineTo(...Q())
                  }
                }
                stroke('#40f3','', 2.5, false)
              })
              
              P.map((v, i) => {
                X = tx = v[0]
                Y = ty = v[1]
                Z = tz = v[2]
                while(X-PX>15) X = v[0]-=30
                while(X-PX<-15) X = v[0]+=30
                while(Y-PY>15) Y = v[1]-=30
                while(Y-PY<-15) Y = v[1]+=30
                if((midx=mask(-X/2, -Y/2))){
                  idxx = X/sp|0
                  idxy = Y/sp|0
                  Z-=floor(X*ksp,Y*ksp,X*ksp,Y*ksp)/rate-.5
                  x.beginPath()
                  a=[]
                  for(let j=4;j--;){
                    X = tx + S(p=Math.PI*2/4*j+Math.PI/4)*ls/1.5
                    Y = ty + C(p)*ls/1.5
                    Z = tz + v[2]
                    Z-=floor(X*ksp,Y*ksp,X*ksp,Y*ksp)/rate
                    a=[...a, [X,Y,Z]]
                    if(Math.hypot(X-PX,Y-PY)<10){
                      X-=PX
                      Y-=PY
                      Z-=PZ
                      R(Rl,Pt,Yw,1)
                      if(Z>0) x.lineTo(...Q())
                    }
                  }
                  col1 = ''
                  X1 = a[0][0]
                  Y1 = a[0][1]
                  X2 = a[1][0]
                  Y2 = a[1][1]
                  X3 = a[2][0]
                  Y3 = a[2][1]
                  X4 = a[3][0]
                  Y4 = a[3][1]
                  tst = PX>X1 && PX<X3 && PY>Y2 && PY<Y4
                  col1=''
                  col2 = tst ? '#0fa8' : '#4fa2'
                  stroke(col1, col2, 3, false)

                  if(!visited[midx[1]][0]){
                    X=tx
                    Y=ty
                    Z=tz + v[2]
                    Z-=floor(X*ksp,Y*ksp,X*ksp,Y*ksp)/rate-.25
                    if(Math.hypot(X-PX,Y-PY)<10){
                      X-=PX
                      Y-=PY
                      Z-=PZ
                      R(Rl,Pt,Yw,1)
                      if(Z>0){
                        l = Q()
                        s = Math.min(1e3,300/Z)
                        x.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                        //s*=1.5
                        //x.drawImage(starImgs[6].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
                      }
                    }
                  }
                  X=tx
                  Y=ty
                  Z=tz + v[2]
                  Z-=floor(X*ksp,Y*ksp,X*ksp,Y*ksp)/rate
                  if(Math.hypot(X-PX,Y-PY)<10){
                    X-=PX
                    Y-=PY
                    Z-=PZ
                    R(Rl,Pt,Yw,1)
                    if(Z>0){
                      l = Q()
                      x.textAlign='center'
                      x.font=(fs=250/Z)+'px Courier Prime'
                      x.fillStyle = '#fff'
                      x.fillText(midx[1]+1,l[0],l[1]-fs/45)
                      //x.fillText('x:'+(-idxx),l[0],l[1]-fs/45)
                      //x.fillText('z:'+(-idxy),l[0],l[1]-fs/45+fs)
                    }
                  }
                }
              })

              s=15
              x.fillStyle='#0fb4'
              x.fillRect(c.width/2-s/2,c.height/2-s/2,s,s)
              s/2
              x.fillStyle='#fff8'
              x.fillRect(c.width/2-s/2,c.height/2-s/2,s,s)
              s/2
              //x.drawImage(crosshair,c.width/2-100,c.height/2-100,200,200)
              Rl = -Ptheta
              return c
            }
          }
          
          topographicalMap()
          
          processPlayers = (AI=false, idx) => {
            
            if(AI){

              AI = Players[idx]
              if(+AI.playerData.id == +userID) return
              AI.Rl += (AI.tRl - AI.Rl) * lerpFactor
              AI.Pt += (AI.tPt - AI.Pt) * lerpFactor
              AI.Yw += (AI.tYw - AI.Yw) * lerpFactor

              //AI.Rl += AI.Rlv
              //AI.Pt += AI.Ptv
              //AI.Yw += AI.Ywv
              if(AI.Pt>Math.PI/2){
                AI.Pt = Math.PI/2
                AI.Ptv = 0
              }
              if(AI.Pt<-Math.PI/2){
                AI.Pt = -Math.PI/2
                AI.Ptv = 0
              }
              AI.Rlv/=1.5
              AI.Ptv/=1.5
              AI.Ywv/=1.5
              
              if(AI.hasTraction || AI.flymode){
                AI.oXv/=2.5
                AI.oYv/=2.5
                AI.oZv/=2.5
              }
              AI.jumpv += AI.jumpvv
              AI.jumpv/=1.5
              AI.jumpvv/=1.05
              AI.oY += AI.jumpv
              if(!AI.flymode && AI.based) {
                if(AI.oY<(AI.fl+playerHeight)-5){
                  AI.oYv += 2
                }else{
                  
                  AI.oYv=0
                  AI.oY = Math.max(AI.fl+playerHeight, AI.oY)
                }
              }
              AI.elevation       = Math.abs((AI.fl+playerHeight)-AI.oY)
              AI.grounded        = AI.based && AI.elevation < .1
              AI.hasTraction     = AI.based && AI.elevation < .5
              if(AI.grounded){
                AI.jumpv = AI.jumpvv = 0
              }
              //if(!AI.grounded && !AI.flymode) AI.jumpvv-=grav
              if(AI.oY-AI.fl<-200) fallingDeath(true, idx)

              AI.oX += (AI.toX - AI.oX) * lerpFactor
              AI.oY += (AI.toY - AI.oY) * lerpFactor // 5
              AI.oZ += (AI.toZ - AI.oZ) * lerpFactor
              //AI.oX += AI.oXv
              //AI.oY += AI.oYv
              //AI.oZ += AI.oZv
              AI.fl = -floor(-AI.oX,-AI.oZ,-AI.oX,-AI.oZ)
              
              X1 = -AI.oX
              Y1 = -AI.oY
              Z1 = -AI.oZ
              
              bullets.map((v,i) => {
                if(v[8] != idx) {
                  X2 = v[0]
                  Y2 = v[1]
                  Z2 = v[2]
                  if(AI.alive && (d = Math.hypot(X1-X2,Y1-Y2,Z1-Z2)<20)){
                    AI.health -= bulletDamage
                    if(AI.alive && AI.health<=0) kill(true, idx)
                    v[6] = 0
                    spawnSparks(X2,Y2,Z2)
                  }
                }
              })
            }else{
              Rl += Rlv
              Pt += Ptv
              Yw += Ywv
              if(Pt>Math.PI/2){
                Pt = Math.PI/2
                Ptv = 0
              }
              if(Pt<-Math.PI/2){
                Pt = -Math.PI/2
                Ptv = 0
              }
              Rlv/=1.5
              Ptv/=1.5
              Ywv/=1.5
              
              if(hasTraction || flymode){
                oXv/=2.5
                oYv/=2.5
                oZv/=2.5
              }
              jumpv += jumpvv
              jumpv/=1.5
              jumpvv/=1.05
              oY += jumpv
              if(!flymode && based) {
                if(oY<(fl+playerHeight)-5){
                  oYv += 2
                }else{
                  oYv=0
                  oY = Math.max(fl+playerHeight, oY)
                }
              }
              elevation       = Math.abs((fl+playerHeight)-oY)
              grounded        = based && elevation < .1
              hasTraction     = based && elevation < .5
              if(grounded){
                jumpv = jumpvv = 0
              }
              if(!grounded && !flymode) jumpvv-=grav
              if(oY-fl<-200) fallingDeath()

              oX += oXv
              oY += oYv
              oZ += oZv

              c.width = 1920*canvasRes
              c.height = 1080*canvasRes
              camBufferCtx = x
              camBufferCtx.globalAlpha = 1
              camBufferCtx.fillStyle='#000'
              camBufferCtx.fillRect(0,0,c.width,c.height)
              camBufferCtx.lineJoin = camBufferCtx.lineCap = 'roud'
              camBufferCtx.textAlign = 'center'
              if(bgcol){
                camBufferCtx.globalAlpha = bgAlpha += alive? 0 : .05
                camBufferCtx.fillStyle = bgcol
                camBufferCtx.fillRect(0,0,c.width,c.height)
              }
              camBufferCtx.globalAlpha = 1

              camFunc = camR2
              Qfunc = Q

              playercam = true
              if(camselected != 0){
                cox = -cams[camselected-1][0]
                coy = -cams[camselected-1][1]
                coz = -cams[camselected-1][2]
                crl = -cams[camselected-1][3]
                cpt = -cams[camselected-1][4]
                cyw = cams[camselected-1][5]
              }else{
                cox = oX
                coy = oY
                coz = oZ
                crl = Rl
                cpt = Pt
                cyw = Yw
              }
              fl = -floor(-oX,-oZ,-oX,-oZ)
              
              X1 = -oX
              Y1 = -oY
              Z1 = -oZ
              
              bullets.map((v,i) => {
                if(v[8] != -1){
                  X2 = v[0]
                  Y2 = v[1]
                  Z2 = v[2]
                  if(alive && (d = Math.hypot(X1-X2,Y1-Y2,Z1-Z2)<20)){
                    health -= bulletDamage
                    if(alive && health<=0) kill()
                    v[6] = 0
                    spawnSparks(X2,Y2,Z2)
                  }
                }
              })
              
            }
          }
          
        }
        
        if(!paused){
          rv    = .05
          mv    = flymode ? 5 : 2.5
          if(mbutton[0]) leftButton()
          if(mbutton[2]) rightButton()
          doKeys()
          doAI_keysim()
          loadCams()
          bases = []
          Players.map((AI, idx)=>{
            AI.bases = []
            AI.orbs = []
          })
          for(let i_ = 0; i_ < cams.length+1; ++i_){
            switch(i_) {
              case cams.length: // player view
              
                processPlayers()
                Players.map((AI, idx) => {
                  processPlayers(true, idx)
                })
                
              break
              default: // cameras
                Qfunc = Q2
                camFunc = camR2
                camBuffer = cams[i_][6]
                camBufferCtx = cams[i_][7]
                camBufferCtx.globalAlpha = 1
                camBufferCtx.fillStyle='#000'
                camBufferCtx.fillRect(0,0,c.width,c.height)
                camBufferCtx.lineJoin = camBufferCtx.lineCap = 'roud'
                cox = -cams[i_][0]
                coy = -cams[i_][1]
                coz = -cams[i_][2]
                crl = -cams[i_][3]
                cpt = -cams[i_][4]
                cyw = cams[i_][5]
                playercam = false
              break
            }
            
            camBufferCtx.globalAlpha = 1
            
            if(showSkybox) {
              if(typeof skybox == 'undefined') skybox = geoSphere(0, 0, 0, 750, 10000)
              skybox[4].map(v=>{
                X = v[0]
                Y = v[1]
                Z = v[2]
                camFunc(crl,cpt,cyw,cox,coy,coz)
                if(Z>0){
                  s = Math.min(1e3, 2e6/Z*canvasRes)
                  p1 = Math.atan2(v[0],v[2])
                  p2 = Math.acos(v[1]/(Math.hypot(...v)+.001))
                  camBufferCtx.fillStyle = `hsla(${360/Math.PI*p2+t*300},99%,${50+S(p2*6+t*10+Math.PI*2*p1/2)*50}%,.06)`
                  l = Qfunc()
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                }
              })
            }
            if(showstars) ST.map(v=>{
              X = v[0]
              Y = v[1]
              Z = v[2]
              camFunc(crl,cpt,cyw,cox,coy,coz)
              if(Z>0){
                if((camBufferCtx.globalAlpha = Math.min(1,(Z/5e3)**2))>.1){
                  s = Math.min(1e3, 8e5/Z*canvasRes)
                  camBufferCtx.fillStyle = '#ffffff04'
                  l = Qfunc()
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  s/=5
                  camBufferCtx.fillStyle = '#fffa'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                }
              }
            })
            camBufferCtx.globalAlpha = 1
       
            ls = sp*(.5**.5)
            ksp = 1.75 * sp * cl
            pcl = 5
            prw = 1
            pbr = 5
            baseHeight = 15
            renderDist = (ksp*(pcl/4+1))/1.25
            maxCt = 4*cl
            for(k=prw*pcl*pbr;k--;){
              kx = (lx_=lx=(k%pcl)-pcl/2+.5)*ksp
              kz = (ly_=lz=(k/pcl/prw|0)-pbr/2+.5)*ksp

              if(showcameras || i_==cams.length || showCamThumbs){
                Players.map((AI,idx) => {
                  if(+AI.playerData.id == +userID) return
                  AI.lx = lx
                  AI.lz = lz
                  AI.kx = kx
                  AI.kz = kz
                  while(AI.kz+AI.oZ<-ksp*pbr/2){
                    AI.lz+=pbr
                    AI.kz+=ksp*pbr
                  }
                  while(AI.kz+AI.oZ>ksp*pbr/2){
                    AI.lz-=pbr
                    AI.kz-=ksp*pbr
                  }
                  while(AI.kx+AI.oX<-ksp*pcl/2){
                    AI.lx+=pcl
                    AI.kx+=ksp*pcl
                  }
                  while(AI.kx+AI.oX>ksp*pcl/2){
                    AI.lx-=pcl
                    AI.kx-=ksp*pcl
                  }
                  if(midx=mask(AI.lx,AI.lz)){

                    AI.ky = AI.ly = floor(AI.lx,AI.lz,AI.lx,AI.lz) + (((k/pcl|0)%prw)-prw/2+.5)*ksp
                    AI.lx_ = AI.kx * .988
                    AI.lz_ = AI.kz * .988
                    //if(i_==cams.length){
                      X = AI.kx
                      Z = AI.kz
                      Y1 = (Y = floor(X,Z,AI.lx_=X,AI.lz_=Z)-7.5)-2+7.5
                      X -= 0//20
                      AI.lx_ *= .988
                      AI.lz_ *= .988
                    //}
                    tcp = camBufferCtx.lineJoin = camBufferCtx.lineCap
                    camBufferCtx.lineJoin = camBufferCtx.lineCap = 'roud'
                    camBufferCtx.globalAlpha = 1

                    for(let n=3;n--;){
                      camBufferCtx.beginPath()
                      X = S(p=Math.PI*2/3*n+t*4)*playerSize/3
                      Y = C(p)*playerSize/3
                      Z = -playerSize/3
                      camR1(0,-AI.Pt,-AI.Yw)
                      X -= AI.oX
                      Y -= AI.oY
                      Z -= AI.oZ
                      camFunc(crl,cpt,cyw,cox,coy,coz)
                      if(Z>0){
                        l=Qfunc()
                        camBufferCtx.lineTo(...l)
                        X = S(p=Math.PI*2/3*(n+1)+t*4)*playerSize/3
                        Y = C(p)*playerSize/3
                        Z = -playerSize/3
                        camR1(0,-AI.Pt,-AI.Yw)
                        X -= AI.oX
                        Y -= AI.oY
                        Z -= AI.oZ
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0){
                          l=Qfunc()
                          camBufferCtx.lineTo(...l)
                          X = 0
                          Y = 0
                          Z = playerSize*.75
                          camR1(0,-AI.Pt,-AI.Yw)
                          X -= AI.oX
                          Y -= AI.oY
                          Z -= AI.oZ
                          camFunc(crl,cpt,cyw,cox,coy,coz)
                          if(Z>0){
                            l=Qfunc()
                            camBufferCtx.lineTo(...l)
                            stroke('','#0f81',1,true)
                          }
                        }
                      }
                    }
                    camBufferCtx.lineJoin = camBufferCtx.lineCap = tcp
                    camBufferCtx.globalAlpha = 1
                    ct = 0

                    if(i_ == cams.length) {
                      if(i_ == cams.length) a_ = []
                      for(let j=4;j--;){
                        ls_ = ls*cl
                        X1 = AI.kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_
                        Z1 = AI.kz + C(p)*ls_
                        X2 = AI.kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_
                        Z2 = AI.kz + C(p)*ls_
                        if(i_ == cams.length) a_ = [...a_, [X1,Y1,Z1]]
                      }
                      if(i_ == cams.length) AI.bases = [...AI.bases, [a_, visited[midx[1]][0], midx[1]]]

                      if(i_ == cams.length && !visited[midx[1]][0]){   // orbs
                        X = AI.lx_
                        Z = AI.lz_
                        Y = floor(X,Z,X,Z)-7.5
                        AI.orbs = [...AI.orbs, [X,Y,Z]]
                      }
                    }


                    if(i_==cams.length){
                      X = AI.kx
                      Z = AI.kz
                      Y1 = (Y = floor(X,Z,AI.lx_=X,AI.lz_=Z)-7.5)-2+7.5
                      if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*10){
                        //visited[midx[1]][1] = .5 / (1+(1+d_)**8/1e16)
                        if(!visited[midx[1]][0] && d_<24){
                          visited[midx[1]][0]=true
                          //AI.score++
                          spawnFlash(X,Y+16,Z)
                        }
                      }
                    }

                    needsRender  = Math.hypot(oX-AI.oX,oY-AI.oY,oZ-AI.oZ)>renderDist*1
      /*******/

                    if(needsRender) {
                      
                      
                      ct = 0
                      for(let j=4;j--;){
                        ls_ = ls*cl
                        X1 = AI.kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_
                        Z1 = AI.kz + C(p)*ls_
                        X2 = AI.kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_
                        Z2 = AI.kz + C(p)*ls_
                        for(let m=cl;m--;){
                          camBufferCtx.beginPath()
                          X = X1 + (X2-X1) / cl * m
                          Z = Z1 + (Z2-Z1) / cl * m
                          Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9
                          if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                            camFunc(crl,cpt,cyw,cox,coy,coz)
                            if(Z>0)camBufferCtx.lineTo(...Qfunc())
                            X = X1 + (X2-X1) / cl * (m+1)
                            Z = Z1 + (Z2-Z1) / cl * (m+1)
                            Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9
                            if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                              camFunc(crl,cpt,cyw,cox,coy,coz)
                              if(Z>0) camBufferCtx.lineTo(...Qfunc())
                              alpha = .85 / (1+(1+d_)**8/1e15)
                              if(alpha > .1) stroke(`hsla(${360/maxCt*ct+t*1e3},99%,${500+S(t)*450}%,${alpha})`,'',3,true)
                            }
                          }
                          ct++
                        }
                      }
                      
                      cl_=(cl/2|0)//-1
                      for(let n=3;n--;) for(j=4;j--;){
                        ls2a = 1/(1+n/2)
                        ls2b = 1/((3+n)/2)
                        X1a = AI.kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_ * ls2a
                        Z1a = AI.kz + C(p)*ls_ * ls2a
                        X2a = AI.kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_ * ls2a
                        Z2a = AI.kz + C(p)*ls_ * ls2a
                        X1b = AI.kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_ * ls2b
                        Z1b = AI.kz + C(p)*ls_ * ls2b
                        X2b = AI.kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_ * ls2b
                        Z2b = AI.kz + C(p)*ls_ * ls2b
                        for(let m=cl_;m--;){
                          a=[]
                          camBufferCtx.beginPath()
                          X = X1a + (X2a-X1a) / cl_ * m
                          Z = Z1a + (Z2a-Z1a) / cl_ * m
                          Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9 + baseHeight * n
                          a=[...a, [X,Y,Z]]
                          if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                            camFunc(crl,cpt,cyw,cox,coy,coz)
                            if(Z>0) camBufferCtx.lineTo(...Qfunc())
                          }
                          X = X1a + (X2a-X1a) / cl_ * (m+1)
                          Z = Z1a + (Z2a-Z1a) / cl_ * (m+1)
                          Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9+ baseHeight * n
                          a=[...a, [X,Y,Z]]
                          if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                            camFunc(crl,cpt,cyw,cox,coy,coz)
                            if(Z>0) camBufferCtx.lineTo(...Qfunc())
                          }
                          X = X1b + (X2b-X1b) / cl_ * (m+1)
                          Z = Z1b + (Z2b-Z1b) / cl_ * (m+1)
                          Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9 + baseHeight * (n+1)
                          a=[...a, [X,Y,Z]]
                          if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                            camFunc(crl,cpt,cyw,cox,coy,coz)
                            if(Z>0) camBufferCtx.lineTo(...Qfunc())
                          }
                          X = X1b + (X2b-X1b) / cl_ * m
                          Z = Z1b + (Z2b-Z1b) / cl_ * m
                          Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 3.9 + baseHeight * (n+1)
                          a=[...a, [X,Y,Z]]
                          if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*1.01){
                            camFunc(crl,cpt,cyw,cox,coy,coz)
                            if(Z>0) camBufferCtx.lineTo(...Qfunc())
                          }
                          alpha = .5 / (1+(1+d_)**8/1e16) / (1+n*3)
                          col1 = alpha > .05 ? `hsla(${-150},99%,60%,${alpha/1.25})` : ''
                          col2 = `hsla(${-100},99%,50%,${alpha})`
                          if(alpha>.02){
                            stroke(col1,col2,3,false)
                            ax=ay=az=0
                            a.map(q=>{
                              ax+=q[0]
                              ay+=q[1]
                              az+=q[2]
                            })
                            ax /=4
                            ay /=4
                            az /=4
                            bullets.map(q=>{
                              X1=q[0]
                              Y1=q[1]
                              Z1=q[2]
                              if(Math.hypot(ax-X1,ay-Y1,az-Z1)<10){
                                X2=(X1+q[3]*1)-q[3]*10
                                Y2=(Y1+q[4]*1)-q[4]*10
                                Z2=(Z1+q[5]*1)-q[5]*10
                                if((l=lineFaceI(X1,Y1,Z1,X2,Y2,Z2,a))){
                                  q[6] = 0
                                  spawnSparks(...l[0])
                                }
                              }
                            })
                          }
                        }
                      }
                      
                      grid.map((v, i) => {
                        if((i+(i/cl|0))%2){
                          tx = -v[0] + AI.kx
                          ty = -v[1] + AI.ky
                          tz = -v[2] + AI.kz
                          camBufferCtx.beginPath()
                          for(j=4;j--;){
                            X = tx + S(p=Math.PI*2/4*j+Math.PI/4)*ls
                            Z = tz + C(p)*ls
                            Y = AI.ky + floor(X,Z,AI.lx_,AI.lz_) + playerHeight * 4
                            if((d_=Math.hypot(X+AI.oX,(Y+AI.oY)/4,Z+AI.oZ))<renderDist){
                              camFunc(crl,cpt,cyw,cox,coy,coz)
                              if(Z>0) camBufferCtx.lineTo(...Qfunc())
                            }
                          }
                          alpha = .75 / (1+(1+d_)**8/1e16)
                          col1=''
                          col2=`hsla(${Math.hypot(tx+AI.oX,tz+AI.oZ)*3-t*100},99%,${(i+(i/cl|0))%2?15:50}%,${alpha})`
                          if(alpha > .05) stroke(col1,col2)
                        }
                      })
                      if(1||i_==cams.length){
                        X = AI.kx
                        Z = AI.kz
                        Y1 = (Y = floor(X,Z,AI.lx_=X,AI.lz_=Z)-7.5)-2+7.5
                        if((d_=Math.hypot(X+AI.oX, (Y+AI.oY)/4, Z+AI.oZ))<renderDist*10){
                          visited[midx[1]][1] = d_
                          if(!visited[midx[1]][0] && d_<24){
                            visited[midx[1]][0]=true
                            collected[midx[1]] = '1'
                            score++
                            spawnFlash(X,Y+16,Z)
                          }
                          camFunc(crl,cpt,cyw,cox,coy,coz)
                          if(Z>0){
                            alpha = .9 / (1+(1+d_)**8/1e18)
                            if(alpha>.05){
                              l=Qfunc()
                              camBufferCtx.globalAlpha = alpha
                              if(!visited[midx[1]][0]){
                                s = Math.min(1e4, 1e4/Z)
                                x.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                              }
                              camBufferCtx.font = (fs=2e3/Z) + 'px Courier Prime'
                              camBufferCtx.fillStyle = '#fff'
                              camBufferCtx.fillText(midx[1]+1,l[0],l[1]+fs/Z*30)
                              //camBufferCtx.fillText('X:' + lx,l[0],l[1])
                              //camBufferCtx.fillText('Y:' + Math.round(-AI.ly*grade + playerHeight * 4-2),l[0],l[1]+fs*1)
                              //camBufferCtx.fillText('Z:' + lz,l[0],l[1]+fs*1)
                            }
                          }
                        }
                      }
                    }
                    camBufferCtx.globalAlpha = 1
                    
      /*******/
               
                  }
                })
              }

              while(kz+oZ<-ksp*pbr/2){
                lz+=pbr
                kz+=ksp*pbr
              }
              while(kz+oZ>ksp*pbr/2){
                lz-=pbr
                kz-=ksp*pbr
              }
              while(kx+oX<-ksp*pcl/2){
                lx+=pcl
                kx+=ksp*pcl
              }
              while(kx+oX>ksp*pcl/2){
                lx-=pcl
                kx-=ksp*pcl
              }
              if(midx=mask(lx,lz)){
                X = kx
                Z = kz
                Y1 = (Y = floor(X,Z,lx_=X,lz_=Z)-7.5)-2+7.5
                d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ)
                if(d_<renderDist*10){
                  ky = ly = floor(lx,lz,lx,lz) + (((k/pcl|0)%prw)-prw/2+.5)*ksp
                  lx_ = kx * .988
                  lz_ = kz * .988
                  if(i_==cams.length){
                    X = kx
                    Z = kz
                    Y1 = (Y = floor(X,Z,lx_=X,lz_=Z)-7.5)-2+7.5
                    X -= 0//20
                    lx_ *= .988
                    lz_ *= .988
                  }
                  tcp = camBufferCtx.lineJoin = camBufferCtx.lineCap
                  camBufferCtx.lineJoin = camBufferCtx.lineCap = 'roud'
                  camBufferCtx.globalAlpha = 1
                  for(n=3;n--;){
                    camBufferCtx.beginPath()
                    X = S(p=Math.PI*2/3*n+t*4)*playerSize/3
                    Y = C(p)*playerSize/3
                    Z = -playerSize/3
                    camR1(0,-Pt,-Yw)
                    X -= oX
                    Y -= oY
                    Z -= oZ
                    camFunc(crl,cpt,cyw,cox,coy,coz)
                    if(Z>0){
                      l=Qfunc()
                      camBufferCtx.lineTo(...l)
                      X = S(p=Math.PI*2/3*(n+1)+t*4)*playerSize/3
                      Y = C(p)*playerSize/3
                      Z = -playerSize/3
                      camR1(0,-Pt,-Yw)
                      X -= oX
                      Y -= oY
                      Z -= oZ
                      camFunc(crl,cpt,cyw,cox,coy,coz)
                      if(Z>0){
                        l=Qfunc()
                        camBufferCtx.lineTo(...l)
                        X = 0
                        Y = 0
                        Z = playerSize*.75
                        camR1(0,-Pt,-Yw)
                        X -= oX
                        Y -= oY
                        Z -= oZ
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0){
                          l=Qfunc()
                          camBufferCtx.lineTo(...l)
                          stroke('','#0f81',1,true)
                        }
                      }
                    }
                  }
                  camBufferCtx.lineJoin = camBufferCtx.lineCap = tcp
                  camBufferCtx.globalAlpha = 1
                  ct = 0
                  if(i_ == cams.length) a_ = []
                  for(let j=4;j--;){
                    ls_ = ls*cl
                    X1 = kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_
                    Z1 = kz + C(p)*ls_
                    X2 = kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_
                    Z2 = kz + C(p)*ls_
                    if(i_ == cams.length) a_ = [...a_, [X1,Y1,Z1]]
                    for(let m=cl;m--;){
                      camBufferCtx.beginPath()
                      X = X1 + (X2-X1) / cl * m
                      Z = Z1 + (Z2-Z1) / cl * m
                      Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9
                      if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0)camBufferCtx.lineTo(...Qfunc())
                        X = X1 + (X2-X1) / cl * (m+1)
                        Z = Z1 + (Z2-Z1) / cl * (m+1)
                        Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9
                        if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                          camFunc(crl,cpt,cyw,cox,coy,coz)
                          if(Z>0) camBufferCtx.lineTo(...Qfunc())
                          alpha = .85 / (1+(1+d_)**8/1e15)
                          if(alpha > .1) stroke(`hsla(${360/maxCt*ct+t*1e3},99%,${500+S(t)*450}%,${alpha})`,'',3,true)
                        }
                      }
                      ct++
                    }
                  }
                  if(i_ == cams.length) bases = [...bases, [a_, visited[midx[1]][0]]]
                  cl_=(cl/2|0)//-1
                  for(let n=3;n--;) for(j=4;j--;){
                    ls2a = 1/(1+n/2)
                    ls2b = 1/((3+n)/2)
                    X1a = kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_ * ls2a
                    Z1a = kz + C(p)*ls_ * ls2a
                    X2a = kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_ * ls2a
                    Z2a = kz + C(p)*ls_ * ls2a
                    X1b = kx + S(p=Math.PI*2/4*j+Math.PI/4)*ls_ * ls2b
                    Z1b = kz + C(p)*ls_ * ls2b
                    X2b = kx + S(p=Math.PI*2/4*(j+1)+Math.PI/4)*ls_ * ls2b
                    Z2b = kz + C(p)*ls_ * ls2b
                    for(let m=cl_;m--;){
                      a=[]
                      camBufferCtx.beginPath()
                      X = X1a + (X2a-X1a) / cl_ * m
                      Z = Z1a + (Z2a-Z1a) / cl_ * m
                      Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9 + baseHeight * n
                      a=[...a, [X,Y,Z]]
                      if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0) camBufferCtx.lineTo(...Qfunc())
                      }
                      X = X1a + (X2a-X1a) / cl_ * (m+1)
                      Z = Z1a + (Z2a-Z1a) / cl_ * (m+1)
                      Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9+ baseHeight * n
                      a=[...a, [X,Y,Z]]
                      if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0) camBufferCtx.lineTo(...Qfunc())
                      }
                      X = X1b + (X2b-X1b) / cl_ * (m+1)
                      Z = Z1b + (Z2b-Z1b) / cl_ * (m+1)
                      Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9 + baseHeight * (n+1)
                      a=[...a, [X,Y,Z]]
                      if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0) camBufferCtx.lineTo(...Qfunc())
                      }
                      X = X1b + (X2b-X1b) / cl_ * m
                      Z = Z1b + (Z2b-Z1b) / cl_ * m
                      Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 3.9 + baseHeight * (n+1)
                      a=[...a, [X,Y,Z]]
                      if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*1.01){
                        camFunc(crl,cpt,cyw,cox,coy,coz)
                        if(Z>0) camBufferCtx.lineTo(...Qfunc())
                      }
                      alpha = .5 / (1+(1+d_)**8/1e16) / (1+n*3)
                      col1 = alpha > .05 ? `hsla(${-150},99%,60%,${alpha/1.25})` : ''
                      col2 = `hsla(${-100},99%,50%,${alpha})`
                      if(alpha>.02){
                        stroke(col1,col2,3,false)
                        ax=ay=az=0
                        a.map(q=>{
                          ax+=q[0]
                          ay+=q[1]
                          az+=q[2]
                        })
                        ax /=4
                        ay /=4
                        az /=4
                        bullets.map(q=>{
                          X1=q[0]
                          Y1=q[1]
                          Z1=q[2]
                          if(Math.hypot(ax-X1,ay-Y1,az-Z1)<10){
                            X2=(X1+q[3]*1)-q[3]*10
                            Y2=(Y1+q[4]*1)-q[4]*10
                            Z2=(Z1+q[5]*1)-q[5]*10
                            if((l=lineFaceI(X1,Y1,Z1,X2,Y2,Z2,a))){
                              q[6] = 0
                              spawnSparks(...l[0])
                            }
                          }
                        })
                      }
                    }
                  }
                  
                  grid.map((v, i) => {
                    if((i+(i/cl|0))%2){
                      tx = -v[0] + kx
                      ty = -v[1] + ky
                      tz = -v[2] + kz
                      camBufferCtx.beginPath()
                      for(j=4;j--;){
                        X = tx + S(p=Math.PI*2/4*j+Math.PI/4)*ls
                        Z = tz + C(p)*ls
                        Y = ky + floor(X,Z,lx_,lz_) + playerHeight * 4
                        if((d_=Math.hypot(X+oX,(Y+oY)/4,Z+oZ))<renderDist){
                          camFunc(crl,cpt,cyw,cox,coy,coz)
                          if(Z>0) camBufferCtx.lineTo(...Qfunc())
                        }
                      }
                      alpha = .75 / (1+(1+d_)**8/1e16)
                      col1=''
                      col2=`hsla(${Math.hypot(tx+oX,tz+oZ)*3-t*100},99%,${(i+(i/cl|0))%2?15:50}%,${alpha})`
                      if(alpha > .05) stroke(col1,col2)
                    }
                  })
                }
                if(i_==cams.length){
                  X = kx
                  Z = kz
                  Y1 = (Y = floor(X,Z,lx_=X,lz_=Z)-7.5)-2+7.5
                  if((d_=Math.hypot(X+oX, (Y+oY)/4, Z+oZ))<renderDist*10){
                    if(!visited[midx[1]][0] && d_<24){
                      visited[midx[1]][0]=true
                      collected[midx[1]] = '1'
                      score++
                      spawnFlash(X,Y+16,Z)
                    }
                    camFunc(crl,cpt,cyw,cox,coy,coz)
                    if(Z>0){
                      alpha = .9 / (1+(1+d_)**8/1e18)
                      if(alpha>.05){
                        l=Qfunc()
                        camBufferCtx.globalAlpha = alpha
                        if(!visited[midx[1]][0]){
                          s = Math.min(1e4, 1e4/Z)
                          x.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                        }
                        camBufferCtx.font = (fs=2e3/Z) + 'px Courier Prime'
                        camBufferCtx.fillStyle = '#fff'
                        camBufferCtx.fillText(midx[1]+1,l[0],l[1]+fs/Z*30)
                        //camBufferCtx.fillText('X:' + lx,l[0],l[1])
                        //camBufferCtx.fillText('Y:' + Math.round(-ly*grade + playerHeight * 4-2),l[0],l[1]+fs*1)
                        //camBufferCtx.fillText('Z:' + lz,l[0],l[1]+fs*1)
                      }
                    }
                  }
                }
                camBufferCtx.globalAlpha = 1
              }
            }


            Players.map((AI, idx) => {
              if(!camselected && +AI.playerData.id == userID) return
              if(AI.playerData.id == userID){
                X = -oX
                Y = -oY-60
                Z = -oZ
              }else{
                X = -AI.oX
                Y = -AI.oY-60
                Z = -AI.oZ
              }
              camFunc(crl,cpt,cyw,cox,coy,coz)
              if(Z>0){
                l = Qfunc()
                if(n=showPlayerStats){
                  camBufferCtx.textAlign = 'center'
                  camBufferCtx.font = (fs=200/(1+Z/16)) + 'px Courier Prime'
                  camBufferCtx.fillStyle = '#f008'
                  x.fillText(AI.playerData.name + ':' + (idx+1), l[0], l[1])
                }
                l[1]+=150/(1+Z/16)
                if(n) x.fillText(' Σ:' + (+AI.playerData.id == +userID ? score : AI.score), l[0], l[1])
                hs = Math.min(1e3,4000/Z)
                ws = hs/16
                l[1]+=50/(1+Z/16)
                camBufferCtx.lineWidth = 50/Z
                camBufferCtx.strokeStyle = '#fff8'
                camBufferCtx.strokeRect(l[0]-hs/2,l[1]-ws,hs,ws)
                camBufferCtx.fillStyle = '#0f8b'
                camBufferCtx.fillRect(l[0]-hs/2,l[1]-ws,hs*AI.health,ws)
                camBufferCtx.fillStyle = '#400b'
                camBufferCtx.fillRect(l[0]+hs/2,l[1]-ws,-hs*(1-AI.health),ws)
              }
            })
            
            //if(shooting) shoot(-1) // shooting cameras D:
            
            if(camselected != 0){
              flashes.map((v,i) => {
                X = v[0]
                Y = v[1]
                Z = v[2]
                v[3]-=.033
                camFunc(crl,cpt,cyw,cox,coy,coz)
                if(Z>0){
                  l = Qfunc()
                  s = Math.min(3e4,2e5/Z*v[3])
                  //camBufferCtx.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                  //s*=1.25
                  //camBufferCtx.drawImage(starImgs[6].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
                  camBufferCtx.fillStyle = '#4400ff06'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  s/=3
                  camBufferCtx.fillStyle = '#00ff8820'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  s/=3
                  camBufferCtx.fillStyle = '#ffffffff'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                }
              })
            }
            
            sparks = sparks.filter(v=>v[6]>0)
            sparks.map((v,i) => {
              if(i_==cams.length){
                X = (v[0] += v[3])
                Y = (v[1] += v[4] += grav)
                Z = (v[2] += v[5])
                v[6]-=.025
              }else{
                X = v[0]
                Y = v[1]
                Z = v[2]
              }
              camFunc(crl,cpt,cyw,cox,coy,coz)
              if(Z>0){
                l = Qfunc()
                s = Math.min(3e3,1e3/Z*v[6])
                camBufferCtx.fillStyle = '#ff000005'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                camBufferCtx.fillStyle = '#ff880016'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                camBufferCtx.fillStyle = '#ffffffff'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
            })
            
            bullets = bullets.filter(v=>v[6]>0)
            bullets.map(v=>{
              X = (v[0] += v[3])
              Y = (v[1] += v[4])
              Z = (v[2] += v[5])
              camFunc(crl,cpt,cyw,cox,coy,coz)
              if(Z>0){
                l = Qfunc()
                s = Math.min(1e3,2500/Z*v[6])
                camBufferCtx.fillStyle = '#00ff8806'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                camBufferCtx.fillStyle = '#00ffff20'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s/=3
                camBufferCtx.fillStyle = '#ffffffcc'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
              v[6]-=.005
            })

            if(i_ == cams.length && bases.length) {
              bases.map((v,i) => {
                ax = ay = az = 0
                v[0].map((q,j)=>{
                  ax += q[0]
                  ay += q[1]
                  az += q[2]
                })
                ax/=4
                ay/=4
                az/=4
                bullets.map(q=>{
                  X1=q[0]
                  Y1=q[1]
                  Z1=q[2]
                  if(Math.hypot(ax-X1,ay-Y1,az-Z1)<35){
                    X2=(X1+q[3]*1)-q[3]*10
                    Y2=(Y1+q[4]*1)-q[4]*10
                    Z2=(Z1+q[5]*1)-q[5]*10
                    if((l=lineFaceI(X1,Y1,Z1,X2,Y2,Z2,JSON.parse(JSON.stringify(v[0])).map(q=>{
                      q[1]+=playerHeight*5
                      return q
                    })))){
                      q[6] = 0
                      spawnSparks(...l[0])
                    }
                  }
                })
              })
              based = false
              if(Math.abs(oY-(fl+playerHeight))<baseHeight*cl_){
                bases.map((v,i) => {
                  if(!based){
                    ax = ay = az = 0
                    v[0].map((q,j)=>{
                      ax += q[0]
                      ay += q[1]
                      az += q[2]
                    })
                    ax/=4
                    ay/=4
                    az/=4
                    if(oY> fl-50){
                      ct = 0
                      v[0].map((q,j)=>{
                        l = j
                        X1 = v[0][l][0]
                        Z1 = v[0][l][2]
                        l = (j+1)%4
                        X2 = v[0][l][0]
                        Z2 = v[0][l][2]
                        X3 = -oX
                        Z3 = -oZ
                        X4 = ax
                        Z4 = az
                        if(!I(X1,Z1,X2,Z2,X3,Z3,X4,Z4)) ct++
                      })
                      if(ct==4) {
                        based = true
                        fl = -ay
                      }
                    }
                  }
                })
              }else{
                based = false
              }
              
              Players.map((AI,idx) => {
                AI.based = false
                if(Math.abs(AI.oY-(AI.fl+playerHeight))<baseHeight*cl_){
                  AI.bases.map((v,i) => {
                    if(!AI.based){
                      ax = ay = az = 0
                      v[0].map((q,j)=>{
                        ax += q[0]
                        ay += q[1]
                        az += q[2]
                      })
                      ax/=4
                      ay/=4
                      az/=4
                      if(AI.oY> AI.fl-50){
                        ct = 0
                        v[0].map((q,j)=>{
                          l = j
                          X1 = v[0][l][0]
                          Z1 = v[0][l][2]
                          l = (j+1)%4
                          X2 = v[0][l][0]
                          Z2 = v[0][l][2]
                          X3 = -AI.oX
                          Z3 = -AI.oZ
                          X4 = ax
                          Z4 = az
                          if(!I(X1,Z1,X2,Z2,X3,Z3,X4,Z4)) ct++
                        })
                        if(ct==4) {
                          AI.based = true
                          AI.fl = -ay
                        }
                      }
                    }
                  })
                }else{
                  AI.based = false
                }
              })
            }
            
            if(!playercam || camselected !=0){

              if(0)cams.map((v,i) => {
                if(i!=camselected-1){
                  X = v[8]
                  Y = v[9]
                  Z = v[10]
                  camFunc(crl,cpt,cyw,cox,coy,coz)
                  if(Z>0) {
                    l = Qfunc()
                    s = Math.min(1e4,20000/Z*canvasRes)
                    camBufferCtx.fillStyle = '#00ff8806'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#00ffff20'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#ffffffcc'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  }
                }
              })

              X = -oX
              Y = -oY
              Z = -oZ
              camFunc(crl,cpt,cyw,cox,coy,coz)
              if(Z>0){
                l = Qfunc()
                s = Math.min(1e4, 20000/Z*canvasRes)
                camBufferCtx.fillStyle = '#ff000010'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s /= 3
                camBufferCtx.fillStyle = '#ff880020'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                s /= 3
                camBufferCtx.fillStyle = '#ffffffff'
                camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
              }
              
              Players.map((v,i) => {
                X = -v.oX
                Y = -v.oY
                Z = -v.oZ
                camFunc(crl,cpt,cyw,cox,coy,coz)
                if(Z>0){
                  l = Qfunc()
                  s = Math.min(1e4, 20000/Z*canvasRes)
                  camBufferCtx.fillStyle = '#ff000010'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  s /= 3
                  camBufferCtx.fillStyle = '#ff880020'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  s /= 3
                  camBufferCtx.fillStyle = '#ffffffff'
                  camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                }
              })
            }
            
            if(playercam){
              processCams()

              flashes = flashes.filter(v=>v[3]>0)

              if(camselected == 0){
                flashes.map((v,i) => {
                  X = v[0]
                  Y = v[1]
                  Z = v[2]
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0){
                    l = Qfunc()
                    s = Math.min(3e4,6e4/Z*v[3])
                    camBufferCtx.drawImage(burst,l[0]-s/2,l[1]-s/2,s,s)
                    s*=1.25
                    camBufferCtx.drawImage(starImgs[6].img,l[0]-s/2/1.05,l[1]-s/2/1.05,s,s)
                  }
                  v[3]-=.05
                })

                sparks.map((v,i) => {
                  X = v[0]
                  Y = v[1]
                  Z = v[2]
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0){
                    l = Qfunc()
                    s = Math.min(3e3,1e3/Z*v[6])
                    camBufferCtx.fillStyle = '#ff000005'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#ff880016'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#ffffffff'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  }
                })

                bullets.map(v=>{
                  X = v[0]
                  Y = v[1]
                  Z = v[2]
                  camFunc(Rl,Pt,Yw,oX,oY,oZ)
                  if(Z>0){
                    l = Qfunc()
                    s = Math.min(1e3,2500/Z*v[6])
                    camBufferCtx.fillStyle = '#00ff8806'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#00ffff20'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                    s/=3
                    camBufferCtx.fillStyle = '#ffffffcc'
                    camBufferCtx.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                  }
                })
              }

              if(!showMenu){
                if(camselected == 0){
                  if(playerName){
                    camBufferCtx.fillStyle = '#0fc'
                    camBufferCtx.font = (fs=20)+'px Courier Prime'
                    camBufferCtx.textAlign = 'center'
                    camBufferCtx.fillText('you: ' + playerName, c.width/2-50, fs/1.33)
                  }
                  if(objectiveTimer>t){
                    camBufferCtx.globalAlpha = Math.min(1,objectiveTimer-t)
                    camBufferCtx.fillStyle = '#0fc'
                    camBufferCtx.font = (fs=20)+'px Courier Prime'
                    camBufferCtx.textAlign = 'center'
                    camBufferCtx.fillText(objectiveText, c.width/2-50, fs+fs/1.33)
                    camBufferCtx.globalAlpha = 1
                  }
                }
                
                if(showScores){
                  let scores = []
                  Players.map((AI, idx) => {
                    scores = [...scores, [`${AI.playerData.name}: `, AI.playerData.id == userID ? score : AI.score]]
                  })
                  
                  scores.sort((a, b) => b[1]-a[1])
                  
                  camBufferCtx.fillStyle = '#0fc'
                  camBufferCtx.font = (fs=16) + 'px Courier Prime'
                  camBufferCtx.textAlign = 'left'
                  camBufferCtx.fillText('leaderboard', 10, c.height-fs*scores.length-fs/2)
                  
                  scores.map((score, i) => {
                    camBufferCtx.fillText(`#${i+1} ` + score[0] + score[1], 10, c.height-fs/2-fs*scores.length + fs * (i+1))
                  })
                }

                if(showHealth){
                  w = 150
                  h = 10
                  camBufferCtx.fillStyle = '#000'
                  camBufferCtx.fillRect(c.width/2-w/2,c.height-h-3,w,h)
                  camBufferCtx.lineWidth = 2
                  camBufferCtx.strokeStyle='#fff8'
                  camBufferCtx.strokeRect(c.width/2-w/2,c.height-h-3,w,h)
                  camBufferCtx.fillStyle = '#0f4'
                  camBufferCtx.fillRect(c.width/2-w/2,c.height-h-3,w * health,h)
                  camBufferCtx.fillStyle = '#f02c'
                  camBufferCtx.font = (fs=10 + S(t*20)*4)+'px Courier Prime'
                  camBufferCtx.textAlign = 'center'
                  for(let m = 10;m--;){
                    camBufferCtx.fillText('♥', c.width/2 + w/10*(m+.5) - w/2, c.height-8+fs/3)
                  }
                  camBufferCtx.fillStyle = '#4008'
                  camBufferCtx.fillRect(c.width/2+w/2,c.height-h-3,-w * (1-health),h)
                }
              }

              if(showCrosshair && crosshairLoaded){
                camBufferCtx.globalAlpha = .85
                scl = 3 * canvasRes
                x.drawImage(crosshair,c.width/2-crosshair.width/2*scl,c.height/2-crosshair.height/2*scl,crosshair.width*scl,crosshair.height*scl)
                camBufferCtx.globalAlpha = 1
              }
            }
          }
          drawMenu()
          
          
          if(!((t*60|0)%0)) {
            Players.map((AI,idx)=>{
              if(+AI.playerData.id == +userID) return
              ob2 = 0
              if(AI.mobile){
                base_mind = 6e6
                base_mind2 = 6e6
                X1 = -AI.oX
                Y1 = -AI.oY
                Z1 = -AI.oZ
                AI.bases.map((base, i) => {
                  ax = ay = az = 0
                  base[0].map(q=>{
                    ax += q[0]
                    ay += q[1]
                    az += q[2]
                  })
                  ax /= base[0].length
                  ay /= base[0].length
                  az /= base[0].length
                  X2 = ax
                  Y2 = ay
                  Z2 = az
                  d=Math.hypot(X2-X1, Z2-Z1)
                  if(base[2]>ob2 && !AI.visited[base[2]] && d<base_mind) {
                    base_mind = d
                    btx = X2
                    bty = Y2
                    btz = Z2
                  }
                  if(d<base_mind2) {
                    base_mind2 = d
                    if(d<15){
                      AI.visited[base[2]] = true
                      ob2 = base[2]
                    }
                    btx_ = X2
                    bty_ = Y2
                    btz_ = Z2
                  }
                })
                
                p1 = -Math.atan2(btx-X1, btz-Z1)
                p2 = Math.acos((bty-Y1) / (Math.hypot(btx-X1,bty-Y1,btz-Z1)+.001)) - Math.PI/2
                x.beginPath()
                X = X1
                Y = Y1
                Z = Z1
                //camFunc(Rl,Pt,Yw,oX,oY,oZ)
                if(Z>0) x.lineTo(...Qfunc())
                X = 0
                Y = 0
                Z = -3
                camR1(0,Math.PI-p2,-p1,0,0,0)
                X2 = X+=X1
                Y2 = Y+=Y1
                Z2 = Z+=Z1
                //camFunc(Rl,Pt,Yw,oX,oY,oZ)
                //if(Z>0) x.lineTo(...Qfunc())
                //stroke('#f00','',3,true)
              
                if(Math.abs(p1 - AI.Yw)>Math.PI){
                  if(p1 > AI.Yw){
                    AI.Yw += Math.PI*2
                  }else{
                    AI.Yw -= Math.PI*2
                  }
                }
              
                AI.keys[keymap[37]] = AI.keys[keymap[38]] = AI.keys[keymap[39]] = AI.keys[keymap[40]] = false
                if(AI.Pt - p2>0+.1){
                  AI.keys[keymap[40]] = true
                }
                if(AI.Pt - p2<0-.1){
                  AI.keys[keymap[38]] = true
                }
                if(AI.Yw - p1>0+.05){
                  AI.keys[keymap[39]] = true
                }
                if(AI.Yw - p1<0-.05){
                  AI.keys[keymap[37]] = true
                }
                
                d1 = Math.hypot(btx_-X1,btz_-Z1)
                d2 = Math.hypot(btx_-X2,btz_-Z2)
                AI.keys[keymap[32]] = false
                //if(Rn()<.5)  AI.keys[keymap[17]] = false
                AI.keys[keymap[87]] = false
                //if(Rn()<.1) AI.keys[keymap[17]] = true
                if(AI.hasTraction){
                  if(Rn()<.1) AI.keys[keymap[16]] = false
                  if(Math.abs(AI.Yw - p1) < .1) AI.keys[keymap[87]] = true
                  if(AI.keys[keymap[87]]){
                    if(bty+6>bty_) AI.keys[keymap[16]] = true
                    if(!AI.keys[keymap[16]] && d2>d1 && d1 > 15) {
                      AI.keys[keymap[32]] = true
                    }
                  }
                }
              }
            })
          }
          
          if(!pointerLocked){ // tooltips
            buttons.map(button=>{
              if(button.hover){
                let fs
                let margin = 4
                camBufferCtx.font = (fs=8) + 'px Courier Prime'
                X = mx + 5
                let w = camBufferCtx.measureText(button.tooltip).width + margin*2
                let h =  fs + margin * 2
                if(X+w > c.width) w*=-1
                if(my+h > c.height) h*=-1
                camBufferCtx.fillStyle = '#040d'
                camBufferCtx.fillRect(X,my,w,h)
                camBufferCtx.fillStyle = '#0ff'
                camBufferCtx.fillText(button.tooltip,X+(w<0?w:0)+fs/2, my+(h<0?h:0)+fs*1.333)
              }
            })
          }
          if(Math.abs(oY-fl)>150 && !flymode){
            flymode = true
            setTimeout(()=>{flymode=false},100)
          }
          t+=1/60
        }else{
          //camBufferCtx.globalAlpha = 1
          //camBufferCtx.fillStyle='#000'
          //camBufferCtx.fillRect(0,0,c.width,c.height)
        }

        buttonsLoaded = true
        
        if(paused && ot==-1){
          ot=t
          camBufferCtx.globalAlpha = 1
          dta = camBufferCtx.textAlign
          camBufferCtx.textAlign = 'right'
          camBufferCtx.font = (fs=15) + 'px Courier Prime'
          camBufferCtx.fillStyle = '#123d'
          camBufferCtx.fillRect(0,0,c.width,c.height)
          camBufferCtx.fillStyle = '#4f8d'
          camBufferCtx.fillText('paused [tab to toggle]', c.width-10, c.height - fs+fs/1.33)
          camBufferCtx.textAlign = dta
        }
        
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
              if(typeof flymode != 'undefined') individualPlayerData['flymode'] = flymode
              if(typeof mbutton != 'undefined') individualPlayerData['mbutton'] = mbutton
              if(typeof keys != 'undefined') individualPlayerData['keys'] = keys
              if(typeof oX != 'undefined') individualPlayerData['oX'] = oX
              if(typeof oZ != 'undefined') individualPlayerData['oZ'] = oZ
              if(typeof oY != 'undefined') individualPlayerData['oY'] = oY
              //if(typeof Rl != 'undefined') individualPlayerData['Rl'] = Rl
              if(typeof Pt != 'undefined') individualPlayerData['Pt'] = Pt
              if(typeof Yw != 'undefined') individualPlayerData['Yw'] = Yw
              if(typeof health != 'undefined') individualPlayerData['health'] = health
              if(typeof score != 'undefined') individualPlayerData['score'] = score
              //if(typeof lx != 'undefined') individualPlayerData['jumpv'] = jumpv
              //if(typeof ly != 'undefined') individualPlayerData['lx'] = lx
              //if(typeof ly != 'undefined') individualPlayerData['ly'] = ly
              //if(typeof lz != 'undefined') individualPlayerData['lz'] = lz
              //if(typeof lx_ != 'undefined') individualPlayerData['lx_'] = lx_
              //if(typeof ly_ != 'undefined') individualPlayerData['ly_'] = ly_
              //if(typeof lz_ != 'undefined') individualPlayerData['lz_'] = lz_
              //if(typeof kx != 'undefined') individualPlayerData['kx'] = kx
              //if(typeof ky != 'undefined') individualPlayerData['ky'] = ky
              //if(typeof kz != 'undefined') individualPlayerData['kz'] = kz
              //if(typeof oXv != 'undefined') individualPlayerData['oXv'] = oXv
              //if(typeof oYv != 'undefined') individualPlayerData['oYv'] = oYv
              //if(typeof oZv != 'undefined') individualPlayerData['oZv'] = oZv
              //if(typeof Rlv != 'undefined') individualPlayerData['Rlv'] = Rlv
              //if(typeof Ptv != 'undefined') individualPlayerData['Ptv'] = Ptv
              //if(typeof Ywv != 'undefined') individualPlayerData['Ywv'] = Ywv
              //if(typeof jumpTimer != 'undefined') individualPlayerData['jumpTimer'] = jumpTimer
              //if(typeof playerShotTimer != 'undefined') individualPlayerData['playerShotTimer'] = playerShotTimer
              //if(typeof elevation != 'undefined') individualPlayerData['elevation'] = elevation
              //if(typeof grounded != 'undefined') individualPlayerData['grounded'] = grounded
              //if(typeof hasTraction != 'undefined') individualPlayerData['hasTraction'] = hasTraction
              //if(typeof based != 'undefined') individualPlayerData['based'] = based
              //if(typeof alive != 'undefined') individualPlayerData['alive'] = alive
              //if(typeof fl != 'undefined') individualPlayerData['fl'] = fl
              
            }else{
              /*if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    case 'score': if(typeof el[key] != 'undefined'){
                      score = Math.max(orbsCollected, el[key])
                    }
                    break;
                  }
                })
              }*/
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    // straight mapping of incoming data <-> players
                    case 'keys': if(typeof el[key] != 'undefined')     AI[key] = el[key]; break;
                    case 'mbutton': if(typeof el[key] != 'undefined') AI[key] = el[key]; break;
                    case 'score': if(typeof el[key] != 'undefined')    AI[key] = el[key]; break;
                    // reassigned mapping of incoming data <-> players (e.g. toX, vs oX, for lerp)
                    case 'oX': if(typeof el[key] != 'undefined') AI.toX = el[key]; break;
                    case 'oY': if(typeof el[key] != 'undefined') AI.toY = el[key]; break;
                    case 'oZ': if(typeof el[key] != 'undefined') AI.toZ = el[key]; break;
                    case 'Rl': if(typeof el[key] != 'undefined') AI.tRl = el[key]; break;
                    case 'Pt': if(typeof el[key] != 'undefined') AI.tPt = el[key]; break;
                    case 'Yw': if(typeof el[key] != 'undefined') AI.tYw = el[key]; break;
                  }
                })
              }
            }
          })
          for(i=0;i<Players.length;i++) if(Players[i]?.playerData?.id == userID) ofidx = i
        }
      }

      recData              = []
      ofidx                = 0
      collected            = []
      users                = []
      userID               = ''
      gameConnected        = false
      playerName           = ''
      sync = () => {
        //console.log('syncing...')
        //console.log('fullSync:', fullSync)
        //console.log("******",users,userID,users.filter(v=>(+v.id)==(+userID)))
        let sendData = {
          gameID,
          userID,
          individualPlayerData,
          collected
        }
        //console.log('sendData', sendData)
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
            
            for(let i = recData.collected.length; i--;){
              if(recData.collected[i] === '1'){
                collected[i] = '1'
                if(typeof visited != 'undefined') visited[i][0] = true
              }
            }
            syncPlayerData(users)
            
            if(userID) playerName = recData.players[data[3]]['name']
            if(data[2]){ //needs reg
              //console.log('needs reg')
              regFrame.style.display = 'block'
              regFrame.src = `reg.php?g=${gameSlug}&gmid=${gmid}` 
            }else{
              if(!gameConnected){
                setInterval(()=>{sync()}, pollFreq = 333)  //ms
                //console.log('game connected')
                gameConnected = true
              }
              if(!launched){
                //console.log('awaiting players...')
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
      }
    </script>
  </body>
</html>
