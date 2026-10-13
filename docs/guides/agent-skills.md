# Menggunakan Agent Skill

[← Kembali ke Guides](./README.md)

Package menyertakan Agent Skill bernama `error-definition-development`. Skill ini mengajarkan AI coding agent, seperti Claude, cara menulis error mengikuti framework ini: membuat enum error, melempar `ApplicationException`, memetakan validation, lalu menjalankan lint dan generate. Tanpa skill, agent cenderung menulis `abort()` atau message error secara bebas.

Skill memakai format standar [Agent Skills](https://agentskills.io) (folder berisi `SKILL.md`) yang dibuat Anthropic untuk Claude. Format yang sama juga dibaca agent lain yang mendukungnya. Instruksi ditulis dalam bahasa Inggris karena dibaca oleh agent, bukan oleh manusia.

| File | Isi |
|------|-----|
| `resources/boost/skills/error-definition-development/SKILL.md` | Skill: workflow lengkap yang dimuat agent ketika tugasnya menyentuh error. |
| `resources/boost/guidelines/core.blade.php` | Guideline: aturan singkat yang selalu dimuat agent. |

Folder `resources/boost` dipakai karena itu lokasi yang dibaca Laravel Boost. Isinya tetap skill biasa dan tidak bergantung pada Boost.

## Pakai Claude Code

1. Pasang package di aplikasi Laravel:

   ```shell
   composer require abu-abbas/error-definition-framework
   ```

2. Salin folder skill ke project aplikasi:

   ```shell
   mkdir -p .claude/skills
   cp -r vendor/abu-abbas/error-definition-framework/resources/boost/skills/error-definition-development .claude/skills/
   ```

3. Opsional: tempel isi `vendor/abu-abbas/error-definition-framework/resources/boost/guidelines/core.blade.php` ke `CLAUDE.md` agar aturan singkatnya selalu dimuat.
4. Commit folder `.claude/skills/` agar seluruh tim memakai skill yang sama.

Claude Code memuat skill tersebut otomatis ketika tugas yang dikerjakan berkaitan dengan error. Setelah meng-update package, ulangi langkah 2 agar skill ikut ter-update.

## Pakai Claude.ai atau Claude Desktop

1. Ambil folder `error-definition-development` dari `vendor/abu-abbas/error-definition-framework/resources/boost/skills/`, atau dari repository GitHub package ini.
2. Kompres folder tersebut menjadi file zip. Folder harus berada di dalam zip, bukan hanya isinya.
3. Upload file zip sebagai custom skill melalui pengaturan Skills di Claude.

Claude memakai skill tersebut ketika percakapan menyangkut pembuatan atau penanganan error dengan framework ini.

## Pakai Laravel Boost

[Laravel Boost](https://laravel.com/docs/boost) dapat memasang skill dan guideline sekaligus, ke Claude Code maupun agent lain yang dipilih.

1. Pasang Boost:

   ```shell
   composer require laravel/boost --dev
   ```

2. Jalankan installer:

   ```shell
   php artisan boost:install
   ```

3. Pilih fitur guidelines dan skills, lalu pilih `abu-abbas/error-definition-framework` ketika Boost menanyakan third-party guidelines/skills.

Boost menyalin skill ke folder agent yang dipilih, misalnya `.claude/skills/`, dan menambahkan guideline ke file seperti `CLAUDE.md` atau `AGENTS.md`. Pilihan package disimpan di `boost.json`:

```json
{
    "guidelines": true,
    "skills": ["error-definition-development"],
    "packages": ["abu-abbas/error-definition-framework"]
}
```

Setelah meng-update package, jalankan `php artisan boost:install` kembali agar skill ikut ter-update.

## Pakai Agent Lain

Agent yang mendukung format Agent Skills dapat memakai folder skill yang sama. Salin folder `error-definition-development` ke lokasi skill milik agent terkait, lalu tempel isi `core.blade.php` ke file instruksinya, misalnya `AGENTS.md`.

## Memastikan Skill Bekerja

Minta agent mengerjakan tugas yang menyangkut error, misalnya:

```text
Surat yang sudah diarsip tidak boleh dihapus.
```

Jika skill terpasang, agent akan menambahkan case baru di enum error dengan `#[ErrorDefinition]`, melempar `ApplicationException::of(...)`, lalu menjalankan `php artisan error-definition:lint` dan `php artisan error-definition:generate`. Jika agent justru menulis `abort(409, '...')`, skill belum terbaca.
