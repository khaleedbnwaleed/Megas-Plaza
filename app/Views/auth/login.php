<?php
$title = 'Login - ' . APP_NAME;
ob_start();
?>

<div class="flex flex--center" style="min-height: 60vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card__header">
            <h2 class="card__title text-center">Login to Your Account</h2>
        </div>
        
        <div class="card__body">
            <form method="POST" action="/login" class="form" data-loading>
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
                    <?php if (isset($errors['email'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['email']) ?></div>
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
                        autocomplete="current-password"
                    >
                    <?php if (isset($errors['password'])): ?>
                        <div class="form__error"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form__group">
                    <label class="flex" style="align-items: center; gap: 8px;">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                </div>
                
                <div class="form__group">
                    <button type="submit" class="btn btn--primary" style="width: 100%;" data-original-text="Login">
                        Login
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="/forgot-password" class="text-sm">Forgot your password?</a>
                </div>
            </form>
        </div>
        
        <div class="card__footer text-center">
            <p class="text-sm text-muted">
                Don't have an account? 
                <a href="/register">Register here</a>
            </p>
        </div>
    </div>
</div>

<!-- Demo Accounts Info -->
<div class="card mt-4">
    <div class="card__header">
        <h3 class="card__title">Demo Accounts</h3>
    </div>
    <div class="card__body">
        <div class="grid grid--cols-3">
            <div>
                <h4 class="font-semibold">Super Admin</h4>
                <p class="text-sm">admin@megaplaza.com</p>
                <p class="text-sm text-muted">password</p>
            </div>
            <div>
                <h4 class="font-semibold">Manager</h4>
                <p class="text-sm">manager1@megaplaza.com</p>
                <p class="text-sm text-muted">password</p>
            </div>
            <div>
                <h4 class="font-semibold">Tenant</h4>
                <p class="text-sm">tenant1@example.com</p>
                <p class="text-sm text-muted">password</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
