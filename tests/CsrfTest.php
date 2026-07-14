<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../englobedocs/helpers/csrf.php';

class CsrfTest extends TestCase {
  public function testGenerateAndVerify() {
    if(session_status() === PHP_SESSION_NONE) session_start();
    $token = generate_csrf_token();
    $this->assertNotEmpty($token);
    $this->assertTrue(verify_csrf_token($token));
    $this->assertFalse(verify_csrf_token('fake_token'));
  }
}
