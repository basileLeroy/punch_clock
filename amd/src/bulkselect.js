/**
 * @fileoverview Handles bulk selection and updating button counts for bulk edit and delete actions.
 */

const bulkEditBtn = document.querySelector('.bulkeditbtn'); // Button for editing selected items
const bulkDeleteBtn = document.querySelector('.bulkdeletebtn'); // Button for deleting selected items
const selectAll = document.querySelector(".selectallrows"); // "Select All" checkbox
const listOfRows = document.querySelectorAll(".selectrow"); // List of row checkboxes

/**
 * Toggles the selection of all checkboxes based on the "Select All" checkbox state.
 */
const toggleAllCheckboxes = () => {
    if (!selectAll) {
        return;
    }
    const isChecked = selectAll.checked;

    listOfRows.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
};

/**
 * Updates the text content of the bulk edit and delete buttons based on the number of selected rows.
 */
const updateBtnCount = () => {
    if (!bulkEditBtn || !bulkDeleteBtn) {
        return;
    }

    const selectedCount = document.querySelectorAll(".selectrow:checked").length;
    bulkEditBtn.textContent = `Edit Selected (${selectedCount})`;
    bulkDeleteBtn.textContent = `Delete Selected (${selectedCount})`;

    const isDisabled = selectedCount === 0;
    bulkEditBtn.disabled = isDisabled;
    bulkDeleteBtn.disabled = isDisabled;
};

/**
 * Remove default buttons since custom buttons are used.
 * @param {Array} buttons - List of buttons to remove
 */
function removeDefaultButtons(buttons) {
    buttons.forEach(button => {
        if (button) {
            let buttonBox = button.parentElement.parentElement;
            buttonBox.className = "d-none";
        }
    });
}

/**
 * Use the click event of the custom button to trigger the default click action.
 * @param {HTMLElement} customButton - The custom button
 * @param {HTMLElement} defaultButton - The default button
 */
function redirectCustomButtonToDefaultAction(customButton, defaultButton) {
    if (customButton && defaultButton) {
        customButton.addEventListener("click", function() {
            defaultButton.click();
        });
    }
}

/**
 * Time Block Button Visibilty controller. Hides the button once a second time block has been added.
 * @param {HTMLElement} button - The button elelement to add new timeblocks
 * @param {HTMLElement} counterElement - A counter field keeping track of the time blocks
 * @param {int} maxCount - The max amount of sections allowed before hiding the button
 */
function updateButtonVisibility(button, counterElement, maxCount) {
    let counter = parseInt(counterElement.value, 10);

    if (counter >= maxCount) {
        let buttonBox = button.parentElement.parentElement;
        buttonBox.className = "d-none";
    }
}

define([], function() {
    return {
        init: function() {
            if (!selectAll || !bulkEditBtn || !bulkDeleteBtn) {
                return;
            }

            /**
             * Event listener for the "Select All" checkbox.
             * When clicked, it toggles all individual row checkboxes and updates the button count.
             */
            selectAll.addEventListener("click", () => {
                toggleAllCheckboxes();
                updateBtnCount();
            });

            /**
             * Event listener for each row checkbox.
             * When clicked, it updates the bulk action button counts.
             */
            listOfRows.forEach(checkbox => {
                checkbox.addEventListener("click", updateBtnCount);
            });

            // Initialize button count on page load
            updateBtnCount();
        },
        bulk: function() {
            let addTimeBlockDefaultButton = document.getElementById("id_timeblock_add_fields");
            let addTimeBlockCustomButton = document.getElementById("add-timeblock-button");

            removeDefaultButtons([addTimeBlockDefaultButton]);
            redirectCustomButtonToDefaultAction(addTimeBlockCustomButton, addTimeBlockDefaultButton);

            const timeblockCounter = document.querySelector('[name="timeblock_repeats"]');

            updateButtonVisibility(addTimeBlockCustomButton, timeblockCounter, 2);
        }
    };
});