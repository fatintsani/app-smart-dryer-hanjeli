# Deployment Docker Smart Dryer Hanjeli di Ubuntu

Deployment production **manual**. Workflow `.github/workflows/deploy.yml` mempertahankan Composer install, frontend build, manifest check dan Laravel tests, lalu membangun serta menguji stack Docker terisolasi. Job SSH non-Docker dihapus agar tidak ada dua jalur CD aktif. Panduan ini tidak memasang/mengubah Nginx host secara otomatis.

## Arsitektur dan prasyarat

- Docker Engine dan Compose plugin modern (disarankan Compose >= 2.24), Git, Bash, `flock`, Python 3, OpenSSL; akses Docker untuk operator. Tidak memerlukan PHP, Composer, atau Node di host production.
- Image aplikasi PHP 8.4 FPM digunakan ulang oleh app, migrate, queue, mqtt, reverb dan scheduler. Image web memuat public assets dari build yang sama. Node **22.23.2** hanya pada build stage.
- `db` menggunakan MySQL **8.4**, user khusus Laravel dan volume baru `<project>_mysql84_data`. Tidak ada published port MySQL/PHP-FPM/Reverb. Volume `<project>_laravel_storage` menyimpan uploads, file privat, views dan file runtime.
- Network `backend` bersifat internal untuk MySQL. Network `egress` tetap privat dalam project Compose, dengan outbound untuk MQTT eksternal, OAuth, email dan API. Tidak menggunakan host network atau static container name.
- `127.0.0.1:8088` → web Nginx → app:9000; `/app` dan `/apps` → reverb:8080. Laravel mengirim broadcast ke reverb:8080 HTTP. Browser menggunakan domain HTTPS/WSS port 443.
- MySQL harus healthy; satu service migrate harus selesai sukses sebelum aplikasi/worker mulai. `restart: unless-stopped` memulihkan background services. Queue timeout 60 detik harus lebih kecil dari `DB_QUEUE_RETRY_AFTER=90` dan grace period 90 detik.

**Jangan** menempelkan volume MySQL 5.7 lama ke MySQL 8.4. Database `dryer` lama dinyatakan kosong; stack ini melakukan inisialisasi baru. Container lama di port 1111 dan volume `dryer` tidak disentuh. Jika ternyata ada data, hentikan rollout dan lakukan logical export/import yang diverifikasi. Semua perintah harus dijalankan dari direktori project ini. Nama project tidak boleh dipakai oleh aplikasi lain atau diganti setelah data dibuat.

## Audit `.env` tanpa membuka secret

Checkout pengembangan ketika implementasi ini dibuat tidak memiliki `.env`. File production server belum dapat diaudit. `.env` tidak dibuat, ditimpa atau dimasukkan image/Git. Gunakan `.env` server yang sudah ada, permission `chmod 600 .env`, lalu **gabungkan manual** nilai referensi dari `.env.docker.example`; jangan menyalin contoh di atas file existing.

Nilai non-rahasia yang harus konsisten:

| Nama | Nilai rekomendasi |
| --- | --- |
| APP_ENV / APP_DEBUG | production / false |
| APP_URL | https://hanjeli-dryer.stas-rg.com |
| APP_TIMEZONE | Asia/Jakarta |
| DB_CONNECTION / DB_HOST / DB_PORT | mysql / db / 3306 |
| DB_DATABASE / DB_USERNAME | dryer / hanjeli_app |
| DB_URL / DB_SOCKET | kosong, agar tidak menimpa koneksi Docker |
| BROADCAST_CONNECTION | reverb |
| REVERB_HOST / REVERB_PORT / REVERB_SCHEME | reverb / 8080 / http |
| REVERB_SERVER_HOST / REVERB_SERVER_PORT | 0.0.0.0 / 8080 |
| VITE_REVERB_HOST / VITE_REVERB_PORT / VITE_REVERB_SCHEME | hanjeli-dryer.stas-rg.com / 443 / https |
| VITE_REVERB_APP_KEY | sama dengan REVERB_APP_KEY |
| VITE_API_BASE_URL | /api |
| QUEUE_CONNECTION / CACHE_STORE / SESSION_DRIVER | database / database / database |
| DB_QUEUE_RETRY_AFTER | 90 |
| SESSION_SECURE_COOKIE | true |
| LOG_CHANNEL | stderr |
| APP_MAINTENANCE_DRIVER / APP_MAINTENANCE_STORE | cache / database |
| COMPOSE_PROJECT_NAME | hanjeli-dryer (tetap) |
| WEB_PORT | 8088 |

Pertahankan `APP_KEY` existing. Isi `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` melalui secret manager/operator; jangan memakai akun/password default. Kedua password database harus unik, berbeda dan acak minimal 20 karakter (misalnya buat melalui password manager). MySQL mengambil database/user/password dari **DB_DATABASE/DB_USERNAME/DB_PASSWORD yang sama** dengan Laravel. Variabel `MYSQL_*` hanya menginisialisasi volume kosong; mengubah `.env` setelah inisialisasi tidak mengubah user/password MySQL. Rotasi password melalui SQL administratif secara terencana, kemudian sesuaikan `.env` dan recreate services.

Tentukan `MQTT_HOST`, port, TLS, kredensial, client ID dan topics sesuai broker production dan ESP32. Setiap instance MQTT worker harus memiliki ID unik. Contoh memakai TLS port 8883; sesuaikan dengan broker sebenarnya. Tidak ada MQTT broker production yang dibuat otomatis. Jangan gunakan broker anonim pengujian untuk perangkat production.

Compose membaca `.env` untuk interpolation dan `env_file` aplikasi. Gunakan sintaks dotenv valid; quote nilai yang mengandung `$` sesuai kebutuhan. Hindari shell variable DB/VITE/COMPOSE yang dapat menimpa nilai `.env`. Jangan menjalankan `docker compose config` tanpa `--quiet` di log publik karena hasilnya mengandung secret. Preflight `docker/check-environment.php` hanya mencetak nama variabel dan rekomendasi; tidak mencetak nilai rahasia.

`VITE_*` adalah informasi publik yang ditanam saat `npm run build`. Hanya public Reverb key dan Google client ID yang boleh digunakan, bukan Reverb secret/Google client secret. Perubahan nilai membutuhkan rebuild **kedua image app dan web**. Metadata public build dibandingkan dengan runtime; ketidaksesuaian menggagalkan startup. Untuk build manual di luar Compose, berikan `--build-arg VITE_REVERB_APP_KEY=...` dan build args publik lain yang sesuai.

## Validasi sebelum cutover

```bash
# Docker lokal/CI, synthetic secrets di /tmp, broker terisolasi, bukan .env production.
bash docker/validate.sh
```

Script menguji Compose config/build/up/ps, migrasi MySQL, `/up`, frontend dan assets, Vue history fallback, API, larangan akses file internal, queue job nyata, koneksi MQTT, WebSocket upgrade HTTP 101, signed Reverb publish, scheduler, serta persistence database/upload saat recreate. Terakhir menjalankan Laravel tests dengan dev dependencies di test image terpisah. Container/network validasi dibersihkan; **semua volume dipertahankan** dan nama project dicetak untuk audit. Tidak menjalankan volume prune atau penghapusan volume. Port uji default 18088, dapat diganti `VALIDATION_WEB_PORT=18089`.

MQTT CI menggunakan Mosquitto privat dengan data sintetis. Ini memverifikasi koneksi worker dan subscribe, bukan koneksi broker production/ESP32. Smoke test memakai HTTP internal; sertifikat TLS dan WSS dari internet tetap harus diuji di server.

Hasil verifikasi lokal 29 Oktober 2026, Docker Desktop Linux ARM64: Compose config/build/up/ps berhasil; MySQL migrations selesai exit 0; seluruh service long-running healthy; frontend/API/queue/MQTT pengujian/Reverb/scheduler lulus; database dan upload bertahan setelah recreate; backup/restore dengan user aplikasi berhasil. Laravel tests: **18 passed, 127 assertions**, tanpa warning PHPUnit. Runtime berjalan sebagai www-data, tanpa `.env`, node_modules, Composer executable atau PHPUnit. Nginx berjalan non-root. BuildKit dapat memberi peringatan heuristik pada nama `VITE_REVERB_APP_KEY`; parameter ini memang **public browser key**, bukan `REVERB_APP_SECRET`. Tidak ada secret production yang digunakan.

Belum diverifikasi di Ubuntu VPS/arsitektur amd64: konfigurasi `.env` server, reverse proxy host dan sertifikat, WSS publik, broker production/ACL dan telemetry ESP32 asli. Script release Ubuntu diverifikasi sintaks; rollout production tetap manual dan memerlukan validasi server.

## Deployment pertama

1. Siapkan clone terpisah dari deployment non-Docker lama. Pastikan `.env` production existing berada di clone deployment tersebut melalui prosedur administrasi yang aman. Jangan commit/overwrite key. Periksa nama project dengan `docker compose ls` dan pastikan port 8088 tersedia. Tidak perlu mematikan MySQL lama atau aplikasi project lain.
2. Gabungkan environment di atas, pastikan broker production dapat diakses dan DNS/certificate sudah tersedia.
3. Dari direktori repo, jalankan:

```bash
docker compose --env-file .env config --quiet
bash docker/deploy.sh "$(git rev-parse --short=12 HEAD)"
docker compose ps -a
curl --fail --silent --show-error http://127.0.0.1:8088/up
```

Script memakai lock project, build images dengan tag release, preflight environment, menghentikan penulis lama dalam project yang sama, menunggu MySQL, backup database, recreate satu migration runner dan menunggu exit code, lalu memulai service dengan healthchecks. Tidak menjalankan seeder, `migrate:fresh` atau rollback schema. Kegagalan healthcheck/smoke menghasilkan exit nonzero dan menghentikan web/worker pemroses. Periksa error dan pulihkan secara eksplisit. Prosedur ini memakai jendela maintenance; belum merupakan deployment tanpa downtime.

Perintah dasar yang setara untuk bootstrap setelah build/preflight adalah `docker compose build app web` dan `docker compose up -d --wait --wait-timeout 240`. Untuk update production gunakan script release agar migrasi selalu direcreate dan worker lama dihentikan terlebih dahulu. Jangan menjalankan migrasi dari beberapa shell/container sekaligus atau melakukan scale pada service migrate.

Tidak ada user default yang dibuat. Lakukan provisioning akun admin melalui prosedur aplikasi yang telah diverifikasi. Jika ada uploads dari instalasi lama, backup lalu salin ke storage volume baru sebelum cutover dan gunakan UID/GID **33:33**; jangan menimpa data baru. Nginx (non-root) membaca public uploads; PHP berjalan sebagai www-data. Source aplikasi bukan bind mount. Jika environment lama memakai file session/cache, beralih ke database mengharuskan pengguna login ulang.

## HTTPS dan WebSocket di Nginx Ubuntu

Review `docker/nginx/host-proxy.conf.example` bersama konfigurasi virtual host existing. File tersebut hanya contoh; **tidak pernah diaktifkan otomatis**. Setelah administrator menyetujui perubahan host, pasang ke virtual host domain yang sesuai, pastikan sertifikat ada, jalankan `sudo nginx -t` lalu reload Nginx secara terkontrol. Jangan membuat dua server block untuk domain yang sama.

Host Nginx menimpa header forwarding dari client dan meneruskan Upgrade/Connection ke localhost:8088. Docker Nginx meneruskannya ke Reverb. Laravel mempercayai forwarded proto/port/IP dari rantai proxy privat; FPM tidak boleh diterbitkan ke jaringan publik. Jika arsitektur berubah menjadi shared network yang tidak dipercaya, batasi trusted proxies sesuai alamat proxy aktual. Tidak mempercayai forwarded Host dari client.

```bash
curl --fail --silent --show-error https://hanjeli-dryer.stas-rg.com/up
curl --fail --silent --show-error https://hanjeli-dryer.stas-rg.com/api/batches
docker compose exec -T app php docker/smoke.php
docker compose exec -T scheduler php artisan schedule:list
```

Buka browser production: frontend harus memuat hashed assets tanpa mixed content; Network → WS harus menunjukkan `wss://hanjeli-dryer.stas-rg.com/app/...` dan status 101. Verifikasi event telemetry dari ESP32 asli, koneksi/subscription broker, queue logs, dan task 02.00 **Asia/Jakarta (WIB)**. Healthchecks MQTT membuka koneksi autentikasi dengan client ID acak lalu disconnect; sesuaikan batas koneksi/ACL broker agar probe ini diizinkan. Worker MQTT existing dapat reconnect sendiri; status proses saja tidak membuktikan pesan perangkat diterima.

## Update berikutnya

```bash
git pull --ff-only origin main
bash docker/deploy.sh "$(git rev-parse --short=12 HEAD)"
docker compose ps -a
curl --fail --silent --show-error https://hanjeli-dryer.stas-rg.com/up
```

Jangan mengubah source lokal saat release berjalan. `.docker-release` dan `.docker-previous-release` menyimpan tag lokal yang diabaikan Git. Untuk inspeksi sesuai tag aktif gunakan `IMAGE_TAG="$(cat .docker-release)" docker compose images`. Startup tidak mewarisi tag dari file ini secara otomatis; script selalu menerima tag eksplisit. Simpan pasangan app/web image lama sebelum prune; gunakan tag release unik, jangan menimpa tag yang telah dirilis. Jangan menjalankan `docker image prune -a` tanpa inventaris release.

## Backup dan restore

```bash
bash docker/database.sh backup
# Opsional lokasi aman di luar repository:
BACKUP_DIR=/srv/backups/hanjeli bash docker/database.sh backup
```

Dump menggunakan user aplikasi dan `--single-transaction --no-tablespaces`, password hanya melalui environment dalam container. Script menggunakan pipefail, file `.partial`, gzip integrity check dan permission privat. Jangan menjalankan DDL/migrasi selama dump. Simpan backup terenkripsi/off-server, termasuk `.env` melalui secret manager dan snapshot uploads secara konsisten. Database saja tidak mencadangkan uploads. Jadwalkan backup melalui mekanisme operasional host yang disetujui; uji restore berkala ke stack terpisah.

Contoh backup uploads (pastikan `IMAGE_TAG` mengacu release yang tersedia):

```bash
umask 077
IMAGE_TAG="$(cat .docker-release)" docker compose run --rm --no-deps --entrypoint tar app -C /var/www/html/storage -czf - app > backups/laravel-uploads.tar.gz
```

Restore berikut **menimpa tabel** yang ada dalam dump. Ambil backup terbaru terlebih dahulu, periksa target dengan operator, hentikan semua penulis, dan gunakan hanya file yang dipercaya:

```bash
docker compose stop web app queue mqtt scheduler reverb
CONFIRM_DATABASE_RESTORE=dryer bash docker/database.sh restore /srv/backups/hanjeli/dryer-TIMESTAMP.sql.gz
# Mulai ulang release yang kompatibel dengan schema hasil restore:
bash docker/deploy.sh TAG_KOMPATIBEL --existing
```

Untuk pemulihan total, restore database ke volume **baru** pada project pemulihan, verifikasi, restore uploads yang cocok dengan UID/GID 33:33, lalu lakukan cutover. Jangan menghapus volume produksi sebagai langkah rutin. Jangan gunakan `docker compose down -v` atau `docker volume prune`.

## Rollback image

Rollback kode hanya aman jika schema dan environment masih kompatibel. Review migrasi release gagal; tidak ada jaminan `migrate:rollback` aman, sehingga script tidak menjalankannya. Kode lama yang melihat schema baru dapat tetap rusak. Jika schema tidak kompatibel, pilih forward fix atau restore backup terverifikasi dengan rencana kehilangan data dan downtime yang disetujui.

```bash
# Pastikan tag pasangan app/web lama masih tersedia dan cocok dengan VITE_*.
previous_tag=$(cat .docker-previous-release)
bash docker/deploy.sh "$previous_tag" --existing
```

`--existing` memakai image lokal tanpa rebuild/pull. Script tetap melakukan preflight, backup, `migrate --force` dari image lama (tidak menurunkan schema), healthcheck dan smoke. Bila image berasal registry, pull **kedua tag** sebelum menjalankan script. Bila konfigurasi VITE berubah, sesuaikan konfigurasi public ke release lama terlebih dahulu tanpa mengganti APP_KEY/secret sembarangan. Simpan commit Compose/config yang sesuai untuk pemulihan; jangan `git reset --hard` otomatis. Volume database/upload tetap dipakai.

## Rancangan CD Docker selanjutnya (belum diaktifkan)

Setelah validasi server, pertahankan tepat satu workflow deployment: `push main → CI → Docker build app/web → push immutable tags/digests → SSH deploy → Compose/migration gate → health + smoke`. Jobs production memakai protected GitHub Environment `production` dengan reviewer, concurrency tunggal dan `needs` CI/Docker. Jangan menghidupkan kembali job non-Docker.

- Registry: gunakan GHCR `GITHUB_TOKEN` dengan `packages: write` di build job, read-only pull credential pada VPS bila private. Bangun untuk platform VPS (biasanya linux/amd64), atau amd64+arm64 jika diperlukan. Semua pengujian lokal yang dicatat terpisah dari platform server.
- Public build vars: GitHub Actions Variables untuk VITE host/port/scheme/API dan Google client ID. Public Reverb key dapat berasal Actions Secret/Variable; jangan kirim server secret atau `.env` sebagai build context/arg.
- SSH: Actions Secrets `SSH_HOST`, `SSH_USER`, `SSH_KEY`, `SSH_PORT`, `DEPLOY_PATH`; pin host fingerprint melalui known_hosts/secret yang diverifikasi, bukan menonaktifkan host checking. Registry token hanya secret, jangan inline dalam script/log.
- Deploy hanya pasangan image dari commit yang sama, simpan tag release sebelumnya. Sinkronkan Compose/scripts dari commit yang sesuai tanpa menimpa `.env`, pull tag, lalu panggil `docker/deploy.sh TAG --existing` via SSH. APP_IMAGE/WEB_IMAGE di `.env` menunjuk registry images.
- Deploy job harus gagal bila SSH, migration exit code, `compose --wait`, smoke test, atau HTTPS `/up` gagal. Lakukan recovery image secara eksplisit, tidak melakukan schema rollback otomatis.

Tidak ada SSH job, push image production, secret production, atau automatic deployment yang diaktifkan oleh perubahan ini.

## Troubleshooting

| Gejala | Pemeriksaan / tindakan |
| --- | --- |
| Compose missing variable | Lengkapi nama variabel pada existing `.env`; jangan mencetak config ter-resolve di CI. |
| Environment preflight failed | Ikuti nama/rekomendasi yang dicetak. Jika VITE berubah, rebuild app/web dengan tag baru. |
| DB unhealthy / access denied | `docker compose ps -a`, `docker compose logs --tail=100 db`; cocokkan DB_* dengan akun yang pertama kali menginisialisasi volume. Jangan hapus volume untuk reset password. |
| Migrasi gagal | `docker compose logs --tail=100 migrate`; workers sengaja belum mulai. Perbaiki penyebab, jalankan ulang release script. Jangan migrate:fresh. |
| Permission denied storage | Pastikan volume baru diinisialisasi image dengan UID/GID 33:33. Jika impor storage lama, koreksi owner secara terkontrol pada volume project ini saja; jangan chmod 777. |
| 502 setelah recreate | Pastikan app/reverb healthy. Docker Nginx me-resolve DNS service setiap 10 detik. Periksa logs app/web dan batas memori FPM. |
| WebSocket 404/502/mixed content | Cek kedua proxy paths `/app`/`/apps`, Upgrade headers, DNS/TLS; browser VITE host public + 443 + https, backend REVERB_HOST=reverb + 8080 + http. |
| MQTT unhealthy/reconnect loop | Periksa DNS/outbound, TLS/CA, auth/ACL, batas koneksi dan client ID. `docker compose logs --tail=100 mqtt`. Jangan share log payload/credentials ke publik. |
| Queue healthy tetapi job tidak selesai | Probe memastikan proses+DB, bukan setiap job. Periksa failed_jobs, `docker compose logs queue`, queue name dan external dependency. Worker baru harus direcreate tiap release. |
| Jadwal 02.00 bergeser | `schedule:list`, APP_TIMEZONE=Asia/Jakarta, config cache baru. Jalankan satu scheduler saja; mutex memakai database cache. |
| HTTPS /up baik tetapi API gagal | Jalankan smoke.php: /up Laravel bawaan bukan pemeriksaan DB; app healthcheck dan smoke juga memeriksa DB/API. |
| Disk penuh | Periksa kapasitas volume/backups dan kebijakan retensi terverifikasi. Logging container dibatasi; jangan hapus volume MySQL lama/production otomatis. |

Referensi: [Vite Node requirements](https://vite.dev/guide/), [Docker startup ordering](https://docs.docker.com/compose/how-tos/startup-order/), [MySQL official image initialization](https://hub.docker.com/_/mysql), [Laravel Reverb reverse proxy](https://laravel.com/docs/13.x/reverb), [Laravel trusted proxies](https://laravel.com/docs/13.x/requests#configuring-trusted-proxies).
