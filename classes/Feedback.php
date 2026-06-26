<?php
class Feedback {
    private $conn;
    public $error = "";
    public $success = "";

    // Feedback properties
    public $name;
    public $email;
    public $subject;
    public $message;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add a new feedback
    public function addFeedback($data) {
        $this->name = mysqli_real_escape_string($this->conn, $data['name']);
        $this->email = mysqli_real_escape_string($this->conn, $data['email']);
        $this->subject = isset($data['subject']) ? mysqli_real_escape_string($this->conn, $data['subject']) : NULL;
        $this->message = mysqli_real_escape_string($this->conn, $data['message']);

        $sql = "INSERT INTO feedback (name, email, subject, message, created_at)
                VALUES ('$this->name', '$this->email', " . ($this->subject ? "'$this->subject'" : "NULL") . ", '$this->message', NOW())";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Thank you! Your message has been sent successfully.";
            return true;
        } else {
            $this->error = "Database error: " . mysqli_error($this->conn);
            return false;
        }
    }
}
?>