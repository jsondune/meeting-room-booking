<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'เกี่ยวกับระบบ';
$this->params['breadcrumbs'][] = $this->title;

// Organization info - customize as needed
$orgName = Yii::$app->name ?? 'ระบบจองห้องประชุม';
$orgFullName = 'สถาบันพระบรมราชชนก';
$orgDepartment = 'กองดิจิทัลเทคโนโลยี';
?>

<div class="site-about">
    <!-- Hero Section -->
    <div class="bg-primary text-white py-5 mb-5" style="margin-top: -1.5rem;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="bi bi-building me-3"></i><?= Html::encode($this->title) ?>
                    </h1>
                    <p class="lead mb-0 opacity-90">
                        ระบบจองห้องประชุมออนไลน์ พัฒนาเพื่อรองรับการทำงานยุคดิจิทัล
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 d-inline-block">
                        <div class="text-white-50 small">เวอร์ชัน</div>
                        <div class="h4 mb-0">1.0.0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- About Section -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 fw-bold mb-4">
                            <i class="bi bi-info-circle text-primary me-2"></i>เกี่ยวกับระบบ
                        </h2>
                        
                        <p class="text-muted">
                            ระบบจองห้องประชุมออนไลน์ (Meeting Room Booking System) พัฒนาขึ้นเพื่อเพิ่มประสิทธิภาพในการบริหารจัดการห้องประชุม
                            ของ<?= Html::encode($orgFullName) ?> โดยรองรับการจองห้องประชุมผ่านระบบออนไลน์ตลอด 24 ชั่วโมง 
                            ลดขั้นตอนการทำงานด้านเอกสาร และช่วยให้การจัดสรรทรัพยากรห้องประชุมเป็นไปอย่างมีประสิทธิภาพสูงสุด
                        </p>

                        <p class="text-muted">
                            ระบบนี้พัฒนาโดย<?= Html::encode($orgDepartment) ?> เพื่อรองรับนโยบายการเปลี่ยนผ่านสู่ดิจิทัล (Digital Transformation) 
                            ของหน่วยงานภาครัฐ และสอดคล้องกับแผนพัฒนารัฐบาลดิจิทัลของประเทศไทย
                        </p>

                        <hr class="my-4">

                        <h3 class="h5 fw-bold mb-3">
                            <i class="bi bi-lightbulb text-warning me-2"></i>วัตถุประสงค์
                        </h3>
                        <ul class="text-muted mb-4">
                            <li class="mb-2">เพิ่มความสะดวกในการจองห้องประชุมผ่านระบบออนไลน์</li>
                            <li class="mb-2">ลดความซ้ำซ้อนและข้อผิดพลาดในการจองห้องประชุม</li>
                            <li class="mb-2">เพิ่มประสิทธิภาพในการบริหารจัดการทรัพยากรห้องประชุม</li>
                            <li class="mb-2">รองรับการทำงานแบบ Paperless Office</li>
                            <li class="mb-2">มีรายงานและสถิติเพื่อการวางแผนและตัดสินใจ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="h4 fw-bold mb-4 text-center">
                    <i class="bi bi-stars text-primary me-2"></i>คุณสมบัติหลักของระบบ
                </h2>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                                </div>
                                <h5 class="card-title">จองง่าย รวดเร็ว</h5>
                                <p class="card-text text-muted small">
                                    จองห้องประชุมได้ตลอด 24 ชั่วโมง ผ่านทุกอุปกรณ์ ดูปฏิทินว่างได้แบบ Real-time
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-shield-check text-success fs-4"></i>
                                </div>
                                <h5 class="card-title">ระบบอนุมัติ</h5>
                                <p class="card-text text-muted small">
                                    รองรับ Workflow การอนุมัติ แจ้งเตือนผ่าน Email และ LINE Notify
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-display text-info fs-4"></i>
                                </div>
                                <h5 class="card-title">จัดการอุปกรณ์</h5>
                                <p class="card-text text-muted small">
                                    เบิกอุปกรณ์เสริมพร้อมการจอง เช่น โปรเจคเตอร์ ไมค์ กล้องวิดีโอคอล
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-graph-up text-warning fs-4"></i>
                                </div>
                                <h5 class="card-title">รายงานและสถิติ</h5>
                                <p class="card-text text-muted small">
                                    Dashboard แสดงสถิติการใช้งาน ส่งออกรายงานได้หลายรูปแบบ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tech Stack Section -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-4">
                            <i class="bi bi-code-slash text-primary me-2"></i>เทคโนโลยีที่ใช้
                        </h2>
                        
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-filetype-php text-primary fs-3 d-block mb-2"></i>
                                    <small class="text-muted">PHP 8.x</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-box text-success fs-3 d-block mb-2"></i>
                                    <small class="text-muted">Yii2 Framework</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-database text-info fs-3 d-block mb-2"></i>
                                    <small class="text-muted">MySQL/MariaDB</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-bootstrap text-purple fs-3 d-block mb-2"></i>
                                    <small class="text-muted">Bootstrap 5</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-4">
                            <i class="bi bi-headset text-primary me-2"></i>ติดต่อสอบถาม
                        </h2>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="fw-bold"><?= Html::encode($orgDepartment) ?></h6>
                                <p class="text-muted small mb-2">
                                    <?= Html::encode($orgFullName) ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>อาคาร 4 ชั้น 3 ตึกสำนักงานปลัดกระทรวงสาธารณสุข<br>
                                    ถนนติวานนท์ ตำบลตลาดขวัญ อำเภอเมือง จังหวัดนนทบุรี 11000
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    <span class="text-muted small">02-590-1234</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    <span class="text-muted small">digital@bitzo.co.th</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <span class="text-muted small">จันทร์ - ศุกร์ 08:30 - 16:30 น.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Version History -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-4">
                            <i class="bi bi-clock-history text-primary me-2"></i>ประวัติการพัฒนา
                        </h2>
                        
                        <div class="timeline">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">v1.0.0</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold small">มกราคม 2569</div>
                                    <p class="text-muted small mb-0">
                                        เปิดใช้งานระบบเวอร์ชันแรก รองรับการจองห้องประชุม การอนุมัติ และการแจ้งเตือน
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-secondary rounded-pill">v0.9.0</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold small">ธันวาคม 2568</div>
                                    <p class="text-muted small mb-0">
                                        ทดสอบระบบ (Beta Testing) และปรับปรุงตาม Feedback ของผู้ใช้งาน
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-secondary rounded-pill">v0.1.0</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold small">ตุลาคม 2568</div>
                                    <p class="text-muted small mb-0">
                                        เริ่มต้นพัฒนาระบบ วิเคราะห์ความต้องการ และออกแบบ UI/UX
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-purple {
    color: #7952b3 !important;
}

.card {
    border-radius: 0.75rem;
}

.timeline .d-flex:not(:last-child) {
    border-left: 2px solid #dee2e6;
    padding-left: 1rem;
    margin-left: 0.75rem;
}

.timeline .d-flex:not(:last-child)::before {
    content: '';
    width: 10px;
    height: 10px;
    background: #0d6efd;
    border-radius: 50%;
    position: absolute;
    left: -6px;
}
</style>
