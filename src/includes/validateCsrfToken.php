<?php

function validateCsrfToken() {
    if (
        isset($_POST['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        return array(
            'success' => true
        );
    } else {
        return array(
            'success' => false,
            'message' => 'Invalid CSRF token!'
        );
    }
}
