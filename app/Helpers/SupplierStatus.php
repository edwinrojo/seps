<?php

namespace App\Helpers;

use App\Models\Document;
use App\Models\Supplier;

class SupplierStatus
{
    public static function getStatusLabel(Supplier $supplier): string
    {
        $address_Label = self::addressValidation($supplier) ? 'Validated' : 'Pending site validation';
        if (self::addressValidation($supplier)) {
            return 'Validated';
        } else {
            return 'Pending Validation / Lacking Documents';
        }
    }

    public static function getStatusColor(Supplier $supplier): string
    {
        if (self::addressValidation($supplier)) {
            return 'success';
        } else {
            return 'warning';
        }
    }

    public static function addressValidation(Supplier $supplier): bool
    {
        if ($supplier->addresses->isEmpty()) {
            return false;
        }

        $is_site_validated = true;

        foreach ($supplier->addresses as $address) {
            if (! $address->is_validated) {
                $is_site_validated = false;
                break;
            }
        }

        return $is_site_validated;
    }

    public static function documentValidation(Supplier $supplier): bool
    {
        if ($supplier->attachments->isEmpty()) {
            return false;
        }

        $required_documents = Document::where('procurement_type', 'LIKE', '%' . $supplier->supplier_type->value . '%')
            ->where('is_required', true)
            ->count();
        if ($supplier->attachments->count() < $required_documents) {
            return false;
        }

        $is_document_validated = true;

        foreach ($supplier->attachments as $attachment) {
            if (! $attachment->is_validated) {
                $is_document_validated = false;
                break;
            }
        }

        return $is_document_validated;
    }

    public static function lobValidation(Supplier $supplier): bool
    {
        if ($supplier->supplierLobs->isEmpty()) {
            return false;
        }

        $is_lob_validated = true;

        foreach ($supplier->lob_statuses as $lob) {
            if (! $lob->is_validated) {
                $is_lob_validated = false;
                break;
            }
        }

        return $is_lob_validated;
    }
}
