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

// Back-to-top button functionality

function userScroll() {
  const toTopBtn = document.querySelector(".back-top-btn");

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      toTopBtn.classList.add("show");
    } else {
      toTopBtn.classList.remove("show");
    }
  });
}

function scrollToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

document.addEventListener("DOMContentLoaded", userScroll);

if (document.querySelector(".back-top-btn")) {
  document
    .querySelector(".back-top-btn")
    .addEventListener("click", scrollToTop);
}

formButton.addEventListener("mouseenter", function () {
  document
    .querySelector(".message-form-inner")
    .classList.remove("message-form-inner-hidden");
});

formButton.addEventListener("mouseleave", function () {
  document
    .querySelector(".message-form-inner")
    .classList.add("message-form-inner-hidden");
});

// Factory Form Validation
document.getElementById("factoryForm").addEventListener("submit", function (e) {
  let valid = true;

  // Get elements
  const fullName = document.getElementById("fullName");
  const phoneNum = document.getElementById("phoneNum");
  const fullNameError = document.getElementById("fullNameError");
  const phoneNumError = document.getElementById("phoneNumError");

  // Clear previous messages
  fullNameError.textContent = "";
  phoneNumError.textContent = "";

  // Validate full name
  if (fullName.value.trim() === "") {
    fullNameError.textContent = 'Câmpul "Nume complet" este obligatoriu.';
    valid = false;
  }

  // Validate phone number
  if (phoneNum.value.trim() === "") {
    phoneNumError.textContent = 'Câmpul "Număr de telefon" este obligatoriu.';
    valid = false;
  }

  // Prevent submission if form not valid
  if (!valid) {
    e.preventDefault();
  }
});

// Workshop Form Validation

document
  .getElementById("workshopForm")
  .addEventListener("submit", function (e) {
    let valid = true;

    const fullName = document.getElementById("workshopFullName");
    const phoneNum = document.getElementById("workshopPhoneNum");
    const fullNameError = document.getElementById("workshopFullNameError");
    const phoneNumError = document.getElementById("workshopPhoneNumError");

    // Clear previous errors
    fullNameError.textContent = "";
    phoneNumError.textContent = "";

    // Validate full name
    if (fullName.value.trim() === "") {
      fullNameError.textContent = 'Câmpul "Nume complet" este obligatoriu.';
      valid = false;
    }

    // Validate phone number

    if (phoneNum.value.trim() === "") {
      phoneNumError.textContent = 'Câmpul "Număr de telefon" este obligatoriu.';
      valid = false;
    }

    // If not valid, prevent submission
    if (!valid) {
      e.preventDefault();
    }
  });
