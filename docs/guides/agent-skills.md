# Menggunakan Agent Skill

[← Kembali ke Guides](./README.md)

Package menyertakan instruksi untuk AI coding agent agar agent menulis error mengikuti framework ini, bukan membuat kode atau message error secara bebas.

| File | Isi |
|------|-----|
| `resources/boost/guidelines/core.blade.php` | Aturan singkat yang selalu dimuat agent. |
| `resources/boost/skills/error-definition-development/SKILL.md` | Workflow lengkap: membuat enum error, melempar `ApplicationException`, mapping validation, menjalankan lint dan generate, serta memakai TypeScript hasil generate. |

Instruksi ditulis dalam bahasa Inggris karena dibaca oleh agent, bukan oleh manusia.

## Dengan Laravel Boost

[Laravel Boost](https://laravel.com/docs/boost) menemukan guideline dan skill dari package yang terpasang langsung di aplikasi.

```shell
composer require laravel/boost --dev
php artisan boost:install
```

Pilih `abu-abbas/error-definition-framework` ketika Boost menanyakan third-party guidelines/skills. Boost menyalin skill ke folder agent yang dipilih, misalnya `.claude/skills/`, dan menambahkan guideline ke file seperti `CLAUDE.md` atau `AGENTS.md`.

Jalankan `php artisan boost:install` kembali setelah meng-update package agar skill ikut ter-update.

## Tanpa Laravel Boost

Skill mengikuti format [Agent Skills](https://agentskills.io), sehingga dapat disalin langsung ke folder skill agent:

```shell
mkdir -p .claude/skills
cp -r vendor/abu-abbas/error-definition-framework/resources/boost/skills/error-definition-development .claude/skills/
```

Isi `core.blade.php` dapat ditempel ke `CLAUDE.md`, `AGENTS.md`, atau file instruksi agent lain.
