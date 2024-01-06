<?php
  require_once('../db.php');
  $uri = explode('gmid=', $_SERVER['REQUEST_URI']);
  if(sizeof($uri)>1){
    $gmid = explode('&', $uri[1])[0];
    $sql = "SELECT name FROM platformSessions WHERE id = $gmid";
    $res = mysqli_query($link, $sql);
    $gameMaster = '';
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $gameMaster = $row['name'];
    }
  }
?>
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
        height: 100vh;
        overflow: hidden;
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
    </style>
  </head>
  <body>
    <canvas id="c" tabindex=0></canvas>
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
          
          stroke = (scol, fcol, lwo=1, od=true, oga=1) => {
            if(scol){
              //x.closePath()
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

          burst = new Image()
          burst.src = "https://srmcgann.github.io/temp/burst.png"
          
          starsLoaded = false, starImgs = [{loaded: false}]
          starImgs = Array(9).fill().map((v,i) => {
            let a = {img: new Image(), loaded: false}
            a.img.onload = () => {
              a.loaded = true
              setTimeout(()=>{
                if(starImgs.filter(v=>v.loaded).length == 9) starsLoaded = true
              }, 0)
            }
            a.img.src = `https://srmcgann.github.io/stars/star${i+1}.png`
            return a
          })
          
          splash = new Image()
          splash.src = 'https://srmcgann.github.io/orbs/splash.jpg'
          starfield = document.createElement('video')
          loaded = false
          starfield.oncanplay = () =>{
            starfield.play()
            loaded = true
          }
          starfield.loop = true
          starfield.muted = true
          starfield.src = 'https://srmcgann.github.io/orbs/compound-starfield.mp4'
          
          
          cursorPos = 0
          curInputLeft = curInputRight = ''
          mask = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-=_+`~\][|}{\'":;/.,?>< '
               
          mx = my = 0
          c.onmousemove = e => {
            e.preventDefault()
            e.stopPropagation()
            rect = c.getBoundingClientRect()
            mx = (e.pageX-rect.x)/c.clientWidth*c.width
            my = (e.pageY-rect.y)/c.clientHeight*c.height
          }
          
          cFocused = false
          c.onfocus = () => {
            cFocused = true
          }
          
          c.onblur = () => {
            cFocused = false
          }
          
          c.onmousedown = e => {
            c.focus()
            e.preventDefault()
            e.stopPropagation()
            if(e.button == 0){
              buttons.map(button=>{
                if(button.hover){
                  eval(button.callback + '()')
                }
              })
            }
          }

          c.onkeydown = e => {
            e.preventDefault()
            e.stopPropagation()
            switch (e.key){
              case 'Enter':
                if((curInputLeft + curInputRight) != ''){
                  join()
                }
              break
              case 'Backspace':
                curInputLeft = curInputLeft.substr(0, curInputLeft.length-1)
              break
              case 'Delete':
                curInputRight = curInputRight.substr(1)
              break
              case 'ArrowLeft':
                curInputRight = curInputLeft.substr(curInputLeft.length-1) + curInputRight
                curInputLeft = curInputLeft.substr(0, curInputLeft.length-1)
              break
              case 'ArrowUp':
              break
              case 'ArrowRight':
                curInputLeft = curInputLeft + curInputRight.substr(0,1)
                curInputRight = curInputRight.substr(1)
              break
              case 'ArrowDown':
              break
              default:
                curInputLeft += mask.indexOf(l=e.key) !== -1 ? l : ''
              break 
            }
          }
          
          c.focus()
          
          //globals
          userName = ''
          
          renderButton = (callback, X, Y, w, h, caption) => {
            tx = X
            ty = Y
            x.fillStyle = '#0f8d'
            x.fillRect(tx,ty,w,h)
            x.strokeStyle = '#0f84'
            x.lineWidth = 10
            x.strokeRect(X1=tx, Y1=ty, w, h)
            x.font = (fs = 50) + "px Courier Prime"
            x.fillStyle = '#0f8e'
            x.fillStyle = '#042f'
            x.fillText(caption, tx + 20, ty+=fs)
            
            X2=X1+w
            Y2=Y1+h
            if(mx>X1 && mx<X2 && my>Y1 && my<Y2){
              if(buttonsLoaded){
                buttons[bct].hover = true
              }else{
                buttons=[...buttons, {callback,X1,Y1,X2,Y2,hover:true}]
              }
              c.style.cursor = 'pointer'
            }else{
              if(buttonsLoaded){
                buttons[bct].hover = false
              }else{
                buttons=[...buttons, {callback,X1,Y1,X2,Y2,hover:false}]
              }
            }
            bct++
          }
          
          renderInput = (textVar, X, Y, w, h, placeholder, caption) => {
            tx = X
            ty = Y
            x.fillStyle = '#112c'
            x.fillRect(tx,ty,w,h)
            x.strokeStyle = '#2fa4'
            x.lineWidth = 10
            x.strokeRect(tx, ty, w, h)
            let fs
            x.font = (fs = 50) + "px Courier Prime"
            x.fillStyle = '#0f8a'
            x.fillText(caption, tx, ty-fs/2, w, h)
            x.fillStyle = eval(`${textVar} ? '#fff' : '#888'`) 
            eval(`x.fillText(${textVar} ? ${textVar} : placeholder, tx + 20, ty+=fs)`)
            eval(`${textVar} = curInputLeft + curInputRight`)
            if(showcursor && ((t*60|0)%30)<15){
              ofx = x.measureText(curInputLeft).width
              x.beginPath()
              x.lineTo(tx + ofx + fs/2, ty-fs/1.25)
              x.lineTo(tx + ofx + fs/2, ty-fs/1.25+fs)
              Z = 1
              stroke('#f00','',.25,true)
            }
          }
          buttonsLoaded = false
          buttons = []
        }
        
        oX=0, oY=0, oZ=16
        Rl=S(t/8)/3, Pt=0, Yw=0
        
        x.globalAlpha = 1
        x.fillStyle='#000'
        x.fillRect(0,0,c.width,c.height)
        x.lineJoin = x.lineCap = 'round'
        
        x.globalAlpha = 1

        if(loaded){
          showcursor = cFocused
          bct = 0
          x.globalAlpha = .6
          x.drawImage(splash,0,0,c.width,c.height)
          x.globalAlpha = 1
          x.fillStyle = '#02040844'
          x.fillRect(0,0,c.width,c.height)
          x.globalAlpha = .2
          x.drawImage(starfield,0,0,c.width,c.height)
          x.globalAlpha = 1
          
          w = c.width -100
          h = c.height -75
          x.fillStyle = '#2084'
          x.fillRect(c.width/2-w/2,c.height/2-h/2,w,h)
          x.strokeStyle = '#40f4'
          x.lineWidth = 20
          x.strokeRect(c.width/2-w/2,c.height/2-h/2,w,h)
          
          x.font = (fs = 55) + 'px Courier Prime'
          x.fillStyle = '#8fca'
          x.textAlign = 'left'
          ofy = 50
          x.fillText('"'+gameMaster+'" has challenged you to an online game of...', fs*2.5, ofy + fs*2)
          ofy += fs*8
          x.font = (fs = 200) + 'px Courier Prime'
          x.fillStyle = '#0f8c'
          x.fillText('ORBS!', fs*2, ofy)
          x.font = (fs = 55) + 'px Courier Prime'
          x.fillStyle = '#8fca'

          ofy += fs*1.5
          renderInput('userName', fs*4, ofy + fs*1.25, 800, 70, 'name', 'enter your name')
          
          ofy += fs*1.5
          renderButton('join', fs*4, ofy + fs*1.25, 375, 70, ' join game')
          buttonsLoaded = true
        }

        t+=1/60
        requestAnimationFrame(Draw)
      }
      

      userName = ''
      gameConnected = false
      
      gameMaster = '<?php echo $gameMaster; ?>'
      join = () => {
        let sendData = {
          gameID,
          userName,
          gmid,
          userID: top.userID ? top.userID : ''
        }
        fetch('joinGame.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res=>res.json()).then(data=>{
          if(data[0]){
            console.log(data)
            if(data[1] != 'fail'){
              console.log(data[2]) //msg
              href = top.window.location.href
              if(href.indexOf('?p=') == -1 && href.indexOf('&p=') == -1){
                top.history.pushState({},null, href + "&p=" + data[4])
              }
              top.doJoined(data[4])
            }else{
              console.log('error! game ID not found!')
            }
          }else{
            console.log('error! crap')
          }
        })
      }

      Draw()

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

      gameID = ''
      gmid = ''
      if(location.href.indexOf('?g=') !== -1){
        gameSlug = location.href.split('?g=')[1].split('&')[0]
        gmid = location.href.split('gmid=')[1].split('&')[0]
        console.log('[reg] game slug: ' + gameSlug)
        gameID = alphaToDec(gameSlug)
        console.log('[reg]       (id: ' + gameID + ')')
        if(top.href.indexOf('?p=') !== -1) userID = top.href.split(pchoice='?p=')[1].split('&')[0]
        if(top.href.indexOf('&p=') !== -1) userID = top.href.split(pchoice='&p=')[1].split('&')[0]
      }
    </script>
  </body>
</html>