/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/otpInput.ts":
/*!****************************!*\
  !*** ./src/js/otpInput.ts ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OTPInputHandler: () => (/* binding */ OTPInputHandler),
/* harmony export */   attachEvent: () => (/* binding */ attachEvent)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
// Copyright (c) 2023 Jeff Oliveira
// OTP Input Field
// Version 1.0
// https://github.com/jeffoliveira977/OTP-input-system
// List of valid event types
var validEvents = [/* Keyboard Events */
"keydown", "keyup", "keypress", /* Form Events */
"submit", "input", "change", "focus", "blur", /* Clipboard Events */
"cut", "copy", "paste", /* Mouse Events */
"click", "mousedown", "mouseup", "mousemove", "mouseenter", "mouseleave"];
/**
 * Attaches an event listener to elements matching the specified selector.
 * @param events - A string containing one or more event types, separated by commas.
 * @param selector - A selector for the target elements.
 * @param callback - A function to be called when the event is triggered.
 */
var attachEvent = function attachEvent(events, selector, callback) {
  // Split the events string into an array of events
  events.split(",").map(function (event) {
    return event.trim();
  }).filter(function (event) {
    return validEvents.includes(event);
  }).forEach(function (event) {
    document.addEventListener(event, function (e) {
      var target = e.target;
      if (target.closest(selector)) {
        // Call the callback function, setting `this` to the target
        callback.call(target, e);
      }
    });
  });
};
/**
 * OTPInputHandler class manages OTP (One-Time Password) input fields and provides event handling.
 * It allows for user interaction and validation of input in OTP input fields.
 */
var OTPInputHandler = /*#__PURE__*/function () {
  /**
   * Constructor for the OTPInputHandler class.
   * @param selector - The CSS selector for OTP input fields.
   */
  function OTPInputHandler(selector) {
    var _this = this;
    _classCallCheck(this, OTPInputHandler);
    /**
     * Handles keydown events for OTP input fields.
     */
    this.handleKeyDown = function (e) {
      // Checks if the event is a KeyboardEvent and prevents the default action if
      // neither the Ctrl key nor the Meta key (Command key on Mac) is pressed,
      // indicating that it's not a Ctrl+C or Ctrl+V event.
      var keyboard = e;
      if (!keyboard.ctrlKey && !keyboard.metaKey) {
        e.preventDefault();
      }
      // Get the index of the current input field
      _this.inputIndex = _this.getInputIndex(e.target);
      var inputValue = _this.inputs[_this.inputIndex].value;
      switch (keyboard.key) {
        case "Backspace":
          _this.inputs[_this.inputIndex].value = "";
          _this.moveFocusLeft();
          break;
        case "Delete":
          _this.inputs[_this.inputIndex].value = "";
          break;
        case "ArrowLeft":
          _this.moveFocusLeft();
          break;
        case "ArrowRight":
          _this.moveFocusRight();
          break;
        default:
          if (/^\d$/.test(keyboard.key) &&
          // Accepts only numeric characters
          !_this.allFilled() && !(_this.inputIndex === _this.inputs.length - 1 && inputValue !== "")) {
            _this.inputs[_this.inputIndex].value = keyboard.key;
            _this.moveFocusRight();
          }
          break;
      }
    };
    /**
     * Handles paste events for OTP input fields.
     */
    this.handlePaste = function (e) {
      e.preventDefault();
      // Get the index of the current input field
      _this.inputIndex = _this.getInputIndex(e.target);
      var clipboardEvent = e;
      // Extracts text data from the clipboard and processes it for input.
      var pasteData = clipboardEvent.clipboardData.getData("text/plain").slice(0, _this.inputs.length - _this.inputIndex) // Limits the pasted data length to the available input fields.
      .split("");
      if (pasteData) {
        // Checks if all pasted values are numeric.
        if (!pasteData.every(function (value) {
          return /^\d$/.test(value);
        })) {
          return;
        }
        // Populates the input fields with the pasted data.
        for (var i = 0; i < pasteData.length; i++) {
          if (_this.inputIndex + i < _this.inputs.length) {
            _this.inputs[_this.inputIndex + i].value = pasteData[i];
          }
        }
      }
    };
    /**
     * Moves the focus to the left input field.
     */
    this.moveFocusLeft = function () {
      if (_this.inputIndex !== 0) {
        _this.inputs[_this.inputIndex - 1].focus();
      }
    };
    /**
     * Moves the focus to the right input field.
     */
    this.moveFocusRight = function () {
      if (_this.inputIndex !== _this.inputs.length - 1) {
        _this.inputs[_this.inputIndex + 1].focus();
      }
    };
    this.selector = selector;
    this.inputIndex = 0;
    this.inputs = document.querySelectorAll(this.selector);
    this.attachEventHandlers();
  }
  /**
   * Attaches event handlers for OTP input fields.
   */
  _createClass(OTPInputHandler, [{
    key: "attachEventHandlers",
    value: function attachEventHandlers() {
      attachEvent("keydown", this.selector, this.handleKeyDown);
      attachEvent("paste", this.selector, this.handlePaste);
    }
    /**
     * Updates the list of input fields based on the current selector.
     * This function should be called if new input fields are added dynamically using AJAX or other means.
     */
  }, {
    key: "updateInputs",
    value: function updateInputs() {
      this.inputs = document.querySelectorAll(this.selector);
    }
    /**
     * Checks if all OTP input fields have values.
     * @returns True if all fields are filled, otherwise false.
     */
  }, {
    key: "allFilled",
    value: function allFilled() {
      return Array.from(this.inputs).every(function (input) {
        return input.value !== "";
      });
    }
    /**
     * Gets the index of the current input field.
     * @param input - The current input field element.
     * @returns The index of the input field in the list.
     */
  }, {
    key: "getInputIndex",
    value: function getInputIndex(input) {
      return Array.from(this.inputs).indexOf(input);
    }
    /**
     * Gets the OTP value from the input fields.
     * @returns The OTP value as a string.
     */
  }, {
    key: "getOTP",
    value: function getOTP() {
      var otpValues = Array.from(this.inputs).map(function (input) {
        return input.value;
      });
      return otpValues.join("");
    }
  }]);
  return OTPInputHandler;
}();

/***/ }),

/***/ "./src/js/script.ts":
/*!**************************!*\
  !*** ./src/js/script.ts ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _otpInput__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./otpInput */ "./src/js/otpInput.ts");


document.addEventListener("DOMContentLoaded", function () {
  var checkedIcon = "\n        <svg xmlns=\"http://www.w3.org/2000/svg\" style=\"margin-right: 5px;\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 30 30.000001\" preserveAspectRatio=\"xMidYMid meet\" version=\"1.0\"><defs><clipPath id=\"id1\"><path d=\"M 2.328125 4.222656 L 27.734375 4.222656 L 27.734375 24.542969 L 2.328125 24.542969 Z M 2.328125 4.222656 \" clip-rule=\"nonzero\"/></clipPath></defs><g clip-path=\"url(#id1)\"><path d=\"M 27.5 7.53125 L 24.464844 4.542969 C 24.15625 4.238281 23.65625 4.238281 23.347656 4.542969 L 11.035156 16.667969 L 6.824219 12.523438 C 6.527344 12.230469 6 12.230469 5.703125 12.523438 L 2.640625 15.539062 C 2.332031 15.84375 2.332031 16.335938 2.640625 16.640625 L 10.445312 24.324219 C 10.59375 24.472656 10.796875 24.554688 11.007812 24.554688 C 11.214844 24.554688 11.417969 24.472656 11.566406 24.324219 L 27.5 8.632812 C 27.648438 8.488281 27.734375 8.289062 27.734375 8.082031 C 27.734375 7.875 27.648438 7.679688 27.5 7.53125 Z M 27.5 7.53125 \"/></g></svg>\n    ";
  var errorIcon = "\n        <svg xml:space=\"preserve\" xmlns=\"http://www.w3.org/2000/svg\" style=\"margin-right: 5px;\" viewBox=\"0 0 512 512\" width=\"16\" height=\"16\" fill=\"currentColor\" ><path d=\"M443.6,387.1L312.4,255.4l131.5-130c5.4-5.4,5.4-14.2,0-19.6l-37.4-37.6c-2.6-2.6-6.1-4-9.8-4c-3.7,0-7.2,1.5-9.8,4  L256,197.8L124.9,68.3c-2.6-2.6-6.1-4-9.8-4c-3.7,0-7.2,1.5-9.8,4L68,105.9c-5.4,5.4-5.4,14.2,0,19.6l131.5,130L68.4,387.1  c-2.6,2.6-4.1,6.1-4.1,9.8c0,3.7,1.4,7.2,4.1,9.8l37.4,37.6c2.7,2.7,6.2,4.1,9.8,4.1c3.5,0,7.1-1.3,9.8-4.1L256,313.1l130.7,131.1  c2.7,2.7,6.2,4.1,9.8,4.1c3.5,0,7.1-1.3,9.8-4.1l37.4-37.6c2.6-2.6,4.1-6.1,4.1-9.8C447.7,393.2,446.2,389.7,443.6,387.1z\"/></svg>\n    ";
  var attentionIcon = "   \n        <svg xmlns=\"http://www.w3.org/2000/svg\" style=\"margin-right: 5px; margin-top: 1px;\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\"><circle cx=\"12\" cy=\"12\" r=\"9\" stroke=\"currentColor\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\"/><rect height=\"0.01\" stroke=\"currentColor\" stroke-linejoin=\"round\" stroke-width=\"3\" width=\"0.01\" x=\"12\" y=\"16\"/><path d=\"M12 12L12 8\" stroke=\"currentColor\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\"/></svg>\n    ";
  var eyeIcon = "\n        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"wsl-eye\" viewBox=\"0 0 16 16\">\n          <path d=\"M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z\"/>\n          <path d=\"M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z\"/>\n        </svg>\n    ";
  var eyeSlashIcon = "\n        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"wsl-eye\" viewBox=\"0 0 16 16\"><path d=\"M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z\"/><path d=\"M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z\"/><path d=\"M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z\"/></svg>\n    ";
  var eventHandlers = function eventHandlers() {
    var otpHandler = new _otpInput__WEBPACK_IMPORTED_MODULE_0__.OTPInputHandler(".wsl-otp-input");
    var formClasses = ["wsl-login-form", "wsl-register-form", "wsl-lost-password-form", "wsl-reset-password-form", "wsl-otp-verification-form"];
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("submit", formClasses.map(function (formClass) {
      return ".".concat(formClass, " form");
    }).join(","), function (e) {
      e.preventDefault();
      var form = e.target;
      var formContainer = form.parentElement;
      if (formContainer) {
        var button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.insertAdjacentHTML("afterbegin", '<span class="wsl-loading-spinner"></span>');
        var currForm = Array.from(formContainer.classList).find(function (className) {
          return formClasses.includes(className);
        }).replace("wsl-", "").replace("-form", "");
        var formData = new FormData(form);
        formData.append("wsl_form", currForm);
        formData.append("action", "process_action");
        formData.append("security", wsl_localize_data.nonce);
        if (currForm === "otp-verification") {
          var otp = otpHandler.getOTP();
          formData.append("wsl-otp-code", otp);
        }
        restoreBordersColor();
        submitForm(formData, button);
      }
    });
    var submitForm = function submitForm(formData, button) {
      fetch(wsl_localize_data.adminurl, {
        method: "POST",
        body: formData
      }).then(function (response) {
        return response.json();
      }).then(function (response) {
        button.disabled = false;
        button.querySelector(".wsl-loading-spinner").remove();
        var extractedSelector = extractSelectorFromString(response.message);
        var formattedMessage = formatString(response.message);
        console.log(extractedSelector, formattedMessage);
        if (extractedSelector) {
          var message = "<div class=\"wsl-password-message check error\">".concat(attentionIcon + formattedMessage, "</div>");
          var isPassword = extractedSelector.includes("password");
          //Se o seletor é uma senha, então usar o seletor completo no lugar do ID
          var selector = isPassword ? ".wsl-password input" : extractedSelector;
          setBordersColor("red", selector);
          var element = document.querySelector(isPassword ? extractedSelector : selector);
          element.focus();
          clearMessages();
          var elementID = selector.split(" ")[0];
          document.querySelector(elementID).insertAdjacentHTML("afterend", message);
        } else {
          if (!response.otp) {
            showMessage(formattedMessage, response.success ? "success" : "attention", false);
          }
        }
        if (response.success) {
          if (response.template) {
            document.querySelector(".wsl-container").innerHTML = response.template;
            otpHandler.updateInputs();
            //showMessage(formattedMessage, 'success', false);
          } else {
            showMessage(formattedMessage, "success", false);
            if (response.redirect) {
              setTimeout(function () {
                window.location.href = response.redirect;
              }, 1000);
            } else {
              location.reload();
            }
          }
        }
      })["catch"](function () {
        button.disabled = false;
        button.querySelector(".wsl-loading-spinner").remove();
        showMessage("An error occurred while processing the request. Please try again later.", "error");
      });
    };
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("click", "#wsl-log-facebook, #wsl-reg-facebook, #wsl-log-google, #wsl-reg-google", function (e) {
      e.preventDefault();
      var link = e.target;
      var form = link.closest("form");
      if (form) {
        var formData = new FormData(form);
        formData.append("wsl_form", link.matches("#wsl-log-facebook, #wsl-reg-facebook") ? "facebook-login" : "google-login");
        formData.append("action", "process_action");
        formData.append("security", wsl_localize_data.nonce);
        var button = this;
        button.disabled = true;
        button.insertAdjacentHTML("afterbegin", '<span class="wsl-loading-spinner" style="border-color: #2563eb rgba(0, 0, 0, 0);"></span>');
        console.log(wsl_localize_data);
        fetch(wsl_localize_data.adminurl, {
          method: "POST",
          body: formData
        }).then(function (response) {
          return response.json();
        }).then(function (response) {
          button.disabled = false;
          button.querySelector(".wsl-loading-spinner").remove();
          if (response.redirect) {
            var w = Math.min(window.innerWidth - 20, 600);
            var h = Math.min(window.innerHeight - 20, 600);
            var x = window.innerWidth / 2 - w / 2;
            var y = window.innerHeight / 2 - h / 2;
            var popup = window.open(response.redirect, "popup", "width=".concat(w, ", height=").concat(h, ", top=").concat(y, ", left=").concat(x));
            if (popup) {
              window.addEventListener("message", function (e) {
                if (typeof e.data === "string" && e.origin === popup.origin) {
                  var panel = document.body.querySelector(".wsl-panel");
                  // Show message
                  if (panel) {
                    panel.insertAdjacentHTML("afterend", e.data);
                  }
                  popup.close();
                  setInterval(function () {
                    //window.location.reload();
                  }, 500);
                }
              });
            }
          }
        })["catch"](function () {
          button.disabled = false;
          button.querySelector(".wsl-loading-spinner").remove();
          showMessage("An error occurred while processing the request. Please try again later.", "error");
        });
      }
    });
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("click", "#wsl-otp-resend", function (e) {
      e.preventDefault();
      var link = e.target;
      var form = link.closest("form");
      if (form) {
        var formData = new FormData(form);
        formData.append("wsl_form", "otp-resend");
        formData.append("action", "process_action");
        formData.append("security", wsl_localize_data.nonce);
        fetch(wsl_localize_data.adminurl, {
          method: "POST",
          body: formData
        }).then(function (response) {
          return response.json();
        }).then(function (data) {
          showMessage(data.message, data.success ? "success" : "error", false);
        })["catch"](function () {
          showMessage("An error occurred while processing the request. Please try again later.", "error");
        });
      }
    });
  };
  var processForm = function processForm() {
    var checkForErrorMessages = function checkForErrorMessages(selector) {
      var elements = document.querySelectorAll(selector);
      if (elements.length) {
        var fadeIn = function fadeIn(element) {
          var opacity = 0;
          if (element instanceof HTMLElement) {
            element.style.opacity = "0";
            element.style.display = "flex";
            var fadeEffect = setInterval(function () {
              if (opacity < 1) {
                opacity += 0.1;
                element.style.opacity = opacity.toString();
              } else {
                clearInterval(fadeEffect);
              }
            }, 50);
          }
        };
        var fadeOut = function fadeOut(element) {
          var opacity = 1;
          if (element instanceof HTMLElement) {
            var fadeEffect = setInterval(function () {
              if (opacity > 0) {
                opacity -= 0.1;
                element.style.opacity = opacity.toString();
              } else {
                clearInterval(fadeEffect);
                element.style.display = "none";
              }
            }, 50);
          }
        };
        elements.forEach(function (element, index) {
          fadeIn(element);
          setTimeout(function () {
            fadeOut(element);
          }, 5000);
        });
      }
    };
    //checkForErrorMessages('.wsl-popup-message');
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        var target = mutation.target;
        if (target.style.display !== "none") {
          target.style.display = "none";
        }
      });
    });
    var observeForms = function observeForms(selectors) {
      observer.disconnect();
      var forms = document.querySelectorAll(selectors);
      forms.forEach(function (form) {
        observer.observe(form, {
          attributes: true,
          attributeFilter: ["style"]
        });
      });
    };
    // Inicia a observação dos formulários de registro e recuperação de senha
    observeForms(".wsl-register-form, .wsl-lost-password-form");
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("click", ".wsl-link:not(#wsl-otp-resend)", function (e) {
      e.preventDefault();
      var target = e.target;
      var isRegisterOrLogin = target.id === "wsl-register" || target.id === "wsl-login";
      var formToToggle = isRegisterOrLogin ? ".wsl-register-form" : ".wsl-lost-password-form";
      document.querySelectorAll(".wsl-login-form, " + formToToggle).forEach(function (element) {
        element.style.display = element.style.display === "none" ? "block" : "none";
      });
      var hideForm = isRegisterOrLogin ? (target.id === "wsl-register" ? ".wsl-login-form" : ".wsl-register-form") + ", .wsl-lost-password-form" : (target.id === "wsl-lost-password" ? ".wsl-login-form" : ".wsl-lost-password-form") + ", .wsl-register-form";
      observeForms(hideForm);
    });
    document.querySelectorAll(".wsl-form input[type=password]:not(#wsl-reg-confirm-password)").forEach(function (element) {
      element.insertAdjacentHTML("afterend", eyeSlashIcon);
    });
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("click", ".wsl-eye", function (e) {
      var target = e.target;
      var inputField = target.previousElementSibling;
      var attrType = inputField.getAttribute("type") === "password" ? "text" : "password";
      inputField.setAttribute("type", attrType);
      target.innerHTML = attrType === "text" ? eyeIcon : eyeSlashIcon;
    });
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("keyup, blur", "#wsl-reg-password, #wsl-reg-confirm-password", function (e) {
      var target = e.target;
      document.querySelectorAll(".wsl-password-message.check").forEach(function (element) {
        return element.remove();
      });
      var passwordValue = document.getElementById("wsl-reg-password").value;
      var confirmPasswordValue = document.getElementById("wsl-reg-confirm-password").value;
      var messages = wsl_localize_data.messages;
      var insertMessage = function insertMessage(message) {
        var messageElement = "<div class=\"wsl-password-message check error\">".concat(attentionIcon + message, "</div>");
        var passwordGroupNode = target.closest(".wsl-password");
        passwordGroupNode.insertAdjacentHTML("afterend", messageElement);
        setBordersColor("red", ".wsl-form input[type=password]");
      };
      if (passwordValue.length === 0 && confirmPasswordValue.length === 0) {
        insertMessage(messages[5]);
        return;
      }
      if (passwordValue !== confirmPasswordValue) {
        insertMessage(messages[4]);
      } else {
        setBordersColor("#d1d5db", ".wsl-form input[type=password]");
      }
    });
    (0,_otpInput__WEBPACK_IMPORTED_MODULE_0__.attachEvent)("keyup", "#wsl-reg-password", function (e) {
      var _a;
      var target = e.target;
      document.querySelectorAll(".wsl-password-message-group").forEach(function (element) {
        return element.remove();
      });
      var password = target.value;
      if (password.length === 0) return;
      var messages = wsl_localize_data.messages;
      var rules = [{
        test: function test(password) {
          return password.length >= 8;
        },
        message: messages[0]
      }, {
        test: function test(password) {
          return /[A-Z]/.test(password);
        },
        message: messages[1]
      }, {
        test: function test(password) {
          return /[a-z]/.test(password);
        },
        message: messages[2]
      }, {
        test: function test(password) {
          return /[0-9]/.test(password);
        },
        message: messages[3]
      }];
      var messageElement = '<div class="wsl-password-message-group">';
      for (var _i = 0, _rules = rules; _i < _rules.length; _i++) {
        var rule = _rules[_i];
        var statusClass = rule.test(password) ? "success" : "error";
        messageElement += "<div class=\"wsl-password-message ".concat(statusClass, "\">").concat((rule.test(password) ? checkedIcon : errorIcon) + rule.message, "</div>");
      }
      messageElement += "</div>";
      var bordersColor = rules.some(function (rule) {
        return !rule.test(password);
      }) ? "red" : "#d1d5db";
      setBordersColor(bordersColor, ".wsl-password input");
      var passwordGroupNode = target.closest(".wsl-password");
      var nextSibling = passwordGroupNode.nextSibling;
      var passwordMessageNode = ((_a = nextSibling === null || nextSibling === void 0 ? void 0 : nextSibling.classList) === null || _a === void 0 ? void 0 : _a.contains("wsl-password-message")) ? nextSibling : null;
      if (passwordMessageNode) {
        passwordMessageNode.insertAdjacentHTML("afterend", messageElement);
      } else if (passwordGroupNode) {
        passwordGroupNode.insertAdjacentHTML("afterend", messageElement);
      }
    });
  };
  processForm();
  eventHandlers();
  var extractSelectorFromString = function extractSelectorFromString(text) {
    var match = text.match(/{(.+?)}/);
    return match ? match[1] : null;
  };
  var formatString = function formatString(text) {
    return text.replace(/{.+?}/, "").trim();
  };
  var validateCPF = function validateCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, "");
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
      return false;
    }
    var digits = cpf.split("").map(Number);
    var calculateDigit = function calculateDigit(cpf, factor) {
      return cpf.reduce(function (sum, digit, index) {
        return sum + digit * (factor - index);
      }, 0) * 10 % 11 % 10;
    };
    var digit1 = calculateDigit(digits.slice(0, 9), 10);
    var digit2 = calculateDigit(digits.slice(0, 10), 11);
    return digit1 === digits[9] && digit2 === digits[10];
  };
  var showMessage = function showMessage(message, type) {
    var showIcon = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
    clearMessages();
    var allowedTypes = ["success", "error", "attention"];
    if (!allowedTypes.includes(type)) {
      return;
    }
    var icons = {
      success: checkedIcon,
      error: errorIcon,
      attention: attentionIcon
    };
    var statusClass = type === "success" ? "success" : "error";
    var messageElement = "<div class=\"wsl-password-message check ".concat(statusClass, "\">").concat(showIcon ? icons[type] : "" + message, "</div>");
    var submitButton = document.querySelector(".wsl-form button[type=submit]");
    submitButton.insertAdjacentHTML("beforebegin", messageElement);
  };
  var clearMessages = function clearMessages() {
    document.querySelectorAll(".wsl-message").forEach(function (element) {
      return element.remove();
    });
    document.querySelectorAll(".wsl-password-message.check").forEach(function (element) {
      return element.remove();
    });
  };
  var setBordersColor = function setBordersColor(color) {
    for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      params[_key - 1] = arguments[_key];
    }
    params.forEach(function (selector) {
      var elements = document.querySelectorAll(selector);
      if (elements.length > 0) {
        elements.forEach(function (element) {
          element.style.borderColor = color;
        });
      }
    });
  };
  var restoreBordersColor = function restoreBordersColor() {
    var element = document.querySelector(".wsl-form input, .wsl-form select");
    if (element) {
      element.style.borderColor = "#d1d5db";
    }
  };
});

/***/ }),

/***/ "./src/scss/style.scss":
/*!*****************************!*\
  !*** ./src/scss/style.scss ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/build/js/script": 0,
/******/ 			"build/css/style": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["build/css/style"], () => (__webpack_require__("./src/js/script.ts")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["build/css/style"], () => (__webpack_require__("./src/scss/style.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;