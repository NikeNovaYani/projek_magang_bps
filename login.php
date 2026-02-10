<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Rapat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Orisinal Anda Tetap Dipertahankan */
        * { box-sizing: border-box; font-family: "Arial", sans-serif; }
        body { margin: 0; padding: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%); overflow: hidden; position: relative; }
        .circle { position: absolute; border-radius: 50%; background: linear-gradient(135deg, rgba(25, 118, 210, 0.2), rgba(33, 150, 243, 0.1)); animation: float 20s infinite ease-in-out; z-index: 0; }
        .c1 { width: 300px; height: 300px; top: -50px; right: -50px; }
        .c2 { width: 200px; height: 200px; bottom: -50px; left: -50px; animation-delay: -5s; }
        .c3 { width: 150px; height: 150px; bottom: 50%; right: 20%; animation-delay: -10s; }
        @keyframes float { 0%, 100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-20px) scale(1.05); } }
        
        /* Modifikasi & Tambahan Style Form */
        .login-card { position: relative; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); width: 100%; max-width: 400px; padding: 40px 30px; border-radius: 20px; box-shadow: 0 15px 35px rgba(27, 110, 235, 0.15); text-align: center; border: 1px solid rgba(255, 255, 255, 0.5); z-index: 10; animation: slideUp 0.8s forwards ease-out; }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .logo-container { width: 120px; height: 120px; margin: 0 auto 15px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); animation: bounceIn 1s 0.3s forwards cubic-bezier(0.175, 0.885, 0.32, 1.275); transform: scale(0); }
        @keyframes bounceIn { to { transform: scale(1); } }
        .logo-container img { width: 100px; height: auto; }

        h1 { color: #1565c0; margin: 0; font-size: 22px; font-weight: 700; }
        p.subtitle { color: #64748b; margin: 10px 0 20px; font-size: 13px; }

        /* Style Input Group */
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group i { position: absolute; left: 15px; top: 40px; color: #1976d2; transition: 0.3s; }
        .input-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: bold; color: #475569; margin-left: 5px; }
        .input-group input { width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: white; outline: none; transition: all 0.3s ease; font-size: 14px; }
        .input-group input:focus { border-color: #1976d2; box-shadow: 0 0 0 4px rgba(25, 118, 210, 0.1); }
        .input-group input:focus + i { color: #1565c0; }

        /* Submit Button */
        .btn-login { width: 100%; border: none; display: flex; align-items: center; justify-content: center; gap: 10px; background: linear-gradient(135deg, #1976d2, #1565c0); color: white; padding: 14px; border-radius: 12px; font-weight: bold; font-size: 15px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(21, 101, 192, 0.3); margin-top: 10px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4); background: linear-gradient(135deg, #1e88e5, #1565c0); }
        .btn-login:active { transform: translateY(0); }

        .footer { margin-top: 25px; color: #94a3b8; font-size: 11px; }
    </style>
</head>

<body>
    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>

    <div class="login-card">
        <div class="logo-container">
            <img src="pdf/logo.png" alt="BPS Logo">
        </div>

        <h1>Selamat Datang</h1>
        <p class="subtitle">Sistem Manajemen Rapat BPS Kota Depok</p>

        <form action="login_user.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-group">
                <label>Password (Angka)</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
                <i class="fas fa-lock"></i>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Masuk ke Sistem
            </button>
        </form>

        <div class="footer">
            &copy; <?= date('Y') ?> BPS Kota Depok. All rights reserved.
        </div>
    </div>
</body>

</html>