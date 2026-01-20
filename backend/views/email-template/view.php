<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'จัดการเทมเพลตอีเมล', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="email-template-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายละเอียดเทมเพลตอีเมล</p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-pencil me-1"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> ลบ', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'คุณแน่ใจหรือไม่ที่จะลบเทมเพลตนี้?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Template Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลเทมเพลต</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">รหัสเทมเพลต</div>
                        <div class="col-md-9"><code><?= Html::encode($model->template_key) ?></code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">ชื่อเทมเพลต</div>
                        <div class="col-md-9"><?= Html::encode($model->name) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">หัวเรื่อง</div>
                        <div class="col-md-9"><?= Html::encode($model->subject) ?></div>
                    </div>
                </div>
            </div>

            <!-- HTML Body -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-code me-2"></i>เนื้อหา HTML</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#htmlPreview">
                        <i class="bi bi-eye me-1"></i>ดูตัวอย่าง
                    </button>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow: auto;"><code><?= Html::encode($model->body_html) ?></code></pre>
                </div>
                <div class="collapse" id="htmlPreview">
                    <div class="card-body border-top bg-light">
                        <h6 class="text-muted mb-3">ตัวอย่างการแสดงผล:</h6>
                        <div class="bg-white p-3 border rounded">
                            <?= $model->body_html ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Body -->
            <?php if ($model->body_text): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>เนื้อหา Text</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded" style="max-height: 200px; overflow: auto;"><?= Html::encode($model->body_text) ?></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>สถานะ</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">สถานะ</span>
                        <?php if ($model->is_active): ?>
                            <span class="badge bg-success">ใช้งาน</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ไม่ใช้งาน</span>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">แก้ไขล่าสุด</span>
                        <span><?= $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at, 'short') : '-' ?></span>
                    </div>
                </div>
            </div>

            <!-- Available Variables -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-braces me-2"></i>ตัวแปรที่ใช้ได้</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">ใช้ตัวแปรเหล่านี้ในเนื้อหาเทมเพลต</p>
                    <div class="d-flex flex-wrap gap-2">
                        <code class="bg-light px-2 py-1 rounded">{app_name}</code>
                        <code class="bg-light px-2 py-1 rounded">{user_name}</code>
                        <code class="bg-light px-2 py-1 rounded">{user_email}</code>
                        <code class="bg-light px-2 py-1 rounded">{booking_code}</code>
                        <code class="bg-light px-2 py-1 rounded">{room_name}</code>
                        <code class="bg-light px-2 py-1 rounded">{booking_date}</code>
                        <code class="bg-light px-2 py-1 rounded">{start_time}</code>
                        <code class="bg-light px-2 py-1 rounded">{end_time}</code>
                        <code class="bg-light px-2 py-1 rounded">{reset_link}</code>
                        <code class="bg-light px-2 py-1 rounded">{verify_link}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
