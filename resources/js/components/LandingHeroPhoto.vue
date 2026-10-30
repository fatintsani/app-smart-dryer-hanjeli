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

@media (max-width: 1024px) {
  .hero-photo {
    width: min(100%, 380px);
    margin: 0 auto;
  }
}

@media (max-width: 768px) {
  .hero-photo {
    width: min(100%, 330px);
    max-width: 330px;
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .hero-photo {
    width: min(100%, 290px);
    max-width: 290px;
    margin: 0 auto;
  }
}
</style>
