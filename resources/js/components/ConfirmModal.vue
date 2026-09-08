<template>
  <Teleport to="body">
    <Transition name="confirm-modal-fade">
      <div 
        v-if="dialogState.isOpen" 
        class="confirm-overlay" 
        @click.self="handleCancel"
        tabindex="-1"
        @keydown.esc="handleCancel"
        @keydown.enter="handleConfirm"
      >
        <div 
          class="confirm-card" 
          :class="[`type-${dialogState.type}`]" 
          role="alertdialog" 
          aria-modal="true"
        >
          <!-- Top Icon Circle with Pulse Effect -->
          <div class="confirm-icon-wrapper" :class="`icon-${dialogState.type}`">
            <!-- Danger / Delete / Warning Icon -->
            <svg v-if="dialogState.type === 'danger'" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 6h18"></path>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              <line x1="10" y1="11" x2="10" y2="17"></line>
              <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>

            <!-- Warning Triangle Icon -->
            <svg v-else-if="dialogState.type === 'warning'" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>

            <!-- Success Checkmark Icon -->
            <svg v-else-if="dialogState.type === 'success'" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>

            <!-- Info Icon -->
            <svg v-else width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>

          <!-- Dialog Body Content -->
          <div class="confirm-body">
            <h3 class="confirm-title">{{ dialogState.title }}</h3>
            <p class="confirm-message">{{ dialogState.message }}</p>

            <!-- Optional Details / Context Snippet -->
            <div v-if="dialogState.details" class="confirm-details-box">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>{{ dialogState.details }}</span>
            </div>
          </div>

          <!-- Dialog Action Buttons -->
          <div class="confirm-actions">
            <button 
              v-if="dialogState.showCancel"
              type="button" 
              class="btn-dialog btn-dialog-cancel" 
              @click="handleCancel"
            >
              {{ dialogState.cancelText }}
            </button>
            <button 
              type="button" 
              class="btn-dialog btn-dialog-confirm" 
              :class="`btn-${dialogState.type}`"
              @click="handleConfirm"
            >
              <span>{{ dialogState.confirmText }}</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { dialogState, closeDialog } from '../services/confirmDialogService'

function handleConfirm() {
  closeDialog(true)
}

function handleCancel() {
  closeDialog(false)
}
</script>

<style scoped>
/* Backdrop overlay */
.confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 99999;
  background-color: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.25rem;
  outline: none;
}

/* Modal Card */
.confirm-card {
  width: 100%;
  max-width: 440px;
  background: #ffffff;
  border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
  padding: 1.75rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Dark Theme Support */
:global(.dark-theme) .confirm-card {
  background: #1e293b;
  border-color: rgba(51, 65, 85, 0.8);
  }

/* Icon Wrapper */
.confirm-icon-wrapper {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
  position: relative;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

.confirm-card:hover .confirm-icon-wrapper {
  transform: scale(1.06);
}

.icon-danger {
  background: rgba(239, 68, 68, 0.12);
  color: #ef4444;
  border: 2px solid rgba(239, 68, 68, 0.2);
}

:global(.dark-theme) .icon-danger {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
  border-color: rgba(239, 68, 68, 0.35);
}

.icon-warning {
  background: rgba(245, 158, 11, 0.12);
  color: #d97706;
  border: 2px solid rgba(245, 158, 11, 0.2);
}

:global(.dark-theme) .icon-warning {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
  border-color: rgba(245, 158, 11, 0.35);
}

.icon-success {
  background: rgba(16, 185, 129, 0.12);
  color: #059669;
  border: 2px solid rgba(16, 185, 129, 0.2);
}

:global(.dark-theme) .icon-success {
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
  border-color: rgba(16, 185, 129, 0.35);
}

.icon-info {
  background: rgba(59, 130, 246, 0.12);
  color: #2563eb;
  border: 2px solid rgba(59, 130, 246, 0.2);
}

:global(.dark-theme) .icon-info {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
  border-color: rgba(59, 130, 246, 0.35);
}

/* Body */
.confirm-body {
  width: 100%;
  margin-bottom: 1.5rem;
}

.confirm-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.5rem 0;
  line-height: 1.35;
  letter-spacing: -0.01em;
}

:global(.dark-theme) .confirm-title {
  color: #f8fafc;
}

.confirm-message {
  font-size: 0.9375rem;
  color: #64748b;
  margin: 0;
  line-height: 1.55;
  word-break: break-word;
}

:global(.dark-theme) .confirm-message {
  color: #94a3b8;
}

/* Details Box */
.confirm-details-box {
  margin-top: 1rem;
  padding: 0.625rem 0.875rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.8125rem;
  color: #475569;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-align: left;
}

:global(.dark-theme) .confirm-details-box {
  background: #0f172a;
  border-color: #334155;
  color: #cbd5e1;
}

/* Actions */
.confirm-actions {
  width: 100%;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.confirm-actions:has(.btn-dialog-cancel:only-child),
.confirm-actions:not(:has(.btn-dialog-cancel)) {
  grid-template-columns: 1fr;
}

.btn-dialog {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.25rem;
  border-radius: 12px;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.btn-dialog-cancel {
  background: #f1f5f9;
  color: #475569;
  border-color: #e2e8f0;
}

.btn-dialog-cancel:hover {
  background: #e2e8f0;
  color: #1e293b;
}

:global(.dark-theme) .btn-dialog-cancel {
  background: #334155;
  color: #e2e8f0;
  border-color: #475569;
}

:global(.dark-theme) .btn-dialog-cancel:hover {
  background: #475569;
  color: #ffffff;
}

/* Confirm Button Variations */
.btn-dialog-confirm {
  color: #ffffff;
}

.btn-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  }

.btn-danger:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-1px);
}

.btn-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  }

.btn-warning:hover {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-1px);
}

.btn-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  }

.btn-success:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
}

.btn-info {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  }

.btn-info:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
}

/* Animations */
.confirm-modal-fade-enter-active,
.confirm-modal-fade-leave-active {
  transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.confirm-modal-fade-enter-from,
.confirm-modal-fade-leave-to {
  opacity: 0;
}

.confirm-modal-fade-enter-active .confirm-card {
  animation: modalPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.confirm-modal-fade-leave-active .confirm-card {
  animation: modalPopOut 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes modalPopIn {
  from {
    opacity: 0;
    transform: scale(0.92) translateY(12px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes modalPopOut {
  from {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
  to {
    opacity: 0;
    transform: scale(0.94) translateY(8px);
  }
}

@media (max-width: 480px) {
  .confirm-card {
    padding: 1.5rem 1.25rem;
  }
  .confirm-title {
    font-size: 1.125rem;
  }
  .confirm-actions {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }
  .btn-dialog-confirm {
    order: 1;
  }
  .btn-dialog-cancel {
    order: 2;
  }
}
</style>
