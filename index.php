<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - Sistem Inventory Aset Yayasan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0D0D0D;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: #1A1A1A;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            border: 1px solid #FF2A6D;
            box-shadow: 0 0 30px rgba(255, 42, 109, 0.2);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 25px;
                margin: 15px;
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 60px;
            color: #FF2A6D;
        }

        @media (max-width: 480px) {
            .logo i {
                font-size: 50px;
            }
            .logo h2 {
                font-size: 18px;
            }
        }

        .logo h2 {
            color: #FFFFFF;
            margin-top: 10px;
            font-size: 20px;
        }

        .logo p {
            color: #888;
            font-size: 12px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            color: #FFFFFF;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            background: #2A2A2A;
            border: 1px solid #333;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 14px;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #FF2A6D;
            box-shadow: 0 0 8px rgba(255, 42, 109, 0.3);
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #FF2A6D;
        }

        .checkbox label {
            color: #CCC;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #FF2A6D;
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #D91A5C;
            transform: translateY(-2px);
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
        }

        .footer-links a {
            color: #FF2A6D;
            text-decoration: none;
            font-size: 13px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .footer-links span {
            color: #555;
            margin: 0 10px;
        }

        .error-msg {
            background: rgba(255, 42, 109, 0.2);
            border: 1px solid #FF2A6D;
            color: #FF2A6D;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-chalkboard-user"></i>
            <h2>Yayasan Pendidikan Bangsa</h2>
            <p>Sistem Informasi Inventory Aset</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-triangle"></i> Login gagal! Periksa username dan password.
        </div>
        <?php endif; ?>

        <form method="POST" action="proses_login.php">
            <div class="input-group">
                <label><i class="fas fa-envelope"></i> Username</label>
                <input type="text" name="username" placeholder="admin" required>
            </div>

            <div class="input-group">
                <label><i class="fas fa-lock"></i> Kata Sandi</label>
                <input type="password" name="password" placeholder="******" required>
            </div>

            <div class="checkbox">
                <input type="checkbox" id="remember">
                <label for="remember">Ingatkan saya</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> MASUK
            </button>
        </form>

        <div class="footer-links">
            <a href="#">Lupa sandi?</a>
            <span>|</span>
            <a href="#">Hubungi Tim IT</a>
        </div>
    </div>
</body>
</html>