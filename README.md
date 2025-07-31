# Missing Person App

A web application for reporting, viewing, and matching missing person cases, supporting families and communities in locating loved ones.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## Features

- **Report Submission:** Users can submit missing or found person reports with details and images.
- **Case Search & Filtering:** Search and filter cases by name, status, and dates.
- **Case Matching:** Admins can match missing and found reports based on name, age, and gender.
- **Notifications:** Email notifications are sent to users when potential matches are found.
- **Profile Management:** Users have profile pictures and role-based access to features.
- **Responsive UI:** Optimized for desktop and mobile with Bootstrap 5.

## Technology Stack

- **Backend:** PHP (OOP), PDO, MySQL
- **Frontend:** HTML, CSS (Bootstrap), JavaScript (jQuery, Typed.js, Popper.js)
- **PHPMailer:** For sending emails
- **DataTables:** For enhanced table filtering and pagination
- **Session Management:** User authentication and roles
- **FPDF:** For generating PDF reports

## Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/DanielChanda/missing-person.git
   cd missing-person
   ```

2. **Set Up Database:**
   - Create a MySQL database named `missing_persons_db`.
   - Import the provided SQL schema in database/missing_persons_db.sql.
   - Configure `config/DatabaseConfiguration.php` with your local database credentials.

3. **Install Dependencies:**
   - Ensure PHP, and MySQL are installed.
   - No additional package manager required; all dependencies are included via CDN or project files.

4. **Start a Local Server:**
   - Use XAMPP/LAMP/MAMP or run:
     ```bash
     php -S localhost:8000
     ```
   - Visit `http://localhost:8000` in your browser.

## Usage

- **Login/Register:** Access the app via the login page.
- **View Cases:** Browse or search cases of missing persons.
- **Report Case:** Submit a new missing or found report.
- **Admin Features:** Match reports and mark cases as found or resolved.
- **Receive Notifications:** Get email updates on matched cases.

## Contributing

Contributions are welcome!

1. Fork the repository.
2. Create a new branch: `git checkout -b feature-branch`.
3. Commit your changes and push your branch.
4. Open a pull request describing your changes.

## License

This project currently does not specify a license. Please check with the repository owner.

## Contact

For questions or suggestions, contact [DanielChanda](https://github.com/DanielChanda).

---

*This app aims to support families and communities in reuniting with missing loved ones through technology, compassion, and collaboration.*
