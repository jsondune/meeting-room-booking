<?php

use yii\helpers\Html;

$this->title = 'คำถามที่พบบ่อย';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-faq">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="bi bi-patch-question me-2"></i><?= Html::encode($this->title) ?>
                    </h1>
                    <p class="lead text-muted">FAQ - Frequently Asked Questions</p>
                </div>

                <div class="accordion" id="faqAccordion">
                    <!-- การจองห้องประชุม -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3"><i class="bi bi-calendar-check me-2"></i>การจองห้องประชุม</h5>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    จองห้องประชุมได้อย่างไร?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>เข้าสู่ระบบด้วยบัญชีผู้ใช้ของคุณ</li>
                                        <li>คลิกที่เมนู "จองห้องประชุม"</li>
                                        <li>เลือกห้องประชุมที่ต้องการ</li>
                                        <li>เลือกวันและเวลาที่ต้องการจอง</li>
                                        <li>กรอกรายละเอียดการประชุม</li>
                                        <li>ยืนยันการจอง</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    จองล่วงหน้าได้กี่วัน?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    สามารถจองล่วงหน้าได้สูงสุด 30 วัน ขึ้นอยู่กับนโยบายของแต่ละห้องประชุม
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    จองห้องประชุมซ้ำ (Recurring) ได้หรือไม่?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ได้ครับ ระบบรองรับการจองแบบซ้ำ ทั้งรายวัน รายสัปดาห์ และรายเดือน
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- การยกเลิก/แก้ไข -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3"><i class="bi bi-x-circle me-2"></i>การยกเลิก/แก้ไข</h5>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    ยกเลิกการจองได้อย่างไร?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ไปที่หน้า "การจองของฉัน" เลือกการจองที่ต้องการยกเลิก แล้วกดปุ่ม "ยกเลิกการจอง"
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    สามารถแก้ไขรายละเอียดการจองได้หรือไม่?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ได้ครับ สามารถแก้ไขได้ก่อนถึงวันประชุม โดยไปที่รายละเอียดการจองแล้วกดปุ่มแก้ไข
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- อุปกรณ์และสิ่งอำนวยความสะดวก -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3"><i class="bi bi-box-seam me-2"></i>อุปกรณ์และสิ่งอำนวยความสะดวก</h5>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    ขออุปกรณ์เพิ่มเติมได้อย่างไร?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ในขั้นตอนการจอง จะมีส่วนให้เลือกอุปกรณ์เพิ่มเติม เช่น โปรเจคเตอร์ ไมโครโฟน เป็นต้น
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    ห้องประชุมมีอุปกรณ์อะไรบ้าง?
                                </button>
                            </h2>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    แต่ละห้องจะมีรายละเอียดอุปกรณ์ประจำห้องแสดงอยู่ในหน้ารายละเอียดห้อง สามารถดูได้ก่อนทำการจอง
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="text-center mt-5 p-4 bg-light rounded">
                    <h5>ไม่พบคำตอบที่ต้องการ?</h5>
                    <p class="text-muted">ติดต่อเจ้าหน้าที่ได้ที่</p>
                    <a href="mailto:support@example.com" class="btn btn-primary">
                        <i class="bi bi-envelope me-2"></i>support@example.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
