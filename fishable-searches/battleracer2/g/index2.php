<!--
  to-do:
    ✔ AI players
    ✔ fix heat seeking
    ✔ levels
    ✔ player health
    ✔ HUD
    ✔ AI Cars
      └► ✔ dist-sorted goal seeking or attacking
      ✔ goal prioritization (e.g. health when needed)
    ✔ power ups [speed-boost, car-gun, missiles (limited)]
    ✔ selectable camera/car
    ✔ score/stats summary, hidable
    ✔ fix drift indicator overlay
    ✔ custom dial fonts
    ✔ working instrument guages @ dashboard
    ✔ 'pain' overlay
    ✔ missile alarm
    ✔ sound effects
    ✔ mute button
    ✔ hotkeys cheatsheet
    ✔ arena selector / lobby
    ✔ different floors for each level
    ✔ logical levels / texture motifs, transition points
    ✔ performance optimization
    * online
-->
<!DOCTYPE html>
<html>
  <head>
    <title>Coordinates boilerplate example</title>
    <style>
      @font-face {
        font-family: 'Technology';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://srmcgann.github.io/objs/car/Technology-Bold.woff2) format('woff2');
      }
      html, body{
        background-repeat: no-repeat;
        background-position: center center;
        background-image: background-size: 100% 100%;
        background-color: #000;
        margin: 0;
        min-height: 100vh;
        overflow: hidden;
      }
      .loadingText{
        position: absolute;
        color: #888;
        font-family: Technology;
        font-size: 200px;
        width: 600px;
        display: block;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
      }
      .overlay{
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background: #111e;
        z-index: 100;
        display: none;
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
    <div class="loadingText" id="loadingTextDiv">loading....</div>
    <script type="module">
      window.addEventListener('load', () => { window.loaded = true } ) 
      window.loaded = false
      var loop = () => {
        var el = document.querySelector('#loadingTextDiv')
        el.innerHTML = 'loading' + ('.').repeat(((new Date()).getTime()/100*6)%8)
        if(!window.loaded) {
          requestAnimationFrame(loop)
        } else {
          document.querySelector('#loadingTextDiv').remove()
        }
      }
      loop()

      import * as Coordinates from
      "https://srmcgann.github.io/Coordinates/coordinates.min.js"
      //"./coordinates.js"
      
      var floorMode = 0
      if(location.href.split('fm=').length > 1){
        floorMode = +location.href.split('fm=')[1].split('&')[0]
      }

      var skySel = 0
      if(location.href.split('sky=').length > 1){
        skySel = +location.href.split('sky=')[1].split('&')[0]
      }

      var floorSel = 0
      if(location.href.split('fl=').length > 1){
        floorSel = +location.href.split('fl=')[1].split('&')[0]
      }
      
      var fogEnabled = false
      if(location.href.split('fe=').length > 1){
        fogEnabled = !!(+location.href.split('fe=')[1].split('&')[0])
      }
      
      
      var rendererOptions = {
        ambientLight: .5, margin: 0,
        fov: 1e3,
        width: 1920,
        height: 1080,
        //fov: 1e3/2,
        //width: 1920/2,
        //height: 1080/2,
      }
      var renderer = await Coordinates.Renderer(rendererOptions)
      
      var rendererOptions = {
        width: renderer.width/7,
        height: renderer.width/7,
        margin: 0, attachToBody: false,
        context: {
          mode: '2d',
          options: {
            willReadFrequently: true,
          }
        },
      }
      var scratchCanvas = await Coordinates.Renderer(rendererOptions)
      var sc = scratchCanvas.c
      var sctx = scratchCanvas.ctx
      
      
      renderer.c.tabIndex = 0
      renderer.c.focus()
      
      var ctx = Coordinates.Overlay.ctx
      var c = Coordinates.Overlay.c
      var w = c.width
      var h = c.height
      

      var refTextures = [
        'https://srmcgann.github.io/Coordinates/resources/landscape_clouds_sun_no-floor_pseudoequirectangular_po2.png',
       'https://srmcgann.github.io/Coordinates/resources/pinkStars_purpleLowerHalf_halfsky_equirectangular.png',
       'https://srmcgann.github.io/Coordinates/resources/equisky_ultra.png',
       'https://srmcgann.github.io/Coordinates/resources/callibration equirectangular image.png',
       'https://srmcgann.github.io/skyboxes2/HDRI/1.jpg',
       'https://srmcgann.github.io/skyboxes3/HDRI/alices.jpg',
       'https://srmcgann.github.io/skyboxes3/HDRI/maze_light.png',
      ]
      var floorTextures = ['https://srmcgann.github.io/Coordinates/resources/grass_texture_lowres.jpg',
         'https://srmcgann.github.io/Coordinates/resources/bumpmap_equirectangular_po2_colors_teal_magenta.jpg',
         'https://srmcgann.github.io/objs/car/refraction_floor_grid.png',
         'https://srmcgann.github.io/Coordinates/resources/monochrome_equirect_lite.jpg', 'https://srmcgann.github.io/Coordinates/resources/bumpmap_equirectangular_po2_colors_violet_puce.jpg',
         'https://srmcgann.github.io/Coordinates/resources/bumpmap_equirectangular_po2_colors_violet_puce.jpg',
      ]
      var rebindTextures = false
      var cycleTexturesFreq = 240

      var fogColor, floorUVX, floorUVY
      var reflectionValue = .2
      var refractionValue = .4
      var ambientLightValue = .4
      var phongValue = .4
      var fogValue = 2.75


      var missileLaunchSound = new Audio()
      missileLaunchSound.src = 'https://srmcgann.github.io/objs/car/missile.wav'
      missileLaunchSound.volume = .66
      
      var goalSound = new Audio()
      goalSound.src = 'https://srmcgann.github.io/objs/car/goal.wav'
      goalSound.volume = .5
      
      var engineIdleSound = new Audio()
      engineIdleSound.src = 'https://srmcgann.github.io/objs/car/engineIdle.mp3'
      engineIdleSound.loop = true
      engineIdleSound.volume = .5
      
      var alarmSound = new Audio()
      alarmSound.src = 'https://srmcgann.github.io/objs/car/alarm.mp3'
      alarmSound.loop = true
      alarmSound.volume = .15
      
      var engineAccelSound = new Audio()
      engineAccelSound.src = 'https://srmcgann.github.io/objs/car/engineAccel.mp3'
      engineAccelSound.loop = true
      engineAccelSound.volume = .2

      var tiresScreechingSound = new Audio()
      tiresScreechingSound.src = 'https://srmcgann.github.io/objs/car/screeching tires.mp3'
      tiresScreechingSound.loop = true
      tiresScreechingSound.volume = .5
      
      var splode1Sound = new Audio()
      splode1Sound.src = 'https://srmcgann.github.io/objs/car/splode.mp3'
      splode1Sound.loop = false
      splode1Sound.volume = 1
      
      var splode2Sound = new Audio()
      splode2Sound.src = 'https://srmcgann.github.io/objs/car/splode2.mp3'
      splode2Sound.loop = false
      splode2Sound.volume = 1
      
      var metalSounds = Array(5).fill().map((v, i) => {
        var ret = new Audio()
        ret.volume = .5
        ret.src = `https://srmcgann.github.io/objs/car/metal${i+1}.ogg`
        return ret
      })
      
/*
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
*/              

      var driftOverlayImage = new Image()
      fetch('https://srmcgann.github.io/objs/car/drift_overlay.png').then(res=>res.blob()).then(data => {
        driftOverlayImage.src = URL.createObjectURL(data)
      })

      var steeringWheelImage = new Image()
      fetch('https://srmcgann.github.io/objs/car/steeringWheel.png').then(res=>res.blob()).then(data => {
        steeringWheelImage.src = URL.createObjectURL(data)
      })

      var H_keytipImage = new Image()
      fetch('https://srmcgann.github.io/objs/car/H_keytip.png?2').then(res=>res.blob()).then(data => {
        H_keytipImage.src = URL.createObjectURL(data)
      })

      var carHUD = new Image()
      fetch('https://srmcgann.github.io/objs/car/carHUD.png').then(res=>res.blob()).then(data => {
        carHUD.src = URL.createObjectURL(data)
      })

      var pain = new Image()
      fetch('https://srmcgann.github.io/objs/car/pain.png').then(res=>res.blob()).then(data => {
        pain.src = URL.createObjectURL(data)
      })

      var guageNeedle = new Image()
      fetch('https://srmcgann.github.io/objs/car/guageNeedle.png').then(res=>res.blob()).then(data => {
        guageNeedle.src = URL.createObjectURL(data)
      })

      var statsContainer = new Image()
      fetch('https://srmcgann.github.io/objs/car/stats.png').then(res=>res.blob()).then(data => {
        statsContainer.src = URL.createObjectURL(data)
      })

      var markerImage = new Image()
      fetch('https://srmcgann.github.io/objs/car/marker.png').then(res=>res.blob()).then(data => {
        markerImage.src = URL.createObjectURL(data)
      })

      var powerupImage = new Image()
      fetch('https://srmcgann.github.io/objs/car/powerup.png').then(res=>res.blob()).then(data => {
        powerupImage.src = URL.createObjectURL(data)
      })

      const SetLevelTextures = (skySel, floorSel) => {
        rebindTextures = true
        switch(floorSel){
          case 0:
            floorUVX = 2
            floorUVY = 2
          break
          case 1:
            floorUVX = 2
            floorUVY = 2
          break
          case 2:
            floorUVX = 1/4
            floorUVY = 1/4
          break
          case 3:
            floorUVX = 1/2
            floorUVY = 1/2
          break
          case 4:
            floorUVX = 2
            floorUVY = 2
          break
          /*case 5:
            floorUVX = 1
            floorUVY = 1
          break
          */
          default:
            floorUVX = 1
            floorUVY = 1
          break
        }
        switch(skySel){
          case 0:
            fogColor = 0xa0f0ff
            fogValue = 2.75
            fogEnabled = true
            reflectionValue = 0
            refractionValue = 0
            ambientLightValue = 1
            phongValue = .4
          break
          case 1:
            fogColor = 0x18002a
            fogValue = 2.75 / 1.2
            fogEnabled = true
            reflectionValue = .1
            refractionValue = .8
            ambientLightValue = 1
            phongValue = .25
          break
          case 2:
            fogColor = 0x000000
            fogValue = 0
            reflectionValue = .1
            refractionValue = .8
            ambientLightValue = .55
            phongValue = .2
          break
          case 3:
            fogColor = 0x000000
            fogValue = 0
            fogEnabled = true
            reflectionValue = .25
            refractionValue = 1
            ambientLightValue = .5
            phongValue = .2
          break
          case 4:
            fogColor = 0xaaeeff
            fogValue = 0
            reflectionValue = .2
            refractionValue = .71
            ambientLightValue = .3
            phongValue = .2
          break
          /*
          case 5:
            //curTexture = level
            floorUVX = 1/8
            floorUVY = 1/8
            fogColor = 0xaaeeff
            fogValue = 0
            reflectionValue = .2
            refractionValue = .72
            ambientLightValue = .3
            phongValue = .2
          break
          case 6:
            curTexture = level
            floorUVX = 1/8
            floorUVY = 1/8
            fogColor = 0xaaeeff
            fogValue = 0
            reflectionValue = .2
            refractionValue = .4
            ambientLightValue = .3
            phongValue = .1
          break
          */
        }
      }
      
      setTimeout(()=>{
        SetLevelTextures(skySel, floorSel)
      }, 0)

      var S = Math.sin
      var C = Math.cos
      var Rn = Math.random

      var x, y, z, p, q, d, fs, str

      Coordinates.AnimationLoop(renderer, 'Draw')

      var shaderOptions = [
        {lighting: {type: 'ambientLight', value: .35}},
        { uniform: {
          type: 'phong',
          value: .4
        } },
        { uniform: {
          type: 'reflection',
          map: refTextures[skySel],
          enabled: true,
          flipReflections: false,
          value: reflectionValue
        } },
        { uniform: {
          type: 'fog',
          enabled: fogEnabled,
          color: fogColor,
          value: 3.4
        } }
      ]
      var shader = await Coordinates.BasicShader(renderer, shaderOptions)

      var shaderOptions = [
        {lighting: {type: 'ambientLight', value: .6}},
        { uniform: {
          type: 'phong',
          value: .5
        } },
        { uniform: {
          type: 'reflection',
          map: refTextures[skySel],
          enabled: true,
          flipReflections: false,
          value: .05
        } },
      ]
      var powerupShader = await Coordinates.BasicShader(renderer, shaderOptions)

      var shaderOptions = [
        {lighting: {type: 'ambientLight', value: .25}},
        { uniform: {
          type: 'phong',
          value: 0
        } },
      ]
      var exhaustShader = await Coordinates.BasicShader(renderer, shaderOptions)

      var shaderOptions = [
        {lighting: {type: 'ambientLight', value: .2}},
        { uniform: {
          type: 'phong',
          value: .2
        } },
        { uniform: {
          type: 'reflection',
          map: refTextures[skySel],
          enabled: true,
          flipReflections: false,
          value: 0,
        } },
        { uniform: {
          type: 'refraction2',
          map: refTextures[skySel],
          enabled: true,
          angleOfRefraction: .05,
          value: .75,
        } },
        { uniform: {
          type: 'fog',
          enabled: fogEnabled,
          color: fogColor,
          value: 3.4
        } }
      ]
      var floorShader = await Coordinates.BasicShader(renderer, shaderOptions)
      

      var colors = []
      var iParticlesc = 600 + 3400 + 1400
      Array(iParticlesc).fill().map((v, i) => {
        var rgb   = Coordinates.HSVToRGB(360/600*i, 1, 1)
        var red   = 1 // rgb[0] / 256
        var green = .75 // rgb[1] / 256
        var blue  = .25 // rgb[2] / 256
        colors.push(red, green, blue, 1)
      })

      var shaderOptions = [
        { attribute: {
          type: 'custom',
          enabled: true,         // can be disabled (removed from shader)
          value: colors,         // buffer data
          name: 'customColors',  // attribute name to inject into shader
          stride: 4,             // in this case, we're passing RGBA floats
          dataType: renderer.ctx.FLOAT,
          vertDeclaration: `     // vertex shader code, pre main
                              attribute vec4 customColors;
                              varying vec4 attribCols;
                            `,
          vertCode:        `     // vertex shader code, in main function
                              attribCols = customColors;
                            `,
          fragDeclaration: `     // fragment shader code, pre main
                              varying vec4 attribCols;
                            `,
          fragCode: `            // fragment shader code, in main function
                              mixColor = attribCols;
                            `,
        }},
      ]
      var particleShader = await Coordinates.BasicShader(renderer, shaderOptions)

      var shaderOptions = [
        {lighting: {type: 'ambientLight', value: 1.33}},
        { uniform: {
          type: 'phong',
          value: 0
        } },
        { uniform: {
          type: 'fog',
          enabled: false,
          color: fogColor,
          value: 2.75
        } }
      ]
      var backgroundShader = await Coordinates.BasicShader(renderer, shaderOptions)

      var keyInterval = .2

      window.onkeydown = e => {

        console.log(e.keyCode)

        if(e.keyCode == 37 ||
           e.keyCode == 38 ||
           e.keyCode == 39 ||
           e.keyCode == 40){
          e.preventDefault()
          e.stopPropagation()
        }
        switch(e.keyCode){
          case 48: // 0
            cameraCar = 0
          break
          case 49: // 1
          case 50: // 2
          case 51: // 3
          case 52: // 4
          case 53: // 5
          case 54: // 6
          case 55: // 7
          case 56: // 8
          case 57: // 9
            if(cars.length > e.keyCode - 48) cameraCar = e.keyCode - 48
          break
        }
        if(e.keyCode == 84){
          showStats = !showStats
        }
        
        if(cameraCar == 0) cars[0].keys[e.keyCode] = true
      }

      window.onkeyup = e => {
        cars[0].keys[e.keyCode] = false
        cars[0].keyTimers[e.keyCode] = 0
      }

      const DoKeys = car => {
        car.keys.map((state, key) =>{
          if(state && car.keyTimers[key] < renderer.t){
            switch(key){
              case 32:
                if(car.hasCannon && car.missiles > 0){
                  car.missiles -- 
                  car.keyTimers[key] = renderer.t + keyInterval
                  SpawnRPG(car)

                  var tx = 0
                  var ty = 1
                  var tz = 1.1
                  p = Math.atan2(ty, tz) + car.pitch
                  d = Math.hypot(ty, tz)
                  ty = S(p) * d
                  tz = C(p) * d
                  p = Math.atan2(tx, ty) + car.roll
                  d = Math.hypot(tx, ty)
                  tx = S(p) * d
                  ty = C(p) * d
                  p = Math.atan2(tx, tz) + car.yaw
                  d = Math.hypot(tx, tz)
                  tx = S(p) * d
                  tz = C(p) * d

                  SpawnFlash(car.x + tx, car.y + ty + 1, car.z + tz, 1)
                }
                break
              case 71:
                car.keyTimers[key] = renderer.t + keyInterval
                //car.hasCannon = !car.hasCannon
              break
              case 72:
                car.keyTimers[key] = renderer.t + keyInterval
                showHUD = !showHUD
              break
              case 69:
                car.keyTimers[key] = renderer.t + keyInterval
                //car.hasOverdrive = !car.hasOverdrive
                break
              case 37:
              case 219: // left arrow
                car.steer = -car.steerVel
              break
              case 38: // up arrow
                car.accel += car.accelVel
                if(car.hasTraction){
                  x = S(p=car.yaw + Math.PI/2) * .5 * (renderer.frameCount/2%2?1:-1)
                  z = C(p) * .5 * (renderer.frameCount/2%2?1:-1)
                  x += S(car.yaw) * -1.6
                  z += C(car.yaw) * -1.6
                  if(Rn() < 1) SpawnSmoke(car.x + x, car.y-.5, car.z + z)
                }
              break
              case 39:
              case 221: // right arrow
                car.steer = car.steerVel
              break
              case 40: // down arrow
                car.accel -= car.accelVel
                if(car.hasTraction){
                  x = S(car.yaw) * -2
                  z = C(car.yaw) * -2
                  if(Rn() < 1) SpawnSmoke(car.x + x, car.y, car.z + z)
                }
              break
              case 76: // l
                if(!promptVisible){
                  car.keyTimers[key] = renderer.t + keyInterval
                  ReturnToLobby()
                }
              break
              case 77: // m
                car.keyTimers[key] = renderer.t + keyInterval
                ToggleMute(true)
              break
              case 79: // o
                car.keyTimers[key] = renderer.t + keyInterval
                if(car.cameraMode == 'fps'){
                  car.cameraMode = 'orbit'
                  renderer.fov = 1e3
                }else{
                  car.cameraMode = 'fps'
                  renderer.fov = 1001
                }
              break
            }
          }
        })
      }

      var shapes = []

      var j
      var wmag = 1/8
      var ox = 0
      var oy = 0
      var oz = 0
      var cl = 16
      var rw = 1
      var br = 16
      var sp = 4
      var cameraCar = 0
      var showDebug = false
      var showStats = true
      var showHUD = true
      var promptVisible = false
      var defaultFollowDistance = 10
      var iSmokev = .05
      var iSparksv = .25
      var iRPGv = 5
      var hillMag = 18
      var driftTimer = 20
      var muted = false
      var smoke = [], sparks = [], RPGs = []
      var flashes = [], powerups = []
      var iPowerupc = 3
      var powerupLife = 100
      var stationaryHillPoints = false
      var trackingYaw = 0
      var hillMovement = .5
      var missileDamage = 15
      var grav = .22
      var homing = .2
      var ls = 2**.5 * sp / 2
      
      var buttons = [
        {
          x: c.width - 200,
          y: 5,
          name: 'lobby button',
          actCap: '[L] lobby',
          inactCap: '[L] lobby',
          activeStrokeStyle: '#0ff8',
          activeFillStyle: '#0448',
          inactiveStrokeStyle: '#0ff8',
          inactiveFillStyle: '#0448',
          activeTextColor: '#8cf',
          inactiveTextColor: '#8cf',
          width: 100,
          height: 32,
          fontSize: 24,
          activeState: true,
          enabled: true,
          toolTip: 'mute / unmute',
          hover: false,
          clickScript: 'ReturnToLobby()',
        },
        {
          x: c.width - 85,
          y: 5,
          name: 'mute button',
          actCap: '[M] ' + (['\uD83D', '\uDD0A']).join(''),
          inactCap: '[M] ' + (['\uD83D', '\uDD07']).join(''),
          activeStrokeStyle: '#0f48',
          activeFillStyle: '#0628',
          inactiveStrokeStyle: '#c008',
          inactiveFillStyle: '#6008',
          activeTextColor: '#8fc',
          inactiveTextColor: '#f88',
          width: 80,
          height: 32,
          fontSize: 24,
          activeState: true,
          enabled: true,
          toolTip: 'mute / unmute',
          hover: false,
          clickScript: 'ToggleMute()',
        }
      ]
      

      var geometryData = Array(iParticlesc).fill().map((v, i) => {
        x = ((((i+10)**3.1)%1)-.5) * cl * sp * 3
        y = Rn() * 150 - 20
        z = ((((i+1e3)**2.6)%1)-.5) * br * sp * 3
        return [x, y, z]
      })

      var baseFlash, flashShape
      var geoOptions = {
        shapeType: 'sprite',
        name: 'flash',
        disableDepthTest: true,
        map: 'https://srmcgann.github.io/Coordinates/resources/stars/star1.png',
        size: .2,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        baseFlash = structuredClone(geometry.vertices)
        flashShape = geometry
      })

      var geoOptions = {
        shapeType: 'particles',
        geometryData,
        name: 'particles',
        size: 3,
        disableDepthTest: true,
        color: 0xffffff,
        alpha: 1,
        penumbra: .3,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        shapes.push(geometry)
        await particleShader.ConnectGeometry(geometry)
      })  

      var iPc = 0
      var geometryData = Array(iPc).fill().map((v, i) => {
        x = ((((i+4)**3.1)%1)-.5) * cl * sp * 3
        y = (((i+101)**3.2)%1) * 10
        z = ((((i+50)**2.6)%1)-.5) * br * sp * 3
        return [x, y, z]
      })

      var hillPointsShape
      var geoOptions = {
        shapeType: 'particles',
        geometryData,
        name: 'hill points',
        size: 2,
        color: 0xff0000,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        hillPointsShape = geometry
        shapes.push(geometry)
      })  

      var wheelSensorScaleX = 1.9 * 1.5 * 1.1
      var wheelSensorScaleZ = 1.16 * 1.75
      var wheelSensorShape
      var geometryData = ([
        //[4 * wheelSensorScaleX, 0, 8 * wheelSensorScaleZ + .7],
        //[-4 * wheelSensorScaleX, 0, 8 * wheelSensorScaleZ + .7],
        //[-4 * wheelSensorScaleX, 0, -8 * wheelSensorScaleZ + .7],
        //[4 * wheelSensorScaleX, 0, -8 * wheelSensorScaleZ + .7],
        [4 * wheelSensorScaleX, 0, .7],
        [-4 * wheelSensorScaleX, 0, .7],
        [0, 0, (8 * wheelSensorScaleZ + .7)],
        [0, 0, (-8 * wheelSensorScaleZ + .7)],
      ]).map(v => v.map(q=>q*=wmag))

      var geoOptions = {
        shapeType: 'particles',
        geometryData,
        name: 'wheel sensors',
        alpha: .75,
        penumbra: .25,
        size: 2,
        color: 0x00ff44,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        wheelSensorShape = structuredClone(geometry.vertices)
        shapes.push(geometry)
      })  

      var geoOptions = {
        shapeType: 'dodecahedron',
        name: 'background',
        sphereize: 1,
        subs: 4,
        map: refTextures[skySel],
        size: 1e3,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        shapes.push(geometry)
        await backgroundShader.ConnectGeometry(geometry)
      })  

      var carPrototype = {
        x: 0,
        y: 0,
        z: 0,
        vx: 0,
        vy: 0,
        vz: 0,
        id: '',
        ovx: 0,
        ovy: 0,
        ovz: 0,
        roll: 0,
        pitch: 0,
        name: '',
        yaw: 0,
        rollv: 0,
        pitchv: 0,
        yawv: 0,
        accel: 0,
        score: 0,
        drift: 0,
        steer: 0,
        steerv: 0,
        respawns: 0,
        idling: -1,
        idlingv: -2,
        steerVel: 1.5,
        health: 100,
        painAlpha: 0,
        accelVel: .02,
        wheelTurn: 0,
        cameraMode: 'fps', // fps | orbit
        speed: .1,
        distance: 0,
        missiles: 0,
        hasCannon: false,
        bounced: false,
        actualSpeed: 0,
        followDistance: 0,
        battleMode: false,
        followTarget: false,
        hasTraction: false,
        hasOverdrive: false,
        brakeAtDestination: false,
        keys: Array(256).fill(false),
        keyTimers: Array(256).fill(0),
      }
      
      renderer.fov = carPrototype.cameraMode == 'fps' ? 1001 : 1e3

      var iCarsc = 1
      var cars = Array(iCarsc).fill().map((v, i) => {
        var ret = structuredClone(carPrototype)
        ret.x = (-iCarsc/2 + i + .5) *10
        ret.id = i
        ret.name = i ? `AI PLAYER ${i}` : 'human'
        if(0) if(i) ret.followTarget = cars[i-1]
        return ret
      })
      
      const Respawn = car => {
        var ret = structuredClone(carPrototype)
        ret.x = (-iCarsc/2 + car.id + .5) *10
        ret.keys = Array(256).fill(false)
        ret.keyTimers = Array(256).fill(0)
        Object.keys(car).forEach(key => {
          switch(key){
            /*case x:
              car[key] = (Rn() -.5) * cl * sp
            break
            case z:
              car[key] = (Rn() -.5) * br * sp
            break*/
            case 'name':
            case 'cameraMode':
            case 'score':
            case 'id':
            break
            case 'respawns':
              car[key]++
            break
            default:
              car[key] = ret[key]
            break
          }
        })
      }

      var carShape, gunCarShape
      var engineCarShape, gunEngineCarShape

      var geoOptions = {
        shapeType: 'obj',
        name: 'car',
        url: 'https://srmcgann.github.io/objs/car/car_engine_block_no_wheels_roofgun.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        cullface: '',
        //flipNormals: true,
        rotationMode: 2,
        showNormals: false,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        gunEngineCarShape = geometry
        await shader.ConnectGeometry(geometry)
      })

      var geoOptions = {
        shapeType: 'obj',
        name: 'car',
        url: 'https://srmcgann.github.io/objs/car/car_no_wheels.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        //flipNormals: true,
        rotationMode: 2,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        carShape = geometry
        await shader.ConnectGeometry(geometry)
      })  

      var geoOptions = {
        shapeType: 'obj',
        name: 'car',
        url: 'https://srmcgann.github.io/objs/car/car_engine_block_no_wheels.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        cullface: '',
        //flipNormals: true,
        rotationMode: 2,
        showNormals: false,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        engineCarShape = geometry
        await shader.ConnectGeometry(geometry)
      })

      var geoOptions = {
        shapeType: 'obj',
        name: 'car',
        url: 'https://srmcgann.github.io/objs/car/car_no_wheels_roofgun.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        //flipNormals: true,
        rotationMode: 2,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        gunCarShape = geometry
        await shader.ConnectGeometry(geometry)
      })  

      var ww = 7
      var wl = 9.22
      var wz = .67 * wmag
      var wheelPoints = [
        [-ww*wmag, 0, wl*wmag],
        [ww*wmag,  0, wl*wmag],
        [ww*wmag,  0, -wl*wmag],
        [-ww*wmag, 0, -wl*wmag],
      ]

      var wheelBaseShape
      var geoOptions = {
        shapeType: 'obj',
        name: 'wheels',
        url: 'https://srmcgann.github.io/objs/car/wheel.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        objY: -2.8/8,
        rotationMode: 2,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry, wheelPoints).then(async res => {
          res.shapeData.map((subShape, sIdx) => {
            if(sIdx == 0 || sIdx == 3){
              for(var i = 0; i < res.stride; i += 3){
                res.nvstate[i+ sIdx * res.stride + 0] *= -1
                res.nvstate[i+ sIdx * res.stride + 2] *= -1

                res.normalVecs[i+ sIdx * res.stride + 0] *= -1
                res.normalVecs[i+ sIdx * res.stride + 2] *= -1
              }
            }
          })
          wheelBaseShape = {
            vstate: structuredClone(res.vstate),
            nvstate: structuredClone(res.nvstate),
            vertices: structuredClone(res.vertices),
            normalVecs: structuredClone(res.normalVecs),
          }
          res.shapeData[0].yaw = Math.PI
          res.shapeData[3].yaw = Math.PI
          for(var i = 0; i < 4; i++) res.shapeData[i].z = wz
          Coordinates.ApplyShapeData(res)
          shapes.push(res)
          await shader.ConnectGeometry(res)
        })  
      })  

      /*
      var geometryData = {
        vertices: [],
        uvs: [],
        normals: [],
        normalVecs: [],
      }

      Array(cl*rw*br).fill().map((v, i) => {

        var tx = ((i%cl)-cl/2+.5) * sp
        var ty = (((i/cl|0)%rw)-rw/2+.5) * sp
        var tz = ((i/cl/rw|0)-br/2+.5) * sp

        j=0
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

        j=1
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

        j=2
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

        j=2
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

        j=3
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

        j=0
        x = ox + tx + S(p=Math.PI*2/4*j+Math.PI/4) * ls
        y = oy + ty
        z = oz + tz + C(p) * ls
        geometryData.vertices.push(x,y,z)
        geometryData.uvs.push(x/cl/sp, z/br/sp + .5)
        geometryData.normals.push(0,0,0,0,0,0)
        geometryData.normalVecs.push(0,0,0)

      })
      */

      var pointArray = Array(9).fill().map((v, i) => {
        x = ((i%3) - 3/2 + .5) * sp * cl
        y = -10
        z = ((i/3|0) - 3/2 + .5) * sp * br
        return [x, y, z]
      })

      /*
      var geoOptions = {
        shapeType: 'custom shape',
        //url: 'https://srmcgann.github.io/Coordinates/custom shapes/floor_grid_16x16_assocs_uvs.json',
        geometryData,
        name: 'floor',
        scaleUVX: 1,
        scaleUVY: 1,
        map: 'https://srmcgann.github.io/Coordinates/resources/grass_texture_lowres.jpg',
        averageNormals: true,
        syncNormals: true,
        //color: 0xffffff,
        //colorMix: .5,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        geometry.downloadShape = true
        Coordinates.ShapeFromArray(geometry, pointArray).then(async res => {

          shapes.push(res)
          await floorShader.ConnectGeometry(res)
          
        })
      })
      */


      /*
      var jumpHeight = -1.5
      var player = {
        vx: 0, vy: 0, vz: 0,
        ovx: 0, ovy: 0, ovz: 0,
      }
      const Jump = () => {
        if(renderer.hasTraction){
          player.vy += jumpHeight
        }
      }

      window.onmousedown = e => {
        if(e.button == 2) Jump()
      }
      */

      var geoOptions = {
        shapeType: 'custom shape',
        url: 'https://srmcgann.github.io/Coordinates/custom shapes/floor_grid_16x16_assocs_uvs.json',
        name: 'floor',
        showNormals: false,
        scaleUVX: floorUVX,
        scaleUVY: floorUVY,
        map: floorTextures[floorSel],
        averageNormals: true,
        syncNormals: true,
        color: 0xff0000,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        /*
        for(var i = 0; i < geometry.vertices.length; i += 3){
          geometry.vertices[i+0] *= 2
          geometry.vertices[i+1] = 0
          geometry.vertices[i+2] *= 2
          geometry.vstate[i+0] *= 2
          geometry.vstate[i+1] = 0
          geometry.vstate[i+2] *= 2
        }
        geometry.shapeData.map(subShape => {
          subShape.ox *= 2
          subShape.oy *= 2
          subShape.oz *= 2
          subShape.wx *= 2
          subShape.wy *= 2
          subShape.wz *= 2
        })
        */
        shapes.push(geometry)
        await floorShader.ConnectGeometry(geometry)
      })

      var flairShape
      var geoOptions = {
        shapeType: 'obj',
        name: 'exhaust flair',
        url: 'https://srmcgann.github.io/objs/car/exhaust_flair.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        objz: -(1-1)*1.55,
        alpha: .9,
        disableDepthTest: true,
        rotationMode: 2,
        showNormals: false,
        y: 1,
        colorMix: 0,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        flairShape = structuredClone(geometry.vertices)
        shapes.push(geometry)
        await exhaustShader.ConnectGeometry(geometry)
      })  

      var RPGShape
      
      var powerupTypes = [
        { name: 'health', shape: ''},
        { name: 'missiles', shape: '' },
        { name: 'cannon', shape: '' },
        { name: 'overdrive', shape: ''},
        { name: 'drift', shape: ''},
      ]

      var geoOptions = {
        shapeType: 'obj',
        url: 'https://srmcgann.github.io/objs/missile_lowpoly/missile_lowpoly.obj',
        map: 'https://srmcgann.github.io/objs/missile_lowpoly/missile.png',
        name: 'RPG',
        colorMix: 0,
        rotationMode: 2,
        objPitch: -Math.PI/2,
        //objY: 2.25,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(50).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          RPGShape = res
          await shader.ConnectGeometry(res)
        })
      })  

      var geoOptions = {
        shapeType: 'obj',
        //url: 'https://srmcgann.github.io/objs/powerupMissile.obj',
        url: 'https://srmcgann.github.io/objs/missilePack_lowpoly.obj',
        map: 'https://srmcgann.github.io/objs/missilePack.png',
        name: 'RPG',
        colorMix: 0,
        scaleX: 4,
        scaleY: 4,
        scaleZ: 4,
        rotationMode: 2,
        objPitch: -Math.PI/2,
        //objY: 2.25,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(iPowerupc).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          powerupTypes.filter(powerup => powerup.name == 'missiles')[0].shape = res
          await powerupShader.ConnectGeometry(res)
        })
      })

      var geoOptions = {
        shapeType: 'obj',
        url: 'https://srmcgann.github.io/objs/car/health.obj',
        map: 'https://srmcgann.github.io/objs/car/health.png',
        name: 'health powerup',
        scaleX: 2,
        scaleY: 2,
        scaleZ: 2,
        colorMix: 0,
        rotationMode: 2,
        //objPitch: -Math.PI/2,
        //objY: 2.25,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(iPowerupc).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          powerupTypes.filter(powerup => powerup.name == 'health')[0].shape = res
          await powerupShader.ConnectGeometry(res)
        })
      })  

      var geoOptions = {
        shapeType: 'obj',
        url: 'https://srmcgann.github.io/objs/car/winged_boot.obj',
        map: 'https://srmcgann.github.io/objs/car/winged_boot.png',
        name: 'drift powerup',
        scaleX: 8,
        scaleY: 8,
        scaleZ: 8,
        colorMix: 0,
        rotationMode: 2,
        //objPitch: -Math.PI/2,
        objY: 1,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(iPowerupc).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          powerupTypes.filter(powerup => powerup.name == 'drift')[0].shape = res
          await powerupShader.ConnectGeometry(res)
        })
      })  

      
      var geoOptions = {
        shapeType: 'obj',
        url: 'https://srmcgann.github.io/objs/car/cannon.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        name: 'cannon powerup',
        colorMix: 0,
        rotationMode: 2,
        objPitch: -Math.PI/2,
        scaleX: 3,
        scaleY: 3,
        scaleZ: 3,
        //objY: 2.25,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(iPowerupc).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          powerupTypes.filter(powerup => powerup.name == 'cannon')[0].shape = res
          await powerupShader.ConnectGeometry(res)
        })
      })  

      var geoOptions = {
        shapeType: 'obj',
        url: 'https://srmcgann.github.io/objs/car/overdrive.obj',
        map: 'https://srmcgann.github.io/objs/car/car.png',
        name: 'overdrive powerup',
        colorMix: 0,
        rotationMode: 2,
        scaleX: 5,
        scaleY: 5,
        scaleZ: 5,
        objPitch: -Math.PI/2,
        //objY: 2.25,
      }
      await Coordinates.LoadGeometry(renderer, geoOptions).then(async (geometry) => {
        Coordinates.ShapeFromArray(geometry,
                                   Array(iPowerupc).fill().map(v=>[1e5, 1e5, 1e5])).then(async res => {
          powerupTypes.filter(powerup => powerup.name == 'overdrive')[0].shape = res
          await powerupShader.ConnectGeometry(res)
        })
      })

      if(1) Coordinates.LoadFPSControls(renderer, {
        flyMode: false,
        mSpeed: .5,
        crosshairSel: 2,
        crosshairSize: 3,
        showCrosshair: true,
        useKeys: false,
        crossharSel: 2,
        crosshairSize: .5,
      })

      //uncomment above to enable mouse/keyboard controls

      var x2, y2, z2
      const Floor = (x, z) => {
        var ret = 0
        
        switch(floorMode){
          case 1:
            return ret - 20
          break
          case 2:
            var mag = 1
            while(x > cl * sp * 3*mag) x -= cl * sp * 6*mag
            while(z > br * sp * 3*mag) z -= br * sp * 6*mag
            while(x < -cl * sp * 3*mag) x += cl * sp * 6*mag
            while(z < -br * sp * 3*mag) z += br * sp * 6*mag
            d = Math.hypot(x, z)
            return  Math.max(-50, Math.min(0,30-(d**.5)*6))
          break
          default:
          break
        }
        
        //for(j = 0; j < 9; j++){
        //var tx = ((j%3)-3/2+.5) * cl * sp * 3
        //var ty = 0
        //var tz = ((j/3|0)-3/2+.5) * br * sp * 3
        for(var i = 0; i < hillPointsShape.vertices.length; i +=3 ){
          var x2 = hillPointsShape.vertices[i+0]
          var z2 = hillPointsShape.vertices[i+2]
          d = Math.hypot(x2-x, z2-z) + .0001
          ret += (hillMag / 10 / (3+C(d/4+renderer.t))) / (1+(1+d)**3/3000) * ((i/3)%2?-1:1)
          //ret += 20 / (1+d**2/100)
        }
        //}
        ret += C(x/64)*C(z/64)  * Math.min(1, Math.max(-1, C(renderer.t/16)*6))

        return Math.min(hillMag/2, Math.max(-hillMag/2, ret * hillMag)) - 20
      }

      const SpawnFlash = (x, y, z, mag=1) => {
        flashes.push({ x, y, z, life: mag })
      }


      const SpawnSmoke = (x, y, z) => {
        for(var m = Rn() < .5 ? 1 : 2; m--;) {
          var v = Rn()**.5 * iSmokev
          var vx = S(p=Math.PI*2*Rn()) * S(q=Rn()<.5?Math.PI/2*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5)  * v
          var vy = C(q)        * v
          var vz = C(p) * S(q) * v
          smoke.push({ x, y, z, vx, vy, vz, life: 1 })
        }
      }

      const SpawnRPG = car => {
        x = car.x
        y = car.y
        z = car.z
        var vx = 0
        var vy = 0
        var vz = 1
        vy = S(car.pitch)
        vz = C(car.pitch)
        p = Math.atan2(vx, vz) + car.yaw
        d = Math.hypot(vx, vz)
        vx = S(p) * d * iRPGv
        vz = C(p) * d * iRPGv

        var tx = 0
        var ty = 1.4
        var tz = 0
        p = Math.atan2(ty, tz) + car.pitch
        d = Math.hypot(ty, tz)
        ty = S(p) * d
        tz = C(p) * d
        p = Math.atan2(tx, ty) + car.roll
        d = Math.hypot(tx, ty)
        tx = S(p) * d
        ty = C(p) * d
        p = Math.atan2(tx, tz) + car.yaw
        d = Math.hypot(tx, tz)
        tx = S(p) * d
        tz = C(p) * d
        RPGs.push({
          x: x + tx, y: y + ty + 1, z: z + tz,
          vx, vy, vz, life: 1,
          roll: car.roll,
          pitch: car.pitch,
          yaw: car.yaw,
          rollv: car.roll,
          pitchv: car.pitch,
          yawv: car.yaw,
          id: car.id,
        })
        
        // missile sounds
        if(!muted){
          if(!missileLaunchSound.paused) missileLaunchSound.pause()
          missileLaunchSound.currentTime = 0
          missileLaunchSound.play()
        }
      }


      const CarShape = car => {
        var ret
        if(car.hasOverdrive && car.hasCannon){
          ret = gunEngineCarShape
        }else if(car.hasOverdrive && !car.hasCannon){
          ret = engineCarShape
        }else if(!car.hasOverdrive && car.hasCannon){
          ret = gunCarShape
        }else{
          ret = carShape
        }
        return ret
      }

      const DrawHUD = car => {
        if(car.cameraMode == 'fps' && cameraCar == car.id) return
        var coord = Coordinates.GetShaderCoord(car.x + car.vx,
                                               car.y + car.vy + 1,
                                               car.z + car.vz,
        {x: 0, y: 0, z: 0, roll: 0, pitch: 0, yaw: 0}, renderer)
        if(coord){
          ctx.fillStyle = '#f00'
          var s = Math.max(25, Math.min(150,
                   1e3 / (1+Math.hypot(car.x + renderer.x,
                                       car.y + renderer.y + 1,
                                       car.z + renderer.z)/5)))
          ctx.globalAlpha = .5
          DrawRotatedImage(markerImage, coord[0], coord[1],s,s, renderer.t*2)
          
          ctx.lineWidth = 8
          ctx.beginPath()
          var x1 = coord[0]
          var y1 = coord[1]
          p = Math.atan2(w/2 - x1, h/2 - y1) + Math.PI
          x1 += S(p) * s / 2
          y1 += C(p) * s / 2
          ctx.lineTo(x1, y1)
          var x2 = x1 + S(p) * 50
          var y2 = y1 + C(p) * 50
          ctx.lineTo(x2, y2)
          ctx.strokeStyle = '#40f3'
          ctx.stroke()
          //ctx.lineWidth /= 5
          //ctx.strokeStyle = '#40f'
          //ctx.stroke()
          ctx.fillStyle = '#204'
          ctx.textAlign = 'left'
          fs=32
          if(coord[0] < w/2){
            if(coord[1] < h/2){
              ctx.strokeRect(x2, y2, -250, -fs*6)
              ctx.fillRect(x2, y2, -250, -fs*6)
              ctx.drawImage(H_keytipImage, x2-250-H_keytipImage.width/2,
                               y2-fs*6-H_keytipImage.height/2)
            }else{
              ctx.strokeRect(x2, y2, -250, fs*6)
              ctx.fillRect(x2, y2, -250, fs*6)
              ctx.drawImage(H_keytipImage, x2-250-H_keytipImage.width/2,
                               y2-H_keytipImage.height/2)
            }
          }else{
            if(coord[1] < h/2){
              ctx.strokeRect(x2, y2, 250, -fs*6)
              ctx.fillRect(x2, y2, 250, -fs*6)
              ctx.drawImage(H_keytipImage, x2-H_keytipImage.width/2,
                               y2-fs*6-H_keytipImage.height/2)
            }else{
              ctx.strokeRect(x2, y2, 250, fs*6)
              ctx.fillRect(x2, y2, 250, fs*6)
              ctx.drawImage(H_keytipImage, x2-H_keytipImage.width/2,
                               y2-H_keytipImage.height/2)
            }
          }
          
          ctx.globalAlpha = 1
          ctx.font = (fs=24) + 'px Technology'
          x2 += fs * (coord[0] < w/2 ? -.5 : .5) - (coord[0] < w/2 ? 250 - fs*1 : 0)
          y2 += fs * (coord[1] < h/2 ? -1 : 1) - (coord[1] < h/2 ? fs*6 : 0)
          ctx.fillStyle = '#ff0'
          ctx.strokeStyle = '#000'
          ctx.lineWidth = 3
          str = `${car.name}`
          ctx.strokeText(str, x2, y2)
          ctx.fillText(str, x2, y2)
          ctx.fillStyle = '#8ff'
          str = `score       : ${car.score}`
          ctx.strokeText(str, x2, y2 + fs * 1)
          ctx.fillText(str, x2, y2 + fs * 1)
          str = `health    : ${Math.round(car.health)}%`
          
          ctx.fillStyle = '#444'
          ctx.fillRect(x2-6, y2-8+fs*1.5, 280-fs*2.3+12, fs*1.2-4)
          ctx.fillStyle = `hsla(${car.health},100%,50%,.8)`
          ctx.fillRect(x2-4, y2-6+fs*1.5, (280-fs*2.3+8)*car.health/100, fs/1.2)
          ctx.strokeText(str, x2, y2 + fs * 2)
          ctx.fillStyle = '#8ff'
          ctx.fillText(str, x2, y2 + fs * 2)
          
          str = `speed     : ${Math.round(car.actualSpeed*50*10)/10} MPH`
          ctx.strokeText(str, x2, y2 + fs * 3)
          ctx.fillText(str, x2, y2 + fs * 3)
          str = `gun       : ${car.hasCannon}`
          ctx.strokeText(str, x2, y2 + fs * 4)
          ctx.fillText(str, x2, y2 + fs * 4)
          str = `o-drive   : ${car.hasOverdrive}`
          ctx.strokeText(str, x2, y2 + fs * 5)
          ctx.fillText(str, x2, y2 + fs * 5)
          str = `missiles  : ${car.missiles}`
          ctx.strokeText(str, x2, y2 + fs * 6)
          ctx.fillText(str, x2, y2 + fs * 6)
        }
      }

      const ReturnToLobby = () => {
        promptVisible = true
        if(confirm('return to lobby?')){
          location.href = `../index2.php?fm=${floorMode}&sky=${skySel}&fl=${floorSel}&fe=${fogEnabled?1:0}`
        }else{
          promptVisible = false
        }
      }
      
      const SpawnSparks = (x, y, z, mag=1, avx=0, avy=0, avz=0) => {
        for(var m = 20 * mag; m--;) {
          var v = (.25+Rn()**.5*.75) * iSparksv * (1+mag/10)
          var vx = S(p=Math.PI*2*Rn()) * S(q=Rn()<1?Math.PI/3*Rn()**.5:Math.PI-Math.PI/2*Rn()**.5)  * v + avx
          var vy = C(q)        * v * 2 + avy
          var vz = C(p) * S(q) * v + avz
          sparks.push({ x, y, z, vx, vy, vz, life: 1 })
        }
      }
      
      const ManagePowerups = () => {
        if(powerupTypes.filter(powerup => powerup.shape?.shapeData).length < powerupTypes.length) return
        powerupTypes.map(powerupType => {
          powerupType.shape.shapeData.map((subShape, sIdx) =>{
            subShape.x = 0
            subShape.y = 0
            subShape.z = 0
          })
        })
        while(powerups.length < iPowerupc){
          var cIdx = cars.length * Rn() | 0
          d = 10 + 100 * Rn() ** .5
          x = cars[cIdx].x + S(p=Math.PI*2*Rn()) * d
          z = cars[cIdx].z + C(p) * d
          y = Floor(x, z) + 3
/*
        { name: 'health', shape: ''},
        { name: 'missiles', shape: '' },
        { name: 'cannon', shape: '' },
        { name: 'overdrive', shape: ''},
        { name: 'drift', shape: ''},
*/

          var rType = Rn()
          if(rType<=.5){
            type = 1 // missiles
          }else if(rType < 1/3/2+.5){
            type = 0 // health
          }else if(rType < 2/3/2+.5){
            type = Rn () < .5 ? 1 : 4 // missiles / drift
          }else if(rType < 3/3/2+.5){
            if(Rn() < .5){ // cannon / overdrive
              type = cars.filter(car => car.hasGun) < cars.length ? 2 : 1 
            }else{
              type = cars.filter(car => car.hasOverdrive) < cars.length ? 3 : 1 
            }
          }
          var type = Rn() * powerupTypes.length | 0, shape
          powerups.push({ x, y, z, life: powerupLife, type,
                          shape: powerupTypes[type].shape,
                          name: powerupTypes[type].name })
        }
        var ct = Array(powerupTypes.length).fill(0)
        powerups = powerups.filter(powerup => powerup.life > 0)
        ctx.globalAlpha = .6
        powerups.map((powerup, pIdx) => {
          var subShape = powerup.shape.shapeData[ct[powerup.type]++]
          subShape.x = powerup.x - subShape.ox
          subShape.z = powerup.z - subShape.oz
          subShape.y = Floor(powerup.x, powerup.z) - subShape.oy + 2
          switch(powerup.name){
            case 'missiles':
              subShape.yaw += .05
              //subShape.roll+= .2
              subShape.y += 2
              subShape.pitch += .025
            break
            case 'cannon':
              subShape.yaw += .2
              subShape.pitch = Math.PI/2
            break
            case 'overdrive':
              subShape.yaw += .2
              subShape.pitch = Math.PI/2
            break
            case 'health':
              subShape.yaw += .15
              subShape.pitch += .025
            break
            case 'drift':
              subShape.yaw += .2
            break
          }
          powerup.life -= .25
          cars.map((car, cIdx) => {
            var dist = Math.hypot(powerup.x - car.x,
                                  //powerup.y - car.y + 3,
                                  powerup.z - car.z)
            if(dist < 10){
              if(cIdx == cameraCar && !muted){
                goalSound.currentTime = 0
                goalSound.play()
              }
              powerup.life = 0
              SpawnFlash(powerup.x, powerup.y, powerup.z, 5)
              subShape.x = 0
              subShape.z = 0
              subShape.y = 0
              switch(powerup.name){
                case 'missiles':
                  car.missiles += 25
                break
                case 'cannon':
                  car.hasCannon = true
                break
                case 'overdrive':
                  car.hasOverdrive = true
                break
                case 'health':
                  car.health = 100
                break
                case 'drift':
                  var td = car.drift
                  car.drift = renderer.t + driftTimer
                  if(td != car.drift && car.hasTraction &&
                     !muted){
                    tiresScreechingSound.currentTime = Rn() * tiresScreechingSound.duration
                    tiresScreechingSound.play()
                  }
                break
              }
            }else if(cIdx == cameraCar){
              if(car.painAlpha > .05){
                ctx.globalAlpha = Math.min(.75, car.painAlpha)
                ctx.drawImage(pain, 0, 0, w, h)
                car.painAlpha -= .05
                ctx.globalAlpha = 1
              }else{
                car.painAlpha = 0
              }
              if(showHUD){
                var coord = Coordinates.GetShaderCoord(powerup.x,
                                                       subShape.y + subShape.oy,
                                                       powerup.z,
                                                       powerup.shape, renderer)
                if(coord){
                  var s = Math.max(32, Math.min(200, 1200 / (1+dist/5)))
                  ctx.globalAlpha = .5
                  DrawRotatedImage(powerupImage, coord[0], coord[1],s,s, -renderer.t * 4)
                  ctx.globalAlpha = 1
                  ctx.font = (fs=24) + 'px Technology'
                  ctx.lineWidth = 8
                  ctx.beginPath()
                  var x1 = coord[0]
                  var y1 = coord[1]
                  p = Math.atan2(w/2 - x1, h/2 - y1) + Math.PI
                  x1 += S(p) * s / 2
                  y1 += C(p) * s / 2
                  ctx.lineTo(x1, y1)
                  var x2 = x1 + S(p) * 50
                  var y2 = y1 + C(p) * 50
                  ctx.lineTo(x2, y2)
                  ctx.strokeStyle = '#0fa3'
                  ctx.stroke()
                  //ctx.lineWidth /= 5
                  //ctx.strokeStyle = '#0fa'
                  //ctx.stroke()
                  ctx.fillStyle = '#0424'
                  ctx.textAlign = 'left'
                  if(coord[0] < w/2){
                    if(coord[1] < h/2){
                      ctx.strokeRect(x2, y2, -200, -fs*3.25)
                      ctx.fillRect(x2, y2, -200, -fs*3.25)
                      ctx.drawImage(H_keytipImage, x2-200-H_keytipImage.width/2,
                                       y2-fs*3.25-H_keytipImage.height/2)
                    }else{
                      ctx.strokeRect(x2, y2, -200, fs*3.25)
                      ctx.fillRect(x2, y2, -200, fs*3.25)
                      ctx.drawImage(H_keytipImage, x2-200-H_keytipImage.width/2,
                                       y2-H_keytipImage.height/2)
                    }
                  }else{
                    if(coord[1] < h/2){
                      ctx.strokeRect(x2, y2, 200, -fs*3.25)
                      ctx.fillRect(x2, y2, 200, -fs*3.25)
                      ctx.drawImage(H_keytipImage, x2-H_keytipImage.width/2,
                                       y2-fs*3.25-H_keytipImage.height/2)
                    }else{
                      ctx.strokeRect(x2, y2, 200, fs*3.25)
                      ctx.fillRect(x2, y2, 200, fs*3.25)
                      ctx.drawImage(H_keytipImage, x2-H_keytipImage.width/2,
                                       y2-H_keytipImage.height/2)
                    }
                  }
                  
                  ctx.globalAlpha = 1
                  ctx.font = (fs=24) + 'px Technology'
                  x2 += fs * (coord[0] < w/2 ? -.5 : .5) - (coord[0] < w/2 ? 200 - fs*1 : 0)
                  y2 += fs * (coord[1] < h/2 ? -1 : 1) - (coord[1] < h/2 ? fs*1.5 : 0)
                  ctx.fillStyle = '#0cf'
                  ctx.strokeStyle = '#000'
                  ctx.lineWidth = 3
                  //str = `powerup  :`
                  //ctx.strokeText(str, x2, y2)
                  //ctx.fillText(str, x2, y2)
                  str = `distance :`
                  ctx.strokeText(str, x2, y2 + fs * 1)
                  ctx.fillText(str, x2, y2 + fs * 1)
                  str = `expires  :`
                  
                  ctx.fillStyle = '#444'
                  ctx.fillRect(x2-6, y2-3+fs*1.23, 230-32*2+14, fs*1.3-4)
                  ctx.fillStyle = `hsla(${powerup.life},100%,50%,.8)`
                  ctx.fillRect(x2-4, y2-1+fs*1.23, (230-32*2+12)*powerup.life/100, fs)
                  
                  ctx.fillStyle = '#0cf'
                  ctx.strokeText(str, x2, y2 + fs * 2)
                  ctx.fillText(str, x2, y2 + fs * 2)
                  ctx.fillStyle = '#ff0'
                  ctx.strokeStyle = '#000'
                  ctx.lineWidth = 3
                  str = ` ${powerup.name}`
                  ctx.strokeText(str, x2, y2)
                  ctx.fillText(str, x2, y2)
                  str = `           ${Math.round(dist)}`
                  ctx.strokeText(str, x2, y2 + fs * 1)
                  ctx.fillText(str, x2, y2 + fs * 1)
                  str = `           ${Math.round(powerup.life)}`
                  
                  
                  ctx.strokeText(str, x2, y2 + fs * 2)
                  ctx.fillText(str, x2, y2 + fs * 2)
                }
              }
            }
          })
        })
        ctx.globalAlpha = 1
        powerupTypes.forEach((powerupType, pIdx) => {
          if(powerups.filter(pu => pu.type == pIdx).length){
            renderer.Draw(powerupType.shape)
          }
        })
      }
      
      const DrawRotatedImage = (image, x, y, width, height, angle) => { 
        ctx.save();
        ctx.translate(x, y);
        ctx.rotate(angle);
        ctx.drawImage(image, -width/2, -height/2, width, height);
        ctx.restore();
      }      
      
      const RebindTexturesMaybe = shape => {
        
        if(rebindTextures){
          SetLevelTextures(skySel, floorSel)
          
          var el = shape.shader?.datasets.filter(dset=>dset?.optionalLighting)
          el.map(el2=>{
            el2.optionalLighting.filter(v=>v.name=='ambientLight').map(oU => {
              oU.value = ambientLightValue
            })
          })
          var el = shape.shader?.datasets.filter(dset=>dset?.optionalUniforms)
          el.map(el2=>{
            el2.optionalUniforms.filter(v=>v.name=='phong').map(oU => {
              oU.value = phongValue
            })
            el2.optionalUniforms.filter(v=>v.name=='fog').map(oU => {
              oU.color = fogColor
              oU.value = fogValue
              oU.enabled = fogEnabled
            })
            el2.optionalUniforms.filter(v=>v.name=='reflection').map(oU => {
              oU.map = refTextures[skySel]
              oU.value = reflectionValue
              oU.rebindTextures = true
            })
            el2.optionalUniforms.filter(v=>v.name=='refraction2').map(oU => {
              oU.map = refTextures[skySel]
              oU.value = refractionValue
              oU.rebindTextures = true
            })
          })
        }
      }
      
      const ToggleMute = (forceToggleButton = false) => {
        muted = !muted
        if(forceToggleButton){
          var el = buttons.filter(button => button.name = 'mute button')[0]
          el.activeState = !el.activeState
        }
        if(muted){
          if(!missileLaunchSound.paused){
            missileLaunchSound.pause()
          }
          if(!goalSound.paused){
            goalSound.pause()
          }
          if(!engineIdleSound.paused){
            engineIdleSound.pause()
          }
          if(!alarmSound.paused){
            alarmSound.pause()
          }
          if(!engineAccelSound.paused){
            engineAccelSound.pause()
          }
          if(!tiresScreechingSound.paused){
            tiresScreechingSound.pause()
          }
          if(!splode1Sound.paused){
            splode1Sound.pause()
          }
          if(!splode2Sound.paused){
            splode2Sound.pause()
          }
          metalSounds.map(snd => {
            if(!snd.paused) snd.pause()
          })
        }
      }
      
      const HandleButtons = () => {
/*
        {
          x: c.width - 200,
          y: 20,
          actCap: '🔊'
          inactCap: '🔇'
          activeStrokeStyle: '#0f4'
          activeFillStyle: '#0628'
          inactiveStrokeStyle: '#c00'
          inactiveFillStyle: '#6008'
          activeTextColor: '#8fc',
          inactiveTextColor: '#f88',
          width: 100,
          height: 32,
          fontSize: 24,
          activeState: false,
          enabled: true,
        }
*/      

        //ctx.fillStyle = '#f00'
        //var s = 20
        var mx = renderer.mouseX
        var my = renderer.mouseY
        //ctx.fillRect(mx-s/2, my-s/2, s, s)
        ctx.textAlign = 'center'
        buttons.map((button, bIdx) => {
          var x1 = button.x
          var y1 = button.y
          var x2 = button.x + button.width
          var y2 = button.y + button.height
          if(button.activeState){
            ctx.strokeStyle = button.activeStrokeStyle
            ctx.fillStyle = button.activeFillStyle
            ctx.fillRect(button.x, button.y, button.width, button.height)
            ctx.strokeRect(button.x, button.y, button.width, button.height)
            ctx.fillStyle = button.activeTextColor
            fs = button.fontSize
            ctx.font = fs + 'px Technology'
            ctx.fillText(button.actCap,
                         button.x + button.width/2,
                         button.y + button.height/2 + fs/3)
          }else{
            ctx.strokeStyle = button.inactiveStrokeStyle
            ctx.fillStyle = button.inactiveFillStyle
            ctx.fillRect(button.x, button.y, button.width, button.height)
            ctx.strokeRect(button.x, button.y, button.width, button.height)
            ctx.fillStyle = button.inactiveTextColor
            fs = button.fontSize
            ctx.font = fs + 'px Technology'
            ctx.fillText(button.inactCap,
                         button.x + button.width/2,
                         button.y + button.height/2 + fs/3)
          }
          var fs = 16
          ctx.font = fs + 'px Technology'
          if((button.hover || renderer.mouseButton == -1) &&
            mx >= x1 && mx < x2 && my >= y1 && my < y2){
            button.hover = true
            ctx.fillStyle = '#333c'
            var textWidth = ctx.measureText(button.toolTip).width
            if(c.width-mx < textWidth + 5){
              ctx.fillRect(mx - textWidth - 20, my, textWidth + 20, fs*2)
              ctx.fillStyle = '#fffc'
              ctx.fillText(button.toolTip,
                    mx - textWidth/2 - 10, my+fs*1.33)
            }else{
              ctx.fillRect(mx - 10, my, textWidth + 20, fs*2)
              ctx.fillStyle = '#fffc'
              ctx.fillText(button.toolTip,
                    mx + textWidth/2, my+fs*1.33)
            }
            if(renderer.mouseButton != -1){
              button.hover = false
              button.activeState = !button.activeState
              eval(button.clickScript)
            }
          }else{
            button.hover = false
          }
        })
      }

      renderer.z = 20
      renderer.yaw = Math.PI/2
      renderer.pitch = .8
      //player.ovx = renderer.x
      //player.ovy = renderer.y 
      //player.ovz = renderer.z

      window.Draw = () => {
        var t = renderer.t
        renderer.Clear()
        ctx.clearRect(0,0,w,h)
        sctx.clearRect(0,0,w,h)

        ManagePowerups()

        if(0) if(!((renderer.frameCount)%cycleTexturesFreq)){
          skySel = (skySel + 1) % refTextures.length
          SetLevelTextures(skySel, floorSel)
        }

        var fs

        cars.map((car, cIdx) => {
          if(showHUD) DrawHUD(car)
          switch(cIdx){
            case 0:  // this player
              break
            default: // other players
              
              if(car.followTarget === false){
                if(Rn() < .01){
                  car.keys[32] = true
                }else{
                  car.keys[32] = false
                }
                car.keys[38] = Rn() < .25 ? true : false
                if(Rn() < .2){
                  if(Rn() < .5){
                    car.keys[37] = true
                    car.keys[39] = false
                  }else{
                    car.keys[37] = false
                    car.keys[39] = true
                  }
                }
              }
              break
          }
          DoKeys(car) 
        })

        //var floor = -Floor(-renderer.x, -renderer.z) - 20 // 6

        //player.vx /= renderer.hasTraction ? 1.01 : 1.01
        //player.vy /= renderer.hasTraction ? 1.01 : 1.01
        //player.vz /= renderer.hasTraction ? 1.01 : 1.01

        //renderer.x += player.vx += (renderer.x - player.ovx) / 40
        //renderer.y += player.vy += grav + (renderer.y - player.ovy) / 2
        //renderer.z += player.vz += (renderer.z - player.ovz) / 40

        //player.ovx = renderer.x
        //player.ovy = renderer.y
        //player.ovz = renderer.z


        //if(renderer.mouseButton == 2 &&
        //   player.vy > jumpHeight) Jump()


        cars.map((car, cIdx) => {
          var shape = CarShape(car)
          car.speed /= 1.1
          car.accel /= 1.1
          var odrft = Math.min(.95, Math.max(0, car.drift > t ? 1 : .5))
          car.speed += car.accel * (8 - odrft*8) * (car.hasOverdrive ? 3 : 1)
          car.distance += car.actualSpeed * 1.1

          car.vx /= 1.01
          car.vy /= 1.01
          car.vz /= 1.01

          if(car.hasTraction){
            car.vx /= 1 + (.95 - odrft)
            car.vz /= 1 + (.95 - odrft)
            car.vx += S(car.yaw) * car.speed / 14
            car.vz += C(car.yaw) * car.speed / 14
          }
          if((car.y - car.ovy) > 1){
            car.vx += (car.x - car.ovx) / 40
            car.vy += (car.y - car.ovy) / 40 * (car.hasTraction ? 10 : 1)
            car.vz += (car.z - car.ovz) / 40
          }

          car.vy = Math.min(10, Math.max(-10, car.vy))

          if(showDebug){
            ctx.font = (fs=40) + 'px Technology'
            ctx.fillStyle = '#fff'
            ctx.strokeStyle = '#000'
            ctx.lineWidth = 5
            fs = 24
            str = `skySel ${skySel}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8 - fs)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8 - fs)
            str = `car.id ${car.id}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8)
            str = `car.vy ${Math.round(car.vy*1e3)/1e3}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8 + fs)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8 + fs)
            str = `car.x ${Math.round(car.x*1e3)/1e3}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8 + fs*2)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8 + fs*2)
            str = `car.y ${Math.round(car.y*1e3)/1e3}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8 + fs*3)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8 + fs*3)
            str = `car.z ${Math.round(car.z*1e3)/1e3}`
            ctx.strokeText(str, 50, 100 + cIdx * fs * 8 + fs*4)
            ctx.fillText(str, 50, 100 + cIdx * fs * 8 + fs*4)
          }

          car.ovx = car.x
          car.ovy = car.y
          car.ovz = car.z

          car.x += car.vx
          car.y += car.vy -= grav * (car.hasTraction ? 0 : 1)
          car.z += car.vz

          var cFloor = Floor(car.x, car.z) + (4.6 + 2.5)*wmag
          car.y = Math.max(cFloor, car.y)

          var tht = car.hasTraction
          car.hasTraction = Math.abs(car.y - cFloor) < 3.5
          if(car.hasTraction && !tht && car.vy < -2){
            car.bounced = true
            SpawnSparks(car.x, car.y, car.z)
            var painVal = Rn() * 10
            car.painAlpha += painVal / 20
            car.health -= painVal
            if(car.health < 0){
              SpawnSparks(car.x, car.y, car.z, 10)
              SpawnSparks(car.x, car.y, car.z, 10)
              SpawnSparks(car.x, car.y, car.z, 10)
              SpawnFlash(car.x, car.y, car.z, 10)
              Respawn(car)
              if(!muted){
                splode2Sound.currentTime = 0
                splode2Sound.volume = 1 / (1+(1+Math.hypot(car.x + renderer.x, car.y + renderer.y,
                        car.z + renderer.z))**2/20000)
                splode2Sound.play()
              }
            }else{
              car.vx /= 1.25
              car.vz /= 1.25
              car.vy = -car.vy/4
              car.hasTraction = false
              if(cIdx == cameraCar && !muted){
                var choice = Rn() * metalSounds.length | 0
                metalSounds[choice].play()
              }
            }
          }

          if(cIdx == cameraCar){
            if(!muted){
              if(car.drift && car.hasTraction){
                if(car.actualSpeed * 10 > 10){
                  if(tiresScreechingSound.paused) tiresScreechingSound.play()
                }else{
                  if(!tiresScreechingSound.paused) tiresScreechingSound.pause()
                }
              }else{
                if(!tiresScreechingSound.paused) tiresScreechingSound.pause()
              }
            }
            car.idlingv = car.idling
            car.idling = car.accel < .05
            if(!muted && car.idlingv != car.idling){
              // idle status changed
              if(car.idling){
                engineIdleSound.volume = car.hasOverdrive ? .5 : .2
                engineAccelSound.pause()
                engineIdleSound.currentTime = Rn() * engineIdleSound.duration
                engineIdleSound.play()
              }else{
                engineAccelSound.volume = car.hasOverdrive ? .2 : .05
                engineIdleSound.pause()
                engineAccelSound.currentTime = Rn() * engineAccelSound.duration
                engineAccelSound.play()
              }
            }
          
            if(cameraCar != 0){
              var fs = 32
              ctx.font = (fs) + 'px Technology'
              ctx.fillStyle = '#40f8'
              ctx.fillRect(0, 0, 450, fs*2)
              ctx.fillStyle = '#0f0'
              ctx.strokeStyle = '#000'
              ctx.strokeText(`cam view: ${cars[cameraCar].name}`, 20, 26)
              ctx.fillText(`cam view: ${cars[cameraCar].name}`, 20, 26)
              ctx.fillStyle = '#888'
              ctx.strokeText(`hit 0 to return`, 20, 26 + fs)
              ctx.fillText(`hit 0 to return`, 20, 26 + fs)
            }
            if(showStats){
              var fs=32
              var ofy = fs
              ctx.font = (fs) + 'px Technology'
              ctx.drawImage(statsContainer, w/2+150, 0, w/2-150, statsContainer.height)
              
              var forSort = Array(cars.length).fill().map((v, i) => {
                return { idx: i, score: cars[i].score }
              })
              
              var sortedCars = forSort.sort((a, b) => b.score - a.score)
              cars.map((q_, j_) => {
                var j = j_ 
                var q = cars[sortedCars[j_].idx]
                ctx.fillStyle = '#ff0'
                ctx.strokeStyle = '#000'
                ctx.strokeText(`#${j+1} ${q.name}`, w-220, fs*1 + fs*9*j + ofy)
                ctx.fillText(`#${j+1} ${q.name}`, w-220, fs*1 + fs*9*j + ofy)
                ctx.fillStyle = '#0f0'
                ctx.strokeStyle = '#000'
                ctx.strokeText(`score`, w-220, fs*2 + fs*9*j + ofy)
                ctx.fillText(`score`, w-220, fs*2 + fs*9*j + ofy)
                ctx.strokeText(`health`, w-220, fs*3 + fs*9*j + ofy)
                ctx.fillText(`health`, w-220, fs*3 + fs*9*j + ofy)
                ctx.strokeText(`missiles`, w-220, fs*4 + fs*9*j + ofy)
                ctx.fillText(`missiles`, w-220, fs*4 + fs*9*j + ofy)
                ctx.strokeText(`cannon`, w-220, fs*5 + fs*9*j + ofy)
                ctx.fillText(`cannon`, w-220, fs*5 + fs*9*j + ofy)
                ctx.strokeText(`overdrive`, w-220, fs*6 + fs*9*j + ofy)
                ctx.fillText(`overdrive`, w-220, fs*6 + fs*9*j + ofy)
                ctx.strokeText(`drift`, w-220, fs*7 + fs*9*j + ofy)
                ctx.fillText(`drift`, w-220, fs*7 + fs*9*j + ofy)

                ctx.fillStyle = '#0ff'
                ctx.strokeStyle = '#000'
                ctx.strokeText(`            ${q.score}`, w-220, fs*2 + fs*9*j + ofy)
                ctx.fillText(`            ${q.score}`, w-220, fs*2 + fs*9*j + ofy)
                ctx.fillStyle = `hsla(${q.health}, 99%, 50%, 1)`
                ctx.strokeText(`            ${Math.round(q.health)}`, w-220, fs*3 + fs*9*j + ofy)
                ctx.fillText(`            ${Math.round(q.health)}`, w-220, fs*3 + fs*9*j + ofy)
                ctx.fillStyle = q.missiles ? '#0f2' : '#f00'
                ctx.strokeText(`            ${q.missiles}`, w-220, fs*4 + fs*9*j + ofy)
                ctx.fillText(`            ${q.missiles}`, w-220, fs*4 + fs*9*j + ofy)
                ctx.fillStyle = q.hasCannon ? '#0f2' : '#f00'
                ctx.strokeText(`            ${q.hasCannon?'YES':'NO'}`, w-220, fs*5 + fs*9*j + ofy)
                ctx.fillText(`            ${q.hasCannon?'YES':'NO'}`, w-220, fs*5 + fs*9*j + ofy)
                ctx.fillStyle = q.hasOverdrive? '#0f2' : '#f00'
                ctx.strokeText(`            ${q.hasOverdrive ? 'YES' : 'NO'}`, w-220, fs*6 + fs*9*j + ofy)
                ctx.fillText(`            ${q.hasOverdrive ? 'YES' : 'NO'}`, w-220, fs*6 + fs*9*j + ofy)
                ctx.fillStyle = q.drift? '#0f2' : '#f00'
                ctx.strokeText(`            ${q.drift? 'YES' : 'NO'}`, w-220, fs*7 + fs*9*j + ofy)
                ctx.fillText(`            ${q.drift ? 'YES' : 'NO'}`, w-220, fs*7 + fs*9*j + ofy)
              })
            }
            switch(car.cameraMode){
              case 'fps':
                if(showHUD){
                  ctx.drawImage(carHUD, 0, 0, w, h)
                  DrawRotatedImage(guageNeedle,
                               372, h-5, 150, 150, -Math.PI / 2 + Math.PI/16*cars[cameraCar].actualSpeed)
                  ctx.textAlign = 'center'
                  ctx.font = '50px Technology'
                  ctx.fillStyle = '#f80'
                  ctx.fillText(Math.max(0,Math.round(cars[cameraCar].actualSpeed*10)), 372, h-16)
                  
                  DrawRotatedImage(guageNeedle,
                               372+263, h-5, 150, 150, -Math.PI / 2 + Math.PI / 100 * cars[cameraCar].health)
                  ctx.fillStyle = '#0ff'
                  ctx.fillText(Math.max(0,Math.round(cars[cameraCar].health)), 372+263, h-16)

                  DrawRotatedImage(guageNeedle,
                               372+263*2, h-5, 150, 150, -Math.PI / 2 + Math.PI/2 * Math.max(0, (cars[cameraCar].drift-t)*.1))
                  ctx.fillStyle = '#f0f'
                  ctx.fillText(Math.max(0,Math.round((cars[cameraCar].drift-t)*5)), 372+263*2, h-16)

                  DrawRotatedImage(steeringWheelImage, w*.773, h + 80, 650, 650, car.steerv*4)
                }
                renderer.yaw = (trackingYaw += (-(car.yaw+ (car.speed > -.5 ? 0: Math.PI)) - trackingYaw) / 10) 
                var camDist = 0
                renderer.x = -car.x
                renderer.z = -car.z
                renderer.pitch = .15
                renderer.y = Math.min(-Floor(-renderer.x, -renderer.z)-6, -car.y - 6)
              break
              case 'orbit':
                renderer.showCrosshair = false
                renderer.yaw = (trackingYaw += (-(car.yaw+ (car.speed > -.5 ? 0: Math.PI)) - trackingYaw + C(t/16)) / 10) 
                var camDist = Math.min(100, 18 + Math.abs(car.actualSpeed * 25)) / 5
                renderer.x = -car.x + S(p = -trackingYaw) * camDist
                renderer.z = -car.z + C(p) *camDist
                renderer.pitch = .66 - camDist / 50
                renderer.y = Math.min(-Floor(-renderer.x, -renderer.z)-8, -car.y - 8)
              break
            }
          }
          if(cIdx != 0){
            // nonhuman players
            var el, score
            var bestScore = 6e6
            car.battleMode = false
            if(car.health < 25 && (el=powerups.filter(powerup => powerup.name == 'health')).length){
              el.map((powerup, pIdx) => {
                var plife = powerup.life
                if((score = Math.hypot(powerup.x-car.x,
                                       powerup.y-car.y,
                                       powerup.z-car.z)/plife) < bestScore &&
                score < 5){
                  bestScore = score
                  car.followDistance = 0
                  car.followTarget = powerup
                }
              })
            }else if(car.hasCannon && car.missiles){
              cars.map((car2, cIdx2) => {
                if(cIdx != cIdx2){
                  if((d = Math.hypot(car2.x-car.x,
                                     car2.y-car.y,
                                     car2.z-car.z)) < bestScore){
                    bestScore = d
                    car.followTarget = car2
                    car.followDistance = defaultFollowDistance
                    car.battleMode = true
                  }
                }
              })
            } else if(!car.hasOverdrive &&
                (el=powerups.filter(powerup => powerup.name == 'overdrive')).length){
              el.map((powerup, pIdx) => {
                var plife = powerup.life
                if((score = Math.hypot(powerup.x-car.x,
                                       powerup.y-car.y,
                                       powerup.z-car.z)/plife) < bestScore &&
                score < 5){
                  bestScore = score
                  car.followDistance = 0
                  car.followTarget = powerup
                }
              })
            }else if(!car.hasCannon &&
                (el=powerups.filter(powerup => powerup.name == 'cannon')).length){
              el.map((powerup, pIdx) => {
                var plife = powerup.life
                if((score = Math.hypot(powerup.x-car.x,
                                       powerup.y-car.y,
                                       powerup.z-car.z)/plife) < bestScore &&
                score < 5){
                  bestScore = score
                  car.followDistance = 0
                  car.followTarget = powerup
                }
              })
            }else if((el=powerups.filter(powerup => powerup.name == 'missiles')).length){
              el.map((powerup, pIdx) => {
                var plife = powerup.life
                if((score = Math.hypot(powerup.x-car.x,
                                       powerup.y-car.y,
                                       powerup.z-car.z)/plife) < bestScore &&
                score < 5){
                  bestScore = score
                  car.followDistance = 0
                  car.followTarget = powerup
                }
              })
            }
            if(car.followTarget === false){
              // did not find a suitable powerup
              if(car.hasCannon && car.missiles){
                cars.map((car2, cIdx2) => {
                  if(cIdx != cIdx2){
                    if((d = Math.hypot(car2.x-car.x,
                                       car2.y-car.y,
                                       car2.z-car.z)) < bestScore){
                      bestScore = d
                      car.followTarget = car2
                      car.followDistance = defaultFollowDistance
                      car.battleMode = true
                    }
                  }
                })
              }else{
                car.followTarget = false
              }
            }
            var fTarg = car.followTarget
            d = Math.hypot(fTarg.x-car.x, fTarg.y-car.y, fTarg.z-car.z)
            if(d > car.followDistance){
              if(Rn() < .1 || (Rn() < .8 && d > 100)) car.keys[38] = true
              p = Math.atan2(fTarg.x-car.x, fTarg.z-car.z)
              while(Math.abs(p-car.yaw) > Math.PI){
                p += p < car.yaw ? Math.PI*2 : -Math.PI*2
              }
              if(Math.abs(p-car.yaw) > .05) {
                if(p < car.yaw){
                  car.keys[39] = false
                  car.keys[37] = true
                }else{
                  car.keys[39] = true
                  car.keys[37] = false
                }
              }
            }else{
              car.keys[38] = false
              car.keys[40] = false
              if(car.brakeAtDestination && car.actualSpeed > .2){
                car.keys[40] = true
              }
            }
            
            if(car.battleMode){
              if(Rn() < .05){
                car.keys[32] = true
              }else{
                car.keys[32] = false
              }
            }
          }
          
          while(Math.abs(car.yawv - car.yaw) > Math.PI){
            car.yaw += Math.PI * 2 * (car.yawv < car.yaw ? -1 : 1)
          }
          
          //while(car.yawv > Math.PI) {
            //car.yaw -= Math.PI * 2
            //car.yawv -= Math.PI * 2
          //}
          //while(car.yawv < -Math.PI) {
            //car.yaw += Math.PI * 2
            //car.yawv += Math.PI * 2
          //}
          
          car.rollv += (car.roll - car.rollv) / 2
          car.pitchv += (car.pitch - car.pitchv) / 2
          car.yawv += (car.yaw - car.yawv) / 2
          if(car.cameraMode != 'fps' ||
             cIdx != cameraCar){
            shape.roll = car.rollv
            shape.pitch = car.pitchv
            shape.yaw = car.yawv
            shape.x = car.x
            shape.y = car.y
            shape.z = car.z

            RebindTexturesMaybe(shape)
            renderer.Draw(shape)
          }
          
          if(car.drift > t && cIdx == cameraCar){
            sctx.drawImage(driftOverlayImage, 0, 0,
                           driftOverlayImage.width,
                           driftOverlayImage.height)
            var imageData = sctx.getImageData(0,0,
                                              driftOverlayImage.width,
                                              driftOverlayImage.height)
            var data = imageData.data
            if(!(renderer.frameCount%2)){
              for(var i = 0; i < data.length; i += 4){
                var red   = data[i+0]
                var green = data[i+1]
                var blue  = data[i+2]
                var alpha = data[i+3]
                var hsv = Coordinates.HSVFromRGB(red, green, blue)
                var rgb = Coordinates.RGBFromHSV(hsv[0] + t * 800,
                                                     hsv[1], hsv[2])
                red   = (rgb[0] / 64) ** 2 * 64
                green = (rgb[1] / 64) ** 2 * 64
                blue  = (rgb[2] / 64) ** 2 * 64
                data[i+0] = red | 0
                data[i+1] = green | 0
                data[i+2] = blue | 0
                data[i+3] = alpha
              }
            }
            //sctx.putImageData(imageData, 0, 0)
            ctx.putImageData(imageData, 1806, 912)
            //ctx.drawImage(sc, 0, 0, w, h)
          }else{
            if(!tiresScreechingSound.paused){
              //tiresScreechingSound.pause()
            }
          }
        })

        RPGs = RPGs.filter((v, i) => v.life > 0)
        if(!RPGs.filter(v=>v.id == cameraCar).length){
          //if(!alarmSound.paused) alarmSound.pause()
        }
        RPGShape.shapeData.map((subShape, sIdx) => {
          if(sIdx >= RPGs.length){
            subShape.x = 1e5 - subShape.ox
            subShape.y = 1e5 - subShape.oy
            subShape.z = 1e5 - subShape.oz
          }
        })
        RPGs.map((rpg, rIdx) => {
          var vx = 0
          var vy = 0
          var vz = iRPGv
          p = Math.atan2(vy, vz) + rpg.pitch
          d = Math.hypot(vy, vz)
          rpg.vy = S(p) * d
          vz = C(p) * d
          p = Math.atan2(vx, vz) + rpg.yaw
          d = Math.hypot(vx, vz)
          rpg.vx = S(p) * d
          rpg.vz = C(p) * d
          rpg.x += rpg.vx
          rpg.y += rpg.vy
          rpg.z += rpg.vz
          if(Rn() < 1) SpawnSmoke(rpg.x, rpg.y-1, rpg.z)
          var rFloor = Floor(rpg.x + rpg.vx, rpg.z + rpg.vz) - 2
          if(rpg.y <= rFloor){
            SpawnFlash(rpg.x, rpg.y, rpg.z, 5)
            SpawnSparks(rpg.x - rpg.vx, rFloor+2, rpg.z - rpg.vz, 3)
            rpg.life = 0
            if(!muted){
              splode2Sound.currentTime = 0
              splode2Sound.volume = 1 / (1+(1+Math.hypot(rpg.x + renderer.x,
                                   rpg.y + renderer.y,
                                   rpg.z + renderer.z))**2/40000)
              splode2Sound.play()
            }
                                 
          }else{
            if(rIdx < RPGShape.shapeData.length){
              RPGShape.shapeData[rIdx].roll = rpg.roll
              RPGShape.shapeData[rIdx].pitch = rpg.pitch
              RPGShape.shapeData[rIdx].yaw = rpg.yaw
              RPGShape.shapeData[rIdx].x = rpg.x - RPGShape.shapeData[rIdx].ox
              RPGShape.shapeData[rIdx].y = rpg.y - RPGShape.shapeData[rIdx].oy
              RPGShape.shapeData[rIdx].z = rpg.z - RPGShape.shapeData[rIdx].oz
              rpg.life -= .01

              var mind = 6e6, tIdx = -1
              cars.forEach((car, cIdx) => {
                if(cIdx != rpg.id &&
                   (d=Math.hypot(car.x-rpg.x,car.y-rpg.y,car.z-rpg.z)) < mind){
                  mind = d
                  tIdx = cIdx
                }
              })
              if(tIdx != -1){
                if(mind < 3){
                
                  var painVal = .5*missileDamage + Rn() * missileDamage
                  cars[tIdx].painAlpha += painVal / 20
                  cars[tIdx].health -= painVal
                  
                  if(!muted && tIdx == cameraCar){
                    var choice = Rn() * metalSounds.length | 0
                    metalSounds[choice].play()
                    if(!alarmSound.paused){
                      alarmSound.pause()
                    }
                  }
                  
                  cars[tIdx].health = Math.round(cars[tIdx].health)
                  if(cars[tIdx].health <= 0){
                    SpawnSparks(cars[tIdx].x, cars[tIdx].y, cars[tIdx].z, 10)
                    SpawnSparks(cars[tIdx].x, cars[tIdx].y, cars[tIdx].z, 10)
                    SpawnSparks(cars[tIdx].x, cars[tIdx].y, cars[tIdx].z, 10)
                    SpawnFlash(rpg.x, rpg.y, rpg.z, 10)
                    cars[rpg.id].score ++
                    Respawn(cars[tIdx])
                    if(!muted){
                      splode2Sound.currentTime = 0
                      splode2Sound.volume = 1 / (1+(1+Math.hypot(cars[tIdx].x + renderer.x, cars[tIdx].y + renderer.y,
                              cars[tIdx].z + renderer.z))**2/20000)
                      splode2Sound.play()
                      
                      splode2Sound.currentTime = 0
                      splode2Sound.volume = 1 / (1+(1+Math.hypot(rpg.x + renderer.x,
                                           rpg.y + renderer.y,
                                           rpg.z + renderer.z))**2/40000)
                      splode2Sound.play()
                    }
                  }else{
                    SpawnFlash(rpg.x, rpg.y, rpg.z, 5)
                    SpawnSparks(rpg.x, rpg.y, rpg.z, 3, rpg.vx / 6, rpg.vy / 10, rpg.vz / 6)
                    if(!muted){
                      splode2Sound.currentTime = 0
                      splode2Sound.volume = 1 / (1+(1+Math.hypot(rpg.x + renderer.x,
                                           rpg.y + renderer.y,
                                           rpg.z + renderer.z))**2/40000)
                      splode2Sound.play()
                    }
                  }
                  rpg.life = 0
                }else{
                  if(!muted && tIdx == cameraCar){
                    if(alarmSound.paused){
                      alarmSound.currentTime = 0
                      alarmSound.play()
                    }
                  }
                
                  var tgtCar = cars[tIdx]

                  // heat-seeking
                  var tHoming = homing * Math.min(1, (mind / 50))
                  p = Math.atan2(rpg.x-tgtCar.x, rpg.z-tgtCar.z)
                  while(Math.abs(p-rpg.yaw) > Math.PI){
                    p += p < rpg.yaw ? Math.PI * 2 : -Math.PI * 2
                  }
                  d = Math.min(Math.abs(p-rpg.yaw), homing)
                  rpg.yaw += p > rpg.yaw ? -d: d

                  var tgtY = rpg.y - Math.max(0, Math.min(400, mind - 100))
                  p = Math.acos((tgtY-tgtCar.y) /
                                Math.hypot(rpg.x-tgtCar.x,
                                           tgtY-tgtCar.y,
                                           rpg.z-tgtCar.z)) - Math.PI/2
                  d = Math.min(Math.abs(rpg.pitch - p), homing)
                  rpg.pitch += rpg.pitch > p ? -d : d
                }
              }
            }
          }
        })
        RebindTexturesMaybe(RPGShape)
        renderer.Draw(RPGShape)


        flashes = flashes.filter(v => v.life > 0)
        flashes.map((flash, fIdx) => {
          for(var i = 0; i < baseFlash.length; i += 3){
            x = baseFlash[i+0] * flash.life * 40
            y = baseFlash[i+1] * flash.life * 40
            z = baseFlash[i+2] * flash.life * 40
            flashShape.vertices[i+0] = x
            flashShape.vertices[i+1] = y
            flashShape.vertices[i+2] = z
          }
          flashShape.x = flash.x
          flashShape.y = flash.y
          flashShape.z = flash.z
          renderer.Draw(flashShape)
          flash.life -= .3
        })

        shapes.forEach(shape => {
          switch(shape.name){
            case 'background':
              shape.x = -renderer.x
              shape.y = -renderer.y
              shape.z = -renderer.z
              if(rebindTextures){
                shape.map = refTextures[skySel]
                shape.rebindTextures = true
              }
              renderer.Draw(shape)
              break
            case 'floor':
              var car = cars[cameraCar]
              shape.shapeData.map((subShape, sIdx) => {
                while(-car.x + subShape.x + subShape.ox > cl * sp * 1.5){
                  subShape.x -= cl*sp*3
                  for(var i = sIdx*shape.stride; i < (1+sIdx)*shape.stride; i+=3){
                    shape.vertices[i+1] = -1e5
                  }
                }
                while(-car.x + subShape.x + subShape.ox < -cl * sp * 1.5){
                  subShape.x += cl*sp*3
                  for(var i = sIdx*shape.stride; i < (1+sIdx)*shape.stride; i+=3){
                    shape.vertices[i+1] = -1e5
                  }
                }
                while(-car.z + subShape.z + subShape.oz > br * sp * 1.5){
                  subShape.z -= br*sp*3
                  for(var i = sIdx*shape.stride; i < (1+sIdx)*shape.stride; i+=3){
                    shape.vertices[i+1] = -1e5
                  }
                }
                while(-car.z + subShape.z + subShape.oz < -br * sp * 1.5){
                  subShape.z += br*sp*3
                  for(var i = sIdx*shape.stride; i < (1+sIdx)*shape.stride; i+=3){
                    shape.vertices[i+1] = -1e5
                  }
                }
              })
              for(var i = 0; i < shape.vstate.length; i+=3){
                var sIdx = i/shape.stride|0
                x = shape.vstate[i+0] + shape.shapeData[sIdx].x
                z = shape.vstate[i+2] + shape.shapeData[sIdx].z
                y = Floor(x, z)
                //shape.vstate[i+0] = x
                shape.vstate[i+1] = shape.vertices[i+1] = y
                //shape.vstate[i+2] = z
              }
              //if(!(renderer.frameCount%5)){
                Coordinates.SyncNormals(shape, true, false)
              //}
              RebindTexturesMaybe(shape)
              if(rebindTextures){
                shape.map = floorTextures[floorSel]
                shape.rebindTextures = true
                shape.scaleUVX = floorUVX
                shape.scaleUVY = floorUVY
              }
              renderer.Draw(shape)
              break
            case 'hill points':
              for(var i = 0; i < shape.vertices.length; i += 3){
                var car = cars[cameraCar]
                x = shape.vertices[i+0] += S(p=i**3.1/200) * hillMovement
                y = shape.vertices[i+1]
                z = shape.vertices[i+2] += C(p) * hillMovement

                if(!stationaryHillPoints){
                  while(-car.x + x > cl * sp * 1.5){
                    x -= cl*sp*3
                  }
                  while(-car.x + x < -cl * sp * 1.5){
                    x += cl*sp*3
                  }
                  while(-car.z + z > br * sp * 1.5){
                    z -= br*sp*3
                  }
                  while(-car.z + z < -br * sp * 1.5){
                    z += br*sp*3
                  }
                }
                shape.vertices[i+0] = x
                shape.vertices[i+1] = y
                shape.vertices[i+2] = z
              }
              renderer.Draw(shape)
              break
            case 'particles':

              /* note:
                                   bottom 1e3 ( 0   - 600 ) == haze
                                   next   1e3 ( 600 - 4000 ) == smoke
                                   next   1e3 ( 4000 - 5400 ) == sparks

                                  it is faster to combine like-types into a single
                                  draw call, leveraging a custom attribute shader
                                  in this case to set colors by range...

                                */

              smoke  = smoke.filter((v, i) => v.life > 0)
              sparks = sparks.filter((v, i) => v.life > 0)

              var ar = particleShader.datasets[0].optionalAttributes[0].value
              var arIdx, red, green, blue, alpha

              for(var i = 0; i < 600*3; i += 3){
                x = shape.vertices[i+0] += S(p=i**3.1/200) * .05
                y = shape.vertices[i+1] + C(i+t/4) * .04
                z = shape.vertices[i+2] += C(p) * .05
                while(renderer.x + x > cl * sp * 1.5){
                  x -= cl*sp*3
                }
                while(renderer.x + x < -cl * sp * 1.5){
                  x += cl*sp*3
                }
                while(renderer.z + z > br * sp * 1.5){
                  z -= br*sp*3
                }
                while(renderer.z + z < -br * sp * 1.5){
                  z += br*sp*3
                }
                arIdx = i/3*4
                //ar[arIdx+0] = red
                //ar[arIdx+1] = green
                //ar[arIdx+2] = blue
                ar[arIdx+3] = .5 / (1+(1+Math.hypot(renderer.x + x,
                                                  renderer.y + y,
                                                  renderer.z + z))**4/1e7)
                shape.vertices[i+0] = x
                shape.vertices[i+1] = y
                shape.vertices[i+2] = z
              }

              for(var i = 600*3; i < 4000*3; i+=3){
                var j = i/3 - 600
                if(j < smoke.length){
                  arIdx = i/3*4
                  red   = .24
                  green = .2
                  blue  = .15
                  alpha = Math.min(1, 2 * Math.min(1, smoke[j].life) ** 2)
                  ar[arIdx+0] = red
                  ar[arIdx+1] = green
                  ar[arIdx+2] = blue
                  ar[arIdx+3] = alpha
                  shape.vertices[i+0] = smoke[j].x += smoke[j].vx
                  shape.vertices[i+1] = smoke[j].y += smoke[j].vy
                  shape.vertices[i+2] = smoke[j].z += smoke[j].vz
                  smoke[j].life -= .004
                }else{
                  shape.vertices[i+0] = 1e5
                  shape.vertices[i+1] = 1e5
                  shape.vertices[i+2] = 1e5
                }
              }

              for(var i = 4000*3; i < 5400*3; i+=3){
                var j = i/3 - 4000
                if(j < sparks.length){
                  arIdx = i/3*4
                  red   = 1
                  green = .5
                  blue  = 0
                  alpha = .85 * Math.min(1, sparks[j].life) ** 2
                  ar[arIdx+0] = red
                  ar[arIdx+1] = green
                  ar[arIdx+2] = blue
                  ar[arIdx+3] = alpha
                  x = sparks[j].x +sparks[j].vx
                  y = sparks[j].y +sparks[j].vy
                  z = sparks[j].z +sparks[j].vz

                  var sFloor = Floor(x, z)
                  if(y < sFloor){
                    sparks[j].vy *= -.75
                  }
                  y = Math.max(sFloor, y)

                  shape.vertices[i+0] = sparks[j].x += sparks[j].vx
                  shape.vertices[i+1] = sparks[j].y += sparks[j].vy -= grav / 8
                  shape.vertices[i+2] = sparks[j].z += sparks[j].vz
                  sparks[j].life -= .01
                }else{
                  shape.vertices[i+0] = 1e5
                  shape.vertices[i+1] = 1e5
                  shape.vertices[i+2] = 1e5
                }
              }

              renderer.Draw(shape)
              break
            case 'wheel sensors':
              cars.map((car, cIdx) => {
                if(car.hasTraction || car.bounced){
                  for(var i = 0; i < shape.vertices.length; i += 3){
                    var j = i/3
                    x = wheelSensorShape[i+0]
                    z = wheelSensorShape[i+2]
                    p = Math.atan2(x, z) + car.yaw
                    d = Math.hypot(x, z)
                    x = S(p) * d
                    z = C(p) * d
                    shape.vertices[i+0] = x + car.x
                    shape.vertices[i+1] = Floor(x + car.x, z + car.z)
                    shape.vertices[i+2] = z + car.z
                  }
                  var idx = 0 * 3
                  var x1 = shape.vertices[idx+0]
                  var y1 = shape.vertices[idx+1]
                  var z1 = shape.vertices[idx+2]
                  var x2 = shape.vertices[idx+3]
                  var y2 = shape.vertices[idx+4]
                  var z2 = shape.vertices[idx+5]
                  var x3 = shape.vertices[idx+6]
                  var y3 = shape.vertices[idx+7]
                  var z3 = shape.vertices[idx+8]
                  var x4 = shape.vertices[idx+9]
                  var y4 = shape.vertices[idx+10]
                  var z4 = shape.vertices[idx+11]
                  car.roll = -Math.acos((y2-y1) * 1.05 / Math.hypot(x2-x1,y2-y1,z2-z1)) + Math.PI/2
                  car.pitch = Math.acos((y4-y3) * 1.05 / Math.hypot(x4-x3,y4-y3,z4-z3)) - Math.PI/2

                  if(Math.abs(car.pitch) > .1) car.accel -= car.pitch / 75

                  car.bounced = false
                  //renderer.Draw(shape)
                }
              })
              break
            case 'wheels':
              cars.map((car, cIdx) => {
                shape.roll = car.rollv
                shape.pitch = car.pitchv
                shape.yaw = car.yawv
                shape.x = car.x
                shape.z = car.z
                shape.y = car.y

                car.steer = Math.min(1.5, Math.max(-1.5, car.steer))
                car.wheelTurn += car.steer
                car.wheelTurn /= 3.5
                car.steer /= 3.5

                car.steerv += (car.steer - car.steerv) / 5

                car.actualSpeed = Math.hypot(car.vx,car.vz) *
                  (car.speed > 0 ? 1 : -1)
                if(car.hasTraction) car.yaw += car.wheelTurn / 16 * Math.abs(car.actualSpeed*8)**1.6  / (1+Math.abs(car.actualSpeed*8)**2/10) * (car.speed > 0 ? 1 : -1)


                shape.shapeData.map((subShape, sIdx) => {
                  var ox = subShape.ox
                  var oy = subShape.oy - 2.79 * wmag
                  var oz = subShape.oz - .085
                  for(var i = sIdx * shape.stride;
                      i < (sIdx + 1) * shape.stride; i += 3){

                    x = wheelBaseShape.vstate[i + 0] - ox
                    y = wheelBaseShape.vstate[i + 1] - oy
                    z = wheelBaseShape.vstate[i + 2] - oz
                    p = Math.atan2(y, z) - car.distance * 1.5
                    d = Math.hypot(y, z)
                    y = S(p) * d
                    z = C(p) * d
                    if(sIdx < 2){
                      p = Math.atan2(x, z) + car.steerv * (sIdx ? 1.5: -1.5)
                      d = Math.hypot(x, z)
                      x = S(p) * d
                      z = C(p) * d
                    }
                    if(sIdx == 0 || sIdx == 3) x *= -1
                    shape.vstate[i + 0] = x + ox
                    shape.vstate[i + 1] = y + oy
                    shape.vstate[i + 2] = z + oz + .085

                    x = wheelBaseShape.vertices[i + 0] - ox
                    y = wheelBaseShape.vertices[i + 1] - oy
                    z = wheelBaseShape.vertices[i + 2] - oz
                    p = Math.atan2(y, z) - car.distance * 1.5
                    d = Math.hypot(y, z)
                    y = S(p) * d
                    z = C(p) * d
                    if(sIdx < 2){
                      p = Math.atan2(x, z) + car.steerv * (sIdx ? 1.5: -1.5)
                      d = Math.hypot(x, z)
                      x = S(p) * d
                      z = C(p) * d
                    }
                    if(sIdx == 0 || sIdx == 3) x *= -1
                    shape.vertices[i + 0] = x + ox
                    shape.vertices[i + 1] = y + oy
                    shape.vertices[i + 2] = z + oz + .085

                    x = wheelBaseShape.nvstate[i + 0]
                    y = wheelBaseShape.nvstate[i + 1]
                    z = wheelBaseShape.nvstate[i + 2]
                    p = Math.atan2(y, z) - car.distance * (sIdx == 0 || sIdx == 3 ? -1.5 : 1.5)
                    d = Math.hypot(y, z)
                    y = S(p) * d
                    z = C(p) * d
                    if(sIdx < 2){
                      p = Math.atan2(x, z) + car.steer
                      d = Math.hypot(x, z)
                      x = S(p) * d
                      z = C(p) * d
                    }
                    if(sIdx == 0 || sIdx == 3) x *= -1, y *= -1
                    shape.nvstate[i + 0] = x
                    shape.nvstate[i + 1] = y
                    shape.nvstate[i + 2] = z

                    x = wheelBaseShape.normalVecs[i + 0]
                    y = wheelBaseShape.normalVecs[i + 1]
                    z = wheelBaseShape.normalVecs[i + 2]
                    p = Math.atan2(y, z) - car.distance * (sIdx == 0 || sIdx == 3 ? -1.5 : 1.5)
                    d = Math.hypot(y, z)
                    y = S(p) * d
                    z = C(p) * d
                    if(sIdx < 2){
                      p = Math.atan2(x, z) + car.steer
                      d = Math.hypot(x, z)
                      x = S(p) * d
                      z = C(p) * d
                    }
                    if(sIdx == 0 || sIdx == 3) x *= -1, y *= -1
                    shape.normalVecs[i + 0] = x
                    shape.normalVecs[i + 1] = y
                    shape.normalVecs[i + 2] = z
                  }
                })
                RebindTexturesMaybe(shape)
                renderer.Draw(shape)
              })
            break
            case 'exhaust flair':
              cars.map((car, cIdx) => {
                var fireLeft = Rn() < (car.hasOverdrive ? .8 : .2)
                var fireRight = Rn() < (car.hasOverdrive ? .8 : .2)
                if(car.keys[38] && (fireLeft || fireRight)){
                  for(var i = 0; i < flairShape.length; i +=3 ){
                    if(flairShape[i+0] < 0){
                      var fMag = 1.5 + C(renderer.t * 50 + Math.PI/2) / 1.5
                      z = flairShape[i+2] * fMag + (fireLeft ? 0 : 1e5)
                      shape.vertices[i+2] = z - (1-fMag)*1.59 - .2
                    }else{
                      var fMag = 1.5 + C(renderer.t * 50) / 1.5
                      z = flairShape[i+2] * fMag + (fireRight ? 0 : 1e5)
                      shape.vertices[i+2] = z - (1-fMag)*1.59 - .2
                    }
                  }
                  shape.x = car.x
                  shape.y = car.y
                  shape.z = car.z
                  shape.roll = car.rollv
                  shape.pitch = car.pitchv
                  shape.yaw= car.yawv
                  renderer.Draw(shape)
                }
              })
              break
            default:
              //renderer.Draw(shape)
              break
          }
        })

        rebindTextures = false
        if(renderer.loaded) loaded = true
        HandleButtons()
        
        //if(!(renderer.frameCount%60)){
          renderer.showCrosshair = cars[cameraCar].cameraMode == 'orbit' ||
                                   !showHUD
        //}
      }



      //////////////// net
      
      const alphaToDec = val => {
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

      var regFrame = document.querySelector('#regFrame')
      var launchModal = document.querySelector('#launchModal')
      var launchStatus = document.querySelector('#launchStatus')
      var gameLink = document.querySelector('#gameLink')

      const launch = () => {
        let none = false
        if(0){//&&(none = typeof users == 'undefined') || users.length<2){
          alert("this game requires at least one other player to join!\n\nCurrent users joined: " + (none ? 0 : users.length))
          return
        }
        launchModal.style.display = 'none'
        launched = true
        //Draw()
      }

      var doJoined = jid => {
        regFrame.style.display = 'none'
        regFrame.src = ''
        userID = +jid
        sync()
      }

      var fullSync = false
      var individualPlayerData = {}
      const syncPlayerData = users => {
        
        /* battleracer2 stuff */
        
        if(typeof cars != 'undefined' && cars.length){
          let keep = []
          users.map(v=>{
            cars.map((car, idx) => {
              if(+v.id == car.id) keep=[...keep, idx]
            })
          })
          cars = cars.filter((car, idx) => {
            let ret = !!keep.filter(v=>v==idx).length
            if(!ret){
              camSelected = 0
              spawnFlashNotice(car.name + ' has left the arena...', '#00f')
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
              //if(typeof score != 'undefined') {
              //  AI.score = score
              //  AI.playerData.score = score
              //  individualPlayerData['score'] = score
              //}
              
              if(typeof cars == 'object' && cars.length) {
                let car = cars[0]
                let sendCar = {
                  X: car.X,
                  Z: car.Z,
                  //rl: car.rl,
                  //pt: car.pt,
                  yw: car.yw,
                  //rlv: car.rlv,
                  //ptv: car.ptv,
                  ywv: car.ywv,
                  vx: car.vx,
                  vz: car.vz,
                  id: userID,
                  drift: car.drift,
                  health: car.health,
                  curGun: car.curGun,
                  camMode: car.camMode,
                  shooting: car.shooting,
                  poweredUp: car.poweredUp,
                  playerName: car.playerName,
                }
                individualPlayerData['car'] = sendCar
                //console.log('sendCar: ', sendCar)
              }

              if(typeof powerups == 'object' && powerups.length){
                individualPlayerData['powerups'] = powerups
              }

              if(typeof scores == 'object' && scores.length){
                individualPlayerData['scores'] = scores
              }

              //if(typeof score1 != 'undefined') individualPlayerData['score1'] = score1
              //if(typeof score2 != 'undefined') individualPlayerData['score2'] = score2
              //if(typeof totalPcs1 != 'undefined') individualPlayerData['totalPcs1'] = totalPcs1
              //if(typeof totalPcs2 != 'undefined') individualPlayerData['totalPcs2'] = totalPcs2

              //if(typeof B1alive != 'undefined') individualPlayerData['B1alive'] = B1alive
              //if(typeof gameInPlay != 'undefined') individualPlayerData['gameInPlay'] = gameInPlay
              
              //if(typeof moves != 'undefined') individualPlayerData['moves'] = moves
              //if(typeof lastWinnerWasOp != 'undefined' && lastWinnerWasOp != -1) individualPlayerData['lastWinnerWasOp'] = lastWinnerWasOp
            }else{
              if(AI.playerData?.id){
                el = users.filter(v=>+v.id == +AI.playerData.id)[0]
                Object.entries(AI).forEach(([key,val]) => {
                  switch(key){
                    // straight mapping of incoming data <-> players
                    case 'car':
                      if(typeof el[key] != 'undefined' && +el[key].id != +userID){
                        let incomingCar = el[key]
                        if(cars.filter(v=>+v.id == +el[key].id).length){
                          let matchingCar = cars.filter(v=>+v.id == +el[key].id)[0]
                          let id = el[key].id
                          Object.entries(incomingCar).forEach(([key2, val2]) => {
                            omit = false
                            switch(key2){
                              case 'Y': omit = true; break
                              case 'vy': omit = true; break
                              case 'pt': omit = true; break;
                              case 'rl': omit = true; break
                            }
                            if(!omit) {
                              if(key2 == 'score'){
                                if(+matchingCar[key2] < +val2) matchingCar[key2] = val2
                              }else{
                                switch(key2){
                                  case 'health':
                                    matchingCar.health = +val2
                                  break
                                  case 'X':
                                    matchingCar.lerpToX = val2
                                  break
                                  case 'Z':
                                    matchingCar.lerpToZ = val2
                                  break
                                  case 'yw':
                                    matchingCar.lerpToYw = val2
                                  break
                                  case 'ywv':
                                    matchingCar.lerpToYwv = val2
                                  break
                                  case 'vx':
                                    matchingCar.lerpToVx = val2
                                  break
                                  case 'vz':
                                    matchingCar.lerpToVz = val2
                                  break
                                  default:
                                    if(+matchingCar.id != +userID) matchingCar[key2] = val2
                                  break
                                }
                              }
                            }
                          })
                        }
                      }
                    break;
                    case 'powerups':
                      if(typeof el[key] != 'undefined'){
                        el[key].map(powerup => {
                          X = powerup[0]
                          Z = powerup[2]
                          let newPowerup = true
                          powerups.map(pup => {
                            if(Math.hypot(pup[0]-X,pup[2]-Z)<.1 || pup[7] < .5)  newPowerup = false
                          })
                          if(newPowerup){
                            powerups = [...powerups, JSON.parse(JSON.stringify(powerup))]
                          }
                        })
                      }
                    break;
                    case 'scores':
                      if(typeof el[key] != 'undefined'){
                        el[key].map(score=>{
                          cars.map(car=>{
                            if(+car.id == +score.id && score.score > car.score) car.score = score.score
                          })
                        })
                      }
                      break
                    /*case 'P1':
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
                    }*/
                    
                    //case 'lastWinnerWasOp': if(typeof el[key] != 'undefined' && el[key] != -1) lastWinnerWasOp = el[key]; break;
                    //case 'score':
                    //  if(typeof el[key] != 'undefined'){
                    //    AI[key] = +el[key]
                    //    AI.playerData[key] = +el[key]
                    //  }
                    //break;
                  }
                })
              }
            }
          })
          for(i=0;i<Players.length;i++) if(Players[i]?.playerData?.id == userID) ofidx = i
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
        var launchButton = document.createElement('button')
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

      const copy = () => {
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
      
      var userID = false
      var launched = false 
      var pchoice = false
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

      
      ////////////////

    </script>
  </body>
</html>