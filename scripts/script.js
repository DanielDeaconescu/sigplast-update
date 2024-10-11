"use strict";

const navigation = document.querySelector(".navbar");
const formContainer = document.querySelector(".form-container");
const workshopForm = document.querySelector(".workshop_form");
const formCloseButton = document.querySelector(".form-close-button");
const cancelFormBtn = document.querySelector(".btn-cancel-custom");
const overlay = document.querySelector(".overlay");

function isElementInViewport(el) {
  if (typeof jQuery === "function" && el instanceof jQuery) {
    el = el[0];
  }

  const rect = el.getBoundingClientRect();
  return (
    (rect.top <= 0 && rect.bottom >= 0) ||
    (rect.bottom >=
      (window.innerHeight || document.documentElement.clientHeight) &&
      rect.top <=
        (window.innerHeight || document.documentElement.clientHeight)) ||
    (rect.top >= 0 &&
      rect.bottom <=
        (window.innerHeight || document.documentElement.clientHeight))
  );
}

const scroll =
  window.requestAnimation ||
  function (callback) {
    window.setTimeout(callback, 1000 / 60);
  };

const elementsToShow = document.querySelectorAll(".show-on-scroll");

function loop() {
  elementsToShow.forEach(function (element) {
    if (isElementInViewport(element)) {
      element.classList.add("is-visible");
    } else {
      element.classList.remove("is-visible");
    }
  });
  scroll(loop);
}

loop();

// Cookie Section

const cookieContainer = document.querySelector(".cookie-container");
const cookieButton = document.querySelector(".cookie-btn");

cookieButton.addEventListener("click", () => {
  cookieContainer.classList.remove("active");
  localStorage.setItem("cookieBannerDisplayed", "true");
});

setTimeout(() => {
  if (!localStorage.getItem("cookieBannerDisplayed")) {
    cookieContainer.classList.add("active");
  }
}, 2000);

// form functionality

const toggleForm = function () {
  formContainer.classList.toggle("no-display");
  overlay.classList.toggle("no-display");
};

const closeForm = function () {
  formContainer.classList.add("no-display");
  overlay.classList.add("no-display");
};

const resetForm = function () {
  workshopForm.reset();
};

cancelFormBtn.addEventListener("click", resetForm);

overlay.addEventListener("click", closeForm);
formCloseButton.addEventListener("click", closeForm);
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeForm();
});

workshopForm.addEventListener("submit", function () {
  workshopForm.reset();
});
