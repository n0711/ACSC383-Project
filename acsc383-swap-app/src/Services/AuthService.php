<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

final class AuthService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
        $this->ensureEmailVerificationTable();
    }

    private function ensureEmailVerificationTable(): void
    {
        // Demo table 
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS email_verifications (
              id INT AUTO_INCREMENT PRIMARY KEY,
              user_id INT NOT NULL,
              token VARCHAR(80) NOT NULL UNIQUE,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              CONSTRAINT fk_ev_user
                FOREIGN KEY (user_id) REFERENCES users(user_id)
                ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE university_email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findUserById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create user with is_verified=0, return:
     * - user_id
     * - verification_token
     */
    public function register(string $displayName, string $email, string $password): array
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO users (university_email, password_hash, display_name, is_verified, created_at)
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$email, $hash, $displayName]);

        $userId = (int)$this->pdo->lastInsertId();

        // Create demo verification token
        $token = bin2hex(random_bytes(20)); // 40 chars
        $stmt2 = $this->pdo->prepare("INSERT INTO email_verifications (user_id, token) VALUES (?, ?)");
        $stmt2->execute([$userId, $token]);

        return ['user_id' => $userId, 'verification_token' => $token];
    }

    /**
     * Verify demo token:
     * - set users.is_verified=1
     * - delete token row
     */
    public function verifyToken(string $token): bool
    {
        $stmt = $this->pdo->prepare("SELECT user_id FROM email_verifications WHERE token = ? LIMIT 1");
        $stmt->execute([$token]);
        $userId = $stmt->fetchColumn();

        if (!$userId) return false;

        $this->pdo->beginTransaction();
        try {
            $stmt1 = $this->pdo->prepare("UPDATE users SET is_verified=1 WHERE user_id=?");
            $stmt1->execute([(int)$userId]);

            $stmt2 = $this->pdo->prepare("DELETE FROM email_verifications WHERE token=?");
            $stmt2->execute([$token]);

            $this->pdo->commit();
            return true;
        } catch (\Throwable $t) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $t;
        }
    }

    /**
     * Attempt login.
     * Returns:
     * - ['ok'=>true,'user'=>row] on success
     * - ['ok'=>false,'error'=>'...'] on failure
     */
    public function login(string $email, string $password): array
    {
        $user = $this->findUserByEmail($email);
        if (!$user) return ['ok' => false, 'error' => 'Invalid email or password.'];

        if (!password_verify($password, (string)$user['password_hash'])) {
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        if ((int)$user['is_verified'] !== 1) {
            return ['ok' => false, 'error' => 'Please verify your account first (demo verification).'];
        }

        return ['ok' => true, 'user' => $user];
    }
}
