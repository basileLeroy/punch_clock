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
};

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
        }
    };
});