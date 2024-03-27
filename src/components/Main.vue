<template>
  <div class="main" ref="main">
    <div id="dropTarget" class="dropTarget" @dragover.prevent @drop.prevent="dropFiles($event)" @click="loadFiles()">
      throw sum filez [drag/click]<br><br>
      accepted: gif, web[p/m], png, jp[e]g, mp4, mp3<br>
      max size: 25MB<br>
      WARRANTY: none [this is a work-in-progress. your files will likely be deleted]<br>
      <div v-if="state.links.length" class="links">
        links<br>
        <Link :state="state" v-for="link in state.links" :link="link" />
      </div>
    </div>
  </div>
</template>

<script>
import Link from './Link'

export default {
  name: 'Main',
  props: [ 'state' ],
  components: {
    Link
  },
  data(){
    return {
    }
  },
  methods: {
    uploadFiles(fd){
      fetch('upload.php', {
        method: "POST", body: fd
      }).then(res=>res.json()).then(data=>{
        console.log('response from upload.php: ', data)
        if(data[0]){
          data[1].map((v, i)=>{
            this.addLink(data[2][i], data[3][i], i, location.href.split('?')[0] + v)
          })
        }
        this.state.uploadInprogress = false
        this.state.modalContent = ''
        this.state.showModal = false
      })
    },
    processUpload(files){
      this.state.uploadInprogress = true
      this.state.modalContent = `<video src="loading.mp4?2" style="min-width:60vw; min-height: 60vh; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); pointer-events: none; opacity: .5;" loop autoplay muted></video>`
      this.state.showModal = true
      
      this.$nextTick(()=>{
        let ct = 0
        let fd = new FormData()
        fd.append('description', 'no description')
        files.map((file, i) => {
          ct++
          console.log(`file ${i}: `, file)
          fd.append(`uploads_${i}`, file)
        })
        if(ct) this.uploadFiles(fd)
      })
    },
    dropFiles(e){
      let files = []
      if(e.dataTransfer.items){
        [...e.dataTransfer.items].forEach((item, i) => {
          if (item.kind === "file") {
            const file = item.getAsFile()
            files = [...files, file]
          }
        })
      }else{
        files = Array.from(e.dataTransfer.files)
      }
      if(files.length) this.processUpload(files)
    },
    addLink(size, type, ct, href){
      let obj = {
        size,
        type,
        ct,
        href,
      }
      this.state.links.push(obj)
    },
    loadFiles(){
      if(this.state.links.length) return
      let files = document.createElement('input')
      files.type = 'file'
      files.name = 'uploads[]'
      files.multiple = true
      files.accept = 'image/gif, image/jiff, image/jpeg, image/jpg, image/png, image/webp, video/mp4, video/webm, video/mkv, audio/mp3, audio/wav, audio/mpeg'
      files.onchange = () => this.processUpload(Array.from(files.files))
      files.click()
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
  .main{
    background-color: #201a;
    font-size: 20px;
    overflow-y: auto;
    overflow-x: hidden;
    height: calc(100vh - 122px);
    min-height: calc(100vh - 122px);
    padding: 20px;
    box-sizing: border-box;
    text-align: center;
  }
  .dropTarget:hover{
    background-color: #0648;
    cursor: pointer;
  }
  .dropTarget{
    color: #0ff;
    display: inline-block;
    padding: 20px;
    text-align: left;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 100% 100%;
    border-radius: 10px;
    /*background-image: url(../assets/loading.gif);*/
    border: 1px solid #4088;
    background-color: #0468;
    width: 100%;
    min-height: 100%;
    box-sizing: border-box;
  }
  .links{
    margin: 10px;
    box-sizing: border-box;
    background: #3338;
  }
</style>

