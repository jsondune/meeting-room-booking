<?php
/**
 * Profile Connections View
 * 
 * Allows users to manage OAuth provider connections
 * 
 * @var yii\web\View $this
 * @var common\models\User $user
 * @var common\models\UserOauth[] $connections
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'เชื่อมต่อบัญชี';
$this->params['breadcrumbs'][] = ['label' => 'โปรไฟล์', 'url' => ['profile/index']];
$this->params['breadcrumbs'][] = $this->title;

// Available providers configuration
$providers = [
    'google' => [
        'name' => 'Google',
        'icon' => 'bi-google',
        'color' => '#DB4437',
        'bgColor' => '#fff',
        'description' => 'เชื่อมต่อกับบัญชี Google เพื่อเข้าสู่ระบบได้ง่ายขึ้น',
        'features' => ['เข้าสู่ระบบด้วยคลิกเดียว', 'ซิงค์ปฏิทิน Google Calendar'],
        'enabled' => !empty(Yii::$app->params['oauth']['google']['clientId']),
    ],
    'microsoft' => [
        'name' => 'Microsoft',
        'icon' => 'bi-microsoft',
        'color' => '#00A4EF',
        'bgColor' => '#fff',
        'description' => 'เชื่อมต่อกับบัญชี Microsoft สำหรับองค์กร',
        'features' => ['เข้าสู่ระบบด้วย Microsoft 365', 'ซิงค์ปฏิทิน Outlook'],
        'enabled' => !empty(Yii::$app->params['oauth']['microsoft']['clientId']),
    ],
    'thaid' => [
        'name' => 'ThaiD',
        'icon' => 'bi-person-badge',
        'color' => '#0052CC',
        'bgColor' => '#E6F0FF',
        'description' => 'ยืนยันตัวตนด้วยบัตรประชาชนดิจิทัล',
        'features' => ['ยืนยันตัวตนที่น่าเชื่อถือ', 'รองรับหน่วยงานราชการ'],
        'enabled' => !empty(Yii::$app->params['oauth']['thaid']['clientId']),
    ],
];

// Map connections by provider
$connectedProviders = [];
foreach ($connections as $connection) {
    $connectedProviders[$connection->provider] = $connection;
}

// Check if user has password set
$hasPassword = !empty($user->password_hash);
$connectedCount = count($connections);
?>

<div class="profile-connections">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-link-45deg text-primary fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">เชื่อมต่อบัญชี</h4>
                            <p class="text-muted mb-0">จัดการการเชื่อมต่อกับบริการภายนอกเพื่อเข้าสู่ระบบได้สะดวกยิ่งขึ้น</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Connection Status Summary -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-success bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-3">
                            <div class="fs-2 fw-bold text-success"><?= $connectedCount ?></div>
                            <div class="small text-muted">บัญชีที่เชื่อมต่อแล้ว</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-3">
                            <div class="fs-2 fw-bold text-primary"><?= count(array_filter($providers, fn($p) => $p['enabled'])) ?></div>
                            <div class="small text-muted">บริการที่รองรับ</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card <?= $hasPassword ? 'bg-success' : 'bg-warning' ?> bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi <?= $hasPassword ? 'bi-shield-check text-success' : 'bi-shield-exclamation text-warning' ?> fs-2"></i>
                            <div class="small text-muted"><?= $hasPassword ? 'มีรหัสผ่าน' : 'ยังไม่ได้ตั้งรหัสผ่าน' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Provider Cards -->
            <div class="row g-4">
                <?php foreach ($providers as $providerId => $provider): ?>
                <?php if (!$provider['enabled']) continue; ?>
                <?php $connection = $connectedProviders[$providerId] ?? null; ?>
                
                <div class="col-12">
                    <div class="card border-0 shadow-sm provider-card <?= $connection ? 'connected' : '' ?>">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- Provider Info -->
                                <div class="col-md-7">
                                    <div class="d-flex align-items-start">
                                        <div class="provider-icon me-3" style="background-color: <?= $provider['bgColor'] ?>">
                                            <i class="bi <?= $provider['icon'] ?>" style="color: <?= $provider['color'] ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="mb-0"><?= Html::encode($provider['name']) ?></h5>
                                                <?php if ($connection): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>เชื่อมต่อแล้ว
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-muted mb-2"><?= Html::encode($provider['description']) ?></p>
                                            
                                            <?php if ($connection): ?>
                                            <?php $profileData = $connection->getProfileData(); ?>
                                            <div class="connected-info bg-light rounded p-2 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($profileData['picture'])): ?>
                                                    <img src="<?= Html::encode($profileData['picture']) ?>" 
                                                         class="rounded-circle me-2" width="32" height="32"
                                                         onerror="this.style.display='none'">
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-medium small">
                                                            <?= Html::encode($profileData['name'] ?? $profileData['email'] ?? 'ผู้ใช้') ?>
                                                        </div>
                                                        <?php if (!empty($profileData['email'])): ?>
                                                        <div class="text-muted small"><?= Html::encode($profileData['email']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>
                                                เชื่อมต่อเมื่อ <?= Yii::$app->formatter->asDatetime($connection->created_at) ?>
                                            </div>
                                            <?php else: ?>
                                            <ul class="feature-list small text-muted mb-0">
                                                <?php foreach ($provider['features'] as $feature): ?>
                                                <li><i class="bi bi-check2 text-success me-1"></i><?= Html::encode($feature) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <?php if ($connection): ?>
                                        <?php
                                        // Check if user can disconnect this provider
                                        $canDisconnect = $hasPassword || $connectedCount > 1;
                                        ?>
                                        <?php if ($canDisconnect): ?>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDisconnect('<?= $providerId ?>', '<?= Html::encode($provider['name']) ?>')">
                                            <i class="bi bi-x-circle me-1"></i>ยกเลิกการเชื่อมต่อ
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-outline-secondary" disabled
                                                data-bs-toggle="tooltip" 
                                                title="ไม่สามารถยกเลิกได้ โปรดตั้งรหัสผ่านก่อน">
                                            <i class="bi bi-x-circle me-1"></i>ยกเลิกการเชื่อมต่อ
                                        </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="<?= Url::to(['/oauth/connect', 'provider' => $providerId]) ?>" 
                                           class="btn btn-primary">
                                            <i class="bi bi-link-45deg me-1"></i>เชื่อมต่อ <?= Html::encode($provider['name']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Security Notice -->
            <div class="alert alert-info mt-4">
                <div class="d-flex">
                    <i class="bi bi-info-circle fs-5 me-2"></i>
                    <div>
                        <strong>หมายเหตุด้านความปลอดภัย</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>การเชื่อมต่อบัญชีจะช่วยให้คุณเข้าสู่ระบบได้โดยไม่ต้องใช้รหัสผ่าน</li>
                            <li>ข้อมูลที่ใช้คือชื่อ อีเมล และรูปโปรไฟล์เท่านั้น</li>
                            <li>คุณสามารถยกเลิกการเชื่อมต่อได้ตลอดเวลา</li>
                            <?php if (!$hasPassword): ?>
                            <li class="text-warning">
                                <strong>แนะนำ:</strong> 
                                <a href="<?= Url::to(['/profile/change-password']) ?>">ตั้งรหัสผ่าน</a> 
                                เพื่อใช้เป็นวิธีเข้าสู่ระบบสำรอง
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Disconnect Confirmation Modal -->
<div class="modal fade" id="disconnectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    ยืนยันการยกเลิกเชื่อมต่อ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการยกเลิกการเชื่อมต่อกับ <strong id="providerName"></strong> หรือไม่?</p>
                <p class="text-muted small mb-0">
                    หลังจากยกเลิก คุณจะไม่สามารถใช้บัญชีนี้ในการเข้าสู่ระบบได้ 
                    แต่สามารถเชื่อมต่อใหม่ได้ตลอดเวลา
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="disconnectForm" method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>ยืนยันยกเลิกการเชื่อมต่อ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 64px;
    height: 64px;
}

.provider-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.provider-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent !important;
}

.provider-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.provider-card.connected {
    border-left-color: #198754 !important;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    margin-bottom: 0.25rem;
}

.connected-info {
    border: 1px solid #e9ecef;
}
</style>

<?php
$this->registerJs(<<<JS
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Confirm disconnect function
window.confirmDisconnect = function(provider, providerName) {
    document.getElementById('providerName').textContent = providerName;
    document.getElementById('disconnectForm').action = '/oauth/disconnect?provider=' + provider;
    var modal = new bootstrap.Modal(document.getElementById('disconnectModal'));
    modal.show();
};
JS);
?>
