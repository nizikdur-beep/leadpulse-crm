<?php

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $ruleName, $value, $params);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        $first = reset($this->errors);
        return is_array($first) ? $first[0] : $first;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function applyRule(string $field, string $rule, mixed $value, array $params): void
    {
        $fieldLabel = $this->formatFieldLabel($field);

        switch ($rule) {
            case 'required':
                if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$fieldLabel} maydonini toʻldirish shart.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$fieldLabel} toʻgʻri email manzil boʻlishi kerak.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "{$fieldLabel} faqat raqamlardan iborat boʻlishi kerak.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (is_numeric($value)) {
                    if ((float)$value < $min) {
                        $this->addError($field, "{$fieldLabel} kamida {$min} boʻlishi kerak.");
                    }
                } elseif (is_string($value) && mb_strlen($value) < $min) {
                    $this->addError($field, "{$fieldLabel} kamida {$min} belgidan iborat boʻlishi kerak.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 255);
                if (is_numeric($value)) {
                    if ((float)$value > $max) {
                        $this->addError($field, "{$fieldLabel} koʻpi bilan {$max} boʻlishi mumkin.");
                    }
                } elseif (is_string($value) && mb_strlen($value) > $max) {
                    $this->addError($field, "{$fieldLabel} koʻpi bilan {$max} belgidan oshmasligi kerak.");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "{$fieldLabel} uchun tanlangan qiymat yaroqsiz.");
                }
                break;

            case 'unique':
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;

                if (!empty($value) && !empty($table)) {
                    $db = Database::connect();
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
                    $queryParams = ['val' => $value];

                    if (!empty($exceptId)) {
                        $sql .= " AND id != :except_id";
                        $queryParams['except_id'] = $exceptId;
                    }

                    $stmt = $db->prepare($sql);
                    $stmt->execute($queryParams);
                    if ((int)$stmt->fetchColumn() > 0) {
                        $this->addError($field, "Ushbu {$fieldLabel} allaqachon roʻyxatdan oʻtkazilgan.");
                    }
                }
                break;

            case 'phone':
                if (!empty($value)) {
                    // Normalize and check phone
                    $cleaned = preg_replace('/[^0-9+]/', '', (string)$value);
                    if (strlen($cleaned) < 7) {
                        $this->addError($field, "{$fieldLabel} toʻgʻri telefon raqam formatida boʻlishi kerak.");
                    }
                }
                break;
        }
    }

    private function formatFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Ism',
            'email' => 'Email',
            'password' => 'Parol',
            'phone' => 'Telefon',
            'company' => 'Kompaniya',
            'title' => 'Sarlavha',
            'amount' => 'Summa',
            'stage' => 'Bosqich',
            'source' => 'Manba',
            'client_id' => 'Mijoz',
            'user_id' => 'Masʼul xodim',
            'due_date' => 'Muddati',
            'priority' => 'Muhimlik darajasi',
            'status' => 'Holat',
            'type' => 'Faoliyat turi',
            'role' => 'Rol',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
