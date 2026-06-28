# AccessForm

**AccessForm** is a PHP-based form and survey platform built with accessibility in mind. This project was created for Virtual University and includes features to make forms easier to use for all users.

---

## 🔍 Project Overview

AccessForm includes three main user roles:
- `Respondent` — fills out forms
- `creator` — creates forms
- `Admin` — monitors the system and views results

This platform:
- allows creators to build accessible forms
- enables respondents to view and submit active forms
- logs accessibility interactions
- provides admins with a system overview

---

## ✨ Key Features

- user registration and login
- two user roles: `Respondent` and `creator`
- dynamic form builder for creators (`creator/create_form.php`)
- active form browsing and submission for respondents
- admin dashboard with system statistics (`admin/admin_home.php`)
- accessibility settings through `js/accessibility.js`:
  - high contrast theme
  - dyslexia-friendly mode
  - larger text
  - voice reader
- logs accessibility actions in the `accessibility_logs` table
- saves responses in `form_responses` and `form_answers`

---

## 🧩 Folder Structure

- `index.php` — homepage
- `config.php` — database connection
- `register.php` — user registration
- `login.php` — user login
- `navbar.php` — shared navigation bar and accessibility menu
- `css/style.css` — custom styles
- `js/accessibility.js` — accessibility logic
- `Database/accessform_db.sql` — database schema and sample data

### Main Directories

- `admin/` — admin login, dashboard, survey monitoring, response viewing
- `creator/` — form creation, creator dashboard, export, form viewing
- `respondent/` — respondent dashboard, form submission, active form viewing

---

## 🗄️ Database Schema

The current database includes the following tables:
- `users` — Respondent and creator users
- `admins` — admin credentials
- `forms` — created forms
- `questions` — form questions
- `form_responses` — each submitted response record
- `form_answers` — individual answers to questions
- `accessibility_logs` — logs of accessibility interactions

---

## ⚙️ Setup Instructions

1. Open XAMPP and start the `Apache` and `MySQL` services.
2. Place the `AccessForm` folder in `c:\xampp\htdocs\AccessForm`.
3. Create the `accessform_db` database in phpMyAdmin.
4. Import `Database/accessform_db.sql`:
   ```sql
   SOURCE c:/xampp/htdocs/AccessForm/Database/accessform_db.sql;
   ```
5. Update `config.php` if needed with your database credentials:
   ```php
   $conn = new mysqli("localhost", "root", "", "accessform_db");
   ```
6. Open in your browser:
   ```
   http://localhost/AccessForm/
   ```

---

## 🔑 Default Credentials

- Admin username: `admin`
- Admin password: `admin`

> Note: The admin password in the `admins` table is stored in plaintext. In production, it should be hashed.

---

## 🚀 How to Use

- Register a user: `register.php`
- Log in: `login.php`
- Respondent dashboard: `respondent/respondent_dashboard.php`
- Creator dashboard: `creator/creator_dashboard.php`
- Admin dashboard: `admin/admin_home.php`
- Admin login: `admin/admin_login.php`

---

## ⚠️ Important Notes

- `register.php` and `login.php` use the `Respondent` and `creator` role values.
- `creator/create_form.php` supports dynamic questions, options, and accessibility metadata (alt text / video caption).
- `respondent/submit_response.php` saves responses and file uploads to `form_answers`.
- `js/accessibility.js` logs accessibility mode changes via `log_accessibility.php`.

---

## 💡 Suggestions for Improvement

- hash admin credentials
- improve session management
- add CSRF protection
- add input validation and XSS protection
- improve user role authorization
- enhance accessibility logs visualization
