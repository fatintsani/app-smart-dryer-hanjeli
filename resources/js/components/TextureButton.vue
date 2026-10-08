<template>
  <button
    :type="type"
    :disabled="disabled"
    class="texture-btn-outer"
    :class="[
      `variant-${variant}`,
      `size-${size}`,
      { 'is-disabled': disabled },
      customClass
    ]"
    v-bind="$attrs"
  >
    <span class="texture-btn-inner" :class="[`inner-variant-${variant}`, `inner-size-${size}`]">
      <slot />
    </span>
  </button>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'accent', 'secondary', 'destructive', 'minimal', 'icon'].includes(v)
  },
  size: {
    type: String,
    default: 'default',
    validator: (s) => ['sm', 'default', 'lg', 'icon'].includes(s)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: [String, Object, Array],
    default: ''
  }
})
</script>

<style scoped>
.texture-btn-outer {
  position: relative;
  display: inline-flex;
  padding: 1.5px;
  background-clip: padding-box;
  border-radius: 12px;
  border: 1px solid rgba(13, 99, 27, 0.2);
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  box-sizing: border-box;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  text-decoration: none;
  user-select: none;
  overflow: hidden;
}

/* Outer Variants */
.texture-btn-outer.variant-primary {
  border-color: rgba(13, 99, 27, 0.35);
  background: linear-gradient(180deg, rgba(203, 255, 194, 0.6) 0%, rgba(13, 99, 27, 0.4) 40%, rgba(9, 71, 19, 0.95) 100%);
  box-shadow: 0 3px 10px -2px rgba(13, 99, 27, 0.35), 0 1px 3px rgba(0, 0, 0, 0.08);
}

.texture-btn-outer.variant-secondary {
  border-color: rgba(203, 213, 225, 0.8);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(226, 232, 240, 0.7) 100%);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.texture-btn-outer.variant-accent {
  border-color: rgba(99, 102, 241, 0.4);
  background: linear-gradient(180deg, rgba(165, 180, 252, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
  box-shadow: 0 3px 10px -2px rgba(79, 70, 229, 0.35);
}

.texture-btn-outer.variant-destructive {
  border-color: rgba(220, 38, 38, 0.4);
  background: linear-gradient(180deg, rgba(252, 165, 165, 0.8) 0%, rgba(185, 28, 28, 0.9) 100%);
  box-shadow: 0 3px 10px -2px rgba(220, 38, 38, 0.35);
}

.texture-btn-outer.variant-minimal {
  border-color: rgba(203, 213, 225, 0.6);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.8) 0%, rgba(241, 245, 249, 0.5) 100%);
}

.texture-btn-outer.variant-icon {
  border-radius: 9999px;
  border-color: rgba(203, 213, 225, 0.8);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(226, 232, 240, 0.7) 100%);
}

/* Outer Sizes */
.texture-btn-outer.size-sm {
  border-radius: 8px;
}

.texture-btn-outer.size-default {
  border-radius: 12px;
}

.texture-btn-outer.size-lg {
  border-radius: 14px;
}

.texture-btn-outer.size-icon {
  border-radius: 9999px;
}

/* Inner Wrapper */
.texture-btn-inner {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  height: 100%;
  font-weight: 700;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  box-sizing: border-box;
}

/* Inner Variants */
.inner-variant-primary {
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3);
}

.texture-btn-outer.variant-primary:hover .inner-variant-primary {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.25);
}

.texture-btn-outer.variant-primary:active .inner-variant-primary {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4);
}

.inner-variant-secondary {
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  color: #1E293B;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 1), inset 0 -1px 0 rgba(203, 213, 225, 0.5);
}

.texture-btn-outer.variant-secondary:hover .inner-variant-secondary {
  background: linear-gradient(180deg, #FFFFFF 0%, #E2E8F0 100%);
  color: #0D631B;
}

.inner-variant-accent {
  background: linear-gradient(180deg, #6366F1 0%, #4F46E5 100%);
  color: #FFFFFF;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3);
}

.inner-variant-destructive {
  background: linear-gradient(180deg, #EF4444 0%, #DC2626 100%);
  color: #FFFFFF;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3);
}

.inner-variant-minimal {
  background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
  color: #334155;
}

.inner-variant-icon {
  border-radius: 9999px;
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  color: #1E293B;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 1);
}

/* Inner Sizes */
.inner-size-sm {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 6.5px;
}

.inner-size-default {
  font-size: 14px;
  padding: 9px 18px;
  border-radius: 10.5px;
}

.inner-size-lg {
  font-size: 15.5px;
  padding: 12px 24px;
  border-radius: 12.5px;
}

.inner-size-icon {
  padding: 8px;
  border-radius: 9999px;
}

/* Hover & Active Transformations */
.texture-btn-outer:hover {
  transform: translateY(-1.5px);
}

.texture-btn-outer:active {
  transform: scale(0.98);
}

.texture-btn-outer.is-disabled {
  opacity: 0.55;
  cursor: not-allowed;
  pointer-events: none;
  transform: none;
}
</style>
