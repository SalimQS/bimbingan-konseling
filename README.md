# Bimbingan Konseling
Full Native PHP App\
UKK 2023/2024\
5 Month Development\
...

## Konfigurasi Database

Konfigurasi database sekarang dibaca dari file `.env`.

Variabel yang dipakai:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_SOCKET`

Catatan:

- Di Linux, gunakan `DB_HOST=127.0.0.1` jika `localhost` memicu error `No such file or directory` dari `mysqli`.
- Isi `DB_SOCKET` hanya jika ingin memaksa koneksi melalui Unix socket MySQL.
