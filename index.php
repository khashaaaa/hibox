<?php

require_once('config.php');
require_once('config/config.php');

$application = new Application();
try {
    $application->run();
} catch (Exception $e) {
    $application->somethingWrong($e);
}
