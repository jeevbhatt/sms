document.addEventListener("DOMContentLoaded", function () {
  // Initialize sidebar toggle
  initSidebar();

  // Initialize tab navigation
  initTabNavigation();

  // Initialize student management
  initStudentManagement();

  // Initialize notices management
  initNoticesManagement();

  // Initialize settings
  initSettings();

  // Add animation classes
  addAnimationClasses();
});

// Sidebar functionality with improved animations
function initSidebar() {
  const toggleBtn = document.getElementById("toggleSidebar");
  const sidebar = document.querySelector(".admin-sidebar");
  const content = document.querySelector(".admin-content");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");
      content.classList.toggle("expanded");

      // Toggle button animation
      toggleBtn.classList.toggle("active");
    });
  }

  // Auto-collapse sidebar on small screens
  function checkScreenSize() {
    if (window.innerWidth <= 768) {
      sidebar.classList.add("collapsed");
      content.classList.add("expanded");
    } else {
      sidebar.classList.remove("collapsed");
      content.classList.remove("expanded");
    }
  }

  // Check on load
  checkScreenSize();

  // Check on resize with debounce
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(checkScreenSize, 250);
  });
}

// Tab navigation with smooth transitions
function initTabNavigation() {
  const navLinks = document.querySelectorAll(".admin-nav a");
  const sections = document.querySelectorAll(".content-section");

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      if (this.getAttribute("href") !== "logout.php") {
        e.preventDefault();

        const targetSection = this.getAttribute("data-section");

        // Update active link with animation
        navLinks.forEach((link) => {
          link.classList.remove("active");
          link.style.transition = "all 0.3s ease";
        });

        this.classList.add("active");

        // Hide all sections first
        sections.forEach((section) => {
          section.style.opacity = "0";
          section.style.transform = "translateY(20px)";
          section.style.display = "none";
          section.classList.remove("active");
        });

        // Show target section with animation
        const currentSection = document.getElementById(
          `${targetSection}-section`
        );
        if (currentSection) {
          currentSection.style.display = "block";
          currentSection.classList.add("active");

          // Trigger reflow
          void currentSection.offsetWidth;

          // Apply animation
          setTimeout(() => {
            currentSection.style.opacity = "1";
            currentSection.style.transform = "translateY(0)";
            currentSection.style.transition =
              "opacity 0.3s ease, transform 0.3s ease";
          }, 50);
        }
      }
    });
  });
}

// Student management with improved interactions
function initStudentManagement() {
  const studentsList = document.getElementById("studentsList");
  const addStudentBtn = document.getElementById("addStudentBtn");
  const studentSearch = document.getElementById("studentSearch");
  const studentFormModal = document.getElementById("studentFormModal");
  const studentFormContainer = document.getElementById("studentFormContainer");
  const closeModalBtns = document.querySelectorAll(".close-modal");

  // Load students list with animation
  if (studentsList) {
    loadStudents();
  }

  // Add student button with ripple effect
  if (addStudentBtn) {
    addStudentBtn.addEventListener("click", () => {
      showStudentForm("add");
    });

    // Add ripple effect
    addStudentBtn.addEventListener("mousedown", createRipple);
  }

  // Search functionality with debounce
  if (studentSearch) {
    studentSearch.addEventListener(
      "input",
      debounce(() => {
        loadStudents(studentSearch.value);
      }, 300)
    );
  }

  // Close modals with animation
  closeModalBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      closeModal();
    });
  });

  // Close modal when clicking outside
  window.addEventListener("click", (e) => {
    if (e.target === studentFormModal) {
      closeModal();
    }
  });

  // Close modal with ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && studentFormModal.style.display === "block") {
      closeModal();
    }
  });

  function closeModal() {
    // Animate modal out
    const modalContent = studentFormModal.querySelector(".modal-content");
    modalContent.style.opacity = "0";
    modalContent.style.transform = "translateY(-20px)";

    setTimeout(() => {
      studentFormModal.style.display = "none";
      // Reset transform for next opening
      modalContent.style.transform = "";
    }, 300);
  }

  function loadStudents(searchTerm = "") {
    if (!studentsList) return;

    // Show loading animation
    studentsList.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>Loading students data...</p>
            </div>
        `;

    fetch(
      `../php/admin/get_students.php?search=${encodeURIComponent(searchTerm)}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.students.length > 0) {
          renderStudents(data.students);
        } else {
          studentsList.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">👨‍🎓</div>
                            <h3>No students found</h3>
                            <p>Try a different search term or add a new student</p>
                        </div>
                    `;
        }
      })
      .catch((error) => {
        console.error("Error loading students:", error);
        studentsList.innerHTML = `
                    <div class="error-state">
                        <div class="error-icon">❌</div>
                        <h3>Failed to load students</h3>
                        <p>Please try again later</p>
                        <button onclick="location.reload()" class="retry-btn">Retry</button>
                    </div>
                `;
      });
  }

  function renderStudents(students) {
    let html = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Roll No</th>
                        <th>Gender</th>
                        <th>Subject</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

    students.forEach((student) => {
      html += `
                <tr class="student-row" data-id="${student.id}">
                    <td>${student.id}</td>
                    <td>${escapeHTML(student.name)}</td>
                    <td>${student.semester}</td>
                    <td>${escapeHTML(student.rollno)}</td>
                    <td>${escapeHTML(student.gender)}</td>
                    <td>${escapeHTML(student.b_ed)}</td>
                    <td>
                        <button class="edit-btn" onclick="editStudent(${
                          student.id
                        })">Edit</button>
                        <button class="delete-btn" onclick="deleteStudent(${
                          student.id
                        }, '${escapeHTML(student.name)}')">Delete</button>
                    </td>
                </tr>
            `;
    });

    html += `
                </tbody>
            </table>
        `;

    studentsList.innerHTML = html;

    // Add row hover animation
    const rows = studentsList.querySelectorAll(".student-row");
    rows.forEach((row) => {
      row.addEventListener("mouseenter", () => {
        row.style.backgroundColor = "var(--gray-100)";
        row.style.transform = "translateX(5px)";
        row.style.transition = "all 0.3s ease";
      });

      row.addEventListener("mouseleave", () => {
        row.style.backgroundColor = "";
        row.style.transform = "";
      });
    });

    // Add button ripple effects
    const buttons = studentsList.querySelectorAll("button");
    buttons.forEach((btn) => {
      btn.addEventListener("mousedown", createRipple);
    });
  }

  function showStudentForm(type, studentId = null) {
    let formHTML = "";

    if (type === "add") {
      formHTML = `
                <h2>Add New Student</h2>
                <form id="addStudentForm" action="../php/admin/add_student.php" method="post" enctype="multipart/form-data" class="animated-form">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade (1-10):</label>
                        <input type="number" id="grade" name="semester" min="1" max="10" required>
                    </div>
                    <div class="form-group">
                        <label for="rollno">Roll No:</label>
                        <input type="text" id="rollno" name="rollno" required>
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="gender" value="Male" required> Male</label>
                            <label class="radio-label"><input type="radio" name="gender" value="Female"> Female</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <select id="subject" name="b_ed" required>
                            <option value="" disabled selected>-- Select a subject --</option>
                            <option value="English">English</option>
                            <option value="Math">Math</option>
                            <option value="Science">Science</option>
                            <option value="Nepali">Nepali</option>
                            <option value="Health">Health</option>
                            <option value="Computer">Computer</option>
                            <option value="Social">Social</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Admitted Year:</label>
                        <select id="year" name="admitted_year" required>
                            <option value="" disabled selected>-- Select a year --</option>
                            ${generateYearOptions()}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo:</label>
                        <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" class="file-input">
                        <div class="file-preview"></div>
                    </div>
                    <button type="submit" class="submit-btn">Add Student</button>
                </form>
            `;
    } else if (type === "edit" && studentId) {
      formHTML = `
                <h2>Edit Student</h2>
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading student data...</p>
                </div>
            `;

      // After rendering the form, fetch student data
      setTimeout(() => {
        fetchStudentData(studentId);
      }, 100);
    }

    if (studentFormContainer) {
      studentFormContainer.innerHTML = formHTML;

      // Show modal with animation
      studentFormModal.style.display = "block";
      const modalContent = studentFormModal.querySelector(".modal-content");

      // Set initial state
      modalContent.style.opacity = "0";
      modalContent.style.transform = "translateY(-20px)";

      // Trigger animation
      setTimeout(() => {
        modalContent.style.transition = "all 0.3s ease";
        modalContent.style.opacity = "1";
        modalContent.style.transform = "translateY(0)";
      }, 50);

      // Initialize file input preview
      initFileInputPreview();

      // Add form submission handler
      const form = document.getElementById("addStudentForm");
      if (form) {
        form.addEventListener("submit", handleFormSubmit);
      }
    }
  }

  function fetchStudentData(studentId) {
    fetch(`../php/admin/get_student.php?id=${studentId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.student) {
          renderEditForm(data.student);
        } else {
          studentFormContainer.innerHTML = `
                        <div class="error-state">
                            <div class="error-icon">❌</div>
                            <h3>Failed to load student data</h3>
                            <p>Please try again later</p>
                            <button onclick="closeModal()" class="cancel-btn">Close</button>
                        </div>
                    `;
        }
      })
      .catch((error) => {
        console.error("Error fetching student:", error);
        studentFormContainer.innerHTML = `
                    <div class="error-state">
                        <div class="error-icon">❌</div>
                        <h3>Failed to load student data</h3>
                        <p>Please try again later</p>
                        <button onclick="closeModal()" class="cancel-btn">Close</button>
                    </div>
                `;
      });
  }

  function renderEditForm(student) {
    const formHTML = `
            <h2>Edit Student</h2>
            <form id="editStudentForm" action="../php/admin/update_student.php" method="post" enctype="multipart/form-data" class="animated-form">
                <input type="hidden" name="id" value="${student.id}">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" maxlength="50" value="${escapeHTML(
                      student.name
                    )}" required>
                </div>
                <div class="form-group">
                    <label for="grade">Grade (1-10):</label>
                    <input type="number" id="grade" name="semester" min="1" max="10" value="${
                      student.semester
                    }" required>
                </div>
                <div class="form-group">
                    <label for="rollno">Roll No:</label>
                    <input type="text" id="rollno" name="rollno" value="${escapeHTML(
                      student.rollno
                    )}" required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <div class="radio-group">
                        <label class="radio-label"><input type="radio" name="gender" value="Male" ${
                          student.gender === "Male" ? "checked" : ""
                        }> Male</label>
                        <label class="radio-label"><input type="radio" name="gender" value="Female" ${
                          student.gender === "Female" ? "checked" : ""
                        }> Female</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <select id="subject" name="b_ed" required>
                        <option value="" disabled>-- Select a subject --</option>
                        <option value="English" ${
                          student.b_ed === "English" ? "selected" : ""
                        }>English</option>
                        <option value="Math" ${
                          student.b_ed === "Math" ? "selected" : ""
                        }>Math</option>
                        <option value="Science" ${
                          student.b_ed === "Science" ? "selected" : ""
                        }>Science</option>
                        <option value="Nepali" ${
                          student.b_ed === "Nepali" ? "selected" : ""
                        }>Nepali</option>
                        <option value="Health" ${
                          student.b_ed === "Health" ? "selected" : ""
                        }>Health</option>
                        <option value="Computer" ${
                          student.b_ed === "Computer" ? "selected" : ""
                        }>Computer</option>
                        <option value="Social" ${
                          student.b_ed === "Social" ? "selected" : ""
                        }>Social</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Admitted Year:</label>
                    <select id="year" name="admitted_year" required>
                        <option value="" disabled>-- Select a year --</option>
                        ${generateYearOptions(student.admitted_year)}
                    </select>
                </div>
                <div class="form-group">
                    <label for="photo">New Photo (leave empty to keep current):</label>
                    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" class="file-input">
                    <div class="file-preview"></div>
                </div>
                ${
                  student.photo
                    ? `
                <div class="form-group">
                    <label>Current Photo:</label>
                    <div class="current-photo">
                        <img src="../uploads/${student.photo}" alt="Student Photo" class="student-photo">
                    </div>
                </div>
                `
                    : ""
                }
                <button type="submit" class="submit-btn">Update Student</button>
            </form>
        `;

    studentFormContainer.innerHTML = formHTML;

    // Initialize file input preview
    initFileInputPreview();

    // Add form submission handler
    const form = document.getElementById("editStudentForm");
    if (form) {
      form.addEventListener("submit", handleFormSubmit);
    }

    // Animate form elements
    animateFormElements();
  }

  function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');

    // Show loading state
    submitBtn.innerHTML = '<span class="spinner-small"></span> Processing...';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Show success message with animation
          const successMessage = document.createElement("div");
          successMessage.className = "success-message";
          successMessage.innerHTML = `
                    <div class="success-icon">✓</div>
                    <p>${data.message}</p>
                `;

          form.style.opacity = "0";
          form.style.height = form.offsetHeight + "px";
          form.style.overflow = "hidden";

          setTimeout(() => {
            form.innerHTML = "";
            form.appendChild(successMessage);
            form.style.opacity = "1";

            // Close modal and refresh list after delay
            setTimeout(() => {
              closeModal();
              loadStudents(); // Refresh the list
            }, 1500);
          }, 300);
        } else {
          // Show error message
          submitBtn.innerHTML = "Try Again";
          submitBtn.disabled = false;

          const errorMessage = document.createElement("div");
          errorMessage.className = "error-message";
          errorMessage.textContent = data.message || "An error occurred";

          // Insert error message after the button
          submitBtn.parentNode.insertBefore(
            errorMessage,
            submitBtn.nextSibling
          );

          // Remove error message after 3 seconds
          setTimeout(() => {
            errorMessage.remove();
          }, 3000);
        }
      })
      .catch((error) => {
        console.error("Error submitting form:", error);
        submitBtn.innerHTML = "Try Again";
        submitBtn.disabled = false;

        const errorMessage = document.createElement("div");
        errorMessage.className = "error-message";
        errorMessage.textContent = "An error occurred. Please try again later.";

        // Insert error message after the button
        submitBtn.parentNode.insertBefore(errorMessage, submitBtn.nextSibling);
      });
  }

  // File input preview
  function initFileInputPreview() {
    const fileInputs = document.querySelectorAll(".file-input");

    fileInputs.forEach((input) => {
      const preview = input.nextElementSibling;

      input.addEventListener("change", function () {
        preview.innerHTML = "";

        if (this.files && this.files[0]) {
          const reader = new FileReader();

          reader.onload = function (e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            img.className = "file-preview-img";
            preview.appendChild(img);

            // Animate image appearance
            img.style.opacity = "0";
            img.style.transform = "scale(0.8)";

            setTimeout(() => {
              img.style.transition = "all 0.3s ease";
              img.style.opacity = "1";
              img.style.transform = "scale(1)";
            }, 50);
          };

          reader.readAsDataURL(this.files[0]);
        }
      });
    });
  }

  // Animate form elements
  function animateFormElements() {
    const formGroups = document.querySelectorAll(".form-group");

    formGroups.forEach((group, index) => {
      group.style.opacity = "0";
      group.style.transform = "translateY(20px)";

      setTimeout(() => {
        group.style.transition = "all 0.3s ease";
        group.style.opacity = "1";
        group.style.transform = "translateY(0)";
      }, 50 * index);
    });
  }

  // Expose functions to global scope for onclick handlers
  window.editStudent = function (studentId) {
    showStudentForm("edit", studentId);
  };

  window.deleteStudent = function (studentId, studentName) {
    // Create custom confirm dialog
    const confirmDialog = document.createElement("div");
    confirmDialog.className = "confirm-dialog";
    confirmDialog.innerHTML = `
            <div class="confirm-dialog-content">
                <h3>Delete Student</h3>
                <p>Are you sure you want to delete <strong>${escapeHTML(
                  studentName
                )}</strong>?</p>
                <p class="warning">This action cannot be undone.</p>
                <div class="confirm-actions">
                    <button class="cancel-btn">Cancel</button>
                    <button class="delete-confirm-btn">Delete</button>
                </div>
            </div>
        `;

    document.body.appendChild(confirmDialog);

    // Add animation
    setTimeout(() => {
      confirmDialog.classList.add("show");
    }, 10);

    // Handle button clicks
    const cancelBtn = confirmDialog.querySelector(".cancel-btn");
    const deleteBtn = confirmDialog.querySelector(".delete-confirm-btn");

    cancelBtn.addEventListener("click", () => {
      confirmDialog.classList.remove("show");
      setTimeout(() => {
        confirmDialog.remove();
      }, 300);
    });

    deleteBtn.addEventListener("click", () => {
      // Show loading state
      deleteBtn.innerHTML = '<span class="spinner-small"></span> Deleting...';
      deleteBtn.disabled = true;
      cancelBtn.disabled = true;

      // Use GET with id param for compatibility with backend
      fetch(
        `../php/admin/delete_student.php?id=${encodeURIComponent(studentId)}`
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // Show success message
            confirmDialog.querySelector(".confirm-dialog-content").innerHTML = `
                        <div class="success-icon">✓</div>
                        <h3>Student Deleted</h3>
                        <p>${
                          data.message || "Student deleted successfully."
                        }</p>
                    `;

            // Close dialog and refresh list after delay
            setTimeout(() => {
              confirmDialog.classList.remove("show");
              setTimeout(() => {
                confirmDialog.remove();
                loadStudents(); // Refresh the list
              }, 300);
            }, 1500);
          } else {
            // Show error message
            confirmDialog.querySelector(".confirm-dialog-content").innerHTML = `
                        <div class="error-icon">❌</div>
                        <h3>Error</h3>
                        <p>${data.message || "Failed to delete student."}</p>
                        <button class="close-btn">Close</button>
                    `;

            confirmDialog
              .querySelector(".close-btn")
              .addEventListener("click", () => {
                confirmDialog.classList.remove("show");
                setTimeout(() => {
                  confirmDialog.remove();
                }, 300);
              });
          }
        })
        .catch((error) => {
          console.error("Error deleting student:", error);
          confirmDialog.querySelector(".confirm-dialog-content").innerHTML = `
                    <div class="error-icon">❌</div>
                    <h3>Error</h3>
                    <p>An error occurred. Please try again later.</p>
                    <button class="close-btn">Close</button>
                `;

          confirmDialog
            .querySelector(".close-btn")
            .addEventListener("click", () => {
              confirmDialog.classList.remove("show");
              setTimeout(() => {
                confirmDialog.remove();
              }, 300);
            });
        });
    });
  };
}

// Notices management
function initNoticesManagement() {
  const noticesList = document.getElementById("noticesList");
  const addNoticeBtn = document.getElementById("addNoticeBtn");
  const noticeFormModal = document.getElementById("noticeFormModal");
  const noticeFormContainer = document.getElementById("noticeFormContainer");
  const closeModalBtns = document.querySelectorAll(
    "#noticeFormModal .close-modal"
  );

  // Load notices on section show
  if (noticesList) {
    loadNotices();
  }

  // Add notice button
  if (addNoticeBtn) {
    addNoticeBtn.addEventListener("click", () => {
      showNoticeForm();
    });
    addNoticeBtn.addEventListener("mousedown", createRipple);
  }

  // Close modal
  closeModalBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      closeNoticeModal();
    });
  });
  window.addEventListener("click", (e) => {
    if (e.target === noticeFormModal) closeNoticeModal();
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && noticeFormModal.style.display === "block")
      closeNoticeModal();
  });

  function closeNoticeModal() {
    const modalContent = noticeFormModal.querySelector(".modal-content");
    modalContent.style.opacity = "0";
    modalContent.style.transform = "translateY(-20px)";
    setTimeout(() => {
      noticeFormModal.style.display = "none";
      modalContent.style.transform = "";
    }, 300);
  }

  function loadNotices() {
    if (!noticesList) return;
    noticesList.innerHTML = `<div class="loading-container"><div class="loading-spinner"></div><p>Loading notices...</p></div>`;
    fetch("../php/admin/get_notices.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success" && data.notices.length > 0) {
          renderNotices(data.notices);
        } else {
          noticesList.innerHTML = `<div class="empty-state"><div class="empty-icon">📝</div><h3>No notices found</h3></div>`;
        }
      })
      .catch(() => {
        noticesList.innerHTML = `<div class="error-state"><div class="error-icon">❌</div><h3>Failed to load notices</h3></div>`;
      });
  }

  function renderNotices(notices) {
    let html = `<table class="data-table"><thead><tr><th>ID</th><th>Title</th><th>Content</th><th>Date</th></tr></thead><tbody>`;
    notices.forEach((notice) => {
      html += `<tr>
        <td>${notice.id}</td>
        <td>${escapeHTML(notice.title)}</td>
        <td>${escapeHTML(notice.content)}</td>
        <td>${escapeHTML(notice.created_at)}</td>
      </tr>`;
    });
    html += `</tbody></table>`;
    noticesList.innerHTML = html;
  }

  function showNoticeForm() {
    noticeFormContainer.innerHTML = `
      <h2>Add New Notice</h2>
      <form id="addNoticeForm" action="../php/admin/add_notice.php" method="post" class="animated-form">
        <div class="form-group">
          <label for="title">Title:</label>
          <input type="text" id="title" name="title" maxlength="100" required>
        </div>
        <div class="form-group">
          <label for="content">Content:</label>
          <textarea id="content" name="content" rows="4" maxlength="1000" required></textarea>
        </div>
        <button type="submit" class="submit-btn">Add Notice</button>
      </form>
    `;
    noticeFormModal.style.display = "block";
    const modalContent = noticeFormModal.querySelector(".modal-content");
    modalContent.style.opacity = "0";
    modalContent.style.transform = "translateY(-20px)";
    setTimeout(() => {
      modalContent.style.transition = "all 0.3s ease";
      modalContent.style.opacity = "1";
      modalContent.style.transform = "translateY(0)";
    }, 50);

    const form = document.getElementById("addNoticeForm");
    if (form) {
      form.addEventListener("submit", handleNoticeFormSubmit);
    }
  }

  function handleNoticeFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<span class="spinner-small"></span> Adding...';
    submitBtn.disabled = true;
    const formData = new FormData(form);
    fetch(form.action, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          form.innerHTML = `<div class="success-message"><div class="success-icon">✓</div><p>${data.message}</p></div>`;
          setTimeout(() => {
            closeNoticeModal();
            loadNotices();
          }, 1200);
        } else {
          submitBtn.innerHTML = "Try Again";
          submitBtn.disabled = false;
          const errorMessage = document.createElement("div");
          errorMessage.className = "error-message";
          errorMessage.textContent = data.message || "An error occurred";
          submitBtn.parentNode.insertBefore(
            errorMessage,
            submitBtn.nextSibling
          );
          setTimeout(() => errorMessage.remove(), 3000);
        }
      })
      .catch(() => {
        submitBtn.innerHTML = "Try Again";
        submitBtn.disabled = false;
        const errorMessage = document.createElement("div");
        errorMessage.className = "error-message";
        errorMessage.textContent = "An error occurred. Please try again later.";
        submitBtn.parentNode.insertBefore(errorMessage, submitBtn.nextSibling);
      });
  }
}
