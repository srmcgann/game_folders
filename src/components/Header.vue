<template>
  <div class="header">
    <div class="headerImg"></div>
  </div>
</template>

<script>
export default {
  name: 'Header',
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
  .header{
    height: 52px;
    background: linear-gradient(90deg, #000a, #206a, #088a);
    color: #4fa;
    font-size: 20px;
  }
  .headerImg{
    height: 52px;
    width: 100%;
    background-position: 0 50%;
    background-image: url(../assets/header.png);
    background-repeat: no-repeat;
    background-size: auto 52px;
  }
</style>
