<?php
include 'koneksi.php';

$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$nim = isset($_POST['nim']) ? $_POST['nim'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$program_studi = isset($_POST['program_studi']) ? $_POST['program_studi'] : '';
$timestamp = date("Y-m-d H:i:s");


$sql = "INSERT INTO mahasiswa (nama, nim, email, program_studi, timestamp) VALUES ('$nama', '$nim', '$email', '$program_studi', '$timestamp')";

if ($conn->query($sql) === TRUE) {

    echo "<script>
            alert('Data berhasil disimpan!');
            window.location.href = 'admin.php';
          </script>";
} else {
    echo "Terjadi kesalahan: " . $conn->error;
}


$conn->close();
