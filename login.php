<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";


if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}


$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if ($email === "" || $password === "") {

        $error = "Email dan password wajib diisi.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, nama, email, password
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();


        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["nama"] = $user["nama"];
            $_SESSION["email"] = $user["email"];


            header("Location: index.php");
            exit;

        } else {

            $error = "Email atau password salah.";

        }
    }
}


$pageTitle = "Login - Remarket";

include __DIR__ . "/partials/header.php";

?>


<div class="form-card">

    <h1>
        Selamat Datang 👋
    </h1>

    <p class="subtitle">
        Login untuk melanjutkan ke Remarket.
    </p>


    <?php if ($error !== ""): ?>

        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST">


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Masukkan email kamu"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password kamu"
                required
            >

        </div>


        <button
            type="submit"
            class="btn full"
        >
            Login
        </button>


    </form>


    <p class="form-footer">

        Belum punya akun?

        <a href="register.php">
            Daftar sekarang
        </a>

    </p>

</div>


<?php

include __DIR__ . "/partials/footer.php";

?>