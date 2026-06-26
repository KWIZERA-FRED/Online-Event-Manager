<?php
class Ticket {
    private $conn;
    public $error = "";
    public $success = "";

    // Ticket properties
    public $ticket_id;
    public $registration_id;
    public $qr_code;
    public $issued_at;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function generateQRCode($length = 12) {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    public function issueTicket($registration_id) {
        $this->registration_id = $registration_id;
        $this->qr_code = $this->generateQRCode();
        $this->issued_at = date("Y-m-d H:i:s");

        $sql = "INSERT INTO tickets (registration_id, qr_code, issued_at)
                VALUES ($this->registration_id, '$this->qr_code', '$this->issued_at')";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Ticket issued successfully! QR Code: $this->qr_code";
            return $this->qr_code;
        } else {
            $this->error = "Database error: " . mysqli_error($this->conn);
            return false;
        }
    }

    public function getTicketByRegistration($registration_id) {
        $this->registration_id = $registration_id;
        $sql = "SELECT * FROM tickets WHERE registration_id = $this->registration_id";
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }
}
?>