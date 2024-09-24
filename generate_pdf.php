<?php
require 'vendor/autoload.php'; // Jika menggunakan Composer

use PhpOffice\PhpWord\TemplateProcessor;

include 'koneksi.php'; // Pastikan ini berisi koneksi database

// Cek apakah ID dikirim
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data mahasiswa berdasarkan ID
    $sql = "SELECT nama, nim, email, program_studi, timestamp FROM mahasiswa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $nama = $data['nama'];
        $nim = $data['nim'];
        $email = $data['email'];
        $program_studi = $data['program_studi'];
        $timestamp = $data['timestamp'];
    } else {
        die("Data tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID tidak tersedia.");
}

// Path ke template DOCX
$templatePath = 'template.docx';

// Buat instance TemplateProcessor
$templateProcessor = new TemplateProcessor($templatePath);

// Isi template dengan data mahasiswa
$templateProcessor->setValue('nama', $nama);
$templateProcessor->setValue('nim', $nim);
$templateProcessor->setValue('program_studi', $program_studi);
$templateProcessor->setValue('timestamp', $timestamp);

// Tentukan folder dan nama file untuk menyimpan dokumen
$folderPath = 'generated_docs'; // Pastikan folder ini ada dan dapat ditulis
if (!is_dir($folderPath)) {
    mkdir($folderPath, 0755, true);
}
$fileName = 'surat_keterangan_bebas_tanggungan_' . $id . '.docx';
$filePath = $folderPath . '/' . $fileName;

// Simpan dokumen yang telah diisi
$templateProcessor->saveAs($filePath);

// Tampilkan link untuk mendownload dokumen
echo "<script>
    alert('Dokumen telah berhasil disimpan. Anda dapat mengunduhnya dari link berikut.');
    window.location.href = 'download_docx.php?file=" . urlencode($fileName) . "';
</script>";

$conn->close();

?>