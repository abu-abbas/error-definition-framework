# Menggunakan Agent Skill

[← Kembali ke Guides](./README.md)

Package menyertakan Agent Skill bernama `error-definition-development`. Skill ini mengajarkan AI coding agent, seperti Claude, cara menulis error mengikuti framework ini: membuat enum error, melempar `ApplicationException`, memetakan validation, lalu menjalankan lint dan generate. Tanpa skill, agent cenderung menulis `abort()` atau message error secara bebas.

Skill memakai format standar [Agent Skills](https://agentskills.io) (folder berisi `SKILL.md`) yang dibuat Anthropic untuk Claude. Format yang sama juga dibaca agent lain yang mendukungnya. Instruksi ditulis dalam bahasa Inggris karena dibaca oleh agent, bukan oleh manusia.

| File | Isi |
|------|-----|
| `resources/boost/skills/error-definition-development/SKILL.md` | Skill: workflow lengkap yang dimuat agent ketika tugasnya menyentuh error. |
| `resources/boost/guidelines/core.blade.php` | Guideline: aturan singkat yang selalu dimuat agent. |

Folder `resources/boost` dipakai karena itu lokasi yang dibaca Laravel Boost. Isinya tetap skill biasa dan tidak bergantung pada Boost.

## Claude Code

Salin folder skill ke project aplikasi:

```shell
mkdir -p .claude/skills
cp -r vendor/abu-abbas/error-definition-framework/resources/boost/skills/error-definition-development .claude/skills/
```

Claude Code memuat skill tersebut otomatis ketika tugas yang dikerjakan berkaitan dengan error. Salin ulang setelah meng-update package agar skill ikut ter-update.

Tempel isi `core.blade.php` ke `CLAUDE.md` jika ingin aturan singkatnya selalu dimuat.

## Claude.ai dan Claude Desktop

Kompres folder `error-definition-development` menjadi file zip, lalu upload sebagai custom skill melalui pengaturan Skills di Claude.

## Laravel Boost

Jika aplikasi memakai [Laravel Boost](https://laravel.com/docs/boost), skill dan guideline dapat dipasang otomatis:

```shell
composer require laravel/boost --dev
php artisan boost:install
```

Pilih `abu-abbas/error-definition-framework` ketika Boost menanyakan third-party guidelines/skills. Boost menyalin skill ke folder agent yang dipilih, misalnya `.claude/skills/`, dan menambahkan guideline ke file seperti `CLAUDE.md` atau `AGENTS.md`. Jalankan `php artisan boost:install` kembali setelah meng-update package.

## Agent Lain

Agent yang mendukung format Agent Skills dapat memakai folder skill yang sama. Salin folder tersebut ke lokasi skill milik agent terkait, dan tempel isi `core.blade.php` ke file instruksinya, misalnya `AGENTS.md`.
