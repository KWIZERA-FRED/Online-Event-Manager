<?php
class Ticket {
    private PDO $conn;
    public string $error = "";
    public string $success = "";

    // Ticket properties
    public ?int $ticket_id = null;
    public ?int $registration_id = null;
    public ?string $qr_code = null;
    public ?string $issued_at = null;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    private function generateQRCode(int $length = 12): string {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    /**
     * Generate a QR code and retry on the rare chance of a collision
     * (the tickets table should have a UNIQUE constraint on qr_code).
     */
    private function generateUniqueQRCode(int $maxAttempts = 5): string {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = $this->generateQRCode();
            $stmt = $this->conn->prepare("SELECT ticket_id FROM tickets WHERE qr_code = :code LIMIT 1");
            $stmt->execute([':code' => $code]);
            if (!$stmt->fetch()) {
                return $code;
            }
        }
        throw new RuntimeException("Could not generate a unique QR code.");
    }

    public function issueTicket(int $registration_id): string|false {
        $this->registration_id = $registration_id;

        try {
            // Confirm the registration exists
            $regCheck = $this->conn->prepare("SELECT registration_id, status FROM registrations WHERE registration_id = :id LIMIT 1");
            $regCheck->execute([':id' => $this->registration_id]);
            $registration = $regCheck->fetch(PDO::FETCH_ASSOC);

            if (!$registration) {
                $this->error = "Registration does not exist.";
                return false;
            }

            if ($registration['status'] !== 'approved') {
                $this->error = "Cannot issue a ticket for a registration that is not approved.";
                return false;
            }

            // Prevent issuing more than one ticket per registration
            $dupCheck = $this->conn->prepare("SELECT ticket_id FROM tickets WHERE registration_id = :id LIMIT 1");
            $dupCheck->execute([':id' => $this->registration_id]);
            if ($dupCheck->fetch()) {
                $this->error = "A ticket has already been issued for this registration.";
                return false;
            }

            $this->qr_code = $this->generateUniqueQRCode();
            $this->issued_at = date("Y-m-d H:i:s");

            $stmt = $this->conn->prepare(
                "INSERT INTO tickets (registration_id, qr_code, issued_at)
                 VALUES (:reg_id, :qr_code, :issued_at)"
            );
            $stmt->execute([
                ':reg_id'    => $this->registration_id,
                ':qr_code'   => $this->qr_code,
                ':issued_at' => $this->issued_at,
            ]);

            $this->ticket_id = (int) $this->conn->lastInsertId();
            $this->success = "Ticket issued successfully! QR Code: {$this->qr_code}";
            return $this->qr_code;
        } catch (PDOException $e) {
            $this->error = "Database error occurred while issuing the ticket.";
            error_log("Ticket::issueTicket failed: " . $e->getMessage());
            return false;
        } catch (RuntimeException $e) {
            $this->error = "Could not generate a QR code. Please try again.";
            error_log("Ticket::issueTicket failed: " . $e->getMessage());
            return false;
        }
    }

    public function getTicketByRegistration(int $registration_id): array|null {
        $this->registration_id = $registration_id;

        try {
            $stmt = $this->conn->prepare("SELECT * FROM tickets WHERE registration_id = :id LIMIT 1");
            $stmt->execute([':id' => $this->registration_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Ticket::getTicketByRegistration failed: " . $e->getMessage());
            return null;
        }
    }

    public function getTicketByQRCode(string $qr_code): array|null {
        $this->qr_code = trim($qr_code);

        try {
            $stmt = $this->conn->prepare("SELECT * FROM tickets WHERE qr_code = :code LIMIT 1");
            $stmt->execute([':code' => $this->qr_code]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Ticket::getTicketByQRCode failed: " . $e->getMessage());
            return null;
        }
    }

    public function deleteTicket(int $ticket_id): bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM tickets WHERE ticket_id = :id");
            $stmt->execute([':id' => $ticket_id]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Ticket not found.";
                return false;
            }

            $this->success = "Ticket deleted successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during deletion.";
            error_log("Ticket::deleteTicket failed: " . $e->getMessage());
            return false;
        }
    }
}