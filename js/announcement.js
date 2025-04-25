document.addEventListener("DOMContentLoaded", function () {
    const announcementBtn = document.getElementById("announcementBtn"); // Floating button
    const announcementPanel = document.getElementById("announcementPanel"); // Popup panel
    const closeAnnouncement = document.getElementById("closeAnnouncement"); // Close button
    const overlay = document.getElementById("overlay"); // Background overlay
    const navAnnouncement = document.getElementById("navAnnouncement"); // Navbar Announcement Link
    const footer = document.querySelector(".footer"); // Select footer by class

    function openAnnouncementPanel() {
        announcementPanel.classList.add("active"); // Open panel
        overlay.classList.add("active"); // Show overlay
    }

    function closeAnnouncementPanel() {
        announcementPanel.classList.remove("active"); // Close panel
        overlay.classList.remove("active"); // Hide overlay
    }

    // Click floating button
    if (announcementBtn) {
        announcementBtn.addEventListener("click", openAnnouncementPanel);
    }

    // Click navbar "Announcement" link
    if (navAnnouncement) {
        navAnnouncement.addEventListener("click", function (e) {
            e.preventDefault(); // Prevent page reload
            openAnnouncementPanel();
        });
    }

    // Close panel when clicking close button
    if (closeAnnouncement) {
        closeAnnouncement.addEventListener("click", closeAnnouncementPanel);
    }

    // Close panel when clicking outside (overlay)
    if (overlay) {
        overlay.addEventListener("click", closeAnnouncementPanel);
    }

    // Function to hide/show the announcement button when the footer is in view
    function toggleAnnouncementButton() {
        if (!announcementBtn || !footer) return;

        const footerRect = footer.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (footerRect.top < windowHeight) {
            announcementBtn.style.opacity = "0";
            announcementBtn.style.pointerEvents = "none";
        } else {
            announcementBtn.style.opacity = "1";
            announcementBtn.style.pointerEvents = "auto";
        }
    }

    // Run on scroll and resize
    window.addEventListener("scroll", toggleAnnouncementButton);
    window.addEventListener("resize", toggleAnnouncementButton);

    // Run once on page load
    toggleAnnouncementButton();
});



/* Smooth Scrolling */

document.addEventListener("DOMContentLoaded", function () {
    const aboutLink = document.getElementById("navAbout"); // Navbar About Us link
    const learnMoreBtn = document.getElementById("learnMoreBtn"); // Learn More button
    const aboutSection = document.getElementById("aboutUs"); // About Us section

    function smoothScroll(target) {
        if (!target) return;

        const targetPosition = target.offsetTop;
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        const duration = 400; // Duration in ms
        let start = null;

        function animation(currentTime) {
            if (start === null) start = currentTime;
            const elapsedTime = currentTime - start;
            const easeInOutQuad = elapsedTime / duration * (2 - elapsedTime / duration);
            window.scrollTo(0, startPosition + distance * easeInOutQuad);

            if (elapsedTime < duration) {
                requestAnimationFrame(animation);
            }
        }

        requestAnimationFrame(animation);
    }

    // Event listeners for About Us link and Learn More button
    if (aboutLink) {
        aboutLink.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default jump
            smoothScroll(aboutSection);
        });
    }

    if (learnMoreBtn) {
        learnMoreBtn.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default action
            smoothScroll(aboutSection);
        });
    }
});


// Function to highlight the active navigation link
function highlightActiveNavLink() {
    // Get the current page URL
    const currentPageUrl = window.location.href;

    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav-link');

    // Loop through the links and add the active class to the current page link
    navLinks.forEach(link => {
        // Check if the link's href matches the current page URL
        if (link.href === currentPageUrl) {
            link.classList.add('active');
        }
    });
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', highlightActiveNavLink);