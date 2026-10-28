<template>
  <aside v-if="pwaState.updateAvailable" class="pwa-update" role="status" aria-live="polite">
    <p>Versi baru tersedia. Simpan perubahan Anda sebelum memperbarui.</p>
    <button type="button" @click="reloadForUpdate">Perbarui</button>
  </aside>
</template>

<script setup>
import { pwaState, reloadForUpdate } from '../services/pwaService';
</script>

<style scoped>
.pwa-update {
  position: fixed;
  z-index: 10000;
  bottom: max(5rem, env(safe-area-inset-bottom));
  left: 50%;
  transform: translateX(-50%);
  box-sizing: border-box;
  width: min(92vw, 34rem);
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-radius: 1rem;
  background: #153824;
  color: #fff;
  box-shadow: 0 .5rem 2rem #0003;
}
.pwa-update p { margin: 0; font-size: .875rem; }
.pwa-update button { flex: none; padding: .6rem .9rem; border: 0; border-radius: .5rem; background: #e8f5e9; color: #153824; font: inherit; font-weight: 700; cursor: pointer; }
</style>
