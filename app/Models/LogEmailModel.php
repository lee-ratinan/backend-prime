<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Log Email Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;
use Mailgun\Mailgun;
use Psr\Http\Client\ClientExceptionInterface;
use ReflectionException;
use Symfony\Component\Mime\Email;

class LogEmailModel extends Model
{
    protected $table = 'log_email';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'email_subject',
        'email_to',
        'email_status',
        'created_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Apply template to the email
     * @param string $subject
     * @param string $text
     * @param string $app_name
     * @return string
     */
    private function applyTemplate(string $subject, string $text, string $app_name): string
    {
        $text     = '<p>' . str_replace("\n", '</p><p>', $text) . '</p>';
        return <<<EOD
<html lang="en"><head><title>{$subject}</title></head>
<body style="padding:15px;font-family:sans-serif;font-size:14px;">
<div style="max-width:600px;margin:0 auto;">
<h1 style="text-align:right;font-size:20px;font-weight:600;">{$app_name}</h1>
<div style="border-top:1px solid #000;border-bottom:1px solid #000;padding-top:10px;padding-bottom:20px;">{$text}</div>
<p style="font-size:11px;color:#888;">This email was sent by {$app_name}</p>
</div>
</body>
</html>
EOD;
    }

    /**
     * Send email using Mailgun
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param string $cc
     * @param string $bcc
     * @return bool
     * @throws ClientExceptionInterface
     * @throws ReflectionException
     */
    public function sendEmail(string $to, string $subject, string $text, string $cc = '', string $bcc = ''): bool
    {
        log_message('debug', 'Emailing: ' . $to . ', Subject: ' . $subject . ', Content: ' . $text);
        if ('development' == getenv('CI_ENVIRONMENT')) {
            $result = ['message' => 'Email not really sent in development environment'];
        } else {
            $session            = session();
            $app_name           = $session->app_name;
            $api_key            = getenv('MAILGUN_SERVICE_API_KEY');
            $domain             = getenv('MAILGUN_SERVICE_DOMAIN');
            $from               = getenv('MAIL_SERVICE_FROM_EMAIL');
            $reply_to           = getenv('MAIL_SERVICE_REPLY_EMAIL');
            $subject            = "[$app_name] $subject";
            $html               = $this->applyTemplate($subject, $text, $app_name);
            $mg                 = Mailgun::create($api_key);
            // RECIPIENTS
            $recipients[]       = $to;
            if (!empty($cc)) {
                $recipients[] = $cc;
            }
            if (!empty($bcc)) {
                $recipients[] = $bcc;
            }
            $boundary     = md5(uniqid());
            $message_body = <<<EOD
From: $from
To: $to
Subject: $subject
MIME-Version: 1.0
Content-Type: multipart/alternative; boundary="{$boundary}"

--{$boundary}
Content-Type: text/plain; charset=UTF-8

{$text}

--{$boundary}
Content-Type: text/html; charset=UTF-8

{$html}

--{$boundary}--
EOD;
            // PARAMS
            $params['from']     = $from;
            $params['to']       = $to;
            $params['reply-to'] = $reply_to;
            $params['subject']  = $subject;
            $params['html']     = $html;
            // Send the message
            $result = $mg->messages()->sendMime($domain, $recipients, $message_body, $params);
        }
        log_message('debug', 'Emailed: ' . json_encode($result));
        // LOG RESULT
        return $this->insert([
            'email_subject' => $subject,
            'email_to'      => $to,
            'email_status'  => json_encode($result),
            'created_at'    => date(DATETIME_FORMAT_DB)
        ]);
    }

    /**
     * Apply filter
     * @param string $email_to
     * @param string $email_subject
     * @param string $date_start
     * @param string $date_end
     * @return void
     */
    private function applyFilter(string $email_to, string $email_subject, string $date_start, string $date_end): void
    {
        if (!empty($email_to)) {
            $this->where('email_to', $email_to);
        }
        if (!empty($email_subject)) {
            $this->like('email_subject', $email_subject);
        }
        if (!empty($date_start)) {
            $this->where('created_at >=', $date_start);
        }
        if (!empty($date_end)) {
            $this->where('created_at <=', $date_end);
        }
    }

    /**
     * Get data for DataTables
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $email_to
     * @param string $email_subject
     * @param string $date_start
     * @param string $date_end
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $email_to, string $email_subject, string $date_start, string $date_end): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($email_to) || !empty($email_subject) || !empty($date_start) || !empty($date_end)) {
            $this->applyFilter($email_to, $email_subject, $date_start, $date_end);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($email_to, $email_subject, $date_start, $date_end);
        }
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $email_status = json_decode($row['email_status'], true);
            $detail_str   = [];
            foreach ($email_status as $key => $value) {
                $detail_str[] = '<li>' . $key . ': ' . $value . '</li>';
            }
            $result[]     = [
                (empty($row['created_at']) ? '' : '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['created_at']) . 'Z</span>'),
                $row['email_to'],
                $row['email_subject'],
                '<ul>' . implode('', $detail_str) . '</ul>'
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * Delete old log activities older than the given date
     * @param string $date
     * @return array
     */
    public function deleteOldLog(string $date): array
    {
        $cnt_before  = $this->countAllResults();
        $date_string = date('Y-m-d 00:00:00', strtotime($date));
        $result      = $this->where('created_at <=', $date_string)->delete();
        $cnt_after   = $this->countAllResults();
        return [
            'deleted' => $result,
            'before'  => $cnt_before,
            'after'   => $cnt_after,
            'delta'   => $cnt_before - $cnt_after
        ];
    }
}