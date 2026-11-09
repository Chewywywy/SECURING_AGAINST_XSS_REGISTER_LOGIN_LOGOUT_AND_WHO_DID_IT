<?php
require_once 'dbconfig.php';

// Wipe the session completely
$_SESSION = [];
session_destroy();
session_start();

flash('You have been logged out.');
redirect('/login.php');
