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
        if (id === "payment_method_sl_dcash_gateway") {
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
    if (document.querySelector("#payment_method_sl_dcash_gateway")?.checked) {
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
   * Check that the fields that need to be filled in are actually so.
   *
   * @see SoaringLeads\DCashWC\Controllers\Frontend\Ajax\Checkout::validateForm()
   */
  function validateFields() {
    document
      .querySelector("#sl-dcash-btn")
      .addEventListener("click", function (e) {
        // Add missing checkout fields that should always be there
        const formElement = document.querySelector(
          ".checkout.woocommerce-checkout"
        );
        const formData = new FormData(formElement);
        const formObject = Object.fromEntries(formData.entries());

        wp.ajax
          .post("dCashValidateCheckout", { checkoutFormFields: formObject })
          .done(function (response) {
            // console.log(response);
            triggerDCashModal();
          })
          .fail(function (response) {
            console.log(response);
            // alert(response.toString());
          });
      });
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
