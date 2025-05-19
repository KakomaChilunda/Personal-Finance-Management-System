<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$user = getUserDetails($userId);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'update_profile') {
        // Get form data
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        
        $errors = [];
        
        // Validate name
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        // If no errors, update profile
        if (empty($errors)) {
            $conn = connectDB();
            
            // Check if email already exists for another user
            if ($email != $user['email']) {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $email, $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Email already exists. Please use a different email.";
                    $stmt->close();
                    $conn->close();
                } else {
                    $stmt->close();
                    
                    // Update profile
                    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $name, $email, $userId);
                    
                    if ($stmt->execute()) {
                        // Update session variables
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        
                        $_SESSION['message'] = "Profile updated successfully";
                        $_SESSION['message_type'] = "success";
                        redirect('profile.php');
                    } else {
                        $errors[] = "Error updating profile: " . $conn->error;
                    }
                    
                    $stmt->close();
                    $conn->close();
                }
            } else {
                // Email not changed, just update the name
                $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
                $stmt->bind_param("si", $name, $userId);
                
                if ($stmt->execute()) {
                    // Update session variable
                    $_SESSION['user_name'] = $name;
                    
                    $_SESSION['message'] = "Profile updated successfully";
                    $_SESSION['message_type'] = "success";
                    redirect('profile.php');
                } else {
                    $errors[] = "Error updating profile: " . $conn->error;
                }
                
                $stmt->close();
                $conn->close();
            }
        }
    } elseif ($action == 'change_password') {
        // Get form data
        $current_password = sanitizeInput($_POST['current_password']);
        $new_password = sanitizeInput($_POST['new_password']);
        $confirm_password = sanitizeInput($_POST['confirm_password']);
        
        $errors = [];
        
        // Validate passwords
        if (empty($current_password)) {
            $errors[] = "Current password is required";
        }
        
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        if ($new_password != $confirm_password) {
            $errors[] = "New passwords do not match";
        }
        
        // If no errors, change password
        if (empty($errors)) {
            $conn = connectDB();
            
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $stmt->close();
            
            if (password_verify($current_password, $user_data['password'])) {
                // Current password is correct, update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Password changed successfully";
                    $_SESSION['message_type'] = "success";
                    redirect('profile.php');
                } else {
                    $errors[] = "Error changing password: " . $conn->error;
                }
                
                $stmt->close();
            } else {
                $errors[] = "Current password is incorrect";
            }
            
            $conn->close();
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">My Profile</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && $action == 'update_profile' && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Created</label>
                        <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($user['created_at'])); ?>" readonly>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && $action == 'change_password' && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $rootPath . '/includes/footer.php';
?>
