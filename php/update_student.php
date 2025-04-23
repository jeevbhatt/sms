<?php
// Include database connection
require_once('db_connect.php');

// Set header to return JSON for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $semester = isset($_POST['semester']) ? intval($_POST['semester']) : 0;
    $rollno = trim($_POST['rollno'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $b_ed = trim($_POST['b_ed'] ?? '');
    $admitted_year = isset($_POST['admitted_year']) ? intval($_POST['admitted_year']) : 0;
    
    // Validate input
    if ($id <= 0 || empty($name) || $semester <= 0 || $semester > 10 || empty($rollno) || empty($gender) || empty($b_ed) || $admitted_year <= 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'All fields are required and must be valid.'
            ]);
        } else {
            header('Location: ../index.html?error=invalid_input');
        }
        exit;
    }
    
    try {
        // Check if student exists
        $query = "SELECT photo FROM students WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            } else {
                header('Location: ../index.html?error=not_found');
            }
            exit;
        }
        
        // Handle photo upload
        $photo = $student['photo']; // Default to existing photo
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            // Validate file type and size
            if (!in_array($_FILES['photo']['type'], $allowed_types)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Only JPG, JPEG, and PNG files are allowed.'
                    ]);
                } else {
                    header('Location: ../index.html?error=invalid_file_type');
                }
                exit;
            }
            
            if ($_FILES['photo']['size'] > $max_size) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'File size must be less than 5MB.'
                    ]);
                } else {
                    header('Location: ../index.html?error=file_too_large');
                }
                exit;
            }
            
            // Generate unique filename
            $file_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = 'student_' . $id . '_' . time() . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if (!empty($student['photo'])) {
                    $old_photo_path = '../uploads/' . $student['photo'];
                    if (file_exists($old_photo_path)) {
                        unlink($old_photo_path);
                    }
                }
                
                $photo = $new_filename;
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to upload photo.'
                    ]);
                } else {
                    header('Location: ../index.html?error=upload_failed');
                }
                exit;
            }
        }
        
        // Update student in database
        $query = "UPDATE students SET name = :name, semester = :semester, rollno = :rollno, gender = :gender, b_ed = :b_ed, admitted_year = :admitted_year";
        
        // Only update photo if a new one was uploaded
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $query .= ", photo = :photo";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':rollno', $rollno);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':b_ed', $b_ed);
        $stmt->bindParam(':admitted_year', $admitted_year);
        $stmt->bindParam(':id', $id);
        
        // Only bind photo if a new one was uploaded
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $stmt->bindParam(':photo', $photo);
        }
        
        $result = $stmt->execute();
        
        if ($result) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Student updated successfully!'
                ]);
            } else {
                header('Location: ../index.html?success=updated');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update student.'
                ]);
            } else {
                header('Location: ../index.html?error=update_failed');
            }
        }
    } catch (PDOException $e) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } else {
            header('Location: ../index.html?error=database');
        }
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
    } else {
        header('Location: ../index.html?error=invalid_method');
    }
}
?>