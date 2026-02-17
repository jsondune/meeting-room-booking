<?php

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
// Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . '/frontend/web/uploads');

// ============================================================
// Shared Uploads Directory Configuration
// ============================================================
// Base path for shared uploads (relative to project root)
$uploadsPath = dirname(dirname(__DIR__)) . '/uploads';

// Create directories if they don't exist
$uploadDirs = ['', '/avatars', '/buildings', '/rooms', '/equipment', '/documents', '/temp'];
foreach ($uploadDirs as $dir) {
    $fullPath = $uploadsPath . $dir;
    if (!is_dir($fullPath)) {
        @mkdir($fullPath, 0755, true);
    }
}

// Set Yii aliases for uploads
Yii::setAlias('@uploads', $uploadsPath);
Yii::setAlias('@uploadsUrl', '/uploads');

// Specific upload type aliases
Yii::setAlias('@avatars', $uploadsPath . '/avatars');
Yii::setAlias('@avatarsUrl', '/uploads/avatars');

Yii::setAlias('@buildingImages', $uploadsPath . '/buildings');
Yii::setAlias('@buildingImagesUrl', '/uploads/buildings');

Yii::setAlias('@roomImages', $uploadsPath . '/rooms');
Yii::setAlias('@roomImagesUrl', '/uploads/rooms');

Yii::setAlias('@equipmentImages', $uploadsPath . '/equipment');
Yii::setAlias('@equipmentImagesUrl', '/uploads/equipment');

Yii::setAlias('@documents', $uploadsPath . '/documents');
Yii::setAlias('@documentsUrl', '/uploads/documents');

Yii::setAlias('@tempUploads', $uploadsPath . '/temp');
