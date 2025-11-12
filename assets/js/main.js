//form Input logic
document.addEventListener("DOMContentLoaded", function () {
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");

  // set current month as default
  const now = new Date();
  // "YYYY-MM"
  const currentMonth = now.toISOString().slice(0, 7);
  console.log(currentMonth);
  startDate.value = currentMonth;
  endDate.value = currentMonth;

  // set min of endDate to startDate initially
  endDate.min = startDate.value;

  // update min of endDate dynamically when startDate changes
  startDate.addEventListener("change", () => {
    endDate.min = startDate.value;
    if (endDate.value < startDate.value) {
      endDate.value = startDate.value;
    }
  });

  // validation check for ascending order (live feedback)
  endDate.addEventListener("change", () => {
    if (endDate.value < startDate.value) {
      endDate.setCustomValidity("End month must be after start month.");
      endDate.reportValidity();
    } else {
      endDate.setCustomValidity("");
    }
  });
});

//form Input Logic
