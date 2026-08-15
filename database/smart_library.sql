CREATE DATABASE IF NOT EXISTS smart_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_library;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','librarian') DEFAULT 'librarian',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    batch VARCHAR(50) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(150),
    qr_code VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE library_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL,
    qr_code VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active','inactive') DEFAULT 'active'
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    isbn VARCHAR(50),
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('issued','returned') DEFAULT 'issued',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE library_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    library_key_id INT NOT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (library_key_id) REFERENCES library_keys(id) ON DELETE CASCADE
);

INSERT INTO users(name,email,password,role) VALUES
('System Admin','admin@library.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC6hV0s4M9pQhV0W1i3K','admin');

INSERT INTO students(name,student_id,batch,phone,email,qr_code) VALUES
('Demo Student','DIU-2026-001','62','01700000000','student@example.com','DIU-2026-001');

INSERT INTO library_keys(key_name,qr_code,status) VALUES
('Main Gate Key','LIB-KEY-001','active'),
('Library Desk Key','LIB-KEY-002','active');

INSERT INTO books(title,author,isbn,total_copies,available_copies) VALUES
('Clean Code','Robert C. Martin','9780132350884',3,3),
('Introduction to Algorithms','Cormen et al.','9780262046305',2,2),
('Software Engineering','Ian Sommerville','9780133943030',4,4);
