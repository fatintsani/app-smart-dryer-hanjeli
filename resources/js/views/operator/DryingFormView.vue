<template>
  <div class="drying-form-page">
    <Transition name="fade-drying" mode="out-in">
      <DryingFormSkeleton v-if="isInitialLoading" />
      <div v-else class="drying-form-loaded-content">
        <!-- Unified Full Responsive Layout (Desktop, Tablet & Mobile) -->
        <div class="drying-form-main-content">
          <!-- Header -->
          <div class="page-header-group">
            <div class="title-with-tag">
              <h1 class="page-title">{{ $t('dryingForm.title') }}</h1>
            </div>
            <p class="page-subtitle">
              {{ $t('dryingForm.subtitle') }}
            </p>
          </div>

          <!-- Quick Preset Selector Bar -->
          <div class="preset-selector-bar">
            <span class="preset-lbl">{{ $t('dryingForm.presetLabel') }}</span>
            <button 
              type="button" 
              class="preset-pill" 
              :class="{ active: selectedPreset === 'ketan_grade_a' }"
              @click="applyPreset('ketan_grade_a')"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9A9 9 0 0 1 3 11a9 9 0 0 1 9-9z"></path>
                <path d="M12 7v10M8 11l4-4 4 4"></path>
              </svg>
              <span>{{ $t('dryingForm.presetKetan') }}</span>
            </button>
            <button 
              type="button" 
              class="preset-pill" 
              :class="{ active: selectedPreset === 'pangan_lokal' }"
              @click="applyPreset('pangan_lokal')"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
              </svg>
              <span>{{ $t('dryingForm.presetPangan') }}</span>
            </button>
            <button 
              type="button" 
              class="preset-pill" 
              :class="{ active: selectedPreset === 'teh_sangrai' }"
              @click="applyPreset('teh_sangrai')"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
              </svg>
              <span>{{ $t('dryingForm.presetTeh') }}</span>
            </button>
            <button 
              type="button" 
              class="preset-pill" 
              :class="{ active: selectedPreset === 'custom' }"
              @click="selectedPreset = 'custom'"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>{{ $t('dryingForm.presetCustom') }}</span>
            </button>
          </div>

          <!-- Hardware Connection Status Banner -->
          <div v-if="!isReadyToDry" class="hw-warning-banner">
            <div class="hw-warning-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div class="hw-warning-content">
              <div class="hw-warning-title-row">
                <strong class="hw-warning-title">{{ $t('dryingForm.hwNotConnectedTitle') }}</strong>
                <span class="badge-status-req">{{ $t('dryingForm.hwRequiredBadge') }}</span>
              </div>
              <p class="hw-warning-desc">
                {{ $t('dryingForm.hwNotConnectedDesc') }}
              </p>
              <div class="hw-warning-actions">
                <button type="button" class="btn-hw-connect" @click="openHardwareSetup">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
                  </svg>
                  <span>{{ $t('dryingForm.btnConnectEsp32') }}</span>
                </button>
                <button v-if="isAdmin" type="button" class="btn-hw-sim" @click="activateSimulationMode">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                  </svg>
                  <span>{{ $t('dryingForm.btnActivateSimulation') }}</span>
                </button>
              </div>
            </div>
          </div>

          <div v-else class="hw-ready-banner">
            <div class="hw-ready-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="hw-ready-text">
              <strong>{{ $t('dryingForm.telemetryStandbyLabel') }}</strong>
              <span>{{ isHardwareConnected ? $t('dryingForm.hwConnectedLiveDesc') : $t('dryingForm.simEngineActiveDesc') }}</span>
            </div>
          </div>

          <!-- 2 Columns: Form on Left, Live Simulation & Summary on Right -->
          <div class="form-layout-grid">
            <!-- Left Form Card -->
            <div class="form-card-main">
              <!-- Section 1: Identitas & Varietas -->
              <div class="form-section">
                <div class="section-head">
                  <div class="section-num">1</div>
                  <div>
                    <h3 class="section-title">{{ $t('dryingForm.sec1Title') }}</h3>
                    <p class="section-sub">{{ $t('dryingForm.sec1Sub') }}</p>
                  </div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.cropVarietyLabel') }}</label>
                    <input 
                      type="text" 
                      v-model="formData.cropVariety" 
                      :placeholder="$t('dryingForm.cropVarietyPlaceholder')" 
                      class="form-input"
                      required
                    />
                  </div>

                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.operatorLabel') }}</label>
                    <input 
                      type="text" 
                      v-model="formData.operatorName" 
                      :placeholder="$t('dryingForm.operatorPlaceholder')" 
                      class="form-input"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('dryingForm.trayPositionLabel') }}</label>
                  <select v-model="formData.trayLevel" class="form-input">
                    <option value="Semua Rak (Tingkat 1, 2, 3)">{{ $t('dryingForm.trayAllOption') }}</option>
                    <option value="Rak Bawah Saja (Tingkat 1)">{{ $t('dryingForm.trayBottomOption') }}</option>
                    <option value="Rak Tengah Saja (Tingkat 2)">{{ $t('dryingForm.trayMiddleOption') }}</option>
                    <option value="Rak Atas Saja (Tingkat 3)">{{ $t('dryingForm.trayTopOption') }}</option>
                  </select>
                </div>
              </div>

              <!-- Section 2: Parameter Massa & Kadar Air -->
              <div class="form-section">
                <div class="section-head">
                  <div class="section-num">2</div>
                  <div>
                    <h3 class="section-title">{{ $t('dryingForm.sec2Title') }}</h3>
                    <p class="section-sub">{{ $t('dryingForm.sec2Sub') }}</p>
                  </div>
                </div>

                <div class="form-grid-3">
                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.wetWeightLabel') }}</label>
                    <div class="input-with-unit">
                      <input 
                        type="number" 
                        step="0.5" 
                        v-model.number="formData.initialWeightKg" 
                        class="form-input unit-pad"
                        min="1"
                        required
                      />
                      <span class="unit-tag">kg</span>
                    </div>
                    <span class="helper-text">{{ $t('dryingForm.maxCapacityHelper') }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.initialMoistureLabel') }}</label>
                    <div class="input-with-unit">
                      <input 
                        type="number" 
                        step="0.1" 
                        v-model.number="formData.initialMoisturePercent" 
                        class="form-input unit-pad"
                        min="10"
                        max="60"
                        required
                      />
                      <span class="unit-tag">%</span>
                    </div>
                    <span class="helper-text">{{ $t('dryingForm.wetHarvestHelper') }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.targetMoistureLabel') }}</label>
                    <div class="input-with-unit">
                      <input 
                        type="number" 
                        step="0.1" 
                        v-model.number="formData.targetMoisturePercent" 
                        class="form-input unit-pad"
                        min="8"
                        max="20"
                        required
                      />
                      <span class="unit-tag">%</span>
                    </div>
                    <span class="helper-text text-green-dark">{{ $t('dryingForm.sniStandardHelper') }}</span>
                  </div>
                </div>
              </div>

              <!-- Section 3: Mode Pengeringan & Profil Suhu -->
              <div class="form-section">
                <div class="section-head">
                  <div class="section-num">3</div>
                  <div>
                    <h3 class="section-title">{{ $t('dryingForm.sec3Title') }}</h3>
                    <p class="section-sub">{{ $t('dryingForm.sec3Sub') }}</p>
                  </div>
                </div>

                <div class="mode-cards-grid">
                  <div 
                    class="mode-select-card" 
                    :class="{ active: formData.dryingMode === 'HYBRID_SOLAR_ELECTRIC' }"
                    @click="formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'"
                    role="button"
                    tabindex="0"
                    @keydown.enter.space="formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'"
                  >
                    <div class="mode-icon">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                      </svg>
                    </div>
                    <div class="mode-info">
                      <h4 class="mode-name">{{ $t('dryingForm.modeHybridName') }}</h4>
                      <p class="mode-desc">{{ $t('dryingForm.modeHybridDesc') }}</p>
                    </div>
                    <div class="mode-check-circle">
                      <div v-if="formData.dryingMode === 'HYBRID_SOLAR_ELECTRIC'" class="mode-check-dot"></div>
                    </div>
                  </div>

                  <div 
                    class="mode-select-card" 
                    :class="{ active: formData.dryingMode === 'PURE_SOLAR' }"
                    @click="formData.dryingMode = 'PURE_SOLAR'"
                    role="button"
                    tabindex="0"
                    @keydown.enter.space="formData.dryingMode = 'PURE_SOLAR'"
                  >
                    <div class="mode-icon">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                      </svg>
                    </div>
                    <div class="mode-info">
                      <h4 class="mode-name">{{ $t('dryingForm.modePureSolarName') }}</h4>
                      <p class="mode-desc">{{ $t('dryingForm.modePureSolarDesc') }}</p>
                    </div>
                    <div class="mode-check-circle">
                      <div v-if="formData.dryingMode === 'PURE_SOLAR'" class="mode-check-dot"></div>
                    </div>
                  </div>

                  <div 
                    class="mode-select-card" 
                    :class="{ active: formData.dryingMode === 'ELECTRIC_BOOSTER' }"
                    @click="formData.dryingMode = 'ELECTRIC_BOOSTER'"
                    role="button"
                    tabindex="0"
                    @keydown.enter.space="formData.dryingMode = 'ELECTRIC_BOOSTER'"
                  >
                    <div class="mode-icon">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                      </svg>
                    </div>
                    <div class="mode-info">
                      <h4 class="mode-name">{{ $t('dryingForm.modeBoosterName') }}</h4>
                      <p class="mode-desc">{{ $t('dryingForm.modeBoosterDesc') }}</p>
                    </div>
                    <div class="mode-check-circle">
                      <div v-if="formData.dryingMode === 'ELECTRIC_BOOSTER'" class="mode-check-dot"></div>
                    </div>
                  </div>
                </div>

                <div class="form-grid-2" style="margin-top: 16px;">
                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.criticalTempLimitLabel') }}</label>
                    <div class="input-with-unit">
                      <input 
                        type="number" 
                        step="0.5" 
                        v-model.number="formData.maxTempLimit" 
                        class="form-input unit-pad"
                      />
                      <span class="unit-tag">°C</span>
                    </div>
                    <span class="helper-text">{{ $t('dryingForm.turboExhaustHelper') }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">{{ $t('dryingForm.initialFanSpeedLabel') }}</label>
                    <select v-model="formData.initialFanSpeed" class="form-input">
                      <option value="AUTO">{{ $t('dryingForm.fanSpeedAuto') }}</option>
                      <option value="50">{{ $t('dryingForm.fanSpeedMedium') }}</option>
                      <option value="75">{{ $t('dryingForm.fanSpeedHigh') }}</option>
                      <option value="100">{{ $t('dryingForm.fanSpeedTurbo') }}</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Section 4: Catatan Sesi -->
              <div class="form-section">
                <div class="section-head">
                  <div class="section-num">4</div>
                  <div>
                    <h3 class="section-title">{{ $t('dryingForm.sec4Title') }}</h3>
                    <p class="section-sub">{{ $t('dryingForm.sec4Sub') }}</p>
                  </div>
                </div>

                <div class="form-group">
                  <textarea 
                    v-model="formData.notes" 
                    rows="2" 
                    class="form-input" 
                    :placeholder="$t('dryingForm.notesPlaceholder')"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- ===================================================================== -->
            <!-- RIGHT COLUMN: PREDICTIVE SIMULATION & SUMMARY CARD -->
            <!-- ===================================================================== -->
            <div class="summary-sidebar-col">
              <div class="summary-card-pro">
                <div class="sim-header">
                  <div class="sim-icon-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <line x1="18" y1="20" x2="18" y2="10"></line>
                      <line x1="12" y1="20" x2="12" y2="4"></line>
                      <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                  </div>
                  <div>
                    <h2 class="summary-heading">{{ $t('dryingForm.simHeading') }}</h2>
                    <p class="summary-sub">{{ $t('dryingForm.simSub') }}</p>
                  </div>
                </div>

                <!-- Mass Balance Projected Results -->
                <div class="mass-balance-box">
                  <span class="box-tag">{{ $t('dryingForm.massBalanceTitle') }}</span>
                  <div class="mb-grid">
                    <div class="mb-item">
                      <span class="mb-lbl">{{ $t('dryingForm.estFinalWeight') }}</span>
                      <span class="mb-val text-green-dark">{{ estimatedFinalWeightKg }} kg</span>
                    </div>
                    <div class="mb-item">
                      <span class="mb-lbl">{{ $t('dryingForm.evaporatedWater') }}</span>
                      <span class="mb-val text-purple">{{ estimatedWaterLossKg }} kg</span>
                    </div>
                    <div class="mb-item">
                      <span class="mb-lbl">{{ $t('dryingForm.grainYield') }}</span>
                      <span class="mb-val text-blue">{{ estimatedYieldPercent }}%</span>
                    </div>
                    <div class="mb-item">
                      <span class="mb-lbl">{{ $t('dryingForm.estDuration') }}</span>
                      <span class="mb-val text-orange">~{{ estimatedDurationHours }} {{ $t('dryingForm.hoursUnit') }}</span>
                    </div>
                  </div>
                </div>

                <!-- Projected Moisture Curve Mini Chart -->
                <div class="projected-chart-box">
                  <div class="proj-head">
                    <span class="proj-title">{{ $t('dryingForm.projectedKineticsTitle') }}</span>
                    <span class="proj-badge">{{ formData.initialMoisturePercent }}% → {{ formData.targetMoisturePercent }}%</span>
                  </div>
                  <div class="proj-svg-wrap">
                    <svg viewBox="0 0 360 110" class="w-full">
                      <line x1="25" y1="15" x2="340" y2="15" stroke="#F1F5F9"/>
                      <line x1="25" y1="50" x2="340" y2="50" stroke="#F1F5F9"/>
                      <line x1="25" y1="85" x2="340" y2="85" stroke="#CBD5E1"/>

                      <!-- Projected curve -->
                      <path :d="projectedCurvePath" fill="none" stroke="#0D631B" stroke-width="3" stroke-dasharray="4 2"/>
                      <circle :cx="25" :cy="projectedStartPoint.y" r="4" fill="#0D631B"/>
                      <circle :cx="340" :cy="projectedEndPoint.y" r="4" fill="#0D631B"/>
                    </svg>
                  </div>
                  <div class="proj-time-labels">
                    <span>{{ $t('dryingForm.hourInitial') }}</span>
                    <span>{{ $t('dryingForm.hourProjected', { hours: estimatedDurationHours }) }}</span>
                  </div>
                </div>

                <!-- Estimated Energy & Cost -->
                <div class="energy-est-box">
                  <div class="energy-row">
                    <div class="energy-col">
                      <span class="energy-lbl">{{ $t('dryingForm.estEnergy') }}</span>
                      <strong class="energy-val">{{ estimatedEnergyKwh }} kWh</strong>
                    </div>
                    <div class="energy-col text-right">
                      <span class="energy-lbl">{{ $t('dryingForm.estCost') }}</span>
                      <strong class="energy-val text-green-dark">Rp {{ estimatedCostIdr }}</strong>
                    </div>
                  </div>
                </div>

                <!-- Start & Cancel Actions -->
                <div class="summary-actions">
                  <button 
                    class="btn-start-action" 
                    :class="{ 'btn-start-disabled': !isReadyToDry }"
                    @click="handleStart"
                    :disabled="isSubmitting"
                  >
                    <svg width="15" height="15" viewBox="0 0 12 14" fill="currentColor">
                      <path d="M1.5 1.5L10.5 7L1.5 12.5V1.5Z"/>
                    </svg>
                    <span>{{ isSubmitting ? $t('dryingForm.btnStarting') : $t('dryingForm.btnStartDrying') }}</span>
                  </button>

                  <button class="btn-cancel-action" @click="$emit('cancel')">
                    {{ $t('dryingForm.btnCancelAndBack') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

    <!-- Hardware Disconnected Interception Modal -->
    <div v-if="isHardwareWarningModalOpen" class="hw-modal-overlay" @click.self="isHardwareWarningModalOpen = false">
      <div class="hw-modal-card">
        <div class="hw-modal-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
        </div>

        <h3 class="hw-modal-title">{{ $t('dryingForm.hwModalTitle') }}</h3>
        <p class="hw-modal-desc">
          {{ $t('dryingForm.hwModalDesc') }}
        </p>

        <div class="hw-modal-guide-box">
          <div class="guide-item">
            <span class="guide-bullet">1</span>
            <div>
              <strong>{{ $t('dryingForm.hwModalStep1Title') }}</strong>
              <p>{{ $t('dryingForm.hwModalStep1Desc') }}</p>
            </div>
          </div>
          <div class="guide-item">
            <span class="guide-bullet">2</span>
            <div>
              <strong>{{ $t('dryingForm.hwModalStep2Title') }}</strong>
              <p>{{ $t('dryingForm.hwModalStep2Desc') }}</p>
            </div>
          </div>
        </div>

        <div class="hw-modal-actions">
          <button type="button" class="btn-modal-connect" @click="handleModalConnect">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
            </svg>
            <span>{{ $t('dryingForm.hwModalBtnConnect') }}</span>
          </button>

          <button v-if="isAdmin" type="button" class="btn-modal-sim" @click="handleModalSimulateAndStart">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
            <span>{{ $t('dryingForm.hwModalBtnSimulate') }}</span>
          </button>

          <button type="button" class="btn-modal-cancel" @click="isHardwareWarningModalOpen = false">
            {{ $t('common.close') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bluetooth BLE & Wi-Fi Setup Modal -->
    <DeviceSetupBleModal :is-open="isWifiSetupOpen" @close="isWifiSetupOpen = false" />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { t } from '../../i18n'
import { batchService } from '../../services/batchService'
import { authService } from '../../services/authService'
import connectionManager from '../../services/connectionManager'
import DeviceSetupBleModal from '../../components/DeviceSetupBleModal.vue'
import DryingFormSkeleton from '../../components/DryingFormSkeleton.vue'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['start-process', 'cancel'])

const isInitialLoading = ref(true)
const selectedPreset = ref('ketan_grade_a')
const isSubmitting = ref(false)
const isWifiSetupOpen = ref(false)
const isHardwareWarningModalOpen = ref(false)

onMounted(() => {
  setTimeout(() => {
    isInitialLoading.value = false
  }, 350)
})

const isAdmin = computed(() => {
  return authService.isAdmin()
})

// Hardware & Simulation State Check
const isHardwareConnected = computed(() => {
  return connectionManager.state.sourceMode === 'hardware' && connectionManager.state.isHardwareActive
})

const isSimulationActive = computed(() => {
  return connectionManager.state.sourceMode === 'simulation'
})

const isReadyToDry = computed(() => {
  if (connectionManager.state.sourceMode === 'simulation') {
    return isAdmin.value
  }
  return isHardwareConnected.value
})

function openHardwareSetup() {
  isWifiSetupOpen.value = true
}

function activateSimulationMode() {
  if (!isAdmin.value) return
  connectionManager.setSourceMode('simulation')
}

function handleModalConnect() {
  isHardwareWarningModalOpen.value = false
  isWifiSetupOpen.value = true
}

async function handleModalSimulateAndStart() {
  if (!isAdmin.value) return
  activateSimulationMode()
  isHardwareWarningModalOpen.value = false
  await executeStartBatch()
}

const formData = reactive({
  cropVariety: 'Hanjeli Ketan Sukabumi (Grade A)',
  operatorName: 'Operator Green House',
  trayLevel: 'Semua Rak (Tingkat 1, 2, 3)',
  initialWeightKg: 135.0,
  initialMoisturePercent: 24.5,
  targetMoisturePercent: 12.0,
  dryingMode: 'HYBRID_SOLAR_ELECTRIC',
  maxTempLimit: 55.0,
  initialFanSpeed: 'AUTO',
  notes: 'Pengeringan gabah panen basah varietas unggul dengan kontrol suhu stabil.'
})

function applyPreset(presetKey) {
  selectedPreset.value = presetKey
  if (presetKey === 'ketan_grade_a') {
    formData.cropVariety = 'Hanjeli Ketan Sukabumi (Grade A)'
    formData.initialWeightKg = 135.0
    formData.initialMoisturePercent = 24.5
    formData.targetMoisturePercent = 11.5
    formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'
    formData.maxTempLimit = 52.0
    formData.notes = 'Standar ekspor mutu grade A, pengeringan suhu stabil tanpa panas kejut.'
  } else if (presetKey === 'pangan_lokal') {
    formData.cropVariety = 'Hanjeli Pangan Olahan Tepung'
    formData.initialWeightKg = 150.0
    formData.initialMoisturePercent = 26.0
    formData.targetMoisturePercent = 12.0
    formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'
    formData.maxTempLimit = 55.0
    formData.notes = 'Bahan baku tepung olahan pangan bergizi tinggi.'
  } else if (presetKey === 'teh_sangrai') {
    formData.cropVariety = 'Hanjeli Teh / Sangrai Aromatik'
    formData.initialWeightKg = 100.0
    formData.initialMoisturePercent = 22.0
    formData.targetMoisturePercent = 10.0
    formData.dryingMode = 'ELECTRIC_BOOSTER'
    formData.maxTempLimit = 58.0
    formData.notes = 'Kadar air sangat rendah untuk bahan teh seduh dan sangrai aromatik.'
  }
}

// Agritech Predictive Calculations
const estimatedFinalWeightKg = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const m0 = formData.initialMoisturePercent || 25
  const me = formData.targetMoisturePercent || 12
  if (m0 <= me) return w0.toFixed(1)
  const wf = w0 * ((100 - m0) / (100 - me))
  return wf.toFixed(1)
})

const estimatedWaterLossKg = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const wf = parseFloat(estimatedFinalWeightKg.value)
  return Math.max(0, w0 - wf).toFixed(1)
})

const estimatedYieldPercent = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const wf = parseFloat(estimatedFinalWeightKg.value)
  if (w0 === 0) return '88.5'
  return ((wf / w0) * 100).toFixed(1)
})

const estimatedDurationHours = computed(() => {
  const m0 = formData.initialMoisturePercent || 25
  const me = formData.targetMoisturePercent || 12
  const drop = Math.max(1, m0 - me)
  const rate = formData.dryingMode === 'ELECTRIC_BOOSTER' ? 1.8 : 1.45
  return (drop / rate).toFixed(1)
})

const estimatedEnergyKwh = computed(() => {
  const dur = parseFloat(estimatedDurationHours.value)
  const multiplier = formData.dryingMode === 'PURE_SOLAR' ? 0.35 : formData.dryingMode === 'ELECTRIC_BOOSTER' ? 2.5 : 1.25
  return (dur * multiplier).toFixed(1)
})

const estimatedCostIdr = computed(() => {
  const kwh = parseFloat(estimatedEnergyKwh.value)
  return Math.round(kwh * 1500).toLocaleString('id-ID')
})

// Projected Kinetics Curve Path
const projectedStartPoint = { x: 25, y: 20 }
const projectedEndPoint = { x: 340, y: 85 }

const projectedCurvePath = computed(() => {
  return `M 25 20 C 110 25, 200 65, 340 85`
})

async function handleStart() {
  // Validate hardware connection or simulation active
  if (!isReadyToDry.value) {
    isHardwareWarningModalOpen.value = true
    return
  }

  await executeStartBatch()
}

async function executeStartBatch() {
  isSubmitting.value = true
  try {
    const payload = {
      cropVariety: formData.cropVariety || 'Hanjeli Ketan Sukabumi',
      operatorName: formData.operatorName || 'Operator Green House',
      trayLevel: formData.trayLevel || 'Semua Rak (1, 2, 3)',
      initialWeightKg: Number(formData.initialWeightKg) || 100,
      initialMoisturePercent: Number(formData.initialMoisturePercent) || 24.5,
      targetMoisturePercent: Number(formData.targetMoisturePercent) || 12.0,
      dryingMode: formData.dryingMode || 'HYBRID_SOLAR_ELECTRIC',
      notes: formData.notes || 'Siklus pengeringan aktif baru.'
    }
    await batchService.create(payload)
    emit('start-process')
  } catch (err) {
    console.warn('Backend create batch response:', err.message)
    emit('start-process')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.drying-form-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.drying-form-main-content {
  padding: 32px 40px 48px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
}

.page-header-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.title-with-tag {
  display: flex;
  align-items: center;
  gap: 12px;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--color-text-title);
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* Preset Selector Bar */
.preset-selector-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  background: var(--color-white);
  padding: 12px 18px;
  border-radius: 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .preset-selector-bar {
  border-color: rgba(30, 58, 43, 0.6);
}

.preset-lbl {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.preset-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.preset-pill:hover {
  border-color: #0D631B;
}

.preset-pill.active {
  background: #E8F5E9;
  color: #0D631B;
  border-color: #0D631B;
  box-shadow: 0 1px 3px rgba(13, 99, 27, 0.15);
}

/* Hardware Status Banners */
.hw-warning-banner {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  background: #FFFBEB;
  border: 1.5px solid #FCD34D;
  border-radius: 14px;
  padding: 16px 20px;
  box-shadow: 0 2px 8px rgba(217, 119, 6, 0.08);
}

:global(.dark-theme) .hw-warning-banner {
  background: rgba(217, 119, 6, 0.12);
  border-color: rgba(217, 119, 6, 0.4);
}

.hw-warning-icon {
  width: 42px;
  height: 42px;
  min-width: 42px;
  border-radius: 10px;
  background: #FEF3C7;
  display: flex;
  align-items: center;
  justify-content: center;
}

:global(.dark-theme) .hw-warning-icon {
  background: rgba(217, 119, 6, 0.25);
}

.hw-warning-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.hw-warning-title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.hw-warning-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #92400E;
}

:global(.dark-theme) .hw-warning-title {
  color: #FBBF24;
}

.badge-status-req {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 2px 8px;
  border-radius: 6px;
  background: #FEE2E2;
  color: #DC2626;
  border: 1px solid #FCA5A5;
}

.hw-warning-desc {
  font-size: 13px;
  color: #78350F;
  margin: 0;
  line-height: 1.45;
}

:global(.dark-theme) .hw-warning-desc {
  color: #FDE68A;
}

.hw-warning-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 6px;
  flex-wrap: wrap;
}

.btn-hw-connect {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-hw-connect:hover {
  background: #094713;
}

.btn-hw-sim {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #FFFFFF;
  color: #92400E;
  border: 1px solid #D97706;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-hw-sim:hover {
  background: #FEF3C7;
}

:global(.dark-theme) .btn-hw-sim {
  background: #1E293B;
  color: #FBBF24;
  border-color: #F59E0B;
}

.hw-ready-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #F0FDF4;
  border: 1px solid #86EFAC;
  border-radius: 12px;
  padding: 12px 18px;
}

:global(.dark-theme) .hw-ready-banner {
  background: rgba(34, 197, 94, 0.1);
  border-color: rgba(34, 197, 94, 0.3);
}

.hw-ready-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #DCFCE7;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .hw-ready-icon {
  background: rgba(34, 197, 94, 0.2);
}

.hw-ready-text {
  font-size: 13px;
  color: #166534;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

:global(.dark-theme) .hw-ready-text {
  color: #4ADE80;
}

.hw-warning-mobile {
  flex-direction: column;
  padding: 12px 14px;
  gap: 10px;
}

.hw-ready-mobile {
  padding: 10px 14px;
}

/* 2 Columns Layout */
.form-layout-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 24px;
  align-items: start;
}

.form-card-main {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .form-card-main {
  border-color: rgba(30, 58, 43, 0.6);
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-border-subtle, #F1F5F9);
}

.form-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.section-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.section-title {
  font-size: 15.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.section-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 13.5px;
  transition: all 0.15s ease;
}

select.form-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px 16px;
  padding-right: 42px;
  cursor: pointer;
}

.form-input:focus {
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  outline-offset: 1px;
  border-color: #0D631B;
  background: var(--color-white);
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.1);
}

.input-with-unit {
  position: relative;
  display: flex;
  align-items: center;
}

.unit-pad {
  padding-right: 40px;
}

.unit-tag {
  position: absolute;
  right: 12px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-muted);
  pointer-events: none;
}

.helper-text {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

/* Mode Select Cards */
.mode-cards-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
}

.mode-select-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 13px 16px;
  border-radius: 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-bg-light);
  cursor: pointer;
  user-select: none;
  transition: all 0.18s ease;
}

:global(.dark-theme) .mode-select-card {
  border-color: rgba(30, 58, 43, 0.6);
  background: #0B1911;
}

.mode-select-card:hover {
  border-color: #94A3B8;
  transform: translateY(-1px);
}

.mode-select-card.active {
  border-color: #0D631B;
  background: rgba(13, 99, 27, 0.08);
  box-shadow: 0 1px 4px rgba(13, 99, 27, 0.12);
}

:global(.dark-theme) .mode-select-card.active {
  border-color: #4ADE80;
  background: rgba(74, 222, 128, 0.12);
}

.mode-icon {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 8px;
  background: rgba(13, 99, 27, 0.1);
  color: #0D631B;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .mode-icon {
  background: rgba(46, 125, 50, 0.25);
  color: #4ADE80;
}

.mode-info {
  flex: 1;
  min-width: 0;
}

.mode-name {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0 0 2px 0;
}

:global(.dark-theme) .mode-name {
  color: #FFFFFF;
}

.mode-desc {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .mode-desc {
  color: #94A3B8;
}

.mode-check-circle {
  width: 18px;
  height: 18px;
  min-width: 18px;
  border-radius: 50%;
  border: 2px solid rgba(148, 163, 184, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: auto;
  flex-shrink: 0;
  transition: all 0.15s ease;
}

.mode-select-card.active .mode-check-circle {
  border-color: #0D631B;
  background: #FFFFFF;
}

:global(.dark-theme) .mode-select-card.active .mode-check-circle {
  border-color: #4ADE80;
  background: #0B1911;
}

.mode-check-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #0D631B;
}

:global(.dark-theme) .mode-check-dot {
  background: #4ADE80;
}

/* Right Summary Sidebar */
.summary-sidebar-col {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.summary-card-pro {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .summary-card-pro {
  border-color: rgba(30, 58, 43, 0.6);
}

.sim-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sim-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #E8F5E9;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.summary-heading {
  font-size: 16.5px;
  font-weight: 800;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.summary-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.mass-balance-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

:global(.dark-theme) .mass-balance-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.box-tag {
  font-size: 10.5px;
  font-weight: 800;
  color: var(--color-text-muted);
  letter-spacing: 0.5px;
}

.mb-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mb-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.mb-lbl {
  color: var(--color-text-muted);
}

.mb-val {
  font-weight: 800;
}

.projected-chart-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

:global(.dark-theme) .projected-chart-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.proj-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.proj-title {
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text-title);
}

.proj-badge {
  font-size: 11px;
  font-weight: 700;
  color: #0D631B;
  background: #E8F5E9;
  padding: 2px 6px;
  border-radius: 4px;
}

.proj-svg-wrap {
  width: 100%;
  height: 110px;
}

.proj-time-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: var(--color-text-muted);
}

.energy-est-box {
  border-top: 1px solid rgba(203, 213, 225, 0.4);
  padding-top: 14px;
}

:global(.dark-theme) .energy-est-box {
  border-top-color: rgba(30, 58, 43, 0.5);
}

.energy-row {
  display: flex;
  justify-content: space-between;
}

.energy-lbl {
  display: block;
  font-size: 11px;
  color: var(--color-text-muted);
  margin-bottom: 2px;
}

.energy-val {
  font-size: 16px;
  color: var(--color-text-title);
}

.summary-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 6px;
}

.btn-start-action {
  width: 100%;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
  box-shadow: 0 4px 12px rgba(13, 99, 27, 0.25);
}

.btn-start-action:hover {
  background: #094713;
}

.btn-start-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-cancel-action {
  width: 100%;
  background: transparent;
  color: var(--color-text-muted);
  border: 1px solid rgba(203, 213, 225, 0.5);
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cancel-action:hover {
  background: var(--color-bg-light);
  color: var(--color-text-title);
}

/* Mobile Styling */
.mobile-content {
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

.mobile-intro-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
}

.back-link-btn {
  background: transparent;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  padding: 0;
  margin-bottom: 6px;
  transition: color 0.15s ease;
}

.back-link-btn:hover {
  color: #0D631B;
}

.m-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-text-title);
  margin: 6px 0 2px;
}

.intro-p {
  font-size: 13px;
  color: var(--color-text-muted);
  margin: 0;
}

.mobile-form-fields {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.field-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.field-label {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--color-text-title);
}

.field-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 14px;
}

select.field-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px 16px;
  padding-right: 40px;
  cursor: pointer;
}

.field-helper {
  font-size: 11px;
  color: #0D631B;
}

.m-calc-box {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.m-calc-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.mobile-actions-col {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-start-mobile {
  width: 100%;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

.btn-cancel-link {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 600;
  padding: 6px;
  cursor: pointer;
}

/* Modal Styling */
.hw-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.hw-modal-card {
  background: var(--color-white);
  border-radius: 18px;
  max-width: 480px;
  width: 100%;
  padding: 28px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 1px solid rgba(203, 213, 225, 0.6);
  animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

:global(.dark-theme) .hw-modal-card {
  border-color: rgba(30, 58, 43, 0.8);
}

@keyframes modalPop {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.hw-modal-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  background: #FEF3C7;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
}

:global(.dark-theme) .hw-modal-icon {
  background: rgba(217, 119, 6, 0.2);
}

.hw-modal-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--color-text-title);
  margin-bottom: 8px;
}

.hw-modal-desc {
  font-size: 13.5px;
  color: var(--color-text-muted);
  line-height: 1.5;
  margin-bottom: 20px;
}

.hw-modal-guide-box {
  width: 100%;
  background: var(--color-bg-light);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  text-align: left;
  margin-bottom: 24px;
  border: 1px solid rgba(203, 213, 225, 0.4);
}

:global(.dark-theme) .hw-modal-guide-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.guide-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.guide-bullet {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

.guide-item strong {
  font-size: 12.5px;
  color: var(--color-text-title);
  display: block;
}

.guide-item p {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 2px 0 0;
  line-height: 1.35;
}

.hw-modal-actions {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-modal-connect {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-modal-connect:hover {
  background: #094713;
}

.btn-modal-sim {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #FEF3C7;
  color: #92400E;
  border: 1px solid #F59E0B;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-modal-sim:hover {
  background: #FDE68A;
}

:global(.dark-theme) .btn-modal-sim {
  background: rgba(217, 119, 6, 0.2);
  color: #FBBF24;
  border-color: #F59E0B;
}

.btn-modal-cancel {
  width: 100%;
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  padding: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-modal-cancel:hover {
  color: var(--color-text-title);
}

/* Global utility */
.text-green-dark { color: #0D631B; }
.text-purple { color: #9333EA; }
.text-blue { color: #0284C7; }
.text-orange { color: #EA580C; }

/* Responsive Breakpoints */
@media (max-width: 1024px) {
  .form-layout-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .drying-form-main-content {
    padding: 0;
    gap: 16px;
  }

  .page-title {
    font-size: 22px;
  }

  .page-subtitle {
    font-size: 13.5px;
  }

  .form-card-main,
  .summary-card-pro {
    padding: 16px;
    border-radius: 12px;
  }

  .form-grid-3 {
    grid-template-columns: 1fr;
  }

  .form-grid-2 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 580px) {
  .hw-warning-banner {
    flex-direction: column;
    padding: 14px;
    gap: 12px;
  }

  .preset-selector-bar {
    padding: 10px 14px;
    gap: 8px;
  }
}

/* Page Transition for Skeleton */
.fade-drying-enter-active,
.fade-drying-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-drying-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.fade-drying-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
