<?php

function validateCsrf() {
    if (
        isset($_POST['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Invalid CSRF token!'];
    }
}
