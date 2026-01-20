<?php

use yii\helpers\Html;

$this->title = 'วิธีใช้งาน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-help">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="bi bi-question-circle me-2"></i><?= Html::encode($this->title) ?>
                    </h1>
                    <p class="lead text-muted">คู่มือการใช้งานระบบจองห้องประชุม</p>
                </div>

                <!-- Quick Start -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-rocket-takeoff me-2"></i>เริ่มต้นใช้งาน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <span class="fs-2 text-primary fw-bold">1</span>
                                </div>
                                <h6>เลือกห้องประชุม</h6>
                                <p class="text-muted small">ดูรายละเอียดห้อง ความจุ และสิ่งอำนวยความสะดวก</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <span class="fs-2 text-primary fw-bold">2</span>
                                </div>
                                <h6>เลือกวันและเวลา</h6>
                                <p class="text-muted small">ตรวจสอบช่วงเวลาว่างและเลือกเวลาที่ต้องการ</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <span class="fs-2 text-primary fw-bold">3</span>
                                </div>
                                <h6>ยืนยันการจอง</h6>
                                <p class="text-muted small">กรอกรายละเอียดการประชุมและยืนยันการจอง</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Accordion -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>คำถามที่พบบ่อย</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        จองห้องประชุมได้ล่วงหน้ากี่วัน?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        สามารถจองห้องประชุมได้ล่วงหน้าสูงสุด 30 วัน ขึ้นอยู่กับการตั้งค่าของแต่ละห้อง
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        สามารถยกเลิกการจองได้หรือไม่?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        ได้ครับ สามารถยกเลิกการจองได้ก่อนถึงวันประชุม โดยไปที่หน้า "การจองของฉัน" แล้วกดปุ่มยกเลิก
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        ต้องรอการอนุมัติหรือไม่?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        บางห้องประชุมต้องรอการอนุมัติจากผู้ดูแลระบบ คุณจะได้รับแจ้งเตือนเมื่อการจองได้รับการอนุมัติ
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        จะขออุปกรณ์เพิ่มเติมได้อย่างไร?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        ในขั้นตอนการจอง คุณสามารถเลือกอุปกรณ์เพิ่มเติมที่ต้องการได้ เช่น โปรเจคเตอร์ ไมโครโฟน หรืออุปกรณ์อื่นๆ ที่มีให้บริการ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-headset me-2"></i>ติดต่อเรา</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="bi bi-telephone text-primary fs-1 mb-3"></i>
                                    <h6>โทรศัพท์</h6>
                                    <p class="text-muted mb-0">02-xxx-xxxx</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="bi bi-envelope text-primary fs-1 mb-3"></i>
                                    <h6>อีเมล</h6>
                                    <p class="text-muted mb-0">support@example.com</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="bi bi-clock text-primary fs-1 mb-3"></i>
                                    <h6>เวลาทำการ</h6>
                                    <p class="text-muted mb-0">จันทร์ - ศุกร์ 08:30 - 16:30 น.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
