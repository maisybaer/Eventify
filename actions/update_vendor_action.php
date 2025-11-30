<?php
header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../settings/db_class.php';
require_once '../helpers/upload_helper.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$customer_id = (int) $_SESSION['user_id'];

// Get POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : null;
$country = isset($_POST['country']) ? trim($_POST['country']) : null;
$city = isset($_POST['city']) ? trim($_POST['city']) : null;
$vendor_type = isset($_POST['vendor_type']) ? trim($_POST['vendor_type']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : null;

// Handle image upload - using remote upload API
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $uploadResult = upload_file_to_api($_FILES['image'], $allowed_extensions);

    if ($uploadResult['success']) {
        // Store the full URL returned from the API
        $image_path = $uploadResult['url'];
    } else {
        echo json_encode(['status' => 'error', 'message' => $uploadResult['error']]);
        exit();
    }
}

try {
    $db = new db_connection();
    $conn = $db->db_conn();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    mysqli_begin_transaction($conn);

    // Update customer table
    $updates = [];
    $params = [];
    $types = '';

    if ($name) {
        $updates[] = "customer_name = ?";
        $params[] = $name;
        $types .= 's';
    }
    if ($contact !== null) {
        $updates[] = "customer_contact = ?";
        $params[] = $contact;
        $types .= 's';
    }
    if ($country !== null) {
        $updates[] = "customer_country = ?";
        $params[] = $country;
        $types .= 's';
    }
    if ($city !== null) {
        $updates[] = "customer_city = ?";
        $params[] = $city;
        $types .= 's';
    }
    if ($image_path) {
        $updates[] = "customer_image = ?";
        $params[] = $image_path;
        $types .= 's';
    }

    if (!empty($updates)) {
        $params[] = $customer_id;
        $types .= 'i';

        $sql = "UPDATE eventify_customer SET " . implode(', ', $updates) . " WHERE customer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update customer record');
        }
    }

    // Update or insert vendor table
    if ($vendor_type || $description) {
        // Check if vendor record exists
        $check_sql = "SELECT vendor_id FROM eventify_vendor WHERE vendor_desc = ? LIMIT 1";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        $vendor_name = $name ?: '';
        mysqli_stmt_bind_param($check_stmt, 's', $vendor_name);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $vendor_exists = mysqli_fetch_assoc($result);

        if ($vendor_exists) {
            // Update existing vendor
            $v_updates = [];
            $v_params = [];
            $v_types = '';

            if ($vendor_type) {
                $v_updates[] = "vendor_type = ?";
                $v_params[] = $vendor_type;
                $v_types .= 's';
            }
            if ($description) {
                $v_updates[] = "vendor_desc = ?";
                $v_params[] = $description;
                $v_types .= 's';
            }

            if (!empty($v_updates)) {
                $v_params[] = $vendor_exists['vendor_id'];
                $v_types .= 'i';

                $v_sql = "UPDATE eventify_vendor SET " . implode(', ', $v_updates) . " WHERE vendor_id = ?";
                $v_stmt = mysqli_prepare($conn, $v_sql);
                mysqli_stmt_bind_param($v_stmt, $v_types, ...$v_params);
                mysqli_stmt_execute($v_stmt);
            }
        } else {
            // Insert new vendor record
            $vendor_type = $vendor_type ?: 'Service Provider';
            $description = $description ?: $name;

            $insert_sql = "INSERT INTO eventify_vendor (vendor_type, vendor_desc) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, 'ss', $vendor_type, $description);
            mysqli_stmt_execute($insert_stmt);
        }
    }

    mysqli_commit($conn);

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile updated successfully!'
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    error_log('Vendor update error: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update profile: ' . $e->getMessage()
    ]);
}
?>
