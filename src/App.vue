<template>
  <div>
    <Header :state="state" />
    <Main   :state="state" />
    <Footer :state="state" />
    <Modal
      :state="state"
      v-if="state.showModal"
      :content="state.modalContent"
    />
  </div>
</template>

<script>
import Header from './components/Header'
import Main from './components/Main'
import Footer from './components/Footer'
import Modal from './components/Modal'

export default {
  name: 'App',
  components: {
    Header,
    Main,
    Footer,
    Modal,
  },
  data(){
    return {
      state: {
        footerMsg: '<b>IMJUR</b><br>A FREE DIGITAL ASSET HOSTING SERVICE - ©'+(new Date()).getFullYear() + `<br>contact: <a href="mailto:whitehotrobot@gmail.com">whitehotrobot@gmail.com</a>`,
        links: [],
        uploadInProgress: false,
        showModal: false,
        modalContent: ''
      }
    }
  },
  watch: {
    'state.uploadInProgress' (val) {
      /*console.log('state.uploadInProgress val', val)
      if(val){
        this.state.modalContent = `
          loading...
        `
        this.state.showModal = true
      }else{
        this.state.modalContent = ''
        this.state.showModal = false
      }
      */
    }
  }
}
</script>

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
  background-color: #000;
  background-image: url(./assets/bg.jpg);
  background-repeat: repeat;
  background-size: cover;
  background-position: center center;
  margin: 0;
  height: 100vh;
  overflow-x: hidden;
  font-family: Courier Prime;
}
a:visited{
  color: #40f;
}
a{
  text-decoration: none;
  color: #08f;
}
</style>
