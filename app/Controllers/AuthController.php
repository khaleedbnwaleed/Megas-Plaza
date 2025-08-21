<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication, registration, and password management
 */

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        // Redirect if already logged in
        if (Auth::check()) {
            Response::redirect($this->getRedirectUrl());
        }
        
        Response::view('auth/login');
    }
    
    /**
     * Handle login
     */
    public function login() {
        if (!Request::isPost()) {
            Response::error(405, 'Method not allowed');
        }
        
        // Verify CSRF token
        Csrf::verify();
        
        $email = Request::post('email');
        $password = Request::post('password');
        $remember = Request::post('remember');
        
        // Validate input
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        if (!empty($errors)) {
            Response::view('auth/login', ['errors' => $errors, 'old' => Request::post()]);
            return;
        }
        
        // Rate limiting check
        if (!$this->checkRateLimit($email)) {
            Response::setFlash('error', 'Too many login attempts. Please try again later.');
            Response::view('auth/login', ['old' => Request::post()]);
            return;
        }
        
        // Find user
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($user, $password)) {
            $this->recordFailedAttempt($email);
            Response::setFlash('error', 'Invalid email or password');
            Response::view('auth/login', ['old' => Request::post()]);
            return;
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            Response::setFlash('error', 'Your account has been disabled');
            Response::view('auth/login', ['old' => Request::post()]);
            return;
        }
        
        // Login successful
        $this->loginUser($user, $remember);
        
        // Clear failed attempts
        $this->clearFailedAttempts($email);
        
        // Log the login
        Audit::log($user['id'], 'User Login', 'User logged in successfully');
        
        Response::setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
        Response::redirect($this->getRedirectUrl($user['role']));
    }
    
    /**
     * Show registration form
     */
    public function showRegister() {
        // Redirect if already logged in
        if (Auth::check()) {
            Response::redirect($this->getRedirectUrl());
        }
        
        Response::view('auth/register');
    }
    
    /**
     * Handle registration
     */
    public function register() {
        if (!Request::isPost()) {
            Response::error(405, 'Method not allowed');
        }
        
        // Verify CSRF token
        Csrf::verify();
        
        $data = Request::post();
        
        // Validate input
        $validator = new Validator();
        $rules = [
            'full_name' => 'required|min:2|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'max:30',
            'password' => 'required|min:' . PASSWORD_MIN_LENGTH,
            'password_confirmation' => 'required|same:password'
        ];
        
        $errors = $validator->validate($data, $rules);
        
        // Check if email already exists
        if (empty($errors['email']) && $this->userModel->emailExists($data['email'])) {
            $errors['email'] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            Response::view('auth/register', ['errors' => $errors, 'old' => $data]);
            return;
        }
        
        // Create user
        $userId = $this->userModel->create([
            'role' => 'tenant',
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'email_verified' => MAIL_ENABLED ? 0 : 1 // Auto-verify if mail disabled
        ]);
        
        if (!$userId) {
            Response::setFlash('error', 'Registration failed. Please try again.');
            Response::view('auth/register', ['old' => $data]);
            return;
        }
        
        // Send verification email or show success
        if (MAIL_ENABLED) {
            $this->sendVerificationEmail($userId, $data['email']);
            Response::setFlash('success', 'Registration successful! Please check your email to verify your account.');
        } else {
            Response::setFlash('success', 'Registration successful! You can now login.');
        }
        
        // Log the registration
        Audit::log($userId, 'User Registration', 'New user registered: ' . $data['email']);
        
        Response::redirect('/login');
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        if (!Request::isPost()) {
            Response::error(405, 'Method not allowed');
        }
        
        $user = Auth::user();
        if ($user) {
            // Log the logout
            Audit::log($user['id'], 'User Logout', 'User logged out');
            
            // Delete session record
            $this->userModel->deleteSession(session_id());
        }
        
        // Destroy session
        session_destroy();
        session_start();
        session_regenerate_id(true);
        
        Response::setFlash('success', 'You have been logged out successfully');
        Response::redirect('/');
    }
    
    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        Response::view('auth/forgot-password');
    }
    
    /**
     * Handle forgot password
     */
    public function forgotPassword() {
        if (!Request::isPost()) {
            Response::error(405, 'Method not allowed');
        }
        
        Csrf::verify();
        
        $email = Request::post('email');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::setFlash('error', 'Please enter a valid email address');
            Response::view('auth/forgot-password', ['old' => Request::post()]);
            return;
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if ($user) {
            if (MAIL_ENABLED) {
                $this->sendPasswordResetEmail($user);
            } else {
                // For demo purposes, show reset link
                $token = $this->generateResetToken($user['id']);
                Response::setFlash('info', 'Reset link: /reset-password?token=' . $token);
            }
        }
        
        // Always show success message for security
        Response::setFlash('success', 'If your email exists in our system, you will receive a password reset link.');
        Response::redirect('/login');
    }
    
    /**
     * Show reset password form
     */
    public function showResetPassword() {
        $token = Request::get('token');
        
        if (empty($token) || !$this->validateResetToken($token)) {
            Response::setFlash('error', 'Invalid or expired reset token');
            Response::redirect('/forgot-password');
        }
        
        Response::view('auth/reset-password', ['token' => $token]);
    }
    
    /**
     * Handle reset password
     */
    public function resetPassword() {
        if (!Request::isPost()) {
            Response::error(405, 'Method not allowed');
        }
        
        Csrf::verify();
        
        $token = Request::post('token');
        $password = Request::post('password');
        $passwordConfirmation = Request::post('password_confirmation');
        
        // Validate token
        $userId = $this->validateResetToken($token);
        if (!$userId) {
            Response::setFlash('error', 'Invalid or expired reset token');
            Response::redirect('/forgot-password');
            return;
        }
        
        // Validate password
        $errors = [];
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            Response::view('auth/reset-password', ['errors' => $errors, 'token' => $token]);
            return;
        }
        
        // Update password
        if ($this->userModel->updatePassword($userId, $password)) {
            $this->clearResetToken($token);
            
            // Log password reset
            Audit::log($userId, 'Password Reset', 'Password reset successfully');
            
            Response::setFlash('success', 'Password reset successfully. You can now login.');
        } else {
            Response::setFlash('error', 'Failed to reset password. Please try again.');
        }
        
        Response::redirect('/login');
    }
    
    /**
     * Verify email
     */
    public function verifyEmail() {
        $token = Request::get('token');
        
        if (empty($token)) {
            Response::setFlash('error', 'Invalid verification token');
            Response::redirect('/login');
            return;
        }
        
        $userId = $this->validateVerificationToken($token);
        if (!$userId) {
            Response::setFlash('error', 'Invalid or expired verification token');
            Response::redirect('/login');
            return;
        }
        
        // Update user as verified
        if ($this->userModel->update($userId, ['email_verified' => 1])) {
            $this->clearVerificationToken($token);
            
            // Log email verification
            Audit::log($userId, 'Email Verified', 'Email address verified successfully');
            
            Response::setFlash('success', 'Email verified successfully! You can now login.');
        } else {
            Response::setFlash('error', 'Failed to verify email. Please try again.');
        }
        
        Response::redirect('/login');
    }
    
    /**
     * Login user and create session
     */
    private function loginUser($user, $remember = false) {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();
        
        // Create session record
        $this->userModel->createSession($user['id'], session_id());
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            // Store token in database (implement if needed)
        }
    }
    
    /**
     * Get redirect URL based on role
     */
    private function getRedirectUrl($role = null) {
        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);
        
        if ($intended) {
            return $intended;
        }
        
        $role = $role ?? Auth::user()['role'] ?? 'tenant';
        
        switch ($role) {
            case 'super_admin':
            case 'manager':
                return '/admin';
            case 'tenant':
                return '/tenant/dashboard';
            default:
                return '/';
        }
    }
    
    /**
     * Check rate limiting for login attempts
     */
    private function checkRateLimit($email) {
        $key = 'login_attempts_' . md5($email . Request::ip());
        $attempts = $_SESSION[$key] ?? 0;
        $lastAttempt = $_SESSION[$key . '_time'] ?? 0;
        
        // Reset if more than lockout time has passed
        if (time() - $lastAttempt > LOGIN_LOCKOUT_TIME) {
            unset($_SESSION[$key], $_SESSION[$key . '_time']);
            return true;
        }
        
        return $attempts < LOGIN_MAX_ATTEMPTS;
    }
    
    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt($email) {
        $key = 'login_attempts_' . md5($email . Request::ip());
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        $_SESSION[$key . '_time'] = time();
    }
    
    /**
     * Clear failed login attempts
     */
    private function clearFailedAttempts($email) {
        $key = 'login_attempts_' . md5($email . Request::ip());
        unset($_SESSION[$key], $_SESSION[$key . '_time']);
    }
    
    /**
     * Generate verification token
     */
    private function generateVerificationToken($userId) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['verification_tokens'][$token] = [
            'user_id' => $userId,
            'expires' => time() + 3600 // 1 hour
        ];
        return $token;
    }
    
    /**
     * Generate reset token
     */
    private function generateResetToken($userId) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['reset_tokens'][$token] = [
            'user_id' => $userId,
            'expires' => time() + 3600 // 1 hour
        ];
        return $token;
    }
    
    /**
     * Validate verification token
     */
    private function validateVerificationToken($token) {
        $tokens = $_SESSION['verification_tokens'] ?? [];
        
        if (!isset($tokens[$token])) {
            return false;
        }
        
        $data = $tokens[$token];
        
        if (time() > $data['expires']) {
            unset($_SESSION['verification_tokens'][$token]);
            return false;
        }
        
        return $data['user_id'];
    }
    
    /**
     * Validate reset token
     */
    private function validateResetToken($token) {
        $tokens = $_SESSION['reset_tokens'] ?? [];
        
        if (!isset($tokens[$token])) {
            return false;
        }
        
        $data = $tokens[$token];
        
        if (time() > $data['expires']) {
            unset($_SESSION['reset_tokens'][$token]);
            return false;
        }
        
        return $data['user_id'];
    }
    
    /**
     * Clear verification token
     */
    private function clearVerificationToken($token) {
        unset($_SESSION['verification_tokens'][$token]);
    }
    
    /**
     * Clear reset token
     */
    private function clearResetToken($token) {
        unset($_SESSION['reset_tokens'][$token]);
    }
    
    /**
     * Send verification email
     */
    private function sendVerificationEmail($userId, $email) {
        $token = $this->generateVerificationToken($userId);
        $link = BASE_URL . '/verify-email?token=' . $token;
        
        $subject = 'Verify Your Email - ' . APP_NAME;
        $message = "Please click the following link to verify your email address:\n\n" . $link;
        
        Mailer::send($email, $subject, $message);
    }
    
    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($user) {
        $token = $this->generateResetToken($user['id']);
        $link = BASE_URL . '/reset-password?token=' . $token;
        
        $subject = 'Password Reset - ' . APP_NAME;
        $message = "Hello " . $user['full_name'] . ",\n\n";
        $message .= "Please click the following link to reset your password:\n\n" . $link;
        $message .= "\n\nThis link will expire in 1 hour.";
        
        Mailer::send($user['email'], $subject, $message);
    }
}
