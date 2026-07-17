# Istilah

[← Kembali ke Concepts](./README.md)

Glosarium ini menjelaskan istilah teknis yang digunakan dalam dokumentasi beserta perannya di dalam framework.

## A

- *ADR (Architecture Decision Record)*: Dokumen yang mencatat sebuah keputusan arsitektur, alasan pemilihannya, alternatif yang dipertimbangkan, dan konsekuensinya. Dokumen pada bagian Decisions menggunakan pendekatan ini.
- *API (Application Programming Interface)*: Kontrak yang menentukan cara aplikasi atau komponen berkomunikasi. Dalam framework ini, API dapat berupa public class, method, interface, atau struktur response.
- *Attribute*: Fitur metadata deklaratif yang tersedia secara native sejak PHP 8.0. Attribute dapat ditempelkan pada class, method, property, parameter, atau enum case, kemudian dibaca melalui Reflection. Berbeda dari PHPDoc annotation yang berupa komentar, Attribute merupakan bagian resmi dari sintaks PHP dan digunakan framework untuk menyimpan metadata error di dekat enum case yang bersangkutan.

## B

- *Backed Enum*: Jenis enum yang tersedia sejak PHP 8.1 dan mewajibkan setiap case memiliki nilai `string` atau `int` yang unik. Berbeda dari Pure Enum yang hanya memiliki nama case, Backed Enum dapat dikonversi dari dan ke nilai dasarnya melalui `from()`, `tryFrom()`, dan property `value`. Framework menggunakannya agar setiap error memiliki kode string yang stabil untuk disimpan atau dipertukarkan antar-sistem.
- *Bail*: Aturan validation Laravel yang menghentikan pemeriksaan rule berikutnya pada field yang sama setelah satu rule gagal. Framework mempertahankan perilaku ini agar jumlah dan urutan error tetap mengikuti Laravel.
- *Breaking Change*: Perubahan kontrak yang dapat membuat consumer lama tidak lagi bekerja tanpa penyesuaian. Mengganti nama atau bentuk field response termasuk breaking change.
- *Business Rule*: Aturan yang berasal dari kebutuhan atau proses bisnis, misalnya surat yang sudah disetujui tidak boleh diubah. Business rule berbeda dari validation dasar seperti format email atau field wajib.

## C

- *Cache*: Penyimpanan sementara untuk data yang akan digunakan kembali agar tidak perlu dihitung atau dibaca berulang kali. Reader memakai in-memory cache untuk menyimpan metadata error yang sudah di-resolve selama process berjalan.
- *Category*: Kelompok yang menunjukkan asal atau konteks sebuah error, misalnya validation, authentication, atau business rule. Category membantu consumer menentukan cara penanganan tanpa membaca pesan error.
- *Closure*: Fungsi tanpa nama yang dapat disimpan atau diteruskan sebagai nilai. Laravel menggunakannya antara lain untuk rule dan validation tambahan yang ditentukan saat runtime.
- *Consumer*: Komponen atau aplikasi yang menggunakan data dari framework, misalnya exception handler, logger, API client, atau generator katalog error.

## D

- *Debug Mode*: Mode pengembangan yang menampilkan informasi tambahan untuk membantu mencari penyebab masalah. Framework tetap tidak mengirim context atau stack trace pada response publik ketika mode ini aktif.
- *Dependency*: Komponen lain yang dibutuhkan agar sebuah komponen dapat bekerja. Runtime aplikasi sengaja tidak menjadikan Central Error Registry sebagai dependency agar tetap dapat menghasilkan error secara mandiri.
- *Deployment*: Proses merilis dan menjalankan versi aplikasi pada suatu environment. Metadata operasional dipisahkan dari source code agar perubahannya tidak selalu membutuhkan deployment baru.
- *Discovery*: Proses menemukan seluruh enum Error Definition yang tersedia dalam aplikasi tanpa membaca setiap enum secara manual. Mekanismenya harus tetap bekerja tanpa bergantung pada satu struktur folder tertentu.
- *Dot Notation*: Cara menulis field bertingkat dengan tanda titik sebagai pemisah level, misalnya `attachments.2.file`. Laravel menggunakannya untuk merepresentasikan input bersarang.
- *DTO (Data Transfer Object)*: Object sederhana yang membawa sekumpulan data antarbagian aplikasi tanpa memuat proses bisnis. Contohnya, `ResolvedErrorDefinition` membawa metadata error yang sudah dibaca dan siap digunakan.

## E

- *Enum (Enumeration)*: Tipe yang tersedia secara native sejak PHP 8.1 dan membatasi nilai ke dalam daftar case yang telah ditentukan. Framework menggunakan enum agar definisi error eksplisit, mudah ditemukan, dan aman diperiksa oleh static analysis.
- *Error Bag*: Wadah Laravel untuk mengelompokkan validation error. Named error bag memungkinkan beberapa form pada halaman yang sama menyimpan error dalam kelompok berbeda.
- *Exception*: Object yang menandai kegagalan atau kondisi tidak normal saat program berjalan. Exception menghentikan alur normal sampai ditangani oleh application layer yang sesuai.
- *Exception Handler*: Komponen Laravel yang menerima exception dan menentukan cara melaporkan atau mengubahnya menjadi response. Handler mendelegasikan exception milik framework kepada `ErrorResponseRenderer`.

## F

- *Fail Fast*: Pendekatan yang langsung menghentikan proses ketika konfigurasi atau kondisi yang tidak valid ditemukan. Tujuannya agar kesalahan terlihat di sumbernya dan tidak berubah menjadi hasil kosong atau error lanjutan yang lebih sulit dilacak.
- *False Positive*: Hasil pemeriksaan yang menyatakan ada masalah padahal kondisi sebenarnya valid. Linter harus menghindarinya agar laporan tetap dapat dipercaya.
- *Final Class*: Class PHP yang tidak dapat diwariskan oleh class lain. Framework menggunakannya ketika variasi perilaku melalui subclass memang tidak dibutuhkan.
- *FormRequest*: Class Laravel yang menggabungkan authorization dan validation untuk sebuah request. Integrasi validation framework menggunakan FormRequest untuk memetakan rule yang gagal ke definisi error.
- *FQCN (Fully Qualified Class Name)*: Nama lengkap sebuah class beserta namespace-nya, misalnya `App\Rules\ValidEmployeeId`. FQCN digunakan ketika nama class harus dapat dikenali secara unik.

## G

- *Generator*: Komponen yang menghasilkan file turunan dari satu sumber data. Framework merencanakan generator untuk membuat katalog JSON dan daftar error code TypeScript dari Error Definition PHP.

## H

- *HTTP Status (Hypertext Transfer Protocol Status)*: Kode angka pada response HTTP yang menjelaskan hasil sebuah permintaan, misalnya `422` untuk input yang tidak valid atau `404` untuk resource yang tidak ditemukan.

## I

- *Immutable*: Sifat data yang tidak dapat diubah setelah dibuat. Metadata immutable aman disimpan dalam cache lintas request karena nilainya tetap selama process berjalan.
- *Interface*: Kontrak PHP yang menetapkan kemampuan atau peran sebuah tipe tanpa menentukan seluruh implementasinya. Class atau enum yang mengimplementasikannya menyatakan bahwa kontrak tersebut dipenuhi.
- *Intersection Type*: Tipe PHP yang mewajibkan sebuah nilai memenuhi beberapa tipe sekaligus dan tersedia sejak PHP 8.1. Penulisan `ErrorCode&BackedEnum` berarti input harus merupakan `ErrorCode` sekaligus `BackedEnum`.
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
- *Long-running Process*: Process yang tetap hidup untuk menangani banyak pekerjaan atau request, seperti Laravel Octane dan Queue Worker. Data per-request tidak boleh tertinggal pada process semacam ini.

## M

- *Marker Interface*: Interface tanpa method yang hanya menandai bahwa sebuah tipe dimaksudkan untuk peran tertentu. `ErrorCode` digunakan untuk membedakan enum error dari Backed Enum lain seperti status atau role.
- *Microservice*: Layanan kecil yang dikembangkan dan dijalankan secara terpisah untuk menangani kemampuan tertentu. Framework tidak mewajibkan arsitektur ini.
- *Monolith*: Arsitektur aplikasi yang menempatkan banyak fitur atau modul dalam satu aplikasi yang dirilis bersama. Struktur berbasis domain tetap dapat digunakan tanpa harus memecah aplikasi menjadi microservice.

## O

- *Observability*: Kemampuan memahami kondisi internal aplikasi melalui log, metric, trace, dan informasi operasional lain. Error code dan context membantu proses investigasi tanpa harus mengekspos detail internal kepada pengguna.
- *Opt-in*: Pendekatan yang mengharuskan pemakai mengaktifkan sebuah fitur secara sengaja. FormRequest baru menggunakan integrasi Error Definition setelah memakai trait `HasErrorDefinitions`.
- *Ownership*: Tanggung jawab suatu modul atau konteks bisnis terhadap definisi dan aturan sebuah error. Lokasi halaman yang menampilkan error tidak otomatis mengubah ownership tersebut.

## P

- *Placeholder*: Penanda dalam template pesan yang akan diganti dengan nilai sebenarnya, misalnya `:attribute` atau `:min`. Laravel memproses placeholder agar pesan tetap sesuai dengan field dan parameter validation.
- *PHP (PHP: Hypertext Preprocessor)*: Bahasa pemrograman yang digunakan untuk membangun framework ini. Fitur enum, attribute, exception, dan reflection PHP menjadi bagian utama desainnya.
- *Pure Enum*: Enum PHP yang case-nya hanya memiliki nama tanpa nilai dasar `string` atau `int`. Framework memilih Backed Enum karena error code memerlukan nilai string yang stabil.

## R

- *Readonly*: Fitur PHP yang mencegah property diubah setelah diinisialisasi. Readonly property tersedia sejak PHP 8.1, sedangkan readonly class tersedia sejak PHP 8.2.
- *Reflection*: Fitur PHP untuk membaca struktur dan metadata kode saat aplikasi berjalan. `ErrorDefinitionReader` menggunakannya untuk membaca attribute dari enum case.
- *Registry*: Katalog terpusat yang mengumpulkan definisi error dari berbagai modul atau aplikasi. Registry dapat digunakan untuk pencarian, dokumentasi, dan integrasi operasional.
- *Renderer*: Komponen yang mengubah exception atau data error menjadi response yang dikirim kepada client. Renderer bertanggung jawab atas format response, bukan Reader atau exception.
- *Runtime*: Periode ketika aplikasi sedang berjalan dan memproses request, job, atau command. Informasi runtime dapat berbeda dari metadata error yang bersifat tetap.
- *Runtime Context*: Informasi mengenai kejadian tertentu ketika aplikasi berjalan, misalnya ID user atau ID dokumen. Data ini berguna untuk logging, tetapi harus disanitasi dan tidak otomatis dikirim kepada client.

## S

- *Sanitizer*: Komponen yang membersihkan, menghapus, atau menyamarkan data sensitif sebelum dikirim ke log dan monitoring. Sanitizer mencegah informasi seperti password atau token ikut tersimpan.
- *Serialization*: Proses mengubah object atau data menjadi format yang dapat disimpan atau dipertukarkan, misalnya JSON. Hasil resolve dibuat sederhana agar mudah diserialisasi.
- *Service Provider*: Class Laravel yang mendaftarkan dan mengaktifkan komponen aplikasi. Framework menggunakannya untuk mengaktifkan renderer tanpa registrasi manual pada setiap aplikasi.
- *Severity*: Tingkat dampak sebuah error terhadap pengguna, proses bisnis, atau layanan. Nilai ini membantu menentukan prioritas penanganan dan logging.
- *Singleton*: Pola penggunaan satu instance object yang sama selama lifecycle container atau process. Reader direkomendasikan sebagai singleton agar cache internalnya dapat digunakan kembali.
- *SLA (Service Level Agreement)*: Kesepakatan mengenai target kualitas layanan, seperti waktu respons, ketersediaan, atau waktu penyelesaian gangguan. Category dan severity dapat membantu menentukan SLA yang relevan.
- *Source of Truth*: Sumber utama yang dianggap paling benar untuk suatu data. Error Definition PHP menjadi source of truth agar message dan metadata tidak didefinisikan ulang oleh setiap consumer.
- *Stack Trace*: Rekaman urutan pemanggilan function atau method sebelum exception terjadi. Previous exception dipertahankan agar jejak penyebab awal tidak hilang.
- *Static Analysis*: Pemeriksaan source code tanpa menjalankan aplikasinya untuk menemukan masalah tipe, kontrak, atau pola kode. Enum dan DTO yang typed membantu alat static analysis mendeteksi kesalahan lebih awal.

## T

- *Throwable*: Interface dasar PHP untuk semua object yang dapat dilempar, termasuk `Exception` dan `Error`. Parameter previous menerima `Throwable` agar penyebab awal dapat dipertahankan.
- *Trait*: Mekanisme reuse kode pada PHP yang tersedia sejak PHP 5.4. Trait menambahkan method atau property ke sebuah class tanpa mewajibkan inheritance dari base class tertentu.
- *Type-safe*: Kondisi ketika bentuk dan tipe data diperiksa secara eksplisit sehingga nilai yang tidak sesuai dapat ditolak lebih awal. Enum, interface, dan DTO mengurangi penggunaan string atau array bebas yang rawan salah ketik.

## V

- *Validation Rule*: Aturan yang menentukan apakah sebuah nilai input dapat diterima, misalnya `required`, `email`, atau custom rule. Rule yang gagal dipetakan ke definisi error yang sesuai.
- *ValidationException*: Exception Laravel yang membawa informasi validation failure. Integrasi framework mempertahankan perilaku exception ini sambil menambahkan definisi error yang telah di-resolve.

## W

- *Wildcard*: Tanda `*` yang mewakili index atau bagian input dengan jumlah dinamis. Dalam Laravel validation, `attachments.*.file` dapat cocok dengan `attachments.0.file`, `attachments.1.file`, dan seterusnya.
