document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-toggle');
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const sidebarLogo = document.getElementById('sidebar-logo');
    const navItems = document.querySelectorAll('.nav-item');
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    const accordionItems = document.querySelectorAll('.accordion-item');
    const userDropdown = document.getElementById('userDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // State Management
    let isMobile = window.innerWidth <= 992;
    let isAnimating = false;
    let clickLock = false;

    // Function to close all accordions except the one specified
    function closeOtherAccordions(currentAccordion) {
      document.querySelectorAll('.accordion-content.open').forEach(openContent => {
        if (!currentAccordion || openContent !== currentAccordion) {
          openContent.classList.remove('open');
          const accordionIcon = openContent.previousElementSibling?.querySelector('.accordion-icon');
          if (accordionIcon) {
            accordionIcon.classList.remove('open');
          }
        }
      });
    }

    // Initialize sidebar state
    function initializeSidebarState() {
      const shouldCollapse = localStorage.getItem('sidebarCollapsed') === 'true';
      sidebar.dataset.collapsed = shouldCollapse;
      document.documentElement.classList.toggle('sidebar-collapsed', shouldCollapse);
      void sidebar.offsetHeight;
    }

    // Update active nav item
    function updateActiveNavItem() {
      const pathParts = window.location.pathname.split('/');
      let currentPath = pathParts[pathParts.length - 1] || 'index.html';

      if (currentPath === '' || currentPath === 'index.html') {
        currentPath = 'adminNavigationDashboard.html';
      }

      navItems.forEach(item => {
        item.classList.remove('active');
        const href = item.getAttribute('href');
        const accordionItems = item.querySelectorAll('.accordion-item');

        if (accordionItems.length > 0) {
          accordionItems.forEach(accordionItem => {
            const accordionHref = accordionItem.getAttribute('href');
            accordionItem.classList.remove('active');
            if (accordionHref && accordionHref.includes(currentPath)) {
              item.classList.add('active');
              accordionItem.classList.add('active');
              const accordionContent = item.querySelector('.accordion-content');
              const accordionIcon = item.querySelector('.accordion-icon');
              if (accordionContent && accordionIcon) {
                accordionContent.classList.add('open');
                accordionIcon.classList.add('open');
              }
            }
          });
        } else if (href && href.includes(currentPath)) {
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

      if (!isCollapsed) {
        closeOtherAccordions();
      }

      setTimeout(() => {
        isAnimating = false;
        clickLock = false;
      }, 350);
    }

    // Expand sidebar for accordion
    function expandSidebarForAccordion() {
      if (sidebar.dataset.collapsed === 'true' && !isMobile) {
        document.documentElement.classList.add('no-transitions');

        sidebar.dataset.collapsed = 'false';
        document.documentElement.classList.remove('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');

        const accordionContent = this.nextElementSibling;
        const accordionIcon = this.querySelector('.accordion-icon');

        if (accordionContent && accordionIcon) {
          closeOtherAccordions();
          accordionContent.classList.add('open');
          accordionIcon.classList.add('open');
        }

        void sidebar.offsetWidth;
        setTimeout(() => {
          document.documentElement.classList.remove('no-transitions');
        }, 10);
      }
    }

    // Accordion toggle handler
    function setupAccordions() {
      accordionHeaders.forEach(header => {
        header.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          const accordionContent = this.nextElementSibling;
          const accordionIcon = this.querySelector('.accordion-icon');

          if (!accordionContent || !accordionIcon) {
            console.warn('Accordion content or icon not found for header:', this);
            return;
          }

          if (sidebar.dataset.collapsed === 'true' && !isMobile) {
            expandSidebarForAccordion.call(this);
            return;
          }

          if (accordionContent.classList.contains('open')) {
            accordionContent.classList.remove('open');
            accordionIcon.classList.remove('open');
            return;
          }

          closeOtherAccordions(accordionContent);
          accordionContent.classList.add('open');
          accordionIcon.classList.add('open');
        });
      });
    }

    // Navigation handler
    function handleNavItemClick(e) {
      if (clickLock) {
        e.preventDefault();
        return;
      }

      const href = this.getAttribute('href');
      if (!href || href === '#' || this.classList.contains('accordion-header')) {
        return;
      }

      if (this.getAttribute('target') === '_blank' || href.startsWith('http')) {
        return;
      }

      e.preventDefault();
      const isCollapsed = sidebar.dataset.collapsed === 'true';

      navItems.forEach(nav => {
        nav.classList.remove('active');
        const accordionItems = nav.querySelectorAll('.accordion-item');
        accordionItems.forEach(item => item.classList.remove('active'));
      });

      this.classList.add('active');
      const parentAccordion = this.closest('.accordion');
      if (parentAccordion) {
        parentAccordion.classList.add('active');
        const accordionContent = parentAccordion.querySelector('.accordion-content');
        const accordionIcon = parentAccordion.querySelector('.accordion-icon');
        if (accordionContent && accordionIcon) {
          accordionContent.classList.add('open');
          accordionIcon.classList.add('open');
        }
      }

      localStorage.setItem('sidebarCollapsed', isCollapsed);
      sessionStorage.setItem('sidebarCollapsed', isCollapsed);

      clickLock = true;
      document.documentElement.classList.add('no-transitions');
      setTimeout(() => {
        window.location.href = href;
      }, 50);
    }

    // Initialize components
    function initialize() {
      initializeSidebarState();
      updateDateTime();
      setInterval(updateDateTime, 60000);
      updateActiveNavItem();
      setupAccordions();

      const dropdowns = [].slice.call(document.querySelectorAll('.btn-user.dropdown-toggle'));
      dropdowns.forEach(function (el) {
        new bootstrap.Dropdown(el);
      });

      navItems.forEach(item => {
        const link = item.querySelector('a:not(.accordion-header)');
        const accordionItems = item.querySelectorAll('.accordion-item');

        if (link) {
          link.addEventListener('click', handleNavItemClick);
        }

        accordionItems.forEach(accordionItem => {
          accordionItem.addEventListener('click', handleNavItemClick);
        });
      });

      if (isMobile) {
        document.addEventListener('click', function (e) {
          if (userDropdown.contains(e.target)) {
            const rect = userDropdown.getBoundingClientRect();
            dropdownMenu.style.left = 'auto';
            dropdownMenu.style.right = '10px';
            dropdownMenu.style.top = `${rect.bottom}px`;
          }
        });
      }
    }

    // Event Listeners
    toggleSidebar.addEventListener('click', function (e) {
      e.stopPropagation();
      if (!isMobile && sidebar.dataset.collapsed === 'false') {
        toggleDesktopCollapse();
      }
    });

    sidebarLogo.addEventListener('click', function (e) {
      e.stopPropagation();
      if (!isMobile && sidebar.dataset.collapsed === 'true') {
        toggleDesktopCollapse();
      }
    });

    mobileToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      handleMobileMenu();
    });

    document.addEventListener('click', function (e) {
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

    window.addEventListener('resize', function () {
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

  // Setup tooltips for collapsed state
  function setupTooltips() {
    const navItems = document.querySelectorAll('.sidebar[data-collapsed="true"] .nav-item');

    navItems.forEach(item => {
      item.addEventListener('mouseenter', function () {
        if (sidebar.dataset.collapsed === 'true' && !item.classList.contains('accordion')) {
          // Handled by CSS
        }
      });

      item.addEventListener('mouseleave', function () {
        // Handled by CSS
      });
    });
  }