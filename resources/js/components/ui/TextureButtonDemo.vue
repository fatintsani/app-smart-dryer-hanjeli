<template>
  <div class="texture-demo-wrapper">
    <div class="demo-box">
      <!-- Primary -->
      <div class="demo-row">
        <TextureButton size="sm">Primary</TextureButton>
        <TextureButton size="default">Primary</TextureButton>
        <TextureButton size="lg">Primary Large</TextureButton>
      </div>

      <!-- Accent -->
      <div class="demo-row">
        <TextureButton variant="accent" size="sm">Accent</TextureButton>
        <TextureButton variant="accent" size="default">Accent</TextureButton>
        <TextureButton variant="accent" size="lg">Accent Large</TextureButton>
      </div>

      <!-- Secondary -->
      <div class="demo-row">
        <TextureButton variant="secondary" size="sm">Secondary</TextureButton>
        <TextureButton variant="secondary" size="default">Secondary</TextureButton>
        <TextureButton variant="secondary" size="lg">Secondary Large</TextureButton>
      </div>

      <!-- Destructive -->
      <div class="demo-row">
        <TextureButton variant="destructive" size="sm">Destructive</TextureButton>
        <TextureButton variant="destructive" size="default">Destructive</TextureButton>
        <TextureButton variant="destructive" size="lg">Destructive Large</TextureButton>
      </div>

      <!-- Minimal -->
      <div class="demo-row">
        <TextureButton variant="minimal" size="sm">Minimal</TextureButton>
        <TextureButton variant="minimal" size="default">Minimal</TextureButton>
        <TextureButton variant="minimal" size="lg">Minimal Large</TextureButton>
      </div>

      <!-- Icons -->
      <div class="demo-row">
        <TextureButton variant="icon" size="icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </TextureButton>
        <TextureButton variant="icon" size="icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </TextureButton>
        <TextureButton variant="icon" size="icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </TextureButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import TextureButton from './TextureButton.vue'
</script>

<style scoped>
.texture-demo-wrapper {
  padding: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #0B1911;
  border-radius: 16px;
  margin: 16px 0;
}

.demo-box {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 540px;
  width: 100%;
}

.demo-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
</style>
