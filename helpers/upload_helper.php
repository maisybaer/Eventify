<?php
/**
 * Upload Helper
 * Provides functions for uploading files to remote upload API
 */

/**
 * Upload a file to the remote upload API
 *
 * @param array $file The $_FILES array element (e.g., $_FILES['flyer'])
 * @param array $allowedExts Array of allowed file extensions (default: ['jpg', 'jpeg', 'png', 'gif'])
 * @return array Returns ['success' => true, 'url' => 'file_url'] on success, or ['success' => false, 'error' => 'message'] on failure
 */
function upload_file_to_api($file, $allowedExts = ['jpg', 'jpeg', 'png', 'gif']) {
    // Upload API endpoint
    $uploadApiUrl = 'http://169.239.251.102:442/~egale-zoyiku/upload_api.php';

    // Validate file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file extension
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExts)) {
        return [
            'success' => false,
            'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExts)
        ];
    }

    // Prepare the file for upload using CURLFile
    $filePath = $file['tmp_name'];
    $cfile = new CURLFile($filePath, $file['type'], $fileName);

    // Prepare POST data
    $postData = [
        'uploadedFile' => $cfile
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_URL => $uploadApiUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Requested-With: XMLHttpRequest'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    // Check for cURL errors
    if ($curlError) {
        error_log("CURL Error during file upload: " . $curlError);
        return [
            'success' => false,
            'error' => 'File upload failed: ' . $curlError
        ];
    }

    // Check HTTP response code
    if ($httpCode !== 200) {
        error_log("Upload API returned HTTP $httpCode: " . $response);
        return [
            'success' => false,
            'error' => 'Upload API error (HTTP ' . $httpCode . ')'
        ];
    }

    // Parse JSON response
    $result = json_decode($response, true);

    if (!$result) {
        error_log("Failed to parse upload API response: " . $response);
        return [
            'success' => false,
            'error' => 'Invalid response from upload API'
        ];
    }

    // Check if upload was successful
    if (!isset($result['success']) || $result['success'] !== true) {
        $errorMsg = $result['message'] ?? 'Unknown error';
        error_log("Upload API failed: " . $errorMsg);
        return [
            'success' => false,
            'error' => $errorMsg
        ];
    }

    // Return success with the URL
    return [
        'success' => true,
        'url' => $result['url'],
        'filename' => $result['filename'] ?? ''
    ];
}
?>
