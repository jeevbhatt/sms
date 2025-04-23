document.addEventListener('DOMContentLoaded', function() {
    // Initialize notices
    initNotices();
    
    // Page transition effect
    initPageTransitions();
    
    // Header scroll effect
    initHeaderScroll();
});

// Header scroll effect
function initHeaderScroll() {
    const header = document.querySelector('.head');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

// Notices functionality with improved animations
function initNotices() {
    const noticesContainer = document.getElementById('notices-list');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    let offset = 0;
    const limit = 5;
    let isLoading = false;
    let hasMoreNotices = true;
    
    // Initial load with animation
    setTimeout(() => {
        loadNotices();
    }, 300);
    
    // Scroll event for infinite loading with improved detection
    window.addEventListener('scroll', () => {
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
        loadingSpinner.style.display = 'block';
        
        // Add loading animation
        loadingSpinner.classList.add('animate-pulse');
        
        fetch(`php/fetch_notices.php?offset=${offset}&limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                loadingSpinner.classList.remove('animate-pulse');
                
                if (data.notices && data.notices.length > 0) {
                    renderNotices(data.notices);
                    offset += data.notices.length;
                    
                    // Check if we've reached the end
                    if (data.notices.length < limit) {
                        hasMoreNotices = false;
                        
                        // Show end of notices message
                        if (offset > 0) {
                            const endMessage = document.createElement('div');
                            endMessage.className = 'end-message';
                            endMessage.textContent = 'You\'ve reached the end of notices';
                            endMessage.style.textAlign = 'center';
                            endMessage.style.padding = '20px';
                            endMessage.style.color = 'var(--gray-600)';
                            endMessage.style.fontStyle = 'italic';
                            endMessage.style.opacity = '0';
                            endMessage.style.transform = 'translateY(20px)';
                            endMessage.style.transition = 'all 0.5s ease';
                            
                            noticesContainer.appendChild(endMessage);
                            
                            // Trigger animation
                            setTimeout(() => {
                                endMessage.style.opacity = '1';
                                endMessage.style.transform = 'translateY(0)';
                            }, 100);
                        }
                    }
                } else {
                    hasMoreNotices = false;
                    
                    if (offset === 0) {
                        noticesContainer.innerHTML = `
                            <div class="no-notices" style="text-align: center; padding: 50px 20px;">
                                <div style="font-size: 50px; color: var(--gray-400); margin-bottom: 20px;">📝</div>
                                <h3>No notices available</h3>
                                <p style="color: var(--gray-600);">Check back later for updates</p>
                            </div>
                        `;
                    }
                }
                
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading notices:', error);
                loadingSpinner.style.display = 'none';
                loadingSpinner.classList.remove('animate-pulse');
                
                const errorMessage = document.createElement('div');
                errorMessage.className = 'error-message';
                errorMessage.innerHTML = `
                    <p>Failed to load notices. Please try again later.</p>
                    <button onclick="location.reload()" class="retry-btn">Retry</button>
                `;
                errorMessage.style.textAlign = 'center';
                errorMessage.style.padding = '20px';
                errorMessage.style.margin = '20px 0';
                errorMessage.style.backgroundColor = 'rgba(249, 65, 68, 0.1)';
                errorMessage.style.color = 'var(--danger-color)';
                errorMessage.style.borderRadius = 'var(--border-radius-md)';
                
                noticesContainer.appendChild(errorMessage);
                isLoading = false;
            });
    }
    
    function renderNotices(notices) {
        notices.forEach((notice, index) => {
            const noticeCard = document.createElement('div');
            noticeCard.className = 'notice-card';
            noticeCard.innerHTML = `
                <h3>${escapeHTML(notice.title)}</h3>
                <p>${escapeHTML(notice.content)}</p>
                <div class="date">Posted on: ${formatDate(notice.created_at)}</div>
            `;
            
            // Set initial state for animation
            noticeCard.style.opacity = '0';
            noticeCard.style.transform = 'translateY(30px)';
            
            noticesContainer.appendChild(noticeCard);
            
            // Trigger animation with staggered delay
            setTimeout(() => {
                noticeCard.style.transition = 'all 0.5s ease';
                noticeCard.style.opacity = '1';
                noticeCard.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    }
    
    function escapeHTML(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }
}

// Page transitions
function initPageTransitions() {
    document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        
        if (href && !href.startsWith('#') && !href.startsWith('http')) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.body.classList.add('fade-out');
                
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            });
        }
    });
    
    // Add fade-in effect when page loads
    window.addEventListener('load', () => {
        document.body.classList.add('fade-in');
    });
}

// Add CSS for additional animations
const styleSheet = document.createElement('style');
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