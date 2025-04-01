export const init = () => {
  document.addEventListener('DOMContentLoaded', () => {
      const button = document.getElementById('punchclock-button');
      if (button) {
          button.addEventListener('click', async () => {
              const punchclockid = button.dataset.punchclockid;
              const courseid = button.dataset.courseid;

              const response = await fetch(M.cfg.wwwroot + '/mod/punchclock/ajax.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ action: 'log_click', punchclockid, courseid })
              });

              if (response.ok) {
                  const result = await response.json();
                  if (result.success) {
                      alert('Check-in saved!');
                  } else {
                      alert('Failed to save check-in.');
                  }
              }
          });
      }
  });
};