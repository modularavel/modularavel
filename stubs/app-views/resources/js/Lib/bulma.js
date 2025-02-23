// which is used in the /resources/views/layouts/app.blade.php file.
document.addEventListener('DOMContentLoaded', () => {
    // Functions to open and close a modal
    function openModal($el) {
        // Add the is-clipped class to disable scrolling
        document.documentElement.classList.add('is-clipped');

        // Add the is-active class to show the modal
        $el.classList.add('is-active');
    }

    // Function to close a modal
    function closeModal($el) {
        // Remove the is-clipped class to enable scrolling again
        $el.classList.remove('is-clipped');

        // Remove the is-active class to hide the modal
        $el.classList.remove('is-active');
    }

    // Function to close all modals
    function closeAllModals() {
        // Get all the modal elements
        (document.querySelectorAll('.modal') || []).forEach(($modal) => {
            // Close the modal
            closeModal($modal);
        });
    }

    // Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
        // Get the ID of the modal to open
        const modal = $trigger.dataset.target;

        // Find the modal element by its ID
        const $target = document.getElementById(modal);

        // Add a click event on the trigger button
        $trigger.addEventListener('click', () => {
            // Open the modal
            openModal($target);
        });
    });

    // Add a click event on various child elements to close the parent modal
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
        // Find the closest modal element
        const $target = $close.closest('.modal');

        // Add a click event on the close button
        $close.addEventListener('click', () => {
            // Close the modal
            closeModal($target);
        });
    });

    // Add a keyboard event to close all modals
    document.addEventListener('keydown', (event) => {
        // Close all modals when the Escape key is pressed
        if(event.key === "Escape") {
            // Prevent the default behavior of the Escape key
            event.preventDefault();

            // Close all modals
            closeAllModals();
        }
    });
});
