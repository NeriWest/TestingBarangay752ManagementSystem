const accountBtn = document.querySelector(".account");
const floatingMenu = document.querySelector(".floating-menu");

accountBtn.addEventListener("click", function (event) {
    event.preventDefault();
    floatingMenu.classList.toggle("show");
    accountBtn.classList.toggle("active");
});

        /*side panel toggle*/
        const mainHead = document.querySelector(".main-head");
        const showcase = document.querySelector(".showcase");



        //RESIDENT
        const residentLink = document.querySelector("#resident-link");
        const residentDropdown = document.querySelector(".resident-dropdown");
        
        // Ensure dropdown toggles only when clicking the main RESIDENT link
        residentLink.addEventListener("click", function (event) {
            // Check if the clicked element is a dropdown item link
            if (event.target.closest(".dropdown-item a")) {
                return; // Allow the default link behavior (navigation)
            }
        
            // Otherwise, toggle the dropdown
            event.preventDefault();
            residentDropdown.classList.toggle("show");
            residentLink.classList.toggle("active");
        });

        // REQUEST
        const requestLink = document.querySelector("#request-link");
        const requestDropdown = document.querySelector(".request-dropdown");
        
        // Ensure dropdown toggles only when clicking the main REQUEST link
        requestLink.addEventListener("click", function (event) {
            // Check if the clicked element is a dropdown item link
            if (event.target.closest(".dropdown-item a")) {
                return; // Allow the default link behavior (navigation)
            }
        
            // Otherwise, toggle the dropdown
            event.preventDefault();
            requestDropdown.classList.toggle("show");
            requestLink.classList.toggle("active");
        });
        

        //REPORT
        const reportLink = document.querySelector("#report-link");
        const reportDropdown = document.querySelector(".report-dropdown");
        
        // Ensure dropdown toggles only when clicking the main REPORT link
        reportLink.addEventListener("click", function (event) {
            // Check if the clicked element is a dropdown item link
            if (event.target.closest(".dropdown-item a")) {
                return; // Allow the default link behavior (navigation)
            }
        
            // Otherwise, toggle the dropdown
            event.preventDefault();
            reportDropdown.classList.toggle("show");
            reportLink.classList.toggle("active");
        });
        
        //ACCOUNTS
        const accountsLink = document.querySelector("#accounts-link");
        const accountsDropdown = document.querySelector(".accounts-dropdown");
        
        // Ensure dropdown toggles only when clicking the main REPORT link
        accountsLink.addEventListener("click", function (event) {
            // Check if the clicked element is a dropdown item link
            if (event.target.closest(".dropdown-item a")) {
                return; // Allow the default link behavior (navigation)
            }
        
            // Otherwise, toggle the dropdown
            event.preventDefault();
            accountsDropdown.classList.toggle("show");
            accountsLink.classList.toggle("active");
        });


        // Hamburgerrrr
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const mainHead = document.querySelector('.main-head');
            const navIcons = document.querySelectorAll('.fa-user, .fa-print, .fa-flag'); // Target icons
            
            // Toggle sidebar collapse on hamburger click
            hamburger.addEventListener('click', function() {
                toggleSidebar();
            });
            
            // Uncollapse sidebar when clicking nav icons
            navIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    if (mainHead.classList.contains('collapsed')) {
                        toggleSidebar(); // Uncollapse if currently collapsed
                    }
                });
            });
            
            // Check localStorage for saved state on page load
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                toggleSidebar(false); // Apply stored state without toggling
            }
            
            // Helper function to toggle sidebar state
            function toggleSidebar(shouldStoreState = true) {
                mainHead.classList.toggle('collapsed');
                
                // Change the hamburger icon
                const icon = hamburger.querySelector('i');
                if (mainHead.classList.contains('collapsed')) {
                    icon.classList.replace('fa-bars', 'fa-times');
                } else {
                    icon.classList.replace('fa-times', 'fa-bars');
                }
                
                // Store the state in localStorage if needed
                if (shouldStoreState) {
                    localStorage.setItem('sidebarCollapsed', mainHead.classList.contains('collapsed'));
                }
            }
        });