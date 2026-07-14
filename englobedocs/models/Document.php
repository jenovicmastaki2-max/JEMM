<?php
require_once __DIR__ . '/../config/database.php';
class DocumentModel {
  private $pdo;
  public function __construct($pdo){ $this->pdo = $pdo; }
  public function find($id){ $stmt = $this->pdo->prepare('SELECT * FROM documents WHERE id = ?'); $stmt->execute([$id]); return $stmt->fetch(); }
  public function all($limit=50){ return $this->pdo->query('SELECT * FROM documents ORDER BY created_at DESC LIMIT '.intval($limit))->fetchAll(); }
}
