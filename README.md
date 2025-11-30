# 🌟 My PHP Weblog 🌟

![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## ✨ Overview

Welcome to **My PHP Weblog** – a dynamic, Twitter-inspired social media platform built with pure PHP and MySQL! 🚀 This web application allows users to share their thoughts, connect with others, and build a vibrant community through tweets, profiles, and interactions.

Whether you're a developer looking to explore PHP web development or someone wanting a simple social platform, this project offers a clean, responsive, and feature-rich experience.

## 🎯 Features

### 🔐 User Management
- **Secure Registration & Login** – User-friendly authentication system
- **Session Management** – Automatic logout after inactivity
- **Password Recovery** – Forgot password functionality
- **Profile Management** – Update personal information and profile pictures

### 🐦 Tweet Functionality
- **Post Tweets** – Share your thoughts with the world
- **Real-time Feed** – View all tweets in a beautiful, scrollable feed
- **User Profiles** – Personalized profiles with avatars and bio
- **Interactive UI** – Hover effects and smooth animations

### 🛠️ Admin Panel
- **User Dashboard** – Manage your account and settings
- **Tweet Management** – View and manage your posts

### 🎨 Design & UX
- **Responsive Design** – Works beautifully on desktop and mobile
- **Modern UI** – Clean, intuitive interface with CSS styling
- **Dark Theme Elements** – Subtle dark mode aesthetics
- **Smooth Animations** – Enhanced user experience with transitions

## 🛠️ Installation

### Prerequisites
- **PHP 8.0+** 🐘
- **MySQL 5.7+** 🗄️
- **Web Server** (Apache/Nginx) 🌐
- **Composer** (optional, for dependencies) 📦

### Setup Steps

1. **Clone the Repository** 📥
   ```bash
   git clone https://github.com/l1nuxd/My_PHP_Weblog.git
   cd My_PHP_Weblog
   ```

2. **Database Setup** 🗃️
   - Create a MySQL database named `Webapp`
   - Run the following SQL commands to create the required tables:

     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL,
         username VARCHAR(50) NOT NULL UNIQUE,
         email VARCHAR(100) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL,
         profile_picture VARCHAR(255) DEFAULT 'default.png',
         bio TEXT,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
     );

     CREATE TABLE tweets (
         id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         user_id INT NOT NULL,
         content VARCHAR(280) NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
     );

     CREATE TABLE invitation_codes (
         id INT AUTO_INCREMENT PRIMARY KEY,
         invitation_code VARCHAR(255) NOT NULL UNIQUE,
         used TINYINT(1) DEFAULT 0,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     -- Optional: For logging login attempts
     CREATE TABLE login_logs (
         id BIGINT AUTO_INCREMENT PRIMARY KEY,
         ip_address VARCHAR(45) NOT NULL,
         user_agent TEXT,
         referrer TEXT,
         username VARCHAR(50),
         login_logs SMALLINT,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```

   - Insert at least one invitation code: `INSERT INTO invitation_codes (invitation_code) VALUES ('your-invitation-code');`
   - Update `sql.php` with your database credentials (host, user, pass, db)
  
3. **Configure Environment** ⚙️
   - Ensure your web server points to the project root
   - Set proper permissions for file uploads (profile pictures)

4. **Access the Application** 🌍
   - Open your browser and navigate to `http://localhost/your-project-path`
   - Register a new account or login with existing credentials

## 🚀 Usage

### For Users
1. **Sign Up** – Create your account
2. **Login** – Access your dashboard
3. **Post Tweets** – Share your thoughts
4. **Explore** – View other users' tweets and profiles
5. **Customize** – Update your profile and settings

### For Developers
- **API Endpoints** – AJAX calls in `tweets.php` for dynamic content
- **Styling** – Customize `statics/styles.css` for themes

## 📁 Project Structure

```
My_PHP_Weblog/
├── index.php          # Main homepage with tweet feed
├── login.php          # User login page
├── register.php       # User registration
├── profile.php        # User profile display
├── panel.php          # User/admin panel
├── tweets.php         # Tweet API and display
├── update_profile.php # Profile update functionality
├── all_users.php      # User management
├── delete.php         # Delete functionality
├── forget_password.php # Password recovery
├── reset_password.php  # Password reset
├── functions.php      # Utility functions
├── sql.php            # Database connection
├── .htaccess          # Apache configuration
└── statics/
    └── styles.css     # Main stylesheet
```

## 🤝 Contributing

We welcome contributions! 🎉

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built with ❤️ using PHP and MySQL
- Inspired by modern social media platforms
- Thanks to the open-source community!

---

**Made with 💖 by [l1nuxd](https://github.com/l1nuxd)**

*Star this repo if you found it helpful!* ⭐
