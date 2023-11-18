<?php

function validateCsrfToken() {
    if (
        isset($_POST['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        return true;
    } else {
        return false;
    }
}
