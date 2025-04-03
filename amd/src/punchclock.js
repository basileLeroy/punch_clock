/**
 * @module mod_punchclock/punchclock
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Initialize punchclock timer, handle click events and send to DB via AJAX
 */
export const init = () => {
    const clockIds = ['morning-start', 'afternoon-start'];
    const intervals = {};

    clockIds.forEach(type => {
        const clock = document.getElementById(`clock-${type}`);
        const button = document.getElementById(`punchclock-button-${type}`);
        /**
         * Update each clocks with current time
        */
       function updateClock() {
           const now = new Date();
           clock.innerText = now.toLocaleTimeString();
        }
        updateClock(); // Set initial time immediately
        intervals[type] = setInterval(updateClock, 1000); // Store interval to catch

        // Stop clock on click
        button.addEventListener('click', () => {
            clearInterval(intervals[type]);
            button.disabled = true;

            const punchedTime = clock.innerText;
            const courseId = button.dataset.courseid;
            const punchclockId = button.dataset.punchclockid;

            // Send AJAX request to log punch time
            Ajax.call([{
                methodname: 'mod_punchclock_log_punch',
                args: {
                    courseid: courseId,
                    punchclockid: punchclockId,
                    type: type,
                    time: punchedTime
                }
            }])[0].then(response => {
                console.log('Punch logged:', response);
            }).catch(Notification.exception);
        });
    });
};
