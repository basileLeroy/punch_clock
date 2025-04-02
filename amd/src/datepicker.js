const datepicker = document.querySelector('.datepicker');
const dateInput = document.querySelector('.date-input');
const yearInput = datepicker.querySelector('.year-input');
const monthInput = datepicker.querySelector('.month-input');
const applyBtn = datepicker.querySelector('.apply');
const cancelBtn = datepicker.querySelector('.cancel');
const prevBtn = datepicker.querySelector(".prev");
const nextBtn = datepicker.querySelector(".next");
const dates = datepicker.querySelector(".dates");

let selectedDate = new Date();
let year = selectedDate.getFullYear();
let month = selectedDate.getMonth();

/**
 * Updates the year and month input fields to match the currently selected date.
 */
const updateYearMonth = () => {
    monthInput.selectedIndex = month;
    yearInput.value = year;
};

/**
 * Handles the click event on a date button.
 *
 * @param {Event} event - The event object from the click event.
 */
const handleDateClick = (event) => {
    event.preventDefault();
    const button = event.target;

    const selected = dates.querySelector('.selected');
    if (selected) {
        selected.classList.remove("selected");
    }

    button.classList.add("selected");

    selectedDate = new Date(year, month, parseInt(button.textContent));
};

/**
 * Displays the calendar dates for the selected month and year.
 */
const displayDates = () => {
    updateYearMonth();
    dates.innerHTML = "";

    const lastOfPrevMonth = new Date(year, month, 0);

    for (let i = 0; i <= lastOfPrevMonth.getDay(); i++) {
        const text = lastOfPrevMonth.getDate() - lastOfPrevMonth.getDay() + i;
        const button = createBtn(text, true, false);
        dates.appendChild(button);
    }

    const lastOfMonth = new Date(year, month + 1, 0);

    for (let i = 1; i <= lastOfMonth.getDate(); i++) {
        const isToday =
            selectedDate.getDate() === i &&
            selectedDate.getFullYear() === year &&
            selectedDate.getMonth() === month;

        const button = createBtn(i, false, isToday);
        button.addEventListener("click", handleDateClick);
        dates.appendChild(button);
    }

    const firstOfNextMonth = new Date(year, month + 1, 1);

    for (let i = firstOfNextMonth.getDay(); i < 7; i++) {
        const text = firstOfNextMonth.getDate() - firstOfNextMonth.getDay() + i;
        const button = createBtn(text, true, false);
        dates.appendChild(button);
    }
};

/**
 * Creates a button element for a date.
 *
 * @param {number} text - The text content of the button (day of the month).
 * @param {boolean} [isDisabled=false] - Whether the button should be disabled.
 * @param {boolean} [isToday=false] - Whether the button represents today's date.
 * @returns {HTMLButtonElement} - The created button element.
 */
const createBtn = (text, isDisabled = false, isToday = false) => {
    const btn = document.createElement('button');
    btn.textContent = text;
    btn.disabled = isDisabled;
    btn.classList.toggle("today", isToday);

    return btn;
};

define([], function() {
    return {
        init: function() {
            displayDates();

            monthInput.addEventListener('change', (event) => {
                event.preventDefault();
                month = monthInput.selectedIndex;
                displayDates();
            });

            yearInput.addEventListener('change', (event) => {
                event.preventDefault();
                year = yearInput.value;
                displayDates();
            });

            nextBtn.addEventListener('click', (event) => {
                event.preventDefault();
                if (month === 11) {
                    year++;
                }
                month = (month + 1) % 12;
                displayDates();
            });

            prevBtn.addEventListener('click', (event) => {
                event.preventDefault();
                if (month === 0) {
                    year--;
                }
                month = (month - 1 + 12) % 12;
                displayDates();
            });

            dateInput.addEventListener('click', (event) => {
                event.preventDefault();
                datepicker.hidden = !datepicker.hidden;
            });

            applyBtn.addEventListener('click', (event) => {
                event.preventDefault();

                const unixTimestamp = Math.floor(Date.UTC(
                    selectedDate.getFullYear(),
                    selectedDate.getMonth(),
                    selectedDate.getDate(),
                    selectedDate.getHours(),
                    selectedDate.getMinutes(),
                    selectedDate.getSeconds()
                ) / 1000);

                const url = new URL(window.location);
                const params = new URLSearchParams(url.search);

                params.set("date", unixTimestamp);

                window.location.href = `${url.pathname}?${params.toString()}`;

                datepicker.hidden = true;
            });

            cancelBtn.addEventListener('click', (event) => {
                event.preventDefault();
                datepicker.hidden = true;
            });
        }
    };
});