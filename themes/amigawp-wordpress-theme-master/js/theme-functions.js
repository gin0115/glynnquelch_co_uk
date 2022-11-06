/**
 * File theme-functions.js.
 *
 * Additional Functionalities
 */
(function() {
    console.log('fff');
    /**
     * Sets a cookie
     *
     * @param {string} cname - The name of the cookie
     * @param {string} cvalue - The value of the cookie
     * @param {number} exdays - The number of days the cookie should be valid
     * @returns {void}
     */
    const setCookie = (cname, cvalue, exdays) => {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    // Create event listeners for the wb1.0 buttons
    const wb1_0Buttons = document.querySelectorAll('.wb1_0');
    wb1_0Buttons.forEach((button) => {
        button.addEventListener('click', () => {
            setCookie('color_scheme', 1, 365);
            // Reload the page
            location.reload();
        });
    });

    // Create event listeners for the wb2.0 buttons
    const wb2_0Buttons = document.querySelectorAll('.wb2_0');
    console.log(wb2_0Buttons);
    wb2_0Buttons.forEach((button) => {
        button.addEventListener('click', () => {
            setCookie('color_scheme', 0, 365);

            // Reload the page
            location.reload();
        });
    });



})();