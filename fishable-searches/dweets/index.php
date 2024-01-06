<?php
  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }

  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

	$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
  $redirect=true;
  if($slug === ''){
    $redirect = false;
    $src='';
  }else{
    require('db.php');
    $id = alphaToDec($slug);
    $sql = "SELECT * FROM `dweet_links` WHERE id = $id";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $src = str_replace("'","`", $row['code']);
  }
?>
<!DOCTYPE html>
<html>
  <?php if($redirect) { ?>
  
    <head>
      <title>dweet <?php echo $slug?></title>
      <link rel="icon" type="image/x-icon" href="/favicon.png">
      <style>
        body,html{
          background: #000;
          margin: 0;
          overflow: hidden;
          height: 100vh;
          font-family: courier;
        }
        #c{
          width: 100%;
          height: 100%;
          position: absolute;
          left: 50%;
          top: 50%;
          background: #fff;
          transform: translate(-50%, -50%);
        }
        pre{
          color: #fff;
        }
        </style>
      </head>
      <body>
        <canvas id=c></canvas>
        <script>
        c=document.querySelector('#c')
				c.width=1920
				c.height=1080
        x=c.getContext('2d')
        S=Math.sin
        C=Math.cos
				T=Math.tan
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
          },0)
        }
        rsz()

        async function Draw(){
          if(!t){
            R=(Rl,Pt,Yw,m)=>{X=S(p=(A=(M=Math).atan2)(X,Y)+Rl)*(d=(H=M.hypot)(X,Y)),Y=C(p)*d,Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z)),Z=C(p)*d,X=S(p=A(X,Z)+Yw)*(d=H(X,Z)),Z=C(p)*d;if(m)X+=oX,Y+=oY,Z+=oZ}
            Q=()=>[c.width/2+X/Z*900,c.height/2+Y/Z*900]
            for(CB=[],j=6;j--;)for(i=4;i--;)CB=[...CB,[(a=[S(p=Math.PI*2/4*i+Math.PI/4),C(p),2**.5/2])[j%3]*(l=j<3?-1:1),a[(j+1)%3]*l,a[(j+2)%3]*l]]
            let src='function bypass_eval(){<?php echo $src?>}'
            let script = document.createElement('script')
            script.innerHTML = src
            document.body.appendChild(script)
            bypass_eval()
            c.height=Math.round(c.width/1.7777777777777778)
          }
					t_m=t
          bypass_eval()
          t=t_m+1/60
          requestAnimationFrame(Draw)
        }
        Draw()
      </script>
    </body>



  <?php } else { ?>
    <head>
      <title>canvas demo</title>
      <style>
        body, html{
          margin: 0;
          height: 100vh;
          background: linear-gradient(45deg, #000, #123);
          font-family: courier;
          font-size: 18px;
          color: #8ff;
          text-align: center;
          overflow: hidden;
        }
        .main{
          max-width: 800px;
          display: inline-block;
          position: absolute;
          top: 40%;
          left: 50%;
          transform: translate(-50%,-50%);
        }
        #codeInput{
          background: #000;
          border: none;
          outline: none;
          color: #cfc;
          width: 700px;
					height: 16px;
					word-break: break-word;
          text-align: left;
          border-bottom: 1px solid #4fc8;
          font-size: 14px;
          font-family: courier;
        }
        .validStatus{
          position: absolute;
          left: 50%;
          margin-top: -20px;
          transform: translate(-50%);
        }
        #dweetButton{
          border: none;
          background: #333;
          border-radius: 10px;
          font-family: courier;
          color: #888;
          font-size: 24px;
          cursor: pointer;
        }
        .resultLink{
          text-decoration: none;
          color: #fff;
          background: #4f86;
          padding: 10px;
        }
        #resultDiv{
          position: absolute;
          margin-top: 50px;
          left: 50%;
          transform: translate(-50%);
        }
        .copyButton{
					display: none;
          width: 30px;
          height: 30px;
          background-image: url(clippy.c6b23471.svg);
          cursor: pointer;
          z-index: 500;
          background-size: 90% 90%;
          position: absolute;
          left: calc(50% + 220px);
          background-position: center center;
          background-repeat: no-repeat;
          border: none;
          background-color: #8fcc;
          margin: 5px;
					margin-top: 115px;
          border-radius: 5px;
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
        a{
          color: red;
          text-decoration: none;
        }
        #innerCopied{
          position: absolute;
          top: 50%;
          width: 100%;
          z-index: 1020;
          text-align: center;
          transform: translate(0, -50%) scale(2.0, 1);
        }
      </style>
    </head>
    <body>
      <script>
        copy = () => {
          var range = document.createRange()
          range.selectNode(document.querySelectorAll('.resultLink')[0])
          window.getSelection().removeAllRanges()
          window.getSelection().addRange(range)
          document.execCommand("copy")
          window.getSelection().removeAllRanges()
          let el = document.querySelector('#copyConfirmation')
          el.style.display = 'block';
          el.style.opacity = 1.5
          reduceOpacity = () => {
            if(+el.style.opacity > 0){
              el.style.opacity -= .02
              setTimeout(()=>{
                reduceOpacity()
              }, 10)
            }
          }
          reduceOpacity()
          setTimeout(()=>{
            el.style.opacity = 1
            el.style.display = 'none'
          }, 750)
        }
			</script>
      <div id="copyConfirmation"><div id="innerCopied">COPIED!</div></div>
      <div class="main">
        enter ONE LINE of js. check the <a href="https://srmcgann.github.io/shim" target="_blank">shim</a> for shortcuts
        <textarea
          oninput="validate()"
          onkeydown="dweetMaybe(event)"
          autofocus
					spellcheck=false
          id="codeInput"
          type="text"
          placeholder="enter code to dweet..."
        ></textarea><br><br>
        <div class="validStatus" id="validStatus"></div><br>
        <button id="dweetButton" onclick="dweet()" disabled>dweet</button>
        <div id="resultDiv"></div>
         <button onclick="copy()" class="copyButton"></button>
      </div>
      <script>
        codeInput = document.querySelector('#codeInput')
        dweetButton = document.querySelector('#dweetButton')
        resultDiv = document.querySelector('#resultDiv')
        function validURL(str) {
          var regex = /(?:https?):\/\/(\w+:?\w*)?(\S+)(:\d+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
          if(!regex .test(str)) {
            return false;
          } else {
            return true;
          }
        }
        validate=()=>{
          codeInput.value=codeInput.value.trim()
          let val = codeInput.value
          //let valid = false
          //valid = validURL(val)
          let valid = true
          if(val){
            validStatus.style.background = valid ? '#0f84' : '#f004'
            validStatus.style.color = valid ? '#8ff' : '#faa'
            validStatus.innerHTML = valid ? '' : 'URL is <b>NOT</b> valid'
            dweetButton.style.color = valid ? '#4f8' : '#888'
            dweetButton.style.background = valid ? '#8f84' : '#333'
            dweetButton.disabled = false
          } else {
            dweetButton.disabled = true
            validStatus.innerHTML =''
            dweetButton.style.color = valid ? '#4f8' : '#888'
            dweetButton.style.background = valid ? '#8f84' : '#333'
          }
          return valid
        }
        dweetMaybe=e=>{
          if(e.keyCode==13 && validate()){
            dweet()
          }
        }
        dweet=()=>{
          let link = codeInput.value
          sendData = { link }
          fetch('dweet.php',{
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res=>res.text()).then(data=>{
            if(data){
              let link = document.createElement('a')
              link.className = 'resultLink'
              link.code = '_blank'
							document.querySelectorAll('.copyButton')[0].style.display='inline-block'
              link.innerHTML = link.href = window.location.origin + '/dweets/' + data
              resultDiv.innerHTML = 'your link<br><br>'
              resultDiv.appendChild(link)
              codeInput.value = ''
              validStatus.innerHTML =''
              dweetButton.style.color = '#888'
              dweetButton.style.background = '#333'
            } else {
              alert('crap.')
            }
          })
        }
      </script>
    </body>
  <?php } ?>
</html>
