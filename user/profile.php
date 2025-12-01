<?php
require_once '../settings/core.php';
require_once '../controllers/customer_controller.php';

if (!checkLogin()) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch fresh user data
$user = get_customer_ctr($_SESSION['id']);

// Determine Profile Image
$profile_pic = !empty($user['customer_image']) 
    ? "../uploads/users/" . $user['customer_image'] 
    : "https://ui-avatars.com/api/?name=" . urlencode($user['customer_name']) . "&background=c453ea&color=fff";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .profile-img-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }
        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #f3e8ff;
        }
        .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #c453eaff;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
        }
        .camera-icon:hover { background-color: #a020f0; }
        #fileInput { display: none; }
    </style>
</head>
<body>

<?php include '../views/navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow p-4 rounded-4">
                <h3 class="fw-bold mb-4 text-center" style="color: #c453eaff;">Profile Settings</h3>
                
                <form id="profile-form" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="profile-img-box">
                            <img src="<?= $profile_pic ?>" id="previewImg" class="profile-img" alt="Profile">
                            <label for="fileInput" class="camera-icon">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" id="fileInput" name="profile_image" accept="image/*" onchange="previewFile(this)">
                        </div>
                        <small class="text-muted mt-2 d-block">Tap icon to change photo</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['customer_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['customer_email']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['customer_contact']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['customer_city']) ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Country</label>
                            <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($user['customer_country']) ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn w-100 text-white fw-bold mt-3 btn-lg" style="background-color: #c453eaff;">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Preview Image immediately when selected
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function() {
                $("#previewImg").attr("src", reader.result);
            }
            reader.readAsDataURL(file);
        }
    }

    // 2. Submit Form with Image via AJAX
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.text();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);

        // Create FormData object (Crucial for file uploads)
        const formData = new FormData(this);

        $.ajax({
            url: '../actions/update_profile_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.message,
                        confirmButtonColor: '#c453eaff'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Server error occurred', 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
</script>
</body>
</html>