<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Rapat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Arial", sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Elements */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(25, 118, 210, 0.2), rgba(33, 150, 243, 0.1));
            animation: float 20s infinite ease-in-out;
            z-index: 0;
        }

        .c1 {
            width: 300px;
            height: 300px;
            top: -50px;
            right: -50px;
            animation-delay: 0s;
        }

        .c2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: -5s;
        }

        .c3 {
            width: 150px;
            height: 150px;
            bottom: 50%;
            right: 20%;
            animation-delay: -10s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-20px) scale(1.05);
            }
        }

        /* Login Card */
        .login-card {
            position: relative;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(27, 110, 235, 0.15);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            z-index: 10;
            transform: translateY(30px);
            opacity: 0;
            animation: slideUp 0.8s forwards ease-out;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo-container {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            animation: bounceIn 1s 0.3s forwards cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: scale(0);
        }

        @keyframes bounceIn {
            to {
                transform: scale(1);
            }
        }

        .logo-container img {
            width: 150px;
            height: auto;
        }

        h1 {
            color: #1565c0;
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: 700;
        }

        p.subtitle {
            color: #64748b;
            margin: 15px 0 30px;
            font-size: 14px;
        }

        .divider {
            height: 2px;
            width: 50px;
            background: #e3f2fd;
            margin: 0 auto 30px;
            border-radius: 2px;
        }

        /* SSO Button */
        .btn-sso {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #1976d2, #1565c0);
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(21, 101, 192, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-sso:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
            background: linear-gradient(135deg, #1e88e5, #1565c0);
        }

        .btn-sso:active {
            transform: translateY(0);
        }

        .btn-sso i {
            font-size: 18px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- Background Shapes -->
    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>

    <div class="login-card">
        <div class="logo-container">
            <!-- Ensure correct path to logo -->
            <img src="pdf/logo.png" alt="BPS Logo">
        </div>

        <h1>Selamat Datang</h1>
        <p class="subtitle">Sistem Manajemen Rapat BPS Kota Depok</p>
        <div class="divider"></div>

        <a href="https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/auth?state=29d3499bf3346bbdae7d075ee1e91936&scope=profile-pegawai%2Cemail&response_type=code&approval_prompt=auto&redirect_uri=https%3A%2F%2Fdaftarhadir.web.bps.go.id%2Fapi_v2%2Fakses%2Flogin%2Fsso%2F%3Fs&client_id=03340-daftarhadir-4d5" class="btn-sso">
            <i class="fas fa-sign-in-alt"></i>
            Login With Single Sign On BPS
        </a>

        <div class="footer">
            &copy; <?= date('Y') ?> BPS Kota Depok. All rights reserved.
        </div>
    </div>

</body>

</html>