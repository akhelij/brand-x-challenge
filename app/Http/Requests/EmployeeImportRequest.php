<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeImportRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('type')) return;

        $contentType = $this->headers->get('Content-Type');
        switch ($contentType) {
            case 'text/csv':
            case 'application/csv':
                $this->merge(['type' => 'csv']);
                break;

            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $this->merge(['type' => 'xlsx']);
                break;

            case 'application/xml':
            case 'text/xml':
                $this->merge(['type' => 'xml']);
                break;

            default:
                $content = $this->getContent();

                if (str_starts_with($content, 'PK')) {
                    $this->merge(['type' => 'xlsx']);
                } elseif (str_starts_with(trim($content), '<?xml')) {
                    $this->merge(['type' => 'xml']);
                } elseif (str_contains($content, ',')) {
                    $this->merge(['type' => 'csv']);
                }
                break;
        }

    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:csv,xml,xlsx,api'],
            'file' => ['sometimes', 'file'],
            'source' => ['required_if:type,api', 'string']
        ];
    }
}
