<?php
/** @var common\models\AuditLog $model */

use yii\helpers\Html;

// Format date as CE (ค.ศ.)
$timestamp = strtotime($model->created_at);
$formattedDate = date('d/m/Y H:i:s', $timestamp);

// Get short model class name
$shortModelClass = $model->model_class ? basename(str_replace('\\', '/', $model->model_class)) : '-';

// Badge class for action
$actionBadges = [
    'create' => 'bg-success',
    'update' => 'bg-info',
    'delete' => 'bg-danger',
    'login' => 'bg-primary',
    'logout' => 'bg-secondary',
    'approve' => 'bg-success',
    'reject' => 'bg-warning text-dark',
    'cancel' => 'bg-danger',
];
$actionClass = $actionBadges[$model->action] ?? 'bg-secondary';
?>

<div class="audit-log-detail">
    <table class="table table-bordered mb-4">
        <tr>
            <th style="width: 150px">ID</th>
            <td><?= $model->id ?></td>
        </tr>
        <tr>
            <th>วันที่/เวลา</th>
            <td><?= $formattedDate ?></td>
        </tr>
        <tr>
            <th>ผู้ใช้</th>
            <td>
                <?php if ($model->user): ?>
                    <?= Html::encode($model->username ?: $model->user->username) ?>
                    <small class="text-muted">(ID: <?= $model->user_id ?>)</small>
                <?php else: ?>
                    <?= Html::encode($model->username ?: '-') ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>การกระทำ</th>
            <td><span class="badge <?= $actionClass ?>"><?= Html::encode($model->action) ?></span></td>
        </tr>
        <tr>
            <th>Model</th>
            <td><code><?= Html::encode($shortModelClass) ?></code></td>
        </tr>
        <tr>
            <th>Record ID</th>
            <td><?= Html::encode($model->model_id ?: '-') ?></td>
        </tr>
        <tr>
            <th>IP Address</th>
            <td><code><?= Html::encode($model->ip_address ?: '-') ?></code></td>
        </tr>
        <tr>
            <th>URL</th>
            <td>
                <?php if ($model->url): ?>
                    <small class="text-break"><?= Html::encode($model->url) ?></small>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>รายละเอียด</th>
            <td><?= Html::encode($model->description ?: '-') ?></td>
        </tr>
    </table>

    <?php if ($model->old_values || $model->new_values): ?>
    <div class="row">
        <?php if ($model->old_values): ?>
        <div class="col-md-6">
            <h6><i class="bi bi-arrow-left-circle text-danger me-1"></i>ค่าเดิม</h6>
            <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow: auto; font-size: 12px;"><?php
                $oldData = json_decode($model->old_values, true);
                echo $oldData ? json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $model->old_values;
            ?></pre>
        </div>
        <?php endif; ?>
        
        <?php if ($model->new_values): ?>
        <div class="col-md-6">
            <h6><i class="bi bi-arrow-right-circle text-success me-1"></i>ค่าใหม่</h6>
            <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow: auto; font-size: 12px;"><?php
                $newData = json_decode($model->new_values, true);
                echo $newData ? json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $model->new_values;
            ?></pre>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($model->user_agent): ?>
    <div class="mt-3">
        <h6><i class="bi bi-globe me-1"></i>User Agent</h6>
        <small class="text-muted text-break"><?= Html::encode($model->user_agent) ?></small>
    </div>
    <?php endif; ?>
</div>
