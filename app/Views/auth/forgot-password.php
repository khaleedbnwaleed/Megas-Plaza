<?php
$title = 'Forgot Password - ' . APP_NAME;
ob_start();
?>

<div class="flex flex--center" style="min-height: 60vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card__header">
            <h2 class="card__title text-center">Reset Your Password</h2>
        </div>
        
        <div class="card__body">
            <p class="text-center text-muted mb-4">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            
            <form method="POST" action="/forgot-password" class="form" data-loading>
                <?= Csrf::field() ?>
                
                <div class="form__group">
                    <label for="email" class="form__label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form__input" 
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>
                
                <div class="form__group">
                    <button type="submit" class="btn btn--primary" style="width: 100%;" data-original-text="Send Reset Link">
                        Send Reset Link
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card__footer text-center">
            <p class="text-sm text-muted">
                Remember your password? 
                <a href="/login">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
