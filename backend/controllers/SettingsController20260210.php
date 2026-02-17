<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\SystemSetting;
use common\models\AuditLog;
use common\models\Holiday;
use common\models\EmailTemplate;

/**
 * SettingsController - System settings management
 */
class SettingsController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'booking', 'notification', 'email-templates', 
                             'holidays', 'security', 'backup', 'cache', 'audit-log',
                             'create-holiday', 'update-holiday', 'delete-holiday',
                             'create-email-template', 'update-email-template', 'delete-email-template',
                             'clear-cache', 'test-email', 'export-settings', 'import-settings'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbActions()
    {
        return [
            'delete-holiday' => ['post'],
            'delete-email-template' => ['post'],
            'clear-cache' => ['post'],
            'test-email' => ['post'],
        ];
    }

    /**
     * General settings page
     *
     * @return string|Response
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $settings = Yii::$app->request->post('Settings', []);
            
            foreach ($settings as $key => $value) {
                $type = $this->getSettingType($key);
                SystemSetting::setValue($key, $value, $type);
            }
            
            // Handle logo upload
            $logoFile = UploadedFile::getInstanceByName('Settings[site_logo]');
            if ($logoFile) {
                $logoPath = $this->uploadLogo($logoFile);
                if ($logoPath) {
                    SystemSetting::setValue('site_logo', $logoPath, 'string');
                }
            }
            
            // Log action
            AuditLog::log('update', 'SystemSetting', null, [], $settings, 'Updated system settings');
            
            Yii::$app->session->setFlash('success', 'บันทึกการตั้งค่าสำเร็จ');
            return $this->refresh();
        }

        // Get all settings by category
        $generalSettings = $this->getSettingsByCategory('general');
        $bookingSettings = $this->getSettingsByCategory('booking');
        $emailSettings = $this->getSettingsByCategory('email');
        $securitySettings = $this->getSettingsByCategory('security');

        return $this->render('index', [
            'generalSettings' => $generalSettings,
            'bookingSettings' => $bookingSettings,
            'emailSettings' => $emailSettings,
            'securitySettings' => $securitySettings,
        ]);
    }

    /**
     * Holiday management page
     *
     * @return string|Response
     */
    public function actionHolidays()
    {
        $year = Yii::$app->request->get('year', date('Y'));

        $dataProvider = new ActiveDataProvider([
            'query' => Holiday::find()->where(['year' => $year])->orderBy(['holiday_date' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        // Handle holiday creation
        $model = new Holiday();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::log('create', Holiday::class, $model->id, [], $model->attributes, 'Created holiday: ' . $model->name_th);
            Yii::$app->session->setFlash('success', 'เพิ่มวันหยุดสำเร็จ');
            return $this->refresh();
        }

        // Get available years
        $years = Holiday::find()
            ->select(['year'])
            ->distinct()
            ->orderBy(['year' => SORT_DESC])
            ->column();
        
        if (!in_array($year, $years)) {
            $years[] = $year;
            rsort($years);
        }

        return $this->render('holidays', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'year' => $year,
            'years' => $years,
        ]);
    }

    /**
     * Create or update holiday
     *
     * @param int|null $id
     * @return Response
     */
    public function actionSaveHoliday($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $model = Holiday::findOne($id);
            if (!$model) {
                return ['success' => false, 'message' => 'ไม่พบข้อมูลวันหยุด'];
            }
            $oldValues = $model->attributes;
        } else {
            $model = new Holiday();
            $oldValues = [];
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::log(
                $id ? 'update' : 'create',
                Holiday::class,
                $model->id,
                $oldValues,
                $model->attributes,
                ($id ? 'Updated' : 'Created') . ' holiday: ' . $model->name_th
            );

            return [
                'success' => true,
                'message' => $id ? 'แก้ไขวันหยุดสำเร็จ' : 'เพิ่มวันหยุดสำเร็จ',
                'holiday' => [
                    'id' => $model->id,
                    'name_th' => $model->name_th,
                    'name_en' => $model->name_en,
                    'holiday_date' => $model->holiday_date,
                    'holiday_type' => $model->holiday_type,
                    'year' => $model->year,
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
            'errors' => $model->errors,
        ];
    }

    /**
     * Delete holiday
     *
     * @param int $id
     * @return Response
     */
    public function actionDeleteHoliday($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Holiday::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบข้อมูลวันหยุด'];
        }

        $oldValues = $model->attributes;
        $name = $model->name_th;

        if ($model->delete()) {
            AuditLog::log('delete', Holiday::class, $id, $oldValues, [], 'Deleted holiday: ' . $name);
            return ['success' => true, 'message' => 'ลบวันหยุดสำเร็จ'];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ'];
    }

    /**
     * Import holidays from file
     *
     * @return Response
     */
    public function actionImportHolidays()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            return ['success' => false, 'message' => 'โปรดเลือกไฟล์'];
        }

        $year = Yii::$app->request->post('year', date('Y'));

        try {
            $content = file_get_contents($file->tempName);
            $data = json_decode($content, true);

            if (!$data || !is_array($data)) {
                // Try CSV format
                $lines = array_filter(explode("\n", $content));
                $data = [];
                foreach ($lines as $index => $line) {
                    if ($index === 0) continue; // Skip header
                    $parts = str_getcsv($line);
                    if (count($parts) >= 2) {
                        $data[] = [
                            'holiday_date' => trim($parts[0]),
                            'name_th' => trim($parts[1]),
                            'name_en' => isset($parts[2]) ? trim($parts[2]) : null,
                            'holiday_type' => isset($parts[3]) ? trim($parts[3]) : 'public',
                        ];
                    }
                }
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $item) {
                $model = new Holiday();
                $model->holiday_date = $item['holiday_date'] ?? null;
                $model->name_th = $item['name_th'] ?? null;
                $model->name_en = $item['name_en'] ?? null;
                $model->holiday_type = $item['holiday_type'] ?? 'public';
                $model->year = date('Y', strtotime($model->holiday_date));

                if ($model->validate() && $model->save()) {
                    $imported++;
                } else {
                    $errors[] = ($item['name_th'] ?? 'Unknown') . ': ' . implode(', ', $model->getFirstErrors());
                }
            }

            AuditLog::log('import', Holiday::class, null, [], ['count' => $imported], 'Imported ' . $imported . ' holidays');

            return [
                'success' => true,
                'message' => "นำเข้าวันหยุดสำเร็จ {$imported} รายการ",
                'imported' => $imported,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Email templates management page
     *
     * @return string
     */
    public function actionEmailTemplates()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EmailTemplate::find()->orderBy(['template_key' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('email-templates', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Edit email template
     *
     * @param int $id
     * @return string|Response
     */
    public function actionEditEmailTemplate($id)
    {
        $model = EmailTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('ไม่พบเทมเพลตอีเมล');
        }

        $oldValues = $model->attributes;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::log('update', EmailTemplate::class, $model->id, $oldValues, $model->attributes, 'Updated email template: ' . $model->template_key);
            Yii::$app->session->setFlash('success', 'บันทึกเทมเพลตสำเร็จ');
            return $this->redirect(['email-templates']);
        }

        return $this->render('edit-email-template', [
            'model' => $model,
        ]);
    }

    /**
     * Create email template
     *
     * @return string|Response
     */
    public function actionCreateEmailTemplate()
    {
        $model = new EmailTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::log('create', EmailTemplate::class, $model->id, [], $model->attributes, 'Created email template: ' . $model->template_key);
            Yii::$app->session->setFlash('success', 'สร้างเทมเพลตสำเร็จ');
            return $this->redirect(['email-templates']);
        }

        return $this->render('create-email-template', [
            'model' => $model,
        ]);
    }

    /**
     * Delete email template
     *
     * @param int $id
     * @return Response
     */
    public function actionDeleteEmailTemplate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EmailTemplate::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบเทมเพลต'];
        }

        if ($model->is_system) {
            return ['success' => false, 'message' => 'ไม่สามารถลบเทมเพลตระบบได้'];
        }

        $oldValues = $model->attributes;
        $key = $model->template_key;

        if ($model->delete()) {
            AuditLog::log('delete', EmailTemplate::class, $id, $oldValues, [], 'Deleted email template: ' . $key);
            return ['success' => true, 'message' => 'ลบเทมเพลตสำเร็จ'];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];
    }

    /**
     * Preview email template
     *
     * @param int $id
     * @return string
     */
    public function actionPreviewEmailTemplate($id)
    {
        $model = EmailTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('ไม่พบเทมเพลต');
        }

        // Sample data for preview
        $sampleData = [
            'user_name' => 'ทดสอบ นามสกุล',
            'booking_code' => 'BK-2024-001234',
            'room_name' => 'ห้องประชุม A101',
            'booking_date' => date('d/m/Y'),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'site_name' => SystemSetting::getValue('site_name', 'Meeting Room Booking'),
            'site_url' => Yii::$app->request->hostInfo,
        ];

        $body = $model->renderBody($sampleData);
        $subject = $model->renderSubject($sampleData);

        $this->layout = false;
        return $this->render('preview-email-template', [
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    /**
     * Test email
     *
     * @return Response
     */
    public function actionTestEmail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $email = Yii::$app->request->post('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'โปรดระบุอีเมลที่ถูกต้อง'];
        }

        try {
            $result = Yii::$app->mailer->compose()
                ->setTo($email)
                ->setSubject('ทดสอบการส่งอีเมล - ' . SystemSetting::getValue('site_name', 'Meeting Room Booking'))
                ->setHtmlBody('<h1>ทดสอบการส่งอีเมล</h1><p>อีเมลนี้ส่งจากระบบจองห้องประชุมเพื่อทดสอบการตั้งค่าอีเมล</p><p>เวลา: ' . date('Y-m-d H:i:s') . '</p>')
                ->send();

            if ($result) {
                return ['success' => true, 'message' => 'ส่งอีเมลทดสอบสำเร็จ'];
            }
            return ['success' => false, 'message' => 'ไม่สามารถส่งอีเมลได้'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Clear system cache
     *
     * @return Response
     */
    public function actionClearCache()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Clear Yii cache
            Yii::$app->cache->flush();

            // Clear asset cache
            $assetPath = Yii::getAlias('@webroot/assets');
            if (is_dir($assetPath)) {
                $files = glob($assetPath . '/*');
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        $this->deleteDirectory($file);
                    }
                }
            }

            AuditLog::log('clear_cache', null, null, [], [], 'Cleared system cache');

            return ['success' => true, 'message' => 'ล้าง Cache สำเร็จ'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Backup settings
     *
     * @return Response
     */
    public function actionBackupSettings()
    {
        $settings = SystemSetting::find()->all();
        $holidays = Holiday::find()->all();
        $emailTemplates = EmailTemplate::find()->all();

        $data = [
            'exported_at' => date('Y-m-d H:i:s'),
            'exported_by' => Yii::$app->user->identity->username,
            'settings' => array_map(function($s) {
                return $s->attributes;
            }, $settings),
            'holidays' => array_map(function($h) {
                return $h->attributes;
            }, $holidays),
            'email_templates' => array_map(function($e) {
                return $e->attributes;
            }, $emailTemplates),
        ];

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'settings-backup-' . date('Y-m-d-His') . '.json';

        AuditLog::log('backup', 'Settings', null, [], [], 'Exported settings backup');

        return Yii::$app->response->sendContentAsFile(
            $content,
            $filename,
            ['mimeType' => 'application/json']
        );
    }

    /**
     * Restore settings from backup
     *
     * @return Response
     */
    public function actionRestoreSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            return ['success' => false, 'message' => 'โปรดเลือกไฟล์'];
        }

        try {
            $content = file_get_contents($file->tempName);
            $data = json_decode($content, true);

            if (!$data || !isset($data['settings'])) {
                return ['success' => false, 'message' => 'รูปแบบไฟล์ไม่ถูกต้อง'];
            }

            $transaction = Yii::$app->db->beginTransaction();

            // Restore settings
            foreach ($data['settings'] as $settingData) {
                $setting = SystemSetting::find()->where(['setting_key' => $settingData['setting_key']])->one();
                if (!$setting) {
                    $setting = new SystemSetting();
                }
                $setting->attributes = $settingData;
                $setting->save(false);
            }

            // Optionally restore holidays
            if (isset($data['holidays']) && Yii::$app->request->post('restore_holidays')) {
                foreach ($data['holidays'] as $holidayData) {
                    $holiday = Holiday::find()->where(['holiday_date' => $holidayData['holiday_date']])->one();
                    if (!$holiday) {
                        $holiday = new Holiday();
                    }
                    unset($holidayData['id']);
                    $holiday->attributes = $holidayData;
                    $holiday->save(false);
                }
            }

            // Optionally restore email templates
            if (isset($data['email_templates']) && Yii::$app->request->post('restore_templates')) {
                foreach ($data['email_templates'] as $templateData) {
                    $template = EmailTemplate::find()->where(['template_key' => $templateData['template_key']])->one();
                    if (!$template) {
                        $template = new EmailTemplate();
                    }
                    unset($templateData['id']);
                    $template->attributes = $templateData;
                    $template->save(false);
                }
            }

            $transaction->commit();

            AuditLog::log('restore', 'Settings', null, [], [], 'Restored settings from backup');

            return ['success' => true, 'message' => 'คืนค่าการตั้งค่าสำเร็จ'];

        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollBack();
            }
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * System information page
     *
     * @return string
     */
    public function actionSystemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'yii_version' => Yii::getVersion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => $this->getDatabaseInfo(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_free_space' => $this->formatBytes(disk_free_space('/')),
            'disk_total_space' => $this->formatBytes(disk_total_space('/')),
        ];

        // Extension check
        $extensions = [
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'gd' => extension_loaded('gd'),
            'curl' => extension_loaded('curl'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'zip' => extension_loaded('zip'),
            'intl' => extension_loaded('intl'),
        ];

        return $this->render('system-info', [
            'info' => $info,
            'extensions' => $extensions,
        ]);
    }

    /**
     * Get settings by category
     *
     * @param string $category
     * @return array
     */
    protected function getSettingsByCategory($category)
    {
        $settings = SystemSetting::find()
            ->where(['category' => $category])
            ->indexBy('setting_key')
            ->all();

        $result = [];
        foreach ($settings as $key => $setting) {
            $result[$key] = SystemSetting::getValue($key);
        }

        return $result;
    }

    /**
     * Get setting type by key
     *
     * @param string $key
     * @return string
     */
    protected function getSettingType($key)
    {
        $booleanKeys = [
            'maintenance_mode',
            'require_approval',
            'allow_recurring',
            'send_notifications',
            'enable_2fa',
            'allow_registration',
        ];

        $integerKeys = [
            'max_booking_days_advance',
            'min_booking_hours',
            'max_booking_hours',
            'session_timeout',
            'max_login_attempts',
        ];

        if (in_array($key, $booleanKeys)) {
            return 'boolean';
        }
        if (in_array($key, $integerKeys)) {
            return 'integer';
        }

        return 'string';
    }

    /**
     * Upload logo
     *
     * @param UploadedFile $file
     * @return string|false
     */
    protected function uploadLogo($file)
    {
        $uploadPath = Yii::getAlias('@webroot/uploads/logo');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'logo_' . time() . '.' . $file->extension;
        $filePath = $uploadPath . '/' . $filename;

        if ($file->saveAs($filePath)) {
            return 'uploads/logo/' . $filename;
        }

        return false;
    }

    /**
     * Delete directory recursively
     *
     * @param string $dir
     */
    protected function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Get database information
     *
     * @return array
     */
    protected function getDatabaseInfo()
    {
        try {
            $db = Yii::$app->db;
            $version = $db->createCommand('SELECT VERSION()')->queryScalar();
            return [
                'driver' => $db->driverName,
                'version' => $version,
                'database' => $db->createCommand('SELECT DATABASE()')->queryScalar(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Format bytes to human readable
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
