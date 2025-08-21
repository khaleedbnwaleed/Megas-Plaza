<?php
/**
 * Mailer Service
 * 
 * Handles email sending with fallback to logging
 */

class Mailer {
    
    /**
     * Send email
     */
    public static function send($to, $subject, $message, $headers = []) {
        if (!MAIL_ENABLED) {
            return self::logEmail($to, $subject, $message);
        }
        
        // Default headers
        $defaultHeaders = [
            'From' => MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>',
            'Reply-To' => MAIL_FROM_EMAIL,
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=UTF-8'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        
        // Convert headers array to string
        $headerString = '';
        foreach ($headers as $key => $value) {
            $headerString .= $key . ': ' . $value . "\r\n";
        }
        
        // Send email
        $result = mail($to, $subject, $message, $headerString);
        
        // Log email attempt
        $status = $result ? 'sent' : 'failed';
        self::logEmail($to, $subject, $message, $status);
        
        return $result;
    }
    
    /**
     * Send HTML email
     */
    public static function sendHtml($to, $subject, $htmlMessage, $textMessage = null) {
        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8'
        ];
        
        if ($textMessage) {
            // Multipart email (HTML + text)
            $boundary = uniqid('boundary_');
            
            $headers['Content-Type'] = 'multipart/alternative; boundary="' . $boundary . '"';
            
            $message = "--{$boundary}\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $textMessage . "\r\n\r\n";
            
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $htmlMessage . "\r\n\r\n";
            
            $message .= "--{$boundary}--";
        } else {
            $message = $htmlMessage;
        }
        
        return self::send($to, $subject, $message, $headers);
    }
    
    /**
     * Log email to file
     */
    private static function logEmail($to, $subject, $message, $status = 'logged') {
        $logFile = LOGS_PATH . '/email.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'status' => $status,
            'message' => substr($message, 0, 200) . (strlen($message) > 200 ? '...' : '')
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        return true;
    }
    
    /**
     * Send notification email
     */
    public static function sendNotification($to, $title, $body, $actionUrl = null) {
        $message = "Hello,\n\n";
        $message .= $body . "\n\n";
        
        if ($actionUrl) {
            $message .= "Click here to take action: " . $actionUrl . "\n\n";
        }
        
        $message .= "Best regards,\n";
        $message .= APP_NAME . " Team";
        
        return self::send($to, $title, $message);
    }
}
