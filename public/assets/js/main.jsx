/**
 * Mega School Plaza - Main JavaScript
 *
 * Core functionality and utilities
 */

// Main App Object
const MegaPlaza = {
  // Configuration
  config: {
    baseUrl: window.location.origin,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),
    currency: "₦",
  },

  // Initialize the application
  init() {
    this.setupEventListeners()
    this.initializeComponents()
    this.handleFlashMessages()
  },

  // Setup global event listeners
  setupEventListeners() {
    // Handle form submissions with loading states
    document.addEventListener("submit", this.handleFormSubmit.bind(this))

    // Handle AJAX requests
    document.addEventListener("click", this.handleAjaxClick.bind(this))

    // Handle file uploads
    document.addEventListener("change", this.handleFileUpload.bind(this))

    // Handle search inputs with debouncing
    document.addEventListener("input", this.handleSearchInput.bind(this))
  },

  // Initialize components
  initializeComponents() {
    this.initModals()
    this.initTooltips()
    this.initTabs()
    this.initAccordions()
  },

  // Handle form submissions
  handleFormSubmit(event) {
    const form = event.target
    if (!form.matches("form[data-loading]")) return

    const submitBtn = form.querySelector('button[type="submit"]')
    if (submitBtn) {
      submitBtn.disabled = true
      submitBtn.innerHTML = '<span class="spinner"></span> Loading...'
    }

    // Re-enable after 5 seconds as fallback
    setTimeout(() => {
      if (submitBtn) {
        submitBtn.disabled = false
        submitBtn.innerHTML = submitBtn.dataset.originalText || "Submit"
      }
    }, 5000)
  },

  // Handle AJAX clicks
  handleAjaxClick(event) {
    const element = event.target.closest("[data-ajax]")
    if (!element) return

    event.preventDefault()

    const url = element.getAttribute("href") || element.dataset.url
    const method = element.dataset.method || "GET"
    const confirm = element.dataset.confirm

    if (confirm && !window.confirm(confirm)) {
      return
    }

    this.makeAjaxRequest(url, method, null, element)
  },

  // Handle file uploads
  handleFileUpload(event) {
    const input = event.target
    if (!input.matches('input[type="file"]')) return

    const files = input.files
    const maxSize = Number.parseInt(input.dataset.maxSize) || 5242880 // 5MB default
    const allowedTypes = input.dataset.allowedTypes?.split(",") || []

    for (const file of files) {
      if (file.size > maxSize) {
        this.showAlert("File size too large. Maximum size is " + this.formatFileSize(maxSize), "error")
        input.value = ""
        return
      }

      if (allowedTypes.length && !allowedTypes.includes(file.type)) {
        this.showAlert("File type not allowed. Allowed types: " + allowedTypes.join(", "), "error")
        input.value = ""
        return
      }
    }

    // Show file preview if it's an image
    if (files[0] && files[0].type.startsWith("image/")) {
      this.showImagePreview(input, files[0])
    }
  },

  // Handle search input with debouncing
  handleSearchInput(event) {
    const input = event.target
    if (!input.matches("[data-search]")) return

    clearTimeout(input.searchTimeout)
    input.searchTimeout = setTimeout(() => {
      this.performSearch(input)
    }, 300)
  },

  // Perform search
  performSearch(input) {
    const query = input.value.trim()
    const target = input.dataset.search
    const minLength = Number.parseInt(input.dataset.minLength) || 2

    if (query.length < minLength) {
      this.clearSearchResults(target)
      return
    }

    // Show loading state
    this.showSearchLoading(target)

    // Make search request
    this.makeAjaxRequest("/api/search", "GET", { q: query, type: target })
      .then((data) => {
        this.displaySearchResults(target, data)
      })
      .catch((error) => {
        console.error("Search error:", error)
        this.clearSearchResults(target)
      })
  },

  // Make AJAX request
  async makeAjaxRequest(url, method = "GET", data = null, element = null) {
    try {
      const options = {
        method: method,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      }

      if (this.config.csrfToken) {
        options.headers["X-CSRF-Token"] = this.config.csrfToken
      }

      if (data && method !== "GET") {
        options.body = JSON.stringify(data)
      } else if (data && method === "GET") {
        url += "?" + new URLSearchParams(data).toString()
      }

      // Show loading state on element
      if (element) {
        element.classList.add("loading")
      }

      const response = await fetch(url, options)
      const result = await response.json()

      if (!response.ok) {
        throw new Error(result.message || "Request failed")
      }

      // Handle different response types
      if (result.redirect) {
        window.location.href = result.redirect
        return
      }

      if (result.reload) {
        window.location.reload()
        return
      }

      if (result.message) {
        this.showAlert(result.message, result.type || "success")
      }

      return result
    } catch (error) {
      console.error("AJAX Error:", error)
      this.showAlert(error.message || "An error occurred", "error")
      throw error
    } finally {
      if (element) {
        element.classList.remove("loading")
      }
    }
  },

  // Show alert message
  showAlert(message, type = "info", duration = 5000) {
    const alertContainer = document.getElementById("alert-container") || this.createAlertContainer()

    const alert = document.createElement("div")
    alert.className = `alert alert--${type}`
    alert.innerHTML = `
            <span>${message}</span>
            <button type="button" class="alert__close" onclick="this.parentElement.remove()">×</button>
        `

    alertContainer.appendChild(alert)

    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        if (alert.parentElement) {
          alert.remove()
        }
      }, duration)
    }
  },

  // Create alert container
  createAlertContainer() {
    const container = document.createElement("div")
    container.id = "alert-container"
    container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
        `
    document.body.appendChild(container)
    return container
  },

  // Handle flash messages
  handleFlashMessages() {
    const flashMessages = document.querySelectorAll("[data-flash]")
    flashMessages.forEach((message) => {
      const type = message.dataset.flash
      const text = message.textContent.trim()
      if (text) {
        this.showAlert(text, type)
      }
      message.remove()
    })
  },

  // Format currency
  formatCurrency(amount) {
    return this.config.currency + new Intl.NumberFormat("en-NG").format(amount)
  },

  // Format file size
  formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes"
    const k = 1024
    const sizes = ["Bytes", "KB", "MB", "GB"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
  },

  // Show image preview
  showImagePreview(input, file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      let preview = input.parentElement.querySelector(".image-preview")
      if (!preview) {
        preview = document.createElement("div")
        preview.className = "image-preview"
        input.parentElement.appendChild(preview)
      }

      preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 4px; margin-top: 8px;">
                <button type="button" onclick="this.parentElement.remove(); document.querySelector('input[type=file]').value = '';" style="display: block; margin-top: 4px; font-size: 12px;">Remove</button>
            `
    }
    reader.readAsDataURL(file)
  },

  // Initialize modals
  initModals() {
    document.addEventListener("click", (e) => {
      if (e.target.matches("[data-modal]")) {
        e.preventDefault()
        const modalId = e.target.dataset.modal
        this.openModal(modalId)
      }

      if (e.target.matches(".modal__close, .modal__backdrop")) {
        this.closeModal(e.target.closest(".modal"))
      }
    })

    // Close modal on Escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const openModal = document.querySelector(".modal.modal--open")
        if (openModal) {
          this.closeModal(openModal)
        }
      }
    })
  },

  // Open modal
  openModal(modalId) {
    const modal = document.getElementById(modalId)
    if (modal) {
      modal.classList.add("modal--open")
      document.body.style.overflow = "hidden"
    }
  },

  // Close modal
  closeModal(modal) {
    if (modal) {
      modal.classList.remove("modal--open")
      document.body.style.overflow = ""
    }
  },

  // Initialize tooltips
  initTooltips() {
    const tooltips = document.querySelectorAll("[data-tooltip]")
    tooltips.forEach((element) => {
      element.addEventListener("mouseenter", this.showTooltip.bind(this))
      element.addEventListener("mouseleave", this.hideTooltip.bind(this))
    })
  },

  // Show tooltip
  showTooltip(event) {
    const element = event.target
    const text = element.dataset.tooltip

    const tooltip = document.createElement("div")
    tooltip.className = "tooltip"
    tooltip.textContent = text
    tooltip.style.cssText = `
            position: absolute;
            background: #333;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1000;
            pointer-events: none;
        `

    document.body.appendChild(tooltip)

    const rect = element.getBoundingClientRect()
    tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px"
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + "px"

    element._tooltip = tooltip
  },

  // Hide tooltip
  hideTooltip(event) {
    const element = event.target
    if (element._tooltip) {
      element._tooltip.remove()
      delete element._tooltip
    }
  },

  // Initialize tabs
  initTabs() {
    document.addEventListener("click", (e) => {
      if (e.target.matches(".tab__button")) {
        e.preventDefault()
        this.switchTab(e.target)
      }
    })
  },

  // Switch tab
  switchTab(button) {
    const tabGroup = button.closest(".tabs")
    const targetId = button.dataset.tab

    // Update buttons
    tabGroup.querySelectorAll(".tab__button").forEach((btn) => {
      btn.classList.remove("tab__button--active")
    })
    button.classList.add("tab__button--active")

    // Update content
    tabGroup.querySelectorAll(".tab__content").forEach((content) => {
      content.classList.remove("tab__content--active")
    })

    const targetContent = document.getElementById(targetId)
    if (targetContent) {
      targetContent.classList.add("tab__content--active")
    }
  },

  // Initialize accordions
  initAccordions() {
    document.addEventListener("click", (e) => {
      if (e.target.matches(".accordion__button")) {
        e.preventDefault()
        this.toggleAccordion(e.target)
      }
    })
  },

  // Toggle accordion
  toggleAccordion(button) {
    const item = button.closest(".accordion__item")
    const content = item.querySelector(".accordion__content")
    const isOpen = item.classList.contains("accordion__item--open")

    if (isOpen) {
      item.classList.remove("accordion__item--open")
      content.style.maxHeight = null
    } else {
      item.classList.add("accordion__item--open")
      content.style.maxHeight = content.scrollHeight + "px"
    }
  },
}

// Initialize when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => MegaPlaza.init())
} else {
  MegaPlaza.init()
}

// Export for global access
window.MegaPlaza = MegaPlaza
