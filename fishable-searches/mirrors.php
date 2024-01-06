  <!DOCTYPE html>
  <html>
    <head>
      <title>ORBS mirrors [updated: 11/27/23]</title>
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
        body, html{
          margin: 0;
          background: #000;
          color: #fff;
          font-family: Courier Prime;
        }
        #title{
          padding: 25px;
          background: linear-gradient(90deg, #40f, #000, #0f8);
          color: #fff;
          font-size: 2em;
        }
        .link{
          padding: 1px;
          border: none;
          display: inline-block;
          min-width: 400px;
          font-size: 1.25em;
          background: #40f8;
          color: #fff;
          text-decoration: none;
          margin: 20px;
          margin-bottom: 0;
          border: 10px solid #4f82;
        }
        #main{
          text-align: center;
          padding: 25px;
        }
        .caption{
          color: #888;
        }
        .msgText{
          display: inline-block;
          width: 400px;
          text-align: left;
          padding: 25px;
        }
        .statusDiv{
          background-size: 25px 25px;
          background-position: 5px center;
          background-repeat: no-repeat;
          display: inline-block;
          width: 375px;
          height: 30px;
          margin-bottom: 10px;
          padding-top: 9px;
        }
      </style>
    </head>
    <body>
      <div id="title">ORBS MIRRORS</div>
      <div id="main">
        <div class="msgText">
          some of these might stop working due<br>
          to database challenges...<br><br>
          just try another link
        </div>
      </div>
      <script>
        links = [
          [
            'https://orbs.work.gd',
            'https://fishable-searches.000webhostapp.com/',
            'id21284549_user',
            'id21284549_videodemos2'
          ],
          [
            'https://orbs1.work.gd',
            'https://orbs2.000webhostapp.com/',
            'id21552617_user',
            'id21552617_orbs2'
          ],
          [
            'https://orbs2.work.gd',
            'https://orbs3.000webhostapp.com/',
            'id21553412_user',
            'id21553412_orbs3'
          ],
          [
            'https://orbs3.work.gd',
            'https://orbs4.000webhostapp.com/',
            'id21583283_user',
            'id21583283_orbs4'
          ]
        ]
        completed = Array(links.length).fill(false)
        els = Array(links.length).fill(v=>{return {el: '', status: false}})
        function genStatus (v, i) {
          fetch(`status.php?url=${v[0]}&db=${v[3]}&user=${v[2]}`).then(res=>res.text()).then(data=>{
            data = data ? JSON.parse(data) : [false]
            let el = document.createElement('a')
            el.className = 'link'
            el.target = '_blank'
            domain = v[0].split('://')[1]
            el.innerHTML = `mirror ${i+1} <span class="caption">[${domain}]</span>`
            el.href = v[0]
            els[i] = [el, data[0]]
            completed[i] = true
            if(completed.filter(v=>v).length == completed.length){
              els.sort((a,b)=>b[1]-a[1])
              els.map(el=>{
                let statusEl = document.createElement('div')
                statusEl.className = 'statusDiv'
                statusEl.style.backgroundImage = el[1] ? 'url(check.png)' : 'url(x.png)'
                statusEl.style.backgroundColor = el[1] ? '#084' : '#400'
                statusEl.style.color = el[1] ? '#0f8' : '#f00'
                statusEl.innerHTML = el[1] ? 'connected / online' : 'ruh roh / problems'
                br = document.createElement('br')
                el[0].appendChild(br)
                el[0].appendChild(statusEl)
                br = document.createElement('br')
                main.appendChild(el[0])
                main.appendChild(br)
              })
            }
          })
        }
        main = document.querySelector('#main')
        //main.innerHTML = ''
        links.map((v, i) => {
          genStatus(v,i)
        })
        setTimeout(()=>{
          location.reload()
        }, 10000)
      </script>
    </body>
  </html>