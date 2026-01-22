jQuery(function ($) {
  // Function to check and show popup
  function checkPopup() {
    $.post(LPAC.ajaxUrl, { action: "lpac_should_popup", nonce: LPAC.nonce }, function (res) {
      if (!res || !res.success || !res.data.show) return;

      const code = res.data.code;

      // Fetch certificate details
      $.post(LPAC.ajaxUrl, { action: "lpac_get_cert", nonce: LPAC.nonce, code }, function (r2) {
        if (!r2 || !r2.success) return;

        const d = r2.data;

        // Prevent duplicate modals
        if ($("#lpac-modal").length) return;

        // Minimal modal (replace with your UI framework)
        const html = `
          <div id="lpac-modal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;max-width:520px;width:92%;border-radius:16px;padding:18px;">
              <h3 style="margin:0 0 8px 0;">Certificate unlocked</h3>
              <p style="margin:0 0 14px 0;">Score: ${d.percent}%</p>
              <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="${d.verifyUrl}" target="_blank" style="padding:10px 12px;border:1px solid #ddd;border-radius:10px;text-decoration:none;">View / Download</a>
                <button id="lpac-close" style="padding:10px 12px;border:1px solid #ddd;border-radius:10px;background:#f7f7f7;">Close</button>
              </div>
            </div>
          </div>
        `;
        $("body").append(html);
        $("#lpac-close").on("click", function () { $("#lpac-modal").remove(); });
        $("#lpac-modal").on("click", function (e) { if (e.target.id === "lpac-modal") $("#lpac-modal").remove(); });
      });
    });
  }

  // Initial check on page load
  checkPopup();

  // Listen for LearnPress Quiz Completion (AJAX / React Store)
  if (typeof wp !== 'undefined' && wp.data) {
    const unsubscribe = wp.data.subscribe(function () {
      try {
        const store = wp.data.select('learnpress/quiz');
        if (!store) return;

        const status = store.getData('status');
        
        // If status changes to completed, check for certificate
        if (status === 'completed') {
          // Use a small timeout to ensure server-side hooks occurred
          // We check a global flag to ensure we don't spam checks in case subscribe fires multiple times
          if (!window.lpacCheckerRun) {
            window.lpacCheckerRun = true;
            setTimeout(function() {
                checkPopup();
                window.lpacCheckerRun = false;
            }, 500); 
          }
        }
      } catch (e) {
        // Store might not exist yet or other error, ignore
      }
    });
  }
});
