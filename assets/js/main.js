/**
* Template Name: Presento
* Template URL: https://bootstrapmade.com/presento-bootstrap-corporate-template/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }
  window.addEventListener("load", initSwiper);

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init isotope layout and filters
   */
  document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

    let initIsotope;
    imagesLoaded(isotopeItem.querySelector('.isotope-container'), function() {
      initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
        itemSelector: '.isotope-item',
        layoutMode: layout,
        filter: filter,
        sortBy: sort
      });
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filters) {
      filters.addEventListener('click', function() {
        isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
        this.classList.add('filter-active');
        initIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        if (typeof aosInit === 'function') {
          aosInit();
        }
      }, false);
    });

  });

  /**
   * Frequently Asked Questions Toggle
   */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

})();

function toggleFilterBox() {
  const filterBox = document.querySelector('.filter-box');
  filterBox.classList.toggle('collapsed');
}

function togglePasswordVisibility(fieldId) {
  const passwordField = document.getElementById(fieldId);
  const icon = passwordField.nextElementSibling.querySelector('i');
  if (passwordField.type === 'password') {
  passwordField.type = 'text';
  icon.classList.remove('bi-eye');
  icon.classList.add('bi-eye-slash');
  } else {
  passwordField.type = 'password';
  icon.classList.remove('bi-eye-slash');
  icon.classList.add('bi-eye');
  }
}

function toggleFavorite(userId,courseId) {
  var btn = document.getElementById('favorite-btn-' + courseId);
  var icon = btn.querySelector('i');

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'toggle_favorite.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
          if (xhr.status === 200) {
              if (xhr.responseText === 'added') {
                  icon.classList.add('text-primary');
                  showToast('Course added to favorites');
              } else if (xhr.responseText === 'removed') {
                  icon.classList.remove('text-primary');
                  showToast('Course removed from favorites');
              }
          } else {
              console.error('Error: ' + xhr.status);
          }
      }
  };
  xhr.send('course_id=' + courseId + '&user_id=' + userId);
}



function showToast(message) {
  const toastContainer = document.createElement('div');
  toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
  toastContainer.style.zIndex = 11;

  const toast = document.createElement('div');
  toast.id = 'liveToast';
  toast.className = 'toast align-items-center text-white bg-primary border-0';
  toast.role = 'alert';
  toast.ariaLive = 'assertive';
  toast.ariaAtomic = 'true';

  const toastBody = document.createElement('div');
  toastBody.className = 'toast-body';
  toastBody.textContent = message;

  const toastButton = document.createElement('button');
  toastButton.type = 'button';
  toastButton.className = 'btn-close btn-close-white me-2 m-auto';
  toastButton.setAttribute('data-bs-dismiss', 'toast');
  toastButton.ariaLabel = 'Close';

  const toastFlex = document.createElement('div');
  toastFlex.className = 'd-flex';
  toastFlex.appendChild(toastBody);
  toastFlex.appendChild(toastButton);

  toast.appendChild(toastFlex);
  toastContainer.appendChild(toast);
  document.body.appendChild(toastContainer);

  const bootstrapToast = new bootstrap.Toast(toast, { delay: 5000 });
  bootstrapToast.show();
}

function previewProfilePicture(event) {
  var reader = new FileReader();
  reader.onload = function(){
      var output = document.getElementById('profilePicturePreview');
      output.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}

function handleCredentialResponse(response) {
  // Decode JWT token to get user data
  const data = JSON.parse(atob(response.credential.split(".")[1]));

  // Send user data to PHP for authentication
  fetch('../google-auth.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(data => {
      if (data.status === 'success') {
          window.location.href = 'dashboard.php';
      } else if (data.status === 'exists') {
          showToast(data.message);
      } else {
          alert('Google authentication failed!');
      }
  })
  .catch(err => console.error(err));
}