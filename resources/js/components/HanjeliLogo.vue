<template>
  <div class="hanjeli-logo-wrapper" :style="{ width: size + 'px', height: size + 'px' }">
    <img 
      src="/assets/img/hanjeli.png" 
      alt="Logo Desa Wisata Hanjeli" 
      class="hanjeli-logo-img"
      loading="eager"
    />
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: [Number, String],
    default: 64
  }
})
</script>

<style scoped>
.hanjeli-logo-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.hanjeli-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
</style>
