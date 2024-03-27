<template>
  <div class="footer">
    <span class="title" v-html="state.footerMsg"></span>
  </div>
</template>

<script>
export default {
  name: 'Footer',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods: {
  },
  mounted(){
  }
}
</script>

<style scoped>
  .footer{
    height: 70px;
    background: linear-gradient(90deg, #088a, #206a, #000a);
    color: #fff;
    min-width:600px;
    width: 100vw;
    left: 50%;
    transform: translatex(-50%);
    font-size: 20px;
    text-align: center;
    position: fixed;
    bottom: 0;
  }
</style>
