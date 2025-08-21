<?php
/**
 * Validator Helper
 * 
 * Handles form validation with various rules
 */

class Validator {
    
    /**
     * Validate data against rules
     */
    public function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                $error = $this->validateRule($field, $value, $rule, $data);
                if ($error) {
                    $errors[$field] = $error;
                    break; // Stop at first error for this field
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate single rule
     */
    private function validateRule($field, $value, $rule, $allData) {
        // Parse rule and parameters
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    return $this->getErrorMessage($field, 'required');
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return $this->getErrorMessage($field, 'email');
                }
                break;
                
            case 'min':
                $min = (int) $params[0];
                if (!empty($value) && strlen($value) < $min) {
                    return $this->getErrorMessage($field, 'min', ['min' => $min]);
                }
                break;
                
            case 'max':
                $max = (int) $params[0];
                if (!empty($value) && strlen($value) > $max) {
                    return $this->getErrorMessage($field, 'max', ['max' => $max]);
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return $this->getErrorMessage($field, 'numeric');
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    return $this->getErrorMessage($field, 'integer');
                }
                break;
                
            case 'same':
                $otherField = $params[0];
                $otherValue = $allData[$otherField] ?? null;
                if ($value !== $otherValue) {
                    return $this->getErrorMessage($field, 'same', ['other' => $this->getFieldName($otherField)]);
                }
                break;
                
            case 'different':
                $otherField = $params[0];
                $otherValue = $allData[$otherField] ?? null;
                if ($value === $otherValue) {
                    return $this->getErrorMessage($field, 'different', ['other' => $this->getFieldName($otherField)]);
                }
                break;
                
            case 'in':
                if (!empty($value) && !in_array($value, $params)) {
                    return $this->getErrorMessage($field, 'in', ['values' => implode(', ', $params)]);
                }
                break;
                
            case 'not_in':
                if (!empty($value) && in_array($value, $params)) {
                    return $this->getErrorMessage($field, 'not_in');
                }
                break;
                
            case 'regex':
                $pattern = $params[0];
                if (!empty($value) && !preg_match($pattern, $value)) {
                    return $this->getErrorMessage($field, 'regex');
                }
                break;
                
            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    return $this->getErrorMessage($field, 'url');
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    return $this->getErrorMessage($field, 'date');
                }
                break;
                
            case 'before':
                $beforeDate = $params[0];
                if (!empty($value) && strtotime($value) >= strtotime($beforeDate)) {
                    return $this->getErrorMessage($field, 'before', ['date' => $beforeDate]);
                }
                break;
                
            case 'after':
                $afterDate = $params[0];
                if (!empty($value) && strtotime($value) <= strtotime($afterDate)) {
                    return $this->getErrorMessage($field, 'after', ['date' => $afterDate]);
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Get error message for rule
     */
    private function getErrorMessage($field, $rule, $params = []) {
        $fieldName = $this->getFieldName($field);
        
        $messages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field must be a valid email address.',
            'min' => 'The :field must be at least :min characters.',
            'max' => 'The :field may not be greater than :max characters.',
            'numeric' => 'The :field must be a number.',
            'integer' => 'The :field must be an integer.',
            'same' => 'The :field and :other must match.',
            'different' => 'The :field and :other must be different.',
            'in' => 'The selected :field is invalid.',
            'not_in' => 'The selected :field is invalid.',
            'regex' => 'The :field format is invalid.',
            'url' => 'The :field must be a valid URL.',
            'date' => 'The :field is not a valid date.',
            'before' => 'The :field must be a date before :date.',
            'after' => 'The :field must be a date after :date.',
        ];
        
        $message = $messages[$rule] ?? 'The :field is invalid.';
        
        // Replace placeholders
        $message = str_replace(':field', $fieldName, $message);
        
        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }
        
        return $message;
    }
    
    /**
     * Convert field name to human readable
     */
    private function getFieldName($field) {
        return ucwords(str_replace(['_', '-'], ' ', $field));
    }
    
    /**
     * Quick validation methods
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function required($value) {
        return !empty($value) || $value === '0';
    }
    
    public static function numeric($value) {
        return is_numeric($value);
    }
    
    public static function minLength($value, $min) {
        return strlen($value) >= $min;
    }
    
    public static function maxLength($value, $max) {
        return strlen($value) <= $max;
    }
}
