<!--
todo
  ✔ dropable files
  ✔ fix video & audio thumbnails
  ✔ file hash unifies same-upload sources
  ✔ fix loading animations
  *  display errors
  * users, optional logins/profiles
    └-> * comments
        * votes
        * collections
        * login button
        * profile page
        
  * youtube field
  * admin panel
  * optional autodelete
  * tile-able cards (flex)
  * lightbox sim / previews
  * log & display asset origin
  * URL uploads
  * log original file name
  * social media metadata
  * gallery / trending
  * load/resource balancing
  
-->

<template>
  <a :href="link.href" target="_blank" class="link" ref="anchor">
    <div class="linkThumb" ref="linkThumb" @click.prevent.stop></div>
    #{{link.ct+1}}<br>
    href: {{link.href}}<br>
    type: {{link.type}}<br>
    size: {{link.size.toLocaleString('en-us')}}<br>
  </a>
  <div style="clear: both;"></div>
</template>

<script>

export default {
  name: 'Link',
  props: [ 'state', 'link' ],
  data(){
    return {
      c: document.createElement('canvas'),
      x: null,
      linkType: '',
      img: null,
      w: 0,
      h: 0
    }
  },
  methods: {
    Draw(){
      this.x.globalAlpha = 1
      this.x.fillStyle='#0008'
      this.x.fillRect(0,0,this.c.width,this.c.height)
      let fillStyle = 'contain'
      let scl
      switch(fillStyle){
        case 'contain':
          scl = this.c.width/this.c.height <= 1.777777778 ? this.c.width/this.w : this.c.height/this.h
          break
        case 'cover':
          scl = this.c.width/this.c.height > 1.777777778 ? this.c.width/this.w : this.c.height/this.h
          break
      }
      let w = this.w * scl
      let h = this.h * scl
      this.x.drawImage(this.img,this.c.width/2-w/2,this.c.height/2-h/2,w,h)
      if(this.linkType == 'video') requestAnimationFrame(this.Draw)
    }
  },
  mounted(){
    this.$refs.linkThumb.appendChild(this.c)
    this.x = this.c.getContext('2d')
    switch(this.link.type.split('/')[0]){
      case 'image': this.linkType = 'image'; break
      case 'audio': this.linkType = 'audio'; break
      case 'video': this.linkType = 'video'; break
    }
    this.c.width = 500
    this.c.height = 500/1.77777778
    this.c.style.width = '150px'
    this.c.style.height = '85px'
    if(this.linkType == 'video'){
      this.img = document.createElement('video')
      this.img.loop = true
      this.img.muted = true
      this.img.oncanplay = () => {
        this.w = this.img.videoWidth
        this.h = this.img.videoHeight
        this.img.play()
        this.Draw()
      }
      this.img.src = this.link.href
    }
    if(this.linkType == 'image' || this.linkType == 'audio'){
      this.img = new Image()
      this.img.onload = () => {
        this.w = this.img.width
        this.h = this.img.height
        this.Draw()
      }
      let l
      this.img.src = this.linkType == 'audio' ? 'musicNote.png' : 'thumb.php?res=uploads/' + (l=this.link.href.split('/'))[l.length-1].split('?')[0]
    }
  }
}
</script>

<style scoped>
  .link:hover{
    background-color: #408a;
  }
  .link{
    display: table;
    width: 100%;
    color: #acd;
    background-color: #204a;
    font-size: 20px;
    height: 50px;
    min-width: 400px;
    margin: 20px;
    box-sizing: border-box;
    text-align: left;
    margin: 10px;
    word-break: break-word;
  }
  .linkThumb{
    float: left;
    width: 150px;
    height: 85px;
    margin: 5px;
    background-size: contain;
    background-position: 20px 20px;
    background-repeat: no-repeat;
    background-color: #000;
  }
</style>

