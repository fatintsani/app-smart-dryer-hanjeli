<template>
    <header class="desktop-header">
        <!-- Left: Sidebar Toggle & Status -->
        <div class="header-left">
            <button
                class="header-action-btn sidebar-toggle-btn"
                @click="$emit('toggle-sidebar')"
                :title="
                    isSidebarCollapsed
                        ? currentLang === 'id'
                            ? 'Buka Sidebar'
                            : 'Expand Sidebar'
                        : currentLang === 'id'
                          ? 'Kecilkan Sidebar'
                          : 'Collapse Sidebar'
                "
            >
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="3"
                        y="3"
                        width="18"
                        height="18"
                        rx="2"
                        ry="2"
                    ></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                    <!-- Left arrow when expanded, Right arrow when collapsed -->
                    <path v-if="isSidebarCollapsed" d="m14 9 3 3-3 3"></path>
                    <path v-else d="m16 9-3 3 3 3"></path>
                </svg>
            </button>

            <!-- 1. System Operational Status Pill -->
            <div
                class="system-status-pill"
                :class="{ 'status-offline': !systemState.isSystemActive }"
            >
                <span
                    class="pulse-indicator"
                    :class="{ 'dot-offline': !systemState.isSystemActive }"
                ></span>
                <span class="status-title">
                    {{
                        systemState.isSystemActive
                            ? currentLang === "id"
                                ? "Sistem Aktif"
                                : "System Active"
                            : currentLang === "id"
                              ? "Sistem Nonaktif"
                              : "System Standby"
                    }}
                </span>
                <span class="status-sep">|</span>
                <span class="status-sub">Smart Room Dryer</span>
            </div>

            <!-- 2. Dual Connectivity Pill (Wi-Fi/MQTT vs BLE) -->
            <div
                class="conn-mode-pill cursor-pointer"
                :class="[
                    connectionMode,
                    { 'conn-offline': !isConnectionActive },
                ]"
                @click="toggleConnectionMode"
                :title="`Mode Koneksi: ${connectionMode === 'wifi' ? 'Wi-Fi + MQTT' : 'Bluetooth BLE'} (Klik untuk beralih mode)`"
            >
                <svg
                    v-if="connectionMode === 'wifi'"
                    width="13"
                    height="13"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
                <svg
                    v-else
                    width="13"
                    height="13"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <polyline
                        points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"
                    ></polyline>
                </svg>
                <span class="conn-mode-title">{{
                    connectionMode === "wifi" ? "Wi-Fi / MQTT" : "BLE"
                }}</span>
                <span
                    class="conn-dot"
                    :class="isConnectionActive ? 'dot-active' : 'dot-inactive'"
                ></span>
            </div>
        </div>

        <!-- Right: Language, Theme Toggle, Notification & Profile Menu -->
        <div class="header-right">
            <!-- 1. Language Selector Button (Vector SVG Flag Only) -->
            <div class="lang-selector-wrapper" ref="langDropdownRef">
                <button
                    class="header-action-btn lang-btn"
                    @click="isLangOpen = !isLangOpen"
                    :title="
                        currentLang === 'id'
                            ? 'Bahasa: Indonesia (Klik untuk ganti)'
                            : 'Language: English (Click to change)'
                    "
                >
                    <FlagIcon :code="currentLang" :size="22" />
                </button>

                <!-- Lang Dropdown Popup -->
                <div v-if="isLangOpen" class="lang-dropdown-menu">
                    <button
                        class="lang-option"
                        :class="{ active: currentLang === 'id' }"
                        @click="selectLang('id')"
                    >
                        <div class="lang-option-lead">
                            <FlagIcon code="id" :size="18" />
                            <span>Bahasa Indonesia</span>
                        </div>
                        <svg
                            v-if="currentLang === 'id'"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0D631B"
                            stroke-width="3"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>

                    <button
                        class="lang-option"
                        :class="{ active: currentLang === 'en' }"
                        @click="selectLang('en')"
                    >
                        <div class="lang-option-lead">
                            <FlagIcon code="en" :size="18" />
                            <span>English</span>
                        </div>
                        <svg
                            v-if="currentLang === 'en'"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0D631B"
                            stroke-width="3"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 2. Dark / Light Mode Toggle Button (Icon Only) -->
            <button
                class="header-action-btn theme-toggle-btn"
                @click="$emit('toggle-theme')"
                :title="
                    isDark
                        ? currentLang === 'id'
                            ? 'Beralih ke Mode Terang'
                            : 'Switch to Light Mode'
                        : currentLang === 'id'
                          ? 'Beralih ke Mode Gelap'
                          : 'Switch to Dark Mode'
                "
            >
                <!-- Sun icon (When in dark mode, click to go light) -->
                <svg
                    v-if="isDark"
                    class="theme-icon sun-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#FBBF24"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
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
                <!-- Moon icon (When in light mode, click to go dark) -->
                <svg
                    v-else
                    class="theme-icon moon-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#0F172A"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"
                    ></path>
                </svg>
            </button>

            <!-- 3. Notifications Bell with Alert Templates -->
            <div class="notif-wrapper" ref="notifDropdownRef">
                <button
                    class="header-action-btn notif-btn"
                    @click="isNotifOpen = !isNotifOpen"
                    :class="{
                        active: isNotifOpen,
                        'has-critical': hasCriticalAlert,
                    }"
                    :title="
                        currentLang === 'id'
                            ? 'Pemberitahuan & Alert'
                            : 'Notifications & Alerts'
                    "
                >
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"
                        ></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span
                        v-if="unreadCount > 0"
                        class="notif-badge"
                        :class="{ 'badge-critical': hasCriticalAlert }"
                    >
                        {{ unreadCount }}
                    </span>
                </button>

                <!-- Rich Alert Notification Dropdown -->
                <div v-if="isNotifOpen" class="notif-dropdown-menu">
                    <!-- Dropdown Header -->
                    <div class="notif-header">
                        <div class="notif-title-group">
                            <h4>
                                {{
                                    currentLang === "id"
                                        ? "Pusat Notifikasi"
                                        : "Notification"
                                }}
                            </h4>
                            <span class="notif-count"
                                >{{ unreadCount }}
                                {{
                                    currentLang === "id"
                                        ? "belum dibaca"
                                        : "unread"
                                }}</span
                            >
                        </div>
                        <div class="notif-header-actions">
                            <button
                                class="btn-text-action"
                                @click="markAllAsRead"
                                :title="
                                    currentLang === 'id'
                                        ? 'Tandai semua dibaca'
                                        : 'Mark all as read'
                                "
                            >
                                {{
                                    currentLang === "id"
                                        ? "Tandai Dibaca"
                                        : "Mark Read"
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- Alert Filter Chips -->
                    <div class="notif-filters">
                        <button
                            class="notif-filter-chip"
                            :class="{ active: notifFilter === 'all' }"
                            @click="notifFilter = 'all'"
                        >
                            {{ currentLang === "id" ? "Semua" : "All" }} ({{
                                notifications.length
                            }})
                        </button>
                        <button
                            class="notif-filter-chip"
                            :class="{ active: notifFilter === 'alert' }"
                            @click="notifFilter = 'alert'"
                        >
                            <span class="chip-dot alert-dot"></span>
                            {{
                                currentLang === "id"
                                    ? "Peringatan & Kritis"
                                    : "Alerts"
                            }}
                        </button>
                        <button
                            class="notif-filter-chip"
                            :class="{ active: notifFilter === 'system' }"
                            @click="notifFilter = 'system'"
                        >
                            <span class="chip-dot system-dot"></span>
                            {{ currentLang === "id" ? "Sistem" : "System" }}
                        </button>
                    </div>

                    <!-- Alert Notifications List -->
                    <div class="notif-list">
                        <div
                            v-for="notif in filteredNotifications"
                            :key="notif.id"
                            class="notif-card"
                            :class="[
                                `type-${notif.type}`,
                                { unread: notif.unread },
                            ]"
                            @click="markAsRead(notif.id)"
                        >
                            <!-- Severity Icon Box -->
                            <div class="notif-icon-box">
                                <!-- Critical -->
                                <svg
                                    v-if="notif.type === 'critical'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line
                                        x1="12"
                                        y1="16"
                                        x2="12.01"
                                        y2="16"
                                    ></line>
                                </svg>
                                <!-- Warning -->
                                <svg
                                    v-else-if="notif.type === 'warning'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                >
                                    <path
                                        d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                    ></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line
                                        x1="12"
                                        y1="17"
                                        x2="12.01"
                                        y2="17"
                                    ></line>
                                </svg>
                                <!-- Success -->
                                <svg
                                    v-else-if="notif.type === 'success'"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                >
                                    <path
                                        d="M22 11.08V12a10 10 0 1 1-5.93-9.14"
                                    ></path>
                                    <polyline
                                        points="22 4 12 14.01 9 11.01"
                                    ></polyline>
                                </svg>
                                <!-- Info -->
                                <svg
                                    v-else
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line
                                        x1="12"
                                        y1="16"
                                        x2="12"
                                        y2="12"
                                    ></line>
                                    <line
                                        x1="12"
                                        y1="8"
                                        x2="12.01"
                                        y2="8"
                                    ></line>
                                </svg>
                            </div>

                            <!-- Content details -->
                            <div class="notif-content-block">
                                <div class="notif-headline-row">
                                    <h5 class="notif-item-title">
                                        {{
                                            currentLang === "id"
                                                ? notif.title
                                                : notif.titleEn
                                        }}
                                    </h5>
                                    <span class="notif-time-tag">{{
                                        currentLang === "id"
                                            ? notif.time
                                            : notif.timeEn
                                    }}</span>
                                </div>
                                <p class="notif-item-desc">
                                    {{
                                        currentLang === "id"
                                            ? notif.message
                                            : notif.messageEn
                                    }}
                                </p>

                                <!-- Action Button in Alert Card -->
                                <div class="notif-item-footer">
                                    <button
                                        v-if="notif.actionLabel"
                                        class="notif-action-btn"
                                        @click.stop="triggerAlertAction(notif)"
                                    >
                                        <span>{{
                                            currentLang === "id"
                                                ? notif.actionLabel
                                                : notif.actionLabelEn
                                        }}</span>
                                        <svg
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <polyline
                                                points="9 18 15 12 9 6"
                                            ></polyline>
                                        </svg>
                                    </button>

                                    <button
                                        class="notif-dismiss-btn"
                                        @click.stop="
                                            dismissNotification(notif.id)
                                        "
                                        :title="
                                            currentLang === 'id'
                                                ? 'Hapus notifikasi ini'
                                                : 'Dismiss'
                                        "
                                    >
                                        <svg
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <line
                                                x1="18"
                                                y1="6"
                                                x2="6"
                                                y2="18"
                                            ></line>
                                            <line
                                                x1="6"
                                                y1="6"
                                                x2="18"
                                                y2="18"
                                            ></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="filteredNotifications.length === 0"
                            class="notif-empty-box"
                        >
                            <div class="notif-empty-icon">
                                <svg
                                    width="28"
                                    height="28"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#94A3B8"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"
                                    ></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                            </div>
                            <p>
                                {{
                                    currentLang === "id"
                                        ? "Tidak ada notifikasi dalam kategori ini."
                                        : "No notifications in this category."
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Dropdown Footer -->
                    <div class="notif-footer">
                        <div class="notif-live-indicator">
                            <span class="live-dot-pulse"></span>
                            <span>{{
                                currentLang === "id"
                                    ? "Sistem Monitoring IoT Terhubung"
                                    : "Live IoT Monitoring Connected"
                            }}</span>
                        </div>
                        <button
                            v-if="notifications.length > 0"
                            class="clear-all-btn"
                            @click="clearAllAlerts"
                            :title="
                                currentLang === 'id'
                                    ? 'Bersihkan semua notifikasi'
                                    : 'Clear all notifications'
                            "
                        >
                            <svg
                                width="13"
                                height="13"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"
                                ></path>
                            </svg>
                            <span>{{
                                currentLang === "id" ? "Bersihkan" : "Clear All"
                            }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="header-divider"></div>

            <!-- 4. User Profile Dropdown Menu -->
            <div class="profile-menu-container" ref="profileDropdownRef">
                <button
                    class="profile-trigger-btn"
                    @click="isProfileOpen = !isProfileOpen"
                    :class="{ active: isProfileOpen }"
                >
                    <div class="avatar-box">
                        <div class="avatar-circle">
                            <img
                                v-if="user?.avatarUrl || user?.avatar"
                                :src="user.avatarUrl || user.avatar"
                                alt="Avatar"
                                class="avatar-img-circle"
                            />
                            <span v-else>{{ userInitials }}</span>
                        </div>
                        <span class="avatar-online-dot"></span>
                    </div>

                    <div class="user-info-text">
                        <span class="user-display-name">{{ user.name }}</span>
                        <span class="user-role-badge">{{ user.role }}</span>
                    </div>

                    <svg
                        class="chevron-profile"
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <!-- Profile Dropdown Popup -->
                <div v-if="isProfileOpen" class="profile-dropdown-menu">
                    <!-- Profile Card Header -->
                    <div class="profile-card-header">
                        <div class="profile-header-avatar">
                            <img
                                v-if="user?.avatarUrl || user?.avatar"
                                :src="user.avatarUrl || user.avatar"
                                alt="Avatar"
                                class="avatar-img-circle"
                            />
                            <span v-else>{{ userInitials }}</span>
                        </div>
                        <div class="profile-header-info">
                            <strong class="ph-name">{{ user.name }}</strong>
                            <span class="ph-email">{{ user.email }}</span>
                            <span class="ph-location">{{ user.location }}</span>
                        </div>
                    </div>

                    <div class="menu-divider"></div>

                    <!-- Menu Items List -->
                    <div class="profile-menu-items">
                        <!-- Admin Dashboard Direct Shortcut (When User is Admin) -->
                        <button
                            v-if="user.role === 'ADMIN'"
                            class="profile-menu-btn admin-portal-btn"
                            @click="handleAdminDashboard"
                        >
                            <div class="menu-icon-box purple-box">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <rect
                                        x="3"
                                        y="3"
                                        width="7"
                                        height="7"
                                    ></rect>
                                    <rect
                                        x="14"
                                        y="3"
                                        width="7"
                                        height="7"
                                    ></rect>
                                    <rect
                                        x="14"
                                        y="14"
                                        width="7"
                                        height="7"
                                    ></rect>
                                    <rect
                                        x="3"
                                        y="14"
                                        width="7"
                                        height="7"
                                    ></rect>
                                </svg>
                            </div>
                            <div class="menu-btn-text">
                                <span class="menu-main-title text-purple">{{
                                    currentLang === "id"
                                        ? "Dashboard Admin"
                                        : "Admin Dashboard"
                                }}</span>
                                <span class="menu-sub-title">{{
                                    currentLang === "id"
                                        ? "Pusat kontrol & user"
                                        : "Control & users center"
                                }}</span>
                            </div>
                        </button>

                        <!-- Edit Profile -->
                        <button
                            class="profile-menu-btn"
                            @click="handleEditProfile"
                        >
                            <div class="menu-icon-box">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                    ></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="menu-btn-text">
                                <span class="menu-main-title">{{
                                    currentLang === "id"
                                        ? "Edit Profil"
                                        : "Edit Profile"
                                }}</span>
                                <span class="menu-sub-title">{{
                                    currentLang === "id"
                                        ? "Ubah nama & data pengguna"
                                        : "Change name & details"
                                }}</span>
                            </div>
                        </button>

                        <!-- Settings -->
                        <button
                            class="profile-menu-btn"
                            @click="handleSettings"
                        >
                            <div class="menu-icon-box">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="menu-btn-text">
                                <span class="menu-main-title">{{
                                    currentLang === "id"
                                        ? "Pengaturan Akun"
                                        : "Account Settings"
                                }}</span>
                                <span class="menu-sub-title">{{
                                    currentLang === "id"
                                        ? "Preferensi & koneksi alat"
                                        : "Preferences & hardware"
                                }}</span>
                            </div>
                        </button>

                        <!-- Guide & Help -->
                        <button class="profile-menu-btn" @click="handleGuide">
                            <div class="menu-icon-box">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path
                                        d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"
                                    ></path>
                                    <line
                                        x1="12"
                                        y1="17"
                                        x2="12.01"
                                        y2="17"
                                    ></line>
                                </svg>
                            </div>
                            <div class="menu-btn-text">
                                <span class="menu-main-title">{{
                                    currentLang === "id"
                                        ? "Pusat Bantuan & Panduan"
                                        : "Help & Guide Center"
                                }}</span>
                                <span class="menu-sub-title">{{
                                    currentLang === "id"
                                        ? "Petunjuk pemakaian alat"
                                        : "Hardware usage instructions"
                                }}</span>
                            </div>
                        </button>
                    </div>

                    <div class="menu-divider"></div>

                    <!-- Logout Action Button -->
                    <div class="profile-menu-footer">
                        <button class="menu-logout-btn" @click="handleLogout">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                ></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>{{
                                currentLang === "id"
                                    ? "Keluar dari Akun"
                                    : "Log Out"
                            }}</span>
                        </button>
                        <div class="profile-menu-version-info">
                            <span
                                >Smart Room Dryer <strong>v1.0.0</strong></span
                            >
                            <span class="version-dot-sep">•</span>
                            <span>CoE STAS-RG</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import FlagIcon from "./FlagIcon.vue";
import { alertService } from "../services/alertService";
import { socketService } from "../services/socketService";
import { simulatorService } from "../services/simulatorService";
import { settingsService, systemState } from "../services/settingsService";
import connectionManager from "../services/connectionManager";

const props = defineProps({
    isDark: {
        type: Boolean,
        default: false,
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
    currentLang: {
        type: String,
        default: "id",
    },
    user: {
        type: Object,
        default: () => ({
            name: "Dr. Ir. Fatin Tsani",
            email: "admin@hanjeli.com",
            role: "ADMIN",
            location:
                "Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat 43175, Indonesia",
        }),
    },
});

const emit = defineEmits([
    "toggle-sidebar",
    "toggle-theme",
    "change-lang",
    "edit-profile",
    "settings",
    "navigate",
    "logout",
    "open-simulator",
    "open-ai-copilot",
]);

const isProfileOpen = ref(false);
const isLangOpen = ref(false);
const notifFilter = ref("all"); // 'all', 'alert', 'system'

const connectionMode = computed(() => connectionManager.state.mode);
const isConnectionActive = computed(() => connectionManager.state.isConnected);
const toggleConnectionMode = () => {
    const next =
        connectionMode.value === "wifi"
            ? "ble"
            : connectionMode.value === "ble"
              ? "usb"
              : "wifi";
    connectionManager.setConnectionMode(next);
};

const notifications = ref([]);
let alertSyncTimer = null;

function transformAlert(a) {
    const type =
        a.type === "DANGER" || a.level === "CRITICAL"
            ? "critical"
            : a.type === "WARNING" || a.level === "WARNING"
              ? "warning"
              : a.type === "SUCCESS" || a.level === "SUCCESS"
                ? "success"
                : "info";
    const timeStr =
        a.time ||
        (a.createdAt
            ? new Date(a.createdAt).toLocaleTimeString("id-ID", {
                  hour: "2-digit",
                  minute: "2-digit",
              })
            : "Baru saja");
    const timeEnStr = a.timeEn || timeStr;

    return {
        id: a.id,
        type,
        title: a.title,
        titleEn: a.titleEn || a.title,
        message: a.message,
        messageEn: a.messageEn || a.message,
        time: timeStr,
        timeEn: timeEnStr,
        unread: !a.isRead && !a.is_read,
        actionLabel:
            a.actionLabel ||
            (type === "critical" || type === "warning"
                ? "Lihat Monitoring"
                : "Lihat Detail"),
        actionLabelEn:
            a.actionLabelEn ||
            (type === "critical" || type === "warning"
                ? "Live Monitoring"
                : "View Details"),
        route:
            a.route ||
            (a.category === "SENSOR"
                ? "monitoring"
                : a.category === "BATCH"
                  ? "history"
                  : a.category === "DEVICE"
                    ? "guide"
                    : "monitoring"),
    };
}

async function loadAlerts() {
    try {
        const res = await alertService.getAll();
        const list = Array.isArray(res) ? res : res?.alerts || [];
        notifications.value = list.map(transformAlert);
    } catch (err) {
        console.warn("Could not load alerts from DB:", err.message);
    }
}

onMounted(() => {
    loadAlerts();

    // Real-time synchronization with database every 5 seconds
    alertSyncTimer = setInterval(loadAlerts, 5000);

    socketService.on("alert_new", (newAlert) => {
        notifications.value.unshift(transformAlert(newAlert));
    });
});

onUnmounted(() => {
    if (alertSyncTimer) clearInterval(alertSyncTimer);
    socketService.off("alert_new");
});

const unreadCount = computed(() => {
    return notifications.value.filter((n) => n.unread).length;
});

const hasCriticalAlert = computed(() => {
    return notifications.value.some((n) => n.unread && n.type === "critical");
});

const filteredNotifications = computed(() => {
    if (notifFilter.value === "alert") {
        return notifications.value.filter(
            (n) => n.type === "critical" || n.type === "warning",
        );
    }
    if (notifFilter.value === "system") {
        return notifications.value.filter(
            (n) => n.type === "info" || n.type === "success",
        );
    }
    return notifications.value;
});

async function markAsRead(id) {
    const notif = notifications.value.find((n) => n.id === id);
    if (notif) notif.unread = false;
    try {
        await alertService.markAsRead(id);
    } catch (e) {
        console.warn("Mark as read note:", e.message);
    }
}

async function markAllAsRead() {
    notifications.value.forEach((n) => {
        n.unread = false;
    });
    try {
        await alertService.markAllAsRead();
    } catch (e) {
        console.warn("Mark all as read note:", e.message);
    }
}

async function dismissNotification(id) {
    notifications.value = notifications.value.filter((n) => n.id !== id);
    try {
        await alertService.deleteAlert(id);
    } catch (e) {
        console.warn("Dismiss alert note:", e.message);
    }
}

async function clearAllAlerts() {
    notifications.value = [];
    try {
        await alertService.clearAll();
    } catch (e) {
        console.warn("Clear all alerts note:", e.message);
    }
}

function triggerAlertAction(notif) {
    markAsRead(notif.id);
    isNotifOpen.value = false;
    if (notif.route) {
        emit("navigate", notif.route);
    }
}

const isNotifOpen = ref(false);
const profileDropdownRef = ref(null);
const langDropdownRef = ref(null);
const notifDropdownRef = ref(null);

const userInitials = computed(() => {
    if (!props.user?.name) return "PA";
    const parts = props.user.name.trim().split(" ");
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return parts[0].slice(0, 2).toUpperCase();
});

function selectLang(lang) {
    emit("change-lang", lang);
    isLangOpen.value = false;
}

function handleAdminDashboard() {
    isProfileOpen.value = false;
    emit("navigate", "admin");
}

function handleEditProfile() {
    isProfileOpen.value = false;
    emit("edit-profile");
}

function handleSettings() {
    isProfileOpen.value = false;
    emit("settings");
}

function handleGuide() {
    isProfileOpen.value = false;
    emit("navigate", "guide");
}

function handleLogout() {
    isProfileOpen.value = false;
    emit("logout");
}

function handleClickOutside(e) {
    if (
        profileDropdownRef.value &&
        !profileDropdownRef.value.contains(e.target)
    ) {
        isProfileOpen.value = false;
    }
    if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) {
        isLangOpen.value = false;
    }
    if (notifDropdownRef.value && !notifDropdownRef.value.contains(e.target)) {
        isNotifOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.desktop-header {
    height: 64px;
    background: #ffffff;
    border-bottom: 1px solid var(--color-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 28px;
    position: sticky;
    top: 0;
    z-index: 30;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.system-status-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f1f8f1;
    border: 1px solid #c8e6c9;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
}

.pulse-indicator {
    width: 8px;
    height: 8px;
    background: #0d631b;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
    }
    70% {
    }
    100% {
    }
}

.status-title {
    font-weight: 700;
    color: #0d631b;
}

.status-sep {
    color: #a5d6a7;
}

.status-sub {
    color: #40493d;
    font-weight: 500;
}

.header-version-tag {
    font-size: 10px;
    font-weight: 700;
    color: #0d631b;
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    padding: 1px 5px;
    border-radius: 4px;
    letter-spacing: 0.3px;
    line-height: 1.2;
}

/* Hardware Simulator Trigger Button */
.sim-trigger-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    padding: 6px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
}

.sim-trigger-btn:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}

.sim-trigger-btn.active {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
}

.system-status-pill.status-offline {
    background: #fef2f2;
    border-color: #fecaca;
}

.system-status-pill.status-offline .status-title {
    color: #dc2626;
}

.system-status-pill.status-offline .status-sep {
    color: #fca5a5;
}

.dot-offline {
    background: #ef4444 !important;
    animation: none !important;
}

/* Environment Pill */
.env-mode-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.02em;
}

.env-tag {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 4px;
}

.env-local {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}

.env-local .env-tag {
    background: #3b82f6;
    color: #ffffff;
}

.env-prod {
    background: #faf5ff;
    border: 1px solid #e9d5ff;
    color: #7e22ce;
}

.env-prod .env-tag {
    background: #9333ea;
    color: #ffffff;
}

/* Hardware / Operator Mode Badges */
.hardware-mode-badge,
.operator-mode-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.hardware-mode-badge {
    background: #f0fdf4;
    border: 1px solid #86efac;
    color: #15803d;
}

.hw-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #16a34a;
    animation: pulse 1.8s infinite;
}

.badge-sim {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.badge-sim .mode-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
}

.badge-hw {
    background: #f0fdf4;
    border: 1px solid #86efac;
    color: #15803d;
}

.badge-hw .mode-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16a34a;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Seamless & Borderless Action Buttons */
.header-action-btn {
    width: 38px;
    height: 38px;
    padding: 0;
    background: transparent;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #334155;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
}

.header-action-btn:hover {
    background: rgba(0, 0, 0, 0.06);
    color: #0f172a;
    transform: translateY(-1px);
}

.theme-icon {
    width: 20px;
    height: 20px;
}

.notif-btn {
    position: relative;
}

.notif-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ba1a1a;
    color: white;
    font-size: 10px;
    font-weight: 700;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ffffff;
}

.header-divider {
    width: 1px;
    height: 24px;
    background: #e2e8f0;
    margin: 0 6px;
}

/* Lang Dropdown */
.lang-selector-wrapper {
    position: relative;
}

.lang-dropdown-menu {
    position: absolute;
    top: 46px;
    right: 0;
    width: 200px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 6px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    z-index: 50;
}

.lang-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 8px;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    text-align: left;
}

.lang-option-lead {
    display: flex;
    align-items: center;
    gap: 10px;
}

.lang-option:hover {
    background: #f1f5f9;
}

.lang-option.active {
    background: #e8f5e9;
    color: #0d631b;
    font-weight: 700;
}

.notif-btn.has-critical .notif-badge {
    background: #ba1a1a;
    animation: badgeBlink 1.5s infinite;
}

@keyframes badgeBlink {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.notif-dropdown-menu {
    position: absolute;
    top: 48px;
    right: 0;
    width: 380px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    z-index: 60;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
}

.notif-title-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.notif-title-group h4 {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

.notif-count {
    font-size: 11px;
    background: #e8f5e9;
    color: #0d631b;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
}

.btn-text-action {
    font-size: 12px;
    font-weight: 600;
    color: #0d631b;
    background: transparent;
    padding: 4px 8px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-text-action:hover {
    background: #e8f5e9;
}

/* Filter Chips */
.notif-filters {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 2px;
}

.notif-filter-chip {
    padding: 5px 10px;
    border-radius: 20px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.15s;
}

.notif-filter-chip.active {
    background: #0d631b;
    border-color: #0d631b;
    color: #ffffff;
}

.chip-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.alert-dot {
    background: #ba1a1a;
}
.system-dot {
    background: #005db7;
}
.notif-filter-chip.active .chip-dot {
    background: #ffffff;
}

/* Notification List */
.notif-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 340px;
    overflow-y: auto;
    padding-right: 2px;
}

.notif-card {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    cursor: pointer;
}

.notif-card:hover {
    transform: translateY(-1px);
}

.notif-card.unread {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Severity borders & colors */
.notif-card.type-critical {
    border-left: 4px solid #ba1a1a;
}
.notif-card.type-critical .notif-icon-box {
    background: rgba(186, 26, 26, 0.1);
    color: #ba1a1a;
}

.notif-card.type-warning {
    border-left: 4px solid #d97706;
}
.notif-card.type-warning .notif-icon-box {
    background: rgba(217, 119, 6, 0.1);
    color: #d97706;
}

.notif-card.type-success {
    border-left: 4px solid #0d631b;
}
.notif-card.type-success .notif-icon-box {
    background: rgba(13, 99, 27, 0.1);
    color: #0d631b;
}

.notif-card.type-info {
    border-left: 4px solid #005db7;
}
.notif-card.type-info .notif-icon-box {
    background: rgba(0, 93, 183, 0.1);
    color: #005db7;
}

.notif-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notif-content-block {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.notif-headline-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 6px;
}

.notif-item-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 16px;
}

.notif-time-tag {
    font-size: 10px;
    color: #94a3b8;
    white-space: nowrap;
}

.notif-item-desc {
    font-size: 11.5px;
    color: #475569;
    line-height: 15px;
}

.notif-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
}

.notif-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #0d631b;
    background: rgba(13, 99, 27, 0.08);
    padding: 3px 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
}

.notif-action-btn:hover {
    background: #0d631b;
    color: #ffffff;
}

.notif-dismiss-btn {
    background: transparent;
    color: #94a3b8;
    padding: 3px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.notif-dismiss-btn:hover {
    background: #f1f5f9;
    color: #ba1a1a;
}

/* Empty State */
.notif-empty-box {
    padding: 30px 16px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #94a3b8;
    font-size: 12px;
}

.notif-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Dropdown Footer */
.notif-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 8px;
    border-top: 1px solid var(--color-border-subtle, #f1f5f9);
}

.notif-live-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #10b981;
}

.live-dot-pulse {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    animation: livePulse 2s infinite;
}

@keyframes livePulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.3);
        opacity: 0.6;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.clear-all-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    background: transparent;
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s;
}

.clear-all-btn:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #ba1a1a;
}

/* Profile Trigger & Dropdown */
.profile-menu-container {
    position: relative;
}

.profile-trigger-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 10px 4px 4px;
    background: transparent;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.2s;
}

.profile-trigger-btn:hover,
.profile-trigger-btn.active {
    background: rgba(0, 0, 0, 0.05);
}

.avatar-box {
    position: relative;
}

.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2e7d32 0%, #0d631b 100%);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar-img-circle {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-online-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background: #22c55e;
    border-radius: 50%;
    border: 2px solid #ffffff;
}

.user-info-text {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.user-display-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 16px;
}

.user-role-badge {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

.chevron-profile {
    color: #64748b;
}

/* Profile Dropdown Box */
.profile-dropdown-menu {
    position: absolute;
    top: 52px;
    right: 0;
    width: 270px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px;
    z-index: 50;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.profile-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 4px 6px 8px;
}

.profile-header-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2e7d32 0%, #0d631b 100%);
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.profile-header-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow: hidden;
}

.ph-name {
    font-size: 14px;
    color: #0f172a;
    font-weight: 700;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

.ph-email {
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

.ph-location {
    font-size: 11px;
    color: #0d631b;
    font-weight: 600;
    margin-top: 2px;
}

.menu-divider {
    height: 1px;
    background: #f1f5f9;
}

.profile-menu-items {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.profile-menu-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 10px;
    border-radius: 8px;
    background: transparent;
    width: 100%;
    text-align: left;
    transition: background 0.15s;
}

.profile-menu-btn:hover {
    background: #f8fafc;
}

.menu-icon-box {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d631b;
    flex-shrink: 0;
}

.menu-btn-text {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.menu-main-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
}

.menu-sub-title {
    font-size: 11px;
    color: #94a3b8;
}

.profile-menu-footer {
    padding-top: 4px;
}

.menu-logout-btn {
    width: 100%;
    padding: 9px 12px;
    background: #fee2e2;
    border-radius: 8px;
    color: #991b1b;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s;
    cursor: pointer;
}

.menu-logout-btn:hover {
    background: #fca5a5;
    color: #7f1d1d;
}

.profile-menu-version-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px 2px;
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

.profile-menu-version-info strong {
    color: #0d631b;
    font-weight: 700;
}

.version-dot-sep {
    color: #cbd5e1;
}

.dark .profile-menu-version-info,
:global(.dark-theme) .profile-menu-version-info {
    color: #94a3b8;
}

.dark .profile-menu-version-info strong,
:global(.dark-theme) .profile-menu-version-info strong {
    color: #4ade80;
}

.dark .header-version-tag,
:global(.dark-theme) .header-version-tag {
    color: #4ade80;
    background: rgba(74, 222, 128, 0.15);
    border-color: rgba(74, 222, 128, 0.3);
}

/* Dual Connectivity Pill */
.conn-mode-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.2s ease;
    user-select: none;
}

.conn-mode-pill.wifi {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.dark .conn-mode-pill.wifi {
    background: rgba(6, 95, 70, 0.3);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.3);
}

.conn-mode-pill.ble {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.dark .conn-mode-pill.ble {
    background: rgba(30, 64, 175, 0.3);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.3);
}

.conn-mode-pill.conn-offline {
    background: #f8fafc;
    color: #64748b;
    border-color: #cbd5e1;
}

.dark .conn-mode-pill.conn-offline {
    background: #1e293b;
    color: #94a3b8;
    border-color: #334155;
}

.conn-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.conn-dot.dot-active {
    background: #10b981;
}

.conn-dot.dot-inactive {
    background: #94a3b8;
}
</style>
