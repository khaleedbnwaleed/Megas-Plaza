<?php
$title = 'Register - ' . APP_NAME;
ob_start();
?>

<div class="flex flex--center" style="min-height: 60vh;">
    <div class="card" style="width: 100%; max-width: 500px;">
        <div class="card__header">
            <h2 class="card__title text-center">Create Your Account</h2>
        </div>
        
        <div class="card__body">
            <form method="POST" action="/register" class="form" data-loading>
                <?= Csrf::field() ?>
                
                <div class="form__group">
                    <label for="full_name" class="form__label">Full Name</label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        class="form__input" 
                        value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                        required
                        autocomplete="name"
                    >
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['full_name']) ?></div>
                    <?php endif; ?>
                </div>
                
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
                    <?php if (isset($errors['email'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form__group">
                    <label for="phone" class="form__label">Phone Number (Optional)</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form__input" 
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                        autocomplete="tel"
                    >
                    <?php if (isset($errors['phone'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form__group">
                    <label for="password" class="form__label">Password</label>
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
                    <label for="password_confirmation" class="form__label">Confirm Password</label>
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
                    <button type="submit" class="btn btn--primary" style="width: 100%;" data-original-text="Create Account">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card__footer text-center">
            <p class="text-sm text-muted">
                Already have an account? 
                <a href="/login">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
