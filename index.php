<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>School Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="icon" type="image/x-icon" href="img/sms.ico" />
    <script src="js/main.js" defer></script>
  </head>
  <body>
    <div class="head">
      <ul class="nav">
        <li class="niv">
          <a href="index.html" class="logo-link">
            <img src="img/sms.png" alt="SMS" id="ima" />
            <h2 class="logo">SMS</h2>
          </a>
        </li>
        <ul class="lin">
          <li><a href="#home" class="ligrad">Home</a></li>
          <li><a href="#about" class="ligrad">About</a></li>
          <li><a href="#services" class="ligrad">Services</a></li>
          <li><a href="#contac" class="ligrad">Contact</a></li>
          <li><a href="notices.html" class="ligrad">Notices</a></li>
        </ul>
        <ul class="site">
          <li>
            <a href="http://www.facebook.com" class="web">
              <img src="img/facebook.svg" alt="Facebook" id="im" />
            </a>
          </li>
          <li>
            <a href="http://www.twitter.com" class="web">
              <img src="img/twitter.svg" alt="Twitter" id="im" />
            </a>
          </li>
          <li>
            <a href="admin/login.php" class="admin-link">
              <img
                src="img/admin.svg"
                alt="Admin"
                id="im"
                title="Admin Login"
              />
            </a>
          </li>
        </ul>
      </ul>
    </div>

    <div id="home" class="carousel-container">
      <div class="carousel">
        <div class="carousel-cell">
          <img src="img/uni.jpg" alt="Badminton" />
        </div>
        <div class="carousel-cell">
          <img src="img/g.jpg" alt="Students Group" />
        </div>
        <div class="carousel-cell">
          <img src="img/v.jpg" alt="Sports" />
        </div>
        <div class="carousel-cell">
          <img src="img/i.jpg" alt="Image" />
        </div>
        <div class="carousel-cell"><img src="img/li.jpg" alt="Library" /></div>
      </div>
      <div class="carousel-nav">
        <button class="prev-button">&#10094;</button>
        <button class="next-button">&#10095;</button>
      </div>
      <div class="carousel-dots"></div>
    </div>

    <section id="about" class="content-section">
      <div class="about-container">
        <h2>About Us</h2>
        <div class="about-content">
          <img src="img/logo.png" class="about-logo" alt="School Logo" />
          <p>
            Welcome to our School Management System. We provide comprehensive
            tools for managing student records, academic performance, and school
            communications. Our system is designed to streamline administrative
            tasks and improve the educational experience for students, teachers,
            and parents.
          </p>
        </div>
        <div class="team-container">
          <h3>Our Team</h3>
          <div class="team-members">
            <div class="team-member">
              <img src="img/tanka.jpg" alt="Team Member" />
              <span>Tanka Timilsina</span>
              <span class="role">Principal</span>
            </div>
            <div class="team-member">
              <img src="img/saurav.jpg" alt="Team Member" />
              <span>Saurav S. Thakuri</span>
              <span class="role">Vice Principal</span>
            </div>
            <div class="team-member">
              <img src="img/puja.jpg" alt="Team Member" />
              <span>Puja Bhatt</span>
              <span class="role">Secretary</span>
            </div>
            <div class="team-member">
              <img src="img/prakash.jpg" alt="Team Member" />
              <span>Prakash Pokharel</span>
              <span class="role">IT Administrator</span>
            </div>
            <div class="team-member">
              <img src="img/dipu.jpg" alt="Team Member" />
              <span>Dipu Pant</span>
              <span class="role">Teacher</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="services" class="content-section">
      <h2>Our Services</h2>
      <div class="services-container">
        <div class="service-panel">
          <h3>Student Management</h3>
          <ul class="service-links">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <li><a href="#" class="service-link" data-form="addStudent">Add Student</a></li>
              <li><a href="#" class="service-link" data-form="updateStudent">Update Student Details</a></li>
              <li><a href="#" class="service-link" data-form="removeStudent">Remove Student</a></li>
            <?php endif; ?>
            <li><a href="#" class="service-link" data-form="viewRecords">View Records</a></li>
          </ul>
        </div>
        <div class="service-demo">
          <img src="img/demo.gif" alt="Demo" class="demo-gif" />
          <p class="demo-text">See how our system works</p>
        </div>
      </div>
    </section>

    <section id="contac" class="content-section">
      <div class="contact-container">
        <h2>Contact Us</h2>
        <p>
          If you have any questions or need assistance, please reach out to us
          using the form below.
        </p>
        <form
          action="php/contact.php"
          method="post"
          class="contact-form"
          id="contactForm"
        >
          <div class="form-group">
            <label for="fullname">Full Name:</label>
            <input
              type="text"
              id="fullname"
              name="fullname"
              maxlength="50"
              placeholder="Your Full Name"
              required
            />
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input
              type="email"
              id="email"
              name="email"
              maxlength="50"
              placeholder="Your Email"
              required
            />
          </div>
          <div class="form-group">
            <label for="message">Your Message:</label>
            <textarea
              id="message"
              name="message"
              rows="5"
              placeholder="Write your message here..."
              required
            ></textarea>
          </div>
          <button type="submit">Send Message</button>
        </form>
        <div id="formResponse" class="form-response"></div>
      </div>
    </section>

    <!-- Modal for forms -->
    <div id="formModal" class="modal">
      <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="modalContent"></div>
      </div>
    </div>

    <footer>
      <div class="footer-content">
        <h3>Student life is the most important life of your journey</h3>
        <p>Copyright &copy; 2024 School Management System</p>
      </div>
      <div class="marquee-bar">
        <div class="marquee-content">
          <span
            >📞 Phone: +1-234-567-8900 | 📧 Email: info@schoolms.com | 🌐
            Website: http://www.schoolms.com</span
          >
        </div>
      </div>
    </footer>
  </body>
</html>
<?php
// Close database connection
/* if (isset($conn)) $conn->close(); */
?>
