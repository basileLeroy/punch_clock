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
            let addTimeBlockDefaultButton = document.getElementById("id_timeblock_add_fields");
            let addTimeBlockCustomButton = document.getElementById("add-timeblock-button");
            let addExceptionDefaultButton = document.getElementById("id_exception_add_fields");
            let addExceptionCustomButton = document.getElementById("add-holiday-button");

            removeDefaultButtons([addExceptionDefaultButton, addTimeBlockDefaultButton]);

            redirectCustomButtonToDefaultAction(addTimeBlockCustomButton, addTimeBlockDefaultButton);
            redirectCustomButtonToDefaultAction(addExceptionCustomButton, addExceptionDefaultButton);

            const timeblockCounter = document.querySelector('[name="timeblock_repeats"]');
            const exceptionsCounter = document.querySelector('[name="exception_repeats"]');

            updateButtonVisibility(addTimeBlockCustomButton, timeblockCounter, 2);
            updateButtonVisibility(addExceptionCustomButton, exceptionsCounter, 4);
        }
    };
});