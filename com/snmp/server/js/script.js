// Confirm before updating SNMP values
document.addEventListener("DOMContentLoaded", function () {
  // Get the SNMP update form if it exists
  const snmpUpdateForm = document.getElementById("snmpUpdateForm");

  if (snmpUpdateForm) {
    snmpUpdateForm.addEventListener("submit", function (e) {
      if (!confirm("Are you sure you want to update these SNMP values?")) {
        e.preventDefault();
      }
    });
  }

  // Add auto-refresh functionality for TCP connections and ICMP stats
  const refreshButton = document.getElementById("refreshButton");
  if (refreshButton) {
    refreshButton.addEventListener("click", function () {
      location.reload();
    });
  }

  // Add auto-refresh interval selector
  const refreshInterval = document.getElementById("refreshInterval");
  if (refreshInterval) {
    refreshInterval.addEventListener("change", function () {
      const seconds = parseInt(this.value);
      if (seconds > 0) {
        // Clear any existing interval
        if (window.refreshTimer) {
          clearInterval(window.refreshTimer);
        }

        // Set new interval
        window.refreshTimer = setInterval(function () {
          location.reload();
        }, seconds * 1000);

        // Show feedback
        const feedbackElement = document.getElementById("refreshFeedback");
        if (feedbackElement) {
          feedbackElement.textContent = `Auto-refreshing every ${seconds} seconds`;
          feedbackElement.style.display = "block";
        }
      } else {
        // Clear interval if "Off" is selected
        if (window.refreshTimer) {
          clearInterval(window.refreshTimer);
          window.refreshTimer = null;

          // Hide feedback
          const feedbackElement = document.getElementById("refreshFeedback");
          if (feedbackElement) {
            feedbackElement.style.display = "none";
          }
        }
      }
    });
  }
});
