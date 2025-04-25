/*==================================ANNOUNCEMENT CONTAINER========================================*/ 
const announcements = [
    {
        id: 1,
        title: "Barangay Clean-up Drive",
        date: "June 15, 2025",
        description: "Join us for our monthly community clean-up drive this Saturday from 7:00 AM to 10:00 AM. We will be focusing on the main streets and public areas. Please bring gloves and wear appropriate clothing. Cleaning materials will be provided by the Barangay. This is part of our ongoing effort to maintain cleanliness and prevent dengue in our community. Refreshments will be served after the activity.",
        type: "event"
    },
    {
        id: 2,
        title: "COVID-19 Vaccination Schedule",
        date: "June 10, 2025",
        description: "The next COVID-19 vaccination schedule for our Barangay will be on June 20-21, 2023, from 8:00 AM to 3:00 PM at the Barangay Hall. First dose, second dose, and booster shots will be available. Please bring a valid ID and your vaccination card (for second dose and booster). Pre-registration is encouraged but walk-ins will also be accommodated.",
        type: "notice"
    },
    {
        id: 3,
        title: "Flood Warning Advisory",
        date: "June 5, 2025",
        description: "Due to the expected heavy rainfall in the coming days, residents in low-lying areas are advised to take necessary precautions. The Barangay has prepared evacuation centers and emergency response teams are on standby. Please secure your belongings, prepare emergency kits, and stay updated with the latest weather announcements. Contact our hotline for any emergencies.",
        type: "alert"
    },
    {
        id: 4,
        title: "Free Medical Check-up",
        date: "May 28, 2025",
        description: "A free medical check-up will be conducted on June 25, 2023, from 9:00 AM to 3:00 PM at the Barangay covered court. Services include general check-up, blood pressure monitoring, blood sugar testing, and basic dental services. This is open to all residents of Barangay 752 Zone-81. Please bring your Barangay ID or any proof of residency.",
        type: "event"
    },
    {
        id: 5,
        title: "Barangay ID Renewal",
        date: "May 20, 2025",
        description: "Residents with Barangay IDs issued before 2020 are encouraged to renew their IDs. The renewal process will be from June 1-30, 2023, during office hours (Monday to Friday, 8:00 AM to 5:00 PM). Please bring your old Barangay ID, a valid government ID, and proof of residency. The renewal fee is waived for senior citizens and persons with disabilities.",
        type: "notice"
    },
    {
        id: 6,
        title: "Water Service Interruption",
        date: "May 15, 2025",
        description: "Please be advised that there will be a scheduled water service interruption on June 18, 2023, from 10:00 PM to 5:00 AM the following day. This is due to maintenance work on the main water lines. Affected areas include all streets within Barangay 752 Zone-81. Please store enough water for your needs during this period.",
        type: "alert"
    }
];

// DOM elements
const announcementsContainer = document.getElementById('announcements-container');
const popup = document.getElementById('announcement-popup');
const popupTitle = document.getElementById('popup-title-text');
const popupDate = document.getElementById('popup-date');
const popupDescription = document.getElementById('popup-description');
const popupIcon = document.getElementById('popup-icon');
const closePopupBtn = document.getElementById('close-popup-btn');
const closePopupX = document.getElementById('close-popup');

// Render announcements
function renderAnnouncements() {
    announcementsContainer.innerHTML = '';
    
    announcements.forEach(announcement => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        
        const card = document.createElement('div');
        card.className = 'card announcement-card';
        card.addEventListener('click', () => openPopup(announcement));
        
        // Card header
        const cardHeader = document.createElement('div');
        cardHeader.className = 'card-header bg-white';
        
        const headerContent = document.createElement('div');
        headerContent.className = 'd-flex justify-content-between align-items-center';
        
        const titleDiv = document.createElement('div');
        titleDiv.className = 'd-flex align-items-center';
        
        // Add icon based on type
        const icon = document.createElement('i');
        if (announcement.type === 'event') {
            icon.className = 'fas fa-calendar-alt event-icon me-2';
        } else if (announcement.type === 'notice') {
            icon.className = 'fas fa-bell notice-icon me-2';
        } else {
            icon.className = 'fas fa-exclamation-circle alert-icon me-2';
        }
        
        const title = document.createElement('h5');
        title.className = 'card-title mb-0';
        title.textContent = announcement.title;
        
        titleDiv.appendChild(icon);
        titleDiv.appendChild(title);
        
        const chevron = document.createElement('i');
        chevron.className = 'fas fa-chevron-right text-muted';
        
        headerContent.appendChild(titleDiv);
        headerContent.appendChild(chevron);
        
        const date = document.createElement('p');
        date.className = 'card-description mt-2 mb-0';
        date.textContent = announcement.date;
        
        cardHeader.appendChild(headerContent);
        cardHeader.appendChild(date);
        
        // Card body
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';
        
        const content = document.createElement('p');
        content.className = 'card-content';
        content.textContent = announcement.description;
        
        cardBody.appendChild(content);
        
        // Card footer
        const cardFooter = document.createElement('div');
        cardFooter.className = 'card-footer bg-white border-0';
        
        const readMore = document.createElement('button');
        readMore.className = 'read-more-btn';
        readMore.textContent = 'Read more';
        
        cardFooter.appendChild(readMore);
        
        // Assemble card
        card.appendChild(cardHeader);
        card.appendChild(cardBody);
        card.appendChild(cardFooter);
        
        col.appendChild(card);
        announcementsContainer.appendChild(col);
    });
}

// Open popup
function openPopup(announcement) {
    popupTitle.textContent = announcement.title;
    popupDate.textContent = announcement.date;
    popupDescription.textContent = announcement.description;
    
    // Set icon based on type
    popupIcon.className = 'popup-icon';
    if (announcement.type === 'event') {
        popupIcon.classList.add('fas', 'fa-calendar-alt', 'event-icon');
    } else if (announcement.type === 'notice') {
        popupIcon.classList.add('fas', 'fa-bell', 'notice-icon');
    } else {
        popupIcon.classList.add('fas', 'fa-exclamation-circle', 'alert-icon');
    }
    
    popup.classList.add('show');
}

// Close popup
function closePopup() {
    popup.classList.remove('show');
}

// Close popup when clicking outside
popup.addEventListener('click', (e) => {
    if (e.target === popup) {
        closePopup();
    }
});

// Event listeners for close buttons
closePopupBtn.addEventListener('click', closePopup);
closePopupX.addEventListener('click', closePopup);

// Initialize
document.addEventListener('DOMContentLoaded', renderAnnouncements);





/*==================================MOBILE VIEW========================================*/ 
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
        const targetId = link.getAttribute('href');

        // Check if the link is an internal section (starts with #)
        if (targetId.startsWith("#")) {
            e.preventDefault(); // Prevent default behavior only for in-page navigation
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        } else {
            // Allow normal navigation for external links (like indexBoostrap.html)
            window.location.href = targetId;
        }

        // Close navbar if open (for mobile)
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse.classList.contains('show')) {
            navbarCollapse.classList.remove('show');
            document.querySelector('.navbar-toggler').setAttribute('aria-expanded', 'false');
        }
    });
});

// Hamburger Toggle Animation
document.querySelector('.navbar-toggler').addEventListener('click', function () {
    document.querySelector('.ham').classList.toggle('active');
});

/*==================================SIDE PANEL========================================*/ 
const navbarToggler = document.querySelector(".navbar-toggler");
const sidePanel = document.querySelector(".side-panel");
const overlay = document.querySelector(".overlay");
const ham = document.querySelector(".ham");
const closeBtn = document.querySelector(".close-btn");

navbarToggler.addEventListener("click", () => {
    sidePanel.classList.toggle("active");
    overlay.classList.toggle("active");
    ham.classList.toggle("active");
});

overlay.addEventListener("click", () => {
    sidePanel.classList.remove("active");
    overlay.classList.remove("active");
    ham.classList.remove("active");
});

closeBtn.addEventListener("click", () => {
    sidePanel.classList.remove("active");
    overlay.classList.remove("active");
    ham.classList.remove("active");
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 991.98) {
        sidePanel.classList.remove("active");
        overlay.classList.remove("active");
        ham.classList.remove("active");
    }
});
