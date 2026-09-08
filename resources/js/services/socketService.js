import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import api from './api';

// Make Pusher globally available for Laravel Echo
window.Pusher = Pusher;

class SocketService {
  constructor() {
    this.listeners = new Map();
    this.isConnected = false;
    this.connectionMode = 'idle'; // 'websocket' | 'polling' | 'offline'
    this.echo = null;
    this.pollingInterval = null;
    this.isInitialized = false;
  }

  /**
   * Initialize real-time WebSocket connection (Laravel Reverb) with Polling Fallback
   */
  connect() {
    if (this.isInitialized) return;
    this.isInitialized = true;

    // 1. Initial snapshot fetch to immediately populate UI
    this.fetchCurrentSnapshot();

    // 2. Initialize Laravel Reverb / Echo
    try {
      const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || 'uw8wsconrradblzfklgo';
      const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname || 'localhost';
      const reverbPort = import.meta.env.VITE_REVERB_PORT ? parseInt(import.meta.env.VITE_REVERB_PORT, 10) : 8080;
      const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || (window.location.protocol === 'https:' ? 'https' : 'http');

      this.echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
      });

      // Bind connection lifecycle events
      if (this.echo?.connector?.pusher?.connection) {
        const conn = this.echo.connector.pusher.connection;

        conn.bind('connected', () => {
          this.isConnected = true;
          this.connectionMode = 'websocket';
          this.stopPolling();
          this.emit('connection_status', { connected: true, mode: 'websocket' });
        });

        conn.bind('disconnected', () => {
          this.connectionMode = 'polling';
          this.startPolling(3000);
          this.emit('connection_status', { connected: false, mode: 'polling' });
        });

        conn.bind('unavailable', () => {
          this.connectionMode = 'polling';
          this.startPolling(3000);
          this.emit('connection_status', { connected: false, mode: 'polling' });
        });

        conn.bind('error', (err) => {
          this.connectionMode = 'polling';
          this.startPolling(3000);
          this.emit('connection_status', { connected: false, mode: 'polling', error: err });
        });
      }

      // Subscribe to Public Telemetry Channel
      this.echo.channel('greenhouse.telemetry')
        .listen('.telemetry.updated', (event) => {
          this.isConnected = true;
          this.connectionMode = 'websocket';
          if (event.telemetry) {
            this.emit('telemetry_update', event.telemetry);
            this.emit('telemetry_live', event.telemetry);
            this.emit('telemetry_new', event.telemetry);
            this.emit('batch_update', event.telemetry);
          }
          if (event.actuators) {
            this.emit('actuator_update', event.actuators);
            this.emit('actuators_update', event.actuators);
          }
          this.emit('connection_status', { connected: true, mode: 'websocket' });
        });

      // Subscribe to Public Actuators Channel
      this.echo.channel('greenhouse.actuators')
        .listen('.actuators.updated', (event) => {
          this.isConnected = true;
          this.connectionMode = 'websocket';
          if (event.actuators) {
            this.emit('actuator_update', event.actuators);
            this.emit('actuators_update', event.actuators);
          }
          this.emit('connection_status', { connected: true, mode: 'websocket' });
        });

      // Subscribe to Public System Alerts Channel
      this.echo.channel('greenhouse.alerts')
        .listen('.alert.triggered', (event) => {
          if (event.alert) {
            this.emit('alert_new', event.alert);
            this.emit('alert_received', event.alert);
            this.emit('alert_triggered', event.alert);
          }
        });

      // Safety timeout: if websocket doesn't connect within 2.5 seconds, activate polling fallback
      setTimeout(() => {
        if (!this.isConnected) {
          this.startPolling(3000);
        }
      }, 2500);

    } catch (e) {
      console.warn('[SocketService] WebSocket initialization fallback to polling:', e);
      this.startPolling(3000);
    }
  }

  /**
   * Fetch a single immediate telemetry snapshot
   */
  async fetchCurrentSnapshot() {
    try {
      const data = await api.get('/telemetry/current');
      if (data) {
        if (data.telemetry) {
          this.emit('telemetry_update', data.telemetry);
          this.emit('telemetry_live', data.telemetry);
          this.emit('telemetry_new', data.telemetry);
        }
        if (data.actuators) {
          this.emit('actuator_update', data.actuators);
          this.emit('actuators_update', data.actuators);
        }
        this.emit('connection_status', { connected: true, mode: this.connectionMode || 'http' });
      }
    } catch (err) {
      // Ignored for initial snapshot
    }
  }

  /**
   * Start HTTP Polling fallback when WebSocket is unavailable
   */
  startPolling(intervalMs = 3000) {
    if (this.pollingInterval) return;

    this.pollingInterval = setInterval(async () => {
      try {
        const data = await api.get('/telemetry/current');
        if (data) {
          this.isConnected = true;
          this.connectionMode = 'polling';
          if (data.telemetry) {
            this.emit('telemetry_update', data.telemetry);
            this.emit('telemetry_live', data.telemetry);
            this.emit('telemetry_new', data.telemetry);
          }
          if (data.actuators) {
            this.emit('actuator_update', data.actuators);
            this.emit('actuators_update', data.actuators);
          }
          this.emit('connection_status', { connected: true, mode: 'polling' });
        } else {
          this.isConnected = false;
          this.connectionMode = 'offline';
          this.emit('connection_status', { connected: false, mode: 'offline' });
        }
      } catch (err) {
        this.isConnected = false;
        this.connectionMode = 'offline';
        this.emit('connection_status', { connected: false, mode: 'offline' });
      }
    }, intervalMs);
  }

  /**
   * Stop HTTP Polling interval
   */
  stopPolling() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
      this.pollingInterval = null;
    }
  }

  /**
   * Disconnect WebSocket and Polling
   */
  stop() {
    this.stopPolling();
    if (this.echo) {
      try {
        this.echo.disconnect();
      } catch (e) {
        // Ignored
      }
      this.echo = null;
    }
    this.isConnected = false;
    this.isInitialized = false;
    this.connectionMode = 'offline';
  }

  /**
   * Register event listener
   */
  on(event, callback) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, new Set());
    }
    this.listeners.get(event).add(callback);
    return () => this.off(event, callback);
  }

  /**
   * Unregister event listener
   */
  off(event, callback) {
    if (this.listeners.has(event)) {
      if (callback) {
        this.listeners.get(event).delete(callback);
      } else {
        this.listeners.delete(event);
      }
    }
  }

  /**
   * Dispatch event to local listeners
   */
  emit(event, data) {
    if (this.listeners.has(event)) {
      this.listeners.get(event).forEach((cb) => {
        try {
          cb(data);
        } catch (e) {
          console.error('[SocketService] Callback error:', e);
        }
      });
    }
  }
}

export const socketService = new SocketService();
export default socketService;
