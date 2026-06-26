<?php
class Events {    
    private $conn;

    public $error = "";
    public $success = "";

    // Event properties
    public $event_id;
    public $event_title;
    public $category;
    public $description;
    public $event_date;
    public $event_time;
    public $venue;
    public $max_capacity;
    public $price;
    public $totalCost;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ================= CALCULATIONS =================

    // Calculate theoretical total cost
    public function calculateTotal() {
        return $this->max_capacity * $this->price;
    }

    // Calculate actual revenue
    public function calculateRevenue($event_id) {
        $event_id = intval($event_id);

        $res = mysqli_query($this->conn, "SELECT COUNT(*) as total_reg FROM registrations WHERE event_id = $event_id");

        $total_reg = 0;
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            $total_reg = intval($row['total_reg']);
        }

        return $total_reg * $this->price;
    }

    // ================= CREATE =================

    public function addEvent($data) {
        $this->event_title = mysqli_real_escape_string($this->conn, $data['event_title']);
        $this->category = mysqli_real_escape_string($this->conn, $data['category']);
        $this->description = mysqli_real_escape_string($this->conn, $data['description']);
        $this->event_date = $data['event_date'];
        $this->event_time = $data['event_time'];
        $this->venue = mysqli_real_escape_string($this->conn, $data['venue']);
        $this->max_capacity = intval($data['max_capacity']);

        // Price logic
        $this->setPrice();

        $this->totalCost = $this->calculateTotal();

        $sql = "INSERT INTO events 
                (event_title, category, description, event_date, event_time, venue, max_capacity, price) 
                VALUES ('$this->event_title', '$this->category', '$this->description', '$this->event_date', '$this->event_time', '$this->venue', $this->max_capacity, $this->price)";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Event added successfully!";
            return true;
        } else {
            $this->error = "Database error: " . mysqli_error($this->conn);
            return false;
        }
    }

    // ================= UPDATE =================

    public function updateEvent($id, $data) {
        $this->event_id = intval($id);

        $this->event_title = mysqli_real_escape_string($this->conn, $data['event_title']);
        $this->category = mysqli_real_escape_string($this->conn, $data['category']);
        $this->description = mysqli_real_escape_string($this->conn, $data['description']);
        $this->event_date = $data['event_date'];
        $this->event_time = $data['event_time'];
        $this->venue = mysqli_real_escape_string($this->conn, $data['venue']);
        $this->max_capacity = intval($data['max_capacity']);

        // Recalculate price
        $this->setPrice();

        $sql = "UPDATE events SET 
                event_title = '$this->event_title',
                category = '$this->category',
                description = '$this->description',
                event_date = '$this->event_date',
                event_time = '$this->event_time',
                venue = '$this->venue',
                max_capacity = $this->max_capacity,
                price = $this->price
                WHERE event_id = $this->event_id";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Event updated successfully!";
            return true;
        } else {
            $this->error = "Error: " . mysqli_error($this->conn);
            return false;
        }
    }

    // ================= DELETE =================

    public function deleteEvent($id) {
        $this->event_id = intval($id);

        $sql = "DELETE FROM events WHERE event_id = $this->event_id";

        if (mysqli_query($this->conn, $sql)) {
            $this->success = "Event deleted successfully!";
            return true;
        } else {
            $this->error = "Error: " . mysqli_error($this->conn);
            return false;
        }
    }

    // ================= HELPER =================

    private function setPrice() {
        if ($this->max_capacity <= 50) {
            $this->price = 1000;
        } elseif ($this->max_capacity <= 100) {
            $this->price = 2000;
        } elseif ($this->max_capacity <= 200) {
            $this->price = 3000;
        } else {
            $this->price = 5000;
        }
    }
}
?>