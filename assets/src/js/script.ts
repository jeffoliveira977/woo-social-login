declare var wsl_localize_data: any;
declare var grecaptcha: any;

import { OTPInputHandler } from "./otpInput";
import { attachEvent } from "./otpInput";

document.addEventListener("DOMContentLoaded", function () {
  const checkedIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;" width="16" height="16" fill="currentColor" viewBox="0 0 30 30.000001" preserveAspectRatio="xMidYMid meet" version="1.0"><defs><clipPath id="id1"><path d="M 2.328125 4.222656 L 27.734375 4.222656 L 27.734375 24.542969 L 2.328125 24.542969 Z M 2.328125 4.222656 " clip-rule="nonzero"/></clipPath></defs><g clip-path="url(#id1)"><path d="M 27.5 7.53125 L 24.464844 4.542969 C 24.15625 4.238281 23.65625 4.238281 23.347656 4.542969 L 11.035156 16.667969 L 6.824219 12.523438 C 6.527344 12.230469 6 12.230469 5.703125 12.523438 L 2.640625 15.539062 C 2.332031 15.84375 2.332031 16.335938 2.640625 16.640625 L 10.445312 24.324219 C 10.59375 24.472656 10.796875 24.554688 11.007812 24.554688 C 11.214844 24.554688 11.417969 24.472656 11.566406 24.324219 L 27.5 8.632812 C 27.648438 8.488281 27.734375 8.289062 27.734375 8.082031 C 27.734375 7.875 27.648438 7.679688 27.5 7.53125 Z M 27.5 7.53125 "/></g></svg>
    `;

  const errorIcon = `
        <svg xml:space="preserve" xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;" viewBox="0 0 512 512" width="16" height="16" fill="currentColor" ><path d="M443.6,387.1L312.4,255.4l131.5-130c5.4-5.4,5.4-14.2,0-19.6l-37.4-37.6c-2.6-2.6-6.1-4-9.8-4c-3.7,0-7.2,1.5-9.8,4  L256,197.8L124.9,68.3c-2.6-2.6-6.1-4-9.8-4c-3.7,0-7.2,1.5-9.8,4L68,105.9c-5.4,5.4-5.4,14.2,0,19.6l131.5,130L68.4,387.1  c-2.6,2.6-4.1,6.1-4.1,9.8c0,3.7,1.4,7.2,4.1,9.8l37.4,37.6c2.7,2.7,6.2,4.1,9.8,4.1c3.5,0,7.1-1.3,9.8-4.1L256,313.1l130.7,131.1  c2.7,2.7,6.2,4.1,9.8,4.1c3.5,0,7.1-1.3,9.8-4.1l37.4-37.6c2.6-2.6,4.1-6.1,4.1-9.8C447.7,393.2,446.2,389.7,443.6,387.1z"/></svg>
    `;

  const attentionIcon = `   
        <svg xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px; margin-top: 1px;" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><rect height="0.01" stroke="currentColor" stroke-linejoin="round" stroke-width="3" width="0.01" x="12" y="16"/><path d="M12 12L12 8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
    `;

  const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="wsl-eye" viewBox="0 0 16 16">
          <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
          <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        </svg>
    `;

  const eyeSlashIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="wsl-eye" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>
    `;

  const eventHandlers = () => {
    const otpHandler = new OTPInputHandler(".wsl-otp-input");

    const formClasses = [
      "wsl-login-form",
      "wsl-register-form",
      "wsl-lost-password-form",
      "wsl-reset-password-form",
      "wsl-otp-verification-form",
    ];

    attachEvent(
      "submit",
      formClasses.map((formClass) => `.${formClass} form`).join(","),
      function (e) {
        e.preventDefault();

        const form = e.target as HTMLFormElement;
        const formContainer = form.parentElement;

        if (formContainer) {
          const button = form.querySelector(
            'button[type="submit"]'
          ) as HTMLButtonElement;

          button.disabled = true;
          button.insertAdjacentHTML(
            "afterbegin",
            '<span class="wsl-loading-spinner"></span>'
          );

          const currForm: string = Array.from(formContainer.classList)
            .find((className) => formClasses.includes(className))
            .replace("wsl-", "")
            .replace("-form", "");

          const formData = new FormData(form);
          formData.append("wsl_form", currForm);
          formData.append("action", "process_action");
          formData.append("security", wsl_localize_data.nonce);

          if (currForm === "otp-verification") {
            const otp: any = otpHandler.getOTP();
            formData.append("wsl-otp-code", otp);
          }

          restoreBordersColor();

          submitForm(formData, button);
        }
      }
    );

    const submitForm = (formData: FormData, button: HTMLButtonElement) => {
      fetch(wsl_localize_data.adminurl, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((response: any) => {
          button.disabled = false;
          button.querySelector(".wsl-loading-spinner").remove();

          const extractedSelector = extractSelectorFromString(response.message);
          const formattedMessage: string = formatString(response.message);
          console.log(extractedSelector, formattedMessage);

          if (extractedSelector) {
            const message: string = `<div class="wsl-password-message check error">${
              attentionIcon + formattedMessage
            }</div>`;
            const isPassword: boolean = extractedSelector.includes("password");

            //Se o seletor é uma senha, então usar o seletor completo no lugar do ID
            const selector: string = isPassword
              ? ".wsl-password input"
              : extractedSelector;

            setBordersColor("red", selector);

            const element = document.querySelector(
              isPassword ? extractedSelector : selector
            ) as HTMLElement;

            element.focus();

            clearMessages();

            const elementID = selector.split(" ")[0];
            document
              .querySelector(elementID)
              .insertAdjacentHTML("afterend", message);
          } else {
            if (!response.otp) {
              showMessage(
                formattedMessage,
                response.success ? "success" : "attention",
                false
              );
            }
          }

          if (response.success) {
            if (response.template) {
              document.querySelector(".wsl-container").innerHTML =
                response.template;
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
        })
        .catch(() => {
          button.disabled = false;
          button.querySelector(".wsl-loading-spinner").remove();
          showMessage(
            "An error occurred while processing the request. Please try again later.",
            "error"
          );
        });
    };

    attachEvent(
      "click",
      "#wsl-log-facebook, #wsl-reg-facebook, #wsl-log-google, #wsl-reg-google",
      function (e: Event) {
        e.preventDefault();

        const link = e.target as HTMLLinkElement;
        const form = link.closest("form");

        if (form) {
          const formData = new FormData(form);

          formData.append(
            "wsl_form",
            link.matches("#wsl-log-facebook, #wsl-reg-facebook")
              ? "facebook-login"
              : "google-login"
          );

          formData.append("action", "process_action");
          formData.append("security", wsl_localize_data.nonce);

          const button = this;
          button.disabled = true;
          button.insertAdjacentHTML(
            "afterbegin",
            '<span class="wsl-loading-spinner" style="border-color: #2563eb rgba(0, 0, 0, 0);"></span>'
          );

          console.log(wsl_localize_data);
          fetch(wsl_localize_data.adminurl, {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((response) => {
              button.disabled = false;
              button.querySelector(".wsl-loading-spinner").remove();

              if (response.redirect) {
                const w = Math.min(window.innerWidth - 20, 600);
                const h = Math.min(window.innerHeight - 20, 600);
                const x = window.innerWidth / 2 - w / 2;
                const y = window.innerHeight / 2 - h / 2;

                const popup = window.open(
                  response.redirect,
                  "popup",
                  `width=${w}, height=${h}, top=${y}, left=${x}`
                );

                if (popup) {
                  window.addEventListener("message", function (e) {
                    if (
                      typeof e.data === "string" &&
                      e.origin === popup.origin
                    ) {
                      const panel = document.body.querySelector(".wsl-panel");

                      // Show message
                      if (panel) {
                        panel.insertAdjacentHTML("afterend", e.data);
                      }

                      popup.close();

                      setInterval(() => {
                        //window.location.reload();
                      }, 500);
                    }
                  });
                }
              }
            })
            .catch(() => {
              button.disabled = false;
              button.querySelector(".wsl-loading-spinner").remove();
              showMessage(
                "An error occurred while processing the request. Please try again later.",
                "error"
              );
            });
        }
      }
    );

    attachEvent("click", "#wsl-otp-resend", function (e: Event) {
      e.preventDefault();

      const link = e.target as HTMLLinkElement;
      const form = link.closest("form");

      if (form) {
        const formData = new FormData(form);
        formData.append("wsl_form", "otp-resend");
        formData.append("action", "process_action");
        formData.append("security", wsl_localize_data.nonce);
        fetch(wsl_localize_data.adminurl, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            showMessage(
              data.message,
              data.success ? "success" : "error",
              false
            );
          })
          .catch(() => {
            showMessage(
              "An error occurred while processing the request. Please try again later.",
              "error"
            );
          });
      }
    });
  };

  const processForm = () => {
    const checkForErrorMessages = (selector: string) => {
      const elements = document.querySelectorAll(selector);
      if (elements.length) {
        const fadeIn = (element: Element) => {
          let opacity = 0 as number;
          if (element instanceof HTMLElement) {
            element.style.opacity = "0";
            element.style.display = "flex";
            const fadeEffect = setInterval(() => {
              if (opacity < 1) {
                opacity += 0.1;
                element.style.opacity = opacity.toString();
              } else {
                clearInterval(fadeEffect);
              }
            }, 50);
          }
        };

        const fadeOut = (element: Element) => {
          let opacity = 1 as number;
          if (element instanceof HTMLElement) {
            const fadeEffect = setInterval(() => {
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

        elements.forEach((element, index) => {
          fadeIn(element);
          setTimeout(() => {
            fadeOut(element);
          }, 5000);
        });
      }
    };

    //checkForErrorMessages('.wsl-popup-message');

    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        const target = mutation.target as HTMLElement;
        if (target.style.display !== "none") {
          target.style.display = "none";
        }
      });
    });

    const observeForms = (selectors: any) => {
      observer.disconnect();

      const forms = document.querySelectorAll(selectors);
      forms.forEach(function (form) {
        observer.observe(form, {
          attributes: true,
          attributeFilter: ["style"],
        });
      });
    };

    // Inicia a observação dos formulários de registro e recuperação de senha
    observeForms(".wsl-register-form, .wsl-lost-password-form");

    attachEvent("click", ".wsl-link:not(#wsl-otp-resend)", function (e: Event) {
      e.preventDefault();

      const target = e.target as HTMLElement;
      const isRegisterOrLogin =
        target.id === "wsl-register" || target.id === "wsl-login";

      const formToToggle = isRegisterOrLogin
        ? ".wsl-register-form"
        : ".wsl-lost-password-form";

      document
        .querySelectorAll(".wsl-login-form, " + formToToggle)
        .forEach(function (element: HTMLElement) {
          element.style.display =
            element.style.display === "none" ? "block" : "none";
        });

      const hideForm = isRegisterOrLogin
        ? (target.id === "wsl-register"
            ? ".wsl-login-form"
            : ".wsl-register-form") + ", .wsl-lost-password-form"
        : (target.id === "wsl-lost-password"
            ? ".wsl-login-form"
            : ".wsl-lost-password-form") + ", .wsl-register-form";

      observeForms(hideForm);
    });

    document
      .querySelectorAll(
        ".wsl-form input[type=password]:not(#wsl-reg-confirm-password)"
      )
      .forEach(function (element: Element) {
        element.insertAdjacentHTML("afterend", eyeSlashIcon);
      });

    attachEvent("click", ".wsl-eye", function (e: Event) {
      const target = e.target as HTMLInputElement;

      const inputField = target.previousElementSibling as HTMLInputElement;
      const attrType =
        inputField.getAttribute("type") === "password" ? "text" : "password";

      inputField.setAttribute("type", attrType);

      target.innerHTML = attrType === "text" ? eyeIcon : eyeSlashIcon;
    });

    attachEvent(
      "keyup, blur",
      "#wsl-reg-password, #wsl-reg-confirm-password",
      function (e: Event) {
        const target = e.target as HTMLInputElement;

        document
          .querySelectorAll(".wsl-password-message.check")
          .forEach((element) => element.remove());

        const passwordValue = (
          document.getElementById("wsl-reg-password") as HTMLInputElement
        ).value;

        const confirmPasswordValue = (
          document.getElementById(
            "wsl-reg-confirm-password"
          ) as HTMLInputElement
        ).value;

        const messages = wsl_localize_data.messages;

        const insertMessage = (message: string) => {
          const messageElement = `<div class="wsl-password-message check error">${
            attentionIcon + message
          }</div>`;

          const passwordGroupNode = target.closest(".wsl-password");
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
      }
    );

    attachEvent("keyup", "#wsl-reg-password", function (e: Event) {
      const target = e.target as HTMLInputElement;

      document
        .querySelectorAll(".wsl-password-message-group")
        .forEach((element) => element.remove());

      const password = target.value;

      if (password.length === 0) return;

      const messages = wsl_localize_data.messages;

      interface PasswordRule {
        test: (password: string) => boolean;
        message: string;
      }

      const rules: PasswordRule[] = [
        { test: (password) => password.length >= 8, message: messages[0] },
        { test: (password) => /[A-Z]/.test(password), message: messages[1] },
        { test: (password) => /[a-z]/.test(password), message: messages[2] },
        { test: (password) => /[0-9]/.test(password), message: messages[3] },
      ];

      let messageElement = '<div class="wsl-password-message-group">';
      for (const rule of rules) {
        const statusClass = rule.test(password) ? "success" : "error";
        messageElement += `<div class="wsl-password-message ${statusClass}">${
          (rule.test(password) ? checkedIcon : errorIcon) + rule.message
        }</div>`;
      }
      messageElement += "</div>";

      const bordersColor = rules.some((rule) => !rule.test(password))
        ? "red"
        : "#d1d5db";

      setBordersColor(bordersColor, ".wsl-password input");

      const passwordGroupNode = target.closest(".wsl-password");
      const nextSibling = passwordGroupNode.nextSibling as HTMLElement;
      const passwordMessageNode = nextSibling?.classList?.contains(
        "wsl-password-message"
      )
        ? nextSibling
        : null;

      if (passwordMessageNode) {
        passwordMessageNode.insertAdjacentHTML("afterend", messageElement);
      } else if (passwordGroupNode) {
        passwordGroupNode.insertAdjacentHTML("afterend", messageElement);
      }
    });
  };

  processForm();
  eventHandlers();

  const extractSelectorFromString = (text: string) => {
    const match = text.match(/{(.+?)}/);
    return match ? match[1] : null;
  };

  const formatString = (text: string) => {
    return text.replace(/{.+?}/, "").trim();
  };

  const validateCPF = (cpf: string) => {
    cpf = cpf.replace(/[^\d]+/g, "");

    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
      return false;
    }

    const digits = cpf.split("").map(Number);

    const calculateDigit = (cpf, factor) =>
      ((cpf.reduce((sum, digit, index) => sum + digit * (factor - index), 0) *
        10) %
        11) %
      10;

    const digit1 = calculateDigit(digits.slice(0, 9), 10);
    const digit2 = calculateDigit(digits.slice(0, 10), 11);

    return digit1 === digits[9] && digit2 === digits[10];
  };

  const showMessage = (
    message: string,
    type: string,
    showIcon: boolean = true
  ) => {
    clearMessages();

    const allowedTypes = ["success", "error", "attention"];

    if (!allowedTypes.includes(type)) {
      return;
    }

    const icons = {
      success: checkedIcon,
      error: errorIcon,
      attention: attentionIcon,
    };

    const statusClass = type === "success" ? "success" : "error";
    const messageElement = `<div class="wsl-password-message check ${statusClass}">${
      showIcon ? icons[type] : "" + message
    }</div>`;

    const submitButton = document.querySelector(
      ".wsl-form button[type=submit]"
    );
    submitButton.insertAdjacentHTML("beforebegin", messageElement);
  };

  const clearMessages = () => {
    document
      .querySelectorAll(".wsl-message")
      .forEach((element) => element.remove());

    document
      .querySelectorAll(".wsl-password-message.check")
      .forEach((element) => element.remove());
  };

  const setBordersColor = (color: string, ...params: string[]) => {
    params.forEach((selector) => {
      const elements = document.querySelectorAll(selector);

      if (elements.length > 0) {
        elements.forEach((element: HTMLElement) => {
          element.style.borderColor = color;
        });
      }
    });
  };

  const restoreBordersColor = () => {
    const element = document.querySelector(
      ".wsl-form input, .wsl-form select"
    ) as HTMLElement;

    if (element) {
      element.style.borderColor = "#d1d5db";
    }
  };
});
