<?php

use yii\helpers\Html;

$this->title = 'นำเข้าวันหยุด';
$this->params['breadcrumbs'][] = ['label' => 'จัดการวันหยุด', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$currentYear = date('Y');
$years = [];
for ($y = $currentYear; $y <= $currentYear + 2; $y++) {
    $years[$y] = "พ.ศ. " . ($y + 543) . " ({$y})";
}
?>

<div class="holiday-import">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-download me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                ระบบจะนำเข้าวันหยุดราชการของประเทศไทย รวมถึง:
                <ul class="mb-0 mt-2">
                    <li>วันหยุดประจำ (วันขึ้นปีใหม่, วันจักรี, วันสงกรานต์, ฯลฯ)</li>
                    <li>วันหยุดทางศาสนา (วันมาฆบูชา, วันวิสาขบูชา, ฯลฯ)</li>
                    <li>วันสำคัญของชาติ</li>
                </ul>
            </div>

            <form method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">เลือกปี</label>
                            <select name="year" class="form-select">
                                <?php foreach ($years as $year => $label): ?>
                                    <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <?= Html::submitButton('<i class="bi bi-download me-1"></i>นำเข้าวันหยุด', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="bi bi-x-lg me-1"></i>ยกเลิก', ['index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </form>
        </div>
    </div>
</div>
