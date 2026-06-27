<?php
class Feedback {
    private PDO $conn;
    public string $error = "";
    public string $success = "";

    // Feedback properties
    public ?int $feedback_id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $subject = null;
    public ?string $message = null;

    private const MAX_NAME_LENGTH = 100;
    private const MAX_SUBJECT_LENGTH = 150;
    private const MAX_MESSAGE_LENGTH = 5000;
    private const MIN_MESSAGE_LENGTH = 5;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // ================= VALIDATION =================

    private function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // ================= CREATE =================

    public function addFeedback(array $data): bool {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $subject = isset($data['subject']) ? trim($data['subject']) : null;
        $message = trim($data['message'] ?? '');

        if ($name === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
            $this->error = "Name is required and must be under " . self::MAX_NAME_LENGTH . " characters.";
            return false;
        }
        if (!$this->validateEmail($email)) {
            $this->error = "Invalid email address.";
            return false;
        }
        if ($subject !== null && mb_strlen($subject) > self::MAX_SUBJECT_LENGTH) {
            $this->error = "Subject must be under " . self::MAX_SUBJECT_LENGTH . " characters.";
            return false;
        }
        if (mb_strlen($message) < self::MIN_MESSAGE_LENGTH || mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            $this->error = "Message must be between " . self::MIN_MESSAGE_LENGTH . " and " . self::MAX_MESSAGE_LENGTH . " characters.";
            return false;
        }

        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject !== '' ? $subject : null;
        $this->message = $message;

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO feedback (name, email, subject, message, created_at)
                 VALUES (:name, :email, :subject, :message, NOW())"
            );

            $stmt->execute([
                ':name'    => $this->name,
                ':email'   => $this->email,
                ':subject' => $this->subject,
                ':message' => $this->message,
            ]);

            $this->feedback_id = (int) $this->conn->lastInsertId();
            $this->success = "Thank you! Your message has been sent successfully.";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred while sending your message.";
            error_log("Feedback::addFeedback failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= READ =================

    public function getAllFeedback(): array {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM feedback ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Feedback::getAllFeedback failed: " . $e->getMessage());
            return [];
        }
    }

    public function getFeedbackById(int $feedback_id): array|null {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM feedback WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $feedback_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Feedback::getFeedbackById failed: " . $e->getMessage());
            return null;
        }
    }

    // ================= DELETE =================

    public function deleteFeedback(int $feedback_id): bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM feedback WHERE id = :id");
            $stmt->execute([':id' => $feedback_id]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Feedback not found.";
                return false;
            }

            $this->success = "Feedback deleted successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during deletion.";
            error_log("Feedback::deleteFeedback failed: " . $e->getMessage());
            return false;
        }
    }
}