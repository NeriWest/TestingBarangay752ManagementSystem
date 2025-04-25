document.addEventListener('DOMContentLoaded', function() {
  // DOM Elements
  const sidebar = document.getElementById('sidebar');
  const mobileToggle = document.getElementById('mobile-toggle');
  const toggleSidebar = document.getElementById('toggle-sidebar');
  const sidebarLogo = document.getElementById('sidebar-logo');
  const navItems = document.querySelectorAll('.nav-item');

  // State Management
  let isMobile = window.innerWidth <= 992;
  let isAnimating = false;
  let clickLock = false;

  // Initialize sidebar state
  function initializeSidebarState() {
    const shouldCollapse = localStorage.getItem('sidebarCollapsed') === 'true';
    sidebar.dataset.collapsed = shouldCollapse;
    document.documentElement.classList.toggle('sidebar-collapsed', shouldCollapse);
    // Force reflow
    void sidebar.offsetHeight;
  }

  // Update active nav item
  function updateActiveNavItem() {
    const pathParts = window.location.pathname.split('/');
    let currentPath = pathParts[pathParts.length - 1] || 'index.html';
    
    if (currentPath === '' || currentPath === 'index.html') {
      currentPath = 'residentBootstrapDashboard.html';
    }

    navItems.forEach(item => {
      item.classList.remove('active');
      const hrefParts = item.getAttribute('href').split('/');
      const href = hrefParts[hrefParts.length - 1] || 'index.html';
      
      if (href === currentPath) {
        item.classList.add('active');
      }
    });
  }

  // Date/Time Updater
  function updateDateTime() {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const now = new Date();
    
    document.querySelector('.day').textContent = days[now.getDay()];
    document.querySelector('.date').textContent = now.toLocaleDateString();
    document.querySelector('.time').textContent = now.toLocaleTimeString([], {
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  // Mobile menu handler
  function handleMobileMenu(forceClose = false) {
    if (!isMobile) return;
    
    if (forceClose) {
      sidebar.classList.remove('show-mobile');
      document.body.classList.remove('sidebar-open');
      return;
    }
    
    sidebar.classList.toggle('show-mobile');
    document.body.classList.toggle('sidebar-open');
  }

  // Desktop collapse handler
  function toggleDesktopCollapse() {
    if (isMobile || isAnimating || clickLock) return;
    
    isAnimating = true;
    clickLock = true;
    
    const isCollapsed = sidebar.dataset.collapsed === 'true';
    sidebar.dataset.collapsed = !isCollapsed;
    document.documentElement.classList.toggle('sidebar-collapsed', !isCollapsed);
    localStorage.setItem('sidebarCollapsed', !isCollapsed);
    
    setTimeout(() => {
      isAnimating = false;
      clickLock = false;
    }, 350); // Slightly longer than CSS transition
  }

  // Navigation handler
  function handleNavItemClick(e) {
    if (clickLock) {
      e.preventDefault();
      return;
    }
    
    if (this.getAttribute('target') === '_blank' || this.getAttribute('href').startsWith('http')) {
      return;
    }
    
    e.preventDefault();
    const path = this.getAttribute('href');
    const isCollapsed = sidebar.dataset.collapsed === 'true';

    // Update active state
    navItems.forEach(nav => nav.classList.remove('active'));
    this.classList.add('active');

    // Persist state
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    sessionStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // Navigate
    clickLock = true;
    document.documentElement.classList.add('no-transitions');
    setTimeout(() => {
      window.location.href = path;
    }, 50);
  }

  // Initialize components
  function initialize() {
    initializeSidebarState();
    updateDateTime();
    setInterval(updateDateTime, 60000);
    updateActiveNavItem();
    
    // Initialize Bootstrap dropdowns
    const dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdowns.forEach(function(el) {
      new bootstrap.Dropdown(el);
    });

    // Add click event listeners
    navItems.forEach(item => {
      item.addEventListener('click', handleNavItemClick);
    });
  }

  // Event Listeners
  toggleSidebar.addEventListener('click', function(e) {
    e.stopPropagation();
    if (!isMobile && sidebar.dataset.collapsed === 'false') {
      toggleDesktopCollapse();
    }
  });

  sidebarLogo.addEventListener('click', function(e) {
    e.stopPropagation();
    if (!isMobile && sidebar.dataset.collapsed === 'true') {
      toggleDesktopCollapse();
    }
  });

  mobileToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    handleMobileMenu();
  });

  document.addEventListener('click', function(e) {
    if (
      isMobile && 
      !sidebar.contains(e.target) && 
      e.target !== mobileToggle && 
      !mobileToggle.contains(e.target) && 
      sidebar.classList.contains('show-mobile')
    ) {
      handleMobileMenu(true);
    }
  });

  window.addEventListener('resize', function() {
    const newIsMobile = window.innerWidth <= 992;
    if (newIsMobile !== isMobile) {
      isMobile = newIsMobile;
      if (!isMobile) {
        sidebar.classList.remove('show-mobile');
        document.body.classList.remove('sidebar-open');
      }
    }
  });

  initialize();
}); 

// Add this to your existing JavaScript
function setupTooltips() {
  const navItems = document.querySelectorAll('.sidebar[data-collapsed="true"] .nav-item');
  
  navItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      if (sidebar.dataset.collapsed === 'true') {
        // This is handled by CSS, but we can add additional logic if needed
      }
    });
    
    item.addEventListener('mouseleave', function() {
      // This is handled by CSS
    });
  });
}

// Call this function in your initialize() function
function initialize() {
  initializeSidebarState();
  updateDateTime();
  setInterval(updateDateTime, 60000);
  updateActiveNavItem();
  setupTooltips(); // Add this line
  
  // ... rest of your existing initialize code ...
}