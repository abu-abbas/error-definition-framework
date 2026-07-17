# Istilah

[← Kembali ke Concepts](./README.md)

Glosarium ini menjelaskan istilah teknis yang digunakan dalam dokumentasi beserta perannya di dalam framework.

## A

- *Abstract Class*: Class yang tidak dapat dibuat langsung dan biasanya menjadi dasar bagi class lain. Discovery hanya mengembalikan concrete FormRequest yang benar-benar dapat digunakan.
- *ADR (Architecture Decision Record)*: Dokumen yang mencatat sebuah keputusan arsitektur, alasan pemilihannya, alternatif yang dipertimbangkan, dan konsekuensinya. Dokumen pada bagian Decisions menggunakan pendekatan ini.
- *API (Application Programming Interface)*: Kontrak yang menentukan cara aplikasi atau komponen berkomunikasi. Dalam framework ini, API dapat berupa public class, method, interface, atau struktur response.
- *Artisan*: Command-line interface bawaan Laravel untuk menjalankan command aplikasi, seperti migration, queue worker, dan Error Definition Linter.
- *AST (Abstract Syntax Tree)*: Representasi terstruktur dari source code yang biasa digunakan oleh parser dan alat static analysis. Linter tidak membangun AST karena kondisi runtime Laravel tetap tidak seluruhnya dapat dibuktikan dari source code.
- *Attribute*: Fitur metadata deklaratif yang tersedia secara native sejak PHP 8.0. Attribute dapat ditempelkan pada class, method, property, parameter, atau enum case, kemudian dibaca melalui Reflection. Berbeda dari PHPDoc annotation yang berupa komentar, Attribute merupakan bagian resmi dari sintaks PHP dan digunakan framework untuk menyimpan metadata error di dekat enum case yang bersangkutan.
- *Autofix*: Kemampuan linter mengubah source code secara otomatis untuk memperbaiki temuan. Error Definition Linter hanya melaporkan masalah karena metadata bisnis tidak aman untuk ditebak.
- *Autoload*: Mekanisme yang memuat file class PHP secara otomatis ketika class tersebut pertama kali digunakan. Discovery memakai autoloader Composer dan tidak menjalankan `require_once` ke seluruh file.

## B

- *Backed Enum*: Jenis enum yang tersedia sejak PHP 8.1 dan mewajibkan setiap case memiliki nilai `string` atau `int` yang unik. Berbeda dari Pure Enum yang hanya memiliki nama case, Backed Enum dapat dikonversi dari dan ke nilai dasarnya melalui `from()`, `tryFrom()`, dan property `value`. Framework menggunakannya agar setiap error memiliki kode string yang stabil untuk disimpan atau dipertukarkan antar-sistem.
- *Bail*: Aturan validation Laravel yang menghentikan pemeriksaan rule berikutnya pada field yang sama setelah satu rule gagal. Framework mempertahankan perilaku ini agar jumlah dan urutan error tetap mengikuti Laravel.
- *Breaking Change*: Perubahan kontrak yang dapat membuat consumer lama tidak lagi bekerja tanpa penyesuaian. Mengganti nama atau bentuk field response termasuk breaking change.
- *Business Rule*: Aturan yang berasal dari kebutuhan atau proses bisnis, misalnya surat yang sudah disetujui tidak boleh diubah. Business rule berbeda dari validation dasar seperti format email atau field wajib.

## C

- *Cache*: Penyimpanan sementara untuk data yang akan digunakan kembali agar tidak perlu dihitung atau dibaca berulang kali. Reader memakai in-memory cache untuk menyimpan metadata error yang sudah di-resolve selama process berjalan.
- *Camel Case*: Gaya penulisan beberapa kata tanpa spasi dengan huruf kapital pada awal kata berikutnya, misalnya `accessToken`. Sanitizer menormalkannya agar cocok dengan key seperti `access_token`.
- *Candidate*: Class yang berpotensi menjadi target sebelum kontrak tipenya diperiksa. Discovery membentuk candidate dari Composer autoload lalu menyaringnya menjadi enum error dan FormRequest.
- *Category*: Kelompok yang menunjukkan asal atau konteks sebuah error, misalnya validation, authentication, atau business rule. Category membantu consumer menentukan cara penanganan tanpa membaca pesan error.
- *CI (Continuous Integration)*: Proses otomatis yang menjalankan pemeriksaan seperti test dan linter setiap kali perubahan kode digabungkan atau dikirim ke repository.
- *Classmap*: Daftar yang memetakan FQCN langsung ke lokasi file PHP. Composer mendukungnya untuk class yang tidak mengikuti struktur PSR-4.
- *Class String*: String yang berisi FQCN dan digunakan untuk merujuk class tanpa membuat object-nya, misalnya `UserError::class`.
- *CLI (Command-Line Interface)*: Antarmuka berbasis perintah teks yang dijalankan melalui terminal. Artisan merupakan CLI milik Laravel.
- *Closure*: Fungsi tanpa nama yang dapat disimpan atau diteruskan sebagai nilai. Laravel menggunakannya antara lain untuk rule dan validation tambahan yang ditentukan saat runtime.
- *Composer*: Dependency manager PHP yang mengelola package dan autoload mapping aplikasi. Discovery menggunakan root `composer.json` sebagai sumber lokasi class.
- *Concrete Class*: Class yang dapat dibuat menjadi object dan bukan abstract class. Discovery hanya memasukkan concrete FormRequest.
- *Consumer*: Komponen atau aplikasi yang menggunakan data dari framework, misalnya exception handler, logger, API client, atau generator katalog error.
- *Container*: Komponen Laravel yang membuat object dan menyediakan dependency yang dibutuhkannya. Linter menggunakannya untuk mengevaluasi FormRequest seperti saat aplikasi berjalan.
- *Correlation ID*: Identifier yang menghubungkan log dari request atau proses yang sama di beberapa komponen. Framework menerima ID tersebut melalui runtime context, tetapi tidak membuatnya sendiri.
- *Credential*: Informasi rahasia yang digunakan untuk membuktikan identitas atau memperoleh akses, seperti password, token, dan API key. Credential harus disamarkan sebelum masuk log.

## D

- *Debug Mode*: Mode pengembangan yang menampilkan informasi tambahan untuk membantu mencari penyebab masalah. Framework tetap tidak mengirim context atau stack trace pada response publik ketika mode ini aktif.
- *Deduplication*: Proses menghapus item ganda sehingga setiap item hanya muncul satu kali. Discovery melakukan deduplication berdasarkan FQCN.
- *Dependency*: Komponen lain yang dibutuhkan agar sebuah komponen dapat bekerja. Runtime aplikasi sengaja tidak menjadikan Central Error Registry sebagai dependency agar tetap dapat menghasilkan error secara mandiri.
- *Deployment*: Proses merilis dan menjalankan versi aplikasi pada suatu environment. Metadata operasional dipisahkan dari source code agar perubahannya tidak selalu membutuhkan deployment baru.
- *Deterministic*: Sifat proses yang selalu menghasilkan output sama ketika menerima input yang sama. Discovery mengurutkan FQCN agar hasil tidak bergantung pada urutan filesystem.
- *Discovery*: Proses menemukan seluruh enum Error Definition yang tersedia dalam aplikasi tanpa membaca setiap enum secara manual. Mekanismenya harus tetap bekerja tanpa bergantung pada satu struktur folder tertentu.
- *Dot Notation*: Cara menulis field bertingkat dengan tanda titik sebagai pemisah level, misalnya `attachments.2.file`. Laravel menggunakannya untuk merepresentasikan input bersarang.
- *DTO (Data Transfer Object)*: Object sederhana yang membawa sekumpulan data antarbagian aplikasi tanpa memuat proses bisnis. Contohnya, `ResolvedErrorDefinition` membawa metadata error yang sudah dibaca dan siap digunakan.

## E

- *Enum (Enumeration)*: Tipe yang tersedia secara native sejak PHP 8.1 dan membatasi nilai ke dalam daftar case yang telah ditentukan. Framework menggunakan enum agar definisi error eksplisit, mudah ditemukan, dan aman diperiksa oleh static analysis.
- *Error Bag*: Wadah Laravel untuk mengelompokkan validation error. Named error bag memungkinkan beberapa form pada halaman yang sama menyimpan error dalam kelompok berbeda.
- *Exception*: Object yang menandai kegagalan atau kondisi tidak normal saat program berjalan. Exception menghentikan alur normal sampai ditangani oleh application layer yang sesuai.
- *Exception Handler*: Komponen Laravel yang menerima exception dan menentukan cara melaporkan atau mengubahnya menjadi response. Handler mendelegasikan exception milik framework kepada `ErrorResponseRenderer`.
- *Exit Code*: Angka yang dikembalikan command kepada sistem operasi ketika selesai. Nilai `0` menandakan berhasil, sedangkan `1` digunakan linter ketika pemeriksaan gagal.

## F

- *Fail Fast*: Pendekatan yang langsung menghentikan proses ketika konfigurasi atau kondisi yang tidak valid ditemukan. Tujuannya agar kesalahan terlihat di sumbernya dan tidak berubah menjadi hasil kosong atau error lanjutan yang lebih sulit dilacak.
- *False Positive*: Hasil pemeriksaan yang menyatakan ada masalah padahal kondisi sebenarnya valid. Linter harus menghindarinya agar laporan tetap dapat dipercaya.
- *Filesystem*: Sistem yang mengatur file dan folder pada media penyimpanan. Discovery memeriksa file PHP di dalam path Composer hanya ketika command dijalankan.
- *Final Class*: Class PHP yang tidak dapat diwariskan oleh class lain. Framework menggunakannya ketika variasi perilaku melalui subclass memang tidak dibutuhkan.
- *Fixture*: Data atau class buatan yang digunakan untuk menyiapkan kondisi test. Discovery mengabaikan `autoload-dev` agar fixture tidak masuk katalog production.
- *FormRequest*: Class Laravel yang menggabungkan authorization dan validation untuk sebuah request. Integrasi validation framework menggunakan FormRequest untuk memetakan rule yang gagal ke definisi error.
- *FQCN (Fully Qualified Class Name)*: Nama lengkap sebuah class beserta namespace-nya, misalnya `App\Rules\ValidEmployeeId`. FQCN digunakan ketika nama class harus dapat dikenali secara unik.

## G

- *Generator*: Komponen yang menghasilkan file turunan dari satu sumber data. Framework merencanakan generator untuk membuat katalog JSON dan daftar error code TypeScript dari Error Definition PHP.

## H

- *HTTP Status (Hypertext Transfer Protocol Status)*: Kode angka pada response HTTP yang menjelaskan hasil sebuah permintaan, misalnya `422` untuk input yang tidak valid atau `404` untuk resource yang tidak ditemukan.

## I

- *Immutable*: Sifat data yang tidak dapat diubah setelah dibuat. Metadata immutable aman disimpan dalam cache lintas request karena nilainya tetap selama process berjalan.
- *Inheritance*: Mekanisme ketika sebuah class mewarisi behavior dari parent class. Discovery tetap mengenali FormRequest yang memperoleh `HasErrorDefinitions` melalui inheritance.
- *Interface*: Kontrak PHP yang menetapkan kemampuan atau peran sebuah tipe tanpa menentukan seluruh implementasinya. Class atau enum yang mengimplementasikannya menyatakan bahwa kontrak tersebut dipenuhi.
- *Intersection Type*: Tipe PHP yang mewajibkan sebuah nilai memenuhi beberapa tipe sekaligus dan tersedia sejak PHP 8.1. Penulisan `ErrorCode&BackedEnum` berarti input harus merupakan `ErrorCode` sekaligus `BackedEnum`.
- *ISO 8601 (International Organization for Standardization 8601)*: Standar penulisan tanggal dan waktu yang tidak ambigu, misalnya `2026-07-17T10:30:00+07:00`. Sanitizer menggunakan format ini untuk nilai tanggal.
- *Iterable*: Nilai yang dapat ditelusuri satu per satu, seperti array atau object `Traversable`. Linter menerimanya agar target dapat diberikan dari sumber yang berbeda.
- *ITSM (Information Technology Service Management)*: Praktik untuk merancang, menyediakan, mengelola, dan meningkatkan layanan teknologi informasi. Metadata error dapat membantu proses ITSM seperti klasifikasi insiden, penentuan prioritas, dan penyusunan knowledge base.

## J

- *JSON (JavaScript Object Notation)*: Format teks terstruktur untuk pertukaran data antar-aplikasi. API umumnya menggunakannya untuk mengirim kode, pesan, dan metadata error kepada client.

## K

- *KB (Knowledge Base)*: Kumpulan artikel atau panduan untuk membantu pengguna dan tim support memahami serta menyelesaikan masalah. Kode error dapat dihubungkan ke artikel KB yang relevan.

## L

- *Lifecycle*: Rentang waktu sebuah object, process, atau data dibuat, digunakan, hingga dihentikan. Lifecycle menentukan berapa lama cache Reader dapat digunakan kembali.
- *Linter*: Alat pemeriksaan otomatis yang mencari kesalahan atau ketidakkonsistenan tanpa menjalankan seluruh alur aplikasi. Error Linter direncanakan untuk mengaudit definisi dan mapping error.
- *Locale*: Pengaturan bahasa dan wilayah yang digunakan aplikasi ketika memilih format atau terjemahan. Locale aktif menentukan bahasa validation message yang dihasilkan Laravel.
- *Localization*: Proses menyesuaikan teks dengan bahasa atau wilayah pengguna. Validation summary tetap menggunakan mekanisme Laravel agar mengikuti locale aplikasi.
- *Log Channel*: Tujuan atau jalur keluaran log yang dikonfigurasi aplikasi, misalnya file, syslog, atau layanan monitoring. Framework menggunakan channel default Laravel.
- *Log Level*: Tingkat kepentingan sebuah log event, seperti `info`, `warning`, `error`, atau `critical`. Reporter memilih level berdasarkan severity Error Definition.
- *Long-running Process*: Process yang tetap hidup untuk menangani banyak pekerjaan atau request, seperti Laravel Octane dan Queue Worker. Data per-request tidak boleh tertinggal pada process semacam ini.

## M

- *Manifest*: File yang menyimpan daftar atau metadata hasil proses sebelumnya agar dapat digunakan kembali. Discovery belum membuat manifest karena in-memory snapshot sudah cukup untuk satu command.
- *Marker Interface*: Interface tanpa method yang hanya menandai bahwa sebuah tipe dimaksudkan untuk peran tertentu. `ErrorCode` digunakan untuk membedakan enum error dari Backed Enum lain seperti status atau role.
- *Microservice*: Layanan kecil yang dikembangkan dan dijalankan secara terpisah untuk menangani kemampuan tertentu. Framework tidak mewajibkan arsitektur ini.
- *Middleware*: Komponen yang memproses request sebelum atau sesudah logic utama aplikasi. Middleware dapat menyediakan correlation ID sebelum exception terjadi.
- *Monolith*: Arsitektur aplikasi yang menempatkan banyak fitur atau modul dalam satu aplikasi yang dirilis bersama. Struktur berbasis domain tetap dapat digunakan tanpa harus memecah aplikasi menjadi microservice.

## N

- *Namespace*: Prefix yang mengelompokkan class PHP dan mencegah bentrok nama antarbagian aplikasi. PSR-4 memetakan namespace ke lokasi folder.

## O

- *Observability*: Kemampuan memahami kondisi internal aplikasi melalui log, metric, trace, dan informasi operasional lain. Error code dan context membantu proses investigasi tanpa harus mengekspos detail internal kepada pengguna.
- *Opt-in*: Pendekatan yang mengharuskan pemakai mengaktifkan sebuah fitur secara sengaja. FormRequest baru menggunakan integrasi Error Definition setelah memakai trait `HasErrorDefinitions`.
- *Ownership*: Tanggung jawab suatu modul atau konteks bisnis terhadap definisi dan aturan sebuah error. Lokasi halaman yang menampilkan error tidak otomatis mengubah ownership tersebut.

## P

- *PHP (PHP: Hypertext Preprocessor)*: Bahasa pemrograman yang digunakan untuk membangun framework ini. Fitur enum, attribute, exception, dan reflection PHP menjadi bagian utama desainnya.
- *Pipeline*: Rangkaian komponen yang memproses data atau kejadian secara berurutan. Reporter terhubung ke pipeline exception Laravel agar error dicatat pada satu boundary.
- *Placeholder*: Penanda dalam template pesan yang akan diganti dengan nilai sebenarnya, misalnya `:attribute` atau `:min`. Laravel memproses placeholder agar pesan tetap sesuai dengan field dan parameter validation.
- *PSR-3 (PHP Standards Recommendation 3)*: Standar interface logger pada ekosistem PHP. Framework menggunakannya agar logging tidak bergantung pada satu library atau tujuan log tertentu.
- *PSR-4 (PHP Standards Recommendation 4)*: Standar autoload yang memetakan namespace dan nama class ke struktur file. Discovery menggunakannya untuk membentuk candidate FQCN.
- *Pure Enum*: Enum PHP yang case-nya hanya memiliki nama tanpa nilai dasar `string` atau `int`. Framework memilih Backed Enum karena error code memerlukan nilai string yang stabil.

## R

- *Readonly*: Fitur PHP yang mencegah property diubah setelah diinisialisasi. Readonly property tersedia sejak PHP 8.1, sedangkan readonly class tersedia sejak PHP 8.2.
- *Recursive*: Proses yang menerapkan aturan yang sama kembali pada data di dalam data tersebut. Sanitizer bekerja secara recursive agar sensitive key di nested array tetap terlindungi.
- *Redaction*: Proses menghapus atau menyamarkan bagian data sensitif sebelum data digunakan atau disimpan. Framework menggunakan nilai `[REDACTED]` agar struktur context tetap terlihat tanpa mengekspos nilainya.
- *Reflection*: Fitur PHP untuk membaca struktur dan metadata kode saat aplikasi berjalan. `ErrorDefinitionReader` menggunakannya untuk membaca attribute dari enum case.
- *Regex (Regular Expression)*: Pola teks untuk memeriksa atau mencari bentuk string tertentu. Linter menggunakannya untuk memvalidasi format error code.
- *Registry*: Katalog terpusat yang mengumpulkan definisi error dari berbagai modul atau aplikasi. Registry dapat digunakan untuk pencarian, dokumentasi, dan integrasi operasional.
- *Renderer*: Komponen yang mengubah exception atau data error menjadi response yang dikirim kepada client. Renderer bertanggung jawab atas format response, bukan Reader atau exception.
- *Reporter*: Komponen yang mengubah exception menjadi event untuk logging atau monitoring. Reporter tidak mengubah response yang diterima client.
- *Resource*: Nilai khusus PHP yang mewakili koneksi atau handle eksternal seperti file dan stream. Sanitizer tidak mencoba menyerialisasi isinya.
- *Rule ID*: Identifier stabil untuk jenis temuan linter, misalnya `EDF003`. Rule ID memudahkan pencarian dan pengujian tanpa bergantung pada kalimat message.
- *Runtime*: Periode ketika aplikasi sedang berjalan dan memproses request, job, atau command. Informasi runtime dapat berbeda dari metadata error yang bersifat tetap.
- *Runtime Context*: Informasi mengenai kejadian tertentu ketika aplikasi berjalan, misalnya ID user atau ID dokumen. Data ini berguna untuk logging, tetapi harus disanitasi dan tidak otomatis dikirim kepada client.

## S

- *Sanitizer*: Komponen yang membersihkan, menghapus, atau menyamarkan data sensitif sebelum dikirim ke log dan monitoring. Sanitizer mencegah informasi seperti password atau token ikut tersimpan.
- *Scalar*: Nilai tunggal sederhana seperti string, integer, float, atau boolean. Scalar identifier lebih aman dan ringkas untuk runtime context daripada seluruh object.
- *Sentry*: Layanan monitoring yang merekam exception, stack trace, dan context untuk membantu diagnosis masalah aplikasi. Framework tidak bergantung langsung pada Sentry dan menggunakan integrasi logging milik aplikasi.
- *Serialization*: Proses mengubah object atau data menjadi format yang dapat disimpan atau dipertukarkan, misalnya JSON. Hasil resolve dibuat sederhana agar mudah diserialisasi.
- *Service Provider*: Class Laravel yang mendaftarkan dan mengaktifkan komponen aplikasi. Framework menggunakannya untuk mengaktifkan renderer tanpa registrasi manual pada setiap aplikasi.
- *Severity*: Tingkat dampak sebuah error terhadap pengguna, proses bisnis, atau layanan. Nilai ini membantu menentukan prioritas penanganan dan logging.
- *Side Effect*: Perubahan atau tindakan di luar nilai yang dikembalikan sebuah method, misalnya menulis log atau mengirim event. Logging tidak ditempatkan di dalam exception agar data carrier tidak memiliki side effect.
- *Singleton*: Pola penggunaan satu instance object yang sama selama lifecycle container atau process. Reader direkomendasikan sebagai singleton agar cache internalnya dapat digunakan kembali.
- *SLA (Service Level Agreement)*: Kesepakatan mengenai target kualitas layanan, seperti waktu respons, ketersediaan, atau waktu penyelesaian gangguan. Category dan severity dapat membantu menentukan SLA yang relevan.
- *Snapshot*: Salinan hasil pada satu titik proses yang digunakan kembali tanpa menghitung ulang. Discovery menyimpan satu in-memory snapshot selama command berjalan.
- *Source of Truth*: Sumber utama yang dianggap paling benar untuk suatu data. Error Definition PHP menjadi source of truth agar message dan metadata tidak didefinisikan ulang oleh setiap consumer.
- *Stack Trace*: Rekaman urutan pemanggilan function atau method sebelum exception terjadi. Previous exception dipertahankan agar jejak penyebab awal tidak hilang.
- *Static Analysis*: Pemeriksaan source code tanpa menjalankan aplikasinya untuk menemukan masalah tipe, kontrak, atau pola kode. Enum dan DTO yang typed membantu alat static analysis mendeteksi kesalahan lebih awal.
- *Strict Mode*: Mode pemeriksaan yang memperlakukan warning sebagai kegagalan. Linter menyediakan opsi `--strict` untuk project yang ingin menerapkan aturan lebih ketat.
- *Structured Log*: Log yang menyimpan informasi dalam field bernama, bukan hanya satu kalimat. Struktur ini memungkinkan pencarian berdasarkan error code, category, severity, atau identifier context.
- *Suppression*: Pengecualian yang menyuruh linter mengabaikan temuan tertentu. Framework belum menyediakannya agar pelanggaran kontrak tidak mudah disembunyikan.

## T

- *Throwable*: Interface dasar PHP untuk semua object yang dapat dilempar, termasuk `Exception` dan `Error`. Parameter previous menerima `Throwable` agar penyebab awal dapat dipertahankan.
- *Trait*: Mekanisme reuse kode pada PHP yang tersedia sejak PHP 5.4. Trait menambahkan method atau property ke sebuah class tanpa mewajibkan inheritance dari base class tertentu.
- *Type-safe*: Kondisi ketika bentuk dan tipe data diperiksa secara eksplisit sehingga nilai yang tidak sesuai dapat ditolak lebih awal. Enum, interface, dan DTO mengurangi penggunaan string atau array bebas yang rawan salah ketik.

## V

- *Validation Rule*: Aturan yang menentukan apakah sebuah nilai input dapat diterima, misalnya `required`, `email`, atau custom rule. Rule yang gagal dipetakan ke definisi error yang sesuai.
- *ValidationException*: Exception Laravel yang membawa informasi validation failure. Integrasi framework mempertahankan perilaku exception ini sambil menambahkan definisi error yang telah di-resolve.
- *Vendor Directory*: Folder `vendor/` yang berisi dependency Composer. Discovery tidak memindainya agar class package lain tidak otomatis masuk katalog aplikasi.

## W

- *Whitespace*: Karakter kosong seperti spasi, tab, atau baris baru. Message yang hanya berisi whitespace dianggap kosong oleh linter.
- *Wildcard*: Tanda `*` yang mewakili index atau bagian input dengan jumlah dinamis. Dalam Laravel validation, `attachments.*.file` dapat cocok dengan `attachments.0.file`, `attachments.1.file`, dan seterusnya.
