<template>
  <div class="stas-logo-wrapper" :style="{ width: size ? (typeof size === 'number' ? size + 'px' : size) : 'auto', height: size ? (typeof size === 'number' ? size + 'px' : size) : 'auto' }">
    <img 
      src="/assets/img/stas.png" 
      alt="Logo STAS - Pembuat Aplikasi & Hardware" 
      class="stas-logo-img"
      loading="eager"
    />
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: [Number, String],
    default: 48
  }
})
</script>

<style scoped>
.stas-logo-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stas-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
</style>
