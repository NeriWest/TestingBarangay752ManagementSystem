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