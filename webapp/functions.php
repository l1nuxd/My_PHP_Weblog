<?php
function random_token($length = 16) {
    return bin2hex(random_bytes($length));
}
?>