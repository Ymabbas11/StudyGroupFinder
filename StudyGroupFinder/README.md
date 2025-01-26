# Study Group Finder

A web-based platform that helps students connect, collaborate, and study together effectively. Create or join study groups, schedule sessions, and communicate in real-time with your peers.

## Features

- **Study Groups**: Create, join, and manage study groups
- **Real-time Chat**: Communicate with group members instantly
- **File Sharing**: Share study materials and resources
- **Session Scheduling**: Plan and organize study sessions
- **User Management**: Secure authentication and member management

## Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Additional**: AJAX, Font Awesome icons

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled

### Installation

1. Clone the repository
```bash
git clone [your-repo-url]
```

2. Set up the database:
   - Create a new MySQL database
   - Import the schema from `schema.sql`

3. Configure database connection:
   - Copy `src/config/db.template.php` to `src/config/db.php`
   - Update the database credentials

4. Set file permissions:
```bash
chmod 755 -R /path/to/project
chmod 777 -R /path/to/project/uploads
```

5. Access the application through your web server

## Project Structure

```
study-group-finder/
├── public/                   # Public-facing files
│   ├── index.php            # Entry point
│   ├── dashboard.php        # Dashboard interface
│   ├── group.php           # Group interface
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
└── src/                    # Application source
    ├── controllers/        # Controller logic
    ├── utils/             # Utility functions
    └── config/            # Configuration files
```

## Security Features

- SQL injection prevention
- XSS protection
- CSRF protection
- Secure password hashing
- File upload validation
- Session security

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support or questions, please open an issue in the GitHub repository.
