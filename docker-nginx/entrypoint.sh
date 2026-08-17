#!/bin/sh
set -e

# Saat container dijalankan (kerangka dev), database di-refresh bersama data
# seeder agar hasil tampilan selalu sesuai dengan data seeder yang terbaru.
echo "[marac] Menyiapkan database (migrate:fresh --seed)..."

attempt=0
while true; do
    attempt=$((attempt + 1))

    if php artisan migrate:fresh --seed --force 2>&1; then
        echo "[marac] Database berhasil di-refresh bersama data seeder."
        break
    fi

    if [ "$attempt" -ge 30 ]; then
        echo "[marac] Peringatan: database belum tersedia, aplikasi tetap dijalankan tanpa seed."
        break
    fi

    echo "[marac] Database belum siap (percobaan ${attempt}/30), mencoba lagi dalam 2 detik..."
    sleep 2
done

echo "[marac] Menjalankan: $*"
exec "$@"