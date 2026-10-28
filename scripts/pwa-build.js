import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';

// Emit a worker whose bytes change with the actual frontend, not a manual version.
export function pwaBuild() {
  return {
    name: 'hanjeli-pwa-build',
    apply: 'build',
    generateBundle(_options, bundle) {
      const template = readFileSync('public/sw.js', 'utf8');
      const hash = createHash('sha256').update(template);
      for (const name of Object.keys(bundle).sort()) {
        hash.update(name).update(bundle[name].code ?? bundle[name].source);
      }
      for (const name of ['offline.html', 'manifest.webmanifest', 'pwa-192x192.png', 'pwa-512x512.png', 'apple-touch-icon.png']) {
        hash.update(readFileSync(`public/${name}`));
      }
      const buildId = hash.digest('hex').slice(0, 16);
      this.emitFile({ type: 'asset', fileName: 'sw.js', source: template.replaceAll('__SMART_DRYER_BUILD__', buildId) });
      this.emitFile({ type: 'asset', fileName: 'version.json', source: JSON.stringify({ buildId, revision: process.env.APP_BUILD_REVISION || 'development' }) });
    },
  };
}
