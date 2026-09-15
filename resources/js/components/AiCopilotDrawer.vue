<template>
  <Teleport to="body">
    <Transition name="copilot-fade">
      <div 
        v-if="isOpen" 
        class="copilot-backdrop" 
        @click.self="closeDrawer"
      >
        <div 
          class="copilot-drawer-panel"
          :class="{ 'mobile-view': isMobile }"
          role="dialog"
          aria-modal="true"
        >
          <!-- Header Bar -->
          <div class="copilot-header">
            <div class="copilot-header-brand">
              <div class="copilot-profile-avatar">
                <img src="/assets/img/ai_profile.jpg" alt="Hanjeli AI Profile" class="copilot-profile-img" />
                <span class="live-pulse-dot" :class="{ 'pulse-active': !isLoading }"></span>
              </div>
              <div class="copilot-header-info">
                <div class="copilot-title-row">
                  <h3 class="copilot-title">{{ t('aiCopilot.title') }}</h3>
                  <span class="copilot-badge" :class="activeBatchCode ? 'badge-batch' : 'badge-idle'">
                    {{ activeBatchCode ? activeBatchCode : t('aiCopilot.standby') }}
                  </span>
                </div>
                <p class="copilot-sub">{{ t('aiCopilot.subtitle') }}</p>
              </div>
            </div>

            <div class="copilot-header-actions">
              <!-- Context Telemetry Inspector Toggle -->
              <button 
                type="button" 
                class="icon-action-btn"
                :class="{ 'btn-active': isContextExpanded }"
                :title="t('aiCopilot.viewSnapshotTooltip')"
                @click="isContextExpanded = !isContextExpanded"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                  <polyline points="2 17 12 22 22 17"></polyline>
                  <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
              </button>

              <!-- Clear Conversation -->
              <button 
                type="button" 
                class="icon-action-btn"
                :title="t('aiCopilot.clearChat')"
                @click="resetChat"
              >
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>

              <!-- Close Drawer -->
              <button 
                type="button" 
                class="icon-action-btn close-btn"
                @click="closeDrawer"
              >
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>
          </div>

          <!-- Feature Navigation Tabs (Chat, Diagnostik, Kalkulator) -->
          <div class="copilot-tabs-nav">
            <button 
              type="button" 
              class="copilot-tab-btn" 
              :class="{ active: activeTab === 'chat' }"
              @click="activeTab = 'chat'"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              <span>{{ t('aiCopilot.tabChat') }}</span>
            </button>
            <button 
              type="button" 
              class="copilot-tab-btn" 
              :class="{ active: activeTab === 'diagnostics' }"
              @click="activeTab = 'diagnostics'"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
              </svg>
              <span>{{ t('aiCopilot.tabDiagnostics') }}</span>
            </button>
            <button 
              type="button" 
              class="copilot-tab-btn" 
              :class="{ active: activeTab === 'calculator' }"
              @click="activeTab = 'calculator'"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="2"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="16" y1="14" x2="16" y2="14.01"></line>
                <line x1="12" y1="14" x2="12" y2="14.01"></line>
                <line x1="8" y1="14" x2="8" y2="14.01"></line>
                <line x1="16" y1="18" x2="16" y2="18.01"></line>
                <line x1="12" y1="18" x2="12" y2="18.01"></line>
                <line x1="8" y1="18" x2="8" y2="18.01"></line>
              </svg>
              <span>{{ t('aiCopilot.tabCalculator') }}</span>
            </button>
          </div>

          <!-- Microclimate Live Telemetry Banner Strip (Seamless, No Scrollbar) -->
          <div class="telemetry-micro-strip" v-if="contextSnapshot">
            <div class="telemetry-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
              </svg>
              <span class="chip-label">{{ t('aiCopilot.chipTempIn') }}</span>
              <strong class="chip-val">{{ contextSnapshot.tempInternal }}°C</strong>
            </div>
            <div class="telemetry-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
              <span class="chip-label">{{ t('aiCopilot.chipRhIn') }}</span>
              <strong class="chip-val">{{ contextSnapshot.humidityInternal }}%</strong>
            </div>
            <div class="telemetry-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
              </svg>
              <span class="chip-label">{{ t('aiCopilot.chipMoisture') }}</span>
              <strong class="chip-val">{{ contextSnapshot.currentMoisture }}%</strong>
            </div>
            <div class="telemetry-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
              </svg>
              <span class="chip-label">{{ t('aiCopilot.chipRadiation') }}</span>
              <strong class="chip-val">{{ contextSnapshot.solarRadiation }} W/m²</strong>
            </div>
          </div>

          <!-- Collapsible Telemetry Context Inspector Accordion -->
          <div v-if="isContextExpanded && contextSnapshot" class="context-inspector-panel">
            <div class="inspector-header">
              <span class="inspector-title">{{ t('aiCopilot.inspectorTitle') }}</span>
              <span class="inspector-time">{{ contextSnapshot.timestamp }}</span>
            </div>
            <div class="inspector-grid">
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorActiveBatch') }}</span>
                <span class="ii-val">{{ contextSnapshot.batchCode }} ({{ contextSnapshot.cropVariety }})</span>
              </div>
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorStatusMode') }}</span>
                <span class="ii-val">{{ contextSnapshot.batchStatus }} | {{ t('aiCopilot.inspectorShrinkage', { current: contextSnapshot.currentWeightKg, initial: contextSnapshot.initialWeightKg }) }}</span>
              </div>
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorTempDelta') }}</span>
                <span class="ii-val">{{ contextSnapshot.tempInternal }}°C / {{ contextSnapshot.tempExternal }}°C (Δ {{ (contextSnapshot.tempInternal - contextSnapshot.tempExternal).toFixed(1) }}°C)</span>
              </div>
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorRhDelta') }}</span>
                <span class="ii-val">{{ contextSnapshot.humidityInternal }}% / {{ contextSnapshot.humidityExternal }}%</span>
              </div>
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorHeaterStatus') }}</span>
                <span class="ii-val">{{ contextSnapshot.heaterStatus ? `${t('aiCopilot.activeLabel')} (${t('aiCopilot.levelLabel')} ${contextSnapshot.heaterLevel})` : t('aiCopilot.inactiveLabel') }}</span>
              </div>
              <div class="inspector-item">
                <span class="ii-label">{{ t('aiCopilot.inspectorExhaustStatus') }}</span>
                <span class="ii-val">{{ contextSnapshot.exhaustFanStatus ? `${t('aiCopilot.activeLabel')} (${contextSnapshot.exhaustFanSpeed}%)` : t('aiCopilot.inactiveLabel') }}</span>
              </div>
            </div>
          </div>

          <!-- TAB 1: CHAT ASISTEN -->
          <div v-show="activeTab === 'chat'" class="tab-content-wrapper">
            <!-- Messages Conversation Feed -->
            <div class="copilot-messages-container no-scrollbar" ref="messagesContainer">
              <!-- Welcome Initial Message -->
              <div class="message-group ai-group">
                <div class="msg-avatar ai-avatar">
                  <img src="/assets/img/ai_profile.jpg" alt="Hanjeli AI" class="ai-msg-avatar-img" />
                </div>
                <div class="msg-body-wrapper">
                  <div class="msg-author-row">
                    <span class="msg-author-name">{{ t('aiCopilot.copilotName') }}</span>
                    <span class="msg-timestamp">{{ t('aiCopilot.systemStandby') }}</span>
                  </div>
                  <div class="msg-bubble ai-bubble welcome-card">
                    <div class="welcome-header">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                      </svg>
                      <strong>{{ t('aiCopilot.welcomeTitle') }}</strong>
                    </div>
                    <p class="welcome-text" v-html="t('aiCopilot.welcomeDesc', { batch: activeBatchCode || t('aiCopilot.welcomeBatchFallback') })"></p>
                  </div>
                </div>
              </div>

              <!-- Messages List -->
              <div 
                v-for="(msg, index) in messages" 
                :key="index"
                class="message-group"
                :class="msg.sender === 'user' ? 'user-group' : 'ai-group'"
              >
                <!-- Avatar on Left (for AI) -->
                <div v-if="msg.sender !== 'user'" class="msg-avatar ai-avatar">
                  <img src="/assets/img/ai_profile.jpg" alt="Hanjeli AI" class="ai-msg-avatar-img" />
                </div>

                <!-- Message Content Body -->
                <div class="msg-body-wrapper">
                  <div class="msg-author-row" :class="{ 'user-author-row': msg.sender === 'user' }">
                    <span class="msg-author-name">{{ msg.sender === 'user' ? t('aiCopilot.operatorName') : t('aiCopilot.copilotName') }}</span>
                    <span class="msg-timestamp">{{ msg.time }}</span>
                  </div>

                  <div 
                    class="msg-bubble"
                    :class="msg.sender === 'user' ? 'user-bubble' : 'ai-bubble'"
                  >
                    <!-- Markdown Formatted Content -->
                    <div class="markdown-body" v-html="renderMarkdown(msg.text)"></div>

                    <!-- Copy Button on AI Bubble -->
                    <div v-if="msg.sender !== 'user'" class="bubble-actions-row">
                      <button 
                        type="button" 
                        class="copy-msg-btn"
                        @click="copyMessage(msg.text, index)"
                      >
                        <svg v-if="copiedIndex !== index" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                          <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                          <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>{{ copiedIndex === index ? t('aiCopilot.copied') : t('aiCopilot.copyAnswer') }}</span>
                      </button>
                    </div>

                    <!-- Follow-up Prompt Pills -->
                    <div v-if="msg.quickActions && msg.quickActions.length" class="followup-pills-row">
                      <button
                        v-for="(action, aIdx) in msg.quickActions"
                        :key="aIdx"
                        type="button"
                        class="followup-pill-btn"
                        @click="handleSendPrompt(action.prompt)"
                      >
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                          <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <span>{{ action.label }}</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Avatar on Right (for User) -->
                <div v-if="msg.sender === 'user'" class="msg-avatar user-avatar">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                </div>
              </div>

              <!-- Typing / Loading Skeleton -->
              <div v-if="isLoading" class="message-group ai-group">
                <div class="msg-avatar ai-avatar">
                  <img src="/assets/img/ai_profile.jpg" alt="Hanjeli AI" class="ai-msg-avatar-img" />
                </div>
                <div class="msg-body-wrapper">
                  <div class="msg-bubble ai-bubble thinking-bubble">
                    <div class="typing-indicator">
                      <span></span>
                      <span></span>
                      <span></span>
                    </div>
                    <span class="thinking-text">{{ t('aiCopilot.statusThinking') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Suggestions Horizontal Scroll (No Scrollbar) -->
            <div class="quick-suggestions-wrapper" v-if="suggestions.length && !isLoading">
              <div class="suggestions-header">
                <span class="suggestions-title-text">{{ t('aiCopilot.quickQuestions') }}</span>
              </div>
              <div class="suggestions-pills-scroll no-scrollbar">
                <button
                  v-for="sug in suggestions"
                  :key="sug.id"
                  type="button"
                  class="quick-pill-btn"
                  @click="handleSendPrompt(sug.prompt)"
                >
                  <svg v-if="sug.icon === 'bolt'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                  <svg v-else-if="sug.icon === 'clock'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <svg v-else-if="sug.icon === 'cloud-rain'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                    <path d="M16 14v6M8 14v6M12 16v6"></path>
                  </svg>
                  <svg v-else-if="sug.icon === 'bulb'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M9 18h6M10 22h4M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5A4.65 4.65 0 0 1 8.9 14"></path>
                  </svg>
                  <svg v-else-if="sug.icon === 'shield'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  </svg>
                  <svg v-else-if="sug.icon === 'sprout'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                  </svg>
                  <svg v-else-if="sug.icon === 'thermometer'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                  </svg>
                  <span class="pill-title">{{ sug.title }}</span>
                </button>
              </div>
            </div>

            <!-- Input Box Form Footer -->
            <div class="copilot-footer">
              <form @submit.prevent="submitInput" class="copilot-input-form">
                <input
                  ref="promptInputRef"
                  v-model="inputPrompt"
                  type="text"
                  class="copilot-input-field"
                  :placeholder="t('aiCopilot.placeholder')"
                  :disabled="isLoading"
                />
                <button 
                  type="submit" 
                  class="copilot-send-btn"
                  :disabled="!inputPrompt.trim() || isLoading"
                  :title="t('aiCopilot.sendTooltip')"
                >
                  <svg v-if="!isLoading" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                  </svg>
                  <div v-else class="spinner-sm"></div>
                </button>
              </form>
              <div class="copilot-disclaimer">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>{{ t('aiCopilot.disclaimer') }}</span>
              </div>
            </div>
          </div>

          <!-- TAB 2: DIAGNOSTIK IKLIM & ANOMALI -->
          <div v-show="activeTab === 'diagnostics'" class="tab-content-wrapper no-scrollbar diagnostics-view">
            <div class="diag-header-card">
              <div class="diag-score-circle">
                <span class="score-val">{{ computedHealthScore }}</span>
                <span class="score-label">{{ t('aiCopilot.diagScoreLabel') }}</span>
              </div>
              <div class="diag-score-info">
                <h4 class="diag-status-title">{{ computedHealthStatus }}</h4>
                <p class="diag-status-desc">{{ computedHealthDesc }}</p>
              </div>
            </div>

            <div class="diag-section-title">{{ t('aiCopilot.diagSectionTitle') }}</div>
            <div class="diag-cards-grid" v-if="contextSnapshot">
              <div class="diag-metric-card">
                <div class="dm-head">
                  <span>{{ t('aiCopilot.diagTempInternal') }}</span>
                  <span class="dm-badge" :class="contextSnapshot.tempInternal > 55 ? 'dm-danger' : 'dm-good'">
                    {{ contextSnapshot.tempInternal > 55 ? t('aiCopilot.diagTempCritical') : t('aiCopilot.diagTempNormal') }}
                  </span>
                </div>
                <div class="dm-val">{{ contextSnapshot.tempInternal }}°C</div>
                <div class="dm-sub">{{ t('aiCopilot.diagTempSafeLimit', { min: contextSnapshot.minSafeTemp, max: contextSnapshot.maxSafeTemp }) }}</div>
              </div>

              <div class="diag-metric-card">
                <div class="dm-head">
                  <span>{{ t('aiCopilot.diagRhInternal') }}</span>
                  <span class="dm-badge" :class="contextSnapshot.humidityInternal > 70 ? 'dm-warn' : 'dm-good'">
                    {{ contextSnapshot.humidityInternal > 70 ? t('aiCopilot.diagRhHigh') : t('aiCopilot.diagRhOptimal') }}
                  </span>
                </div>
                <div class="dm-val">{{ contextSnapshot.humidityInternal }}%</div>
                <div class="dm-sub">{{ t('aiCopilot.diagRhTarget') }}</div>
              </div>

              <div class="diag-metric-card">
                <div class="dm-head">
                  <span>{{ t('aiCopilot.diagSolarRadiation') }}</span>
                  <span class="dm-badge dm-info">
                    {{ contextSnapshot.solarRadiation > 500 ? t('aiCopilot.diagSolarHigh') : t('aiCopilot.diagSolarModerate') }}
                  </span>
                </div>
                <div class="dm-val">{{ contextSnapshot.solarRadiation }} W/m²</div>
                <div class="dm-sub">{{ t('aiCopilot.diagAutoHeater', { status: contextSnapshot.solarRadiation < 300 ? t('aiCopilot.activeLabel') : t('aiCopilot.standby') }) }}</div>
              </div>

              <div class="diag-metric-card">
                <div class="dm-head">
                  <span>{{ t('aiCopilot.diagMoistureTitle') }}</span>
                  <span class="dm-badge" :class="contextSnapshot.currentMoisture <= contextSnapshot.targetMoisture ? 'dm-success' : 'dm-info'">
                    {{ contextSnapshot.currentMoisture <= contextSnapshot.targetMoisture ? t('aiCopilot.diagMoistureDone') : t('aiCopilot.diagMoistureDrying') }}
                  </span>
                </div>
                <div class="dm-val">{{ contextSnapshot.currentMoisture }}%</div>
                <div class="dm-sub">{{ t('aiCopilot.diagMoistureTarget', { target: contextSnapshot.targetMoisture }) }}</div>
              </div>
            </div>

            <!-- Quick Action Button to Ask AI from Diagnostics -->
            <button 
              type="button" 
              class="diag-ask-ai-btn"
              @click="askAiDiagnostics"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              <span>{{ t('aiCopilot.diagAskAiBtn') }}</span>
            </button>
          </div>

          <!-- TAB 3: KALKULATOR SUSUT & ETA -->
          <div v-show="activeTab === 'calculator'" class="tab-content-wrapper no-scrollbar calculator-view">
            <div class="calc-card">
              <h4 class="calc-card-title">{{ t('aiCopilot.calcCardTitle') }}</h4>
              <p class="calc-card-desc">{{ t('aiCopilot.calcCardDesc') }}</p>

              <div class="calc-inputs-grid">
                <div class="calc-input-group">
                  <label class="calc-label">{{ t('aiCopilot.calcInitialWeightLabel') }}</label>
                  <input v-model.number="calcInitialWeight" type="number" step="0.5" class="calc-input" />
                </div>
                <div class="calc-input-group">
                  <label class="calc-label">{{ t('aiCopilot.calcInitialMoistureLabel') }}</label>
                  <input v-model.number="calcInitialMoisture" type="number" step="0.5" class="calc-input" />
                </div>
                <div class="calc-input-group">
                  <label class="calc-label">{{ t('aiCopilot.calcTargetMoistureLabel') }}</label>
                  <input v-model.number="calcTargetMoisture" type="number" step="0.5" class="calc-input" />
                </div>
              </div>

              <!-- Calculation Result Cards -->
              <div class="calc-results-panel">
                <div class="cr-item">
                  <span class="cr-label">{{ t('aiCopilot.calcEstFinalWeight') }}</span>
                  <strong class="cr-val highlight">{{ computedFinalWeight }} kg</strong>
                </div>
                <div class="cr-item">
                  <span class="cr-label">{{ t('aiCopilot.calcTotalWaterEvap') }}</span>
                  <strong class="cr-val">{{ computedWaterEvaporated }} kg</strong>
                </div>
                <div class="cr-item">
                  <span class="cr-label">{{ t('aiCopilot.calcWeightLossPercent') }}</span>
                  <strong class="cr-val">{{ computedWeightLossPercent }}%</strong>
                </div>
              </div>

              <button 
                type="button" 
                class="diag-ask-ai-btn"
                @click="askAiCalculation"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>{{ t('aiCopilot.calcConsultBtn') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useI18n } from '../i18n'
import { aiService } from '../services/aiService'

const { t, currentLang } = useI18n()

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  isMobile: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close'])

const activeTab = ref('chat') // 'chat', 'diagnostics', 'calculator'
const isContextExpanded = ref(false)

const inputPrompt = ref('')
const isLoading = ref(false)
const messages = ref([])
const suggestions = ref([])
const contextSnapshot = ref(null)
const activeBatchCode = ref(null)
const lastSource = ref(null)
const copiedIndex = ref(null)

const messagesContainer = ref(null)
const promptInputRef = ref(null)

// Calculator state
const calcInitialWeight = ref(50)
const calcInitialMoisture = ref(24)
const calcTargetMoisture = ref(14)

// Calculator formulas (Standard grain drying equation: W2 = W1 * (100 - M1) / (100 - M2))
const computedFinalWeight = computed(() => {
  const w1 = calcInitialWeight.value || 0
  const m1 = calcInitialMoisture.value || 0
  const m2 = calcTargetMoisture.value || 0
  if (m2 >= 100 || m1 >= 100 || m2 >= m1) return w1
  const w2 = (w1 * (100 - m1)) / (100 - m2)
  return Math.round(w2 * 10) / 10
})

const computedWaterEvaporated = computed(() => {
  const w1 = calcInitialWeight.value || 0
  const w2 = computedFinalWeight.value || 0
  return Math.max(0, Math.round((w1 - w2) * 10) / 10)
})

const computedWeightLossPercent = computed(() => {
  const w1 = calcInitialWeight.value || 0
  const we = computedWaterEvaporated.value || 0
  if (w1 <= 0) return 0
  return Math.round((we / w1) * 1000) / 10
})

// Computed Health score for diagnostics
const computedHealthScore = computed(() => {
  if (!contextSnapshot.value) return 92
  let score = 100
  if (contextSnapshot.value.tempInternal > 55) score -= 25
  if (contextSnapshot.value.humidityInternal > 70) score -= 15
  if (contextSnapshot.value.activeAlertsCount > 0) score -= 10
  return Math.max(50, score)
})

const computedHealthStatus = computed(() => {
  const s = computedHealthScore.value
  if (s >= 85) return t('aiCopilot.diagScoreOptimal')
  if (s >= 70) return t('aiCopilot.diagScoreAdjust')
  return t('aiCopilot.diagScoreAnomaly')
})

const computedHealthDesc = computed(() => {
  if (!contextSnapshot.value) return t('aiCopilot.diagDescOptimal')
  if (contextSnapshot.value.tempInternal > 55) return t('aiCopilot.diagDescHighTemp')
  if (contextSnapshot.value.humidityInternal > 70) return t('aiCopilot.diagDescHighRh')
  return t('aiCopilot.diagDescDefault')
})

function getTimeNow() {
  const d = new Date()
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

async function loadContextAndSuggestions() {
  try {
    const [sugRes, ctxRes] = await Promise.allSettled([
      aiService.getSuggestions(currentLang.value),
      aiService.getContextSnapshot(),
    ])

    if (sugRes.status === 'fulfilled' && sugRes.value) {
      suggestions.value = sugRes.value.suggestions || []
      activeBatchCode.value = sugRes.value.batchCode !== 'TIDAK ADA' ? sugRes.value.batchCode : null
    }

    if (ctxRes.status === 'fulfilled' && ctxRes.value && ctxRes.value.context) {
      contextSnapshot.value = ctxRes.value.context
      if (contextSnapshot.value.initialWeightKg) calcInitialWeight.value = contextSnapshot.value.initialWeightKg
      if (contextSnapshot.value.initialMoisture) calcInitialMoisture.value = contextSnapshot.value.initialMoisture
      if (contextSnapshot.value.targetMoisture) calcTargetMoisture.value = contextSnapshot.value.targetMoisture
    }
  } catch (err) {
    console.error('Failed loading AI context snapshot:', err)
  }
}

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loadContextAndSuggestions()
    nextTick(() => {
      scrollToBottom()
      if (promptInputRef.value) {
        promptInputRef.value.focus()
      }
    })
  }
})

// Automatically re-fetch suggestions when language changes while drawer is open
watch(currentLang, () => {
  if (props.isOpen) {
    loadContextAndSuggestions()
  }
})

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

function submitInput() {
  if (!inputPrompt.value.trim() || isLoading.value) return
  const prompt = inputPrompt.value.trim()
  inputPrompt.value = ''
  handleSendPrompt(prompt)
}

async function handleSendPrompt(promptText) {
  if (!promptText || isLoading.value) return

  activeTab.value = 'chat'

  messages.value.push({
    sender: 'user',
    text: promptText,
    time: getTimeNow(),
  })

  isLoading.value = true
  nextTick(scrollToBottom)

  try {
    const historyPayload = messages.value.map(m => ({
      sender: m.sender === 'user' ? 'user' : 'model',
      text: m.text,
    }))

    const res = await aiService.sendMessage(promptText, historyPayload, currentLang.value)

    if (res && res.success) {
      lastSource.value = res.source
      if (res.contextSnapshot) {
        contextSnapshot.value = res.contextSnapshot
        if (res.contextSnapshot.batchCode && res.contextSnapshot.batchCode !== 'TIDAK ADA') {
          activeBatchCode.value = res.contextSnapshot.batchCode
        }
      }

      messages.value.push({
        sender: 'ai',
        text: res.reply,
        time: getTimeNow(),
        quickActions: res.quickActions || [],
      })
    } else {
      messages.value.push({
        sender: 'ai',
        text: t('aiCopilot.errorProcess'),
        time: getTimeNow(),
      })
    }
  } catch (err) {
    console.error('AI Chat Error:', err)
    messages.value.push({
      sender: 'ai',
      text: t('aiCopilot.errorConnection'),
      time: getTimeNow(),
    })
  } finally {
    isLoading.value = false
    nextTick(() => {
      scrollToBottom()
      if (promptInputRef.value) {
        promptInputRef.value.focus()
      }
    })
  }
}

function copyMessage(text, index) {
  if (!text) return
  navigator.clipboard.writeText(text).then(() => {
    copiedIndex.value = index
    setTimeout(() => {
      copiedIndex.value = null
    }, 2000)
  })
}

function askAiDiagnostics() {
  handleSendPrompt(t('aiCopilot.diagPromptText'))
}

function askAiCalculation() {
  handleSendPrompt(t('aiCopilot.calcPromptText', {
    w: calcInitialWeight.value,
    m1: calcInitialMoisture.value,
    m2: calcTargetMoisture.value,
  }))
}

function resetChat() {
  messages.value = []
  lastSource.value = null
  loadContextAndSuggestions()
}

function closeDrawer() {
  emit('close')
}

// Markdown parser
function renderMarkdown(raw) {
  if (!raw) return ''
  let html = raw

  // Escape HTML
  html = html
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')

  // Headers
  html = html.replace(/^### (.*$)/gim, '<h4 class="md-h4">$1</h4>')
  html = html.replace(/^## (.*$)/gim, '<h3 class="md-h3">$1</h3>')
  html = html.replace(/^# (.*$)/gim, '<h2 class="md-h2">$1</h2>')

  // Blockquotes
  html = html.replace(/^\> (.*$)/gim, '<blockquote class="md-quote">$1</blockquote>')

  // Bold & Italic
  html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
  html = html.replace(/\*(.*?)\*/g, '<em>$1</em>')

  // Bullet points
  html = html.replace(/^\s*[\*\-]\s+(.*$)/gim, '<li class="md-li">$1</li>')
  html = html.replace(/(<li class="md-li">[\s\S]*?<\/li>)/g, '<ul class="md-ul">$1</ul>')
  html = html.replace(/<\/ul>\s*<ul class="md-ul">/g, '')

  // Numbered list
  html = html.replace(/^\d+\.\s+(.*$)/gim, '<li class="md-oli">$1</li>')
  
  // Horizontal Rule
  html = html.replace(/^---$/gim, '<hr class="md-hr" />')

  // Linebreaks
  html = html.replace(/\n\n/g, '<br class="my-2"/>')

  return html
}

onMounted(() => {
  loadContextAndSuggestions()
})
</script>

<style scoped>
/* Universal Zero-Scrollbar Utility */
.no-scrollbar {
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.no-scrollbar::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

/* Backdrop & Transitions */
.copilot-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(7, 30, 39, 0.65);
  backdrop-filter: blur(6px);
  z-index: 9999;
  display: flex;
  justify-content: flex-end;
  align-items: stretch;
}

.copilot-fade-enter-active,
.copilot-fade-leave-active {
  transition: opacity 0.25s ease;
}

.copilot-fade-enter-from,
.copilot-fade-leave-to {
  opacity: 0;
}

.copilot-drawer-panel {
  width: 100%;
  max-width: 500px;
  background: var(--color-white, #FFFFFF);
  color: var(--color-text-title, #071E27);
  display: flex;
  flex-direction: column;
  border-left: 1px solid var(--color-border);
  animation: slideInRight 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.copilot-drawer-panel.mobile-view {
  max-width: 100%;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

/* Header */
.copilot-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  background: var(--color-white, #FFFFFF);
  border-bottom: 1px solid var(--color-border);
}

:global(.dark-theme) .copilot-header {
  background: #0B242F !important;
  border-bottom-color: #1E4E61 !important;
}

:global(.dark-theme) .copilot-drawer-panel {
  background: #071E27 !important;
  border-left-color: #1E4E61 !important;
  color: #F8FAFC !important;
}

.copilot-header-brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.copilot-profile-avatar {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 0 2px rgba(13, 99, 27, 0.2);
}

.copilot-profile-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}

:global(.dark-theme) .copilot-profile-avatar {
  background: #113847 !important;
  box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.3) !important;
}

.live-pulse-dot {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #0D631B;
  border: 2px solid #FFFFFF;
}

:global(.dark-theme) .live-pulse-dot {
  background: #4ADE80 !important;
  border-color: #0B242F !important;
}

.live-pulse-dot.pulse-active {
  animation: pulseDot 2s infinite;
}

@keyframes pulseDot {
  0% { box-shadow: 0 0 0 0 rgba(13, 99, 27, 0.7); }
  70% { box-shadow: 0 0 0 6px rgba(13, 99, 27, 0); }
  100% { box-shadow: 0 0 0 0 rgba(13, 99, 27, 0); }
}

.copilot-header-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.copilot-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.copilot-title {
  font-size: 15px;
  font-weight: 700;
  margin: 0;
  color: var(--color-text-title, #071E27);
}

:global(.dark-theme) .copilot-title {
  color: #F8FAFC !important;
}

.copilot-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  letter-spacing: 0.02em;
}

.badge-batch {
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #C8E6C9;
}

:global(.dark-theme) .badge-batch {
  background: rgba(74, 222, 128, 0.15) !important;
  color: #4ADE80 !important;
  border-color: rgba(74, 222, 128, 0.3) !important;
}

.badge-idle {
  background: #F1F5F9;
  color: #64748B;
  border: 1px solid #E2E8F0;
}

:global(.dark-theme) .badge-idle {
  background: #113847 !important;
  color: #94A3B8 !important;
  border-color: #1E4E61 !important;
}

.copilot-sub {
  font-size: 11.5px;
  color: var(--color-text-muted, #40493D);
  margin: 0;
}

:global(.dark-theme) .copilot-sub {
  color: #94A3B8 !important;
}

.copilot-header-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}

.icon-action-btn {
  padding: 7px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: var(--color-text-muted, #40493D);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-action-btn:hover,
.icon-action-btn.btn-active {
  background: rgba(0, 0, 0, 0.05);
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .icon-action-btn {
  color: #94A3B8 !important;
}

:global(.dark-theme) .icon-action-btn:hover,
:global(.dark-theme) .icon-action-btn.btn-active {
  background: rgba(255, 255, 255, 0.08) !important;
  color: #4ADE80 !important;
}

.icon-action-btn.close-btn:hover {
  background: var(--color-red-bg, rgba(186, 26, 26, 0.1));
  color: var(--color-red, #BA1A1A);
}

/* Feature Navigation Tabs */
.copilot-tabs-nav {
  display: flex;
  border-bottom: 1px solid var(--color-border);
  background: var(--color-bg-light, #F4F7F5);
  padding: 4px 8px 0 8px;
  gap: 4px;
}

:global(.dark-theme) .copilot-tabs-nav {
  background: #0B242F !important;
  border-bottom-color: #1E4E61 !important;
}

.copilot-tab-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 6px;
  border: none;
  background: transparent;
  color: var(--color-text-muted, #40493D);
  font-size: 12px;
  font-weight: 600;
  border-radius: 8px 8px 0 0;
  cursor: pointer;
  transition: all 0.2s;
  border-bottom: 2px solid transparent;
}

:global(.dark-theme) .copilot-tab-btn {
  color: #94A3B8 !important;
}

.copilot-tab-btn:hover {
  color: var(--color-primary-dark, #0D631B);
  background: rgba(0, 0, 0, 0.03);
}

.copilot-tab-btn.active {
  color: var(--color-primary-dark, #0D631B);
  background: var(--color-white, #FFFFFF);
  border-bottom: 2px solid var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .copilot-tab-btn.active {
  color: #4ADE80 !important;
  background: #071E27 !important;
  border-bottom-color: #4ADE80 !important;
}

/* Microstrip Telemetry Bar */
.telemetry-micro-strip {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  background: var(--color-white, #FFFFFF);
  border-bottom: 1px solid var(--color-border);
  overflow-x: auto;
  white-space: nowrap;
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.telemetry-micro-strip::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

:global(.dark-theme) .telemetry-micro-strip {
  background: #0B242F !important;
  border-bottom-color: #1E4E61 !important;
}

.telemetry-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  padding: 4px 8px;
  background: var(--color-bg-light, #F4F7F5);
  border-radius: 6px;
  border: 1px solid var(--color-border);
  color: var(--color-text-body, #40493D);
  flex-shrink: 0;
}

:global(.dark-theme) .telemetry-chip {
  background: #113847 !important;
  color: #CBD5E1 !important;
  border-color: #1E4E61 !important;
}

.chip-label {
  color: var(--color-text-muted, #40493D);
}

:global(.dark-theme) .chip-label {
  color: #94A3B8 !important;
}

.chip-val {
  color: var(--color-primary-dark, #0D631B);
  font-weight: 700;
}

:global(.dark-theme) .chip-val {
  color: #4ADE80 !important;
}

/* Context Inspector Panel */
.context-inspector-panel {
  background: var(--color-bg-light, #F4F7F5);
  border-bottom: 1px solid var(--color-border);
  padding: 12px 16px;
  font-size: 12px;
}

:global(.dark-theme) .context-inspector-panel {
  background: #0B242F !important;
  border-bottom-color: #1E4E61 !important;
}

.inspector-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.inspector-title {
  font-weight: 700;
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .inspector-title {
  color: #4ADE80 !important;
}

.inspector-time {
  font-size: 11px;
  color: var(--color-text-dim);
}

:global(.dark-theme) .inspector-time {
  color: #94A3B8 !important;
}

.inspector-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}

.inspector-item {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  padding: 6px 8px;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
}

:global(.dark-theme) .inspector-item {
  background: #113847 !important;
  border-color: #1E4E61 !important;
}

.ii-label {
  font-size: 10.5px;
  color: var(--color-text-muted);
}

:global(.dark-theme) .ii-label {
  color: #94A3B8 !important;
}

.ii-val {
  font-weight: 600;
  color: var(--color-text-title);
}

:global(.dark-theme) .ii-val {
  color: #F8FAFC !important;
}

/* Tab Content Wrappers */
.tab-content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}

/* Messages Feed */
.copilot-messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: var(--color-white, #FFFFFF);
}

:global(.dark-theme) .copilot-messages-container {
  background: #071E27 !important;
}

/* Message Group Layout with Separate Avatars */
.message-group {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  max-width: 94%;
}

.message-group.ai-group {
  align-self: flex-start;
}

.message-group.user-group {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.msg-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
  overflow: hidden;
}

.ai-msg-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}

.ai-avatar {
  background: #E8F5E9;
  border: 1px solid #C8E6C9;
}

:global(.dark-theme) .ai-avatar {
  background: #113847 !important;
  border-color: #1E4E61 !important;
}

.user-avatar {
  background: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .user-avatar {
  background: #0D631B !important;
}

.msg-body-wrapper {
  display: flex;
  flex-direction: column;
  gap: 3px;
  max-width: 100%;
}

.msg-author-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: var(--color-text-muted, #40493D);
  padding: 0 4px;
}

:global(.dark-theme) .msg-author-row {
  color: #94A3B8 !important;
}

.user-author-row {
  justify-content: flex-end;
}

.msg-author-name {
  font-weight: 700;
}

.msg-timestamp {
  font-size: 10.5px;
  color: var(--color-text-dim);
}

:global(.dark-theme) .msg-timestamp {
  color: #94A3B8 !important;
}

/* Separate Individual Message Bubbles */
.msg-bubble {
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 13.5px;
  line-height: 1.55;
  word-break: break-word;
}

.user-bubble {
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  border-top-right-radius: 2px;
}

.ai-bubble {
  background: var(--color-bg-light, #F4F7F5);
  color: var(--color-text-title, #071E27);
  border: 1px solid var(--color-border);
  border-top-left-radius: 2px;
}

:global(.dark-theme) .ai-bubble {
  background: #0B242F !important;
  color: #F8FAFC !important;
  border-color: #1E4E61 !important;
}

.welcome-card {
  background: rgba(46, 125, 50, 0.08);
  border: 1px dashed var(--color-primary, #2E7D32);
}

:global(.dark-theme) .welcome-card {
  background: rgba(74, 222, 128, 0.08) !important;
  border-color: rgba(74, 222, 128, 0.3) !important;
}

.welcome-header {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 4px;
  color: var(--color-primary-dark, #0D631B);
  font-size: 13.5px;
}

:global(.dark-theme) .welcome-header {
  color: #4ADE80 !important;
}

.welcome-text {
  font-size: 12.5px;
  color: var(--color-text-body, #40493D);
  margin: 0;
}

:global(.dark-theme) .welcome-text {
  color: #CBD5E1 !important;
}

/* Bubble Actions (Copy) */
.bubble-actions-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
  padding-top: 6px;
  border-top: 1px dashed var(--color-border);
}

:global(.dark-theme) .bubble-actions-row {
  border-top-color: #1E4E61 !important;
}

.copy-msg-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: var(--color-text-muted);
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 2px 6px;
  border-radius: 4px;
}

.copy-msg-btn:hover {
  background: rgba(0, 0, 0, 0.05);
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .copy-msg-btn {
  color: #94A3B8 !important;
}

:global(.dark-theme) .copy-msg-btn:hover {
  background: rgba(255, 255, 255, 0.08) !important;
  color: #4ADE80 !important;
}

/* Follow-up Pills */
.followup-pills-row {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 8px;
}

.followup-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11.5px;
  padding: 4px 10px;
  border-radius: 16px;
  background: var(--color-primary-bg, rgba(46, 125, 50, 0.1));
  color: var(--color-primary-dark, #0D631B);
  border: 1px solid var(--color-primary-badge, rgba(46, 125, 50, 0.2));
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 600;
}

.followup-pill-btn:hover {
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
}

:global(.dark-theme) .followup-pill-btn {
  background: rgba(74, 222, 128, 0.12) !important;
  color: #4ADE80 !important;
  border-color: rgba(74, 222, 128, 0.25) !important;
}

:global(.dark-theme) .followup-pill-btn:hover {
  background: #0D631B !important;
  color: #FFFFFF !important;
}

/* Typing Indicator */
.thinking-bubble {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
}

.typing-indicator {
  display: flex;
  align-items: center;
  gap: 4px;
}

.typing-indicator span {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-primary-dark, #0D631B);
  animation: bounce 1.4s infinite ease-in-out both;
}

:global(.dark-theme) .typing-indicator span {
  background: #4ADE80 !important;
}

.typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
.typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1.0); }
}

.thinking-text {
  font-size: 12px;
  color: var(--color-text-muted, #40493D);
  font-style: italic;
}

:global(.dark-theme) .thinking-text {
  color: #94A3B8 !important;
}

/* Quick Suggestions Horizontal Bar */
.quick-suggestions-wrapper {
  padding: 8px 16px;
  background: var(--color-bg-light, #F4F7F5);
  border-top: 1px solid var(--color-border);
}

:global(.dark-theme) .quick-suggestions-wrapper {
  background: #0B242F !important;
  border-top-color: #1E4E61 !important;
}

.suggestions-title-text {
  font-size: 11px;
  font-weight: 700;
  color: var(--color-text-muted, #40493D);
}

:global(.dark-theme) .suggestions-title-text {
  color: #94A3B8 !important;
}

.suggestions-pills-scroll {
  display: flex;
  gap: 6px;
  overflow-x: auto;
  padding-bottom: 2px;
  margin-top: 4px;
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.suggestions-pills-scroll::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

.quick-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 10px;
  border-radius: 16px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  color: var(--color-text-title, #071E27);
  font-size: 11.5px;
  font-weight: 600;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

:global(.dark-theme) .quick-pill-btn {
  background: #113847 !important;
  color: #F8FAFC !important;
  border-color: #1E4E61 !important;
}

.quick-pill-btn:hover {
  background: var(--color-primary-bg, rgba(46, 125, 50, 0.1));
  border-color: var(--color-primary-dark, #0D631B);
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .quick-pill-btn:hover {
  background: rgba(74, 222, 128, 0.15) !important;
  border-color: #4ADE80 !important;
  color: #4ADE80 !important;
}

/* Footer & Input */
.copilot-footer {
  padding: 10px 16px 12px 16px;
  border-top: 1px solid var(--color-border);
  background: var(--color-white, #FFFFFF);
}

:global(.dark-theme) .copilot-footer {
  background: #0B242F !important;
  border-top-color: #1E4E61 !important;
}

.copilot-input-form {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 4px 6px 4px 12px;
  transition: all 0.2s;
}

:global(.dark-theme) .copilot-input-form {
  background: #113847 !important;
  border-color: #1E4E61 !important;
}

.copilot-input-form:focus-within {
  border-color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .copilot-input-form:focus-within {
  border-color: #4ADE80 !important;
}

.copilot-input-field {
  flex: 1;
  border: none;
  background: transparent;
  color: var(--color-text-title, #071E27);
  font-size: 13.5px;
  outline: none;
}

:global(.dark-theme) .copilot-input-field {
  color: #F8FAFC !important;
}

.copilot-send-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.copilot-send-btn:hover:not(:disabled) {
  background: #084011;
}

.copilot-send-btn:disabled {
  background: #E2E8F0;
  color: #94A3B8;
  cursor: not-allowed;
}

:global(.dark-theme) .copilot-send-btn:disabled {
  background: #1E4E61 !important;
  color: #64748B !important;
}

.copilot-disclaimer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  font-size: 10.5px;
  color: var(--color-text-dim, rgba(64, 73, 61, 0.5));
  margin-top: 6px;
}

:global(.dark-theme) .copilot-disclaimer {
  color: #94A3B8 !important;
}

/* Spinner */
.spinner-sm {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* DIAGNOSTICS VIEW STYLES */
.diagnostics-view, .calculator-view {
  overflow-y: auto;
  padding: 16px;
  gap: 14px;
  background: var(--color-white, #FFFFFF);
  box-sizing: border-box;
  width: 100%;
}

:global(.dark-theme) .diagnostics-view,
:global(.dark-theme) .calculator-view {
  background: #071E27 !important;
}

.diag-header-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
  background: var(--color-bg-light, #F4F7F5);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  box-sizing: border-box;
}

:global(.dark-theme) .diag-header-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.diag-score-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #E8F5E9;
  border: 3px solid #0D631B;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .diag-score-circle {
  background: #113847 !important;
  border-color: #4ADE80 !important;
}

.score-val {
  font-size: 16px;
  font-weight: 800;
  color: #0D631B;
}

:global(.dark-theme) .score-val {
  color: #4ADE80 !important;
}

.score-label {
  font-size: 8.5px;
  color: #40493D;
  text-align: center;
}

:global(.dark-theme) .score-label {
  color: #94A3B8 !important;
}

.diag-status-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

:global(.dark-theme) .diag-status-title {
  color: #F8FAFC !important;
}

.diag-status-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  margin: 0;
}

:global(.dark-theme) .diag-status-desc {
  color: #94A3B8 !important;
}

.diag-section-title {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-top: 8px;
}

:global(.dark-theme) .diag-section-title {
  color: #F8FAFC !important;
}

.diag-cards-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  width: 100%;
}

.diag-metric-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
  box-sizing: border-box;
}

:global(.dark-theme) .diag-metric-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.dm-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 11px;
  color: var(--color-text-muted);
  gap: 4px;
}

:global(.dark-theme) .dm-head {
  color: #94A3B8 !important;
}

.dm-head span:first-child {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dm-badge {
  font-size: 9.5px;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 4px;
  flex-shrink: 0;
}

.dm-good { background: #E8F5E9; color: #0D631B; }
.dm-warn { background: #FFF3E0; color: #B45000; }
.dm-danger { background: #FFEBEE; color: #BA1A1A; }
.dm-info { background: #E3F2FD; color: #005DB7; }
.dm-success { background: #E8F5E9; color: #0D631B; }

:global(.dark-theme) .dm-good { background: rgba(74, 222, 128, 0.15) !important; color: #4ADE80 !important; }
:global(.dark-theme) .dm-warn { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; }
:global(.dark-theme) .dm-danger { background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important; }
:global(.dark-theme) .dm-info { background: rgba(56, 189, 248, 0.15) !important; color: #38BDF8 !important; }
:global(.dark-theme) .dm-success { background: rgba(74, 222, 128, 0.15) !important; color: #4ADE80 !important; }

.dm-val {
  font-size: 18px;
  font-weight: 800;
  color: var(--color-text-title);
}

:global(.dark-theme) .dm-val {
  color: #F8FAFC !important;
}

.dm-sub {
  font-size: 10.5px;
  color: var(--color-text-dim);
}

:global(.dark-theme) .dm-sub {
  color: #94A3B8 !important;
}

.diag-ask-ai-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px;
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 8px;
  width: 100%;
  box-sizing: border-box;
}

.diag-ask-ai-btn:hover {
  background: #084011;
}

/* CALCULATOR VIEW STYLES */
.calc-card {
  background: var(--color-bg-light, #F4F7F5);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-sizing: border-box;
  width: 100%;
}

:global(.dark-theme) .calc-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.calc-card-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0;
}

:global(.dark-theme) .calc-card-title {
  color: #F8FAFC !important;
}

.calc-card-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.45;
}

:global(.dark-theme) .calc-card-desc {
  color: #94A3B8 !important;
}

.calc-inputs-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
  width: 100%;
  box-sizing: border-box;
}

.calc-input-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.calc-label {
  font-size: 10.5px;
  font-weight: 600;
  color: var(--color-text-muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

:global(.dark-theme) .calc-label {
  color: #94A3B8 !important;
}

.calc-input {
  width: 100%;
  min-width: 0;
  box-sizing: border-box;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  padding: 6px 8px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-title);
  outline: none;
}

:global(.dark-theme) .calc-input {
  background: #113847 !important;
  border-color: #1E4E61 !important;
  color: #F8FAFC !important;
}

.calc-input:focus {
  border-color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .calc-input:focus {
  border-color: #4ADE80 !important;
}

.calc-results-panel {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  box-sizing: border-box;
  width: 100%;
}

:global(.dark-theme) .calc-results-panel {
  background: #113847 !important;
  border-color: #1E4E61 !important;
}

.cr-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
}

.cr-label {
  color: var(--color-text-muted);
}

:global(.dark-theme) .cr-label {
  color: #94A3B8 !important;
}

.cr-val {
  color: var(--color-text-title);
}

:global(.dark-theme) .cr-val {
  color: #F8FAFC !important;
}

.cr-val.highlight {
  color: var(--color-primary-dark, #0D631B);
  font-size: 14px;
}

:global(.dark-theme) .cr-val.highlight {
  color: #4ADE80 !important;
}

/* Markdown Styles inside Bubbles */
:deep(.md-h4) {
  font-size: 13.5px;
  font-weight: 700;
  margin-top: 6px;
  margin-bottom: 3px;
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) :deep(.md-h4) {
  color: #4ADE80 !important;
}

:deep(.md-quote) {
  border-left: 3px solid var(--color-primary, #2E7D32);
  padding-left: 8px;
  margin: 6px 0;
  font-size: 12px;
  color: var(--color-text-body, #40493D);
  background: var(--color-primary-bg, rgba(46, 125, 50, 0.1));
  border-radius: 0 4px 4px 0;
}

:global(.dark-theme) :deep(.md-quote) {
  border-left-color: #4ADE80 !important;
  color: #CBD5E1 !important;
  background: rgba(74, 222, 128, 0.1) !important;
}

:deep(.md-ul) {
  padding-left: 16px;
  margin: 4px 0;
}

:deep(.md-li) {
  margin-bottom: 2px;
}

:deep(.md-hr) {
  border: none;
  border-top: 1px solid var(--color-border);
  margin: 6px 0;
}

:global(.dark-theme) :deep(.md-hr) {
  border-top-color: #1E4E61 !important;
}
</style>
