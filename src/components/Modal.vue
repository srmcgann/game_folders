<template>
  <div class="modal">
    <button @click="close()" class="cancelButton">
      close/cancel
    </button>
    <div class="modalInner" v-html="content"></div>
  </div>
</template>

<script>
export default {
  name: 'Modal',
  props: [ 'state', 'content' ],
  methods: {
    close(){
      this.state.showModal = false
    }
  },
  mounted(){
  }
}
</script>

<style scoped>
  .modal{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 100vh;
  }
  .cancelButton{
    background: #822;
    color: #f88;
    width: 200px;
    font-family: Courier Prime;
    font-size: 16px;
    border: none;
    border-radius: 10px;
    padding: 5px;
    position: absolute;
    z-index: 1100;
    left: 10px;
    top: 10px;
  }
  .modalInner{
    text-align: center;
    padding: 25px;
    width: 100%;
    height: 100%;
    font-size: 25px;
    color: #fff;
    text-shadow: 2px 2px 2px #000;
    background: #000d;
  }
</style>
