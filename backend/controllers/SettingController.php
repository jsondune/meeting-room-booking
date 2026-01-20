<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Setting;
use common\models\Holiday;
use common\models\EmailTemplate;

/**
 * SettingController - Backend system settings
 */
class SettingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'holidays', 'create-holiday', 
                                     'update-holiday', 'delete-holiday', 'import-holidays',
                                     'email-templates', 'update-email-template', 'preview-email',
                                     'test-email', 'backup', 'restore', 'cache-clear',
                                     'maintenance-mode', 'logs', 'system-info'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update' => ['post'],
                    'delete-holiday' => ['post'],
                    'cache-clear' => ['post'],
                    'maintenance-mode' => ['post'],
                ],
            ],
        ];
    }

    /**
     * General settings
     *
     * @return string
     */
    public function actionIndex()
    {
        // Get all settings
        $settings = Setting::find()
            ->indexBy('key')
            ->all();

        // Group settings by category
        $groupedSettings = [
            'general' => [],
            'booking' => [],
            'email' => [],
            'notification' => [],
            'security' => [],
            'appearance' => [],
        ];

        foreach ($settings as $key => $setting) {
            $category = $setting->category ?? 'general';
            if (isset($groupedSettings[$category])) {
                $groupedSettings[$category][$key] = $setting;
            } else {
                $groupedSettings['general'][$key] = $setting;
            }
        }

        return $this->render('index', [
            'settings' => $settings,
            'groupedSettings' => $groupedSettings,
        ]);
    }

    /**
     * Update settings
     *
     * @return Response
     */
    public function actionUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $settings = Yii::$app->request->post('settings', []);

        $updated = 0;
        $errors = [];

        foreach ($settings as $key => $value) {
            $model = Setting::findOne(['key' => $key]);
            if (!$model) {
                $model = new Setting();
                $model->key = $key;
            }

            $model->value = is_array($value) ? json_encode($value) : $value;

            if ($model->save()) {
                $updated++;
            } else {
                $errors[$key] = $model->getFirstErrors();
            }
        }

        // Clear cache
        Yii::$app->cache->flush();

        if (empty($errors)) {
            return [
                'success' => true,
                'message' => "บันทึกการตั้งค่า {$updated} รายการสำเร็จ",
            ];
        }

        return [
            'success' => false,
            'message' => 'บันทึกการตั้งค่าบางรายการไม่สำเร็จ',
            'errors' => $errors,
        ];
    }

    /**
     * Holidays management
     *
     * @return string
     */
    public function actionHolidays()
    {
        $year = Yii::$app->request->get('year', date('Y'));

        $dataProvider = new ActiveDataProvider([
            'query' => Holiday::find()->where(['year' => $year]),
            'pagination' => ['pageSize' => 50],
            'sort' => [
                'defaultOrder' => ['holiday_date' => SORT_ASC],
            ],
        ]);

        // Get years with holidays
        $years = Holiday::find()
            ->select('year')
            ->distinct()
            ->orderBy(['year' => SORT_DESC])
            ->column();

        // Add current year if not in list
        if (!in_array($year, $years)) {
            $years[] = (int)$year;
            rsort($years);
        }

        // Get holiday statistics
        $stats = [
            'total' => Holiday::find()->where(['year' => $year])->count(),
            'upcoming' => Holiday::find()
                ->where(['year' => $year])
                ->andWhere(['>=', 'holiday_date', date('Y-m-d')])
                ->count(),
            'passed' => Holiday::find()
                ->where(['year' => $year])
                ->andWhere(['<', 'holiday_date', date('Y-m-d')])
                ->count(),
        ];

        return $this->render('holidays', [
            'dataProvider' => $dataProvider,
            'year' => $year,
            'years' => $years,
            'stats' => $stats,
        ]);
    }

    /**
     * Create holiday
     *
     * @return Response
     */
    public function actionCreateHoliday()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Holiday();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'เพิ่มวันหยุดสำเร็จ',
                'holiday' => [
                    'id' => $model->id,
                    'holiday_date' => $model->holiday_date,
                    'name_th' => $model->name_th,
                    'name_en' => $model->name_en,
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
        ];
    }

    /**
     * Update holiday
     *
     * @param int $id
     * @return Response
     */
    public function actionUpdateHoliday($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Holiday::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบวันหยุด'];
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'บันทึกวันหยุดสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
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
            return ['success' => false, 'message' => 'ไม่พบวันหยุด'];
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'ลบวันหยุดสำเร็จ'];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบวันหยุด'];
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
        $year = Yii::$app->request->post('year', date('Y'));

        if (!$file) {
            return ['success' => false, 'message' => 'กรุณาเลือกไฟล์'];
        }

        // Read CSV file
        $handle = fopen($file->tempName, 'r');
        if (!$handle) {
            return ['success' => false, 'message' => 'ไม่สามารถอ่านไฟล์ได้'];
        }

        $imported = 0;
        $errors = [];
        $lineNumber = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            // Skip header row
            if ($lineNumber === 1) {
                continue;
            }

            if (count($row) < 2) {
                continue;
            }

            $holiday = new Holiday();
            $holiday->holiday_date = date('Y-m-d', strtotime($row[0]));
            $holiday->name_th = $row[1];
            $holiday->name_en = isset($row[2]) ? $row[2] : $row[1];
            $holiday->year = date('Y', strtotime($row[0]));
            $holiday->is_recurring = isset($row[3]) && $row[3] ? 1 : 0;

            if ($holiday->save()) {
                $imported++;
            } else {
                $errors[] = "บรรทัด {$lineNumber}: " . implode(', ', $holiday->getFirstErrors());
            }
        }

        fclose($handle);

        if ($imported > 0) {
            return [
                'success' => true,
                'message' => "นำเข้าวันหยุด {$imported} รายการสำเร็จ",
                'imported' => $imported,
                'errors' => $errors,
            ];
        }

        return [
            'success' => false,
            'message' => 'ไม่สามารถนำเข้าวันหยุดได้',
            'errors' => $errors,
        ];
    }

    /**
     * Email templates management
     *
     * @return string
     */
    public function actionEmailTemplates()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EmailTemplate::find(),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);

        // Get template types
        $templateTypes = EmailTemplate::getTemplateTypes();

        return $this->render('email-templates', [
            'dataProvider' => $dataProvider,
            'templateTypes' => $templateTypes,
        ]);
    }

    /**
     * Update email template
     *
     * @param int $id
     * @return string|Response
     */
    public function actionUpdateEmailTemplate($id)
    {
        $model = EmailTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('ไม่พบแม่แบบอีเมล');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->save()) {
                    return ['success' => true, 'message' => 'บันทึกแม่แบบอีเมลสำเร็จ'];
                }

                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
                ];
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'บันทึกแม่แบบอีเมลสำเร็จ');
                return $this->redirect(['email-templates']);
            }
        }

        // Get available variables
        $variables = EmailTemplate::getAvailableVariables($model->type);

        return $this->render('update-email-template', [
            'model' => $model,
            'variables' => $variables,
        ]);
    }

    /**
     * Preview email template
     *
     * @param int $id
     * @return Response
     */
    public function actionPreviewEmail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EmailTemplate::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบแม่แบบอีเมล'];
        }

        // Sample data for preview
        $sampleData = [
            'user_name' => 'ทดสอบ ระบบ',
            'booking_code' => 'BK-2024-0001',
            'room_name' => 'ห้องประชุม A',
            'booking_date' => date('d/m/Y'),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'total_cost' => '500.00',
            'app_name' => Setting::get('app_name', 'Meeting Room Booking'),
            'app_url' => Yii::$app->urlManager->createAbsoluteUrl(['/'], true),
        ];

        $subject = $model->parseTemplate($model->subject, $sampleData);
        $body = $model->parseTemplate($model->body, $sampleData);

        return [
            'success' => true,
            'subject' => $subject,
            'body' => $body,
        ];
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
        $templateId = Yii::$app->request->post('template_id');

        if (!$email) {
            return ['success' => false, 'message' => 'กรุณาระบุอีเมล'];
        }

        $template = $templateId ? EmailTemplate::findOne($templateId) : null;

        try {
            $mailer = Yii::$app->mailer->compose()
                ->setTo($email)
                ->setSubject($template ? $template->subject : 'ทดสอบอีเมล - Meeting Room Booking')
                ->setHtmlBody($template ? $template->body : '<p>นี่คืออีเมลทดสอบจากระบบจองห้องประชุม</p>');

            if ($mailer->send()) {
                return ['success' => true, 'message' => 'ส่งอีเมลทดสอบสำเร็จ'];
            }

            return ['success' => false, 'message' => 'ไม่สามารถส่งอีเมลได้'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Clear cache
     *
     * @return Response
     */
    public function actionCacheClear()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Clear application cache
            Yii::$app->cache->flush();

            // Clear schema cache
            Yii::$app->db->schema->refresh();

            return ['success' => true, 'message' => 'ล้างแคชสำเร็จ'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Maintenance mode toggle
     *
     * @return Response
     */
    public function actionMaintenanceMode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $enable = Yii::$app->request->post('enable', false);
        $message = Yii::$app->request->post('message', 'ระบบอยู่ระหว่างการปรับปรุง กรุณากลับมาใหม่ภายหลัง');

        $setting = Setting::findOne(['key' => 'maintenance_mode']);
        if (!$setting) {
            $setting = new Setting();
            $setting->key = 'maintenance_mode';
            $setting->category = 'general';
        }
        $setting->value = $enable ? '1' : '0';
        $setting->save();

        $messageSetting = Setting::findOne(['key' => 'maintenance_message']);
        if (!$messageSetting) {
            $messageSetting = new Setting();
            $messageSetting->key = 'maintenance_message';
            $messageSetting->category = 'general';
        }
        $messageSetting->value = $message;
        $messageSetting->save();

        Yii::$app->cache->flush();

        return [
            'success' => true,
            'message' => $enable ? 'เปิดโหมดบำรุงรักษาแล้ว' : 'ปิดโหมดบำรุงรักษาแล้ว',
            'maintenance_mode' => $enable,
        ];
    }

    /**
     * System logs
     *
     * @return string
     */
    public function actionLogs()
    {
        $logFile = Yii::$app->request->get('file', 'app');
        $lines = Yii::$app->request->get('lines', 100);

        $logPath = Yii::getAlias('@runtime/logs');
        $availableLogs = [];

        // Scan log directory
        if (is_dir($logPath)) {
            foreach (scandir($logPath) as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $availableLogs[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }

        $logContent = '';
        $targetFile = $logPath . '/' . $logFile . '.log';

        if (file_exists($targetFile)) {
            // Read last N lines
            $logContent = $this->tailFile($targetFile, $lines);
        }

        return $this->render('logs', [
            'availableLogs' => $availableLogs,
            'currentLog' => $logFile,
            'logContent' => $logContent,
            'lines' => $lines,
        ]);
    }

    /**
     * System information
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
            'extensions' => get_loaded_extensions(),
        ];

        // Disk usage
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $info['disk'] = [
            'total' => $this->formatBytes($totalSpace),
            'free' => $this->formatBytes($freeSpace),
            'used' => $this->formatBytes($totalSpace - $freeSpace),
            'usage_percent' => round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1),
        ];

        return $this->render('system-info', [
            'info' => $info,
        ]);
    }

    /**
     * Create database backup
     *
     * @return Response
     */
    public function actionBackup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $db = Yii::$app->db;
            $dsn = $db->dsn;

            // Parse DSN
            preg_match('/dbname=([^;]+)/', $dsn, $matches);
            $database = $matches[1] ?? 'database';

            $filename = 'backup-' . $database . '-' . date('Y-m-d-His') . '.sql';
            $backupPath = Yii::getAlias('@runtime/backups');

            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $filename;

            // Get all tables
            $tables = $db->schema->tableNames;

            $output = "-- Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $table) {
                // Get CREATE TABLE statement
                $createTable = $db->createCommand("SHOW CREATE TABLE `{$table}`")->queryOne();
                $output .= "\n-- Table: {$table}\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createTable['Create Table'] . ";\n\n";

                // Get data
                $rows = $db->createCommand("SELECT * FROM `{$table}`")->queryAll();
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $columns = array_keys($row);
                        $values = array_map(function($v) use ($db) {
                            if ($v === null) {
                                return 'NULL';
                            }
                            return $db->quoteValue($v);
                        }, array_values($row));

                        $output .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
            }

            file_put_contents($filePath, $output);

            return [
                'success' => true,
                'message' => 'สร้างไฟล์สำรองข้อมูลสำเร็จ',
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($filePath)),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Get database info
     */
    protected function getDatabaseInfo()
    {
        $db = Yii::$app->db;
        $version = $db->createCommand('SELECT VERSION()')->queryScalar();

        return [
            'driver' => $db->driverName,
            'version' => $version,
            'database' => $db->createCommand('SELECT DATABASE()')->queryScalar(),
        ];
    }

    /**
     * Read last N lines from file
     */
    protected function tailFile($filepath, $lines = 100)
    {
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return '';
        }

        $buffer = '';
        $lineCount = 0;

        // Go to end of file
        fseek($handle, 0, SEEK_END);
        $position = ftell($handle);

        while ($position > 0 && $lineCount < $lines) {
            $position--;
            fseek($handle, $position);
            $char = fgetc($handle);

            if ($char === "\n") {
                $lineCount++;
            }

            $buffer = $char . $buffer;
        }

        fclose($handle);

        return $buffer;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
