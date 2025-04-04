<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Previous head content remains the same -->
    <title>Grade 12 Online National Exam</title>
    <!-- Previous styles remain the same -->
    <style>
        /* Add auth-specific styles */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f7fa;
        }

        .auth-form {
            background: #ffffff;
            padding: 32px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .auth-form h2 {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 24px;
            text-align: center;
        }

        .auth-form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }

        .auth-form button {
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 16px;
        }

        .auth-form .toggle-link {
            text-align: center;
            margin-top: 16px;
            color: #1a73e8;
            cursor: pointer;
        }

        .error-message {
            color: #e53e3e;
            text-align: center;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <!-- Login Page -->
    <div class="auth-container" id="login-page">
        <div class="auth-form">
            <h2>Login</h2>
            <input type="email" id="login-email" placeholder="Email" required>
            <input type="password" id="login-password" placeholder="Password" required>
            <div class="error-message" id="login-error"></div>
            <button onclick="handleLogin()">Login</button>
            <div class="toggle-link" onclick="showRegister()">Don't have an account? Register</div>
        </div>
    </div>

    <!-- Register Page -->
    <div class="auth-container" id="register-page" style="display: none;">
        <div class="auth-form">
            <h2>Register</h2>
            <input type="text" id="reg-fullname" placeholder="Full Name" required>
            <input type="email" id="reg-email" placeholder="Email" required>
            <input type="password" id="reg-password" placeholder="Password" required>
            <input type="text" id="reg-admission" placeholder="Admission Number" required>
            <input type="text" id="reg-school" placeholder="School" required>
            <input type="text" id="reg-examcenter" placeholder="Exam Center" required>
            <div class="error-message" id="register-error"></div>
            <button onclick="handleRegister()">Register</button>
            <div class="toggle-link" onclick="showLogin()">Already have an account? Login</div>
        </div>
    </div>

    <!-- Main Exam Content (hidden until logged in) -->
    <div class="container" id="exam-container" style="display: none;">
        <!-- Previous exam content remains the same -->
    </div>

    <script>
        let currentUser = null;
        const API_URL = 'http://localhost:3000/api';

        // Auth functions
        async function handleLogin() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const errorDiv = document.getElementById('login-error');

            try {
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                if (data.error) {
                    errorDiv.textContent = data.error;
                    return;
                }

                localStorage.setItem('token', data.token);
                currentUser = data.user;
                showExam();
                updateUserInfo();
            } catch (error) {
                errorDiv.textContent = 'An error occurred';
            }
        }

        async function handleRegister() {
            const full_name = document.getElementById('reg-fullname').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            const admission_number = document.getElementById('reg-admission').value;
            const school = document.getElementById('reg-school').value;
            const exam_center = document.getElementById('reg-examcenter').value;
            const errorDiv = document.getElementById('register-error');

            try {
                const response = await fetch(`${API_URL}/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        full_name, email, password, admission_number,
                        school, exam_center, is_blind: false, is_deaf: false
                    })
                });

                const data = await response.json();
                if (data.error) {
                    errorDiv.textContent = data.error;
                    return;
                }

                showLogin();
            } catch (error) {
                errorDiv.textContent = 'An error occurred';
            }
        }

        function showRegister() {
            document.getElementById('login-page').style.display = 'none';
            document.getElementById('register-page').style.display = 'flex';
        }

        function showLogin() {
            document.getElementById('register-page').style.display = 'none';
            document.getElementById('login-page').style.display = 'flex';
        }

        function showExam() {
            document.getElementById('login-page').style.display = 'none';
            document.getElementById('register-page').style.display = 'none';
            document.getElementById('exam-container').style.display = 'flex';
        }

        function updateUserInfo() {
            const basicInfo = document.querySelector('.basic-info');
            basicInfo.innerHTML = `
                <div>
                    <p><strong>FULL NAME:</strong> ${currentUser.full_name}</p>
                    <p><strong>Is Blind / Is Deaf:</strong> ${currentUser.is_blind ? 'Yes' : 'No'} / ${currentUser.is_deaf ? 'Yes' : 'No'}</p>
                    <p><strong>EXAM CENTER:</strong> ${currentUser.exam_center}</p>
                </div>
                <div>
                    <p><strong>School:</strong> ${currentUser.school}</p>
                    <p><strong>ADMISSION NUMBER:</strong> ${currentUser.admission_number}</p>
                    <p><strong>ENROLLMENT TYPE:</strong> ${currentUser.enrollment_type}</p>
                </div>
            `;
        }

        // Check if user is already logged in
        async function checkAuth() {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const response = await fetch(`${API_URL}/user`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const data = await response.json();
                    if (data.error) {
                        localStorage.removeItem('token');
                        return;
                    }
                    currentUser = data;
                    showExam();
                    updateUserInfo();
                } catch (error) {
                    localStorage.removeItem('token');
                }
            }
        }

        // Previous exam script content remains here
        // ...

        // Call checkAuth on page load
        checkAuth();
    </script>
</body>
</html>