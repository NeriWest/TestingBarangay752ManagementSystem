lucide.createIcons();
AOS.init({ duration: 600, once: true });

const announcementsContainer = document.getElementById('announcements-container');
const hiddenAnnouncements = document.getElementById('hidden-announcements');
const viewAllBtn = document.getElementById('view-all-btn');
const popup = document.getElementById('announcement-popup');
const popupTitle = document.getElementById('popup-title');
const popupDate = document.getElementById('popup-date');
const popupDescription = document.getElementById('popup-description');
const popupType = document.getElementById('popup-type');
const popupTypeIcon = document.getElementById('popup-type-icon');
const popupCloseBtn = document.getElementById('popup-close-btn');
const closePopupBtn = document.getElementById('close-popup');

let isAnnouncementsLoaded = false;
let lastAnnouncementsHash = '';

function createAnnouncementCard(announcement, index) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';
    col.setAttribute('data-aos', 'fade-up');
    col.setAttribute('data-aos-delay', `${index * 100}`);

    const card = document.createElement('div');
    card.className = 'announcement-card';
    card.setAttribute('data-id', announcement.id);
    card.setAttribute('data-title', announcement.title);
    card.setAttribute('data-description', announcement.description);
    card.setAttribute('data-date', announcement.date);
    card.setAttribute('data-type', announcement.type);

    const image = document.createElement('img');

    const cardBody = document.createElement('div');
    cardBody.className = 'card-body';

    const badge = document.createElement('span');
    badge.className = 'announcement-badge';
    badge.innerHTML = `
        <svg class="type-icon">
            <use xlink:href="#${announcement.type}-icon"></use>
        </svg>
        <span>${capitalize(announcement.type)}</span>
    `;

    const title = document.createElement('h5');
    title.className = 'announcement-title';
    title.textContent = announcement.title;

    const date = document.createElement('span');
    date.className = 'announcement-date';
    date.innerHTML = `
        <i data-lucide="calendar" width="14" height="14"></i>
        ${announcement.date}
    `;

    const description = document.createElement('p');
    description.className = 'announcement-description';
    description.textContent = truncate(announcement.description, 100);

    const readMoreBtn = document.createElement('button');
    readMoreBtn.className = 'read-more-btn';
    readMoreBtn.textContent = 'Read More';

    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar';
    const progress = document.createElement('div');
    const daysOld = Math.min(30, Math.floor((new Date() - new Date(announcement.date)) / (1000 * 60 * 60 * 24)));
    progress.style.width = `${100 - (daysOld / 30) * 100}%`;
    progressBar.appendChild(progress);

    cardBody.appendChild(badge);
    cardBody.appendChild(title);
    cardBody.appendChild(date);
    cardBody.appendChild(description);
    cardBody.appendChild(readMoreBtn);
    cardBody.appendChild(progressBar);
    card.appendChild(image);
    card.appendChild(cardBody);
    col.appendChild(card);

    return col;
}

function truncate(str, maxLength) {
    return str.length > maxLength ? str.slice(0, maxLength) + '...' : str;
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function generateHash(data) {
    return JSON.stringify(data.map(item => item.id + item.date + item.title)).slice(0, 50);
}

function fetchAnnouncements(isInitialLoad = false) {
    if (isInitialLoad && isAnnouncementsLoaded) {
        console.log('fetchAnnouncements skipped: already loaded');
        return;
    }

    console.log('Fetching announcements...');
    fetch('indexAnnouncements.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error('Invalid data format: Expected an array');
            }

            const newHash = generateHash(data);
            if (newHash === lastAnnouncementsHash && !isInitialLoad) {
                console.log('No new announcements detected');
                return;
            }
            lastAnnouncementsHash = newHash;
            isAnnouncementsLoaded = true;

            console.log('Announcements fetched:', data.length);
            const fragmentMain = document.createDocumentFragment();
            const fragmentHidden = document.createDocumentFragment();

            data.forEach((announcement, index) => {
                if (!announcement.id || !announcement.title || !announcement.date) {
                    console.warn('Skipping invalid announcement:', announcement);
                    return;
                }
                const card = createAnnouncementCard(announcement, index);
                if (index < 3) {
                    fragmentMain.appendChild(card);
                } else {
                    fragmentHidden.appendChild(card);
                }
            });

            announcementsContainer.innerHTML = '';
            announcementsContainer.appendChild(fragmentMain);
            hiddenAnnouncements.innerHTML = '';
            hiddenAnnouncements.appendChild(fragmentHidden);

            lucide.createIcons();
            AOS.refresh();
            console.log('Announcements rendered');
        })
        .catch(error => {
            console.error('Error fetching announcements:', error);
            announcementsContainer.innerHTML = '<p class="text-danger">Failed to load announcements. Please try again later.</p>';
        });
}

[announcementsContainer, hiddenAnnouncements].forEach(container => {
    container.addEventListener('click', (e) => {
        const card = e.target.closest('.announcement-card');
        if (card) {
            popupTitle.textContent = card.dataset.title;
            popupDate.textContent = card.dataset.date;
            popupDescription.textContent = card.dataset.description;
            popupType.textContent = capitalize(card.dataset.type);
            popupTypeIcon.innerHTML = `<use xlink:href="#${card.dataset.type}-icon"></use>`;
            popup.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    });
});

viewAllBtn.addEventListener('click', function () {
    hiddenAnnouncements.classList.toggle('show');
    this.innerHTML = hiddenAnnouncements.classList.contains('show')
        ? 'Show Less <i data-lucide="chevron-up" class="chevron-down" width="16" height="16"></i>'
        : 'View All <i data-lucide="chevron-down" class="chevron-down" width="16" height="16"></i>';
    lucide.createIcons();
    AOS.refresh();
});

function closePopup() {
    popup.classList.remove('show');
    document.body.style.overflow = 'auto';
}

if (popupCloseBtn) {
    popupCloseBtn.addEventListener('click', closePopup);
}
if (closePopupBtn) {
    closePopupBtn.addEventListener('click', closePopup);
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && popup.classList.contains('show')) {
        closePopup();
    }
});

window.addEventListener('load', () => {
    console.log('Window fully loaded. Loading announcements...');
    fetchAnnouncements(true);
    setInterval(() => fetchAnnouncements(false), 30000);
});

document.addEventListener('DOMContentLoaded', function() {
    const councilMembers = [
        {
            initials: "ATQ",
            name: "ANTONIO T. QUIRANTE",
            position: "BARANGAY CAPTAIN",
            description: "Leading our barangay with dedication and vision since 2019. Focused on community development and resident welfare.",
            isCaptain: true,
            imageUrl: "img/Officials/Quirante.jpg"
        },
        {
            initials: "E'TGJ",
            name: "ERNESTO 'EJ' T. GABRIEL JR.",
            position: "BARANGAY SECRETARY",
            description: "Manages administrative tasks and ensures efficient record-keeping for all barangay matters and concerns.",
            imageUrl: "img/Officials/EJ.jpg"
        },
        {
            initials: "MFM",
            name: "MARIEL F. MONTENEGRO",
            position: "BARANGAY TREASURER",
            description: "Oversees the financial resources and ensures transparent management of barangay funds.",
            imageUrl: "img/Officials/Mariel.jpg"
        },
        {
            initials: "ATC",
            name: "ANDREW T. CAPARANGAN",
            position: "SK KAGAWAD",
            description: "Responsible for maintaining peace and security within the barangay through various programs and initiatives.",
            imageUrl: "img/Officials/Andy.jpg"
        },
        {
            initials: "AIM",
            name: "ANDRES I. MALLILLIN",
            position: "SK KAGAWAD",
            description: "Leads health programs and ensures proper sanitation practices throughout the barangay.",
            imageUrl: "img/Barangay Logo.png"
        },
        {
            initials: "RAR",
            name: "ROBERTA A. RIVERA",
            position: "SK KAGAWAD",
            description: "Spearheads educational initiatives and scholarship programs for barangay residents.",
            imageUrl: "img/Barangay Logo.png"
        },
        {
            initials: "ALSM",
            name: "ANA LIZA S. MONTEMAYOR",
            position: "SK KAGAWAD",
            description: "Oversees infrastructure projects and maintenance of public facilities within the barangay.",
            imageUrl: "img/Officials/LIZA.jpg"
        },
        {
            initials: "CSG",
            name: "CECILIA S. GUINTO",
            position: "SK KAGAWAD",
            description: "Represents the youth sector and implements programs catering to the needs of young residents.",
            imageUrl: "img/Officials/CECILE.jpg"
        },
        {
            initials: "BSF",
            name: "BENJAMIN S. FRANCISCO",
            position: "SK KAGAWAD",
            description: "Assists the SK Chairperson in managing youth programs and activities.",
            imageUrl: "img/Barangay Logo.png"
        },
        {
            initials: "RBDL",
            name: "REDENTOR B. DE LEON",
            position: "SK KAGAWAD",
            description: "Responsible for the financial management of SK projects and initiatives.",
            imageUrl: "img/Barangay Logo.png"
        },
        {
            initials: "CMAO",
            name: "CHARLENE MAE A. OBEJERO",
            position: "SK CHAIRPERSON",
            description: "Leads the Sangguniang Kabataan in implementing youth development programs and activities.",
            imageUrl: "img/Barangay Logo.png"
        },
        {
            initials: "PCD",
            name: "PAULINE C. DAGOHOY",
            position: "DAYCARE TEACHER",
            description: "Provides early childhood education and care for the youngest members of our community.",
            imageUrl: "img/Barangay Logo.png"
        }
    ];

    const councilGrid = document.getElementById('councilGrid');
    let isGridInitialized = false;

    function initGrid() {
        if (isGridInitialized) return;
        isGridInitialized = true;

        councilGrid.innerHTML = '';
        councilMembers.forEach((member, index) => {
            const memberElement = document.createElement('div');
            memberElement.className = `council-card ${member.isCaptain ? 'captain-card' : ''} animate__animated animate__fadeInUp`;
            memberElement.style.animationDelay = `${index * 0.1}s`;
            const imageContent = member.imageUrl 
                ? `<img src="${member.imageUrl}" alt="${member.name}" class="img-fluid">`
                : `<div class="card-initials">${member.initials}</div>
                   <img src="https://source.unsplash.com/random/600x600/?portrait,official,${index}" alt="Portrait" class="img-fluid">`;
            memberElement.innerHTML = `
                ${member.isCaptain ? '<span class="role-badge">Head Official</span>' : ''}
                <div class="card-image">
                    ${imageContent}
                </div>
                <div class="card-info">
                    <h3 class="card-name">${member.name}</h3>
                    <p class="card-position">${member.position}</p>
                    <p class="card-description">${member.description}</p>
                </div>
            `;
            councilGrid.appendChild(memberElement);
        });
    }

    initGrid();
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.council-card').forEach(card => {
        observer.observe(card);
    });
});

document.getElementById('copyrightYear').textContent = new Date().getFullYear();