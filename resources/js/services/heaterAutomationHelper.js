/**
 * heaterAutomationHelper.js
 * Menerjemahkan parameter heaterReason / kondisi sensor dari mikrokontroler ESP32
 * ke format yang ramah pengguna bagi operator & penguji akademik.
 *
 * Format tampilan yang dihasilkan:
 * Status: MENYALA | STANDBY
 * Alasan: <Penjelasan ramah operator>
 */

export function resolveHeaterReason(reasonCode, context = {}) {
  const {
    isHeaterActive = false,
    tempInternal = null,
    humidityInternal = null,
    isRaining = false,
    isWithinNtpSchedule = false,
    isOverrideActive = false,
    isHardwareActive = true
  } = context

  // Normalisasi kode alasan dari ESP32
  const raw = String(reasonCode || '').trim().toUpperCase()

  // 1. Kasus Hardware Belum Terhubung
  if (!isHardwareActive) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-standby',
      code: 'HARDWARE_OFFLINE',
      shortReason: 'Menunggu Alat Terhubung',
      fullReason: 'Alat mikrokontroler ESP32 belum terhubung atau belum mengirimkan data sensor.',
      icon: 'clock',
      isWarning: false
    }
  }

  // 2. Matching berdasarkan raw reason code dari ESP32
  if (raw.includes('LOCKOUT') || raw.includes('OVER_TEMPERATURE') || (tempInternal !== null && tempInternal >= 50.0)) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY (PROTEKSI)',
      statusText: 'STANDBY',
      statusClass: 'status-lockout',
      badgeClass: 'badge-lockout',
      code: 'OVER_TEMPERATURE_LOCKOUT',
      shortReason: 'Proteksi Suhu Maksimal (≥50°C)',
      fullReason: 'Suhu telah mencapai batas keselamatan (≥50°C) sehingga pemanas dinonaktifkan sementara demi melindungi mutu gabah hanjeli.',
      icon: 'shield',
      isWarning: true
    }
  }

  if (raw.includes('JADWAL') || raw.includes('NIGHT_SCHEDULE')) {
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-on',
      code: 'NIGHT_SCHEDULE_AUTO_ON',
      shortReason: 'Jadwal Malam NTP (18:00 - 05:00 WIB)',
      fullReason: 'Jadwal operasional malam (18:00 - 05:00 WIB): Pemanas otomatis aktif menjaga suhu ruang tanpa sinar matahari.',
      icon: 'moon',
      isWarning: false
    }
  }

  if (raw.includes('HUJAN') || raw.includes('RAIN')) {
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-on',
      code: 'AUTO_RAIN_DEMAND',
      shortReason: 'Hujan & Suhu Rendah',
      fullReason: 'Sistem mendeteksi kondisi hujan dan suhu lingkungan yang rendah sehingga pemanas dinyalakan otomatis.',
      icon: 'rain',
      isWarning: false
    }
  }

  if (raw.includes('HIGH_HUMIDITY') || raw.includes('LEMBAP')) {
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-on',
      code: 'AUTO_HIGH_HUMIDITY',
      shortReason: 'Kelembapan Udara Tinggi (≥70% RH)',
      fullReason: 'Kelembapan udara ruangan tinggi (≥70% RH); pemanas otomatis aktif untuk membantu mengeringkan udara lembap.',
      icon: 'droplet',
      isWarning: false
    }
  }

  if (raw.includes('COLD_TEMPERATURE') || raw.includes('DINGIN')) {
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-on',
      code: 'AUTO_COLD_TEMPERATURE',
      shortReason: 'Suhu Ruangan Dingin (≤35°C)',
      fullReason: 'Suhu ruangan turun di bawah batas ideal (≤35°C); pemanas otomatis aktif memulihkan suhu optimal pengeringan.',
      icon: 'thermometer',
      isWarning: false
    }
  }

  if (raw.includes('STANDBY') || raw.includes('OPTIMAL_DAY') || raw.includes('SIANG CERAH')) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-standby',
      code: 'STANDBY_OPTIMAL_DAY',
      shortReason: 'Siang Hari Cerah & Hangat',
      fullReason: 'Siang hari cuaca cerah dan suhu ruang mencukupi; pemanas siaga dan memprioritaskan panas alami dari sinar matahari.',
      icon: 'sun',
      isWarning: false
    }
  }

  if (raw.includes('REMOTE_ON') || raw.includes('MANUAL_ON')) {
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA (MANUAL)',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-manual',
      code: 'REMOTE_ON_WEB',
      shortReason: 'Kendali Manual Operator (ON)',
      fullReason: 'Pemanas dinyalakan secara manual oleh operator melalui tombol di website (Mode Override).',
      icon: 'hand',
      isWarning: false
    }
  }

  if (raw.includes('REMOTE_OFF') || raw.includes('MANUAL_OFF')) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY (MANUAL)',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-manual',
      code: 'REMOTE_OFF_WEB',
      shortReason: 'Kendali Manual Operator (OFF)',
      fullReason: 'Pemanas dimatikan secara manual oleh operator melalui tombol di website (Mode Override).',
      icon: 'hand',
      isWarning: false
    }
  }

  if (raw.includes('DHT22_ERROR')) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY (SENSOR ERROR)',
      statusText: 'STANDBY',
      statusClass: 'status-lockout',
      badgeClass: 'badge-lockout',
      code: 'DHT22_ERROR',
      shortReason: 'Sensor DHT22 Tidak Terbaca',
      fullReason: 'Sensor suhu DHT22 tidak terbaca dengan benar; pemanas otomatis dimatikan demi keselamatan sistem (Safety Shut-Off).',
      icon: 'shield',
      isWarning: true
    }
  }

  if (raw.includes('BH1750_ERROR')) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-standby',
      code: 'BH1750_ERROR',
      shortReason: 'Sensor Cahaya Error',
      fullReason: 'Sensor intensitas cahaya BH1750 mengalami gangguan pembacaan, sistem beralih ke estimasi terjadwal.',
      icon: 'clock',
      isWarning: false
    }
  }

  if (raw.includes('WAITING_FOR_NTP')) {
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-standby',
      code: 'WAITING_FOR_NTP_TIME',
      shortReason: 'Sinkronisasi Waktu NTP',
      fullReason: 'ESP32 sedang menyinkronkan waktu dengan server NTP WIB sebelum memulai jadwal otomatis.',
      icon: 'clock',
      isWarning: false
    }
  }

  // 3. Fallback jika reasonCode tidak dikirim atau tidak dikenal (Evaluasi dari telemetri real-time)
  if (isOverrideActive) {
    if (isHeaterActive) {
      return {
        status: 'MENYALA',
        statusLabel: 'MENYALA (MANUAL)',
        statusText: 'MENYALA',
        statusClass: 'status-on',
        badgeClass: 'badge-manual',
        code: 'REMOTE_ON_WEB',
        shortReason: 'Kendali Manual Operator (ON)',
        fullReason: 'Pemanas dinyalakan secara manual oleh operator melalui tombol di website (Mode Override).',
        icon: 'hand',
        isWarning: false
      }
    } else {
      return {
        status: 'STANDBY',
        statusLabel: 'STANDBY (MANUAL)',
        statusText: 'STANDBY',
        statusClass: 'status-standby',
        badgeClass: 'badge-manual',
        code: 'REMOTE_OFF_WEB',
        shortReason: 'Kendali Manual Operator (OFF)',
        fullReason: 'Pemanas dimatikan secara manual oleh operator melalui tombol di website (Mode Override).',
        icon: 'hand',
        isWarning: false
      }
    }
  }

  if (isHeaterActive) {
    if (isWithinNtpSchedule) {
      return {
        status: 'MENYALA',
        statusLabel: 'MENYALA',
        statusText: 'MENYALA',
        statusClass: 'status-on',
        badgeClass: 'badge-on',
        code: 'NIGHT_SCHEDULE_AUTO_ON',
        shortReason: 'Jadwal Malam NTP (18:00 - 05:00 WIB)',
        fullReason: 'Jadwal operasional malam (18:00 - 05:00 WIB): Pemanas otomatis aktif menjaga suhu ruang tanpa sinar matahari.',
        icon: 'moon',
        isWarning: false
      }
    }
    if (isRaining) {
      return {
        status: 'MENYALA',
        statusLabel: 'MENYALA',
        statusText: 'MENYALA',
        statusClass: 'status-on',
        badgeClass: 'badge-on',
        code: 'AUTO_RAIN_DEMAND',
        shortReason: 'Hujan & Suhu Rendah',
        fullReason: 'Sistem mendeteksi kondisi hujan dan suhu lingkungan yang rendah sehingga pemanas dinyalakan otomatis.',
        icon: 'rain',
        isWarning: false
      }
    }
    if (humidityInternal !== null && humidityInternal >= 70.0) {
      return {
        status: 'MENYALA',
        statusLabel: 'MENYALA',
        statusText: 'MENYALA',
        statusClass: 'status-on',
        badgeClass: 'badge-on',
        code: 'AUTO_HIGH_HUMIDITY',
        shortReason: 'Kelembapan Udara Tinggi (≥70% RH)',
        fullReason: 'Kelembapan udara ruangan tinggi (≥70% RH); pemanas otomatis aktif untuk membantu mengeringkan udara lembap.',
        icon: 'droplet',
        isWarning: false
      }
    }
    if (tempInternal !== null && tempInternal <= 35.0) {
      return {
        status: 'MENYALA',
        statusLabel: 'MENYALA',
        statusText: 'MENYALA',
        statusClass: 'status-on',
        badgeClass: 'badge-on',
        code: 'AUTO_COLD_TEMPERATURE',
        shortReason: 'Suhu Ruangan Dingin (≤35°C)',
        fullReason: 'Suhu ruangan turun di bawah batas ideal (≤35°C); pemanas otomatis aktif memulihkan suhu optimal pengeringan.',
        icon: 'thermometer',
        isWarning: false
      }
    }
    return {
      status: 'MENYALA',
      statusLabel: 'MENYALA',
      statusText: 'MENYALA',
      statusClass: 'status-on',
      badgeClass: 'badge-on',
      code: 'AUTO_ON',
      shortReason: 'Pemanas Aktif Otomatis',
      fullReason: 'Pemanas bekerja otomatis menjaga kestabilan mikroklimat ruang pengering.',
      icon: 'zap',
      isWarning: false
    }
  } else {
    if (isWithinNtpSchedule) {
      return {
        status: 'STANDBY',
        statusLabel: 'STANDBY',
        statusText: 'STANDBY',
        statusClass: 'status-standby',
        badgeClass: 'badge-standby',
        code: 'STANDBY_NIGHT_WARM',
        shortReason: 'Standby dalam Jadwal Malam',
        fullReason: 'Pemanas siaga dalam jadwal malam karena suhu ruangan saat ini telah hangat dan mencukupi target SNI.',
        icon: 'moon',
        isWarning: false
      }
    }
    return {
      status: 'STANDBY',
      statusLabel: 'STANDBY',
      statusText: 'STANDBY',
      statusClass: 'status-standby',
      badgeClass: 'badge-standby',
      code: 'STANDBY_OPTIMAL_DAY',
      shortReason: 'Standby (Siang Hari Cerah)',
      fullReason: 'Siang hari cuaca cerah; pemanas siaga dan memprioritaskan pemanasan alami dari radiasi matahari.',
      icon: 'sun',
      isWarning: false
    }
  }
}

