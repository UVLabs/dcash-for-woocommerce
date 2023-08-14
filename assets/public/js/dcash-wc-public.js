(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  /**
   * Block the UI like how WC does it.
   */
  function blockUI() {
    $(".woocommerce")
      .addClass("processing")
      .block({
        message: null,
        overlayCSS: {
          background: "#fff",
          opacity: 0.6,
        },
      });
  }

  /**
   * Unblock the UI.
   */
  function unblockUI() {
    $(".woocommerce")
      .addClass("processing")
      .unblock({
        message: null,
        overlayCSS: {
          background: "#fff",
          opacity: 0.6,
        },
      });
  }

  /**
   * Handle when a customer switches between different shipping methods.
   */
  function handleShippingMethodChanged() {
    $(document.body).on("updated_checkout", function () {
      const oldDCashBtn = moveDCashBtn();
      const newDCashBtn = document.querySelector("#sl-dcash-btn");
      const placeOrderDiv = document.querySelector("#order_review");
      placeOrderDiv.replaceChild(newDCashBtn, oldDCashBtn); // So we don't have multiple DCash buttons on page.

      handlePaymentOptionChange(); // Add event listeners back since the DOM refreshes.
    });
  }

  /**
   * Handle when a customer switches payment methods.
   */
  function handlePaymentOptionChange() {
    const paymentMethods = document.querySelectorAll(
      ".wc_payment_methods li input"
    );

    paymentMethods.forEach((paymentMethod) => {
      paymentMethod.addEventListener("click", function (e) {
        const id = e.target.id;
        const dCashBtn = document.querySelector("#sl-dcash-btn");
        const placeOrderBtn = document.querySelector("#place_order");
        if (id === "payment_method_dcash_for_wc_gateway") {
          dCashBtn.style.display = "flex";
          placeOrderBtn.classList.add("hidden");
        } else {
          dCashBtn.style.display = "none";
          placeOrderBtn.classList.remove("hidden");
        }
      });
    });
  }

  /**
   * Move the DCash button to the same area as where the "Place order" button typically appears.
   */
  function moveDCashBtn() {
    const dCashBtn = document.querySelector("#sl-dcash-btn");
    const orderReviewContainer = document.querySelector("#order_review");
    orderReviewContainer.appendChild(dCashBtn);
    if (
      document.querySelector("#payment_method_dcash_for_wc_gateway")?.checked
    ) {
      dCashBtn.style.display = "flex";
    } else {
      document.querySelector("#place_order").classList.remove("hidden");
    }
    return dCashBtn;
  }

  /**
   * Trigger the DCash modal.
   */
  function triggerDCashModal() {
    // Remove any errors once the modal is triggered
    const errorsDiv = document.querySelector("#sl-dcash-wc-errors");
    if (errorsDiv) {
      errorsDiv.remove();
    }

    unblockUI();

    document
      .querySelector("#dcash-button")
      .shadowRoot.querySelector("#payWithEcommerceButton")
      .click();

    // The modal div is created after the DCash JS button is clicked.
    document
      .querySelector("#sl-dcash-container")
      .appendChild(
        document
          .querySelector("#dcash-button")
          .shadowRoot.querySelector("#modalContainer")
      );
  }

  /**
   * Show errors recieved from PHP after validating the checkout form.
   *
   * @param {string} errors
   */
  function showWCErrors(errors) {
    unblockUI();

    // console.log(errors);
    if (typeof errors !== "string") {
      console.error("Errors need to be a string value");
      alert(
        "An error occurred, ensure that all information is filled in correctly. Please contact us this message continues to appear."
      );
      location.reload();
      return;
    }

    const html = `
    <div id='sl-dcash-wc-errors' class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
    <ul class="woocommerce-error" role="alert">
    ${errors}
    </ul>
    </div>
    `;

    // Remove default WooCommerce errors if present. We are going to be showing all errors in our new errors Div.
    const defaultErrorsDiv = document.querySelector(
      ".woocommerce-NoticeGroup-checkout"
    );
    if (defaultErrorsDiv) {
      defaultErrorsDiv.remove();
    }

    const errorsDiv = document.querySelector("#sl-dcash-wc-errors");
    if (errorsDiv) {
      errorsDiv.remove();
    }

    document
      .querySelector(".checkout.woocommerce-checkout")
      .insertAdjacentHTML("afterbegin", html);

    const container = document.querySelector(".woocommerce");
    if (container) {
      document
        .querySelector(".woocommerce")
        .scrollIntoView({ behavior: "smooth" });
    } else {
      document
        .querySelector("#sl-dcash-wc-errors")
        .scrollIntoView({ behavior: "smooth" });
    }
  }

  /**
   * Check that the fields that need to be filled in are actually so.
   *
   * @see SoaringLeads\DCashWC\Controllers\Frontend\Ajax\Checkout::validateForm()
   */
  function validateFields() {
    document
      .querySelector("#sl-dcash-btn")
      .addEventListener("click", function (e) {
        blockUI();

        // Add missing checkout fields that should always be there
        const formElement = document.querySelector(
          ".checkout.woocommerce-checkout"
        );
        const formData = new FormData(formElement);
        const formObject = Object.fromEntries(formData.entries());

        const shipToDifferentAddressEl = document.querySelector(
          "#ship-to-different-address-checkbox"
        );
        let shipToDifferentAddress = false;

        if (shipToDifferentAddressEl) {
          if (shipToDifferentAddressEl.checked) {
            shipToDifferentAddress = true;
          }
        }

        formObject.ship_to_different_address = shipToDifferentAddress;
        /**
         * This is field only POSTed when JS is not available in the browser.
         * Since we depend on JS then this will always be false.
         * see woocommerce/templates/checkout/payment.php
         */
        formObject.woocommerce_checkout_update_totals = false;

        wp.ajax
          .post("dCashValidateCheckout", { checkoutFormFields: formObject })
          .done(function (response) {
            // console.log(response);
            triggerDCashModal();
          })
          .fail(function (response) {
            // console.log(response);
            showWCErrors(response);
          });
      });

    unblockUI(); // Always unblock at end
  }

  /**
   * Bootstrap our logic when button is found in DOM.
   */
  $(window).load(function () {
    const dCashBtn = setInterval(function () {
      if (document.querySelector("#sl-dcash-btn")) {
        validateFields();
        moveDCashBtn();
        handlePaymentOptionChange();
        handleShippingMethodChanged();
        clearInterval(dCashBtn);
      }
    }, 500);
  });

  $(document).ready(function () {});
})(jQuery);
