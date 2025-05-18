<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - School Management System</title>
    <link rel="stylesheet" type="text/css" href="/sms/css/style.css">
    <link rel="stylesheet" type="text/css" href="/sms/css/admin.css">
    <link rel="icon" type="image/x-icon" href="/sms/img/sms.ico">
    <script src="/sms/js/admin.js" defer></script>
</head>
<body class="dashboard-body">
    <div class="admin-sidebar">
        <div class="admin-profile">
            <img src="/sms/img/admin-avatar.png" alt="Admin" class="admin-avatar">
            <h3 style='color: #000;'>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h3>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="#" class="active" data-section="dashboard">Dashboard</a></li>
                <li><a href="#" data-section="students">Manage Students</a></li>
                <li><a href="#" data-section="notices">Manage Notices</a></li>
                <li><a href="#" data-section="contacts">Contact Messages</a></li>
                <li><a href="#" data-section="send-email">Send Email</a></li>
                <li><a href="#" data-section="settings">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="admin-content">
        <header class="admin-header">
            <h2>Admin Dashboard</h2>
            <div class="admin-actions">
                <button id="toggleSidebar">☰</button>
            </div>
        </header>

        <main>
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section active">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Students</h3>
                        <p class="stat-number">
                            <?php
                            require_once(__DIR__ . '/../php/db_connect.php');
                            $result = $conn->query("SELECT COUNT(*) as count FROM students");
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Notices</h3>
                        <p class="stat-number">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM notices");
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Recent Activity</h3>
                        <p>Last login: <?php echo date("Y-m-d H:i:s"); ?></p>
                    </div>
                </div>

                <div class="recent-activity">
                    <h3>Recent Students</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Grade</th>
                                <th>Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT id, name, semester, created_at FROM students ORDER BY created_at DESC LIMIT 5");
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . $row['semester'] . "</td>";
                                echo "<td>" . $row['created_at'] . "</td>";
                                echo "</tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Students Section -->
            <section id="students-section" class="content-section">
                <h2>Manage Students</h2>
                <div class="action-buttons" style="display:flex;gap:10px;">
                    <button id="addStudentBtn" class="action-btn">Add Student</button>
                    <button id="updateStudentBtn" class="action-btn">Update Student</button>
                    <button id="removeStudentBtn" class="action-btn">Remove Student</button>
                    <input type="text" id="studentSearch" placeholder="Search students by name, roll no, semester, etc..." style="margin-left:auto;">
                </div>
                <div id="studentsList" class="data-list">
                    <!-- Student data will be loaded here via AJAX -->
                </div>
                <div id="studentsLoading" style="display:none;">Loading...</div>
                <div id="studentsError" style="color:red;"></div>
                <script>
                // AJAX search/filter students
                function fetchStudents(query = '') {
                    document.getElementById('studentsLoading').style.display = 'block';
                    document.getElementById('studentsError').innerText = '';
                    fetch('../php/admin/search_students.php?q=' + encodeURIComponent(query))
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('studentsList').innerHTML = html;
                            document.getElementById('studentsLoading').style.display = 'none';
                        })
                        .catch(() => {
                            document.getElementById('studentsError').innerText = 'Error loading students.';
                            document.getElementById('studentsLoading').style.display = 'none';
                        });
                }
                document.addEventListener('DOMContentLoaded', function() {
                    fetchStudents();
                    document.getElementById('studentSearch').addEventListener('input', function() {
                        fetchStudents(this.value);
                    });
                    // Add Student button
                    document.getElementById('addStudentBtn').onclick = function() {
                        window.open('../php/admin/add_student.php', '_blank');
                    };
                    // Update Student button
                    document.getElementById('updateStudentBtn').onclick = function() {
                        window.open('../php/admin/update_student.php', '_blank');
                    };
                    // Remove Student button
                    document.getElementById('removeStudentBtn').onclick = function() {
                        window.open('../php/admin/delete_student.php', '_blank');
                    };
                });
                </script>
            </section>

            <!-- Notices Section -->
            <section id="notices-section" class="content-section">
                <h2>Manage Notices</h2>
                <div style="margin-bottom:10px;">
                    <button id="addNoticeBtn" title="Add Notice" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:6px 12px;font-size:18px;cursor:pointer;">
                        &#43; <!-- Plus sign -->
                    </button>
                </div>
                <div id="noticesList" class="data-list">
                    <p>Loading notices data...</p>
                </div>
                <!-- Modal for Add/Edit Notice -->
                <div id="noticeFormModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close-modal" style="cursor:pointer;float:right;font-size:24px;">&times;</span>
                        <div id="noticeFormContainer"></div>
                    </div>
                </div>

                <!-- Custom Confirmation Modal -->
                <div id="confirmModal" class="modal" style="display:none;">
                    <div class="modal-content" style="max-width:350px;text-align:center;">
                        <div id="confirmMessage" style="margin-bottom:20px;font-size:18px;"></div>
                        <button id="confirmYesBtn" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:6px 18px;margin-right:10px;cursor:pointer;">Yes</button>
                        <button id="confirmNoBtn" style="background:#dc2626;color:#fff;border:none;border-radius:4px;padding:6px 18px;cursor:pointer;">No</button>
                    </div>
                </div>
                <script>
                // Helper to escape HTML
                function escapeHTML(str) {
                    return String(str)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const noticesList = document.getElementById('noticesList');
                    const addBtn = document.getElementById('addNoticeBtn');
                    const modal = document.getElementById('noticeFormModal');
                    const formContainer = document.getElementById('noticeFormContainer');
                    const closeBtn = modal.querySelector('.close-modal');

                    // Fetch and render notices
                    function fetchNotices() {
                        noticesList.innerHTML = '<p>Loading notices data...</p>';
                        fetch('../php/admin/get_notices.php')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.notices && data.notices.length > 0) {
                                    let html = `<table class="data-table"><thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Content</th>
                                            <th>Created At</th>
                                            <th style="text-align:right;">Actions</th>
                                        </tr>
                                    </thead><tbody>`;
                                    data.notices.forEach(notice => {
                                        html += `<tr>
                                            <td>${escapeHTML(notice.title)}</td>
                                            <td>${escapeHTML(notice.content)}</td>
                                            <td>${escapeHTML(notice.created_at)}</td>
                                            <td style="text-align:right;">
                                                <button class="edit-notice-btn" data-id="${notice.id}" title="Edit" style="background:none;border:none;cursor:pointer;">
                                                    <!-- Pen SVG -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb" style="width:22px;height:22px;vertical-align:middle;">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                                <button class="delete-notice-btn" data-id="${notice.id}" title="Delete" style="background:none;border:none;cursor:pointer;">
                                                    <!-- X SVG -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width:22px;height:22px;vertical-align:middle;">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>`;
                                    });
                                    html += '</tbody></table>';
                                    noticesList.innerHTML = html;

                                    // Attach event listeners for edit/delete
                                    document.querySelectorAll('.edit-notice-btn').forEach(btn => {
                                        btn.onclick = function() {
                                            const id = this.dataset.id;
                                            showNoticeForm('edit', id);
                                        };
                                    });
                                    document.querySelectorAll('.delete-notice-btn').forEach(btn => {
                                        btn.onclick = function() {
                                            const id = this.dataset.id;
                                            if (confirm('Are you sure you want to delete this notice?')) {
                                                fetch('../php/admin/delete_notice.php?id=' + encodeURIComponent(id), { method: 'GET' })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            fetchNotices();
                                                        } else {
                                                            alert(data.error || 'Failed to delete notice.');
                                                        }
                                                    })
                                                    .catch(() => alert('Error deleting notice.'));
                                            }
                                        };
                                    });
                                } else {
                                    noticesList.innerHTML = '<p>No notices found.</p>';
                                }
                            })
                            .catch(() => {
                                noticesList.innerHTML = '<p style="color:red;">Failed to load notices. Please try again later.</p>';
                            });
                    }

                    // Show Add/Edit Notice Form
                    function showNoticeForm(mode, id = null) {
                        let title = '', content = '';
                        if (mode === 'edit' && id) {
                            // Fetch notice data for editing
                            fetch('../php/admin/get_notice.php?id=' + encodeURIComponent(id))
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        title = data.notice.title;
                                        content = data.notice.content;
                                        renderForm();
                                    } else {
                                        alert('Failed to load notice.');
                                    }
                                })
                                .catch(() => alert('Error loading notice.'));
                        } else {
                            renderForm();
                        }

                        function renderForm() {
                            formContainer.innerHTML = `
                                <form id="noticeForm">
                                    <div class="form-group">
                                        <label for="noticeTitle">Title:</label>
                                        <input type="text" id="noticeTitle" name="title" required value="${escapeHTML(title)}">
                                    </div>
                                    <div class="form-group">
                                        <label for="noticeContent">Content:</label>
                                        <textarea id="noticeContent" name="content" required>${escapeHTML(content)}</textarea>
                                    </div>
                                    <button type="submit" class="action-btn">${mode === 'edit' ? 'Update' : 'Add'} Notice</button>
                                    <div id="noticeMsg" style="margin-top:10px;"></div>
                                </form>
                            `;
                            modal.style.display = 'block';

                            document.getElementById('noticeForm').onsubmit = function(e) {
                                e.preventDefault();
                                const formData = new FormData(this);
                                let url = mode === 'edit'
                                    ? '../php/admin/update_notice.php'
                                    : '../php/admin/add_notice.php';
                                if (mode === 'edit') formData.append('id', id);
                                fetch(url, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    const msg = document.getElementById('noticeMsg');
                                    if (data.success) {
                                        msg.style.color = 'green';
                                        msg.textContent = 'Notice ' + (mode === 'edit' ? 'updated' : 'added') + ' successfully!';
                                        setTimeout(() => {
                                            modal.style.display = 'none';
                                            fetchNotices();
                                        }, 800);
                                    } else {
                                        msg.style.color = 'red';
                                        msg.textContent = data.error || 'Failed to submit notice.';
                                    }
                                })
                                .catch(() => {
                                    const msg = document.getElementById('noticeMsg');
                                    msg.style.color = 'red';
                                    msg.textContent = 'Error submitting notice.';
                                });
                            };
                        }
                    }

                    // Add Notice button
                    addBtn.onclick = function() {
                        showNoticeForm('add');
                    };

                    // Modal close logic
                    closeBtn.onclick = function() {
                        modal.style.display = 'none';
                    };
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = 'none';
                        }
                    };

                    // Initial load
                    fetchNotices();
                });
                </script>
            </section>

            <!-- Contact Messages Section -->
            <section id="contacts-section" class="content-section">
                <h2>Contact Messages</h2>
                <div id="contactsList" class="data-list">
                    <p>Loading contact messages...</p>
                </div>
                <script>
                // Load contact messages via AJAX
                function fetchContacts() {
                    const contactsList = document.getElementById('contactsList');
                    contactsList.innerHTML = '<p>Loading contact messages...</p>';
                    fetch('../php/admin/view_contacts.php')
                        .then(res => res.text())
                        .then(html => {
                            contactsList.innerHTML = html;
                        })
                        .catch(() => {
                            contactsList.innerHTML = '<p style="color:red;">Error loading contact messages.</p>';
                        });
                }
                document.addEventListener('DOMContentLoaded', fetchContacts);
                </script>
            </section>

            <!-- Send Email Section -->
            <section id="send-email-section" class="content-section">
                <h2>Send Email (Gmail)</h2>
                <form id="sendEmailForm" style="max-width:400px;">
                    <div class="form-group">
                        <label for="emailTo">Recipient Gmail:</label>
                        <select id="emailSelect" style="width:100%;margin-bottom:8px;">
                            <option value="">-- Select from Contact Messages --</option>
                        </select>
                        <input type="email" id="emailTo" name="emailTo" required placeholder="recipient@gmail.com" style="width:100%;">
                    </div>
                    <div class="form-group">
                        <label for="emailMessage">Message:</label>
                        <textarea id="emailMessage" name="emailMessage" required placeholder="Type your message here"></textarea>
                    </div>
                    <button type="submit" class="action-btn">Send Email</button>
                    <div id="emailStatus" style="margin-top:10px;"></div>
                </form>
                <script>
                // Populate email dropdown from contact messages
                fetch('../php/admin/contact_emails.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.emails)) {
                            const select = document.getElementById('emailSelect');
                            data.emails.forEach(email => {
                                const opt = document.createElement('option');
                                opt.value = email;
                                opt.textContent = email;
                                select.appendChild(opt);
                            });
                        }
                    });

                // When dropdown changes, fill input
                document.getElementById('emailSelect').addEventListener('change', function() {
                    document.getElementById('emailTo').value = this.value;
                });

                document.getElementById('sendEmailForm').onsubmit = function(e) {
                    e.preventDefault();
                    const status = document.getElementById('emailStatus');
                    status.textContent = 'Sending...';
                    status.style.color = 'black';
                    fetch('../php/admin/send_gmail.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            status.textContent = 'Email sent successfully!';
                            status.style.color = 'green';
                        } else {
                            status.textContent = data.error || 'Failed to send email.';
                            status.style.color = 'red';
                        }
                    })
                    .catch(() => {
                        status.textContent = 'Error sending email.';
                        status.style.color = 'red';
                    });
                };
                </script>
            </section>

            <!-- Settings Section -->
            <section id="settings-section" class="content-section">
                <h2>Settings</h2>
                <div class="settings-form">
                    <h3>Change Password</h3>
                    <form id="changePasswordForm" action="../php/admin/change_password.php" method="post">
                        <div class="form-group">
                            <label for="currentPassword">Current Password:</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password:</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password:</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="action-btn">Update Password</button>
                    </form>
                    <div id="passwordMessage"></div>
                </div>
            </section>
        </main>
    </div>
    <script>
    // Sidebar navigation for sections
    document.querySelectorAll('.admin-nav a[data-section]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.admin-nav a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
            const section = document.getElementById(this.getAttribute('data-section') + '-section');
            if (section) section.classList.add('active');
        });
    });
    </script>
</body>
</html>
