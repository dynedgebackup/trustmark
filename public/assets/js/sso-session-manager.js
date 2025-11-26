/**
 * SSO Session Manager
 * Periodically checks if the SSO session is still valid
 */
class SsoSessionManager {
    constructor(options = {}) {
        // Get check interval from server configuration or default to 5 minutes
        this.checkInterval = (options.checkInterval || 5) * 60 * 1000;
        this.isChecking = false;
        this.init();
    }

    init() {
        // Only run for SSO authenticated users
        if (document.querySelector('[data-sso-authenticated]')) {
            this.startPeriodicCheck();
        }
    }

    startPeriodicCheck() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
    }

    async checkSession() {
        if (this.isChecking) return;
        
        this.isChecking = true;
        
        try {
            const response = await fetch('/sso/check-session', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();
            
            if (!data.valid) {
                this.handleSessionExpired();
            }
        } catch (error) {
            console.warn('SSO session check failed:', error);
            // Don't logout on network errors
        } finally {
            this.isChecking = false;
        }
    }

    handleSessionExpired() {
        // Show notification to user
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: 'Your SSO session has expired. You will be redirected to login.',
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            alert('Your SSO session has expired. You will be redirected to login.');
            window.location.href = '/login';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get configuration from the server if available
    const ssoConfig = window.ssoConfig || {};
    new SsoSessionManager(ssoConfig);
});
