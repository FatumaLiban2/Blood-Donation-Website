<?php

require_once __DIR__ . "/../autoload.php";

$keyPair = sodium_crypto_sign_keypair();
$privateKey = sodium_crypto_sign_secretkey($keyPair);
$publicKey = sodium_crypto_sign_publickey($keyPair);

$privateKeyEncoded = base64_encode($privateKey);
$publicKeyEncoded = base64_encode($publicKey);

echo "PRIVATE_KEY=$privateKeyEncoded\n";
echo "PUBLIC_KEY=$publicKeyEncoded\n";
