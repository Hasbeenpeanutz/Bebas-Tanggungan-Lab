<?php
require 'vendor/autoload.php'; // Jika menggunakan Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpWord\TemplateProcessor;

include 'koneksi.php'; // Pastikan ini berisi koneksi database

// Cek apakah ID dikirim
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

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
$folderPath = __DIR__ . '/generated_docs'; 
if (!is_dir($folderPath)) {
    mkdir($folderPath, 0755, true);
}
$fileName = 'surat_keterangan_bebas_tanggungan_' . $id . '.docx';
$filePath = $folderPath . '/' . $fileName;

// Simpan dokumen yang telah diisi
$templateProcessor->saveAs($filePath);

// Kirim email
$mail = new PHPMailer(true);

try {
    // Pengaturan server
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'lkomftd@machung.ac.id'; 
    $mail->Password = 'qmrynloumzskbkfp'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Pengirim dan penerima
    $mail->setFrom('no-reply@gmail.com', 'Labkom FTD UMC'); 
    $mail->addAddress($email, $nama);

    // Konten email
    $mail->isHTML(true);
    $mail->Subject = 'SURAT KETERANGAN BEBAS TANGGUNGAN/PINJAMAN LABORATORIUM KOMPUTER FTD';
    $mail->Body = "<b>Dear $nama</b>,<br><br>
                Berikut kami lampirkan Surat Bebas Tanggungan Laboratorium Komputer FTD yang telah terverifikasi dan legal untuk digunakan sebagai persyaratan pendaftaran Yudisium.<br><br>
                Mohon tidak perlu membalas pesan otomatis ini. Terima kasih.<br><br><br>
                Best Regard,<br><br>
                <b>Laboratorium Komputer FTD - Universitas Ma Chung?</b>";

    // Menambahkan lampiran
    if (file_exists($filePath)) {
        $mail->addAttachment($filePath);
    } else {
        echo "<script>
            alert('File tidak ditemukan: $filePath');
            window.location.href = 'admin.php';
        </script>";
        exit();
    }

    // Kirim email
    $mail->send();

    // Hapus file setelah email dikirim
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $data = "$nama,$nim,$email,$program_studi,$timestamp\n";
    
    // Append data to responses.csv
    $file = fopen('responses.csv', 'a');
    fwrite($file, $data);
    fclose($file);

    $responses = [];
    if (file_exists('responses.csv')) {
        $file = fopen('responses.csv', 'r');
        while (($row = fgetcsv($file)) !== FALSE) {
            $responses[] = $row;
        }
        fclose($file);
    }

    // Update status to 'approved'
    $updateSql = "UPDATE mahasiswa SET status = 'approved' WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $id);
    $updateStmt->execute();
    $updateStmt->close();

    echo "<script>
        alert('Success!');
        window.location.href = 'admin.php';
    </script>";
} catch (Exception $e) {
    echo "<script>
        alert('Error: {$mail->ErrorInfo}');
        window.location.href = 'admin.php';
    </script>";
}

$conn->close();
