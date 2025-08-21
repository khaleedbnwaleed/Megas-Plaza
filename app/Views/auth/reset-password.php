<?php
$title = 'Reset Password - ' . APP_NAME;
ob_start();
?>

<div class="flex flex--center" style="min-height: 60vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card__header">
            <h2 class="card__title text-center">Reset Your Password</h2>
        </div>
        
        <div class="card__body">
            <form method="POST" action="/reset-password" class="form" data-loading>
                <?= Csrf::field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form__group">
                    <label for="password" class="form__label">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form__input" 
                        required
                        autocomplete="new-password"
                        minlength="<?= PASSWORD_MIN_LENGTH ?>"
                    >
                    <div class="form__help">
                        Password must be at least <?= PASSWORD_MIN_LENGTH ?> characters long
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form__group">
                    <label for="password_confirmation" class="form__label">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form__input" 
                        required
                        autocomplete="new-password"
                    >
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['password_confirmation']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form__group">
                    <button type="submit" class="btn btn--primary" style="width: 100%;" data-original-text="Reset Password">
                        Reset Password
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
