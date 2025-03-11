/**
 * Creates a time block for "block2time" and appends it to the document.
 * This function generates a structured HTML form element dynamically.
 */
function createTimeBlock2() {
    const container = document.createElement('div');
    container.id = 'fgroup_id_block2time';
    container.className = 'mb-3 row fitem';
    container.setAttribute('data-groupname', 'block2time');

    const labelDiv = document.createElement('div');
    labelDiv.className = 'col-md-3 col-form-label d-flex pb-0 pe-md-0';
    labelDiv.innerHTML = `
        <p id="fgroup_id_block2time_label" class="mb-0 word-break" aria-hidden="true" style="cursor: default;">
            Set second time block
        </p>
        <div class="form-label-addon d-flex align-items-center align-self-start"></div>
    `;

    const inputDiv = document.createElement('div');
    inputDiv.className = 'col-md-9 d-flex flex-wrap align-items-start felement';
    inputDiv.setAttribute('data-fieldtype', 'group');

    const fieldset = document.createElement('fieldset');
    fieldset.className = 'w-100 m-0 p-0 border-0';
    fieldset.innerHTML = `<legend class="sr-only">Set second time block</legend>`;

    const timeDiv = document.createElement('div');
    timeDiv.className = 'd-flex flex-wrap align-items-center';

    timeDiv.innerHTML = `
        From: 
        ${createSelect('starthour2', 0, 23)}
        ${createSelect('startminute2', 0, 59)}
        - To: 
        ${createSelect('endhour2', 0, 23)}
        ${createSelect('endminute2', 0, 59)}
    `;

    fieldset.appendChild(timeDiv);
    inputDiv.appendChild(fieldset);
    container.appendChild(labelDiv);
    container.appendChild(inputDiv);

    const sectionField = document.getElementById('id_sessionconfigcontainer');
    sectionField.appendChild(container);
}

/**
 * Creates a select dropdown element with numeric options.
 *
 * @param {string} name - The name and ID for the select element.
 * @param {number} min - The minimum value for the select options.
 * @param {number} max - The maximum value for the select options.
 * @returns {string} The HTML string for the select element.
 */
function createSelect(name, min, max) {
    let selectHTML= `<div class="mb-3  fitem" >`;
    selectHTML += `<select class="custom-select" name="${name}" id="id_${name}">`;
    for (let i = min; i <= max; i++) {
        selectHTML += `<option value="${i}">${i}</option>`;
    }
    selectHTML += `</select>`;
    selectHTML += `</div>`;

    return selectHTML;
}


define([], function() {
    return {
        init: function() {
            document.getElementById('id_addsessionblocks').addEventListener('change', function() {
                if (this.value === "1") {
                    const secondBlock = document.getElementById("fgroup_id_block2time");

                    if (secondBlock) {
                        secondBlock.remove();
                    }
                }
                if (this.value === '2') {
                    createTimeBlock2();
                }
            });
        }
    };
});