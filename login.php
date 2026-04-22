<?php
require_once 'config/connect_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $stmt = $conn->prepare("SELECT * FROM ims_user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            header('Location: select_event.php');
            exit;
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - SAC Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #ffffff 0%, #aaafb8 100%);
            padding: 40px 30px 30px;
            text-align: center;
        }
        .login-header img {
            max-width: 180px;
            height: auto;
            filter: brightness(1.1) contrast(1.05);
        }
        .login-header h2 {
            color: white;
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 0;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .login-body {
            padding: 35px 30px;
        }
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(42, 82, 152, 0.4);
        }
        .alert-danger {
            border-radius: 10px;
            border: none;
            background: #fff5f5;
            color: #c53030;
            padding: 12px 15px;
        }
        .login-footer {
            text-align: center;
            padding: 0 30px 25px;
            color: #666;
            font-size: 0.9rem;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .form-control.with-icon {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="img/logo/logo text-01.png" alt="Logo">
                <h2>ระบบบริหารจัดการข้อมูลงาน Event</h2>
            </div>
            
            <div class="login-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <div class="input-group">
                            <input type="text" class="form-control with-icon" name="username" placeholder="กรุณากรอกชื่อผู้ใช้" required autofocus>
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#666" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">รหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" class="form-control with-icon" name="password" placeholder="กรุณากรอกรหัสผ่าน" required>
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#666" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100">
                        <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <p class="mb-0">© 2026 SAC Event Management System</p>
            </div>
        </div>
    </div>
</body>
</html>