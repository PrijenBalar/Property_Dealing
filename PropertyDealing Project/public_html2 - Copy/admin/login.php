<?php
include "../includes/config.php";

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Server-side validation (Double Security)
    if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
        $error = "Username only allows characters and spaces.";
    } else {
        // Secure Fetch with Prepared Statement
        $stmt = mysqli_prepare($con, "SELECT * FROM tbl_admin WHERE username=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);

            // Plain password check (Matches current DB structure)
            if($password === $row['password']){
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_user'] = $row['username'];

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Username not found.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Kargil Property</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Toastr for Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
      body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .login-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        overflow: hidden;
        border: none;
      }
      .login-header {
        background: #f8fafc;
        padding: 30px 20px 20px;
        border-bottom: 1px solid #e2e8f0;
      }
      .form-control {
        padding: 12px 16px;
        border-radius: 8px;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
      }
      .form-control:focus {
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        border-color: #0ea5e9;
        outline: none;
      }
      .btn-primary {
        background: #0ea5e9;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
      }
      .btn-primary:hover {
        background: #0284c7;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
      }
    </style>
</head>

<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4">

      <div class="login-card">
        <div class="login-header text-center">
          <i class="fa fa-user-shield text-primary mb-2" style="font-size: 2.5rem;"></i>
          <h4 class="fw-bold text-dark mb-0">Admin Portal</h4>
          <p class="text-secondary small mt-1">Sign in to manage Kargil Property</p>
        </div>
        
        <div class="card-body p-4 p-md-5">

          <form id="loginForm" method="POST" autocomplete="off" novalidate>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Username</label>
              <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label small fw-semibold text-secondary">Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 shadow-sm">
              <i class="fa fa-sign-in-alt me-2"></i> Secure Login
            </button>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Handle Server-side Errors
    <?php if($error): ?>
        toastr.error("<?= $error ?>");
    <?php endif; ?>

    // JS Validation
    $('#loginForm').on('submit', function(e) {
        const username = $('#username').val().trim();
        const password = $('#password').val().trim();
        const usernameRegex = /^[a-zA-Z\s]+$/;

        // Username Check: Characters and spaces only
        if (!usernameRegex.test(username)) {
            e.preventDefault();
            toastr.warning("Username: Only letters and spaces are allowed. No numbers or special symbols.");
            $('#username').focus();
            return false;
        }

        // Basic Password Check (Not empty)
        if (password === "") {
            e.preventDefault();
            toastr.warning("Please enter your password.");
            $('#password').focus();
            return false;
        }
        
        return true;
    });
});
</script>

</body>
</html>

