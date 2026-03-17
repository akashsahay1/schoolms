<?php

namespace App\Traits;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesCustomFields
{
    /**
     * Get form field settings for a scope (student/staff).
     * Returns array of [field_name => ['visible' => bool, 'required' => bool]]
     */
    protected function getFormFieldSettings(string $scope): array
    {
        $key = $scope . '_form_fields';
        return json_decode(Setting::get($key, '{}'), true) ?: [];
    }

    /**
     * Get active custom fields for a given scope (student/teacher/staff).
     */
    protected function getCustomFields(string $scope)
    {
        return CustomField::active()
            ->where(function ($q) use ($scope) {
                $q->where('applies_to', $scope)->orWhere('applies_to', 'all');
            })
            ->ordered()
            ->get();
    }

    /**
     * Get existing custom field values for a model as [field_id => value] array.
     */
    protected function getCustomFieldValues($model): array
    {
        return $model->customFieldValues()
            ->pluck('value', 'custom_field_id')
            ->toArray();
    }

    /**
     * Validate and save custom field values for a model.
     */
    protected function saveCustomFieldValues(Request $request, $model, string $scope): void
    {
        $customFields = $this->getCustomFields($scope);
        $values = $request->input('custom_fields', []);

        foreach ($customFields as $field) {
            $value = $values[$field->id] ?? null;

            // Handle file uploads
            if ($field->field_type === 'file' && $request->hasFile("custom_fields.{$field->id}")) {
                $file = $request->file("custom_fields.{$field->id}");
                $value = $file->store('custom-fields', 'public');

                // Delete old file if exists
                $existing = CustomFieldValue::where('custom_field_id', $field->id)
                    ->where('model_type', get_class($model))
                    ->where('model_id', $model->id)
                    ->first();
                if ($existing && $existing->value) {
                    Storage::disk('public')->delete($existing->value);
                }
            }

            // Skip if file field with no new upload
            if ($field->field_type === 'file' && !$request->hasFile("custom_fields.{$field->id}")) {
                continue;
            }

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $field->id,
                    'model_type' => get_class($model),
                    'model_id' => $model->id,
                ],
                ['value' => $value]
            );
        }
    }
}
