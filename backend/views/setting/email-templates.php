<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'เทมเพลตอีเมล';
$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่าระบบ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Sample email templates
$templates = [
    [
        'id' => 1,
        'code' => 'booking_created',
        'name' => 'การจองถูกสร้าง',
        'name_en' => 'Booking Created',
        'description' => 'ส่งเมื่อผู้ใช้สร้างการจองใหม่',
        'subject' => 'ยืนยันการจองห้องประชุม - {{room_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 2,
        'code' => 'booking_approved',
        'name' => 'การจองได้รับการอนุมัติ',
        'name_en' => 'Booking Approved',
        'description' => 'ส่งเมื่อการจองได้รับการอนุมัติ',
        'subject' => 'การจองห้องประชุมได้รับการอนุมัติ - {{room_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 3,
        'code' => 'booking_rejected',
        'name' => 'การจองถูกปฏิเสธ',
        'name_en' => 'Booking Rejected',
        'description' => 'ส่งเมื่อการจองถูกปฏิเสธ',
        'subject' => 'การจองห้องประชุมถูกปฏิเสธ - {{room_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 4,
        'code' => 'booking_cancelled',
        'name' => 'การจองถูกยกเลิก',
        'name_en' => 'Booking Cancelled',
        'description' => 'ส่งเมื่อการจองถูกยกเลิก',
        'subject' => 'การจองห้องประชุมถูกยกเลิก - {{room_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 5,
        'code' => 'booking_reminder',
        'name' => 'เตือนความจำก่อนประชุม',
        'name_en' => 'Booking Reminder',
        'description' => 'ส่งเตือนก่อนถึงเวลาประชุม',
        'subject' => 'เตือน: การประชุมของคุณจะเริ่มในอีก {{hours}} ชั่วโมง',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 6,
        'code' => 'booking_modified',
        'name' => 'การจองถูกแก้ไข',
        'name_en' => 'Booking Modified',
        'description' => 'ส่งเมื่อรายละเอียดการจองถูกเปลี่ยนแปลง',
        'subject' => 'รายละเอียดการจองถูกเปลี่ยนแปลง - {{room_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 7,
        'code' => 'admin_new_booking',
        'name' => 'แจ้งเตือนผู้ดูแล - การจองใหม่',
        'name_en' => 'Admin Notification - New Booking',
        'description' => 'แจ้งผู้ดูแลเมื่อมีการจองใหม่ที่รอการอนุมัติ',
        'subject' => '[รอการอนุมัติ] การจองใหม่: {{room_name}} โดย {{user_name}}',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 8,
        'code' => 'welcome_email',
        'name' => 'ยินดีต้อนรับผู้ใช้ใหม่',
        'name_en' => 'Welcome Email',
        'description' => 'ส่งเมื่อมีการสร้างบัญชีผู้ใช้ใหม่',
        'subject' => 'ยินดีต้อนรับสู่ระบบจองห้องประชุม',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 9,
        'code' => 'password_reset',
        'name' => 'รีเซ็ตรหัสผ่าน',
        'name_en' => 'Password Reset',
        'description' => 'ส่งเมื่อผู้ใช้ขอรีเซ็ตรหัสผ่าน',
        'subject' => 'รีเซ็ตรหัสผ่านของคุณ',
        'status' => 'active',
        'last_updated' => '2025-01-15 10:30:00',
    ],
    [
        'id' => 10,
        'code' => 'account_verification',
        'name' => 'ยืนยันบัญชี',
        'name_en' => 'Account Verification',
        'description' => 'ส่งเพื่อยืนยันอีเมลของผู้ใช้',
        'subject' => 'ยืนยันอีเมลของคุณ',
        'status' => 'inactive',
        'last_updated' => '2025-01-15 10:30:00',
    ],
];

$categories = [
    'booking' => [
        'label' => 'การจอง',
        'icon' => 'bi-calendar-check',
        'templates' => ['booking_created', 'booking_approved', 'booking_rejected', 'booking_cancelled', 'booking_reminder', 'booking_modified'],
    ],
    'admin' => [
        'label' => 'แจ้งเตือนผู้ดูแล',
        'icon' => 'bi-shield-check',
        'templates' => ['admin_new_booking'],
    ],
    'account' => [
        'label' => 'บัญชีผู้ใช้',
        'icon' => 'bi-person-check',
        'templates' => ['welcome_email', 'password_reset', 'account_verification'],
    ],
];

// Sample variables
$variables = [
    'user' => [
        '{{user_name}}' => 'ชื่อผู้ใช้',
        '{{user_email}}' => 'อีเมลผู้ใช้',
        '{{user_department}}' => 'หน่วยงาน',
        '{{user_phone}}' => 'เบอร์โทรศัพท์',
    ],
    'booking' => [
        '{{booking_id}}' => 'รหัสการจอง',
        '{{booking_date}}' => 'วันที่จอง',
        '{{booking_time}}' => 'เวลาจอง',
        '{{booking_duration}}' => 'ระยะเวลา',
        '{{booking_purpose}}' => 'วัตถุประสงค์',
        '{{booking_status}}' => 'สถานะการจอง',
    ],
    'room' => [
        '{{room_name}}' => 'ชื่อห้อง',
        '{{room_location}}' => 'ที่ตั้งห้อง',
        '{{room_capacity}}' => 'ความจุห้อง',
    ],
    'system' => [
        '{{site_name}}' => 'ชื่อเว็บไซต์',
        '{{site_url}}' => 'URL เว็บไซต์',
        '{{contact_email}}' => 'อีเมลติดต่อ',
        '{{current_date}}' => 'วันที่ปัจจุบัน',
        '{{current_time}}' => 'เวลาปัจจุบัน',
    ],
];

$selectedTemplate = $templates[0]; // Default to first template
?>

<div class="setting-email-templates">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">ปรับแต่งเทมเพลตอีเมลที่ส่งจากระบบ</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                <i class="bi bi-envelope-check me-1"></i> ทดสอบอีเมล
            </button>
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#variablesModal">
                <i class="bi bi-braces me-1"></i> ตัวแปร
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Sidebar - Template List -->
        <div class="col-lg-4 mb-4">
            <!-- Category Navigation -->
            <?php foreach ($categories as $catKey => $category): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi <?= $category['icon'] ?> me-2"></i><?= $category['label'] ?>
                        </h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($templates as $template): ?>
                            <?php if (in_array($template['code'], $category['templates'])): ?>
                                <a href="#" class="list-group-item list-group-item-action template-item <?= $template['id'] === 1 ? 'active' : '' ?>" data-template-id="<?= $template['id'] ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium"><?= Html::encode($template['name']) ?></div>
                                            <small class="<?= $template['id'] === 1 ? 'text-white-50' : 'text-muted' ?>"><?= Html::encode($template['name_en']) ?></small>
                                        </div>
                                        <?php if ($template['status'] === 'active'): ?>
                                            <span class="badge bg-success">เปิดใช้</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ปิด</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Right Side - Template Editor -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0" id="templateTitle"><?= Html::encode($selectedTemplate['name']) ?></h5>
                        <small class="text-muted" id="templateDescription"><?= Html::encode($selectedTemplate['description']) ?></small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="templateStatus" checked>
                        <label class="form-check-label" for="templateStatus">เปิดใช้งาน</label>
                    </div>
                </div>
                <div class="card-body">
                    <form id="templateForm">
                        <!-- Subject -->
                        <div class="mb-4">
                            <label class="form-label">หัวข้ออีเมล (Subject)</label>
                            <input type="text" class="form-control" id="emailSubject" value="<?= Html::encode($selectedTemplate['subject']) ?>">
                            <small class="text-muted">สามารถใช้ตัวแปรได้ เช่น {{room_name}}, {{user_name}}</small>
                        </div>

                        <!-- Tabs for HTML/Text -->
                        <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html-content" type="button">
                                    <i class="bi bi-code-slash me-1"></i> HTML
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-content" type="button">
                                    <i class="bi bi-file-text me-1"></i> Plain Text
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-content" type="button">
                                    <i class="bi bi-eye me-1"></i> ตัวอย่าง
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content border border-top-0 rounded-bottom" id="contentTabsContent">
                            <!-- HTML Content -->
                            <div class="tab-pane fade show active p-3" id="html-content" role="tabpanel">
                                <div class="mb-2">
                                    <div class="btn-toolbar mb-2">
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" title="Bold" onclick="insertTag('strong')"><i class="bi bi-type-bold"></i></button>
                                            <button type="button" class="btn btn-outline-secondary" title="Italic" onclick="insertTag('em')"><i class="bi bi-type-italic"></i></button>
                                            <button type="button" class="btn btn-outline-secondary" title="Underline" onclick="insertTag('u')"><i class="bi bi-type-underline"></i></button>
                                        </div>
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" title="Link" onclick="insertLink()"><i class="bi bi-link"></i></button>
                                            <button type="button" class="btn btn-outline-secondary" title="Image" onclick="insertImage()"><i class="bi bi-image"></i></button>
                                        </div>
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" title="Button" onclick="insertButton()"><i class="bi bi-square"></i> Button</button>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Insert Variable">
                                                <i class="bi bi-braces"></i> ตัวแปร
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" style="max-height: 300px; overflow-y: auto;">
                                                <?php foreach ($variables as $group => $vars): ?>
                                                    <li><h6 class="dropdown-header"><?= ucfirst($group) ?></h6></li>
                                                    <?php foreach ($vars as $var => $label): ?>
                                                        <li><a class="dropdown-item small" href="#" onclick="insertVariable('<?= $var ?>')"><?= $var ?> - <?= $label ?></a></li>
                                                    <?php endforeach; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <textarea class="form-control font-monospace" id="htmlContent" rows="15" style="font-size: 13px;"><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{site_name}}</title>
</head>
<body style="font-family: 'Sarabun', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{site_url}}/images/logo.png" alt="{{site_name}}" style="max-width: 150px;">
        </div>
        
        <!-- Content -->
        <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #0d6efd; margin-top: 0;">ยืนยันการจองห้องประชุม</h2>
            
            <p>เรียน คุณ{{user_name}},</p>
            
            <p>การจองห้องประชุมของคุณได้รับการบันทึกเรียบร้อยแล้ว รายละเอียดมีดังนี้:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; width: 40%;">รหัสการจอง:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{booking_id}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">ห้องประชุม:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{room_name}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">วันที่:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{booking_date}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">เวลา:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{booking_time}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">วัตถุประสงค์:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{booking_purpose}}</td>
                </tr>
            </table>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{site_url}}/booking/view?id={{booking_id}}" style="display: inline-block; background: #0d6efd; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">ดูรายละเอียดการจอง</a>
            </div>
            
            <p style="color: #666; font-size: 14px;">หากคุณมีข้อสงสัย โปรดติดต่อ {{contact_email}}</p>
        </div>
        
        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            <p>{{site_name}}</p>
            <p>อีเมลนี้ถูกส่งโดยอัตโนมัติ โปรดอย่าตอบกลับ</p>
        </div>
    </div>
</body>
</html></textarea>
                            </div>

                            <!-- Plain Text Content -->
                            <div class="tab-pane fade p-3" id="text-content" role="tabpanel">
                                <textarea class="form-control font-monospace" id="textContent" rows="15" style="font-size: 13px;">ยืนยันการจองห้องประชุม
==============================

เรียน คุณ{{user_name}},

การจองห้องประชุมของคุณได้รับการบันทึกเรียบร้อยแล้ว รายละเอียดมีดังนี้:

รหัสการจอง: {{booking_id}}
ห้องประชุม: {{room_name}}
วันที่: {{booking_date}}
เวลา: {{booking_time}}
วัตถุประสงค์: {{booking_purpose}}

ดูรายละเอียดการจอง: {{site_url}}/booking/view?id={{booking_id}}

หากคุณมีข้อสงสัย โปรดติดต่อ {{contact_email}}

--
{{site_name}}
อีเมลนี้ถูกส่งโดยอัตโนมัติ โปรดอย่าตอบกลับ</textarea>
                            </div>

                            <!-- Preview -->
                            <div class="tab-pane fade p-0" id="preview-content" role="tabpanel">
                                <div class="bg-light p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">ตัวอย่างอีเมล (ข้อมูลจำลอง)</span>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary active" id="previewDesktop">
                                                <i class="bi bi-display"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="previewMobile">
                                                <i class="bi bi-phone"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="previewFrame" style="background: #fff; min-height: 400px; padding: 20px;">
                                    <iframe id="emailPreview" style="width: 100%; height: 500px; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            แก้ไขล่าสุด: <?= $selectedTemplate['last_updated'] ?>
                        </small>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetTemplate()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ตเป็นค่าเริ่มต้น
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveTemplate()">
                                <i class="bi bi-check-lg me-1"></i> บันทึก
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variables Reference Modal -->
<div class="modal fade" id="variablesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-braces me-2"></i>ตัวแปรที่ใช้ได้ในเทมเพลต
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    ใช้ตัวแปรเหล่านี้ในเทมเพลตเพื่อแสดงข้อมูลแบบไดนามิก ตัวแปรจะถูกแทนที่ด้วยค่าจริงเมื่อส่งอีเมล
                </div>
                
                <div class="row">
                    <?php foreach ($variables as $group => $vars): ?>
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary mb-3">
                                <?php
                                $groupIcons = [
                                    'user' => 'bi-person',
                                    'booking' => 'bi-calendar-check',
                                    'room' => 'bi-door-open',
                                    'system' => 'bi-gear',
                                ];
                                ?>
                                <i class="bi <?= $groupIcons[$group] ?? 'bi-tag' ?> me-2"></i>
                                <?= ucfirst($group) ?>
                            </h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>ตัวแปร</th>
                                        <th>คำอธิบาย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vars as $var => $label): ?>
                                        <tr>
                                            <td><code class="text-primary"><?= $var ?></code></td>
                                            <td><?= $label ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-check me-2"></i>ส่งอีเมลทดสอบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">เทมเพลต</label>
                    <select class="form-select" id="testTemplate">
                        <?php foreach ($templates as $template): ?>
                            <option value="<?= $template['id'] ?>"><?= Html::encode($template['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ส่งไปยังอีเมล</label>
                    <input type="email" class="form-control" id="testEmail" placeholder="test@example.com">
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    อีเมลทดสอบจะใช้ข้อมูลจำลอง ตัวแปรจะถูกแทนที่ด้วยค่าตัวอย่าง
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">
                    <i class="bi bi-send me-1"></i> ส่งอีเมลทดสอบ
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Template selection
document.querySelectorAll('.template-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active state
        document.querySelectorAll('.template-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        // Load template (AJAX in real implementation)
        const templateId = this.dataset.templateId;
        console.log('Load template:', templateId);
    });
});

// Preview tab
document.getElementById('preview-tab').addEventListener('shown.bs.tab', function() {
    updatePreview();
});

function updatePreview() {
    const htmlContent = document.getElementById('htmlContent').value;
    const iframe = document.getElementById('emailPreview');
    
    // Replace variables with sample data
    let preview = htmlContent
        .replace(/\{\{user_name\}\}/g, 'สมชาย ใจดี')
        .replace(/\{\{user_email\}\}/g, 'somchai@example.com')
        .replace(/\{\{user_department\}\}/g, 'กองเทคโนโลยีสารสนเทศ')
        .replace(/\{\{booking_id\}\}/g, 'BK-2025-0001')
        .replace(/\{\{booking_date\}\}/g, '15 มกราคม 2568')
        .replace(/\{\{booking_time\}\}/g, '09:00 - 12:00 น.')
        .replace(/\{\{booking_purpose\}\}/g, 'ประชุมทีมประจำสัปดาห์')
        .replace(/\{\{room_name\}\}/g, 'ห้องประชุมใหญ่ ชั้น 5')
        .replace(/\{\{room_location\}\}/g, 'อาคาร A ชั้น 5')
        .replace(/\{\{site_name\}\}/g, 'ระบบจองห้องประชุม')
        .replace(/\{\{site_url\}\}/g, 'https://meeting.example.com')
        .replace(/\{\{contact_email\}\}/g, 'support@example.com');
    
    iframe.srcdoc = preview;
}

// Preview device toggle
document.getElementById('previewDesktop').addEventListener('click', function() {
    document.getElementById('emailPreview').style.width = '100%';
    this.classList.add('active');
    document.getElementById('previewMobile').classList.remove('active');
});

document.getElementById('previewMobile').addEventListener('click', function() {
    document.getElementById('emailPreview').style.width = '375px';
    this.classList.add('active');
    document.getElementById('previewDesktop').classList.remove('active');
});

// Insert tag helper
function insertTag(tag) {
    const textarea = document.getElementById('htmlContent');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selected = text.substring(start, end);
    
    const replacement = '<' + tag + '>' + selected + '</' + tag + '>';
    textarea.value = text.substring(0, start) + replacement + text.substring(end);
    textarea.focus();
}

// Insert variable
function insertVariable(variable) {
    const textarea = document.getElementById('htmlContent');
    const start = textarea.selectionStart;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + variable + text.substring(start);
    textarea.focus();
}

// Insert link
function insertLink() {
    const url = prompt('Enter URL:', 'https://');
    if (url) {
        const textarea = document.getElementById('htmlContent');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const selected = text.substring(start, end) || 'Link Text';
        
        const link = '<a href="' + url + '">' + selected + '</a>';
        textarea.value = text.substring(0, start) + link + text.substring(end);
        textarea.focus();
    }
}

// Insert image
function insertImage() {
    const url = prompt('Enter image URL:', 'https://');
    if (url) {
        const textarea = document.getElementById('htmlContent');
        const start = textarea.selectionStart;
        const text = textarea.value;
        
        const img = '<img src="' + url + '" alt="" style="max-width: 100%;">';
        textarea.value = text.substring(0, start) + img + text.substring(start);
        textarea.focus();
    }
}

// Insert button
function insertButton() {
    const textarea = document.getElementById('htmlContent');
    const start = textarea.selectionStart;
    const text = textarea.value;
    
    const button = '<a href="#" style="display: inline-block; background: #0d6efd; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Button Text</a>';
    textarea.value = text.substring(0, start) + button + text.substring(start);
    textarea.focus();
}

// Save template
function saveTemplate() {
    const data = {
        subject: document.getElementById('emailSubject').value,
        html: document.getElementById('htmlContent').value,
        text: document.getElementById('textContent').value,
        status: document.getElementById('templateStatus').checked ? 'active' : 'inactive'
    };
    
    console.log('Save template:', data);
    
    // Show success message
    alert('บันทึกเทมเพลตเรียบร้อยแล้ว');
}

// Reset template
function resetTemplate() {
    if (confirm('คุณต้องการรีเซ็ตเทมเพลตเป็นค่าเริ่มต้นหรือไม่? การเปลี่ยนแปลงที่ยังไม่บันทึกจะหายไป')) {
        console.log('Reset template');
        location.reload();
    }
}

// Send test email
function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    if (!email) {
        alert('โปรดกรอกอีเมล');
        return;
    }
    
    console.log('Send test email to:', email);
    
    // Show success message
    alert('ส่งอีเมลทดสอบเรียบร้อยแล้ว');
    bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
}
JS;
$this->registerJs($js);
?>
