<template>
  <figure class="hero-photo">
    <img
      src="/assets/icons/hero/hero.png"
      :alt="alt"
      width="987"
      height="987"
      fetchpriority="high"
      decoding="async"
    />
  </figure>
</template>

<script setup>
defineProps({ alt: { type: String, default: 'Smart Dryer Greenhouse Hanjeli' } });
</script>

<style scoped>
.hero-photo {
  width: min(100%, 520px);
  margin: 0;
}
.hero-photo img {
  display: block;
  width: 100%;
  height: auto;
  object-fit: contain;
}
</style>
