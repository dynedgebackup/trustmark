<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: white;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Background Map Image */
        .background-map {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('{{ asset('assets/img/ph-map.png') }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            opacity: 0.7;
            z-index: 1;
            pointer-events: none;
            /* Add filters to enhance visibility of grey image */
            filter: contrast(1.8) brightness(0.7);
        }

        /* Debug: Temporary higher opacity to see if image loads */
        .background-map.debug {
            opacity: 0.4;
            filter: contrast(2) brightness(0.6);
        }

        /* Main Content Container */
        .error-container {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            padding: 20px;
        }

        /* 403 Error Logo */
        .error-logo {
            width: 600px;
            height: auto;
            margin-bottom: 40px;
        }

        /* Text Content */
        .error-content {
            color: #43499f;
            margin-bottom: 40px;
        }

        .error-title {
            font-size: 56px;
            font-weight: bold;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        .error-divider {
            width: 240px;
            height: 5px;
            background-color: #43499f;
            margin: 0 auto 25px auto;
        }

        .error-description {
            font-size: 20px;
            font-weight: normal;
            letter-spacing: 0.5px;
        }

        /* Resend Button */
        .resend-button {
            background-color: #43499f;
            color: white;
            border: none;
            padding: 18px 48px;
            font-size: 22px;
            font-weight: bold;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            letter-spacing: 1px;
        }

        .resend-button:hover {
            background-color: #363c8a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 73, 159, 0.3);
        }

        .resend-button:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        
        /* Large Desktop screens (1920px+) - Default styles */
        
        /* Laptop screens (1024px to 1440px) */
        @media (max-width: 1440px) and (min-width: 1024px) {
            .error-logo {
                width: 400px;
            }

            .error-title {
                font-size: 42px;
            }

            .error-divider {
                width: 180px;
                height: 4px;
            }

            .error-description {
                font-size: 18px;
            }

            .resend-button {
                font-size: 18px;
                padding: 15px 40px;
            }

            .error-container {
                padding: 15px;
            }
        }

        /* Small Laptop screens (768px to 1024px) */
        @media (max-width: 1024px) and (min-width: 768px) {
            .error-logo {
                width: 320px;
            }

            .error-title {
                font-size: 38px;
            }

            .error-divider {
                width: 160px;
                height: 4px;
            }

            .error-description {
                font-size: 17px;
            }

            .resend-button {
                font-size: 17px;
                padding: 14px 35px;
            }

            .error-container {
                padding: 15px;
            }
        }

        /* Tablet screens (768px and below) */
        @media (max-width: 768px) {
            .error-logo {
                width: 250px;
            }

            .error-title {
                font-size: 36px;
            }

            .error-divider {
                width: 150px;
            }

            .error-description {
                font-size: 16px;
            }

            .resend-button {
                font-size: 16px;
                padding: 12px 30px;
            }
        }

        /* Mobile screens (480px and below) */
        @media (max-width: 480px) {
            .error-logo {
                width: 200px;
            }

            .error-title {
                font-size: 28px;
            }

            .error-divider {
                width: 120px;
            }

            .error-description {
                font-size: 14px;
            }

            .resend-button {
                font-size: 14px;
                padding: 10px 25px;
            }
        }

        /* Error Section Styles */
        .error-section {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #ff6b6b;
            border-radius: 10px;
            padding: 15px 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            z-index: 1000;
            max-width: 400px;
            text-align: center;
            animation: slideUpFade 0.3s ease-out;
        }

        .error-message-text {
            color: #d63031;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .countdown-timer {
            color: #43499f;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .error-section.success {
            border-color: #00b894;
        }

        .error-section.success .error-message-text {
            color: #00b894;
        }
    </style>
</head>
<body>
    <!-- Background Map -->
    <div class="background-map"></div>

    <!-- Main Error Content -->
    <div class="error-container">
        <!-- Dynamic Logo based on success/error state -->
        @if(isset($isSuccess) && $isSuccess)
            <img src="{{ asset('assets/img/success-code.png') }}" alt="Success" class="error-logo">
        @else
            <img src="{{ asset('assets/img/error-code.png') }}" alt="403 Error" class="error-logo">
        @endif

        <!-- Dynamic Text Content based on success/error state -->
        <div class="error-content">
            @if(isset($isSuccess) && $isSuccess)
                <h1 class="error-title">Success!</h1>
                <div class="error-divider"></div>
                <p class="error-description">YOUR EMAIL HAS BEEN SUCCESSFULLY VERIFIED!</p>
            @else
                <h1 class="error-title">Forbidden</h1>
                <div class="error-divider"></div>
                <p class="error-description">{{ strtoupper($message ?? 'INVALID OR EXPIRED VERIFICATION TOKEN') }}</p>
            @endif
        </div>

        <!-- Dynamic Button based on success/error state -->
        @if(isset($isSuccess) && $isSuccess)
            <a href="{{ route('login') }}" class="resend-button">REDIRECT TO LOGIN</a>
        @else
            <!-- Email Input and Resend Button for error state -->
            @if(isset($showResendButton) && $showResendButton)
                @if(isset($showEmailInput) && $showEmailInput)
                    <!-- Email input field when we don't know the user -->
                    <div style="margin-bottom: 20px;">
                        <input type="email" id="userEmail" placeholder="Enter your email address" 
                               style="padding: 12px 20px; border: 2px solid #43499f; border-radius: 25px; font-size: 16px; width: 300px; text-align: center; margin-bottom: 15px;">
                    </div>
                @endif
                <a href="#" class="resend-button" onclick="resendEmail()">RESEND E-MAIL</a>
            @endif
        @endif

        <!-- Error Message Section -->
        <div id="errorSection" class="error-section" style="display: none;">
            <div id="errorMessage" class="error-message-text"></div>
            <div id="countdownTimer" class="countdown-timer" style="display: none;">
                <span id="timerText"></span>
            </div>
        </div>
    </div>

    <script>
        // Error display functions
        function showError(message, isSuccess = false) {
            const errorSection = document.getElementById('errorSection');
            const errorMessage = document.getElementById('errorMessage');
            const countdownTimer = document.getElementById('countdownTimer');
            
            errorMessage.textContent = message;
            errorSection.className = 'error-section' + (isSuccess ? ' success' : '');
            errorSection.style.display = 'block';
            countdownTimer.style.display = 'none';
            
            // Auto hide after 5 seconds for success messages
            if (isSuccess) {
                setTimeout(() => {
                    hideError();
                }, 5000);
            }
        }

        function showErrorWithCountdown(message, timeRemaining) {
            showError(message);
            const countdownTimer = document.getElementById('countdownTimer');
            const timerText = document.getElementById('timerText');
            
            countdownTimer.style.display = 'block';
            
            function updateTimer() {
                if (timeRemaining <= 0) {
                    hideError();
                    return;
                }
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerText.textContent = `Please wait ${minutes}:${seconds.toString().padStart(2, '0')}`;
                timeRemaining--;
                setTimeout(updateTimer, 1000);
            }
            
            updateTimer();
        }

        function hideError() {
            const errorSection = document.getElementById('errorSection');
            errorSection.style.display = 'none';
        }

        async function resendEmail() {
            const button = document.querySelector('.resend-button');
            const originalText = button.textContent;
            
            // Hide any existing errors
            hideError();
            
            // Get email from input field or user object
            let userEmail = '{{ isset($user) ? $user->email : "" }}';
            
            // If email is empty, try to get from input field
            if (!userEmail) {
                const emailInput = document.getElementById('userEmail');
                if (emailInput) {
                    userEmail = emailInput.value.trim();
                    if (!userEmail) {
                        showError('Please enter your email address');
                        return;
                    }
                }
            }

            // Disable button and show loading
            button.style.opacity = '0.6';
            button.style.pointerEvents = 'none';
            button.textContent = 'SENDING...';

            try {
                const response = await fetch('{{ route("verification.resend-simple") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: userEmail
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showError(data.message, true);
                } else {
                    // Check if it's a rate limit error with time remaining
                    if (data.time_remaining) {
                        showErrorWithCountdown(data.message || 'Please wait before requesting another email', data.time_remaining);
                    } else {
                        showError(data.message || 'Failed to send email');
                    }
                }
            } catch (error) {
                showError('Failed to send verification email. Please try again.');
                console.error('Error:', error);
            } finally {
                // Re-enable button
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto';
                button.textContent = originalText;
            }
        }

        // Debug: Check if background image loads
        document.addEventListener('DOMContentLoaded', function() {
            const img = new Image();
            img.onload = function() {
                console.log('Background image loaded successfully:', img.src);
            };
            img.onerror = function() {
                console.error('Failed to load background image:', img.src);
            };
            img.src = '{{ asset('assets/img/ph-map.png') }}';
        });
    </script>
</body>
</html>