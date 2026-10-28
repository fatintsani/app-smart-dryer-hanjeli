const localHost = (host) => /^(localhost|.*\.localhost|127(?:\.\d+){3}|0\.0\.0\.0|\[?::1\]?)$/i.test(host);
const internalHost = (host) => localHost(host) || ['reverb', 'app', 'web', 'db'].includes(host);

// Browser endpoints are independent of Laravel's private reverb:8080 endpoint.
export function reverbConnectionOptions(location, env = {}) {
  const configuredHost = String(env.VITE_REVERB_HOST || '').trim();
  const publicPage = !localHost(location.hostname);
  const invalidPublicHost = publicPage && internalHost(configuredHost);
  const host = invalidPublicHost ? location.hostname : configuredHost || location.hostname;
  const secure = location.protocol === 'https:' || ['https', 'wss'].includes(env.VITE_REVERB_SCHEME);
  const configuredPort = Number(env.VITE_REVERB_PORT);
  const port = !invalidPublicHost && Number.isInteger(configuredPort) && configuredPort > 0 && configuredPort <= 65535
    ? configuredPort : secure ? 443 : 80;

  return {
    wsHost: host,
    wsPort: secure ? 80 : port,
    wssPort: secure ? port : 443,
    forceTLS: secure,
    enabledTransports: ['ws', 'wss'],
  };
}
