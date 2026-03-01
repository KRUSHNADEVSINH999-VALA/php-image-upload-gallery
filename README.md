# 🖼️ PixelVault — PHP Image Upload & Gallery Application

> **Activity Learning Assignment** · PHP Backend Development · Group Project

---

## 📌 Project Objective

PixelVault is a secure, fully-featured image upload and gallery application built with **pure PHP, HTML5, CSS3, and Vanilla JavaScript**. Developed as part of a hands-on PHP Activity Learning Assignment, it demonstrates real-world backend development skills including:

- PHP file handling and form processing
- Multi-layer server-side validation
- Session management for activity tracking
- Dynamic gallery rendering
- Secure file storage practices

---

## 🚀 Features

| Feature | Description |
|---|---|
| **Drag & Drop Upload** | Drop images directly onto the upload zone or click to browse |
| **Live Preview** | See image preview with dimensions before uploading |
| **Client-Side Validation** | Instant browser-side feedback for type and size errors |
| **Server-Side Validation** | PHP validates MIME type, extension, file size, and image integrity |
| **Session Tracking** | Every upload is tracked with metadata in PHP sessions |
| **Dynamic Gallery** | Responsive grid/list gallery built from session + disk data |
| **Lightbox Viewer** | Full-screen image viewer with keyboard arrow navigation |
| **Download & Delete** | Save any image to device or remove it from the vault |
| **Session Dashboard** | Live stats, upload activity log, and file registry |
| **Dark / Light Mode** | Toggle with preference saved to localStorage |
| **Mobile Responsive** | Fully responsive — works on all screen sizes |
| **Security Hardened** | .htaccess blocks PHP execution in uploads folder |

---

## 🗂️ Project Structure

```
pixelvault2/
│
├── index.php                  ← Upload form (main page)
├── gallery.php                ← Dynamic image gallery
├── session_info.php           ← Session tracking dashboard
│
├── includes/
│   └── upload_handler.php     ← All upload logic & validation
│
├── assets/
│   ├── css/
│   │   └── style.css          ← Complete UI stylesheet (dark + light)
│   └── js/
│       └── app.js             ← Theme, upload UX, gallery, lightbox
│
├── uploads/
│   ├── .htaccess              ← Blocks PHP execution in uploads dir
│   └── .gitkeep               ← Keeps folder tracked by Git
│
├── .gitignore
└── README.md
```

---

## ✅ Validation Rules

### Client-Side (JavaScript — `assets/js/app.js`)

| Check | Rule |
|---|---|
| File type | Must be `image/jpeg`, `image/png`, `image/gif`, or `image/webp` |
| File size | Must not exceed 5 MB |
| Empty file | File must not be 0 bytes |

> Client-side validation provides **instant feedback** but is not relied upon for security.

### Server-Side (PHP — `includes/upload_handler.php`)

| Layer | Check | Function / Method |
|---|---|---|
| **1** | PHP upload error codes | `$_FILES['image']['error']` vs all `UPLOAD_ERR_*` constants |
| **2** | Real HTTP POST upload | `is_uploaded_file()` |
| **3** | File size (0 bytes) | `$file['size'] === 0` |
| **4** | File size (max limit) | `$file['size'] > MAX_FILE_SIZE` (5,242,880 bytes) |
| **5** | Extension whitelist | `pathinfo()` + array check |
| **6** | MIME type (server-detected) | `finfo::file()` — NOT the browser-supplied type |
| **7** | Image integrity | `getimagesize()` — confirms valid image data |

> Only after **all 7 layers pass** is the file saved to disk.

---

## 🔧 PHP File-Handling Functions Used

| Function | Purpose |
|---|---|
| `move_uploaded_file()` | Safely moves the temp file to the uploads directory |
| `is_uploaded_file()` | Security check — confirms file came via HTTP POST |
| `finfo::file()` | Detects true MIME type server-side |
| `getimagesize()` | Validates image integrity and gets dimensions |
| `pathinfo()` | Extracts original filename and extension |
| `uniqid()` | Generates a unique filename to prevent conflicts |
| `file_exists()` | Checks if file exists before attempting delete |
| `unlink()` | Permanently deletes an image file |
| `glob()` | Scans the uploads directory for image files |
| `filesize()` | Gets file size for disk-discovered files |
| `filemtime()` | Gets file modification timestamp |
| `mkdir()` | Creates the uploads directory if it doesn't exist |
| `basename()` | Sanitizes delete requests to prevent path traversal |

---

## 🔐 Session Management

`session_start()` is called on every page. Three session variables are maintained:

| Variable | Type | Purpose |
|---|---|---|
| `$_SESSION['uploaded_images']` | Array | Stores metadata for every uploaded image (filename, original name, MIME type, size, dimensions, timestamp) |
| `$_SESSION['upload_count']` | Integer | Running total of successful uploads this session |
| `$_SESSION['upload_log']` | Array | Timestamped log of every upload attempt with status |

Session data persists until the browser is closed or the user clicks **"Clear Session"** on the Session dashboard.

---

## 🔒 Security Considerations

| Threat | Mitigation Applied |
|---|---|
| Malicious file upload (e.g., PHP disguised as image) | MIME type detected by `finfo` server-side, not from browser |
| PHP execution in uploads folder | `.htaccess` blocks all `.php` files in `uploads/` |
| Path traversal in delete requests | `basename()` strips any directory separators |
| Filename injection / special characters | `preg_replace` allows only `[a-zA-Z0-9_-]` in filenames |
| Directory listing | `Options -Indexes` in `.htaccess` |
| Corrupt / fake image files | `getimagesize()` confirms valid image structure |
| Empty or zero-byte files | Explicit size check before saving |

---

## ⚙️ Execution Steps

### Requirements

- **PHP 7.4+** (PHP 8.x recommended)
- **Web server**: Apache (XAMPP/WAMP) or PHP built-in server
- **PHP Extensions**: `fileinfo`, `gd` (enabled by default in most installs)

---

### ▶️ Option 1 — XAMPP (Windows) — Recommended for Students

1. Download and install **XAMPP** from [apachefriends.org](https://www.apachefriends.org)
2. Open **XAMPP Control Panel** → click **Start** next to **Apache**
3. Extract this ZIP file
4. Copy the `pixelvault2` folder into:
   ```
   C:\xampp\htdocs\pixelvault2\
   ```
5. Open your browser and navigate to:
   ```
   http://localhost/pixelvault2/
   ```

---

### ▶️ Option 2 — PHP Built-in Server (Mac / Linux / Windows)

```bash
# Navigate to the project folder
cd path/to/pixelvault2

# Set permissions (Mac/Linux only)
chmod 755 uploads/

# Start the server
php -S localhost:8000

# Open in browser
# http://localhost:8000
```

---

### ▶️ Option 3 — WAMP (Windows)

Same as XAMPP — copy folder to `C:\wamp64\www\pixelvault2\` and visit `http://localhost/pixelvault2/`

---

## 📸 Pages Overview

| Page | URL | Description |
|---|---|---|
| Upload | `/index.php` | Drag-drop upload form with live preview and validation |
| Gallery | `/gallery.php` | Responsive grid/list gallery with lightbox, download, delete |
| Session | `/session_info.php` | Upload stats, activity log, file registry, session controls |

---

## 📋 Presentation Guide

### Topic 1 — Image Upload Process & Validation
- Form uses `enctype="multipart/form-data"` (required for file uploads)
- PHP receives file via `$_FILES['image']`
- 7-layer server-side validation in `upload_handler.php`
- Key point: MIME check uses `finfo::file()` not `$_FILES['type']` (which is browser-supplied and can be faked)

### Topic 2 — PHP File-Handling Functions
- `is_uploaded_file()` → security baseline
- `finfo::file()` → true MIME detection
- `getimagesize()` → image integrity confirmation
- `move_uploaded_file()` → only called after all validations pass
- `uniqid()` → unique filename prevents overwrite attacks

### Topic 3 — Role of Sessions
- `session_start()` on every page creates a persistent server-side store
- Each upload appends to `$_SESSION['uploaded_images']` array
- Gallery page reads from both session AND disk (handles server restarts)
- Upload log provides an audit trail of all activity

### Topic 4 — Overall Workflow
```
User selects image
    → JS client validation (type, size)
    → Form POST to index.php
    → PHP layer 1–7 validation
    → Unique filename generated
    → File moved to /uploads/
    → Session updated
    → Success/error shown to user
    → Gallery reads session + disk
```

---

## 👥 Team Members

| Name | Role |
|---|---|
| Member 1 | PHP Backend — upload_handler.php |
| Member 2 | Frontend — HTML structure, CSS |
| Member 3 | Session Management — session_info.php |
| Member 4 | Gallery, Documentation, Testing |

> Update with your actual team member names before submission.

---

## 📄 License

Created for educational purposes as part of a PHP Activity Learning Assignment.
