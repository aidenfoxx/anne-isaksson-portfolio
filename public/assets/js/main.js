function easeOutQuart(t) {
  return 1 - (--t) * t * t * t;
}

/**
 * Smooth scroll.
 */
(function() {
  function scroll(duration, position, target, callback) {
    let initialTime;

    function step(timestamp) {
      if (!initialTime) {
        initialTime = timestamp;
      }

      const elapsed = timestamp - initialTime;

      if (elapsed >= duration) {
        document.documentElement.scrollTop = document.body.scrollTop = target;
        callback();
        return;
      }

      document.documentElement.scrollTop = document.body.scrollTop = position + easeOutQuart(elapsed / duration) * (target - position);
      window.requestAnimationFrame(step);
    }

    window.requestAnimationFrame(step);
  }

  function scrollAnchor(e) {
    e.preventDefault();

    const targetId = e.currentTarget.getAttribute('href');
    const target = document.querySelector(targetId);

    if (target) {
      const scrollTop = window.pageYOffset;

      scroll(500, scrollTop, target.getBoundingClientRect().top + scrollTop, function() {
        window.location.hash = targetId;
      });
    } else {
      window.location.hash = targetId;
    }
  }

  const links = document.querySelectorAll('a[href^="#"]');

  for (i = 0; i < links.length; i++) {
    links[i].addEventListener('click', scrollAnchor);
  }
})();

/**
 * Simple slider.
 */
(function() {
  const slides = document.querySelectorAll('#portfolio .slider .slide');
  const slideContainer = document.querySelector('#portfolio .slider .slides');
  const slideTitle = document.querySelector('#portfolio .controls .title');

  if (!slides.length) {
    return;
  }

  let activeSlide = 0;

  // NOTE: Old school width calculations for IE.
  slideContainer.style.width = (slides.length * 100) + '%';

  for (let i = 0; i < slides.length; i++) {
    slides[i].style.width = (100 / slides.length) + '%';
  }

  const scrollInterval = setInterval(() => {
    window.nextSlide(true);
  }, 3000);

  slideContainer.addEventListener('click', () => {
    clearInterval(scrollInterval);
  });

  slideContainer.addEventListener('mouseover', () => {
    clearInterval(scrollInterval);
  });

  window.nextSlide = function(autoScroll) {
    if (!autoScroll) {
      clearInterval(scrollInterval);
    }
    activeSlide = activeSlide < slides.length - 1 ? activeSlide + 1 : 0;
    updateSlide();
  };

  window.prevSlide = function(autoScroll) {
    if (!autoScroll) {
      clearInterval(scrollInterval);
    }
    activeSlide = activeSlide > 0 ? activeSlide - 1 : slides.length - 1;
    updateSlide();
  };

  function updateSlide() {
    slideTitle.innerText = slides[activeSlide].getAttribute('title');
    slideContainer.style.marginLeft = '-' + (activeSlide * 100) + '%';
  }

  updateSlide();
})();

/**
 * Header resize.
 */
(function() {
  window.addEventListener('scroll', function() {
    window.requestAnimationFrame(function() {
      if (window.pageYOffset > 0) {
        document.body.classList.add('scroll');
      } else {
        document.body.classList.remove('scroll');
      }
    });
  });
})();

/**
 * Page loaded.
 */
(function() {
  window.addEventListener('load', function() {
    document.body.classList.add('loaded');

    if (window.pageYOffset > 0) {
      document.body.classList.add('scroll');
    } else {
      document.body.classList.remove('scroll');
    }
  });
})();


