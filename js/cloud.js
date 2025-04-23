// Add click event listener to all navigation links
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        // Get click position
        const rect = link.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Create the cloud effect
        const cloud = document.createElement('div');
        cloud.classList.add('cloud');
        cloud.style.left = `${x}px`;
        cloud.style.top = `${y}px`;

        // Append to the link
        link.appendChild(cloud);

        // Remove the cloud after animation ends
        cloud.addEventListener('animationend', () => {
            cloud.remove();
        });
    });
});