<!DOCTYPE html>
<html>
  <head>
    <title>TIC TAC TOE arena/multiplayer</title>
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
        if(!t){
          Rn=Math.random
          bgimg=new Image()
          bgimg.src='./network.jpg'
          catface = new Image()
          catface.src = './cat-face.gif'
          mx=my=moves=0
          winnerLine = []
          inplay=true
          sqid=-1
          gamesPlayed = 0
          window.onclick=e=>{
            if(inplay && B[sqid]==-1) play()
          }
          window.ontouchend=e=>{
            setTimeout(()=>{
              if(inplay && B[sqid]==-1) play()
            },500)
          }
          window.ontouchstart=e=>{
            r=c.getBoundingClientRect()
            mx=(e.pageX-r.left)/c.clientWidth*c.width
            my=(e.pageY-r.top)/c.clientHeight*c.height
          }
          window.ontouchmove=e=>{
            r=c.getBoundingClientRect()
            mx=(e.pageX-r.left)/c.clientWidth*c.width
            my=(e.pageY-r.top)/c.clientHeight*c.height
          }
          window.onmousemove=e=>{
            r=c.getBoundingClientRect()
            mx=(e.pageX-r.left)/c.clientWidth*c.width
            my=(e.pageY-r.top)/c.clientHeight*c.height
          }
          turn=false
          B=Array(9).fill(-1)

          idx=(X,Y)=>{
            return Y*3+X
          }

          idx2=val=>{
            return [val%3, val/3|0]
          }

          fill=(idx, col)=>{
            l=idx2(idx)
            X=(l[0]-1.5)*s*2
            Y=(l[1]-1.5)*s*2
            x1=c.width/2+X
            y1=c.height/2+Y
            x.fillStyle=col
            x.fillRect(x1,y1,s*2,s*2)
          }

          restart=()=>{
            document.querySelectorAll('.replay')[0].style.display='none'
            setTimeout(()=>{
              turn=false
              gamesPlayed++
              B=Array(9).fill(-1)
              inplay=true
              winnerLine = []
              sqid=-1
              if(lastWinnerWasOp != -1){
                if(lastWinnerWasOp){
                  turnID = userID == gmid ? 0 : 1
                  opIsX = true
                }else{
                  turnID = userID == gmid ? 1 : 0
                  opIsX = false
                }
              }

              moves=0
            },500)
          }

          doEnding=()=>{
            inplay=false
            let el = document.querySelectorAll('.replay')[0]
            el.style.display='block'
            el.innerHTML = winnerLine.length>1?'play again':'"cat\'s game!"<br><div style="display: inline-block;width:100px;height:50px;background-size: 100px 50px;background-image: url(./cat-face.gif)"></div><br>play again'
          }

          victor=(O,n,q)=>{
            a=[]
            if(O){ //x won
              lastWinnerWasOp = !opIsX
            }else{
              lastWinnerWasOp = opIsX
            }
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
            for(let m=3;m--;){
              switch(q){
               case 0: //ROW
                 a=[...a, [n, m]]
               break
               case 1: //COLUMN
                 a=[...a, [m, n]]
               break
               case 2: //DIAGONAL
                 l=[[0,4,8],[2,4,6]]
                 a=[...a, [...idx2(l[n][m])]]
               break
              }
            }
            winnerLine=a
          }

          check=()=>{
            let inplay=true
            for(O=2;O--;){
              tgval=O?1:0
              for(n=3;n--;){
                for(ct=0, m=3; m--;) ct+=B[id=idx(n, m)]==tgval?1:0
                if(inplay && ct==3) inplay=false, victor(O,n,0)
                for(ct=0, m=3; m--;) ct+=B[id=idx(m, n)]===tgval?1:0
                if(inplay && ct==3) inplay=false, victor(O,n,1)
              }
              l=[[0,4,8],[2,4,6]]
              for(m=3;m--;)for(n=2;n--;){
                if(inplay && B[l[n][m]]===tgval&&B[l[n][(m+1)%3]]===tgval&&B[l[n][(m+2)%3]]==tgval) inplay=false,victor(O,n,2)
              }
            }
            return inplay
          }

          play=()=>{
            inplay=false
            B.map((v,i)=>{
              if(v==-1){
                inplay=true
              }
            })
            if(!check() || !inplay){
              doEnding()
              return
            }
            if(turnID){ // guest
              if(!(moves%2)) return
            }else{      // Op
              if(moves%2) return
            }
            moved=false
            B[sqid]=turnID
            /*if(!moves){
              if(sqid==1||sqid==3||sqid==5||sqid==7){
                l=[4]
                for(i=4;i--;){
                  if(!moved){
                    id = l[(i)%l.length]
                    if(B[id]==-1){B[id]=true,moves++;return}
                  }
                }
              }
              if(sqid==0||sqid==2||sqid==6||sqid==8){
                if(B[4]==-1){B[4]=true,moves++;return}
                l=[1,3,5,7]
                for(i=4;i--;){
                  if(!moved){
                    id = l[(i)%l.length]
                    if(B[id]==-1){B[id]=true,moves++;return}
                  }
                }
              }
              if(B[4]===false){
                l=[0,2,6,8]
                for(i=4;i--;){
                  if(!moved){
                    id = l[(i)%l.length]
                    if(B[id]==-1){B[id]=true,moves++;moved=true;return}
                  }
                }
              }
            }
            */
            moves++
            for(O=2;O--;){
              if(!moved){
                tgval=O?true: false
                for(n=3;n--;){
                  for(ct=cs=0, m=3; m--;){
                    if(B[id=idx(m, n)]==-1) bk=id, cs++
                    ct+=B[id]===tgval?1:0
                  }
                  if(!moved && cs && ct==2) B[bk]=true, moved=true
                  for(ct=cs=0, m=3; m--;){
                    if(B[id=idx(n, m)]==-1) bk=id, cs++
                    ct+=B[id]===tgval?1:0
                  }
                  if(!moved && cs && ct==2) B[bk]=true, moved=true
                }
                l=[[0,4,8],[2,4,6]]
                for(m=3;m--;)for(n=2;n--;){
                  if(!moved&&B[l[n][m]]===tgval&&B[l[n][(m+1)%3]]===tgval&&B[l[n][(m+2)%3]]==-1)B[l[n][(m+2)%3]]=true,moved=true
                }
              }
            }

            inplay=false
            B.map((v,i)=>{
              if(v==-1){
                inplay=true
              }
            })
            if(!check() || !inplay){
              doEnding()
            } else if(!moved) {
              /*a=[]
              B.map((v,i)=>{
                if(v==-1) a=[...a, i]
              })
              if(moves==2){
                if(sqid==0||sqid==2||sqid==6||sqid==8){
                  if(B[4]==-1){B[4]=true,moves++;return}
                  l=B[4]==false?[0,2,6,8]:[1,3,5,7]
                  for(i=4;i--;){
                    if(!moved){
                      id = l[(i)%l.length]
                      if(B[id]==-1){B[id]=true,moves++;return}
                    }
                  }
                }
              }
              if(B[4]==-1){B[4]=true,moves++;return}      
              B[a[Math.random()*a.length|0]]=true*/
            }
          }

          drawBoard=()=>{
            x.globalAlpha=.2
            x.drawImage(bgimg,0,0,c.width,c.height)
            x.fillStyle='#0004'
            x.fillRect(0,0,c.width,c.height)
            x.lineJoin=x.lineCap='round'
            x.beginPath()
            x.lineTo(c.width/2-s,c.height/2-s2)
            x.lineTo(c.width/2-s,c.height/2+s2)
            x.lineWidth=50
            x.strokeStyle='#fff3'
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle='#fff'
            x.stroke()
            x.beginPath()
            x.lineTo(c.width/2+s,c.height/2-s2)
            x.lineTo(c.width/2+s,c.height/2+s2)
            x.lineWidth=50
            x.strokeStyle='#fff3'
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle='#fff'
            x.stroke()
            x.beginPath()
            x.lineTo(c.width/2-s2,c.height/2-s)
            x.lineTo(c.width/2+s2,c.height/2-s)
            x.lineWidth=50
            x.strokeStyle='#fff3'
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle='#fff'
            x.stroke()
            x.beginPath()
            x.lineTo(c.width/2-s2,c.height/2+s)
            x.lineTo(c.width/2+s2,c.height/2+s)
            x.lineWidth=50
            x.strokeStyle='#fff3'
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle='#fff'
            x.stroke()
            x.globalAlpha=1
            
            x.font="64px Courier Prime"
            x.fillStyle = '#fff'
            if(Players.length){
              if(!moves){
                if(lastWinnerWasOp !=-1){
                  if(lastWinnerWasOp){
                    x.fillText(Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name + ` goes first as X. waiting...`,20,c.height-10)
                  }else{
                    x.fillText(Players.filter(v=>+v.playerData.id != +gmid)[0].playerData.name + ` goes first as X. waiting...`,20,c.height-10)
                  }
                }else{
                  x.fillText(Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name + ` goes first as X. waiting...`,20,c.height-10)
                }
              }else{
                OPname = Players.filter(v=>+v.playerData.id == +gmid)[0].playerData.name
                challengerName = Players.filter(v=>+v.playerData.id != +gmid)[0].playerData.name

                if(check()){  // this player's move now
                  if(inplay){
                    x.fillText(`your move...`, 20, c.height-10)
                  }else{
                    x.fillText(`game over! ${lastWinnerWasOp?OPname:challengerName} won...`,20,c.height-10)
                  }
                }else{
                  x.fillText(`waiting for ${(moves+(opIsX?0:1))%2?challengerName:OPname} to make a move...`,20,c.height-10)
                }
              }
            }
            x.font = (fs = 64) + "px Courier Prime"
            x.fillStyle = '#fff'
            Players.map((AI, idx) => {
              x.fillText(AI.playerData['score'] + ' <- ' + AI.playerData['name'], 200, 40 + (fs+1)*idx)
            })
          }

          PlayerInit = idx => { // called initially & when a player dies
            Players[idx].B = B
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

        if(xpicloaded && opicloaded){

          s=c.width>c.height?c.width/12:c.height/12
          s2=s*3
          drawBoard()
       
          x.fillStyle='#4f8'
          x.fillRect(mx-s/8,my-s/8,s/4,s/4)

          B.map((v,i)=>{
            if(v!=-1){
              l=idx2(i)
              X=(l[0]-1.5)*s*2
              Y=(l[1]-1.5)*s*2
              x1=c.width/2+X
              y1=c.height/2+Y
              x.drawImage(v?xpic:opic,x1,y1,s*2,s*2)
            }
          })

          if(inplay){
            sqid=-1
            for(i=9;i--;){
              l=idx2(i)
              X=(l[0]-1.5)*s*2
              Y=(l[1]-1.5)*s*2
              x1=c.width/2+X
              y1=c.height/2+Y
              x2=x1+s*2
              y2=y1+s*2
              if(mx>x1&&mx<x2&&my>y1&&my<y2){
                sqid=i
                fill(i, B[i]==-1?'#4f82':'#f442')
              }
            }
          }
          if(winnerLine.length){
            x.beginPath()
            winnerLine.map(v=>{
              X=c.width/2+(v[0]-1)*s*2
              Y=c.height/2+(v[1]-1)*s*2
              x.lineTo(X,Y)
            })
            x.strokeStyle='#f00'
            x.lineWidth=40
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle='#faa'
            x.stroke()
          }
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
              if(typeof score != 'undefined') {
                AI.score = score
                AI.playerData.score = score
                individualPlayerData['score'] = score
              }
              //if(typeof inplay != 'undefined') individualPlayerData['inplay'] = inplay
              if(typeof B != 'undefined'){
                if(B.length){
                  individualPlayerData['B'] = B
                }
              }
              //if(typeof turn != 'undefined') individualPlayerData['turn'] = turn
              if(typeof moves != 'undefined') individualPlayerData['moves'] = moves
              if(typeof lastWinnerWasOp != 'undefined' && lastWinnerWasOp != -1) individualPlayerData['lastWinnerWasOp'] = lastWinnerWasOp
            }else{
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    // straight mapping of incoming data <-> players
                    case 'B':
                      if(typeof el[key] != 'undefined'){
                        if(
                          //(el[key].filter(v=>v!=-1).length > B.filter(v=>v!=-1).length)// &&
                          (el[key].filter(v=>v!=-1).length - B.filter(v=>v!=-1).length) == 1
                        ){
                          moves++
                          B = el[key]
                          console.log('turn', turn)
                          inplay=false
                          B.map((v,i)=>{
                            if(v==-1){
                              inplay=true
                            }
                          })
                          if(!check() || !inplay){
                            doEnding()
                            return
                          }
                        }
                      }
                    break;
                    //case 'turn': if(typeof el[key] != 'undefined')     turn = el[key]; break;
                    case 'moves': if(typeof el[key] != 'undefined' && el[key]>moves) moves = el[key]; break;
                    case 'lastWinnerWasOp': if(typeof el[key] != 'undefined' && el[key] != -1) lastWinnerWasOp = el[key]; break;
                    //case 'inplay': if(typeof el[key] != 'undefined')     inplay = el[key]; break;
                    
                    case 'score':
                      if(typeof el[key] != 'undefined'){
                        AI[key] = +el[key]
                        AI.playerData[key] = +el[key]
                      }
                    break;
                    // reassigned mapping of incoming data <-> players (e.g. toX, vs oX, for lerp)
                    //case 'oX': if(typeof el[key] != 'undefined') AI.toX = el[key]; break;
                    //case 'oY': if(typeof el[key] != 'undefined') AI.toY = el[key]; break;
                    //case 'oZ': if(typeof el[key] != 'undefined') AI.toZ = el[key]; break;
                    //case 'Rl': if(typeof el[key] != 'undefined') AI.tRl = el[key]; break;
                    //case 'Pt': if(typeof el[key] != 'undefined') AI.tPt = el[key]; break;
                    //case 'Yw': if(typeof el[key] != 'undefined') AI.tYw = el[key]; break;
                  }
                })
              }
            }
          })
          for(i=0;i<Players.length;i++) if(Players[i]?.playerData?.id == userID) ofidx = i
        }
      }

      xpic=new Image()
      xpicloaded=false
      xpic.onload=()=>{xpicloaded=true}
      opic=new Image()
      opicloaded=false
      opic.onload=()=>{opicloaded=true}
      recData              = []
      lastWinnerWasOp = -1
      opIsX = true
      xpic.src = './o.png'
      opic.src = './x.png'
      ofidx                = 0
      //collected            = []
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
