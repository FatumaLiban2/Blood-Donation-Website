<?php

require_once __DIR__ . '/../autoload.php';

SessionManager::endSession(); 

header("Location: ../index.php?logout=success");
exit();
