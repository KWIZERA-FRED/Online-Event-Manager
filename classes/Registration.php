<?php
class Registration {
    private $conn;

    public $error = "";
    public $success = "";

    public $registration_id;
    public $user_id;
    public $event_id;
    public $ticket_code;
    public $status;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ================= HELPER =================

    private function generateTicketCode($length = 10) {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    private function validateStatus($status) {
        $allowed = ['pending', 'approved', 'rejected'];
        return in_array($status, $allowed);
    }

    // ================= CREATE =================

    public function registerUser($user_id, $event_id, $status = 'pending') {
        $this->user_id = intval($user_id);
        $this->event_id = intval($event_id);
        $this->status = mysqli_real_escape_string($this->conn, $status);
        $this->ticket_code = $this->generateTicketCode();

        $sql = "INSERT INTO registrations 
                (user_id, event_id, ticket_code, status, created_at)
                VALUES 
                ($this->user_id, $this->event_id, '$this->ticket_code', '$this->status', NOW())";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Registration successful! Ticket: $this->ticket_code";
            return $this->ticket_code;
        } else {
            $this->error = "Error: " . mysqli_error($this->conn);
            return false;
        }
    }

    // ================= READ =================

    public function getAllRegistrations() {
        $sql = "SELECT * FROM registrations ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            $this->error = "Error: " . mysqli_error($this->conn);
            return [];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getUserRegistrations($user_id) {
        $this->user_id = intval($user_id);

        $sql = "SELECT * FROM registrations 
                WHERE user_id = $this->user_id 
                ORDER BY created_at DESC";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            $this->error = "Error: " . mysqli_error($this->conn);
            return [];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getRegistrationById($registration_id) {
        $this->registration_id = intval($registration_id);

        $sql = "SELECT * FROM registrations 
                WHERE registration_id = $this->registration_id LIMIT 1";

        $result = mysqli_query($this->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    public function getRegistrationByTicket($ticket_code) {
        $this->ticket_code = mysqli_real_escape_string($this->conn, $ticket_code);

        $sql = "SELECT * FROM registrations 
                WHERE ticket_code = '$this->ticket_code' LIMIT 1";

        $result = mysqli_query($this->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    public function getUserTicketsAndStatus($user_id) {
        $this->user_id = intval($user_id);

        $sql = "SELECT ticket_code, status, event_id 
                FROM registrations 
                WHERE user_id = $this->user_id 
                ORDER BY created_at DESC";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            $this->error = "Error: " . mysqli_error($this->conn);
            return [];
        }

        $tickets = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tickets[] = [
                'ticket_code' => $row['ticket_code'],
                'status' => $row['status'],
                'event_id' => $row['event_id']
            ];
        }

        return $tickets;
    }

    // ================= UPDATE =================

    public function updateStatus($registration_id, $status) {
        if (!$this->validateStatus($status)) {
            $this->error = "Invalid status!";
            return false;
        }

        $this->registration_id = intval($registration_id);
        $this->status = mysqli_real_escape_string($this->conn, $status);

        $sql = "UPDATE registrations 
                SET status = '$this->status' 
                WHERE registration_id = $this->registration_id";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Status updated successfully!";
            return true;
        } else {
            $this->error = "Error: " . mysqli_error($this->conn);
            return false;
        }
    }

    // ================= DELETE =================

    public function deleteRegistration($registration_id) {
        $this->registration_id = intval($registration_id);

        $sql = "DELETE FROM registrations 
                WHERE registration_id = $this->registration_id";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Registration deleted successfully!";
            return true;
        } else {
            $this->error = "Error: " . mysqli_error($this->conn);
            return false;
        }
    }
}
?>