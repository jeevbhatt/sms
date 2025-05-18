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
  const updateStudentBtn = document.getElementById("updateStudentBtn");
  const removeStudentBtn = document.getElementById("removeStudentBtn");
  const studentSearch = document.getElementById("studentSearch");
  const studentFormModal = document.getElementById("studentFormModal");
  // Ensure modal content container exists
  let studentFormContainer = document.getElementById("studentFormContainer");
  if (!studentFormContainer) {
    studentFormContainer = document.createElement("div");
    studentFormContainer.id = "studentFormContainer";
    studentFormModal.appendChild(studentFormContainer);
  }

  // Load students list
  if (studentsList) {
    loadStudents();
  }

  // Add student button
  if (addStudentBtn) {
    addStudentBtn.addEventListener("click", () => {
      // Load and show the same add form as frontend
      loadFrontendServiceForm("addStudent");
    });
  }

  // Update student button
  if (updateStudentBtn) {
    updateStudentBtn.addEventListener("click", () => {
      loadFrontendServiceForm("updateStudent");
    });
  }

  // Remove student button
  if (removeStudentBtn) {
    removeStudentBtn.addEventListener("click", () => {
      loadFrontendServiceForm("removeStudent");
    });
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
  const closeModalBtns = document.querySelectorAll(".close-modal");
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
}

// --- Load frontend service form in admin modal ---
function loadFrontendServiceForm(formType) {
  // Ensure modal and container exist
  const modal = document.getElementById("studentFormModal");
  const container = document.getElementById("studentFormContainer");
  if (!modal || !container) return;
  // Dynamically load main.js if not already loaded
  if (!window.initServiceForms) {
    const script = document.createElement("script");
    script.src = "/sms/js/main.js";
    script.onload = () => {
      showFrontendForm(formType, modal, container);
    };
    document.body.appendChild(script);
  } else {
    showFrontendForm(formType, modal, container);
  }
}

function showFrontendForm(formType, modal, container) {
  // Use the frontend's form loader
  if (window.initServiceForms) {
    // Create a temp div to get the form HTML
    const tempDiv = document.createElement("div");
    // Monkey-patch document.body for form injection
    const origBody = document.body;
    document.body = tempDiv;
    try {
      window.loadForm && window.loadForm(formType);
    } catch (e) {}
    document.body = origBody;
    // Extract form HTML
    const formHTML = tempDiv.innerHTML;
    container.innerHTML = formHTML;
    modal.style.display = "block";
    // Animate modal in
    const modalContent = modal.querySelector(".modal-content");
    if (modalContent) {
      modalContent.style.opacity = "0";
      modalContent.style.transform = "translateY(-20px)";
      setTimeout(() => {
        modalContent.style.transition = "all 0.3s ease";
        modalContent.style.opacity = "1";
        modalContent.style.transform = "translateY(0)";
      }, 50);
    }
  } else {
    container.innerHTML =
      '<div style="color:red;">Unable to load form. Please reload the page.</div>';
    modal.style.display = "block";
  }
}

// Ensure showStudentForm and removeStudent are defined globally
window.showStudentForm = function (mode, id) {
  // Show modal and load form for add/edit
  const modal = document.getElementById("studentFormModal");
  const container = document.getElementById("studentFormContainer");
  if (!modal || !container) return;
  modal.style.display = "block";
  if (mode === "add") {
    container.innerHTML =
      '<h3>Add Student</h3><form id="addStudentForm">...form fields here...</form>';
    // TODO: Implement AJAX form submit for add
  } else if (mode === "edit" && id) {
    container.innerHTML =
      '<h3>Edit Student</h3><form id="editStudentForm">...form fields here...</form>';
    // TODO: Load student data and implement AJAX form submit for edit
  }
};

window.removeStudent = function (id) {
  // TODO: Implement AJAX call to remove student
  alert("Student with ID " + id + " would be removed (implement AJAX call).");
};

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
        if (data.success && data.notices && data.notices.length > 0) {
          renderNotices(data.notices);
        } else {
          noticesList.innerHTML = `<div class="empty-state"><div class="empty-icon">📝</div><h3>No notices found.</h3></div>`;
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
