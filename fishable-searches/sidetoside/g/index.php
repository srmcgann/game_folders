<!DOCTYPE html>
<html>
  <head>
    <style>
    <title>SIDE TO SIDE arena/multiplayer</title>
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

      c=document.querySelector('#c')
      x=c.getContext('2d')
      S=Math.sin
      C=Math.cos
      Rn=Math.random
      t=go=0
      rsz=window.onresize=()=>{
        setTimeout(()=>{
          if(document.body.clientWidth > document.body.clientHeight*1.77777778){
            c.style.height = '100vh'
            setTimeout(()=>c.style.width = c.clientHeight*1.77777778+'px',0)
          }else{
            c.style.width = '100vw'
            setTimeout(()=>c.style.height = c.clientWidth/1.77777778 + 'px',0)
          }
          c.width=1920
          c.height=c.width/1.777777778
        },0)
      }
      rsz()

      Draw=()=>{
        if(!t){
          inplay=true
          gamesPlayed = stage = 0
          R=(Rl,Pt,Yw,m)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            if(m){
              X+=oX
              Y+=oY
              Z+=oZ
            }
          }
          Q=()=>[c.width/2+X/Z*800,c.height/2+Y/Z*800]
          I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
          stroke=(scol,fcol)=>{
            if(scol){
              x.closePath()
              x.lineWidth=Math.min(500, 500/Z)
              x.globalAlpha=.2
              x.strokeStyle=scol
              x.stroke()
              x.globalAlpha=1
              x.lineWidth/=5
              x.stroke()
            }
            if(fcol){
              x.fillStyle=fcol
              x.fill()
            }
          }
          spawnBoard=()=>{
            spawnBoardTimer = t+1
            endGameTimer = 0
            inplay=true
            boardSpawning = true
            setTimeout(()=>{
              gamesPlayed++
              inplay=true
              boardSpawning = false
              P=Array(122).fill().map((v,i)=>{
                ret = -1
                if(i>0&&(i<121)&&(i%2)){
                  val = (((i+(i/11|0))|0))%2?3:2
                  ret = val
                }
                return ret
              })
              if(lastWinnerWasOp != -1){
                if(lastWinnerWasOp){
                  turnID = userID == gmid ? 0 : 1
                  opIsRed = true
                }else{
                  turnID = userID == gmid ? 1 : 0
                  opIsRed = false
                }
                turn = turnID
                begCol = 0
              }
            }, 500)
            moves=0
            P=Array(122).fill().map((v,i)=>{
              ret = -1
              if(i>0&&(i<121)&&(i%2)){
                val = (((i+(i/11|0))|0))%2?3:2
                ret = val
              }
              return ret
            })
            gameover = 0
            begCol=-1
            sp=2, mx=my=turn=0
            B=Array(100).fill().map((v,i)=>{
              X=((i%10)-5+.5)*sp
              Y=((i/10|0)-5+.5)*sp
              Z=0
              return [X,Y,Z,i]
            })
          }

          c.onmousedown=e=>{
            switch(stage){
              case 0:
                if(!gameover && e.button==0 && begCol != -1){
                  turn=lastWinnerWasOp!=-1?(lastWinnerWasOp?1:0):turnID
                  stage++
                  moves++
                }
              break
              case 1:
                if(e.button==0){
                  if(gameover && !boardSpawning && t>endGameTimer) spawnBoard()
                  if(turnID){ // guest
                    if(!(moves%2)) return
                  }else{      // Op
                    if(moves%2) return
                  }
                  if(t < spawnBoardTimer) return
                  if(P[tgt_]==-1){
                    turn=(turn+1)%2
                    P[tgt_]=turnID?0:1
                    moves++
                    sync()
                  }
                }
            }
          }
          c.onmousemove=e=>{
            let rect = c.getBoundingClientRect()
            mx=(e.pageX-rect.left)/c.clientWidth*c.width
            my=(e.pageY-rect.top)/c.clientHeight*c.height
          }
          bgimg = new Image()
          bgimg.onload=()=>{
            go=true
            spawnBoard()
          }
          go=true;spawnBoard()
          bgimg.src='/games_shared_assets/sidetoside_bg.jpg'
          //bgimg.src='/proxy.php?url=https://jsbot.twilightparadox.com/1NWwfr.jpg'

          PlayerInit = idx => { // called initially & when a player dies
            Players[idx].B = B
            Players[idx].P = P
            stage = 0
            if(turnID){
              turn=0
              stage=1
            }else{
              turn=0
              stage=1
            }
          }


          addPlayers = playerData => {
            PlayerLs = 1
            playerData.score = 0
            Players = [...Players, {playerData}]
            PlayerCount++
            PlayerInit(Players.length-1)
          }
          
          masterInit = () => { // called only initially
            PlayerCount      = 0
            Players          = []
            score            = 0
          }
          masterInit()
        }

        if(go){
          x.globalAlpha=.2
          x.drawImage(bgimg,0,0,c.width,c.height)
          x.globalAlpha=1
          x.fillStyle='#0004'
          x.fillRect(0,0,c.width,c.height)
          x.lineCap=x.lineJoin='round'
          oX=oY=0, oZ=20
          Rl=0
          Pt=gameover?C(t/2)*10:0
          Yw=gameover?S(t/2)*10:0

          B.map(v=>{
            tx=v[0]
            ty=v[1]
            tz=v[2]
            ls=sp*(2**.5/2)
            x.beginPath()
            for(i=4;i--;){
              X=tx+S(p=Math.PI*2/4*i+Math.PI/4)*ls
              Y=ty+C(p)*ls
              Z=tz
              R(Rl,Pt,Yw,1)
              if(Z>0) x.lineTo(...Q())
            }
            stroke('#fff2','4f86')
          })

          for(i=110;i--;){
            if(i%10<9&&!(i%2)&&!((i/10|0)%2)){
              X=((i%10)-5+.5)*sp+sp/2
              Y=((i/10|0)-5+.5)*sp-sp/2
              Z=0
              x.beginPath()
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 300/Z)
              x.arc(...Q(),s,0,7)
              x.fillStyle='#f00'
              x.fill()

              X=((i/10|0)-5+.5)*sp-sp/2
              Y=((i%10)-5+.5)*sp+sp/2
              Z=0
              x.beginPath()
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 300/Z)
              x.arc(...Q(),s,0,7)
              x.fillStyle='#08F'
              x.fill()
            }
          }
          l=2**.5*5*sp*1.01
          for(i=4;i--;){
            x.beginPath()
            x.lineWidth=Math.min(500, 600/Z)
            x.strokeStyle=i%2?'#f008':'#08F8'
            X=S(p=Math.PI*2/4*i+Math.PI/4)*l
            Y=C(p)*l
            Z=0
            R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=S(p=Math.PI*2/4*(i+1)+Math.PI/4)*l
            Y=C(p)*l
            Z=0
            R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle=i%2?'#f00':'#08F'
            x.stroke()
          }
          switch(stage){
            case 0:
              l_=[]
              x.font='90px courier'
              x.textAlign='center'
              x.fillStyle='#fff'
              x.fillText('choose a color to go first',1260,70)
              x.beginPath()
              X=-16, Y=0, Z=0
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 2e3/Z)
              x.arc(...(l_[0]=Q()),s,0,7)
              x.fillStyle='#f00'
              x.fill()
              x.strokeStyle='#fff8'
              ga=t*200%200
              x.beginPath()
              x.arc(...Q(),s+ga,0,7)
              x.globalAlpha=1/(1+ga**3/99999)
              x.lineWidth=Math.min(500,500/Z)
              x.stroke()
              X=16, Y=0, Z=0
              R(Rl,Pt,Yw,1)
              x.beginPath()
              x.arc(...Q(),s+ga,0,7)
              x.globalAlpha=1/(1+ga**3/99999)
              x.lineWidth=Math.min(500,500/Z)
              x.stroke()
              x.globalAlpha=1
              x.beginPath()
              s=Math.min(500, 2e3/Z)
              x.arc(...(l_[1]=Q()),s,0,7)
              x.fillStyle='#08f'
              x.fill()
              
              for(let m=2;m--;){
                v=l_[m]
                d=Math.hypot(v[0]-mx,v[1]-my)
                if(d<200){
                  begCol=m
                  x.beginPath()
                  x.strokeStyle='#4ff8'
                  s=120
                  x.arc(...v,s,0,7)
                  x.lineWidth=1e3/Z
                  x.stroke()
                }
              }
            break
            case 1:
              tgt_=-1
              for(i=122;i--;){
                if((!(i%2))&&((i/11|0)>0)&&(i%11!==10)&&(i%11)&&((i/11|0)<10)){
                  X=((i%11)-5.5+.5)*sp
                  Y=((i/11|0)-5.5+.5)*sp
                  Z=0
                  R(Rl,Pt,Yw,1)
                  tgt=Q()
                  d=Math.hypot(tgt[0]-mx,tgt[1]-my)
                  if(d<sp*25){
                    x.beginPath()
                    s=Math.min(500, 500/Z)
                    x.arc(...Q(),s,0,7)
                    x.fillStyle='#0f0'
                    if(P[i]==-1)x.fill()
                    tgt_=i
                  }
                }
              }
              P.map((v,i)=>{
                if(v!=-1&&v<2){
                  if((v&&((i/11|0)%2))||(!v&&(!((i/11|0)%2)))){
                    x.fillStyle=v?'#f008':'#08fa'
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.beginPath()
                    s=Math.min(500,750/Z)
                    x.arc(...Q(),s,0,7)
                    x.fill()
                    x.beginPath()
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp-sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp+sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    x.lineWidth=Math.min(500,500/Z)
                    x.strokeStyle=v?'#f44c':'#48fc'
                    x.stroke()
                  }else{
                    x.fillStyle=v?'#f008':'#08fa'
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.beginPath()
                    s=Math.min(500,750/Z)
                    x.arc(...Q(),s,0,7)
                    x.fill()
                    x.beginPath()
                    X=((i%11)-5.5+.5)*sp-sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    X=((i%11)-5.5+.5)*sp+sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    x.lineWidth=Math.min(500,500/Z)
                    x.strokeStyle=v?'#f44c':'#48fc'
                    x.stroke()
                  }
                }
              })
              recursered=(idx)=>{
                if(P[idx]!==1 && P[idx]!=3) return
                if(idx>120 || idx<1) return
                if(memo[idx]) return
                if(idx>100){
                  good = true
                  return
                }
                memo[idx]=true
                let X=idx%11
                let Y=idx/11|0
                recursered(idx+1)
                recursered(idx-1)
                recursered(idx+11)
                recursered(idx-11)
              }
              for(let j=5;j--;){
                good=false
                memo=Array(122).fill(false)
                recursered(1+j*2,0)
                if(good){
                  if(!gameover){
                    lastWinnerWasOp = opIsRed
                    if(lastWinnerWasOp){
                      if((+gmid) == (+userID)){
                        score++
                      }
                      Players.filter(AI=>+AI.playerData.id == +gmid)[0].score++
                    }else{
                      if((+gmid) != (+userID)){
                        score++
                      }
                      Players.filter(AI=>+AI.playerData.id != +gmid)[0].score++
                    }
                  }
                  gameover=true, victor=1, inplay=false
                  if(!endGameTimer) endGameTimer = t+1
                }
              }

              //check blue victory
              recurseblue=(idx)=>{
                if(P[idx]!==0 && P[idx]!=2) return
                if(idx>120 || idx<1) return
                if(memo[idx]) return
                if(idx%11==10){
                  good = true
                  return
                }
                memo[idx]=true
                let X=idx%11
                let Y=idx/11|0
                recurseblue(idx+1)
                recurseblue(idx-1)
                recurseblue(idx+11)
                recurseblue(idx-11)
              }
              for(let j=5;j--;){
                good=false
                memo=Array(122).fill(false)
                recurseblue(11+11*j*2,0)
                if(good) {
                  if(!gameover){
                    lastWinnerWasOp = !opIsRed
                    if(lastWinnerWasOp){
                      if((+gmid) == (+userID)){
                        score++
                      }
                      Players.filter(AI=>+AI.playerData.id == +gmid)[0].score++
                    }else{
                      if((+gmid) != (+userID)){
                        score++
                      }
                      Players.filter(AI=>+AI.playerData.id != +gmid)[0].score++
                    }
                  }
                  gameover=true, victor=0, inplay=false
                }
              }
              if(!gameover){
                x.font='90px courier'
                x.textAlign='center'
                let rb
                if(!moves){
                  rb = false
                }else{
                  if(lastWinnerWasOp != -1){
                    rb = (turn + lastWinnerWasOp)%2
                  }else{
                    rb = turn%2
                  }
                }


                x.fillStyle=rb?'#08f':'#f00'
                x.fillText((rb?'blue':'red')+'\'s'+' turn',1260,80)
              } else {
                x.globalAlpha=1
                x.font='200px courier'
                x.textAlign='center'
                x.fillStyle=!victor?'#08f':'#f00'
                x.strokeStyle=!victor?'#8cf8':'#faa8'
                x.lineWidth=20
                x.strokeText((!victor?'blue':'red')+' WON!',1260,500)
                x.fillText((!victor?'blue':'red')+' WON!',1260,500)
                x.fillStyle='#fff'
                x.font='120px courier'
                x.fillText('click to play again...',1260,640)
              }



              x.font="64px Courier Prime"
              x.textAlign = 'left'
              x.fillStyle = '#fff'
              if(Players.length){
                if(!moves){
                  if(lastWinnerWasOp !=-1){
                    if(lastWinnerWasOp){
                      x.fillText(Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name + ` goes first as RED. waiting...`,20,c.height-10)
                    }else{
                      x.fillText(Players.filter(v=>+v.playerData.id != +gmid)[0].playerData.name + ` goes first as RED. waiting...`,20,c.height-10)
                    }
                  }else{
                    x.fillText(Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name + ` goes first as RED. waiting...`,20,c.height-10)
                  }
                }else{
                  OPname = Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name
                  challengerName = Players.filter(v=>+v.playerData.id != +gmid)[0].playerData.name
                  if(inplay){

                    let myMove = true
                    if(turnID){ // guest
                      if(!(moves%2)) myMove = false
                    }else{      // Op
                      if(moves%2) myMove = false
                    }


                    if(myMove){
                        x.fillText(`your move...`, 20, c.height-10)
                    }else{
                      x.fillText(`waiting for ${(moves+(opIsRed?0:1))%2?challengerName:OPname} to make a move...`,20,c.height-10)
                    }
                  }else{
                    x.fillText(`game over! ${lastWinnerWasOp?OPname:challengerName} won...`,20,c.height-10)
                  }
                }
              }
              x.font = (fs = 64) + "px Courier Prime"
              x.fillStyle = '#fff'
              //x.fillText('scores -', 100, 40)
              Players.map((AI, idx) => {
                x.fillText(AI.playerData['score'] + ' <- ' + AI.playerData['name'], 200, 40 + (fs+1)*idx)
              })

            break

          }

        } else {
          x.fillStyle='#0008'
          x.fillRect(0,0,c.width,c.height)
        }

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
          Players.map((AI, idx) => {
            if(AI.playerData.id == userID){
              individualPlayerData['id'] = userID
              individualPlayerData['name'] = AI.playerData.name
              individualPlayerData['time'] = AI.playerData.time
              if(typeof score != 'undefined') {
                AI.score = score
                AI.playerData.score = score
                individualPlayerData['score'] = score
              }
              if(typeof P != 'undefined'){
                individualPlayerData['P'] = P
              }
              //if(typeof stage != 'undefined') individualPlayerData['stage'] = stage
              //if(typeof score != 'undefined') {
              //  AI.moves = moves
              //  AI.playerData.moves = moves
              //  individualPlayerData['moves'] = moves
              //}
              //if(typeof lastWinnerWasOp != 'undefined' && lastWinnerWasOp != -1) individualPlayerData['lastWinnerWasOp'] = lastWinnerWasOp
            }else{
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    // straight mapping of incoming data <-> players
                    //case 'stage': if(typeof el[key] != 'undefined' && el[key] > stage) stage = el[key]; break;
                    case 'P':
                      if(typeof el[key] != 'undefined' &&
                          el[key].filter(v=>v!=-1).length - P.filter(v=>v!=-1).length == 1
                        ){
                        turn = (turn+1)%2
                        moves++
                        P = el[key];
                        console.log('rec P', P, 'turn: ' + turn, 'moves: ' + moves, 'turnID: ' + turnID)
                      }
                    break;
                    //case 'moves': if(typeof el[key] != 'undefined' && +el[key] > moves) moves = +el[key]; break;
                    case 'lastWinnerWasOp': if(typeof el[key] != 'undefined' && el[key] != -1) lastWinnerWasOp = el[key]; break;
                    case 'score':
                      if(typeof el[key] != 'undefined'){
                        AI[key] = +el[key]
                        AI.playerData[key] = +el[key]
                      }
                    break;
                  }
                })
              }
            }
          })
          for(i=0;i<Players.length;i++) if(Players[i]?.playerData?.id == userID) ofidx = i
        }
      }

      recData              = []
      lastWinnerWasOp      = -1
      opIsRed              = true
      ofidx                = 0
      moves                = 0
      spawnBoardTimer      = 0
      endGameTimer         = 0
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
                setInterval(()=>{sync()}, pollFreq = 1000)  //ms
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
