<?php

use yii\helpers\Html;

$this->title = $name;
?>

<div class="site-error">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
                </div>
                
                <h1 class="display-4 fw-bold text-primary"><?= Html::encode($this->title) ?></h1>
                
                <div class="alert alert-danger my-4">
                    <?= nl2br(Html::encode($message)) ?>
                </div>

                <p class="lead text-muted mb-4">
                    ขออภัย เกิดข้อผิดพลาดขึ้นในระบบ
                </p>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>กลับหน้าหลัก
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>ย้อนกลับ
                    </button>
                </div>

                <?php if (YII_DEBUG): ?>
                <div class="mt-5 text-start">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <i class="bi bi-bug me-2"></i>Debug Information
                        </div>
                        <div class="card-body">
                            <p><strong>Exception:</strong> <?= get_class($exception) ?></p>
                            <p><strong>File:</strong> <?= $exception->getFile() ?>:<?= $exception->getLine() ?></p>
                            <pre class="bg-light p-3 rounded overflow-auto" style="max-height: 300px;"><?= Html::encode($exception->getTraceAsString()) ?></pre>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
