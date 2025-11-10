<?php
$plain = 'adminpassword'; // the password you are entering in the form
$hash = '$2a$12$Nmi1153E3ul4STgnninrSeSK3kUze/UZUPuQX3yn42B6hmEhnRrEu'; // copy from your DB

if (password_verify($plain, $hash)) {
    echo "MATCH";
} else {
    echo "NO MATCH";
}