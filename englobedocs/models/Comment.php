<?php
require_once __DIR__ . '/../config/database.php';
class CommentModel {
  private $pdo;
  public function __construct($pdo){ $this->pdo = $pdo; }
  public function forDocument($document_id){ $stmt = $this->pdo->prepare('SELECT cm.*, u.name FROM comments cm JOIN users u ON cm.user_id = u.id WHERE cm.document_id = ? ORDER BY cm.created_at DESC'); $stmt->execute([$document_id]); return $stmt->fetchAll(); }
}
