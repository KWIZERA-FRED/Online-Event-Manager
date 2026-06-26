<?php
class User {
    private $conn;
    public $error = "";
    public $success = "";

    // User properties
    public $user_id;
    public $username;
    public $email;
    public $password;
    public $phone;
    public $role;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($username, $email, $password, $phone = null, $role = 'user') {
        $this->email = mysqli_real_escape_string($this->conn, $email);
        $checkSql = "SELECT user_id FROM users WHERE email = '$this->email'";
        $result = mysqli_query($this->conn, $checkSql);
        if (mysqli_num_rows($result) > 0) {
            $this->error = "Email already registered.";
            return false;
        }

        $this->username = mysqli_real_escape_string($this->conn, $username);
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->phone = $phone ? mysqli_real_escape_string($this->conn, $phone) : NULL;
        $this->role = ($role === 'admin') ? 'admin' : 'user';

        $sql = "INSERT INTO users (username, email, password, phone, role, created_at)
                VALUES ('$this->username', '$this->email', '$this->password', " . ($this->phone ? "'$this->phone'" : "NULL") . ", '$this->role', NOW())";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Registration successful!";
            return true;
        } else {
            $this->error = "Database error: " . mysqli_error($this->conn);
            return false;
        }
    }

    public function login($email, $password) {
        $this->email = mysqli_real_escape_string($this->conn, $email);
        $sql = "SELECT * FROM users WHERE email = '$this->email' LIMIT 1";
        $result = mysqli_query($this->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                $this->error = "Incorrect password.";
                return false;
            }
        } else {
            $this->error = "Email not found.";
            return false;
        }
    }

    public function getUserById($user_id) {
        $this->user_id = $user_id;
        $sql = "SELECT * FROM users WHERE user_id = $this->user_id LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    public function getAllUsers($role = null) {
        $sql = "SELECT * FROM users";
        if ($role) {
            $role = mysqli_real_escape_string($this->conn, $role);
            $sql .= " WHERE role = '$role'";
        }
        $sql .= " ORDER BY created_at DESC";

        $result = mysqli_query($this->conn, $sql);
        $users = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        return $users;
    }

    // ================= NEW UPDATE USER METHOD =================
    public function updateUser($user_id, $data) {
        $this->user_id = intval($user_id);

        $updates = [];
        if (isset($data['username'])) {
            $updates[] = "username = '" . mysqli_real_escape_string($this->conn, $data['username']) . "'";
        }
        if (isset($data['email'])) {
            $updates[] = "email = '" . mysqli_real_escape_string($this->conn, $data['email']) . "'";
        }
        if (isset($data['phone'])) {
            $phone = $data['phone'] ? "'" . mysqli_real_escape_string($this->conn, $data['phone']) . "'" : "NULL";
            $updates[] = "phone = $phone";
        }
        if (isset($data['role'])) {
            $role = ($data['role'] === 'admin') ? 'admin' : 'user';
            $updates[] = "role = '$role'";
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
            $updates[] = "password = '$hashed'";
        }

        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE user_id = $this->user_id";
            if (mysqli_query($this->conn, $sql)) {
                $this->success = "User updated successfully!";
                return true;
            } else {
                $this->error = "Update failed: " . mysqli_error($this->conn);
                return false;
            }
        } else {
            $this->error = "No data to update.";
            return false;
        }
    }
}
?>