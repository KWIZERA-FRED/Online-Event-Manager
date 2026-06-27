<?php
class Events {
    private PDO $conn;

    public string $error = "";
    public string $success = "";

    // Event properties
    public ?int $event_id = null;
    public ?string $event_title = null;
    public ?string $category = null;
    public ?string $description = null;
    public ?string $event_date = null;
    public ?string $event_time = null;
    public ?string $venue = null;
    public ?int $max_capacity = null;
    public ?float $price = null;
    public ?float $totalCost = null;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // ================= VALIDATION =================

    private function validateRequired(array $data): bool {
        $required = ['event_title', 'category', 'description', 'event_date', 'event_time', 'venue', 'max_capacity'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $this->error = "Missing required field: $field";
                return false;
            }
        }
        return true;
    }

    private function validateDate(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function validateTime(string $time): bool {
        $t = DateTime::createFromFormat('H:i', $time) ?: DateTime::createFromFormat('H:i:s', $time);
        return $t !== false;
    }

    private function validateCapacity(int $capacity): bool {
        return $capacity > 0 && $capacity <= 100000;
    }

    // ================= CALCULATIONS =================

    public function calculateTotal(): float {
        return ($this->max_capacity ?? 0) * ($this->price ?? 0);
    }

    public function calculateRevenue(int $event_id): float {
        try {
            $stmt = $this->conn->prepare(
                "SELECT e.price, COUNT(r.registration_id) as total_reg
                 FROM events e
                 LEFT JOIN registrations r ON r.event_id = e.event_id AND r.status != 'rejected'
                 WHERE e.event_id = :id
                 GROUP BY e.event_id, e.price"
            );
            $stmt->execute([':id' => $event_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $this->error = "Event not found.";
                return 0.0;
            }

            return ((float) $row['price']) * ((int) $row['total_reg']);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Events::calculateRevenue failed: " . $e->getMessage());
            return 0.0;
        }
    }

    // ================= CREATE =================

    public function addEvent(array $data): bool {
        if (!$this->validateRequired($data)) {
            return false; // error already set
        }

        $this->event_title = trim($data['event_title']);
        $this->category = trim($data['category']);
        $this->description = trim($data['description']);
        $this->event_date = trim($data['event_date']);
        $this->event_time = trim($data['event_time']);
        $this->venue = trim($data['venue']);
        $this->max_capacity = (int) $data['max_capacity'];

        if (!$this->validateDate($this->event_date)) {
            $this->error = "Invalid event date format. Use YYYY-MM-DD.";
            return false;
        }
        if (!$this->validateTime($this->event_time)) {
            $this->error = "Invalid event time format. Use HH:MM.";
            return false;
        }
        if (!$this->validateCapacity($this->max_capacity)) {
            $this->error = "Max capacity must be a positive number.";
            return false;
        }

        $this->setPrice();
        $this->totalCost = $this->calculateTotal();

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO events 
                 (event_title, category, description, event_date, event_time, venue, max_capacity, price) 
                 VALUES (:title, :category, :description, :date, :time, :venue, :capacity, :price)"
            );

            $stmt->execute([
                ':title'       => $this->event_title,
                ':category'    => $this->category,
                ':description' => $this->description,
                ':date'        => $this->event_date,
                ':time'        => $this->event_time,
                ':venue'       => $this->venue,
                ':capacity'    => $this->max_capacity,
                ':price'       => $this->price,
            ]);

            $this->event_id = (int) $this->conn->lastInsertId();
            $this->success = "Event added successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred while adding the event.";
            error_log("Events::addEvent failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= READ =================

    public function getEventById(int $event_id): array|null {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM events WHERE event_id = :id LIMIT 1");
            $stmt->execute([':id' => $event_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Events::getEventById failed: " . $e->getMessage());
            return null;
        }
    }

    public function getAllEvents(): array {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM events ORDER BY event_date ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Database error occurred.";
            error_log("Events::getAllEvents failed: " . $e->getMessage());
            return [];
        }
    }

    // ================= UPDATE =================

    public function updateEvent(int $id, array $data): bool {
        $this->event_id = $id;

        if (!$this->validateRequired($data)) {
            return false;
        }

        $this->event_title = trim($data['event_title']);
        $this->category = trim($data['category']);
        $this->description = trim($data['description']);
        $this->event_date = trim($data['event_date']);
        $this->event_time = trim($data['event_time']);
        $this->venue = trim($data['venue']);
        $this->max_capacity = (int) $data['max_capacity'];

        if (!$this->validateDate($this->event_date)) {
            $this->error = "Invalid event date format. Use YYYY-MM-DD.";
            return false;
        }
        if (!$this->validateTime($this->event_time)) {
            $this->error = "Invalid event time format. Use HH:MM.";
            return false;
        }
        if (!$this->validateCapacity($this->max_capacity)) {
            $this->error = "Max capacity must be a positive number.";
            return false;
        }

        $this->setPrice();

        try {
            $stmt = $this->conn->prepare(
                "UPDATE events SET 
                    event_title = :title,
                    category = :category,
                    description = :description,
                    event_date = :date,
                    event_time = :time,
                    venue = :venue,
                    max_capacity = :capacity,
                    price = :price
                 WHERE event_id = :id"
            );

            $stmt->execute([
                ':title'       => $this->event_title,
                ':category'    => $this->category,
                ':description' => $this->description,
                ':date'        => $this->event_date,
                ':time'        => $this->event_time,
                ':venue'       => $this->venue,
                ':capacity'    => $this->max_capacity,
                ':price'       => $this->price,
                ':id'          => $this->event_id,
            ]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Event not found or no changes made.";
                return false;
            }

            $this->success = "Event updated successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during update.";
            error_log("Events::updateEvent failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= DELETE =================

    public function deleteEvent(int $id): bool {
        $this->event_id = $id;

        try {
            $stmt = $this->conn->prepare("DELETE FROM events WHERE event_id = :id");
            $stmt->execute([':id' => $this->event_id]);

            if ($stmt->rowCount() === 0) {
                $this->error = "Event not found.";
                return false;
            }

            $this->success = "Event deleted successfully!";
            return true;
        } catch (PDOException $e) {
            $this->error = "Database error occurred during deletion.";
            error_log("Events::deleteEvent failed: " . $e->getMessage());
            return false;
        }
    }

    // ================= HELPER =================

    private function setPrice(): void {
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