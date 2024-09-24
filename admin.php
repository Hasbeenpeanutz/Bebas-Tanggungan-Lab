<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Data Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <style>
        /* Full page wrapper */
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: "Poppins", sans-serif;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Centering container */
        .table-container {
            width: 50%;
            display: flex;
            justify-content: center;
            box-sizing: border-box;
            flex: 1;
        }

        table {
            width: 100%;
            max-width: 1200px;
            border-collapse: collapse;
            font-family: "Poppins", sans-serif;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 13px;
            text-align: center;
        }

        th {
            background-color: #d1d1d1;
        }

        .verify-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: not-allowed;
        }

        .verify-button.active {
            cursor: pointer;
        }

        .verify-button.approved {
            background-color: #888;
            cursor: not-allowed;
        }

        .heading-container {
            text-align: center;
            padding-top: 3rem;
        }

        /* Footer styling */
        footer {
            background-color: #f8f8f8;
            padding-top: 20px;
            padding-bottom: 20px;
            margin-top: 3rem;
            text-align: center;
            font-family: "Poppins", sans-serif;
            font-size: 16px;
            width: 100%;
        }

        footer a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 10px;
        }

        footer .social-icons {
            margin-top: 10px;
        }

        /* Media Queries */
        @media (max-width: 992px) {
            html {
                font-size: 75%;
            }

            footer {
                font-size: 100%;
            }
        }

        @media(max-width: 768px) {
            html {
                font-size: 65%;
            }
        }

        @media(max-width: 576px) {
            html {
                font-size: 60%;
            }

            table {
                max-width: 576px;
            }
        }
    </style>
    <script>
        function toggleTimestamp(row) {
            var timestamp = row.getElementsByClassName('timestamp')[0];
            var verifyButton = row.getElementsByClassName('verify-button')[0];
            var checkbox = row.getElementsByClassName('show-timestamp-checkbox')[0];

            if (checkbox.checked) {
                timestamp.style.display = 'table-cell';
                verifyButton.disabled = false;
                verifyButton.classList.add('active');
            } else {
                timestamp.style.display = 'none';
                verifyButton.disabled = true;
                verifyButton.classList.remove('active');
            }
        }

        function verifyData(form) {
            form.submit();
        }

        function updateButtonStatus(row, status) {
            var verifyButton = row.getElementsByClassName('verify-button')[0];
            if (status === 'approved') {
                verifyButton.classList.add('approved');
                verifyButton.classList.remove('active');
                verifyButton.disabled = true;
                verifyButton.textContent = 'Approved';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Update button statuses based on stored data
            var rows = document.querySelectorAll('table tr');
            rows.forEach(row => {
                var status = row.getAttribute('data-status');
                if (status === 'approved') {
                    updateButtonStatus(row, 'approved');
                }
            });
        });
    </script>
</head>

<body>
    <div class="heading-container">
        <h1>Data Mahasiswa</h1>
    </div>

    <div class="content">
        <div class="table-container">
            <?php
            include 'koneksi.php';

            $sql = "SELECT id, nama, nim, email, program_studi, timestamp, status FROM mahasiswa"; // Include 'status'
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "table>";
                echo "<tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Timestamp</th>
                <th>Status</th> <!-- New Column for Status -->
                <th>Action</th>
                </tr>";

                while ($row = $result->fetch_assoc()) {
                    $status = $row["status"] ? $row["status"] : 'pending'; // Default to 'pending' if no status

                    echo "<tr data-status='$status'>";
                    echo "<td class='id'>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nama"] . "</td>";
                    echo "<td>" . $row["nim"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["program_studi"] . "</td>";
                    echo "<td>";
                    echo "<input type='checkbox' class='show-timestamp-checkbox' onchange='toggleTimestamp(this.parentNode.parentNode)'>";
                    echo "<span class='timestamp' style='display: none;'>" . $row["timestamp"] . "</span>";
                    echo "</td>";
                    echo "<td>" . ucfirst($status) . "</td>"; // Display status
                    echo "<td>";
                    echo "<form method='post' action='verify.php'>"; // Mengarah ke verify.php
                    echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                    echo "<button type='button' class='verify-button " . ($status == 'approved' ? 'approved' : 'active') . "' " . ($status == 'approved' ? 'disabled' : '') . " onclick='verifyData(this.form)'>" . ($status == 'approved' ? 'Approved' : 'Verify Data') . "</button>";
                    echo "</form>";
                    echo "</td>";

                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Tidak ada data yang ditemukan.";
            };

            $conn->close();
            ?>
        </div>
    </div>

    <footer>
        <small>&copy; 2024 DB Form Bebas Tanggungan Lab. </small>
        <small>Design by <a href="https://instagram.com/hasbeenpeanutz">@hasbeenpeanutz</a>.</small>
    </footer>

</body>

</html>