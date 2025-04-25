/*==================================SCROLLING FUNCTIONALITIES========================================*/ 

let isScrolling = false; // Track if the user is scrolling
let visibleSections = new Set(); // Track visible sections

function checkVisibility() {
  const sections = document.querySelectorAll('section');
  const windowHeight = window.innerHeight;

  sections.forEach(section => {
    const sectionTop = section.getBoundingClientRect().top;
    const sectionBottom = section.getBoundingClientRect().bottom;

    // Check if section is in viewport
    if (sectionTop < windowHeight * 0.8 && sectionBottom > 100) {
      if (!visibleSections.has(section)) {
        section.classList.add('visible');
        visibleSections.add(section);
      }
    }
  });

  isScrolling = false; // Reset scroll flag
}

// Smooth scroll animation handling
function handleScroll() {
  if (!isScrolling) {
    isScrolling = true;
    requestAnimationFrame(() => {
      checkVisibility();
      isScrolling = false;
    });
  }
}

// ✅ FIXED: Prevent unwanted scrolling on reload
window.addEventListener('load', () => {
  if (performance.navigation.type !== 1) {  // Type 1 means reload
    window.scrollTo({ top: 0, behavior: 'instant' }); 
  }

  let introSection = document.querySelector('.intro-section');
  if (introSection) {
    setTimeout(() => {
      introSection.classList.add('visible');
    }, 300);
  }

  checkVisibility(); // Ensure sections are checked after loading
});

// Run on scroll
document.addEventListener('scroll', handleScroll);

/*==================================BACK TO TOP========================================*/

const backToTopButton = document.getElementById("backToTop");

window.addEventListener("scroll", () => {
  if (window.scrollY > 200) {
    backToTopButton.classList.add("show");
  } else {
    backToTopButton.classList.remove("show");
  }
});

backToTopButton.addEventListener("click", (e) => {
  e.preventDefault();

  let currentPosition = window.scrollY;
  let scrollDuration = 500;
  let startTime = null;

  function smoothScroll(timestamp) {
    if (!startTime) startTime = timestamp;

    let elapsedTime = timestamp - startTime;
    let progress = Math.min(elapsedTime / scrollDuration, 1);

    window.scrollTo(0, currentPosition * (1 - progress));

    if (progress < 1) {
      requestAnimationFrame(smoothScroll);
    }
  }

  requestAnimationFrame(smoothScroll);
});

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

/*==================================FIX HERO SECTION BACKGROUND ISSUE========================================*/
document.addEventListener("DOMContentLoaded", function () {
  let infoSection = document.querySelector('#info-section');

  if (infoSection) {
    setTimeout(() => {
      infoSection.classList.add('visible'); 
    }, 300);
  }
});

/*==================================ENSURE POP-UP DOESN'T VANISH WHEN SCROLLING UP========================================*/
window.addEventListener("scroll", function () {
  document.querySelectorAll(".visible").forEach((section) => {
    if (!section.classList.contains("always-visible")) {
      section.classList.add("always-visible");
    }
  });
});

/*==================================CAROUSEL========================================*/
document.addEventListener('DOMContentLoaded', function() {
  // Image carousel functionality
  const images = [
      "img/AboutUsCarousel1.jpg",
      "img/AboutUsCarousel2.jpg",
      "img/AboutUsCarousel3.jpg",
      "img/AboutUsCarousel4.jpg"
  ];
  
  const carousel = document.getElementById('imageCarousel');
  let currentImageIndex = 0;
  let isHovering = false;
  let interval;
  
  // Create image elements and controls
  images.forEach((image, index) => {
      const imgElement = document.createElement('img');
      imgElement.src = image;
      imgElement.alt = `Community Image ${index + 1}`;
      imgElement.className = 'carousel-image';
      if (index === 0) imgElement.classList.add('active');
      carousel.insertBefore(imgElement, carousel.firstChild);
  });
  
  const controlsContainer = document.createElement('div');
  controlsContainer.className = 'carousel-controls';
  
  images.forEach((_, index) => {
      const control = document.createElement('button');
      control.className = 'carousel-control';
      if (index === 0) control.classList.add('active');
      control.addEventListener('click', () => {
          currentImageIndex = index;
          updateCarousel();
      });
      controlsContainer.appendChild(control);
  });
  
  carousel.appendChild(controlsContainer);
  
  // Carousel functions
  function updateCarousel() {
      const images = document.querySelectorAll('.carousel-image');
      const controls = document.querySelectorAll('.carousel-control');
      
      images.forEach((img, idx) => {
          if (idx === currentImageIndex) {
              img.classList.add('active');
          } else {
              img.classList.remove('active');
          }
      });
      
      controls.forEach((ctrl, idx) => {
          if (idx === currentImageIndex) {
              ctrl.classList.add('active');
          } else {
              ctrl.classList.remove('active');
          }
      });
  }
  
  function nextImage() {
      currentImageIndex = (currentImageIndex + 1) % images.length;
      updateCarousel();
  }
  
  function startCarousel() {
      if (!isHovering) {
          interval = setInterval(nextImage, 4000);
      }
  }
  
  // Event listeners
  carousel.addEventListener('mouseenter', () => {
      isHovering = true;
      clearInterval(interval);
  });
  
  carousel.addEventListener('mouseleave', () => {
      isHovering = false;
      startCarousel();
  });
  
  // Initialize
  startCarousel();
});
