<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";


// Kalau sudah login, tidak perlu daftar lagi
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}


$error = "";
$success = "";


// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ambil data dari form
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $domisili = trim($_POST["domisili"] ?? "");
    $password = $_POST["password"] ?? "";
    $konfirmasi_password = $_POST["konfirmasi_password"] ?? "";


    // =========================
    // VALIDASI
    // =========================

    if (
        $nama === "" ||
        $email === "" ||
        $domisili === "" ||
        $password === "" ||
        $konfirmasi_password === ""
    ) {

        $error = "Semua data wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter.";

    } elseif ($password !== $konfirmasi_password) {

        $error = "Konfirmasi password tidak sama.";

    } else {

        // =========================
        // CEK EMAIL
        // =========================

        $stmt = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows > 0) {

            $error = "Email tersebut sudah terdaftar.";

        } else {

            // =========================
            // HASH PASSWORD
            // =========================

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // =========================
            // SIMPAN USER
            // =========================

            $stmt = $conn->prepare("
                INSERT INTO users
                (nama, email, domisili, password)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssss",
                $nama,
                $email,
                $domisili,
                $password_hash
            );


            if ($stmt->execute()) {

                $success = "Pendaftaran berhasil! Silakan login.";

            } else {

                $error = "Pendaftaran gagal. Silakan coba lagi.";

            }
        }
    }
}


$pageTitle = "Daftar - Remarket";

include __DIR__ . "/partials/header.php";

?>


<div class="form-card">

    <h1>
        Buat Akun ✨
    </h1>

    <p class="subtitle">
        Bergabung dengan Remarket dan mulai jual beli barang bekas yang masih bermanfaat.
    </p>


    <!-- PESAN ERROR -->

    <?php if ($error !== ""): ?>

        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- PESAN BERHASIL -->

    <?php if ($success !== ""): ?>

        <div
            class="alert"
            style="
                background: #eaf5f0;
                color: #2f6f5e;
            "
        >
            <?= htmlspecialchars($success) ?>
        </div>


        <a
            href="login.php"
            class="btn full"
            style="
                text-align: center;
                margin-top: 5px;
            "
        >
            Login Sekarang
        </a>

    <?php else: ?>


        <!-- FORM REGISTER -->

        <form
            method="POST"
            action=""
        >


            <!-- NAMA -->

            <div class="form-group">

                <label for="nama">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Contoh: Felicia"
                    value="<?= htmlspecialchars($_POST["nama"] ?? "") ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Contoh: felicia@gmail.com"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >

            </div>


            <!-- DOMISILI -->

            <div class="form-group">

                <label for="domisili">
                    Domisili
                </label>

                <input
                    type="text"
                    id="domisili"
                    name="domisili"
                    placeholder="Contoh: Jakarta Barat"
                    value="<?= htmlspecialchars($_POST["domisili"] ?? "") ?>"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    required
                >

            </div>


            <!-- KONFIRMASI PASSWORD -->

            <div class="form-group">

                <label for="konfirmasi_password">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    placeholder="Masukkan password lagi"
                    required
                >

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="btn full"
            >
                Daftar
            </button>


        </form>


        <!-- LOGIN -->

        <p
            style="
                text-align: center;
                margin-top: 20px;
                color: #718079;
            "
        >

            Sudah punya akun?

            <a
                href="login.php"
                style="
                    color: #2f6f5e;
                    font-weight: 700;
                "
            >
                Login di sini
            </a>

        </p>


    <?php endif; ?>


</div>


<?php

include __DIR__ . "/partials/footer.php";

?>