"use strict";

const navigation = document.querySelector(".navbar");
const formContainer = document.querySelector(".form-container");
const cancelFormBtn = document.querySelectorAll(".btn-cancel-custom");
const overlay = document.querySelector(".overlay");
const formFactoryBtn = document.querySelector(".factory-workshop__factory");
const formWorkshopBtn = document.querySelector(".factory-workshop__workshop");
const initialDisplay = document.querySelector(".initial-display");
const workshopForm = document.querySelector(".workshop-form");
const factoryForm = document.querySelector(".factory-form");

const workshopFormInner = document.querySelector(".workshop_form");
const factoryFormInner = document.querySelector(".factory_form");

const formInitialButtonsContainer = document.querySelector(
  ".form-initial-buttons-container"
);
const closeFormButton = document.querySelectorAll(".btn-close");
const formButton = document.querySelector(".side-buttons-form-button");
const sideButtons = document.querySelector(".side-buttons");
const newsletter = document.getElementById("newsletter");
const backTopButtonInner = document.querySelector(".back-top-btn-inner");

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

// function that closes a form based on a value
const closeFormParticular = function (formType) {
  if (formType === "factory") factoryForm.classList.add("d-none");
  if (formType === "workshop") workshopForm.classList.add("d-none");
};

const resetForm = function () {
  workshopFormInner.reset();
  factoryFormInner.reset();
  // make sure both forms are closed
  closeFormParticular("factory");
  closeFormParticular("workshop");

  // display inital screen of the form
  initialDisplay.classList.remove("d-none");
};

cancelFormBtn.forEach((cancelButton) =>
  cancelButton.addEventListener("click", resetForm)
);

// closeFormButton.addEventListener("click", resetForm);
closeFormButton.forEach((closeButton) => {
  closeButton.addEventListener("click", resetForm);
});

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("modal")) resetForm();
});

// overlay.addEventListener("click", function (e) {
//   console.log(e);
// });
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeForm();
});

workshopForm.addEventListener("submit", function () {
  setTimeout(() => {
    resetForm();
  }, 100);
});

// close the navigation when clicking outside of it
document.addEventListener("click", function (e) {
  if (!navigation.contains(e.target)) {
    document.querySelector("#navbarNav").classList.remove("show");
    document
      .querySelector(".navbar-toggler")
      .setAttribute("aria-expanded", "false");
    document.querySelector(".navbar-toggler").classList.add("collapsed");
  }
});

// formFactoryBtn.addEventListener("click", function () {
//   initialDisplay.classList.add("d-none");
//   factoryForm.classList.remove("d-none");
// });

// formWorkshopBtn.addEventListener("click", function () {
//   initialDisplay.classList.add("d-none");
//   workshopForm.classList.remove("d-none");
// });

// function that opens a form based on a given input
const openForm = function (formType) {
  if (formType === "factory") {
    // make sure the workshop form is closed
    workshopForm.classList.add("d-none");
    // display the factory form
    initialDisplay.classList.add("d-none");
    factoryForm.classList.remove("d-none");
  }
  if (formType === "workshop") {
    // make sure the factory form is closed
    factoryForm.classList.add("d-none");
    // display the workshop form
    initialDisplay.classList.add("d-none");
    workshopForm.classList.remove("d-none");
  }
};

// Using event delegation in order to make the form work as expected
formInitialButtonsContainer.addEventListener("click", function (e) {
  const clickedElement = e.target.closest(".form-button-initial");
  if (clickedElement.classList.contains("factory-workshop__factory")) {
    openForm("factory");
  }
  if (clickedElement.classList.contains("factory-workshop__workshop")) {
    openForm("workshop");
  }
});

// formButton hover effect
formButton.addEventListener("mouseover", function (e) {
  const hovered = e.target.closest(".side-buttons-form-button");
  if (hovered) {
    document
      .querySelector(".message-form-inner")
      .classList.remove("message-form-inner-hidden");
  }
});

formButton.addEventListener("mouseout", function (e) {
  document
    .querySelector(".message-form-inner")
    .classList.add("message-form-inner-hidden");
});

// Intersection Observer for the back-top button

const options = {
  root: document.querySelector(".newsletter"),
  threshold: 0,
};

const observeBtn = function (entries, observer) {
  const [entry] = entries;
  console.log(entry);
};

const buttonObserver = new IntersectionObserver(observeBtn, options);

buttonObserver.observe(backTopButtonInner);
