<?php
class Registration {
    private PDO $conn;

    public string $error = "";
    public string $success = "";

    public ?int $registration_id = null;
    public ?int $user_id = null;
    public ?int $event_id = null;
    public ?string $ticket_code = null;
    public ?string $status = null;

    private const ALLOWED_STATUSES = ['pending', 'approved', 'rejected'];

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // ================= HELPERS =================

    private function generateTicketCode(int $length = 10): string {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    private function validateStatus(string $status): bool {
        return in_array($status, self::ALLOWED_STATUSES, true);
    }

    /**
     * Generate a ticket code and retry on the rare chance of a collision
     * (the registrations table should have a UNIQUE constraint on ticket_code).
     */
    private function generateUniqueTicketCode(int $maxAttempts = 5): string {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = $this->generateTicketCode();
            $stmt = $this->conn->prepare("SELECT registration_id FROM registrations WHERE ticket_code = :code LIMIT 1");
            $stmt->execute([':code' => $code]);
            if (!$stmt->fetch()) {
                return $code;
            }
        }
        // Extremely unlikely with random_bytes(10), but fail safe rather than loop forever
        throw new RuntimeException("Could not generate a unique ticket code.");
    }

    // ================= CREATE =================

    public function registerUser(int $user_id, int $event_id, string $status = 'pending'): string|false {
        if (!$this->validateStatus($status)) {
            $this->error = "Invalid status.";
            return false;
        }

        $this->user_id = $user_id;
        $this->event_id = $event_id;
        $this->status = $status;

        try {
            // Verify the user exists
            $userCheck = $this->conn->prepare("SELECT user_id FROM users WHERE user_id = :id LIMIT 1");
            $userCheck->execute([':id' => $this->user_id]);
            if (!$userCheck->fetch()) {
                $this->error = "User does not exist.";
                return false;
            }

            // Verify the event exists and check capacity
            $eventCheck = $this->conn->prepare("SELECT event_id, max_capacity FROM events WHERE event_id = :id LIMIT 1");
            $eventCheck->execute([':id' => $this->event_id]);
            $event = $eventCheck->fetch(PDO::FETCH_ASSOC);
            if (!$event) {
                $this->error = "Event does not exist.";
                return false;
            }

            // Prevent duplicate registration for the same event
            $dupCheck = $this->conn->prepare(
                "SELECT registration_id FROM registrations WHERE user_id = :uid AND event_id = :eid LIMIT 1"
            );
            $dupCheck->execute([':uid' => $this->user_id, ':eid' => $this->event_id]);
            if ($dupCheck->fetch()) {
                $this->error = "User is already registered for this event.";
                return false;
            }

            // Enforce capacity (only approved/pending registrations count toward capacity)
            $countStmt = $this->conn->prepare(
                "SELECT COUNT(*) FROM registrations WHERE event_id = :eid AND status != 'rejected'"
            );
            $countStmt->execute([':eid' => $this->event_id]);
            $currentCount = (int) $countStmt->fetchColumn();

            if ($currentCount >= (int) $event['max_capacity']) {
                $this->error = "Event is at full capacity.";
                return false;
            }

            $this->ticket_code = $this->generateUniqueTicketCode();

            $stmt = $this->conn->prepare(
                "INSERT INTO registrations (user_id, event_id, ticket_code, status, created_at)
                 VALUES (:uid, :eid, :code, :status, NOW())"
            );
            $stmt->execute([
                ':uid'    => $this->user_id,
                ':eid'    => $this->event_id,
                ':code'   => $this->ticket_code,
                ':status' => $this->status,
            ]);

            $this->registration_id = (int) $this->conn->lastInsertId();
            $this->success = "Registration successful! Ticket: {$this->ticket_code}";
            return $this->ticket_code;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during registration.";
            error_log("Registration::registerUser failed: " . $e->getMessage());
            return false;
        } catch (RuntimeException $e) {
            $this->error = "Could not generate a ticket code. Please try again.";
            error_log("Registration::registerUser failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= READ =================

    public function getAllRegistrations(): array {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM registrations ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Registration::getAllRegistrations failed: " . $e->getMessage());
            return [];
        }
    }

    public function getUserRegistrations(int $user_id): array {
        $this->user_id = $user_id;

        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM registrations WHERE user_id = :uid ORDER BY created_at DESC"
            );
            $stmt->execute([':uid' => $this->user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Registration::getUserRegistrations failed: " . $e->getMessage());
            return [];
        }
    }

    public function getRegistrationById(int $registration_id): array|null {
        $this->registration_id = $registration_id;

        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM registrations WHERE registration_id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $this->registration_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Registration::getRegistrationById failed: " . $e->getMessage());
            return null;
        }
    }

    public function getRegistrationByTicket(string $ticket_code): array|null {
        $this->ticket_code = trim($ticket_code);

        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM registrations WHERE ticket_code = :code LIMIT 1"
            );
            $stmt->execute([':code' => $this->ticket_code]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Registration::getRegistrationByTicket failed: " . $e->getMessage());
            return null;
        }
    }

    public function getUserTicketsAndStatus(int $user_id): array {
        $this->user_id = $user_id;

        try {
            $stmt = $this->conn->prepare(
                "SELECT ticket_code, status, event_id 
                 FROM registrations 
                 WHERE user_id = :uid 
                 ORDER BY created_at DESC"
            );
            $stmt->execute([':uid' => $this->user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Registration::getUserTicketsAndStatus failed: " . $e->getMessage());
            return [];
        }
    }

    // ================= UPDATE =================

    public function updateStatus(int $registration_id, string $status): bool {
        if (!$this->validateStatus($status)) {
            $this->error = "Invalid status!";
            return false;
        }

        $this->registration_id = $registration_id;
        $this->status = $status;

        try {
            $stmt = $this->conn->prepare(
                "UPDATE registrations SET status = :status WHERE registration_id = :id"
            );
            $stmt->execute([
                ':status' => $this->status,
                ':id'     => $this->registration_id,
            ]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Registration not found or status unchanged.";
                return false;
            }

            $this->success = "Status updated successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during update.";
            error_log("Registration::updateStatus failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= DELETE =================

    public function deleteRegistration(int $registration_id): bool {
        $this->registration_id = $registration_id;

        try {
            $stmt = $this->conn->prepare("DELETE FROM registrations WHERE registration_id = :id");
            $stmt->execute([':id' => $this->registration_id]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Registration not found.";
                return false;
            }

            $this->success = "Registration deleted successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during deletion.";
            error_log("Registration::deleteRegistration failed: " . $e->getMessage());
            return false;
        }
    }
}