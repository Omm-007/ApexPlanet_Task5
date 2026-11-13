# BlogSphere – Write, Share, Grow

A dynamic blog management platform developed as part of **Task 5 of the ApexPlanet Web Development Internship**.

BlogSphere allows users to create, manage, search, and share blog posts through a simple and user-friendly interface.

## 🚀 Features

- **User Authentication:** Secure user registration, login, and logout.
- **Personal Dashboard:** Individual dashboard for logged-in users.
- **Blog Management:** Create, edit, and delete blog posts.
- **Admin Dashboard:** Admin can manage all blog posts.
- **Access Control:** Users can manage their own posts, while admins have management privileges.
- **Search Functionality:** Search blog posts using keywords.
- **Pagination:** Organize posts across multiple pages.
- **CSRF Protection:** CSRF tokens for protected form submissions.
- **Responsive Design:** Clean interface with CSS styling.

## 🛠️ Technologies Used

- HTML5
- CSS3
- PHP
- MySQL
- XAMPP
- Visual Studio Code
- Git & GitHub

## 📂 Project Structure

```text
BlogSphere/
│
├── index.php
├── config.php
├── database.sql
├── login.php
├── register.php
├── logout.php
├── create.php
├── edit.php
├── delete.php
├── style.css
└── README.md
```

## ⚙️ Installation and Setup

1. Install and open XAMPP.
2. Start Apache and MySQL.
3. Copy the project folder into the `htdocs` directory.
4. Open phpMyAdmin.
5. Create a MySQL database and import `database.sql`.
6. Check the database configuration in `config.php`.
7. Open your browser and visit:

```text
http://localhost/BlogSphere/
```

## 🔐 Security Features

- Session-based authentication.
- Password hashing.
- CSRF token validation.
- User-specific post permissions.
- Admin-level post management.
- Protected database operations using PDO.

## 🎯 Internship Information

**Organization:** ApexPlanet Software Pvt. Ltd.  
**Program:** Web Development Internship  
**Task:** Task 5  
**Project:** BlogSphere – Blog Management Platform

## 👨‍💻 Developer

**Omm Prakash Behera**

GitHub: [Omm-007](https://github.com/Omm-007)

---

⭐ If you find this project interesting, feel free to explore the repository!
