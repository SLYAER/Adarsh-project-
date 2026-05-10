# 🎓 Smart Student Portal
### BCA Project 2026 — Created by Adarsh Ray

---

## 📦 Tech Stack
- **Frontend**: HTML5, CSS3 (Glassmorphism + animations)
- **Backend**: PHP 8+
- **Database**: MySQL
- **Server**: XAMPP / MAMP

---

## 🚀 Quick Setup

### Step 1 — Start XAMPP/MAMP
Start **Apache** and **MySQL** from the control panel.

### Step 2 — Copy files
Place the `student-portal/` folder inside:
- XAMPP: `C:/xampp/htdocs/`
- MAMP:  `/Applications/MAMP/htdocs/`

### Step 3 — Create Database
Open **phpMyAdmin** → create a database named `college`.
> Or just open the site — `config.php` auto-creates tables!

### Step 4 — Open the portal
Visit: `http://localhost/student-portal/`

---

## 📁 File Structure
```
student-portal/
├── index.php          → Homepage with hero, features, stats
├── register.php       → Student registration with password strength
├── login.php          → Login with session management
├── dashboard.php      → Student dashboard with progress bars
├── upload.php         → Assignment upload (drag & drop)
├── download.php       → Notes download with search
├── feedback.php       → Feedback form with star rating
├── students.php       → Full CRUD for student records
├── logout.php         → Session destroy + redirect
├── config.php         → DB connection + auto table setup
│
├── includes/
│   ├── navbar.php     → Reusable navigation
│   └── footer.php     → Reusable footer + JS effects
│
├── css/
│   └── style.css      → Main stylesheet (700+ lines)
│
├── uploads/           → Student assignment uploads go here
├── notes/             → Place study notes/PDFs here
└── README.md          → This file
```

---

## ✨ Features
| Feature | File |
|---------|------|
| Visit counter (cookies) | `index.php` |
| User registration | `register.php` |
| Session login/logout | `login.php`, `logout.php` |
| Dashboard with stats | `dashboard.php` |
| Drag-and-drop file upload | `upload.php` |
| Notes download with search | `download.php` |
| Star-rated feedback form | `feedback.php` |
| CRUD with modals | `students.php` |
| Prepared statements (security) | All DB files |

---

## 🎨 Design Features
- Glassmorphism cards
- Animated background orbs
- Floating particles
- Scroll-reveal animations
- Hover effects on all cards
- Gradient text and buttons
- Password strength indicator
- Star rating system
- Typing effect on hero
- Animated counters
- Dark mode (purple theme)

---

## ⚠️ Notes
- Make sure `uploads/` folder has write permission (chmod 755)
- Place PDF/DOCX study notes in the `notes/` folder
- Default DB: `college` | User: `root` | Pass: `` (blank for XAMPP)
- Change DB credentials in `config.php` if needed

---

## 🏆 Presentation Highlights
1. Secure login using PHP sessions
2. File upload with type/size restrictions
3. Prepared statements for SQL injection prevention
4. Cookie-based visit counter
5. Full CRUD operations
6. Modern responsive UI

---

*Smart Student Portal — Adarsh Ray | BCA 2026*
