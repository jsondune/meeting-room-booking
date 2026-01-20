<?php
/**
 * OAuth Login Buttons Partial View
 * 
 * Displays social login buttons for Google, Microsoft, and ThaiD
 * Include this in login/register pages
 * 
 * @var \yii\web\View $this
 */

use yii\helpers\Url;

// Check which providers are enabled
$googleEnabled = !empty(Yii::$app->params['oauth']['google']['clientId']);
$microsoftEnabled = !empty(Yii::$app->params['oauth']['microsoft']['clientId']);
$thaidEnabled = !empty(Yii::$app->params['oauth']['thaid']['clientId']);

$anyEnabled = $googleEnabled || $microsoftEnabled || $thaidEnabled;

if (!$anyEnabled) {
    return; // Don't show anything if no providers are configured
}
?>

<div class="oauth-login-buttons mt-4">
    <div class="divider-with-text mb-4">
        <span>หรือเข้าสู่ระบบด้วย</span>
    </div>
    
    <div class="d-grid gap-2">
        <?php if ($googleEnabled): ?>
        <a href="<?= Url::to(['/oauth/auth', 'provider' => 'google']) ?>" 
           class="btn btn-outline-dark btn-oauth btn-google">
            <svg class="oauth-icon" viewBox="0 0 24 24" width="20" height="20">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>เข้าสู่ระบบด้วย Google</span>
        </a>
        <?php endif; ?>
        
        <?php if ($microsoftEnabled): ?>
        <a href="<?= Url::to(['/oauth/auth', 'provider' => 'microsoft']) ?>" 
           class="btn btn-outline-dark btn-oauth btn-microsoft">
            <svg class="oauth-icon" viewBox="0 0 21 21" width="20" height="20">
                <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
                <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
                <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
                <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
            </svg>
            <span>เข้าสู่ระบบด้วย Microsoft</span>
        </a>
        <?php endif; ?>
        
        <?php if ($thaidEnabled): ?>
        <a href="<?= Url::to(['/oauth/auth', 'provider' => 'thaid']) ?>" 
           class="btn btn-outline-primary btn-oauth btn-thaid">
            <svg class="oauth-icon" viewBox="0 0 24 24" width="20" height="20">
                <circle cx="12" cy="12" r="10" fill="#0052CC"/>
                <text x="12" y="16" text-anchor="middle" fill="white" font-size="10" font-weight="bold">ID</text>
            </svg>
            <span>เข้าสู่ระบบด้วย ThaiD</span>
        </a>
        <?php endif; ?>
    </div>
    
    <p class="text-muted text-center mt-3 small">
        <i class="bi bi-shield-check me-1"></i>
        การเข้าสู่ระบบด้วยบัญชีภายนอกมีความปลอดภัยและสะดวก
    </p>
</div>

<style>
.divider-with-text {
    display: flex;
    align-items: center;
    text-align: center;
    color: #6c757d;
}

.divider-with-text::before,
.divider-with-text::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #dee2e6;
}

.divider-with-text span {
    padding: 0 1rem;
    font-size: 0.875rem;
}

.btn-oauth {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.625rem 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.btn-oauth:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-oauth .oauth-icon {
    flex-shrink: 0;
}

.btn-google:hover {
    background-color: #f8f9fa;
    border-color: #dadce0;
}

.btn-microsoft:hover {
    background-color: #f8f9fa;
    border-color: #dadce0;
}

.btn-thaid {
    background-color: #0052CC;
    border-color: #0052CC;
    color: white;
}

.btn-thaid:hover {
    background-color: #0047B3;
    border-color: #0047B3;
    color: white;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .btn-oauth {
        background-color: #1e1e1e;
        color: #fff;
    }
    
    .divider-with-text {
        color: #adb5bd;
    }
    
    .divider-with-text::before,
    .divider-with-text::after {
        border-color: #495057;
    }
}
</style>
