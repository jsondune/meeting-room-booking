<?php
/**
 * Error View - Backend
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

use yii\helpers\Html;

$this->title = $name;
$this->context->layout = 'main-login';

$statusCode = $exception->statusCode ?? 500;
$errorIcons = [
    400 => 'bi-exclamation-circle',
    401 => 'bi-shield-lock',
    403 => 'bi-hand-thumbs-down',
    404 => 'bi-search',
    500 => 'bi-bug',
];
$icon = $errorIcons[$statusCode] ?? 'bi-exclamation-triangle';
?>

<div class="auth-card text-center">
    <div class="auth-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
        <div class="logo" style="color: #dc3545;">
            <i class="<?= $icon ?>"></i>
        </div>
        <h1><?= Html::encode($statusCode) ?></h1>
        <p><?= nl2br(Html::encode($message)) ?></p>
    </div>
    
    <div class="auth-body">
        <div class="mb-4">
            <?php if ($statusCode === 404): ?>
                <p class="text-muted">ไม่พบหน้าที่คุณต้องการ หน้านี้อาจถูกย้ายหรือลบไปแล้ว</p>
            <?php elseif ($statusCode === 403): ?>
                <p class="text-muted">คุณไม่มีสิทธิ์เข้าถึงหน้านี้ โปรดติดต่อผู้ดูแลระบบ</p>
            <?php elseif ($statusCode === 401): ?>
                <p class="text-muted">โปรดเข้าสู่ระบบเพื่อเข้าถึงหน้านี้</p>
            <?php else: ?>
                <p class="text-muted">เกิดข้อผิดพลาดขึ้น โปรดลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ</p>
            <?php endif; ?>
        </div>
        
        <div class="d-grid gap-2">
            <?php if ($statusCode === 401): ?>
                <?= Html::a(
                    '<i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ',
                    ['site/login'],
                    ['class' => 'btn btn-primary']
                ) ?>
            <?php endif; ?>
            
            <?= Html::a(
                '<i class="bi bi-house me-1"></i> กลับหน้าหลัก',
                ['/'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>
            
            <button onclick="history.back()" class="btn btn-link text-muted">
                <i class="bi bi-arrow-left me-1"></i> กลับหน้าก่อนหน้า
            </button>
        </div>
    </div>
    
    <?php if (YII_DEBUG): ?>
        <div class="auth-footer text-start">
            <details>
                <summary class="text-muted cursor-pointer">
                    <i class="bi bi-code-slash me-1"></i>Debug Info
                </summary>
                <pre class="mt-2 p-3 bg-light rounded small text-start" style="max-height: 200px; overflow: auto;">
Exception: <?= get_class($exception) ?>

Message: <?= Html::encode($message) ?>

File: <?= $exception->getFile() ?>:<?= $exception->getLine() ?>

Trace:
<?= Html::encode($exception->getTraceAsString()) ?>
                </pre>
            </details>
        </div>
    <?php endif; ?>
</div>

<style>
.cursor-pointer { cursor: pointer; }
details summary { list-style: none; }
details summary::-webkit-details-marker { display: none; }
</style>
