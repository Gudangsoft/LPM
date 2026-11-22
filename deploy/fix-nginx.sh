#!/usr/bin/env bash
# Memperbaiki config nginx agar semua route Laravel (mis. /login) diteruskan ke public/index.php.
#
# Pemakaian di server:
#   sudo bash fix-nginx.sh                      # deteksi otomatis
#   sudo bash fix-nginx.sh /var/www/LPM-SPMI    # tentukan folder project
#   sudo CONF=/etc/nginx/sites-available/spmi bash fix-nginx.sh   # tentukan file config
#
# Config lama dibackup (.bak-<waktu>). Jika `nginx -t` gagal, otomatis dikembalikan.
set -euo pipefail

DOMAIN="${DOMAIN:-spmi.staithawalib.ac.id}"
PROJECT="${1:-}"

[ "$(id -u)" -eq 0 ] || { echo "Jalankan dengan sudo."; exit 1; }
command -v nginx >/dev/null || { echo "nginx tidak ditemukan."; exit 1; }

# ---------- 1. Cari file config untuk domain ----------
if [ -z "${CONF:-}" ]; then
  mapfile -t FILES < <(
    grep -RlsE "server_name[^;]*[[:space:]]${DOMAIN//./\\.}([[:space:];]|$)" \
      /etc/nginx /www/server/panel/vhost/nginx /usr/local/nginx/conf 2>/dev/null \
      | xargs -r -n1 readlink -f | sort -u
  )
  if [ "${#FILES[@]}" -eq 0 ]; then
    echo "Tidak ada config nginx dengan server_name $DOMAIN."
    echo "Buat dulu, atau jalankan dengan CONF=/path/file.conf"
    exit 1
  elif [ "${#FILES[@]}" -gt 1 ]; then
    echo "Ditemukan lebih dari satu config untuk $DOMAIN (bisa saling bentrok):"
    printf '  %s\n' "${FILES[@]}"
    echo "Pilih satu: sudo CONF=<file> bash $0 ${PROJECT}"
    echo "lalu hapus/nonaktifkan file lainnya."
    exit 1
  fi
  CONF="${FILES[0]}"
fi
echo "Config        : $CONF"

# ---------- 2. Tentukan folder project ----------
if [ -z "$PROJECT" ]; then
  OLD_ROOT=$(grep -oP '^\s*root\s+\K[^;]+' "$CONF" | head -n1 || true)
  OLD_ROOT="${OLD_ROOT%/}"
  PROJECT="${OLD_ROOT%/public}"
fi
PROJECT="${PROJECT%/}"
if [ ! -f "$PROJECT/public/index.php" ] || [ ! -f "$PROJECT/artisan" ]; then
  echo "Folder project Laravel tidak valid: '$PROJECT'"
  echo "Kandidat yang ditemukan:"
  find /var/www /home /www/wwwroot /srv -maxdepth 4 -name artisan 2>/dev/null | xargs -r -n1 dirname | sed 's/^/  /'
  echo "Jalankan ulang: sudo bash $0 /path/ke/project"
  exit 1
fi
echo "Project       : $PROJECT"

# ---------- 3. Deteksi PHP handler ----------
PHP_BLOCK=""
PANEL_PHP=$(grep -oP '^\s*include\s+enable-php-\d+\.conf\s*;' "$CONF" | head -n1 || true)
if [ -n "$PANEL_PHP" ]; then
  PHP_BLOCK="    $(echo "$PANEL_PHP" | sed 's/^\s*//')"            # aaPanel
else
  FPM=$(grep -oP '^\s*fastcgi_pass\s+\K[^;]+' "$CONF" | head -n1 || true)
  if [ -z "$FPM" ]; then
    FPM=$(ls /run/php/php8*-fpm.sock /var/run/php/php8*-fpm.sock 2>/dev/null | sort -V | tail -n1 || true)
    [ -n "$FPM" ] && FPM="unix:$FPM"
  fi
  [ -n "$FPM" ] || { echo "Socket PHP-FPM tidak ditemukan. Pastikan php8.2-fpm terpasang & jalan."; exit 1; }
  PHP_BLOCK="    location ~ \\.php\$ {
        include fastcgi_params;
        fastcgi_pass $FPM;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT \$realpath_root;
        fastcgi_hide_header X-Powered-By;
    }"
fi
echo "PHP handler   : $(echo "$PHP_BLOCK" | grep -oE 'enable-php-[0-9]+\.conf|unix:[^;]+|[0-9.]+:[0-9]+' | head -n1)"

# ---------- 4. Ambil pengaturan SSL yang sudah ada ----------
SSL_LINES=$(grep -E '^\s*(ssl_certificate|ssl_certificate_key|ssl_dhparam|ssl_protocols|ssl_ciphers|ssl_prefer_server_ciphers|ssl_session_[a-z_]+)\s|include\s+\S*letsencrypt\S*' "$CONF" \
  | sed 's/^\s*/    /' | sed 's/\s*#.*managed by Certbot//' | awk '!seen[$0]++' || true)

APP_BODY="    root $PROJECT/public;
    index index.php index.html;
    charset utf-8;
    client_max_body_size 50M;

    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header X-Content-Type-Options \"nosniff\";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

$PHP_BLOCK

    location ~ /\\.(?!well-known).* {
        deny all;
    }"

if grep -qE '^\s*ssl_certificate\s' "$CONF"; then
  echo "SSL           : ya (sertifikat lama dipakai ulang)"
  NEW="server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN;

$SSL_LINES

$APP_BODY
}
"
else
  echo "SSL           : tidak"
  NEW="server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;

$APP_BODY
}
"
fi

# ---------- 5. Tulis, uji, reload (rollback bila gagal) ----------
BAK="$CONF.bak-$(date +%Y%m%d%H%M%S)"
cp -a "$CONF" "$BAK"
echo "Backup        : $BAK"
printf '%s' "$NEW" > "$CONF"

# Pastikan aktif di sites-enabled (Debian/Ubuntu)
if [[ "$CONF" == /etc/nginx/sites-available/* ]] && [ -d /etc/nginx/sites-enabled ]; then
  LINK="/etc/nginx/sites-enabled/$(basename "$CONF")"
  [ -e "$LINK" ] || { ln -s "$CONF" "$LINK"; echo "Diaktifkan    : $LINK"; }
fi

if nginx -t; then
  systemctl reload nginx 2>/dev/null || nginx -s reload
  echo "nginx berhasil di-reload."
else
  echo "nginx -t GAGAL -> mengembalikan config lama."
  cp -a "$BAK" "$CONF"
  nginx -t && (systemctl reload nginx 2>/dev/null || nginx -s reload)
  exit 1
fi

# ---------- 6. Beres-beres Laravel ----------
cd "$PROJECT"
OWNER=$(stat -c '%U' "$PROJECT/public/index.php")
WEBUSER=$(ps -o user= -C php-fpm8.2,php-fpm8.3,php-fpm8.4,php-fpm 2>/dev/null | grep -v root | head -n1 || true)
WEBUSER="${WEBUSER:-www-data}"
chown -R "$OWNER:$WEBUSER" storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache
[ -L public/storage ] || sudo -u "$OWNER" php artisan storage:link || true
sudo -u "$OWNER" php artisan optimize:clear || php artisan optimize:clear
grep -q "^APP_URL=https\?://$DOMAIN" .env || echo "PERINGATAN: APP_URL di .env belum https://$DOMAIN"

# ---------- 7. Tes ----------
SCHEME=http; grep -qE '^\s*ssl_certificate\s' "$CONF" && SCHEME=https
CODE=$(curl -sk -o /dev/null -w '%{http_code}' --resolve "$DOMAIN:$([ $SCHEME = https ] && echo 443 || echo 80):127.0.0.1" "$SCHEME://$DOMAIN/login" || true)
echo "Tes /login    : HTTP $CODE"
[ "$CODE" = "200" ] && echo "SELESAI. Buka $SCHEME://$DOMAIN/login" \
  || echo "Belum 200. Cek: tail -n 30 $PROJECT/storage/logs/laravel.log /var/log/nginx/error.log"
