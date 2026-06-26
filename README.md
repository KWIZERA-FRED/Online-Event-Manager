# Online Event Manager

A full-stack event management platform built with PHP and MySQL, featuring role-based access control, QR-code ticketing, and category-based event browsing. Developed as a 3rd-year Software Engineering group project.

## Overview

Online Event Manager lets organizers publish and manage events while giving attendees a streamlined way to discover, register for, and check in to them. The system separates admin and user experiences through dedicated dashboards and enforces access control so each role only sees what's relevant to them.

## Key Features

- **Role-Based Access Control** — Separate admin and user dashboards (`adminPage.php`, `userPage.php`) with distinct permissions and views.
- **Event Registration & Ticketing** — Users can register for events, with registrations and tickets managed through dedicated classes (`Registration.php`, `Ticket.php`).
- **QR Code Ticket Generation** — Integrates the `phpqrcode` library to generate unique QR codes per ticket for fast, scannable event check-in.
- **Event Categories** — Events are organized into browsable categories: competitions, concerts, conferences, social events, and workshops.
- **Admin Tools** — Dedicated admin utilities for account creation and event/registration management.
- **User Feedback System** — Built-in feedback collection tied to events (`Feedback.php`).
- **Client-Side Form Validation** — JavaScript validation on registration forms (`validateRegister.js`) for a smoother user experience before server-side checks.
- **Modular UI Components** — Shared header/footer/utility includes (`utils/`) for consistent layout across pages.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP |
| Database | MySQL |
| Frontend | HTML, CSS, JavaScript |
| Libraries | [phpqrcode](https://github.com/t0k4rt/phpqrcode) for QR code generation |

## Project Structure

```
├── adminPage.php              # Admin dashboard
├── userPage.php               # User dashboard
├── register.php               # User registration
├── register_event.php         # Event registration flow
├── ticket_registration_edit.php
├── viewEvent.php              # Event detail view
├── login.php / logout.php
├── about_us.php / contact_us.php
├── classes/                   # Core application logic
│   ├── User.php
│   ├── Registration.php
│   ├── Ticket.php
│   ├── Feedback.php
│   ├── events.php
│   └── connect.php / db1.php
├── user page/                 # Event category pages
│   ├── competition.php
│   ├── concerts.php
│   ├── conferences.php
│   ├── social.php
│   └── workshop.php
├── utils/                     # Shared layout components
├── phpqrcode/                 # QR code generation library
├── tickets_qr/                # Generated ticket QR codes
├── css/                        # Page-specific stylesheets
└── Admin Tools/                # Admin account creation utilities
```

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any Apache + PHP + MySQL stack)
- A web browser

### Installation

1. Clone the repository into your local server's web root:
   ```bash
   git clone https://github.com/KWIZERA-FRED/Online-Event-Manager.git
   ```
2. Place the project folder inside `htdocs` (XAMPP) or your equivalent web root.
3. Start **Apache** and **MySQL** from your XAMPP control panel.
4. Create a local database via phpMyAdmin and import the project's schema.
5. Update database credentials in `classes/connect.php` (or `db1.php`) to match your local setup.
6. Open the project in your browser:
   ```
   http://localhost/<project-folder-name>/
   ```

## Roadmap

- [ ] Migrate to a PDO/prepared-statement data layer for stronger SQL injection protection
- [ ] Add automated email confirmations for event registration
- [ ] Deploy a live demo

## Contributors

Built by **Group 11** as part of a Software Engineering coursework project.

## License

This project was developed for academic purposes.