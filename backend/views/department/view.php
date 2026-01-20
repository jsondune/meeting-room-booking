<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Department $model */

$this->title = $model->name ?? 'สำนักงานอธิการบดี';
$this->params['breadcrumbs'][] = ['label' => 'จัดการหน่วยงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Sample data
$department = [
    'id' => 1,
    'name' => 'สำนักงานอธิการบดี',
    'name_en' => 'Office of the President',
    'code' => 'PRES',
    'short_name' => 'สนอ.',
    'description' => 'หน่วยงานหลักที่รับผิดชอบการบริหารจัดการมหาวิทยาลัยในภาพรวม ดูแลนโยบายและยุทธศาสตร์ขององค์กร',
    'parent' => null,
    'level' => 1,
    'status' => 'active',
    'color' => '#3b82f6',
    'phone' => '02-123-4567',
    'fax' => '02-123-4568',
    'email' => 'president@gmail.com',
    'website' => 'https://president.gmail.com',
    'address' => 'อาคารสำนักงานอธิการบดี ชั้น 5 มหาวิทยาลัย',
    'head' => ['name' => 'ศ.ดร.สมชาย ใจดี', 'position' => 'อธิการบดี'],
    'deputy' => ['name' => 'รศ.ดร.สมหญิง รักงาน', 'position' => 'รองอธิการบดี'],
    'total_users' => 15,
    'total_bookings' => 234,
    'bookings_this_month' => 18,
    'created_at' => '2566-01-15',
    'updated_at' => '2567-12-20',
];

$childDepartments = [
    ['id' => 2, 'name' => 'กองกลาง', 'code' => 'ADMIN', 'users' => 25, 'status' => 'active'],
    ['id' => 3, 'name' => 'กองคลัง', 'code' => 'FIN', 'users' => 18, 'status' => 'active'],
    ['id' => 4, 'name' => 'กองบริหารงานบุคคล', 'code' => 'HR', 'users' => 12, 'status' => 'active'],
    ['id' => 5, 'name' => 'กองนโยบายและแผน', 'code' => 'PLAN', 'users' => 10, 'status' => 'active'],
];

$users = [
    ['id' => 1, 'name' => 'ศ.ดร.สมชาย ใจดี', 'position' => 'อธิการบดี', 'email' => 'somchai.ja@gmail.com', 'phone' => '02-123-4500', 'role' => 'admin'],
    ['id' => 2, 'name' => 'รศ.ดร.สมหญิง รักงาน', 'position' => 'รองอธิการบดี', 'email' => 'somying.ra@gmail.com', 'phone' => '02-123-4501', 'role' => 'manager'],
    ['id' => 3, 'name' => 'นางสาวมณี แก้วใส', 'position' => 'เลขานุการ', 'email' => 'manee.ka@gmail.com', 'phone' => '02-123-4502', 'role' => 'staff'],
    ['id' => 4, 'name' => 'นายวิชัย มานะ', 'position' => 'นักวิเคราะห์', 'email' => 'wichai.ma@gmail.com', 'phone' => '02-123-4503', 'role' => 'staff'],
];

$recentBookings = [
    ['id' => 101, 'room' => 'ห้องประชุม 1', 'title' => 'ประชุมคณะกรรมการบริหาร', 'date' => '2567-12-25', 'time' => '09:00-12:00', 'status' => 'approved'],
    ['id' => 102, 'room' => 'ห้องประชุม VIP', 'title' => 'ประชุมกับหน่วยงานภายนอก', 'date' => '2567-12-26', 'time' => '13:00-16:00', 'status' => 'pending'],
    ['id' => 103, 'room' => 'ห้องประชุม 2', 'title' => 'สัมมนาเชิงปฏิบัติการ', 'date' => '2567-12-27', 'time' => '09:00-17:00', 'status' => 'approved'],
];

$statusColors = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
];

$statusLabels = [
    'pending' => 'รออนุมัติ',
    'approved' => 'อนุมัติแล้ว',
    'rejected' => 'ปฏิเสธ',
    'cancelled' => 'ยกเลิก',
];
?>

<div class="department-view">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center">
            <div class="rounded-3 p-3 me-3" style="background-color: <?= $department['color'] ?>20;">
                <i class="fas fa-building fa-2x" style="color: <?= $department['color'] ?>"></i>
            </div>
            <div>
                <h1 class="h3 mb-1"><?= Html::encode($department['name']) ?></h1>
                <div class="text-muted">
                    <code class="me-2"><?= Html::encode($department['code']) ?></code>
                    <?php if ($department['status'] === 'active'): ?>
                        <span class="badge bg-success">ใช้งานอยู่</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">ไม่ใช้งาน</span>
                    <?php endif; ?>
                    <?php if (!$department['parent']): ?>
                        <span class="badge bg-primary ms-1">หน่วยงานหลัก</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="btn-group">
            <?= Html::a('<i class="fas fa-edit me-1"></i> แก้ไข', ['update', 'id' => $department['id']], ['class' => 'btn btn-warning']) ?>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-1"></i> ลบ
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-print me-2"></i>พิมพ์
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-warning" href="#">
                            <i class="fas fa-eye-slash me-2"></i>ปิดใช้งาน
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="h3 mb-0"><?= number_format($department['total_users']) ?></div>
                    <div class="text-muted small">บุคลากร</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                    <div class="h3 mb-0"><?= number_format($department['total_bookings']) ?></div>
                    <div class="text-muted small">การจองทั้งหมด</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                    <div class="h3 mb-0"><?= number_format($department['bookings_this_month']) ?></div>
                    <div class="text-muted small">จองเดือนนี้</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-folder fa-2x"></i>
                    </div>
                    <div class="h3 mb-0"><?= count($childDepartments) ?></div>
                    <div class="text-muted small">หน่วยงานย่อย</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>ข้อมูลหน่วยงาน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">ชื่อหน่วยงาน</div>
                            <div class="fw-medium"><?= Html::encode($department['name']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">ชื่อภาษาอังกฤษ</div>
                            <div class="fw-medium"><?= Html::encode($department['name_en']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">รหัสหน่วยงาน</div>
                            <div><code class="bg-light px-2 py-1 rounded"><?= Html::encode($department['code']) ?></code></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">ชื่อย่อ</div>
                            <div class="fw-medium"><?= Html::encode($department['short_name']) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">คำอธิบาย</div>
                            <div><?= Html::encode($department['description']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book text-success me-2"></i>ข้อมูลติดต่อ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">โทรศัพท์</div>
                                    <div class="fw-medium"><?= Html::encode($department['phone']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fas fa-fax text-secondary"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">โทรสาร</div>
                                    <div class="fw-medium"><?= Html::encode($department['fax']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fas fa-envelope text-info"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">อีเมล</div>
                                    <a href="mailto:<?= Html::encode($department['email']) ?>" class="fw-medium text-decoration-none">
                                        <?= Html::encode($department['email']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fas fa-globe text-success"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">เว็บไซต์</div>
                                    <a href="<?= Html::encode($department['website']) ?>" target="_blank" class="fw-medium text-decoration-none">
                                        <?= Html::encode($department['website']) ?>
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">ที่อยู่/สถานที่ตั้ง</div>
                                    <div class="fw-medium"><?= Html::encode($department['address']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Departments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-sitemap text-info me-2"></i>หน่วยงานย่อย
                    </h5>
                    <?= Html::a('<i class="fas fa-plus me-1"></i> เพิ่ม', ['create', 'parent_id' => $department['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>หน่วยงาน</th>
                                <th>รหัส</th>
                                <th class="text-center">บุคลากร</th>
                                <th class="text-center">สถานะ</th>
                                <th style="width: 80px">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($childDepartments as $child): ?>
                            <tr>
                                <td>
                                    <a href="<?= Url::to(['view', 'id' => $child['id']]) ?>" class="text-decoration-none fw-medium">
                                        <?= Html::encode($child['name']) ?>
                                    </a>
                                </td>
                                <td><code><?= Html::encode($child['code']) ?></code></td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info"><?= $child['users'] ?> คน</span>
                                </td>
                                <td class="text-center">
                                    <?php if ($child['status'] === 'active'): ?>
                                        <span class="badge bg-success">ใช้งานอยู่</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $child['id']], ['class' => 'btn btn-outline-info']) ?>
                                        <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $child['id']], ['class' => 'btn btn-outline-warning']) ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Users -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-primary me-2"></i>บุคลากร
                    </h5>
                    <a href="<?= Url::to(['user/index', 'department_id' => $department['id']]) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> ดูทั้งหมด
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ชื่อ-สกุล</th>
                                <th>ตำแหน่ง</th>
                                <th>อีเมล</th>
                                <th>โทรศัพท์</th>
                                <th class="text-center">บทบาท</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                        $color = $colors[$user['id'] % count($colors)];
                                        $initials = mb_substr($user['name'], 0, 1);
                                        ?>
                                        <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> d-flex align-items-center justify-content-center me-2" 
                                            style="width: 32px; height: 32px; font-size: 14px;">
                                            <?= $initials ?>
                                        </div>
                                        <a href="<?= Url::to(['user/view', 'id' => $user['id']]) ?>" class="text-decoration-none fw-medium">
                                            <?= Html::encode($user['name']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-muted"><?= Html::encode($user['position']) ?></td>
                                <td><a href="mailto:<?= Html::encode($user['email']) ?>" class="text-decoration-none"><?= Html::encode($user['email']) ?></a></td>
                                <td><?= Html::encode($user['phone']) ?></td>
                                <td class="text-center">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">ผู้ดูแลระบบ</span>
                                    <?php elseif ($user['role'] === 'manager'): ?>
                                        <span class="badge bg-warning text-dark">ผู้จัดการ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">เจ้าหน้าที่</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt text-warning me-2"></i>การจองล่าสุด
                    </h5>
                    <a href="<?= Url::to(['booking/index', 'department_id' => $department['id']]) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> ดูทั้งหมด
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ห้องประชุม</th>
                                <th>หัวข้อ</th>
                                <th>วันที่</th>
                                <th>เวลา</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td>
                                    <i class="fas fa-door-open text-muted me-1"></i>
                                    <?= Html::encode($booking['room']) ?>
                                </td>
                                <td>
                                    <a href="<?= Url::to(['booking/view', 'id' => $booking['id']]) ?>" class="text-decoration-none">
                                        <?= Html::encode($booking['title']) ?>
                                    </a>
                                </td>
                                <td><?= Html::encode($booking['date']) ?></td>
                                <td><?= Html::encode($booking['time']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusColors[$booking['status']] ?>">
                                        <?= $statusLabels[$booking['status']] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Head of Department -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie text-warning me-2"></i>ผู้บริหาร
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Head -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" 
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="fw-medium"><?= Html::encode($department['head']['name']) ?></div>
                            <div class="text-muted small"><?= Html::encode($department['head']['position']) ?></div>
                        </div>
                    </div>
                    <!-- Deputy -->
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-3" 
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-medium"><?= Html::encode($department['deputy']['name']) ?></div>
                            <div class="text-muted small"><?= Html::encode($department['deputy']['position']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hierarchy -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-sitemap text-info me-2"></i>โครงสร้างลำดับชั้น
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">หน่วยงานหลัก</span>
                            <?php if ($department['parent']): ?>
                                <a href="#" class="text-decoration-none"><?= Html::encode($department['parent']) ?></a>
                            <?php else: ?>
                                <span class="badge bg-primary">ระดับสูงสุด</span>
                            <?php endif; ?>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">ระดับ</span>
                            <span class="badge bg-secondary">ระดับ <?= $department['level'] ?></span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">หน่วยงานย่อย</span>
                            <strong><?= count($childDepartments) ?> หน่วยงาน</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-secondary me-2"></i>การตั้งค่า
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">สีประจำหน่วยงาน</span>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 20px; height: 20px; background-color: <?= $department['color'] ?>"></div>
                                <code><?= $department['color'] ?></code>
                            </div>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">จองห้องหน่วยงานอื่น</span>
                            <span class="badge bg-success">อนุญาต</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">ต้องขออนุมัติ</span>
                            <span class="badge bg-warning text-dark">ใช่</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">จำนวนจองสูงสุด/เดือน</span>
                            <strong>ไม่จำกัด</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- System Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info text-muted me-2"></i>ข้อมูลระบบ
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">รหัส ID</span>
                            <code><?= $department['id'] ?></code>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">สร้างเมื่อ</span>
                            <span><?= $department['created_at'] ?></span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">แก้ไขล่าสุด</span>
                            <span><?= $department['updated_at'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบหน่วยงาน <strong><?= Html::encode($department['name']) ?></strong> หรือไม่?</p>
                
                <?php if (count($childDepartments) > 0): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>ไม่สามารถลบได้!</strong><br>
                    หน่วยงานนี้มีหน่วยงานย่อย <?= count($childDepartments) ?> หน่วยงาน กรุณาย้ายหรือลบหน่วยงานย่อยก่อน
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>คำเตือน:</strong> การลบหน่วยงานจะส่งผลต่อบุคลากรและข้อมูลการจองที่เกี่ยวข้อง
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <?php if (count($childDepartments) === 0): ?>
                <form method="post" action="<?= Url::to(['delete', 'id' => $department['id']]) ?>" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> ลบหน่วยงาน
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
