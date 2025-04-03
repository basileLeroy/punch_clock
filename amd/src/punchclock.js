/**
 * @module mod_punchclock/punchclock
 */

/**
 * Initialize punchclock buttons
 */
export const init = () => {
    const clocks = document.querySelectorAll

    const clockMorningStart = document.querySelector('#clock-morning-start');
    const clockAfternoonStart = document.querySelector('#clock-afternoon-start');

    

    const updateClock = () => {
        const now = new Date();
        clockElement.innerText = now.toLocaleTimeString();
    };

    updateClock(); // Set initial time immediately
    setInterval(updateClock, 1000);
};
