document.addEventListener("DOMContentLoaded", function () {
  // Initialize notices
  initNotices();

  // Add admin controls if user is admin (for demo, always show)
  addNoticeAdminControls();

  // Page transition effect
  initPageTransitions();

  // Header scroll effect
  initHeaderScroll();
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

// Notices functionality with improved animations
function initNotices() {
  const noticesContainer = document.getElementById("notices-list");
  const loadingSpinner = document.getElementById("loading-spinner");

  let offset = 0;
  const limit = 5;
  let isLoading = false;
  let hasMoreNotices = true;

  // Initial load with animation
  setTimeout(() => {
    loadNotices();
  }, 300);

  // Scroll event for infinite loading with improved detection
  window.addEventListener("scroll", () => {
    const scrollPosition = window.scrollY + window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // Load more when user is 200px from bottom
    if (scrollPosition >= documentHeight - 200) {
      if (!isLoading && hasMoreNotices) {
        loadNotices();
      }
    }
  });

  function loadNotices() {
    if (isLoading) return;

    isLoading = true;
    loadingSpinner.style.display = "block";

    // Add loading animation
    loadingSpinner.classList.add("animate-pulse");

    fetch(`php/fetch_notices.php?offset=${offset}&limit=${limit}`)
      .then((response) => response.json())
      .then((data) => {
        loadingSpinner.style.display = "none";
        loadingSpinner.classList.remove("animate-pulse");

        if (data.success && data.notices && data.notices.length > 0) {
          renderNotices(data.notices);
          offset += data.notices.length;

          // Check if we've reached the end
          if (data.notices.length < limit) {
            hasMoreNotices = false;

            // Show end of notices message
            if (offset > 0) {
              const endMessage = document.createElement("div");
              endMessage.className = "end-message";
              endMessage.textContent = "You've reached the end of notices";
              endMessage.style.textAlign = "center";
              endMessage.style.padding = "20px";
              endMessage.style.color = "var(--gray-600)";
              endMessage.style.fontStyle = "italic";
              endMessage.style.opacity = "0";
              endMessage.style.transform = "translateY(20px)";
              endMessage.style.transition = "all 0.5s ease";

              noticesContainer.appendChild(endMessage);

              // Trigger animation
              setTimeout(() => {
                endMessage.style.opacity = "1";
                endMessage.style.transform = "translateY(0)";
              }, 100);
            }
          }
        } else {
          hasMoreNotices = false;

          if (offset === 0) {
            noticesContainer.innerHTML = `
                            <div class="no-notices" style="text-align: center; padding: 50px 20px;">
                                <div style="font-size: 50px; color: var(--gray-400); margin-bottom: 20px;">📝</div>
                                <h3>No notices found.</h3>
                                <p style="color: var(--gray-600);">Check back later for updates</p>
                            </div>
                        `;
          }
        }

        isLoading = false;
      })
      .catch((error) => {
        console.error("Error loading notices:", error);
        loadingSpinner.style.display = "none";
        loadingSpinner.classList.remove("animate-pulse");

        const errorMessage = document.createElement("div");
        errorMessage.className = "error-message";
        errorMessage.innerHTML = `
                    <p>Failed to load notices. Please try again later.</p>
                    <button onclick="location.reload()" class="retry-btn">Retry</button>
                `;
        errorMessage.style.textAlign = "center";
        errorMessage.style.padding = "20px";
        errorMessage.style.margin = "20px 0";
        errorMessage.style.backgroundColor = "rgba(249, 65, 68, 0.1)";
        errorMessage.style.color = "var(--danger-color)";
        errorMessage.style.borderRadius = "var(--border-radius-md)";

        noticesContainer.appendChild(errorMessage);
        isLoading = false;
      });
  }

  function renderNotices(notices) {
    notices.forEach((notice, index) => {
      const noticeCard = document.createElement("div");
      noticeCard.className = "notice-card";
      noticeCard.innerHTML = `
        <div style="display: flex; align-items: flex-start;">
          <div style="flex:1;">
            <h3>${escapeHTML(notice.title)}</h3>
            <p>${escapeHTML(notice.content)}</p>
            <div class="date">Posted on: ${formatDate(notice.created_at)}</div>
          </div>
          <div class="inline-flex items-center rounded-md shadow-sm" style="margin-left: 16px;">
            <button class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-l-lg font-medium px-4 py-2 inline-flex space-x-1 items-center edit-notice-btn" data-id="${
              notice.id
            }" title="Edit">
              <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg></span>
              <span class="hidden md:inline-block">Edit</span>
            </button>
            <button class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border-y border-slate-200 font-medium px-4 py-2 inline-flex space-x-1 items-center view-notice-btn" data-id="${
              notice.id
            }" title="View">
              <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span>
              <span class="hidden md:inline-block">View</span>
            </button>
            <button class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-r-lg font-medium px-4 py-2 inline-flex space-x-1 items-center delete-notice-btn" data-id="${
              notice.id
            }" title="Delete">
              <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg></span>
              <span class="hidden md:inline-block">Delete</span>
            </button>
          </div>
        </div>
      `;

      // Set initial state for animation
      noticeCard.style.opacity = "0";
      noticeCard.style.transform = "translateY(30px)";

      noticesContainer.appendChild(noticeCard);

      // Trigger animation with staggered delay
      setTimeout(() => {
        noticeCard.style.transition = "all 0.5s ease";
        noticeCard.style.opacity = "1";
        noticeCard.style.transform = "translateY(0)";
      }, 100 * index);
    });

    // Add event listeners for action buttons
    setTimeout(() => {
      document.querySelectorAll(".edit-notice-btn").forEach((btn) => {
        btn.onclick = function () {
          showNoticeForm("update", this.getAttribute("data-id"));
        };
      });
      document.querySelectorAll(".delete-notice-btn").forEach((btn) => {
        btn.onclick = function () {
          showNoticeForm("remove", this.getAttribute("data-id"));
        };
      });
      document.querySelectorAll(".view-notice-btn").forEach((btn) => {
        btn.onclick = function () {
          showNoticeForm("view", this.getAttribute("data-id"));
        };
      });
    }, 0);
  }

  function escapeHTML(str) {
    if (!str) return "";
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    return date.toLocaleDateString("en-US", options);
  }
}

// Page transitions
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
  // Add fade-in effect when the page loads
  window.addEventListener("load", () => {
    document.body.classList.add("fade-in");
  });
}

function addNoticeAdminControls() {
  const noticesContainer = document.getElementById("notices-container");
  if (!noticesContainer) return;
  // Admin controls bar
  const adminBar = document.createElement("div");
  adminBar.className = "notice-admin-bar";
  adminBar.innerHTML = `
        <button id="addNoticeBtn" class="action-btn">Add Notice</button>
    `;
  noticesContainer.insertBefore(adminBar, noticesContainer.children[1]);

  // Modal for add/edit/delete
  const modal = document.createElement("div");
  modal.id = "noticeModal";
  modal.className = "modal";
  modal.style.display = "none";
  modal.innerHTML = `
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="noticeModalContent"></div>
        </div>
    `;
  document.body.appendChild(modal);
  document.querySelector(".close-modal").onclick = () =>
    (modal.style.display = "none");
  window.onclick = (e) => {
    if (e.target === modal) modal.style.display = "none";
  };

  // Add Notice
  document.getElementById("addNoticeBtn").onclick = function () {
    showNoticeForm("add");
  };
}

function showNoticeForm(mode, noticeId = null) {
  const modal = document.getElementById("noticeModal");
  const content = document.getElementById("noticeModalContent");
  let html = "";
  if (mode === "add") {
    html = `
            <h3>Add Notice</h3>
            <form id="addNoticeForm">
                <input type="text" name="title" placeholder="Title" required style="width:100%;margin-bottom:8px;">
                <textarea name="content" placeholder="Content" required style="width:100%;height:100px;"></textarea>
                <button type="submit" class="action-btn">Add</button>
                <div id="noticeFormMsg"></div>
            </form>
        `;
    content.innerHTML = html;
    modal.style.display = "block";
    document.getElementById("addNoticeForm").onsubmit = function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch("php/admin/add_notice.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          const msg = document.getElementById("noticeFormMsg");
          if (data.success) {
            msg.style.color = "green";
            msg.textContent = "Notice added!";
            setTimeout(() => {
              modal.style.display = "none";
              location.reload();
            }, 800);
          } else {
            msg.style.color = "red";
            msg.textContent = data.error || "Failed to add notice.";
          }
        });
    };
  } else if (mode === "update") {
    html = `
      <h3>Edit Notice</h3>
      <form id="updateNoticeForm">
        <input type="hidden" name="id" value="${noticeId || ""}">
        <input type="text" name="title" placeholder="New Title" required style="width:100%;margin-bottom:8px;">
        <textarea name="content" placeholder="New Content" required style="width:100%;height:100px;"></textarea>
        <button type="submit" class="action-btn">Update</button>
        <div id="noticeFormMsg"></div>
      </form>
    `;
    content.innerHTML = html;
    // Fetch notice data and prefill
    fetch(`php/admin/get_notice.php?id=${noticeId}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.notice) {
          document.querySelector("#updateNoticeForm [name=title]").value =
            data.notice.title;
          document.querySelector("#updateNoticeForm [name=content]").value =
            data.notice.content;
        }
      });
    modal.style.display = "block";
    document.getElementById("updateNoticeForm").onsubmit = function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch("php/admin/update_notice.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          const msg = document.getElementById("noticeFormMsg");
          if (data.success) {
            msg.style.color = "green";
            msg.textContent = "Notice updated!";
            setTimeout(() => {
              modal.style.display = "none";
              location.reload();
            }, 800);
          } else {
            msg.style.color = "red";
            msg.textContent = data.error || "Failed to update notice.";
          }
        });
    };
  } else if (mode === "remove") {
    html = `
      <h3>Delete Notice</h3>
      <form id="removeNoticeForm">
        <input type="hidden" name="noticeId" value="${noticeId || ""}">
        <p>Are you sure you want to delete this notice?</p>
        <button type="submit" class="action-btn" style="background:#e74c3c;">Delete</button>
        <div id="noticeFormMsg"></div>
      </form>
    `;
    content.innerHTML = html;
    modal.style.display = "block";
    document.getElementById("removeNoticeForm").onsubmit = function (e) {
      e.preventDefault();
      const id = this.noticeId.value;
      if (!confirm("Are you sure you want to delete this notice?")) return;
      fetch(`php/admin/delete_notice.php?id=${encodeURIComponent(id)}`, {
        method: "GET",
      })
        .then((res) => res.json())
        .then((data) => {
          const msg = document.getElementById("noticeFormMsg");
          if (data.success) {
            msg.style.color = "green";
            msg.textContent = "Notice deleted!";
            setTimeout(() => {
              modal.style.display = "none";
              location.reload();
            }, 800);
          } else {
            msg.style.color = "red";
            msg.textContent = data.error || "Failed to delete notice.";
          }
        });
    };
  } else if (mode === "view") {
    html = `<div id="viewNoticeContent">Loading...</div>`;
    content.innerHTML = html;
    modal.style.display = "block";
    fetch(`php/admin/get_notice.php?id=${noticeId}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.notice) {
          document.getElementById("viewNoticeContent").innerHTML = `
            <h3>${escapeHTML(data.notice.title)}</h3>
            <p>${escapeHTML(data.notice.content)}</p>
            <div class="date">Posted on: ${formatDate(
              data.notice.created_at
            )}</div>
          `;
        } else {
          document.getElementById("viewNoticeContent").innerHTML =
            "Notice not found.";
        }
      });
  }
}

// Add CSS for additional animations
const styleSheet = document.createElement("style");
styleSheet.textContent = `
    .notice-card {
        transition: all 0.3s ease;
    }

    .retry-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: var(--border-radius-md);
        margin-top: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .retry-btn:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .animate-pulse {
        animation: pulse 1s infinite;
    }
`;
document.head.appendChild(styleSheet);
