<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

final class ItemService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function browse(string $q = '', string $category = '', string $condition = '', string $type = ''): array
    {
        $sql = "SELECT i.*, u.display_name
                FROM items i
                LEFT JOIN users u ON u.user_id = i.owner_id
                WHERE i.status = 'AVAILABLE' ";

        $params = [];

        if ($q !== '') {
            $sql .= " AND (i.title LIKE ? OR i.description LIKE ?) ";
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if ($category !== '') {
            $sql .= " AND i.category = ? ";
            $params[] = $category;
        }
        if ($condition !== '') {
            $sql .= " AND i.`condition` = ? ";
            $params[] = $condition;
        }
        if ($type !== '') {
            $sql .= " AND i.transaction_type = ? ";
            $params[] = $type;
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT 60";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function get(int $itemId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT i.*, u.display_name, u.university_email
            FROM items i
            JOIN users u ON u.user_id = i.owner_id
            WHERE i.item_id = ?
            LIMIT 1
        ");
        $stmt->execute([$itemId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function images(int $itemId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM item_images WHERE item_id=? ORDER BY image_id ASC");
        $stmt->execute([$itemId]);
        return $stmt->fetchAll() ?: [];
    }

    public function mine(int $ownerId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM items
            WHERE owner_id = ?
            ORDER BY created_at DESC
            LIMIT 100
        ");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll() ?: [];
    }

    public function create(int $ownerId, array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO items (owner_id, title, description, category, `condition`, weight_kg, transaction_type, price, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'AVAILABLE', NOW())
            ");
        $stmt->execute([
        $ownerId,
        $data['title'],
        $data['description'],
        $data['category'],
        $data['condition'],
        $data['weight_kg'],
        $data['transaction_type'],
        $data['price'] !== '' ? $data['price'] : null
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $itemId, int $ownerId, array $data): bool
    {
        // Only update if owned by user and item is still available (minimal edge-case rule)
        $stmt = $this->pdo->prepare("
            UPDATE items
            SET title=?, description=?, category=?, `condition`=?, weight_kg=?, transaction_type=?, price=?
            WHERE item_id=? AND owner_id=? AND status='AVAILABLE'
        ");
        $stmt->execute([
        $data['title'],
        $data['description'],
        $data['category'],
        $data['condition'],
        $data['weight_kg'],
        $data['transaction_type'],
        $data['price'] !== '' ? $data['price'] : null,
        $itemId,
        $ownerId
        ]);
        return $stmt->rowCount() === 1;
    }

    public function delete(int $itemId, int $ownerId): bool
    {
        // Only delete if owned by user and available
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE item_id=? AND owner_id=? AND status='AVAILABLE'");
        $stmt->execute([$itemId, $ownerId]);
        return $stmt->rowCount() === 1;
    }

    public function addImage(int $itemId, string $filePath): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO item_images (item_id, file_path, uploaded_at) VALUES (?, ?, NOW())");
        $stmt->execute([$itemId, $filePath]);
    }
}
