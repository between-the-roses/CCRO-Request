// AdminDashboard/transactions/assets/calendar.js
(function() {
  const calendarBody  = document.getElementById("calendar-body");
  const calendarTitle = document.getElementById("calendar-title");
  const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
  let currentMonth = new Date().getMonth();
  let currentYear  = new Date().getFullYear();
  const events = window.CALENDAR_EVENTS || {};

  function generateCalendar(year, month) {
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const today = new Date();

    calendarBody.innerHTML = "";
    calendarTitle.textContent = `${monthNames[month]} ${year}`;
    let date = 1;

    for (let i = 0; i < 6; i++) {
      const row = document.createElement("tr");
      for (let j = 0; j < 7; j++) {
        const cell = document.createElement("td");
        if (i === 0 && j < firstDay || date > lastDate) {
          cell.textContent = "";
        } else {
          cell.textContent = date;
          const d = `${year}-${String(month+1).padStart(2,'0')}-${String(date).padStart(2,'0')}`;
          if (events[d]) {
            cell.classList.add("has-events");
            cell.title = `${events[d].count} request(s): ${events[d].types}`;
          }
          if (year === today.getFullYear() && month === today.getMonth() && date === today.getDate()) {
            cell.classList.add("highlight-green");
          }
          date++;
        }
        row.appendChild(cell);
      }
      calendarBody.appendChild(row);
      if (date > lastDate) break;
    }
  }

  window.prevMonth = function() {
    currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    generateCalendar(currentYear, currentMonth);
  }
  window.nextMonth = function() {
    currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    generateCalendar(currentYear, currentMonth);
  }

  document.addEventListener('DOMContentLoaded', () => generateCalendar(currentYear, currentMonth));
})();
