import { getIcon } from "./icons";

let slideUp = (target, duration = 500) => {
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.boxSizing = "border-box";
  target.style.height = target.offsetHeight + "px";
  target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  window.setTimeout(() => {
    target.style.display = "none";
    target.style.removeProperty("height");
    target.style.removeProperty("padding-top");
    target.style.removeProperty("padding-bottom");
    target.style.removeProperty("margin-top");
    target.style.removeProperty("margin-bottom");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
    //alert("!");
  }, duration);
};

let slideDown = (target, duration = 500) => {
  target.style.removeProperty("display");
  let display = window.getComputedStyle(target).display;

  if (display === "none") display = "block";

  target.style.display = display;
  let height = target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = "border-box";
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.height = height + "px";
  target.style.removeProperty("padding-top");
  target.style.removeProperty("padding-bottom");
  target.style.removeProperty("margin-top");
  target.style.removeProperty("margin-bottom");
  window.setTimeout(() => {
    target.style.removeProperty("height");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
  }, duration);
};

export const slideToggle = (target: HTMLElement, duration = 400): void => {
  if (window.getComputedStyle(target).display === "none") {
    slideDown(target, duration);
  } else {
    slideUp(target, duration);
  }
};

interface NotifymeStyles {
  containerStyle?: {
    [key: string]: string;
  };
  fontStyle?: {
    [key: string]: string;
  };
}

interface NotifymeContent {
  message: string;
  type: string;
  title?: string;
  showIcon?: boolean;
  duration?: number;
  customStyles?: NotifymeStyles;
  position?: string;
  priority?: number;
  callback?: () => void;
}

class Notifyme {
  public content: NotifymeContent;

  public container: HTMLElement;
  private wrapper: HTMLElement;
  private closeTimeout: ReturnType<typeof setTimeout>;
  private onCloseCallback?: () => void;

  constructor(content: NotifymeContent) {
    this.content = {
      ...content,
      showIcon: content.showIcon === undefined ? true : content.showIcon,
      duration: content.duration === undefined ? 5000 : content.duration
    };

    this.onCloseCallback = this.content.callback;
    this.onInitialization();
  }

  // Initialization logic for the notification
  private onInitialization = (): void => {
    try {
      if (this.content.message.length === 0) {
        throw "Incorrect description";
      }

      const regex = /^(info|success|warning|error|notify)$/;
      if (!regex.test(this.content.type)) {
        throw "The notification type " + this.content.type + " is not valid";
      }
    } catch (error) {
      console.error(error);
      return;
    }

    this.buildContainer();
    this.onEventHandlers();
  };

  private onEventHandlers = (): void => {
    this.container.addEventListener("click", () => {
      this.onClose();
    });

    // Hover event to stop the close timeout when mouse is over the container
    this.container.addEventListener("mouseover", () => {
      this.stopCloseTimeout();
    });

    this.container.addEventListener("mouseout", () => {
      this.startCloseTimeout();
    });

    const close = this.container.querySelector(".notifyme-icon");

    if (close) {
      close.addEventListener("click", () => {
        this.onClose();
      });
    }

    this.startCloseTimeout();
  };

  private buildContainer = (): void => {
    const containerHTML = `
        <div class="notifyme-container notifyme-${this.content.type}">
            <div class="notifyme-message">
              <div class="notifyme-title">${this.content.title}</div>

                  <div class="notifyme-description">${this.content.message}</div>
              </div>
            
            <div class="notifyme-close">
              <svg xmlns="http://www.w3.org/2000/svg" height="1em" fill="currentColor" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
            </div>
        </div>
    `;

    // Create a temporary div to hold the HTML
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = containerHTML.trim();

    // Get the first (and only) child of the temporary div
    this.container = tempDiv.firstChild as HTMLElement;

    if (this.content.showIcon) {
      const iconHTML = `
      <div class="notifyme-icon">
          ${getIcon(this.content.type, true)}
      </div>
      `;

      this.container.insertAdjacentHTML("afterbegin", iconHTML);
    }

    this.appendContainer(this.container);

    this.slideOutContainer(this.container);
    this.buildStyles();
    this.setPosition(this.content.position);
  };

  private appendContainer(container: HTMLElement) {
    const validPositions = ["topLeft", "topRight", "bottomLeft", "bottomRight"];
    const wrapperClass = validPositions.includes(this.content.position)
      ? `notifyme-${this.content.position}`
      : "notifyme-topRight";

    // Verifique se já existe um wrapper com a classe de posição
    this.wrapper = document.querySelector(`.notifyme-wrapper.${wrapperClass}`);

    // Se não existir, crie um novo wrapper com a classe correspondente
    if (!this.wrapper) {
      this.wrapper = document.createElement("div");
      this.wrapper.className = `notifyme-wrapper ${wrapperClass}`;
      document.body.appendChild(this.wrapper);
    }
    if (this.wrapper && this.wrapper.childElementCount === 1) {
      this.wrapper.classList.add("slide-down-animation");
    }
    const order = true;
    if (order) {
      this.wrapper.insertBefore(container, this.wrapper.firstChild);
    } else {
      this.wrapper.insertBefore(container, this.wrapper.lastChild);
    }

    //this.wrapper.appendChild(container);
  }

  private buildStyles() {
    if (this.content.customStyles) {
      const containerStyles = this.content.customStyles.containerStyle || {};
      const fontStyles = this.content.customStyles.fontStyle || {};

      for (const key in containerStyles) {
        if (Object.prototype.hasOwnProperty.call(containerStyles, key)) {
          this.container.style.setProperty(key, containerStyles[key]);
        }
      }

      const textElement = this.container.querySelector(
        ".notifyme-description"
      ) as HTMLElement;
      if (textElement) {
        for (const key in fontStyles) {
          if (Object.prototype.hasOwnProperty.call(fontStyles, key)) {
            textElement.style.setProperty(key, fontStyles[key]);
          }
        }
      }
    }
  }

  private setPosition = (position: string): void => {
    const validPositions = ["topLeft", "topRight", "bottomLeft", "bottomRight"];

    if (validPositions.includes(position)) {
      this.position(this.wrapper, position);
    } else {
      this.position(this.wrapper, "topRight");
    }
  };

  private position(wrapper: HTMLElement, position: string) {
    switch (position) {
      case "topLeft":
        wrapper.style.top = "0";
        wrapper.style.left = "0";
        break;
      case "topRight":
        wrapper.style.top = "0";
        wrapper.style.right = "0";
        break;
      case "bottomLeft":
        wrapper.style.bottom = "0";
        wrapper.style.left = "0";
        break;
      case "bottomRight":
        wrapper.style.bottom = "0";
        wrapper.style.right = "0";
        break;
      case "centerTop":
        wrapper.style.top = "0";
        wrapper.style.left = "50%";
        wrapper.style.transform = "translateX(-50%)";
        break;
      case "centerBottom":
        wrapper.style.bottom = "0";
        wrapper.style.left = "50%";
        wrapper.style.transform = "translateX(-50%)";
        break;
    }
  }

  private fadeinContainer = (container: HTMLElement): void => {
    // Initially hide the notification container by setting its opacity to 0
    container.style.opacity = "0";

    // After a short delay, gradually increase the opacity to 1
    setTimeout(() => {
      container.style.transition = "opacity 2s";
      container.style.opacity = "1";
    }, 100);
  };

  private fadeoutContainer = (container: HTMLElement): void => {
    // Initially hide the notification container by setting its opacity to 0
    container.style.opacity = "1";

    // After a short delay, gradually increase the opacity to 1
    setTimeout(() => {
      container.style.transition = "opacity 2s";
      container.style.opacity = "0";
    }, 100);
  };
  
  private slideInContainer = (container: HTMLElement): void => {
    // Adicione a animação de slide-in (você pode personalizar isso)
    container.style.transform = "translateY(100%)";
    container.style.transition = "transform 0.5s ease";

    // Use um timeout para garantir que a transformação seja aplicada após a renderização inicial
    setTimeout(() => {
      container.style.transform = "translateY(0)";
    }, 100);
  };

  private slideOutContainer = (container: HTMLElement): void => {
    // Adicione a animação de slide-out (você pode personalizar isso)
    container.style.transform = "translateY(0)";
    container.style.transition = "transform 0.5s ease";

    // Use um timeout para garantir que a transformação seja aplicada após a renderização inicial
    setTimeout(() => {
      container.style.transform = "translateY(100%)";
    }, 100);
  };
  
  private onClose = (): void => {
    this.fadeoutContainer(this.container);
    setTimeout(() => {
      if (this.container) {
        this.container.remove();
        if (this.onCloseCallback) {
          this.onCloseCallback();
        }
      }
    }, 1000);
  };

  private startCloseTimeout = (): void => {
    if (this.content.duration === -1) return;

    this.closeTimeout = setTimeout(() => {
      this.onClose();
    }, this.content.duration);
  };

  private stopCloseTimeout = () => {
    clearTimeout(this.closeTimeout);
  };
}

function getRandomColor() {
  const r = Math.floor(Math.random() * 256); 
  const g = Math.floor(Math.random() * 256);
  const b = Math.floor(Math.random() * 256); 
  return `rgb(${r},${g},${b})`;
}

const customStyles = {
  containerStyle: {
    "background-color": getRandomColor(),
    "border-radius": "10px"
  },
  fontStyle: {
    color: getRandomColor()
  }
};

export const Notifymes = (content) => {
  new Notifyme(content);
};

// Chame o método AddNotification na instância notyfyme
Notifymes({
  type: "info",
  title: "Info",
  message:
    "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
  showIcon: true,
  duration: -1,
  customStyles: customStyles,
  position: "bottomLeft"
});

setInterval(() => {
  const customStyles = {
    containerStyle: {
      "background-color": getRandomColor(),
      "border-radius": "10px"
    },
    fontStyle: {
      color: getRandomColor()
    }
  };

  Notifymes({
    message: "Lorem ipsum",
    type: "error",
    title: "Error",
    showIcon: true,
    duration: 3000,
    customStyles: customStyles,
    position: "topRight"
  });
}, 2000);
