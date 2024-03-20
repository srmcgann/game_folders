<?php 
  $url = (isset($_GET['i']) && $_GET['i'] && strlen($_GET['i']) > 5) ? str_replace('https:///', 'https://', $_GET['i']) : 'https://jsbot.twilightparadox.com/uploads/1cUypu.mp4';
  if(strpos($url, 'https:/') !== false && strpos($url, 'https://') === false){
    $url = str_replace('https:/', 'https://', $url);
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      body, html{
        margin: 0;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        background: #000;
      }
      canvas{
        border: 2px solid #486;
        position: absolute;
        top: calc(50% + 100px);
        left: 50%;
        background-color: #222;
        background-position: center center;
        background-size: 100% 100%;
        transform: translate(-50%, -50%);
      }
      .downloadLink{
        text-decoration: none;
        font-family: courier;
        font-size: 24px;
        color: #044;
        text-shadow: 1px 1px 1px #000;
        margin: 50px;
        padding: 10px;
        background: #0fa;
        display: inline-block;
        border-radius: 10px;
      }
      .newDiv{
        text-align: center;
      }
      #goButton{
        border: none;
        background: #0fc4;
        color: #fff;
        cursor: pointer;
        position: fixed;
        top: 0;
	z-index: 10000;
        right: 20px;
        margin: 20px;
        font-size: 24px;
        border-radius: 10px;
        min-width: 100px;
        line-height: 0;
        padding: 0;
        height: 40px;
      }
      .imgThumb{
        width: calc(100% - 50px);
        margin:25px;
      }
      #button{
        position:absolute;
        left:50%;
        top:50%;
        transform: translate(-50%,-50%);
        font-size: 24px;
      }
      #codeArea{
        width: calc(100vw - 65px);
        border: 1px solid #4f88;
        position: absolute;
        left: 50%;
        margin-top: 10px;
        height: 80px;
        transform: translate(-50%);
        background: #002;
        color: #0f8;
	z-index: 10000;
      }
      #codeArea:focus{
        outline: none;
      }
    </style>
    <script>
      url = ''
      if(location.href.indexOf('src=') != -1) url = location.href.indexOf('src=') != -1 ? location.href.split('src=')[1].split('&')[0] : ''
    </script>
  </head>
  <body>
    <button onclick="Draw_()" id="button">Draw</button>
    <div style="font-family: courier;position:absolute; z-index:-100">a</div>
    <textarea placeholder="code" spellcheck="false" id="codeArea"></textarea>
    <canvas id="c"></canvas>
    <button id="goButton" onclick="launch_(false)">go</button>
  </body>
  <script>
    asset = ''
    document.fonts.onloadingdone=()=>{console.log('fonts loaded')}
    duration = window.location.href.toUpperCase().split('DURATION=')[1]
    if(typeof duration !== 'undefined' && duration.length){
      duration=+duration.split('&')[0]
    } else {
      duration = 1000*60|0
    }
    initialDelay = window.location.href.toUpperCase().split('DELAY=')[1]
    if(typeof initialDelay !== 'undefined' && initialDelay.length){
      initialDelay=+initialDelay.split('&')[0]
    } else {
      initialDelay = 4000
    }
    playbackSpeed = window.location.href.toUpperCase().split('PLAYBACKSPEED=')[1]
    if(typeof playbackSpeed !== 'undefined' && playbackSpeed.length){
      playbackSpeed=+playbackSpeed.split('&')[0]
    } else {
      playbackSpeed = 1
    }
    vars=0
    song = './epiphany.mp3'
    vid = ''
    stage2 = false
    export_base = true
    fftSize = 128
    visualization = 13
    autoStart = 1

    recordingStarted=playing=vidplaying=analyzerSetup=0
    canvas2 = document.querySelector("#c")
    canvas2.width = 1920|0
    canvas2.height = 1080|0
    x2 = canvas2.getContext('2d')
    c = canvas2.cloneNode()
    c.id = 'canvas2'
    c.style.visibility = 'hidden'
    c.style.position = 'absolute'
    document.body.appendChild(c)
    c.width=1920
    c.height=1080
    c.style.width='calc(' + 1920 + 'px - 10px)'
    c.style.height='calc(' + 1080 + 'px - 10px)'


    //c.style.display='none'
    x = c.getContext('2d')
    C = Math.cos
    S = Math.sin
    t = 0
    T = Math.tan

    rsz=window.onresize=()=>{
      setTimeout(()=>{
        if(document.body.clientWidth > document.body.clientHeight*1.77777778+1|0){
          c.style.height = 'calc(100vh - 40px)'
          setTimeout(()=>c.style.width = 'calc(' + (c.clientHeight*1.77777778+1|0)+'px)',0)
        }else{
          c.style.width = 'calc(100vw - 40px)'
          setTimeout(()=>c.style.height = 'calc(' + (c.clientWidth/1.77777778+1|0)+'px)',0)
        }
        c.width=1920
        c.height=c.width/1.777777778+1|0
        if(document.body.clientWidth > document.body.clientHeight*1.77777778+1|0){
          canvas2.style.height = 'calc(100vh - 40px)'
          setTimeout(()=>canvas2.style.width = 'calc(' + (canvas2.clientHeight*1.77777778+1|0)+'px)',0)
        }else{
          canvas2.style.width = 'calc(100vw - 40px)'
          setTimeout(()=>canvas2.style.height = 'calc(' + (canvas2.clientWidth/1.77777778+1|0)+'px)',0)
        }
        canvas2.width=c.width
        canvas2.height=canvas2.width/1.777777778+1|0
      },0)
      redraw=true
    }
    rsz()

    async function init(){
      if(window.location.href.indexOf('usecode=')!=-1){
        animationStyle=2
        url=window.location.href.split('usecode=')[1].split('&')[0]
        await fetch(url).then(res=>res.text()).then(data => {
          chosenDemo = data
          launch_(true)
        })
      }
    }
    init()
    
    async function Draw(){

      if(!t){
        R=(Rl,Pt,Yw,m)=>{X=S(p=(A=(M=Math).atan2)(X,Y)+Rl)*(d=(H=M.hypot)(X,Y)),Y=C(p)*d,Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z)),Z=C(p)*d,X=S(p=A(X,Z)+Yw)*(d=H(X,Z)),Z=C(p)*d;if(m)X+=oX,Y+=oY,Z+=oZ}
        I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
        Q=q=>[c.width/2+X/Z*1e3,c.height/2+Y/Z*1e3]
        Rn=Math.random
        document.querySelector('#button').style.display='none'
        x.lineJoin=x.lineCap='butt'
      }

      if(stage2){
        if(song){
          analyzerSetup||setupAnalyzerAndContent()
        }else{
          loaded=1
        }
      } else { //base animation (pre freq)


      if(!t){
        url = "<?php echo $url?>"
        var srcimg = '/k/proxy.php?url=' + url
        hexToRGBA=q=>{
          q=q.replace('#', '')
          let l=q.length
          if(l!=3 && l!=4 && l!==6 && l!=8) return
          let red, green, blue, alpha, red_, green_, blue_, alpha_
          switch(l){
            case 3:
              red_     = q[0]+q[0]
              green_   = q[1]+q[1]
              blue_    = q[2]+q[2]
              alpha    = 255
            break
            case 4:
              red_     = q[0]+q[0]
              green_   = q[1]+q[1]
              blue_    = q[2]+q[2]
              alpha_   = q[3]+q[3]
              alpha    = +("0x"+alpha_)
            break
            case 6:
              red_     = q[0]+q[1]
              green_   = q[2]+q[3]
              blue_    = q[4]+q[5]
              alpha    = 255
            break
            case 8:
              red_     = q[0]+q[1]
              green_   = q[2]+q[3]
              blue_    = q[4]+q[5]
              alpha_   = q[6]+q[7]
              alpha    = +("0x"+alpha_)
            break
          }
          red    = +("0x"+red_)
          green  = +("0x"+green_)
          blue   = +("0x"+blue_)
          return [red, green, blue, alpha]
        }
        R=(Rl,Pt,Yw,m)=>{X=S(p=(A=(M=Math).atan2)(X,Y)+Rl)*(d=(H=M.hypot)(X,Y)),Y=C(p)*d,Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z)),Z=C(p)*d,X=S(p=A(X,Z)+Yw)*(d=H(X,Z)),Z=C(p)*d;if(m)X+=oX,Y+=oY,Z+=oZ}
        Q=()=>[c.width/2+X/Z*900,c.height/2+Y/Z*900]
        for(CB=[],j=6;j--;)for(i=4;i--;)CB=[...CB,[(a=[S(p=Math.PI*2/4*i+Math.PI/4),C(p),2**.5/2])[j%3]*(l=j<3?-1:1),a[(j+1)%3]*l,a[(j+2)%3]*l]]
        s = location.href.indexOf('src=') != -1 ? window.location.href.split('src=')[1].split('?')[0] : ''
        go___=false
        if(typeof s !== 'undefined' && s.length){
          s=s.split('&')[0]
          asset=s
        } else {
          if(srcimg.length){
            asset=srcimg
          } else {
            go___ = true
          }
        }
        asset = 'proxy.php?url='+asset.split('&')[0]
        if(asset.indexOf('.mp4')!==-1 || asset.indexOf('.webm')!==-1){
          jpg = document.createElement('video')
					jpg.loop = true
					jpg.muted = true
					jpgIsVid=true
        }else{
					jpgIsVid=false
					jpg=new Image()
				}
        jpg[jpgIsVid?'oncanplaythrough':'onload']=e=>{
					if(jpgIsVid){
            jpg.defaultPlaybackRate = jpg.playbackRate = playbackSpeed
            jpg.play()
	        }
          //setTimeout(()=>{
          buffer___ = document.createElement('canvas')
          buffer___.width=c.width
          buffer___.height=c.height
          bctx = buffer___.getContext('2d', {willReadFrequently: true})
          if(jpgIsVid){
	        setTimeout(()=>{jpg.play()},50)
            vid=asset
            if(vid.videoWidth/vid.videoHeight>1.7777777778){
              w=c.width
              h=c.width/(vid.videoWidth/vid.videoHeight)
            } else {
              w=c.height*(vid.videoWidth/vid.videoHeight)
              h=c.height
            }
					}else{
            if(jpg.width/jpg.height>1.7777777778){
              w=c.width
              h=c.width*(jpg.width/jpg.height)
            } else {
              w=c.height*(jpg.width/jpg.height)
              h=c.height
            }
					}
					getMask=()=>{
            bctx.clearRect(0,0,c.width,c.height)
            bctx.drawImage(jpg,c.width/2-w/2,c.height/2-h/2,w,h)
            data = bctx.getImageData(0,0,buffer___.width,buffer___.height)
            l=data.data
            for(i=0;i<l.length;i+=4){
              red   = l[i+0]
              green = l[i+1]
              blue  = l[i+2]
              alpha = l[i+3]

              //greenTolerance=100
              //alpha=green>255-greenTolerance && red<greenTolerance && blue<greenTolerance?0:255
              diff=(Math.abs(red-tgtRGBA[0])+Math.abs(green-tgtRGBA[1])+Math.abs(blue-tgtRGBA[2]))/3
              if(invert){
                alpha = diff>tol?0:255
              } else {
                alpha = diff>tol?255:0
              }
              l[i+0] = red
              l[i+1] = green
              l[i+2] = blue
              l[i+3] = alpha
            }
            bctx.putImageData(data,0,0)
            go___=true
					}
					getMask()
        }
        tolerance=window.location.href.toUpperCase().split('TOLERANCE=')[1]
        if(typeof tolerance !== 'undefined' && tolerance.length){
          tolerance = tolerance.split('&')[0]
          tol=tolerance
        } else {
          tol=100
        }
        invert=window.location.href.toUpperCase().split('INVERT=')[1]
        if(typeof invert !== 'undefined'){
          invert=invert.split('&')[0]
          invert=!!(+invert)
        } else {
          invert=false
        }
        tgtcol=window.location.href.toUpperCase().split('TGTCOL=')[1]
        if(typeof tgtcol !== 'undefined' && tgtcol.length){
          tgtcol=tgtcol.split('&')[0]
          tgt_col=tgtcol
        } else {
          tgt_col='#eab29c'
        }
        /*animationStyle=window.location.href.toUpperCase().split('ANIMATIONSTYLE=')[1]
        if(typeof animationStyle !== 'undefined' && animationStyle.length){
          animationStyle=+animationStyle.split('&')[0]
        } else {
          animationStyle=0
        }*/
        tgtRGBA=hexToRGBA(tgt_col)
				jpg.src=asset
      }

      switch(animationStyle){
        case 0:
          if(!t){
            for(a=[1,1],i=40;i--;)a=[...a,a[l=a.length-1]+a[l-1]]
            phi = a[l+1]/a[l]

            rects=Array(3).fill().map((v,i)=>{
              a=[]
              a=[...a, [-phi/2, -.5, 0]]
              a=[...a, [phi/2,  -.5, 0]]
              a=[...a, [phi/2,  .5,  0]]
              a=[...a, [-phi/2, .5,  0]]
              a=a.map(q=>{
                X=q[0], Y=q[1], Z=q[2]
                switch(i){
                  case 0: R(Math.PI/2,0,0); break
                  case 1: R(Math.PI/2,Math.PI/2,Math.PI/2); break
                  case 2: R(0,0,Math.PI/2); break
                }
                return [X,Y,Z]
              })
              return a
            })
            facets=[[[0,0], [2,2], [0,3]],[[1,2], [2,2], [0,3]],[[1,2], [2,2], [2,1]],[[1,2], [0,2], [2,1]],[[2,1], [0,1], [1,3]],[[2,1], [2,2], [1,3]],[[2,1], [0,1], [0,2]],[[1,2], [1,1], [0,2]],[[0,2], [0,1], [2,0]],[[0,2], [2,0], [1,1]],[[2,3], [2,0], [1,1]],[[2,3], [2,0], [1,0]],[[0,1], [2,0], [1,0]],[[0,1], [1,3], [1,0]],[[0,0], [1,3], [1,0]],[[0,0], [2,3], [1,0]],[[0,0], [2,3], [0,3]],[[1,1], [2,3], [0,3]],[[1,1], [1,2], [0,3]],[[0,0], [2,2], [1,3]]]

            shp=[]
            facets.map(n=>{
              a=[]
              n.map(q=>{
                v=rects[q[0]][q[1]]
                X=v[0]
                Y=v[1]
                Z=v[2]
                a=[...a, [X,Y,Z]]
              })
              shp=[...shp, a]
            })

            subdivide = () =>{
              subdivisions=[]
              new_shp=[]
              shp.map((v,i,a)=>{
                a=[]
                v.map((q,j)=>{
                  X1=q[0]
                  Y1=q[1]
                  Z1=q[2]
                  X2=v[l=(j+1)%3][0]
                  Y2=v[l][1]
                  Z2=v[l][2]
                  mx=(X1+X2)/2
                  my=(Y1+Y2)/2
                  mz=(Z1+Z2)/2
                  a=[...a, [mx,my,mz]]
                })
                subdivisions=[...subdivisions, a]
                v.map((q,j)=>{
                  b=[]
                  for(m=1;m--;){
                    b=[...b, [...v[(j+1)%3]], [...a[(m+j)%3]], [...a[(m+j+1)%3]]]
                  }
                  subdivisions=[...subdivisions, b]
                })
              })
              shp=[...new_shp, ...subdivisions]      
            }

            subdivisions=3
            for(let m = subdivisions; m--;)subdivide()
            base_shp=JSON.parse(JSON.stringify(shp))

            expansion=10+10*S(t*2)
            ls=.1/(20+expansion)*270
            shps=[]
            shps=[...shps, ((base_shp.map(v=>{
              v=v.map(q=>{
                X1=q[0]
                Y1=q[1]
                Z1=q[2]
                d=Math.hypot(X1,Y1,Z1)
                X2=X1/d
                Y2=Y1/d
                Z2=Z1/d
                X=(X2*expansion+(X1*(1-expansion)))*ls
                Y=(Y2*expansion+(Y1*(1-expansion)))*ls
                Z=(Z2*expansion+(Z1*(1-expansion)))*ls
                return [X,Y,Z]
              })
              return v
            })))]
            expansion=S(t*4)*1-2
            ls=1.5
            shps=[...shps, ((base_shp.map(v=>{
              v=v.map(q=>{
                X1=q[0]
                Y1=q[1]
                Z1=q[2]
                d=Math.hypot(X1,Y1,Z1)
                X2=X1/d
                Y2=Y1/d
                Z2=Z1/d
                X=(X2*expansion+(X1*(1-expansion)))*ls
                Y=(Y2*expansion+(Y1*(1-expansion)))*ls
                Z=(Z2*expansion+(Z1*(1-expansion)))*ls
                return [X,Y,Z]
              })
              return v
            })))]
          }
          if(go___){
            x.lineJoin=x.lineCap='butt'
            Rl=0,Pt=-4,Yw=t
            oX=0,oY=0,oZ=3.5

            x.globalAlpha=1
            x.fillStyle='#0008'
            x.fillRect(0,0,c.width,c.height)

            shps.map(shp=>{
             shp.map(n=>{
                x.beginPath()
                ax=ay=az=0
                n.map(q=>{
                  v=q
                  X=v[0]
                  Y=v[1]
                  Z=v[2]
                  ax+=X
                  ay+=Y
                  az+=Z
                  R(Rl,Pt,Yw,1)
                  x.lineTo(...Q())
                })
                ax/=n.length
                ay/=n.length
                az/=n.length
                x.closePath()
                x.lineWidth=70/(Z**1.5)
                x.strokeStyle=`hsla(${(l=H(ax,ay,az)*200)*1-t*200},99%,${100-l**3.5/80000000}%,.2)`
                x.stroke()
                x.lineWidth/=5
                x.lineWidth|=0
                x.strokeStyle='#fff3'
                //x.stroke()
                //x.fillStyle=`hsla(${(l=H(ax,ay,az)*200)*2-t*200},99%,${100-l**3.5/100000000}%,.1)`
                //x.fill()
              })
            })
            x.globalAlpha=1
            if(go___) x.drawImage(buffer___,0,0,c.width,c.height)      
          }
        break
        case 1:
          if(!t){
            keys=Array(256).fill(0)
            window.onkeydown=e=>{
              e.preventDefault()
              e.stopPropagation()
              if(e.keyCode-48>=0 && e.keyCode-48<10) focus_idx=e.keyCode-48
              keys[e.keyCode]=true
            }
            window.onkeyup=e=>{
              e.preventDefault()
              e.stopPropagation()
              keys[e.keyCode]=false
            }
            Rn = Math.random
            NPC_vel = 3
            player_vel=NPC_vel*8
            NPC_turn_vel=.05
            B=Array(100).fill().map(v=>{
              X = (Rn()-.5) * c.width
              Y = (Rn()-.5) * c.width
              direction = Rn()*Math.PI*2
              return [X, Y, direction, 0]
            })
            P=Array(10).fill().map(v=>{
              X = 0
              Y = 0
              direction = Rn()*Math.PI*2
              return [X, Y, direction, 0]
            })
            
            Q=(X,Y,r)=>{
              let a, b
              let p=Math.atan2(a=X-c.width/2, b=Y-c.height/2)-(r?P[focus_idx][2]:0)+Math.PI
              let d=Math.hypot(a,b)
              X=c.width/2+S(p)*d
              Y=c.height/2+C(p)*d
              return [X, Y]
            }

            focus_idx=0
          }
          if(go___){
            x.globalAlpha=1
            x.fillStyle='#0008'
            x.fillRect(0,0,c.width,c.height)
            P.map((v, i)=>{
              v[3]/=i==focus_idx?1.5:1.02
              v[2]+=v[3]
              if(i!==focus_idx){
                vx = S(v[2])*NPC_vel
                vy = C(v[2])*NPC_vel
                v[3]+=(Rn()-.5)*NPC_turn_vel
              } else {
                vx=0, vy=0
                if(keys[37]){
                  v[3]+=NPC_turn_vel
                }
                if(keys[38]){
                  vx = S(v[2])*player_vel
                  vy = C(v[2])*player_vel
                }
                if(keys[39]){
                  v[3]-=NPC_turn_vel
                }
                if(keys[40]){
                  vx = -S(v[2])*player_vel
                  vy = -C(v[2])*player_vel
                }
              }
              X = c.width/2 + (v[0] += vx)
              Y = c.height/2 + (v[1] += vy)
              if(v[0] > c.width/2) v[0] -= c.width
              if(v[0] < -c.width/2) v[0] += c.width
              if(v[1] > c.width/2) v[1] -= c.width
              if(v[1] < -c.width/2) v[1] += c.width
              x.fillStyle='#0ff3'
              s=50
              if(i==focus_idx){
                cx = X-c.width/2, cy = Y-c.height/2
              }
              l=Q(X-cx, Y-cy, 1)
              x.fillRect(l[0]-s/2,l[1]-s/2, s, s)
              x.fillStyle='#0ff'
              s/=3
              l=Q(X-cx, Y-cy, 1)
              x.fillRect(l[0]-s/2,l[1]-s/2, s, s)

              x.fillStyle='#fff'
              x.font='70px courier'
              x.fillText(i,l[0]+20, l[1]+40)

              x.beginPath()
              X=v[0]+c.width/2
              y=v[1]+c.height/2
              tx=S(p=i==focus_idx?0:v[2])
              ty=C(p)
              x.lineTo(...Q(X-tx*50-cx,Y-ty*50-cy, i!=focus_idx))
              x.lineTo(...Q(X+tx*100-cx,Y+ty*100-cy, i!=focus_idx))
              x.strokeStyle='#0ff'
              x.lineWidth=10
              x.stroke()
            })

            B.map(v=>{
              v[3]+=(Rn()-.5)*NPC_turn_vel
              v[3]/=1.1
              vx = S(v[2]+=v[3])*NPC_vel
              vy = C(v[2])*NPC_vel
              X = c.width/2 + (v[0] += vx)
              Y = c.height/2 + (v[1] += vy)
              if(v[0] > c.width/2) v[0] -= c.width
              if(v[0] < -c.width/2) v[0] += c.width
              if(v[1] > c.width/2) v[1] -= c.width
              if(v[1] < -c.width/2) v[1] += c.width
              x.fillStyle='#fff3'
              s=25
              l=Q(X-cx, Y-cy, 1)
              x.fillRect(l[0]-s/2,l[1]-s/2, s, s)
              x.fillStyle='#fff'
              s/=3
              l=Q(X-cx, Y-cy, 1)
              x.fillRect(l[0]-s/2,l[1]-s/2, s, s)

              x.beginPath()
              X=v[0]+c.width/2
              y=v[1]+c.height/2
              tx=S(p=v[2])
              ty=C(p)
              x.lineTo(...Q(X-tx*25-cx,Y-ty*25-cy, 1))
              x.lineTo(...Q(X+tx*50-cx,Y+ty*50-cy, 1))
              x.strokeStyle='#f00'
              x.lineWidth=3
              x.stroke()
            })

            x.beginPath()
            for(i=4;i--;){
              X=S(p=Math.PI*2/4*i+Math.PI/4)*c.width
              Y=C(p)*c.width
              q=Math.atan2(X,Y)//-P[focus_idx][2]
              d=Math.hypot(X,Y)
              X=S(q)*d/2**.5
              Y=C(q)*d/2**.5
              x.lineTo(...Q(c.width/2+X-cx,c.height/2+Y-cy, 1))
            }
            x.strokeStyle='#f00'
            x.closePath()
            x.lineWidth=20
            x.stroke()
          }
        break
        case 2:
          if(!t){
            goCustom = false
            if(typeof chosenDemo !== 'undefined'){
              customJS = chosenDemo//.demoJS.split('Draw=()=>{')
              goCustom = true
            }
            let src = `function bypass_eval(){` + customJS + `}`
            let script = document.createElement('script')
            script.innerHTML = src
            document.body.appendChild(script)
            bypass_eval()
          }
          if(goCustom && go___){
            bypass_eval()
          }
        break
      }
      if(go___){
				if(jpgIsVid)getMask()
        x.globalAlpha=1
        if(typeof buffer___ != 'undefined') x.drawImage(buffer___,0,0,c.width,c.height)
      }else{
        x.globalAlpha=1
        x.fillStyle='#0008'
        x.fillRect(0,0,c.width,c.height)
      }

	if(jpgIsVid) jpg.defaultPlaybackRate = jpg.playbackRate = playbackSpeed


        t+=1/60
      }

      if(stage2 && loaded){

        if(!recordingStarted && export_base) startRecording()

        switch(visualization){
          case 0:
            scaleX=2
            framels=5
            if(vid){
              x.globalAlpha=.5
              x.drawImage(bgvid,0,0,w=c.width,c.height)
              x.globalAlpha=1
            }else{
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }
            
            G=2
            oX=0,oY=0,oZ=7+S(t)*2
            Rl=S(t/1.5)/4,Pt=S(t)/4,Yw=S(t*1.5)/2
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
            } else {
              trim=0
              B=Array(64).fill(1)
            }

            x.beginPath()
            x.lineWidth=framels
            X=G*scaleX,Y=G,Z=0,R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=G*scaleX,Y=-G,Z=0,R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=-G*scaleX,Y=-G,Z=0,R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=-G*scaleX,Y=G,Z=0,R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=G*scaleX,Y=G,Z=0,R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            x.strokeStyle=`hsla(${t*99},99%,70%,.1`
            x.fillStyle=`hsla(${t*99},99%,20%,.2`
            x.stroke()
            x.fill()

            B.map((v,i)=>{
              if(v&&i<B.length-trim){
                X=(G/(B.length-trim)*(i+.5)-G/2)*scaleX*2
                Y=G/128*(128-v/1.1+1)-(G/10)
                Z=0
                R(Rl,Pt,Yw,1)
                x.beginPath()
                x.lineTo(...Q())
                X=(G/(B.length-trim)*(i+.5)-G/2)*scaleX*2
                Y=G-(G/10)
                Z=0
                R(Rl,Pt,Yw,1)
                x.lineTo(...Q())
                x.strokeStyle=`hsla(${360/(B.length-trim)*i+360/128*v+t*900},50%,${40+50/1e9*(v**4)}%,.4)`
                x.lineWidth=750/(1+Z)/(2+(Z)/3.5)
                x.stroke()
                x.strokeStyle=`hsla(${360/(B.length-trim)*i+360/128*v+t*900},50%,${60+40/1e9*(v**4)}%,1)`
                x.lineWidth=350/(1+Z)/(2+(Z)/3.5)
                x.stroke()
              }
            })
          t+= 1/60
          break
          case 1:
          
            if(vid){
              x.globalAlpha=.5
              x.drawImage(bgvid,0,0,w=c.width,c.height)
              x.globalAlpha=1
              x.fillStyle='#0004'
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }

            G=2
            oX=0,oY=0,oZ=G+S(t*2)
            Rl=t,Pt=0,Yw=C(t/2)
            
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
              iPc=B.length-trim
            }else{
              iPc=32,B=Array(iPc).fill(0)
            }
            

            if(!t){
              x.lineCap=x.lineJoin='round'
              P=Array(iPc).fill().map((v,i)=>{
                X=G/iPc*(i-iPc/2)*1
                Y=G/3
                Z=0
                return [X,Y,Z,1,[]]
              })
            }

            P.map((v,k)=>{
              ty=v[1]+(B[k]-128)/250
              X=v[0]
              Y=ty
              Z=v[2]
              R(Rl,Pt,Yw,1)
              x.beginPath()
              x.lineTo(...Q())
              v[4].map((q,j)=>{
                X=v[4][l=v[4].length-j-1][0]
                Y=v[4][l][1]
                Z=v[4][l][2]
                v[4][l][2]+=.08
                R(Rl,Pt,Yw,1)
                if(j<v[4].length-1){
                  x.beginPath()
                  x.lineTo(...Q())
                  X=v[4][l=v[4].length-j-2][0]
                  Y=v[4][l][1]
                  Z=v[4][l][2]
                  R(Rl,Pt,Yw,1)
                  x.lineTo(...Q())
                  x.strokeStyle=`hsla(${360/128*B[k]-t*800+j*10},99%,${150-60/128*B[k]}%,.4)`
                  x.lineWidth=30*v[4][l][3]/Z
                  if(Z>.5)x.stroke()
                }
                v[4][l][3]/=1.05
              })
              v[4].push([v[0],ty,v[2],v[3]/1.05])
              v[4]=v[4].filter(v=>v[3]>.05)
            })
            P.map((v,k)=>{
              X=v[0]
              Y=-v[1]
              Z=v[2]
              R(Rl,Pt,Yw,1)
              x.beginPath()
              x.lineTo(...Q())
              v[4].map((q,j)=>{
                X=v[4][l=v[4].length-j-1][0]
                Y=-v[4][l][1]
                Z=v[4][l][2]
                R(Rl,Pt,Yw,1)
                if(j<v[4].length-1){
                  x.beginPath()
                  x.lineTo(...Q())
                  X=v[4][l=v[4].length-j-2][0]
                  Y=-v[4][l][1]
                  Z=v[4][l][2]
                  R(Rl,Pt,Yw,1)
                  x.lineTo(...Q())
                  x.strokeStyle=`hsla(${360/128*B[k]+t*800+j*10},99%,${150-60/128*B[k]}%,.4)`
                  x.lineWidth=30*v[4][l][3]/Z
                  if(Z>.5)x.stroke()
                }
              })
            })
            
            t+= 1/60
          break
          
          case 2:
          
            scaleX=2
            framels=5
            if(vid){
              x.globalAlpha=.5
              x.drawImage(bgvid,0,0,w=c.width,h=c.height);
              x.globalAlpha=1
              x.fillStyle='#0005'
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#0004'
              x.fillRect(0,0,w=c.width,w)
            }
            
            oX=0,oY=0,oZ=10
            Rl=0,Pt=-t/1.5,Yw=t
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
            } else {
              trim=0
              B=Array(64).fill(1)
            }

            if(!t){
              x.lineJoin=x.lineCap='round'
              cl=30,rw=16,iSr=2
              P=Array(cl*(rw+1)|0).fill().map((v,i)=>{
                j=i/cl|0
                X=S(p=Math.PI*2/cl*i)*S(q=Math.PI/(rw)*j)*iSr
                Y=C(q)*iSr
                Z=C(p)*S(q)*iSr
                return [X,Y,Z,0]
              })
            }
            P.map((v,i)=>{
              
              d3=Math.hypot(...v)
              v[1]=v[1]/d3*iSr
              v[0]=v[0]/d3*iSr*(d1=1+B[l=cl/B.length*(i%cl)|0]**2/19999/(((1+Math.abs(v[1]))**.5*5))*3)
              v[1]=v[1]*d1
              v[2]=v[2]/d3*iSr*d1

              X=v[0]
              Y=v[1]
              Z=v[2]
              R(Rl,Pt,Yw,1)
              x.beginPath()
              x.lineTo(...Q())
              if((i%cl)==cl-1){
                X=P[l=i-cl+1][0]
                Y=P[l][1]
                Z=P[l][2]
              } else {
                X=P[i+1][0]
                Y=P[i+1][1]
                Z=P[i+1][2]
              }
              R(Rl,Pt,Yw,1)
              x.lineTo(...Q())
              x.strokeStyle=`hsla(${d3*80-t*399},99%,${Math.max(40,95-(1+d3)**5/70)}%,.1`
              x.lineWidth=1+999/Z/Z
              x.stroke()
              x.strokeStyle=`hsla(${d3*80-t*399},99%,${Math.max(40,115-(1+d3)**5/70)}%,.6`
              x.lineWidth=1+200/Z/Z
              x.stroke()

              if(i<P.length-cl){
                if((i+1)%cl){
                  X=P[l=i+cl+1][0]
                  Y=P[l][1]
                  Z=P[l][2]
                }else{
                  X=P[l=i+1][0]
                  Y=P[l][1]
                  Z=P[l][2]
                }
                R(Rl,Pt,Yw,1)
                x.lineTo(...Q())
                x.strokeStyle=`hsla(${d3*80-t*399},99%,${Math.max(40,95-(1+d3)**5/70)}%,.1`
                x.lineWidth=1+999/Z/Z
                x.stroke()
                x.strokeStyle=`hsla(${d3*80-t*399},99%,${Math.max(40,115-(1+d3)**5/70)}%,.6`
                x.lineWidth=1+200/Z/Z
                x.stroke()
              }
            })
          
            t+= 1/60
          break
          case 3:
            x.fillStyle='#0008',x.fillRect(0,0,w=c.width,w)
            
            if(!t){
              x.lineJoin=x.lineCap='round'
              cl=100,iBc=16,iBr=10,iBv=16
              rw=cl/aspect
              ls=w/cl
              P=Array(cl*(rw+1)|0).fill().map((v,i)=>[(i%cl)*ls,(i/cl|0)*ls])
              E=Array(iBc).fill().map(v=>[iBr+Rn()*(w-iBr*2),iBr+Rn()*(c.height-iBr*2),S(p=Rn()*Math.PI)*iBv,C(p)*iBv])
            }

            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
              amp=0
              B.map((v,i)=>amp+=v*(1+(B.length/2-Math.abs(B.length/2-i))/2))
            } else {
              trim=0
              B=Array(64).fill(1)
            }

            P.map(v=>{
              e=0
              E.map(q=>{
                d=Math.hypot(q[0]-(v[0]+ls/2),q[1]-(v[1]+ls/2))
                e+=5/(d**1.35/600)*(1+amp**4/300000000000000)
              })
              x.fillStyle=`hsla(${e*1.5-65},99%,${Math.min(100,e)}%,1`
              x.fillRect(...v,ls+1,ls+1)
              /*
              x.strokeStyle='#fff1'
              x.lineWidth=ls/10
              x.strokeRect(...v,ls,ls)
              */
            })

            E.map(v=>{
              if(v[0]>w-iBr||v[0]<iBr)v[2]*=-1
              if(v[1]>c.height-iBr||v[1]<iBr)v[3]*=-1
              x.beginPath()
              x.arc(v[0],v[1],iBr,0,7)
              x.fillStyle='#aff6'
              x.fill()
              x.beginPath()
              x.arc(v[0],v[1],iBr/1.75,0,7)
              x.fillStyle='#6aa8'
              x.fill()
              v[0]+=v[2]
              v[1]+=v[3]
            })
            
            t+=1/60
          break
          case 4:
            x.lineJoin=x.lineCap='round'
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
              amp=0
              B.map((v,i)=>{amp+=v*(1+(B.length/2-Math.abs(B.length/2-i))/2)})
            } else {
              trim=0
              B=Array(fftSize).fill(1)
            }
            Q=q=>{
              with(x)
              beginPath(),
              lineWidth=w/Z**3.15*(o>-1?20:20.5),
              moveTo(q[0],q[1]),
              lineTo(q[2],q[3])
              if(i>l-3){
                lineWidth=w/2**2.5*v,
                x.moveTo(w-q[5]/14*h,h+4/14*h*q[4][0]),
                x.lineTo(q[0],q[1])
              }
              x.stroke()
            }
            R=s=>{
              for(n=3;n--;){
                ls=1
                if(playing){
                  B.map((v,i)=>{
                    switch(2-n){
                      case 0: if(i<fftSize/3)ls+=v/2000; break
                      case 1: if(i>=fftSize/3 && i<fftSize/3*2)ls+=v/400; break
                      case 2: if(i<=fftSize-fftSize/3)ls+=v/2000; break
                    }
                  })
                }
                ls=ls**4
                ls=1+ls/10
                for(k=2;k--;){
                  for(l=i=24;i--;){
                    X=S(p=(Math.PI/2+Math.PI*2/38*i)*(g=k?1:-1))-1*g
                    Y=(C(p)-2)*ls
                    p=Math.atan2(X,0)-t*4
                    d=(X*X)**.5*ls
                    ox=-12+n*12
                    X=S(q=p+Math.PI*2/9*s[1]*1.425+t*2)*d*s[0]-ox
                    Z=C(q)*d+14
                    D=w+X/Z*h
                    E=h+(Y+2)/Z*h*s[0]
                    //x.strokeStyle=`rgba(${255},${v=(o=s[2]>-1)?150:3},${v},${.85/((s[0]/2)**3*(o?8:1))}`
                    v=(o=s[2]>-1)?150:3
                    if(s[0]==1){
                      x.strokeStyle='#f008'
                    }else{
                      x.strokeStyle=`hsla(${255+360/24*i+t*500},99%,${120-Math.min(50,s[2]**3)}%,${.5/((s[0]/2)**3*(o?8:1))}`
                    }
                    if(i-l+1)Q([A,G,D,E,s,ox,ls])
                    A=D,G=E
                  }
                }
              }
          }
          if(typeof bgvid !== 'undefined'){
            x.globalAlpha=.3
            x.drawImage(bgvid,0,0,(w=c.width/2)*2,c.height)
            x.globalAlpha=1
            x.fillStyle='#0002'
            x.fillRect(0,0,w*2,w*2)
          }else{
            x.fillStyle='#0002'
            x.fillRect(0,0,(w=c.width/2)*2,c.height)
          }
          h=c.height/2
          R([1,0])
          for(j=16;j--;)R([1+(v=j/5.3+(t*1.5)%(1/5.3)),v,j])

          t+=1/60
          break
          
          case 5:

            if(typeof bgvid !== 'undefined'){
              x.globalAlpha=.5
              x.drawImage(bgvid,0,0,w=c.width,c.height)
              x.globalAlpha=1
              //x.fillStyle='#0000'
              //x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#0004'
              x.fillRect(0,0,w=c.width,w)
            }

            Rl=t/6,Pt=S(t/2)/2,Yw=S(t/1.5)/1.5
            oX=0,oY=0,oZ=4+S(t/1.5)/1.75
            mt=S(t/4)
            
            if(!t){
              I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
              x.lineJoin=x.lineCap='round'
              sd=6
              iBr=2,iPv=.006,iPsv=.025,iPc=8,iPs=150,iRm=iPv*65,iRsm=iPsv*2.75,iPbv=.02
              B=Array(sd).fill().map((v,i)=>{
                return [S(p=Math.PI*2/sd*i)*iBr,C(p)*iBr,0]
              })
              P=Array(iPc).fill().map(v=>{return {X:0,Y:0,Z:0,vx:S(p=Rn()*Math.PI*2)*iPv,vy:C(p)*iPv,vz:0,theta:0,P:[]}})
            }

            P.map((v,j)=>{
              x3_=v.X+=v.vx
              y3_=v.Y+=v.vy
              z3_=v.Z+=v.vz
              d=Math.hypot(v.vx,v.vy,v.vz)
              vxd=v.vx/d*iRm
              vyd=v.vy/d*iRm
              vzd=v.vz/d*iRm
              x4_=x3_+vxd
              y4_=y3_+vyd
              z4_=z3_+vzd
              B.map((q,i)=>{
                if(i){
                  X=B[i-1][0]
                  Y=B[i-1][1]
                  Z=0
                }else{
                  X=B[l=B.length-1][0]
                  Y=B[l][1]
                  Z=0
                }
                R(mt,0,0,0)
                x2_=X,y2_=Y
                X=q[0],Y=q[1],Z=0
                R(mt,0,0,0)
                x1_=X,y1_=Y
                if(a=I(x1_,y1_,x2_,y2_,x3_,y3_,x4_,y4_)){
                  u=((x3_-x1_)*(x2_-x1_)+(y3_-y1_)*(y2_-y1_))/Math.hypot(x2_-x1_,y2_-y1_)
                  mx=u*(x2_-x1_)
                  my=u*(y2_-y1_)
                  v.vx+=S(p=Math.atan2(mx-v.X,my-v.Y)-Math.PI/2)/80
                  v.vy+=C(p)/80
                }
              })
              d=Math.hypot(v.vx,v.vy)
              v.vx/=d
              v.vy/=d
              v.vx*=iPv
              v.vy*=iPv
              for(m=3;m--;)v.P.push([v.X,v.Y,v.Z,S(p=v.theta+=Math.PI/sd*(1+j==3?5:1+j)+S(t/1.5)/16)*iPsv,C(p)*iPsv,0,iPs])
              v.P.map(v=>{

                x3_=v[0]
                y3_=v[1]
                z3_=v[2]
                d=Math.hypot(v[3],v[4],v[5])
                vxd=v[3]/d*iRsm
                vyd=v[4]/d*iRsm
                vzd=v[5]/d*iRsm
                x4_=x3_+vxd
                y4_=y3_+vyd
                z4_=z3_+vzd
                bnc=0
                B.map((q,i)=>{
                  if(i){
                    X=B[i-1][0]
                    Y=B[i-1][1]
                    Z=0
                  }else{
                    X=B[l=B.length-1][0]
                    Y=B[l][1]
                    Z=0
                  }
                  R(mt,0,0,0)
                  x2_=X,y2_=Y
                  X=q[0],Y=q[1],Z=0
                  R(mt,0,0,0)
                  x1_=X,y1_=Y
                  if(!bnc&&(a=I(x1_,y1_,x2_,y2_,x3_,y3_,x4_,y4_))){
                    bnc=0
                    u=((x3_-x1_)*(x2_-x1_)+(y3_-y1_)*(y2_-y1_))/Math.hypot(x2_-x1_,y2_-y1_)
                    mx=u*(x2_-x1_)
                    my=u*(y2_-y1_)
                    v[3]+=S(p=Math.atan2(mx-v[0],my-v[1])-.8)/80*(d=.25+Math.hypot(v[3],v[4])*100)
                    v[4]+=C(p)/100*d
                  }
                })
                d=Math.hypot(v[3],v[4])
                v[3]/=d
                v[4]/=d
                v[3]*=iPsv
                v[4]*=iPsv

                X=v[0]+=v[3]
                Y=v[1]+=v[4]
                Z=v[2]+=v[5]
                R(Rl,Pt,Yw,1)
                if(Z>0){
                  x.beginPath()
                  x.arc(...Q(),Math.min(99,(v[6]-=1.5)/Z/Z),0,7)
                  x.fillStyle='#4f84'
                  x.fill()
                }
              })
              v.P=v.P.filter(v=>v[6]>10)
            })

            B.map((v,i)=>{
              x.beginPath()
              if(i){
                X=B[i-1][0]
                Y=B[i-1][1]
                Z=B[i-1][2]
              }else{
                X=B[l=B.length-1][0]
                Y=B[l][1]
                Z=B[l][2]
              }
              R(mt,0,0,0)
              R(Rl,Pt,Yw,1)
              x.lineTo(...Q())
              X=v[0]
              Y=v[1]
              Z=v[2]
              R(mt,0,0,0)
              R(Rl,Pt,Yw,1)
              x.lineTo(...Q())
              x.strokeStyle='#84f6'
              x.lineWidth=50/Z
              x.stroke()
            })

            t+=1/60
            
          break
          case 6:

            x.globalAlpha=.07*(.25-C(t*1.5)/4)
            x.drawImage(bgimg,0,0,w=c.width,1080)
            x.globalAlpha=1
            x.fillStyle='#00000018'
            x.fillRect(0,0,w,w)
            
            oX=0,oY=0,oZ=4
            Rl=0,Pt=-t/2,Yw=S(t/3)*4
            
            if(!t){
              iPv=.002,iPsv=.005,iPc=10,iPsf=5,iPss=2,iPrr=.025
              G=2
              SP=v=>[S(p=Math.PI*2*Rn())*S(q=Math.PI*(Rn()**.5)/-2+Math.PI*(Rn()<.5?0:1))*v,C(q)*v,C(p)*S(q)*v]
              P=Array(iPc).fill().map(v=>[G*(Rn()-.5),G*(Rn()-.5),G*(Rn()-.5),...SP(iPv),[]])
            }
            
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
              amp=0
              B.map((v,i)=>amp+=v*5**4)
              amp/=550000
            } else {
              trim=0
              B=Array(64).fill(1)
            }

            P.map((v,j)=>{
              v[0]+=v[3]
              v[1]+=v[4]
              v[2]+=v[5]
              if(v[0]<-G/2||v[0]>G/2)v[3]*=-1
              if(v[1]<-G/2||v[1]>G/2)v[4]*=-1
              if(v[2]<-G/2||v[2]>G/2)v[5]*=-1
              for(m=iPsf;m--;)v[6]=[...v[6],[v[0],v[1],v[2],...SP(iPsv),iPss]]
              v[6]=v[6].filter(q=>q[6]>.3)
              v[6].map(q=>{
                d1=Math.hypot(q[0]-v[0],q[1]-v[1],q[2]-v[2])
                X=v[0]+(q[0]+=q[3]*=1.04)
                Y=v[1]+(q[1]+=q[4]*=1.04)
                Z=v[2]+(q[2]+=q[5]*=1.04)
                R(Rl,Pt,Yw,1)
                if(Z>0){
                  x.fillStyle=`hsla(${d1*120-t*400+360/P.length*j},99%,${Math.max(50, amp*15+88-((d1+1)**3*20))}%,${.05/(1+(1+d1)**4/350)})`
                  l=Q(),s=Math.min(500,(.75+amp**3.5/40)*(q[6]-=iPrr)*150/Z/Z)
                  x.fillRect(l[0]-s/2,l[1]-s/2,s,s)
                }
              })
            })
            
            t+=1/60

          break;
          case 7:
          
            if(!t){
              R=(Rl,Pt,Yw,o)=>{
                X=S(p=(A=(M=Math).atan2)(X,Y)+Rl)*(d=(H=M.hypot)(X,Y)),Y=C(p)*d,Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z)),Z=C(p)*d,X=S(p=A(X,Z)+Yw)*(d=H(X,Z)),Z=C(p)*d
                if(o)X+=oX,Y+=oY,Z+=oZ
              }
              Q=q=>[c.width/2+X/Z*800,c.height/2+Y/Z*800]
              Rn=Math.random
            }
            
            if(typeof bgvid !== 'undefined'){
              x.globalAlpha=.4
              x.drawImage(bgvid,0,0,w=c.width,c.height)
              x.globalAlpha=1
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }

            oX=0,oY=0,oZ=14+S(t*3)*4
            Rl=t/4,Pt=0,Yw=t
            iBCsc=3
            G=6

            x.lineJoin=x.lineCap='round'
            if(!t){
              BC=[(-G/2|0)+.5,(G/2|0)+.5,0,[],1,0]
              for(i=6;i--;){
                for(j=4;j--;){
                  BC[3]=[...BC[3],[
                    (a=[S(p=Math.PI*2/4*j+Math.PI/4)*(d=(2**.5)/2),C(p)*d,.5])[i%3]*(l=i<3?1:-1),
                    a[(i+1)%3]*l,
                    a[(i+2)%3]*l]
                  ]
                }
              }
              BCs=Array(iBCsc).fill().map(v=>[JSON.parse(JSON.stringify(BC))])
            }

            if(1){
              BCs.map((q,j)=>{
                for(m=1;m--;){
                  a=JSON.parse(JSON.stringify(q[q.length-1]))
                  n=a[5]
                  if(!(((t*60+j*(20/BCs.length))|0)%3)) n=a[5]=Rn()*6|0
                  tx=a[0]
                  ty=a[1]
                  tz=a[2]
                  tx+=(n==0?1:0)
                  ty+=(n==1?1:0)
                  tz+=(n==2?1:0)
                  tx+=(n==3?-1:0)
                  ty+=(n==4?-1:0)
                  tz+=(n==5?-1:0)
                  tx=Math.max(-G,tx)
                  ty=Math.max(-G,ty)
                  tz=Math.max(-G,tz)
                  tx=Math.min(G,tx)
                  ty=Math.min(G,ty)
                  tz=Math.min(G,tz)
                  a[0]=tx,a[1]=ty,a[2]=tz
                  a[4]=1
                  q.push(a)
                }
              })
            }

            BCs=BCs.map((v,j)=>{
              v.map(v=>{
                a=v[4]/=1.3
                x.strokeStyle=`hsla(${0},99%,${115-(1-a)*75}%,${a/4}`
                x.fillStyle=`hsla(${0},99%,${115-(1-a)*75}%,${a/8}`
                for(m=8;m--;){
                  switch(m){
                    case 0:
                      tx=v[0]
                      ty=v[1]
                      tz=v[2]
                      break
                    case 1:
                      tx=v[0]*-1
                      ty=v[1]
                      tz=v[2]
                      break
                    case 2:
                      tx=v[0]*-1
                      ty=v[1]*-1
                      tz=v[2]
                      break
                    case 3:
                      tx=v[0]*-1
                      ty=v[1]*-1
                      tz=v[2]*-1
                      break
                    case 4:
                      tx=v[0]
                      ty=v[1]*-1
                      tz=v[2]*-1
                      break
                    case 5:
                      tx=v[0]
                      ty=v[1]
                      tz=v[2]*-1
                      break
                    case 6:
                      tx=v[0]*-1
                      ty=v[1]
                      tz=v[2]*-1
                      break
                    case 7:
                      tx=v[0]
                      ty=v[1]*-1
                      tz=v[2]
                      break
                  }
                  v[3].map((v,i)=>{
                    if(i%4==0)x.beginPath()
                    X=v[0]+tx
                    Y=v[1]+ty
                    Z=v[2]+tz
                    R(Rl,Pt,Yw,1)
                    if(Z>0){
                      x.lineTo(...Q())
                      if(i%4==3){
                        x.closePath()
                        x.lineWidth=Math.min(50,1+599/Z/Z)
                        x.stroke()
                        x.fill()
                      }
                    }
                  })
                }
              })
              return v.filter(v=>v[4]>.08)
            })            
            t+=1/60
            
          break
          
          case 8:
          
            if(typeof bgvid !== 'undefined'){
              x.globalAlpha=.6
              x.drawImage(bgvid,0,0,w=c.width,c.height);
              x.globalAlpha=1
            }else{
              x.fillStyle='#0001'
              x.fillRect(0,0,w=c.width,w)
            }
            if(!t){
              iR=c.width/1.5,iV=7,sql=.15,iS=24
              P=[]
            }

            for(m=10;m--;)P=[...P,[c.width/2+S(p=Math.PI*2*Rn())*iR*.95,c.height/2+C(p)*iR*(c.height/c.width),-S(p)*iV,-C(p)*iV,iS,Rn()*80]]
            P.map((v,i)=>{
              aX=v[0]+=v[2]=S(p=Math.atan2(v[2],v[3])+(Rn()-.5)*sql)*iV
              aY=v[1]+=v[3]=C(p)*iV
              x.beginPath()
              x.arc(aX,aY,Math.min(120,((v[4]/=1.01)+1)**2.65/26),0,7)
              x.fillStyle=`hsla(${140+v[5]},99%,${4+Math.hypot(c.width/2-aX,c.height/2-aY)/iR*20}%,${.08*Math.hypot(c.width/2-aX,c.height/2-aY)/iR}`
              x.fill()
            })
            P=P.filter(v=>v[4]>5)

            t+=1/60
          break
          
          case 9:
            if(vid){
              x.globalAlpha=.8
              x.drawImage(bgvid,0,0,w=c.width,c.height)
              x.globalAlpha=1
              x.fillStyle='#0003'
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }


            Rl=t/4,Pt=-t,Yw=t/2
            oX=0,oY=0,oZ=60+S(t*2)*16
            
            if(!t){
              x.lineJoin=x.lineCap='round'
              cl=3,rw=16*3,s=6
            }
            P=Array(cl).fill().map((v,j)=>{
              return Array(rw).fill().map((v,i)=>{
                l2=1
                l1=rw*l2/16
                d=s+S(Math.PI*2/rw*l1*i)*(.15+C(Math.PI*8/rw*l1*i))**3*2
                X=S(p=Math.PI*2/(rw)*l2*i)*d
                Y=C(p)*d
                Z=0
                R(0,0,Math.PI*2/cl*j,0)
                return [X,Y,Z]
              })
            })
            
            for(k=3**3;k--;){
              tx=((k%3)-1.5+.5)*s*4
              ty=(((k/3|0)%3)-1.5+.5)*s*4
              tz=((k/3/3|0)-1.5+.5)*s*4
              d3=Math.hypot(tx,ty,tz)/16+t*2
              P.map((v,j)=>{
                v.map((q,i)=>{
                  x.beginPath()
                  X=q[0]
                  Y=q[1]
                  Z=q[2]
                  R(t+d3,-t/2+d3,t/3+d3,0)
                  X+=tx,Y+=ty,Z+=tz
                  R(Rl,Pt,Yw,1)
                  x.lineTo(...Q())
                  if(i<v.length-1){
                    X=v[i+1][0]
                    Y=v[i+1][1]
                    Z=v[i+1][2]
                  }else{
                    X=v[0][0]
                    Y=v[0][1]
                    Z=v[0][2]
                  }
                  R(t+d3,-t/2+d3,t/3+d3,0)
                  X+=tx,Y+=ty,Z+=tz
                  R(Rl,Pt,Yw,1)
                  if(Z>5){
                    x.lineTo(...Q())
                    x.strokeStyle='#8fb3'
                    x.lineWidth=Math.min(32,1+50000/Z/Z)
                    x.stroke()
                    x.strokeStyle='#eef8'
                    x.lineWidth=Math.min(12,1+5000/Z/Z)
                    x.stroke()
                  }
                })
              })
            }

            t+=1/60

          break
          
          case 10:

            if(typeof bgvid !== 'undefined'){
              x.globalAlpha=.9
              x.drawImage(bgvid,0,0,w=c.width,c.height);
              x.globalAlpha=1
            }else{
              x.fillStyle='#0006'
              x.fillRect(0,0,w=c.width,w)
            }
            if(!t){
              iR=c.width/1.5,iV=7,sql=.15,iS=24
              P=[]
            }

            for(m=10;m--;)P=[...P,[c.width/2+S(p=Math.PI*2*Rn())*iR*.95,c.height/2+C(p)*iR*(c.height/c.width),-S(p)*iV,-C(p)*iV,iS,Rn()*80]]
            P.map((v,i)=>{
              aX=v[0]+=v[2]=S(p=Math.atan2(v[2],v[3])+(Rn()-.5)*sql)*iV
              aY=v[1]+=v[3]=C(p)*iV
              x.beginPath()
              x.arc(aX,aY,Math.min(120,((v[4]/=1.01)+1)**2.65/26),0,7)
              x.fillStyle=`hsla(${140+v[5]},99%,${4+Math.hypot(c.width/2-aX,c.height/2-aY)/iR*20}%,${.08*Math.hypot(c.width/2-aX,c.height/2-aY)/iR}`
              x.fill()
            })
            P=P.filter(v=>v[4]>5)

            t+=1/60

          break;
          
          case 11:

            if(typeof bgvid !== 'undefined'){
              x.globalAlpha=.3
              x.drawImage(bgvid,0,0,w=c.width,c.height+3);
              x.globalAlpha=1
              x.fillStyle='#0002'
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#000'
              x.fillRect(0,0,w=c.width,w)
            }
            if(!t){
              x.fillStyle='#000'
              x.fillRect(0,0,w=c.width,w)
            }

            oX=0,oY=0,oZ=16+S(t)*6
            Rl=0,Pt=-t/2,Yw=t

            Rnd=q=>(((q+23))**3.1)%1

            if(!t){
              x.lineJoin=x.lineCap='butt'
              iBr=9,G=8,iPc=3,iPv=.3,iPs=35,iBc=w/3.5|0
              B=Array(iBc).fill().map((v,i)=>{
                s=iBr*((.0001+Rnd(i*4+3))**(1/3))
                X=S(p1=Math.PI*2*Rnd(i*4))*S(p2=Math.PI/2*(Rnd(i*4+1)**.5)-Math.PI*(Rnd(i*4+2)<.5?1:0))*s
                Y=C(p2)*s
                Z=C(p1)*S(p2)*s
                return [X,Y,Z]
              })
              B=B.map(v=>{
                v[3]=[]
                B.map(q=>{
                  v[3].push(Math.hypot(q[0]-v[0],q[1]-v[1],q[2]-v[2]))
                })
                return v
              })
              P=Array(iPc).fill().map((v,i)=>{
                X=(Rnd(i*9+0)-.5)*G
                Y=(Rnd(i*9+1)-.5)*G
                Z=(Rnd(i*9+2)-.5)*G
                return [X,Y,Z,(Rnd(i*9+3)-.5)*iPv,(Rnd(i*9+4)-.5)*iPv,(Rnd(i*9+5)-.5)*iPv]
              })
            }

            B.map((v,i)=>{
              tx=v[0]
              ty=v[1]
              tz=v[2]
              v[3].map((q,j)=>{
                d=.000001+Math.hypot(B[j][0]-v[0],B[j][1]-v[1],B[j][2]-v[2])
                tx-=(B[j][0]-v[0])*(q-d)
                ty-=(B[j][1]-v[1])*(q-d)
                tz-=(B[j][2]-v[2])*(q-d)
              })
              tx/=B.length
              ty/=B.length
              tz/=B.length
              v[0]=v[0]+(tx-v[0])/4
              v[1]=v[1]+(ty-v[1])/4
              v[2]=v[2]+(tz-v[2])/4
              P.map(q=>{
                d=Math.hypot(q[0]-v[0],q[1]-v[1],q[2]-v[2])
                if(d<iPs/6){
                  v[0]=q[0]+(v[0]-q[0])/d*iPs/6
                  v[1]=q[1]+(v[1]-q[1])/d*iPs/6
                  v[2]=q[2]+(v[2]-q[2])/d*iPs/6
                }
              })
              tx=X=v[0]
              ty=Y=v[1]
              tz=Z=v[2]
              R(Rl,Pt,Yw,1)
              s=200/Z
              l=Q()
              x.globalAlpha=.5+S(t/1.75)/2
              x.fillStyle='#ffffff16'
              x.fillRect(l[0]-s*2,l[1]-s*2,s*4,s*4)
              x.fillStyle='#ffffff26'
              x.fillRect(l[0]-s,l[1]-s,s*2,s*2)
              x.fillStyle='#ffffffaf'
              x.fillRect(l[0]-s/3,l[1]-s/3,s/1.5,s/1.5)
            })
            x.globalAlpha=1

            P.map(v=>{
              X=v[0]+=v[3]
              Y=v[1]+=v[4]
              Z=v[2]+=v[5]
              if(v[0]>G)v[3]*=-1
              if(v[0]<-G)v[3]*=-1
              if(v[1]>G)v[4]*=-1
              if(v[1]<-G)v[4]*=-1
              if(v[2]>G)v[5]*=-1
              if(v[2]<-G)v[5]*=-1
              R(Rl,Pt,Yw,1)
              x.beginPath()
              if(Z>0){
                x.arc(...Q(),150*iPs/Z,0,7)
                x.fillStyle=`hsla(${160},99%,50%,${Math.max(0,S(t)/4)}`
                x.fill()
              }
            })
            
            t+=1/60
            
          break;
          
          case 12:
          
            if(!t){
              setTimeout(()=>go=1,400)
              go=0
            }
            if(vidplaying && go){
              x.globalAlpha = 1
              s=8-S(t/2)*7
              for(i=0;i<25;++i){
                X=c.width/2+((i%5)-2)*384*s-192*s
                Y=c.height/2+((i/5|0)-2)*216*s-108*s
                x.drawImage(bgvid,X,Y,384*s,216*s+5);
              }
              x.globalCompositeOperation='color-dodge'
              x.fillStyle=`#34f`
              x.fillRect(0,0,c.width,c.height)
              x.globalCompositeOperation='source-over'
              X=S(p=t*10)*(d=Math.hypot(c.width/2,c.height/2))
              Y=C(p)*(d=Math.hypot(c.width/2,c.height/2))
              g=x.createLinearGradient(c.width/2+X,c.height/2+Y,c.width/2-X,c.height/2-Y)
              g.addColorStop(0, `hsla(${t*300+0},99%,${50-S(t*3)*50}%,.4)`)
              g.addColorStop(1, `hsla(${t*300+180},99%,${50-S(t*6)*50}%,.4)`)
              x.fillStyle = g
              x.fillRect(0,0,w=c.width,w)
              x.fillStyle = `hsla(0,0%,0%,${.33+S(t*2)/3})`
              x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#000'
              x.fillRect(0,0,w=c.width,w)
            }
          
            x.lineCap='round',R=(n,r,o)=>{Y=S(p=(A=(M=Math).atan2)(Y,Z)+n)*(d=(H=M.hypot)(Y,Z)),Z=C(p)*d,X=S(p=A(X,Z)+r)*(d=H(X,Z)),Z=C(p)*d,Z+=o?6:0};Q=q=>[960+X/Z*L,540+Y/Z*L],a=99,V=1.15,W=.35,B=Array(L=600).fill().map((v,i)=>{j=i/2|0,q=(s=Math.PI)/a*3*j,v=s/a*3*(j+1),X=V*(2+C(q)),Y=S(q)*2,R(Z=0,s*2/a*j),D=X,E=Y,F=Z,X=V*(2+C(v)),Y=S(v)*2,R(Z=0,s*2/a*(j+1)),I=X,J=Y,K=Z
            U=A(I-D,K-F),e=M.acos((J-E)/H(I-D,J-E,K-F)),X=S(p=s*i+s*2/a*j*8)*W,Y=C(p)*W,R(s/2-e,U,Z=0);return[X+D,Y+E,Z+F]})
            for(n=(r=t)*2,j=2;j--;){B.map((v,i)=>{G=`hsla(${.9*i+t*L},99%,${75+S(.002*i+t*6)*35}%,.1)`,X=v[0],Y=v[1],Z=v[2],R(n,r,1);if(j){if(i<L&&!(i%2))x[h='beginPath']();x[O='lineTo'](...Q());if(i<533&&(i%2)==1){x[g='globalAlpha']=1,x[l='lineWidth']=1+50/Z/Z,x[s='strokeStyle']=T='#fffc',x[m='stroke'](),x[l]=L/Z/Z,x[s]=G,x[m]()}}else{if(i>=2){x[g]=1,x[h](),x[O](...Q()),X=B[i-2][0],Y=B[i-2][1],Z=B[i-2][2],R(n,r,1),x[O](...Q()),x[l]=1+50/Z/Z,x[s]=T,x[m](),x[l]=L/Z/Z,x[s]=G,x[m]()}}})}

            t+=1/60
          break
          
          case 13:

            oX=0,oY=0,oZ=12+C(t/2)*4
            Rl=t/2,Pt=-t/4,Yw=S(t/2.5)*10
            

            if(!t){
              go=false
              bgimg=new Image()
              bgimg.addEventListener('load',()=>{
                go=true
              })
              //bgimg.src='MlBFm.jpg'
              bgimg.src='./MlBFm.jpg'
            }

            if(go){
              x.globalAlpha=.075
              x.drawImage(bgimg, 0,0,c.width,c.height)
              x.globalAlpha=1
              x.fillStyle='#1013', x.fillRect(0,0,w=c.width,w)
            }else{
              x.fillStyle='#1026', x.fillRect(0,0,w=c.width,w)
            }
            
            Q=q=>[c.width/2+X/Z*400,c.height/2+Y/Z*400]
            
            if(playing){
              B = new Uint8Array(bufferLength)
              analyser.getByteFrequencyData(B)
              amp=0
              B.map((v,i)=>amp+=v*5**4)
              amp/=550000
            } else {
              trim=0
              B=Array(128).fill(1)
            }
            //B=B.map((v,i)=>{if(i)v=2000;return v})

            rw=50, col=50, sp=9
            for(m=2;m--;){
              for(i=rw*col;i--;){
                x.beginPath()
                j=i+1
                if(m){
                  X=((j%rw)/rw-.5)*sp
                  Z=((j/rw|0)/col-.5)*sp
                }else{
                  Z=((j%rw)/rw-.5)*sp
                  X=((j/rw|0)/col-.5)*sp
                }
                l=Math.hypot(X,Z)/(Math.hypot(rw,col))*1024|0
                d1=-(.75+S(Math.hypot(X,Z)*(3+S(t)*2)-t*10)/4)-B[l]/256*(1+l**.5)/2+.75+(l<2?-.6:0)
                ty=Y=d1*2
                X*=2
                Z*=2
                R(Rl,Pt,Yw,1)
                if(Z>0)x.lineTo(...Q())
                j=i+rw+1
                if(i<rw*col-rw){
                  if(m){
                    X=((j%rw)/rw-.5)*sp
                    Z=((j/rw|0)/col-.5)*sp
                  }else{
                    Z=((j%rw)/rw-.5)*sp
                    X=((j/rw|0)/col-.5)*sp
                  }
                  l=Math.hypot(X,Z)/(Math.hypot(rw,col))*1024|0
                  d=-(.75+S(Math.hypot(X,Z)*(3+S(t)*2)-t*10)/4)-B[l]/256*(1+l**.5)/2+.75+(l<2?-.6:0)
                  Y=d*2
                  X*=2
                  Z*=2
                  R(Rl,Pt,Yw,1)
                  if(Z>0)x.lineTo(...Q())
                }
                x.lineWidth=1
                x.strokeStyle=`hsla(${d1*200-t*300},99%,${70-Math.min(40,Math.abs(d1*25))+(1-ty)**4/3.5}%,${.1-d1/1.5})`
                x.stroke()
              }
            }
            t+=1/60
          break;
        }
      }

      x2.clearRect(0,0,w=canvas2.width|=0,w)
      //x2.save()
      //x2.translate(canvas2.width / 2, canvas2.height / 2)
      //x2.rotate(0)

      sc = 1
      //x2.drawImage(c, -canvas2.width/2*sc, -canvas2.height/2*sc, canvas2.width*sc, canvas2.height*sc)
      x2.drawImage(c,0,0,canvas2.width,canvas2.height)
      //x2.restore()
      requestAnimationFrame(Draw)
    }

    setupAnalyzerAndContent = () =>{
      analyzerSetup=1
      if(!recordingStarted){
        x.lineCap=x.lineJoin='round'
        loaded=0
        mp3 = new Audio()
        mp3.src = song
        if(!mp3)loaded=1
        mp3.addEventListener('canplay',()=>{
          if(!playing){
            loaded=playing=1
            audioCtx = new (window.AudioContext || window.webkitAudioContext)()
            analyser = audioCtx.createAnalyser()
            source = audioCtx.createMediaElementSource(mp3)
            source.connect(analyser)
            analyser.connect(audioCtx.destination)
            analyser.fftSize=fftSize
            trim=analyser.fftSize*.25
            bufferLength = analyser.frequencyBinCount
            mp3.loop = true
            mp3.play()
            if(vid){
              bgvid = document.createElement('video')
              bgvid.src = vid
              bgvid.addEventListener('canplay',()=>{
                if(!vidplaying){
                  vidplaying=1
                  bgvid.loop=true
                  bgvid.play()
                }
              })
            }
          }
        })
      }
    }


    startRecording = () => {
      console.log('recording started')
      recordingStarted=1
      const chunks = []
      const stream = canvas2.captureStream()
      const rec = new MediaRecorder(stream, {videoBitsPerSecond:12000000})
      rec.ondataavailable = e => chunks.push(e.data);
      rec.onstop = e => exportVid(new Blob(chunks, {type: 'video/webm'}));
      rec.start();
      setTimeout(()=>{
        rec.stop()
        canvas2.style.display='none'
        setTimeout(()=>{
          document.querySelectorAll('video').forEach(v=>{
            v.style.display = 'none'
          })
        },200)
        canvas2.style.display='none'
        console.log('recording stopped')
      }, duration);
    }

    exportVid = (blob) => {
      const vid = document.createElement('video')
      vid.src = URL.createObjectURL(blob)
      vid.controls = true
      document.body.appendChild(vid)
      const a = document.createElement('a')
      a.download = 'myvid.webm'
      a.href = vid.src
      a.className='downloadLink'
      a.textContent = 'download the video'
      a.onclick=()=>{
        open(vid.src)
      }
      document.body.appendChild(a)
    }
  
    launch_ = (mode=false) => {
      if(!mode){
        chosenDemo = document.querySelector('#codeArea').value
        console.log(chosenDemo)
        goCustom =  true
      } else {
        document.querySelector('#codeArea').value = chosenDemo
      }
      animationStyle = 2
      Draw()
      if(export_base) setTimeout(()=>startRecording(), initialDelay)
    }

    //if(stage2) Draw()
    //if(!stage2){
    //  launch_()
    //}

  </script>
</html>

