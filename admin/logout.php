<?php
/**
 * 로그아웃.
 */

require_once dirname(__DIR__) . '/includes/auth.php';

logout();

header('Location: ' . url('admin/login.php'));
exit;
