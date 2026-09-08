<template>
  <div class="flag-icon-box" :style="{ width: size + 'px', height: size + 'px' }">
    <!-- Indonesia Flag (Merah Putih) -->
    <svg 
      v-if="normalizedCode === 'id'" 
      viewBox="0 0 32 32" 
      class="flag-svg"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <clipPath :id="'clip-id-' + uid">
          <circle cx="16" cy="16" r="16" />
        </clipPath>
      </defs>
      <g :clip-path="'url(#clip-id-' + uid + ')'">
        <rect width="32" height="16" fill="#DC2626" />
        <rect y="16" width="32" height="16" fill="#FFFFFF" />
        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(0, 0, 0, 0.15)" stroke-width="1" />
      </g>
    </svg>

    <!-- UK Flag (Union Jack) -->
    <svg 
      v-else-if="normalizedCode === 'en' || normalizedCode === 'gb'" 
      viewBox="0 0 32 32" 
      class="flag-svg"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <clipPath :id="'clip-gb-' + uid">
          <circle cx="16" cy="16" r="16" />
        </clipPath>
      </defs>
      <g :clip-path="'url(#clip-gb-' + uid + ')'">
        <rect width="32" height="32" fill="#012169" />
        <path d="M0 0 L32 32 M32 0 L0 32" stroke="#FFFFFF" stroke-width="5" />
        <path d="M0 0 L32 32 M32 0 L0 32" stroke="#C8102E" stroke-width="3" />
        <path d="M16 0 V32 M0 16 H32" stroke="#FFFFFF" stroke-width="8" />
        <path d="M16 0 V32 M0 16 H32" stroke="#C8102E" stroke-width="4.8" />
        <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(0, 0, 0, 0.15)" stroke-width="1" />
      </g>
    </svg>
  </div>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'

const props = defineProps({
  code: {
    type: String,
    default: 'id'
  },
  size: {
    type: [Number, String],
    default: 18
  }
})

const normalizedCode = computed(() => (props.code || 'id').toLowerCase().trim())
const uid = getCurrentInstance()?.uid || Math.floor(Math.random() * 100000)
</script>

<style scoped>
.flag-icon-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  line-height: 0;
}

.flag-svg {
  width: 100%;
  height: 100%;
  display: block;
  border-radius: 50%;
  }
</style>
