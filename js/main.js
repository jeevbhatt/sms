document.addEventListener("DOMContentLoaded", function () {
  // Initialize carousel
  initCarousel();

  // Initialize scroll animations
  initScrollAnimations();

  // Initialize service forms
  initServiceForms();

  // Initialize contact form
  initContactForm();

  // Page transition effect
  initPageTransitions();

  // Header scroll effect
  initHeaderScroll();

  // Initialize button ripple effects
  initRippleEffect();

  // Add animation classes to elements
  addAnimationClasses();
});

// Header scroll effect
function initHeaderScroll() {
  const header = document.querySelector(".head");

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });
}

// Carousel functionality with improved animations
function initCarousel() {
  const carousel = document.querySelector(".carousel");
  const cells = document.querySelectorAll(".carousel-cell");
  const dotsContainer = document.querySelector(".carousel-dots");
  const prevButton = document.querySelector(".prev-button");
  const nextButton = document.querySelector(".next-button");

  if (!carousel || !cells.length) return;

  let currentIndex = 0;
  const totalSlides = cells.length;

  // Create dots
  for (let i = 0; i < totalSlides; i++) {
    const dot = document.createElement("div");
    dot.classList.add("dot");
    if (i === 0) dot.classList.add("active");
    dot.addEventListener("click", () => goToSlide(i));
    dotsContainer.appendChild(dot);
  }

  // Navigation buttons
  prevButton.addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    updateCarousel("prev");
  });

  nextButton.addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateCarousel("next");
  });

  // Go to specific slide
  function goToSlide(index) {
    const direction = index > currentIndex ? "next" : "prev";
    currentIndex = index;
    updateCarousel(direction);
  }

  // Update carousel position with direction-based animation
  function updateCarousel(direction = "next") {
    // Apply transition
    carousel.style.transition =
      "transform 0.5s cubic-bezier(0.645, 0.045, 0.355, 1)";
    carousel.style.transform = `translateX(-${currentIndex * 100}%)`;

    // Update dots
    document.querySelectorAll(".dot").forEach((dot, index) => {
      dot.classList.toggle("active", index === currentIndex);
    });

    // Pause all videos
    document.querySelectorAll(".carousel-cell video").forEach((video) => {
      video.pause();
    });

    // Play current video if exists
    const currentVideo = cells[currentIndex].querySelector("video");
    if (currentVideo) {
      currentVideo.play();
    }
  }

  // Auto-advance carousel
  let autoplayInterval = setInterval(() => {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateCarousel();
  }, 5000);

  // Pause autoplay on hover
  carousel.addEventListener("mouseenter", () => {
    clearInterval(autoplayInterval);
  });

  carousel.addEventListener("mouseleave", () => {
    autoplayInterval = setInterval(() => {
      currentIndex = (currentIndex + 1) % totalSlides;
      updateCarousel();
    }, 5000);
  });

  // Handle touch events for mobile
  let touchStartX = 0;
  let touchEndX = 0;

  carousel.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].screenX;
    // Pause transition during touch
    carousel.style.transition = "none";
  });

  carousel.addEventListener("touchmove", (e) => {
    const currentX = e.changedTouches[0].screenX;
    const diff = currentX - touchStartX;
    const offset = -currentIndex * 100 + (diff / carousel.offsetWidth) * 100;

    // Limit the drag to the next/prev slide only
    if (offset < (-totalSlides + 1) * 100 || offset > 0) return;

    carousel.style.transform = `translateX(${offset}%)`;
  });

  carousel.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchEndX - touchStartX;

    // Restore transition
    carousel.style.transition =
      "transform 0.5s cubic-bezier(0.645, 0.045, 0.355, 1)";

    // Determine if we should change slides based on swipe distance
    if (Math.abs(diff) > 50) {
      if (diff < 0) {
        // Swipe left - next slide
        currentIndex = Math.min(currentIndex + 1, totalSlides - 1);
      } else {
        // Swipe right - previous slide
        currentIndex = Math.max(currentIndex - 1, 0);
      }
    }

    updateCarousel();
  });

  // Initial update
  updateCarousel();
}

// Scroll animations with improved intersection observer
function initScrollAnimations() {
  const sections = document.querySelectorAll(".content-section");

  const observer = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");

          // Animate children with staggered delay
          const animElements = entry.target.querySelectorAll(".anim-item");
          animElements.forEach((el, index) => {
            setTimeout(() => {
              el.classList.add("visible");
            }, 100 * index);
          });

          // Stop observing after animation
          // observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.15,
      rootMargin: "0px 0px -10% 0px",
    }
  );

  sections.forEach((section) => observer.observe(section));
}

// Service forms with improved modal animations
function initServiceForms() {
  const serviceLinks = document.querySelectorAll(".service-link");
  const modal = document.getElementById("formModal");
  const modalContent = document.getElementById("modalContent");
  const closeModalBtn = document.querySelector(".close-modal");

  serviceLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const formType = this.getAttribute("data-form");
      loadForm(formType);
      openModal();
    });
  });

  if (closeModalBtn) {
    closeModalBtn.addEventListener("click", () => {
      closeModal();
    });
  }

  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Escape key to close modal
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.style.display === "block") {
      closeModal();
    }
  });

  function openModal() {
    modal.style.display = "block";
    // Trigger reflow
    void modal.offsetWidth;
    modal.classList.add("show");
    document.body.style.overflow = "hidden"; // Prevent background scrolling
  }

  function closeModal() {
    modal.classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
      document.body.style.overflow = ""; // Restore scrolling
    }, 300); // Match transition duration
  }

  function loadForm(formType) {
    let formHTML = "";

    switch (formType) {
      case "addStudent":
        formHTML = `
                    <h2>Add New Student</h2>
                    <form action="php/add_student.php" method="post" enctype="multipart/form-data" class="animated-form">
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
                        <button type="submit" class="submit-btn">Submit</button>
                    </form>
                `;
        break;

      case "updateStudent":
        formHTML = `
                    <h2>Update Student Details</h2>
                    <form action="php/update_student.php" method="post" enctype="multipart/form-data" class="animated-form">
                        <div class="form-group">
                            <label for="student_id">Student ID:</label>
                            <input type="number" id="student_id" name="id" required>
                        </div>
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
                                <label class="radio-label"><input type="radio" name="gender" value="Male"> Male</label>
                                <label class="radio-label"><input type="radio" name="gender" value="Female"> Female</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <select id="subject" name="b_ed">
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
                            <select id="year" name="admitted_year">
                                <option value="" disabled selected>-- Select a year --</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="photo">New Photo (leave empty to keep current):</label>
                            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" class="file-input">
                            <div class="file-preview"></div>
                        </div>
                        <button type="submit" class="submit-btn">Update</button>
                    </form>
                `;
        break;

      case "removeStudent":
        formHTML = `
                    <h2>Remove Student</h2>
                    <form id="deleteStudentForm" action="php/delete_student.php" method="post">
                        <div class="form-group">
                            <label for="student_id">Student ID:</label>
                            <input type="number" id="student_id" name="id" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Grade (1-10):</label>
                            <input type="number" id="semester" name="semester" min="1" max="10" required>
                            <small>Required: Enter the student's grade (semester) to confirm deletion</small>
                        </div>
                        <button type="button" onclick="confirmDelete()" class="delete-btn">Delete</button>
                    </form>
                `;
        break;

      case "viewRecords":
        formHTML = `
                    <h2>View Student Records</h2>
                    <form action="php/view_records.php" method="post" class="animated-form">
                        <div class="form-group">
                            <label for="student_id">Student ID (optional):</label>
                            <input type="number" id="student_id" name="id">
                        </div>
                        <div class="form-group">
                            <label for="grade">Grade (1-10) (optional):</label>
                            <input type="number" id="grade" name="semester" min="1" max="10">
                        </div>
                        <div class="form-group">
                            <label for="rollno">Roll No (optional):</label>
                            <input type="text" id="rollno" name="rollno">
                        </div>
                        <div class="form-group">
                            <label>Gender (optional):</label>
                            <div class="radio-group">
                                <label class="radio-label"><input type="radio" name="gender" value="Male"> Male</label>
                                <label class="radio-label"><input type="radio" name="gender" value="Female"> Female</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject (optional):</label>
                            <select id="subject" name="b_ed">
                                <option value="" selected>-- Any --</option>
                                <option value="English">English</option>
                                <option value="Math">Math</option>
                                <option value="Science">Science</option>
                                <option value="Nepali">Nepali</option>
                                <option value="Health">Health</option>
                                <option value="Computer">Computer</option>
                                <option value="Social">Social</option>
                            </select>
                        </div>
                        <button type="submit" class="submit-btn">Search</button>
                    </form>
                `;
        break;
    }

    modalContent.innerHTML = formHTML;

    // Initialize file input preview
    initFileInputPreview();

    // Add animation to form elements
    const formElements = modalContent.querySelectorAll(".form-group");
    formElements.forEach((el, index) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(20px)";
      setTimeout(() => {
        el.style.transition = "all 0.3s ease";
        el.style.opacity = "1";
        el.style.transform = "translateY(0)";
      }, 50 * index);
    });
  }

  // Add this function after loadForm
  function confirmDelete() {
    const studentId = document.getElementById("student_id").value;
    if (!studentId) {
      alert("Please enter a Student ID to delete.");
      return false;
    }

    if (
      confirm(
        "Are you sure you want to delete this student? This action cannot be undone."
      )
    ) {
      document.getElementById("deleteStudentForm").submit();
      return true;
    }
    return false;
  }

  // Make the confirmDelete function globally accessible
  window.confirmDelete = confirmDelete;

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
            img.style.maxWidth = "100px";
            img.style.maxHeight = "100px";
            img.style.borderRadius = "5px";
            img.style.marginTop = "10px";
            preview.appendChild(img);
          };

          reader.readAsDataURL(this.files[0]);
        }
      });
    });
  }

  function generateYearOptions() {
    const currentYear = new Date().getFullYear();
    let options = "";

    // Generate options for the last 10 years
    for (let year = currentYear; year >= currentYear - 10; year--) {
      options += `<option value="${year}">${year}</option>`;
    }

    return options;
  }
}

// Contact form with improved feedback
function initContactForm() {
  const contactForm = document.getElementById("contactForm");
  const formResponse = document.getElementById("formResponse");

  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Show loading indicator
      formResponse.innerHTML =
        '<div class="loading-spinner"><div class="spinner"></div> Sending message...</div>';
      formResponse.style.display = "block";
      formResponse.className = "form-response";

      const formData = new FormData(this);

      fetch("php/contact.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((data) => {
          formResponse.innerHTML = data;
          formResponse.classList.add("success-message");
          formResponse.classList.add("animate-fadeIn");
          contactForm.reset();

          // Scroll to response
          formResponse.scrollIntoView({ behavior: "smooth", block: "nearest" });
        })
        .catch((error) => {
          console.error("Error:", error);
          formResponse.innerHTML = "An error occurred. Please try again later.";
          formResponse.classList.add("error-message");
          formResponse.classList.add("animate-fadeIn");
        });
    });
  }
}

// Page transitions with improved animations
function initPageTransitions() {
  document.querySelectorAll("a").forEach((link) => {
    const href = link.getAttribute("href");

    if (href && !href.startsWith("#") && !href.startsWith("http")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        document.body.classList.add("fade-out");

        setTimeout(() => {
          window.location.href = href;
        }, 500);
      });
    }
  });

  // Add fade-in effect when page loads
  window.addEventListener("load", () => {
    document.body.classList.add("fade-in");
  });
}

// Button ripple effect
function initRippleEffect() {
  const buttons = document.querySelectorAll("button, .btn, .service-link");

  buttons.forEach((button) => {
    button.addEventListener("mousedown", function (e) {
      const rect = button.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const ripple = document.createElement("span");
      ripple.classList.add("ripple");
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;

      this.appendChild(ripple);

      setTimeout(() => {
        ripple.remove();
      }, 600); // Match the animation duration
    });
  });
}

// Add animation classes to elements
function addAnimationClasses() {
  // Add anim-item class to elements that should animate on scroll
  const animElements = [
    ".team-member",
    ".service-link",
    ".stat-card",
    ".notice-card",
  ];

  animElements.forEach((selector) => {
    document.querySelectorAll(selector).forEach((el) => {
      el.classList.add("anim-item");
    });
  });
}

// Add CSS for additional animations
const styleSheet = document.createElement("style");
styleSheet.textContent = `
    .anim-item {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .anim-item.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .ripple {
        position: absolute;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .animated-form .form-group {
        transition: all 0.3s ease;
    }

    .radio-group {
        display: flex;
        gap: 20px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .radio-label input {
        margin-right: 5px;
        width: auto;
    }

    .file-input {
        padding: 10px 0;
    }

    .loading-spinner {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(67, 97, 238, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .submit-btn {
        background-color: var(--primary-color);
        color: white;
        width: 100%;
        margin-top: 10px;
    }

    .delete-btn {
        background-color: var(--danger-color);
        color: white;
        width: 100%;
        margin-top: 10px;
    }
`;
document.head.appendChild(styleSheet);

function confirmDelete() {
  const form = document.getElementById("deleteStudentForm");
  const studentId = document.getElementById("student_id").value;
  if (!studentId) {
    alert("Please enter a valid Student ID.");
    return;
  }
  if (
    confirm(
      "Are you sure you want to delete this student? This action cannot be undone."
    )
  ) {
    form.submit();
  }
}
