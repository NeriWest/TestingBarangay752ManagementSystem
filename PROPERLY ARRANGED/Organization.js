document.addEventListener('DOMContentLoaded', function() {
    const councilMembers = [
        {
            initials: "ATQ",
            name: "ANTONIO T. QUIRANTE",
            position: "BARANGAY CAPTAIN",
            description: "Leading our barangay with dedication and vision since 2019. Focused on community development and resident welfare.",
            isCaptain: true,
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "E'TGJ",
            name: "ERNESTO 'EJ' T. GABRIEL JR.",
            position: "BARANGAY SECRETARY",
            description: "Manages administrative tasks and ensures efficient record-keeping for all barangay matters and concerns.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "MFM",
            name: "MARIEL F. MONTENEGRO",
            position: "BARANGAY TREASURER",
            description: "Oversees the financial resources and ensures transparent management of barangay funds.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "ATC",
            name: "ANDRES T. CAPARANGAN",
            position: "SK KAGAWAD",
            description: "Responsible for maintaining peace and security within the barangay through various programs and initiatives.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "AIM",
            name: "ANDRES I. MALLILLIN",
            position: "SK KAGAWAD",
            description: "Leads health programs and ensures proper sanitation practices throughout the barangay.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "RAR",
            name: "ROBERTA A. RIVERA",
            position: "SK KAGAWAD",
            description: "Spearheads educational initiatives and scholarship programs for barangay residents.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "ALSM",
            name: "ANA LIZA S. MONTEMAYOR",
            position: "SK KAGAWAD",
            description: "Oversees infrastructure projects and maintenance of public facilities within the barangay.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "CSG",
            name: "CECILIA S. GUINTO",
            position: "SK KAGAWAD",
            description: "Represents the youth sector and implements programs catering to the needs of young residents.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "BSF",
            name: "BENJAMIN S. FRANCISCO",
            position: "SK KAGAWAD",
            description: "Assists the SK Chairperson in managing youth programs and activities.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "RBDL",
            name: "REDENTOR B. DE LEON",
            position: "SK KAGAWAD",
            description: "Responsible for the financial management of SK projects and initiatives.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "CMAO",
            name: "CHARLENE MAE A. OBEJERO",
            position: "SK CHAIRPERSON",
            description: "Leads the Sangguniang Kabataan in implementing youth development programs and activities.",
            imageUrl: "../img/Barangay Logo.png"
        },
        {
            initials: "PCD",
            name: "PAULINE C. DAGOHOY",
            position: "DAYCARE TEACHER",
            description: "Provides early childhood education and care for the youngest members of our community.",
            imageUrl: "../img/Barangay Logo.png"
        }
    ];

    const councilGrid = document.getElementById('councilGrid');

    // Initialize grid with animation
    function initGrid() {
        councilGrid.innerHTML = '';
        
        councilMembers.forEach((member, index) => {
            const memberElement = document.createElement('div');
            memberElement.className = `council-card ${member.isCaptain ? 'captain-card' : ''} animate__animated animate__fadeInUp`;
            memberElement.style.animationDelay = `${index * 0.1}s`;
            
            // Check if image URL is provided
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

    // Initialize
    initGrid();
    
    // Add scroll animation
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