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
    const placeOrderDiv = document.querySelector(".place-order");
    placeOrderDiv.appendChild(dCashBtn);
    if (document.querySelector("#payment_method_sl_dcash_gateway")?.checked) {
      dCashBtn.style.display = "flex";
    } else {
      document.querySelector("#place_order").classList.remove("hidden");
    }
  }

  /**
   * Check that the checkout fields that need to be filled in are actually so.
   */
  function validateFields() {
    document
      .querySelector("#sl-dcash-btn")
      .addEventListener("click", function (e) {
        $(".validate-required input, .validate-required select").trigger(
          "validate"
        );

        // TODO this checks shipping fields as well...we need to handle cases where ship to a different address is not enabled.
        // Possible fix is to check if shipping to dif address is enabled and then run through every invalidated field and drop shipping ones
        var invalidFields = document.querySelectorAll(
          ".woocommerce-invalid-required-field"
        );

        if (invalidFields.length > 0) {
          alert("fix errors");
        } else {
          document
            .querySelector("#dcash-button")
            .shadowRoot.querySelector("#payWithEcommerceButton")
            .click();
          document
            .querySelector("#sl-dcash-container")
            .appendChild(
              document
                .querySelector("#dcash-button")
                .shadowRoot.querySelector("#modalContainer")
            );
        }

        // wp.ajax
        // .post("dCashValidateCheckout", {})
        // .done(function (response) {
        //   console.log(response);
        //   // const show = Boolean(response);
        //   // changeMapVisibility(show);
        // })
        // .fail(function (response) {
        //   console.log(response);
        // });
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
        clearInterval(dCashBtn);
      }
    }, 500);
  });

  $(document).ready(function () {});
})(jQuery);
