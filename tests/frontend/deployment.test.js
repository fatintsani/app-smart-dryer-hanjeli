import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, existsSync } from 'node:fs';
import vm from 'node:vm';
import { reverbConnectionOptions } from '../../resources/js/services/reverbOptions.js';
import { pwaBuild } from '../../scripts/pwa-build.js';

test('public HTTPS defaults to WSS on the current hostname and port 443', () => {
  const options = reverbConnectionOptions({ hostname: 'hanjeli-dryer.stas-rg.com', protocol: 'https:' });
  assert.equal(options.wsHost, 'hanjeli-dryer.stas-rg.com');
  assert.equal(options.wssPort, 443);
  assert.equal(options.forceTLS, true);
});

test('public pages reject stale localhost and Docker-private endpoints', () => {
  for (const host of ['localhost', '127.0.0.1', '[::1]', 'reverb', 'app']) {
    const options = reverbConnectionOptions({ hostname: 'hanjeli-dryer.stas-rg.com', protocol: 'https:' }, {
      VITE_REVERB_HOST: host, VITE_REVERB_PORT: '8080', VITE_REVERB_SCHEME: 'http',
    });
    assert.equal(options.wsHost, 'hanjeli-dryer.stas-rg.com');
    assert.equal(options.wssPort, 443);
    assert.equal(options.forceTLS, true);
  }
});

test('explicit public configuration and local development remain supported', () => {
  const options = reverbConnectionOptions({ hostname: 'localhost', protocol: 'http:' }, {
    VITE_REVERB_HOST: 'localhost', VITE_REVERB_PORT: '8080', VITE_REVERB_SCHEME: 'http',
  });
  assert.equal(options.wsPort, 8080);
  assert.equal(options.forceTLS, false);
  assert.equal(reverbConnectionOptions({ hostname: 'hanjeli-dryer.stas-rg.com', protocol: 'https:' }, {
    VITE_REVERB_HOST: 'hanjeli-dryer.stas-rg.com', VITE_REVERB_PORT: '443', VITE_REVERB_SCHEME: 'https',
  }).wssPort, 443);
});

test('PWA PNG dimensions match the manifest and Apple icon declaration', () => {
  const manifest = JSON.parse(readFileSync('public/manifest.webmanifest'));
  for (const icon of [...manifest.icons, { src: '/apple-touch-icon.png', sizes: '180x180' }]) {
    const data = readFileSync(`public${icon.src}`);
    assert.equal(data.subarray(1, 4).toString(), 'PNG');
    assert.equal(`${data.readUInt32BE(16)}x${data.readUInt32BE(20)}`, icon.sizes);
  }
});

test('worker version changes with the actual frontend build', () => {
  const build = (code) => {
    let emitted;
    pwaBuild().generateBundle.call({ emitFile: (file) => { if (file.fileName === 'sw.js') emitted = file; } }, {}, { 'app.js': { code } });
    assert.equal(emitted.fileName, 'sw.js');
    assert.ok(!emitted.source.includes('__SMART_DRYER_BUILD__'));
    return emitted.source;
  };
  assert.notEqual(build('release one'), build('release two'));
  assert.equal(build('same release'), build('same release'));
});

test('frontend image references resolve to committed public assets', () => {
  for (const name of readdirSync('resources', { recursive: true }).filter((name) => /\.(vue|js|css)$/.test(name))) {
    const source = readFileSync(`resources/${name}`, 'utf8');
    for (const [path] of source.matchAll(/(?<![.a-zA-Z0-9])\/assets\/[a-zA-Z0-9_/-]+\.(?:png|jpe?g|webp|svg)/g)) {
      assert.ok(existsSync(`public${path}`), `${name}: ${path}`);
    }
  }
});

function workerFixture(network = async () => new Response('network')) {
  const handlers = {};
  const stores = new Map();
  const key = (request) => typeof request === 'string' ? request : request.url;
  const cache = (name) => {
    if (!stores.has(name)) stores.set(name, new Map());
    const store = stores.get(name);
    return {
      match: async (request) => store.get(key(request))?.clone(),
      put: async (request, response) => store.set(key(request), response),
      addAll: async () => {},
    };
  };
  const context = {
    self: { location: { origin: 'https://dryer.test' }, addEventListener: (name, handler) => { handlers[name] = handler; }, clients: { claim: async () => {} }, skipWaiting: async () => {} },
    caches: { keys: async () => [...stores.keys()], open: async (name) => cache(name), delete: async (name) => stores.delete(name) },
    fetch: network,
    Request: class {
      constructor(request, options = {}) { this.url = typeof request === 'string' ? request : request.url; Object.assign(this, options); }
    },
    Response, URL, console,
  };
  vm.runInNewContext(readFileSync('public/sw.js', 'utf8').replaceAll('__SMART_DRYER_BUILD__', 'test-release'), context);
  const request = (path, mode = 'cors', method = 'GET') => {
    let response;
    handlers.fetch({ request: { url: new URL(path, 'https://dryer.test').href, method, mode }, respondWith: (value) => { response = value; } });
    return response;
  };
  return { context, handlers, stores, cache, request };
}

test('activation removes only obsolete Smart Dryer caches', async () => {
  const worker = workerFixture();
  worker.cache('smart-dryer-v1.0.0');
  worker.cache('smart-dryer-test-release');
  worker.cache('unrelated-app-cache');
  let done;
  worker.handlers.activate({ waitUntil: (promise) => { done = promise; } });
  await done;
  assert.deepEqual([...worker.stores.keys()], ['smart-dryer-test-release', 'unrelated-app-cache']);
});

test('navigation fetches fresh HTML without serving a cached application shell', async () => {
  let request;
  const worker = workerFixture(async (value) => { request = value; return new Response('new HTML'); });
  await worker.cache('smart-dryer-test-release').put('https://dryer.test/', new Response('stale HTML'));
  const response = await worker.request('/', 'navigate');
  assert.equal(await response.text(), 'new HTML');
  assert.equal(request.cache, 'no-store');
});

test('offline navigation serves a static offline page, never stale telemetry HTML', async () => {
  const worker = workerFixture(async () => { throw new Error('offline'); });
  await worker.cache('smart-dryer-test-release').put('/offline.html', new Response('offline help'));
  assert.equal(await (await worker.request('/', 'navigate')).text(), 'offline help');
});

test('API, cross-origin, mutable images and worker requests bypass cache interception', () => {
  const worker = workerFixture();
  for (const path of ['/api/telemetry/current', '/sw.js', '/assets/icons/profile/cs.png', '/manifest.webmanifest', 'https://external.test/file.js']) {
    assert.equal(worker.request(path), undefined);
  }
  assert.equal(worker.request('/build/assets/test.js', 'cors', 'POST'), undefined);
});

test('failed Vite assets are not cached', async () => {
  const worker = workerFixture(async () => new Response('missing', { status: 404 }));
  assert.equal((await worker.request('/build/assets/missing-hash.js')).status, 404);
  assert.equal(worker.stores.get('smart-dryer-test-release').size, 0);
});

test('immutable Vite assets remain available offline once cached', async () => {
  const worker = workerFixture(async () => { throw new Error('offline'); });
  await worker.cache('smart-dryer-test-release').put('https://dryer.test/build/assets/app-hash.js', new Response('cached JS'));
  assert.equal(await (await worker.request('/build/assets/app-hash.js')).text(), 'cached JS');
});
