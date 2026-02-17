<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'รายงาน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <div class="row g-4">
        <!-- Usage Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-bar-chart-line text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">รายงานการใช้งาน</h4>
                    <p class="card-text text-muted">
                        สถิติการใช้งานห้องประชุม อัตราการใช้งาน การจองตามช่วงเวลา หน่วยงาน และผู้ใช้งาน
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>จำนวนการจองและชั่วโมงใช้งาน</li>
                        <li><i class="bi bi-check text-success me-1"></i>อัตราการใช้งานห้องประชุม</li>
                        <li><i class="bi bi-check text-success me-1"></i>การจองตามช่วงเวลาและวัน</li>
                        <li><i class="bi bi-check text-success me-1"></i>ห้องและผู้ใช้ที่ใช้งานมากที่สุด</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/usage']) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-eye me-1"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-cash-stack text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">รายงานรายได้</h4>
                    <p class="card-text text-muted">
                        รายได้จากการจองห้องประชุม ค่าอุปกรณ์ ค่าบริการเสริม และสถานะการชำระเงิน
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>รายได้รวมและแยกประเภท</li>
                        <li><i class="bi bi-check text-success me-1"></i>แนวโน้มรายได้ตามช่วงเวลา</li>
                        <li><i class="bi bi-check text-success me-1"></i>รายได้ตามห้องและหน่วยงาน</li>
                        <li><i class="bi bi-check text-success me-1"></i>สถานะการชำระเงิน</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/revenue']) ?>" class="btn btn-success w-100">
                        <i class="bi bi-eye me-1"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>

        <!-- Audit Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-check text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">รายงาน Audit Log</h4>
                    <p class="card-text text-muted">
                        บันทึกกิจกรรมทั้งหมดในระบบ การเข้าสู่ระบบ การแก้ไขข้อมูล และการแจ้งเตือนความปลอดภัย
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>บันทึกกิจกรรมผู้ใช้</li>
                        <li><i class="bi bi-check text-success me-1"></i>การเข้า-ออกจากระบบ</li>
                        <li><i class="bi bi-check text-success me-1"></i>การแก้ไขข้อมูลสำคัญ</li>
                        <li><i class="bi bi-check text-success me-1"></i>การแจ้งเตือนความปลอดภัย</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/audit']) ?>" class="btn btn-info w-100">
                        <i class="bi bi-eye me-1"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>

        <!-- Room Statistics -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-door-open text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">สถิติห้องประชุม</h4>
                    <p class="card-text text-muted">
                        รายละเอียดการใช้งานห้องประชุมแต่ละห้อง อัตราการใช้งาน และช่วงเวลายอดนิยม
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>อัตราการใช้งานแต่ละห้อง</li>
                        <li><i class="bi bi-check text-success me-1"></i>ช่วงเวลายอดนิยม</li>
                        <li><i class="bi bi-check text-success me-1"></i>ผู้ใช้งานประจำ</li>
                        <li><i class="bi bi-check text-success me-1"></i>ประวัติการบำรุงรักษา</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/room']) ?>" class="btn btn-warning w-100">
                        <i class="bi bi-eye me-1"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>

        <!-- Department Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-building text-secondary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">รายงานหน่วยงาน</h4>
                    <p class="card-text text-muted">
                        สถิติการใช้งานห้องประชุมแยกตามหน่วยงาน งบประมาณ และการเปรียบเทียบ
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>การใช้งานแต่ละหน่วยงาน</li>
                        <li><i class="bi bi-check text-success me-1"></i>เปรียบเทียบระหว่างหน่วยงาน</li>
                        <li><i class="bi bi-check text-success me-1"></i>ค่าใช้จ่ายตามหน่วยงาน</li>
                        <li><i class="bi bi-check text-success me-1"></i>แนวโน้มการใช้งาน</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/department']) ?>" class="btn btn-secondary w-100">
                        <i class="bi bi-eye me-1"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>

        <!-- Export Center -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center p-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-download text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="card-title">ศูนย์ส่งออกรายงาน</h4>
                    <p class="card-text text-muted">
                        ส่งออกรายงานในรูปแบบต่างๆ รวมถึงการตั้งเวลาส่งรายงานอัตโนมัติ
                    </p>
                    <ul class="list-unstyled text-start small text-muted mb-3">
                        <li><i class="bi bi-check text-success me-1"></i>ส่งออก PDF, Excel, CSV</li>
                        <li><i class="bi bi-check text-success me-1"></i>ตั้งเวลาส่งรายงานอัตโนมัติ</li>
                        <li><i class="bi bi-check text-success me-1"></i>ส่งรายงานทางอีเมล</li>
                        <li><i class="bi bi-check text-success me-1"></i>ประวัติการส่งออก</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <a href="<?= Url::to(['report/export']) ?>" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-up me-1"></i>ส่งออกรายงาน
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="bi bi-lightning text-warning me-2"></i>ข้อมูลด่วน (วันนี้)</h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">การจองวันนี้</h6>
                            <h3 class="card-title mb-0">12</h3>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">รายได้วันนี้</h6>
                            <h3 class="card-title mb-0">฿8,500</h3>
                        </div>
                        <i class="bi bi-cash-coin fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ชั่วโมงใช้งาน</h6>
                            <h3 class="card-title mb-0">28</h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">รอดำเนินการ</h6>
                            <h3 class="card-title mb-0">5</h3>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
