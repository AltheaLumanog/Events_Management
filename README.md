# Campus Events - Event Registration System

An event registration system for my Web Application Development final project.
Students can browse campus events and register for them, and the organizer (admin) manages events and attendees.

- **Live site:** https://[firstname-lastname].infinityfreeapp.com
- **GitHub:** https://github.com/[your-username]/campus-events
- **Student:** [Firstname Lastname]

## Features
- User registration and login (passwords hashed with `password_hash()`)
- Two roles: **admin** (organizer) and **user** (student)
- Home page with a banner, event cards, category filter, and live search (anyone can browse)
- **User:** register for an event, see seats left, view "My Registrations", cancel before the event
- **Admin:** create / edit / delete events (CRUD), see the attendee list, mark attendance, remove attendees, download attendees as CSV
- Seat limit: an event cannot be over-booked, and a user cannot register twice
- Register button and attendance checkboxes work with JavaScript fetch (no page reload)
- Validation on the client (JavaScript / HTML5) and on the server (PHP)
- Responsive design using Bootstrap 5

## Technologies
HTML5, CSS3, Bootstrap 5, Bootstrap Icons, JavaScript, PHP, MySQL

## Security
- Prepared statements (PDO) to stop SQL injection
- `htmlspecialchars()` on output to stop XSS
- Passwords hashed, session ID regenerated at login
- Admin pages check the role in PHP, and users can only cancel their own registrations
- The registration uses a database transaction with a row lock, so the last seat can't be taken twice

## Test accounts

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |
| User  | juan     | user123  |
| User  | maria    | user123  |

## How to set up
1. Create a MySQL database (phpMyAdmin).
2. Import `database/schema.sql`.
3. Open `config.php` and type your database host, name, username and password.
4. Upload all files to the `htdocs` folder of your hosting (or XAMPP `htdocs` for local testing).
5. Open the site. Browse as a guest, or login.

## Database
- `users` (id, username, full_name, password, role)
- `events` (id, title, category, event_date, venue, capacity, description)
- `registrations` (id, user_id, event_id, registered_at, attended)

`registrations` connects users and events (many-to-many). It has a UNIQUE key on (user_id, event_id),
so the same user cannot register for the same event twice.
Deleting an event or a user also deletes their registrations (ON DELETE CASCADE).

## Files
- `config.php` - database connection and helper functions
- `header.php` / `footer.php` - shared layout
- `index.php`, `event.php` - public pages (event list and details)
- `register.php`, `login.php`, `logout.php` - authentication
- `register_event.php`, `my_events.php`, `cancel_registration.php` - user registrations
- `admin_events.php`, `event_form.php`, `event_delete.php` - admin event CRUD
- `attendees.php`, `toggle_attendance.php`, `remove_registration.php`, `export_attendees.php` - admin attendee tools
- `script.js`, `style.css` - JavaScript and CSS
