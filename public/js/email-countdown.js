/**
 * Email Token Countdown Handler
 * Handles countdown timers for email verification and OTP sending
 */
class EmailCountdownHandler {
    constructor() {
        this.initializeCountdowns();
    }

    initializeCountdowns() {
        // Initialize countdown for login page
        this.initializeLoginCountdown();
        
        // Initialize countdown for forgot password page
        this.initializeForgotPasswordCountdown();
    }

    initializeLoginCountdown() {
        const errorMessage = document.querySelector('.alert-danger');
        const countdownElement = document.getElementById('countdown-seconds');
        
        if (errorMessage && countdownElement) {
            const initialSeconds = parseInt(countdownElement.dataset.seconds);
            this.startCountdown(initialSeconds, countdownElement, 'login');
        }
    }

    initializeForgotPasswordCountdown() {
        const errorMessage = document.querySelector('.alert-danger');
        const countdownElement = document.getElementById('otp-countdown-seconds');
        
        if (errorMessage && countdownElement) {
            const initialSeconds = parseInt(countdownElement.dataset.seconds);
            this.startCountdown(initialSeconds, countdownElement, 'otp');
        }
    }

    startCountdown(seconds, element, type) {
        if (seconds <= 0) return;

        const updateCountdown = () => {
            if (seconds <= 0) {
                this.onCountdownComplete(type);
                return;
            }

            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            
            let message = '';
            if (type === 'login') {
                message = `Please verify your email before logging-in.\nNew verification will be sent after ${minutes} minutes.`;
            } else if (type === 'otp') {
                message = `Please wait before requesting another OTP.\nNew OTP can be sent after ${minutes} minutes.`;
            }

            this.updateMessage(message, type);
            seconds--;
            
            setTimeout(updateCountdown, 1000);
        };

        updateCountdown();
    }

    updateMessage(message, type) {
        const errorElement = document.querySelector('.alert-danger');
        if (errorElement) {
            errorElement.innerHTML = message.replace(/\n/g, '<br>');
        }
    }

    onCountdownComplete(type) {
        const errorElement = document.querySelector('.alert-danger');
        if (errorElement) {
            if (type === 'login') {
                errorElement.innerHTML = 'Please verify your email before logging-in.<br>You can now request a new verification email by attempting to login again.';
                errorElement.className = errorElement.className.replace('alert-danger', 'alert-info');
            } else if (type === 'otp') {
                errorElement.innerHTML = 'You can now request a new OTP.';
                errorElement.className = errorElement.className.replace('alert-danger', 'alert-info');
                
                // Re-enable the forgot password form
                const form = document.querySelector('form[action*="forgot-password"]');
                if (form) {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Send OTP';
                    }
                }
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new EmailCountdownHandler();
});

// Disable form submission during countdown
document.addEventListener('DOMContentLoaded', function() {
    const countdownElement = document.getElementById('countdown-seconds') || document.getElementById('otp-countdown-seconds');
    
    if (countdownElement) {
        const seconds = parseInt(countdownElement.dataset.seconds);
        
        if (seconds > 0) {
            // Disable login form
            const loginForm = document.querySelector('form[action*="login"]');
            if (loginForm) {
                const submitButton = loginForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Please wait...';
                    
                    setTimeout(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Login';
                    }, seconds * 1000);
                }
            }
            
            // Disable forgot password form
            const forgotForm = document.querySelector('form[action*="forgot-password"]');
            if (forgotForm) {
                const submitButton = forgotForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Please wait...';
                    
                    setTimeout(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Send OTP';
                    }, seconds * 1000);
                }
            }
        }
    }
});