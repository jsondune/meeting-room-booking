<?php
/**
 * OAuth Connections Management View
 * backend/views/profile/connections.php
 * 
 * @var yii\web\View $this
 * @var common\models\User $user
 * @var array $connections
 * @var array $providers
 * @var bool $hasPassword
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'การเชื่อมต่อบัญชี';
$this->params['breadcrumbs'][] = ['label' => 'โปรไฟล์', 'url' => ['/site/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-connections">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-link-45deg me-2"></i><?= Html::encode($this->title) ?>
                </h1>
                <p class="text-muted mb-0">จัดการการเชื่อมต่อกับบัญชีภายนอก</p>
            </div>
            <a href="<?= Url::to(['/site/profile']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>กลับ
            </a>
        </div>

        <?php if (!$hasPassword): ?>
        <!-- Warning: No Password Set -->
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <strong>คำเตือน:</strong> คุณยังไม่ได้ตั้งรหัสผ่าน หากยกเลิกการเชื่อมต่อทั้งหมด คุณจะไม่สามารถเข้าสู่ระบบได้
                <a href="<?= Url::to(['/site/change-password']) ?>" class="alert-link">ตั้งรหัสผ่านเลย</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Connected Accounts -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-check-circle text-success me-2"></i>บัญชีที่เชื่อมต่อแล้ว
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($connections)): ?>
                <div class="row g-3">
                    <?php foreach ($connections as $provider => $connection): ?>
                    <?php 
                    $providerInfo = $providers[$provider] ?? [
                        'name' => ucfirst($provider),
                        'icon' => 'link',
                        'color' => '#6c757d',
                    ];
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 48px; height: 48px; background: <?= $providerInfo['color'] ?>15;">
                                    <?php if ($provider === 'google'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                                        <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2970244 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                                        <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                                        <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7## 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                                    </svg>
                                    <?php elseif ($provider === 'microsoft'): ?>
                                    <svg width="24" height="24" viewBox="0 0 23 23">
                                        <path fill="#f35325" d="M1 1h10v10H1z"/>
                                        <path fill="#81bc06" d="M12 1h10v10H12z"/>
                                        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                                        <path fill="#ffba08" d="M12 12h10v10H12z"/>
                                    </svg>
                                    <?php elseif ($provider === 'thaid'): ?>
                                    <i class="bi bi-shield-check fs-4" style="color: #1E3A8A;"></i>
                                    <?php else: ?>
                                    <i class="bi bi-link-45deg fs-4" style="color: <?= $providerInfo['color'] ?>;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= Html::encode($providerInfo['name']) ?></h6>
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>เชื่อมต่อแล้ว
                                    </small>
                                </div>
                            </div>
                            
                            <?php if ($connection->email): ?>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-envelope me-1"></i><?= Html::encode($connection->email) ?>
                            </p>
                            <?php endif; ?>
                            
                            <p class="text-muted small mb-3">
                                <i class="bi bi-clock me-1"></i>เชื่อมต่อเมื่อ: 
                                <?php
                                $dt = new DateTime($connection->created_at);
                                $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                                echo $dt->format('j') . ' ' . $thaiMonths[(int)$dt->format('n')] . ' ' . ($dt->format('Y') + 543);
                                ?>
                            </p>
                            
                            <?= Html::a(
                                '<i class="bi bi-x-lg me-1"></i>ยกเลิกการเชื่อมต่อ',
                                ['disconnect-oauth', 'provider' => $provider],
                                [
                                    'class' => 'btn btn-outline-danger btn-sm w-100',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'คุณต้องการยกเลิกการเชื่อมต่อ ' . $providerInfo['name'] . ' ใช่หรือไม่?',
                                    ],
                                ]
                            ) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-link-45deg d-block mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">ยังไม่มีบัญชีที่เชื่อมต่อ</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Providers -->
        <?php 
        $availableProviders = array_diff_key($providers, $connections);
        if (!empty($availableProviders)): 
        ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle text-primary me-2"></i>บัญชีที่สามารถเชื่อมต่อได้
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($availableProviders as $provider => $info): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 48px; height: 48px; background: <?= $info['color'] ?>15;">
                                    <?php if ($provider === 'google'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                                        <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2970244 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                                        <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                                        <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                                    </svg>
                                    <?php elseif ($provider === 'microsoft'): ?>
                                    <svg width="24" height="24" viewBox="0 0 23 23">
                                        <path fill="#f35325" d="M1 1h10v10H1z"/>
                                        <path fill="#81bc06" d="M12 1h10v10H12z"/>
                                        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                                        <path fill="#ffba08" d="M12 12h10v10H12z"/>
                                    </svg>
                                    <?php elseif ($provider === 'thaid'): ?>
                                    <i class="bi bi-shield-check fs-4" style="color: #1E3A8A;"></i>
                                    <?php else: ?>
                                    <i class="bi bi-link-45deg fs-4" style="color: <?= $info['color'] ?>;"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= Html::encode($info['name']) ?></h6>
                                    <small class="text-muted">พร้อมเชื่อมต่อ</small>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-3"><?= Html::encode($info['description']) ?></p>
                            
                            <?php if (!empty($info['features'])): ?>
                            <ul class="small text-muted mb-3 ps-3">
                                <?php foreach ($info['features'] as $feature): ?>
                                <li><?= Html::encode($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            
                            <a href="<?= Url::to(['/auth/' . $provider]) ?>" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-plus-lg me-1"></i>เชื่อมต่อ <?= Html::encode($info['name']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Security Notice -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex">
                    <i class="bi bi-info-circle text-info me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">ข้อมูลความปลอดภัย</h6>
                        <p class="text-muted small mb-0">
                            การเชื่อมต่อบัญชีภายนอกช่วยให้คุณสามารถเข้าสู่ระบบได้สะดวกขึ้น 
                            โดยเราจะเก็บเฉพาะข้อมูลที่จำเป็นสำหรับการยืนยันตัวตนเท่านั้น 
                            คุณสามารถยกเลิกการเชื่อมต่อได้ตลอดเวลา
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
