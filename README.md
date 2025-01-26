# Study Group Finder

A web-based platform designed to help students connect, collaborate, and study together effectively. With Study Group Finder, users can create or join study groups, schedule sessions, and communicate in real time, fostering an environment for better collaboration and academic success.

---

## Project Overview

**Study Group Finder** is a comprehensive platform tailored to address the challenges of collaborative learning. It allows students to organize study groups, share resources, and streamline communication. With its user-friendly interface and robust backend, the application offers:

- Secure group creation and management tools
- Real-time chat functionality
- File sharing capabilities for study materials
- Intuitive scheduling features

The project is designed to make group collaboration efficient, secure, and accessible for students.

---

## Features

### User Authentication System
- **Secure Registration and Login**
  - Password hashing with bcrypt for enhanced security.
  - Session-based authentication with automatic timeout.
  - User-friendly error messaging for failed logins or sign-ups.
- **Automatic Logout**
  - Inactivity-based session termination.

### Group Management
- **Group Creation and Customization**
  - Ability to create and customize study groups.
- **Join/Leave Groups**
  - View all available groups and join or leave as needed.
- **Advanced Permissions**
  - Group creators have advanced privileges such as removing members and managing settings.

### Real-Time Chat System
- **Instant Messaging**
  - Send and receive messages within groups.
- **File Sharing**
  - Upload and share files, including PDFs, images, and documents.
  - Preview image files directly in the chat.
  - File type detection and appropriate icon display.
- **Message History**
  - Persistent chat history for all groups.

### Study Session Management
- **Scheduling**
  - Plan study sessions with a date-time picker.
- **Session Management**
  - View upcoming sessions and manage them in real time.
- **Notifications**
  - Receive alerts for session updates.

### File Management
- **Supported File Types**
  - Documents: PDF, DOC, DOCX, TXT
  - Spreadsheets: XLS, XLSX
  - Presentations: PPT, PPTX
  - Images: JPG, PNG, GIF
  - Archives: ZIP, RAR
- **File Preview**
  - Preview supported files before downloading.

### Responsive Interface
- **Mobile-Friendly Design**
  - Built using Bootstrap 5 for responsiveness.
- **Interactive Elements**
  - Modal forms, AJAX updates, and dynamic notifications.

---

## Technical Stack

**Frontend:**
- HTML5, CSS3, Bootstrap 5
- JavaScript (Vanilla JS)
- AJAX for asynchronous updates
- Font Awesome icons for UI enhancement

**Backend:**
- PHP 7.4+
- MySQL database
- Secure session management
- File upload handling

**Additional Tools:**
- GD Library for image processing

---

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- `mod_rewrite` enabled for routing
- GD Library installed for image processing

### Installation Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yourusername/study-group-finder.git
   ```

2. **Set Up the Database:**
   - Log in to your MySQL server:
     ```bash
     mysql -u your_username -p
     ```
   - Create and use the database:
     ```sql
     CREATE DATABASE study_group_finder;
     USE study_group_finder;
     ```
   - Import the provided schema:
     ```bash
     source schema.sql;
     ```

3. **Configure the Database Connection:**
   - Copy the template configuration file:
     ```bash
     cp src/config/db.template.php src/config/db.php
     ```
   - Update the `db.php` file with your database credentials:
     ```php
     $servername = "localhost";
     $username = "your_username";
     $password = "your_password";
     $dbname = "study_group_finder";
     ```

4. **Set File Permissions:**
   ```bash
   chmod 755 -R /path/to/project
   chmod 777 -R /path/to/project/public/uploads
   ```

5. **Access the Application:**
   - Open your web browser and navigate to your local server URL (e.g., `http://localhost/study-group-finder`).

---

## Directory Structure

```
study-group-finder/
├── public/                   # Public-facing files
│   ├── index.php            # Entry point
│   ├── dashboard.php        # Dashboard interface
│   ├── group.php            # Group interface
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── uploads/             # File upload storage
├── src/                      # Application source files
│   ├── controllers/         # Core controller logic
│   ├── utils/               # Utility functions
│   └── config/              # Configuration files
├── schema.sql                # Database schema
├── .gitignore                # Git ignore configuration
└── README.md                 # Project documentation
```

---

## Security Features

- **SQL Injection Prevention:** All queries use prepared statements.
- **XSS Protection:** Input is sanitized before rendering.
- **CSRF Protection:** Anti-CSRF tokens are implemented for forms.
- **Secure Password Storage:** Bcrypt hashing for passwords.
- **File Validation:** Uploaded files are validated for type and size.
- **Session Security:** Automatic logout and inactivity handling.

---

## Future Improvements

### Security Enhancements
- Two-factor authentication
- IP-based login restrictions
- Advanced file scanning for malware

### Feature Additions
- WebRTC-based video chat
- Advanced file preview with document rendering
- Mobile app integration

### Performance Optimizations
- Caching with Redis
- Query optimization
- CDN integration for static assets

### UI/UX Improvements
- Dark/Light mode toggle
- Drag-and-drop file uploads
- Calendar integration for scheduling

---

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature:
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add some AmazingFeature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/AmazingFeature
   ```
5. Open a Pull Request on GitHub.

---

## Acknowledgments

- **Bootstrap** for the responsive UI framework
- **Font Awesome** for the icons
- **Open Source Community** for tools and inspiration

