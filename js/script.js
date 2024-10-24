

// amenities

$(".hover").mouseleave(
  function () {
    $(this).removeClass("hover");
  }
);

// popup function 
$(window).ready(function () {
  setTimeout(function () {
    $('#popupModal').modal("show")
  }, 3000)
})


// gallery

"use strict";
(function () {
  window.onmouseover = () => {
    const obj = document.querySelector("#gallery");
    const time = 10000;
    function animStart() {
      if (obj.classList.contains("active") == false) {
        obj.classList.add("active");
        setTimeout(() => {
          animEnd();
        }, time);
      }
    }
    function animEnd() {
      obj.classList.remove("active");
      obj.offsetWidth;
    }
    document.addEventListener("scroll", function () {
      // scroll or scrollend
      animStart();
    });
    window.addEventListener("resize", animStart);
    animStart();
  };
})();

// fancy Box
Fancybox.bind('[data-fancybox="gallery"]', {
  // Custom options for the first gallery
});

function toggleContent(dotsId, moreTextId, btnId) {
  var dots = document.getElementById(dotsId);
  var moreText = document.getElementById(moreTextId);
  var btnText = document.getElementById(btnId);

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    btnText.innerHTML = "Read More ...";
    moreText.style.display = "none";
  } else {
    dots.style.display = "none";
    btnText.innerHTML = "Read Less";
    moreText.style.display = "inline";
  }
}