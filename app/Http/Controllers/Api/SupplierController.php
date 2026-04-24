<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierCollection;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): SupplierCollection
    {
        $suppliers = Supplier::query()
            ->whereHas('supplierLobs')
            ->with(['supplierLobs.lobCategory', 'supplierLobs.lobSubcategory'])
            ->orderBy('business_name')
            ->get();

        return new SupplierCollection($suppliers);
    }
}
