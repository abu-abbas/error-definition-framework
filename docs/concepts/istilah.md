# Istilah

[← Kembali ke Concepts](./README.md)

Glosarium ini menjelaskan istilah teknis yang digunakan dalam dokumentasi beserta perannya di dalam framework.

## A

- *ADR (Architecture Decision Record)*: Dokumen yang mencatat sebuah keputusan arsitektur, alasan pemilihannya, alternatif yang dipertimbangkan, dan konsekuensinya. Dokumen pada bagian Decisions menggunakan pendekatan ini.
- *API (Application Programming Interface)*: Kontrak yang menentukan cara aplikasi atau komponen berkomunikasi. Dalam framework ini, API dapat berupa public class, method, interface, atau struktur response.
- *Attribute*: Fitur metadata deklaratif yang tersedia secara native sejak PHP 8.0. Attribute dapat ditempelkan pada class, method, property, parameter, atau enum case, kemudian dibaca melalui Reflection. Berbeda dari PHPDoc annotation yang berupa komentar, Attribute merupakan bagian resmi dari sintaks PHP dan digunakan framework untuk menyimpan metadata error di dekat enum case yang bersangkutan.

## B

- *Backed Enum*: Jenis enum yang tersedia sejak PHP 8.1 dan mewajibkan setiap case memiliki nilai `string` atau `int` yang unik. Berbeda dari Pure Enum yang hanya memiliki nama case, Backed Enum dapat dikonversi dari dan ke nilai dasarnya melalui `from()`, `tryFrom()`, dan property `value`. Framework menggunakannya agar setiap error memiliki kode string yang stabil untuk disimpan atau dipertukarkan antar-sistem.

## C

- *Category*: Kelompok yang menunjukkan asal atau konteks sebuah error, misalnya validation, authentication, atau business rule. Category membantu consumer menentukan cara penanganan tanpa membaca pesan error.
- *Consumer*: Komponen atau aplikasi yang menggunakan data dari framework, misalnya exception handler, logger, API client, atau generator katalog error.

## D

- *DTO (Data Transfer Object)*: Object sederhana yang membawa sekumpulan data antarbagian aplikasi tanpa memuat proses bisnis. Contohnya, `ResolvedErrorDefinition` membawa metadata error yang sudah dibaca dan siap digunakan.

## E

- *Enum (Enumeration)*: Tipe yang membatasi nilai ke dalam daftar case yang telah ditentukan. Framework menggunakan enum agar definisi error eksplisit, mudah ditemukan, dan aman diperiksa oleh static analysis.

## F

- *FormRequest*: Class Laravel yang menggabungkan authorization dan validation untuk sebuah request. Integrasi validation framework menggunakan FormRequest untuk memetakan rule yang gagal ke definisi error.
- *FQCN (Fully Qualified Class Name)*: Nama lengkap sebuah class beserta namespace-nya, misalnya `App\Rules\ValidEmployeeId`. FQCN digunakan ketika nama class harus dapat dikenali secara unik.

## H

- *HTTP Status (Hypertext Transfer Protocol Status)*: Kode angka pada response HTTP yang menjelaskan hasil sebuah permintaan, misalnya `422` untuk input yang tidak valid atau `404` untuk resource yang tidak ditemukan.

## I

- *ITSM (Information Technology Service Management)*: Praktik untuk merancang, menyediakan, mengelola, dan meningkatkan layanan teknologi informasi. Metadata error dapat membantu proses ITSM seperti klasifikasi insiden, penentuan prioritas, dan penyusunan knowledge base.

## J

- *JSON (JavaScript Object Notation)*: Format teks terstruktur untuk pertukaran data antar-aplikasi. API umumnya menggunakannya untuk mengirim kode, pesan, dan metadata error kepada client.

## K

- *KB (Knowledge Base)*: Kumpulan artikel atau panduan untuk membantu pengguna dan tim support memahami serta menyelesaikan masalah. Kode error dapat dihubungkan ke artikel KB yang relevan.

## P

- *PHP (PHP: Hypertext Preprocessor)*: Bahasa pemrograman yang digunakan untuk membangun framework ini. Fitur enum, attribute, exception, dan reflection PHP menjadi bagian utama desainnya.

## R

- *Reflection*: Fitur PHP untuk membaca struktur dan metadata kode saat aplikasi berjalan. `ErrorDefinitionReader` menggunakannya untuk membaca attribute dari enum case.
- *Registry*: Katalog terpusat yang mengumpulkan definisi error dari berbagai modul atau aplikasi. Registry dapat digunakan untuk pencarian, dokumentasi, dan integrasi operasional.
- *Runtime*: Periode ketika aplikasi sedang berjalan dan memproses request, job, atau command. Informasi runtime dapat berbeda dari metadata error yang bersifat tetap.

## S

- *Severity*: Tingkat dampak sebuah error terhadap pengguna, proses bisnis, atau layanan. Nilai ini membantu menentukan prioritas penanganan dan logging.
- *SLA (Service Level Agreement)*: Kesepakatan mengenai target kualitas layanan, seperti waktu respons, ketersediaan, atau waktu penyelesaian gangguan. Category dan severity dapat membantu menentukan SLA yang relevan.

## V

- *Validation Rule*: Aturan yang menentukan apakah sebuah nilai input dapat diterima, misalnya `required`, `email`, atau custom rule. Rule yang gagal dipetakan ke definisi error yang sesuai.
- *ValidationException*: Exception Laravel yang membawa informasi validation failure. Integrasi framework mempertahankan perilaku exception ini sambil menambahkan definisi error yang telah di-resolve.
