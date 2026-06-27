<?php
class User {
    private PDO $conn;
    public string $error = "";
    public string $success = "";

    // User properties
    public ?int $user_id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $phone = null;
    public ?string $role = null;

    private const MIN_PASSWORD_LENGTH = 8;
    private const ALLOWED_ROLES = ['user', 'admin'];

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // ================= VALIDATION =================

    private function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateUsername(string $username): bool {
        // 3-30 chars, letters/numbers/underscore only
        return (bool) preg_match('/^[A-Za-z0-9_]{3,30}$/', $username);
    }

    private function validatePassword(string $password): bool {
        return strlen($password) >= self::MIN_PASSWORD_LENGTH;
    }

    private function validatePhone(?string $phone): bool {
        if ($phone === null || $phone === '') {
            return true;
        }
        // Allow digits, spaces, +, - ; 7-15 digits total
        return (bool) preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone);
    }

    private function sanitizeRole(string $role): string {
        return in_array($role, self::ALLOWED_ROLES, true) ? $role : 'user';
    }

    // ================= CREATE =================

    public function register(string $username, string $email, string $password, ?string $phone = null, string $role = 'user'): bool {
        $username = trim($username);
        $email = trim($email);

        if (!$this->validateUsername($username)) {
            $this->error = "Username must be 3-30 characters and contain only letters, numbers, or underscores.";
            return false;
        }
        if (!$this->validateEmail($email)) {
            $this->error = "Invalid email address.";
            return false;
        }
        if (!$this->validatePassword($password)) {
            $this->error = "Password must be at least " . self::MIN_PASSWORD_LENGTH . " characters long.";
            return false;
        }
        if (!$this->validatePhone($phone)) {
            $this->error = "Invalid phone number format.";
            return false;
        }

        try {
            // Check for existing email
            $checkStmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
            $checkStmt->execute([':email' => $email]);
            if ($checkStmt->fetch()) {
                $this->error = "Email already registered.";
                return false;
            }

            $this->username = $username;
            $this->email = $email;
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $this->phone = $phone ? trim($phone) : null;
            $this->role = $this->sanitizeRole($role);

            $stmt = $this->conn->prepare(
                "INSERT INTO users (username, email, password, phone, role, created_at)
                 VALUES (:username, :email, :password, :phone, :role, NOW())"
            );

            $stmt->execute([
                ':username' => $this->username,
                ':email'    => $this->email,
                ':password' => $this->password,
                ':phone'    => $this->phone,
                ':role'     => $this->role,
            ]);

            $this->user_id = (int) $this->conn->lastInsertId();
            $this->success = "Registration successful!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during registration.";
            error_log("User::register failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= AUTH =================

    public function login(string $email, string $password): array|false {
        $email = trim($email);

        if (!$this->validateEmail($email)) {
            $this->error = "Invalid email address.";
            return false;
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Generic message — don't reveal whether the email exists
                $this->error = "Invalid email or password.";
                return false;
            }

            if (!password_verify($password, $user['password'])) {
                $this->error = "Invalid email or password.";
                return false;
            }

            unset($user['password']); // never hand the hash back to callers
            return $user;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during login.";
            error_log("User::login failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= READ =================

    public function getUserById(int $user_id): array|null {
        $this->user_id = $user_id;

        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = :id LIMIT 1");
            $stmt->execute([':id' => $this->user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                unset($user['password']);
            }
            return $user ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("User::getUserById failed: " . $e->getMessage());
            return null;
        }
    }

    public function getAllUsers(?string $role = null): array {
        try {
            if ($role !== null) {
                $role = $this->sanitizeRole($role);
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = :role ORDER BY created_at DESC");
                $stmt->execute([':role' => $role]);
            } else {
                $stmt = $this->conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
                $stmt->execute();
            }

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as &$u) {
                unset($u['password']);
            }
            return $users;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("User::getAllUsers failed: " . $e->getMessage());
            return [];
        }
    }

    // ================= UPDATE =================

    public function updateUser(int $user_id, array $data): bool {
        $this->user_id = $user_id;

        $fields = [];
        $params = [':id' => $this->user_id];

        if (isset($data['username'])) {
            $username = trim($data['username']);
            if (!$this->validateUsername($username)) {
                $this->error = "Username must be 3-30 characters and contain only letters, numbers, or underscores.";
                return false;
            }
            $fields[] = "username = :username";
            $params[':username'] = $username;
        }

        if (isset($data['email'])) {
            $email = trim($data['email']);
            if (!$this->validateEmail($email)) {
                $this->error = "Invalid email address.";
                return false;
            }
            $fields[] = "email = :email";
            $params[':email'] = $email;
        }

        if (isset($data['phone'])) {
            $phone = $data['phone'] !== '' ? trim($data['phone']) : null;
            if (!$this->validatePhone($phone)) {
                $this->error = "Invalid phone number format.";
                return false;
            }
            $fields[] = "phone = :phone";
            $params[':phone'] = $phone;
        }

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params[':role'] = $this->sanitizeRole($data['role']);
        }

        if (isset($data['password']) && $data['password'] !== '') {
            if (!$this->validatePassword($data['password'])) {
                $this->error = "Password must be at least " . self::MIN_PASSWORD_LENGTH . " characters long.";
                return false;
            }
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            $this->error = "No data to update.";
            return false;
        }

        try {
            // Prevent updating to an email already used by another account
            if (isset($params[':email'])) {
                $checkStmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :id");
                $checkStmt->execute([':email' => $params[':email'], ':id' => $this->user_id]);
                if ($checkStmt->fetch()) {
                    $this->error = "Email already in use by another account.";
                    return false;
                }
            }

            $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $this->success = "User updated successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during update.";
            error_log("User::updateUser failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= DELETE =================

    public function deleteUser(int $user_id): bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = :id");
            $stmt->execute([':id' => $user_id]);

            if ($stmt->rowCount() === 0) {
                $this->error = "User not found.";
                return false;
            }

            $this->success = "User deleted successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during deletion.";
            error_log("User::deleteUser failed: " . $e->getMessage());
            return false;
        }
    }
}