<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DisbursementType;
use App\Models\FinancialInstitution;
use Illuminate\Validation\Rule;

class PurchaseRequestController extends Controller
{
    public function create()
    {
        $disbursementTypes = DisbursementType::orderBy('name')->get();
        $bankTransferType = $disbursementTypes->firstWhere(
            'name',
            'Bank Transfer'
        );
        $financialInstitutions = FinancialInstitution::with(
            'financialInstitutionType'
        )
            ->orderBy('name')
            ->get();
        return view('purchasing.request.create', [
            'disbursementTypes' => $disbursementTypes,
            'bankTransferTypeId' => $bankTransferType?->id,
            'financialInstitutions' => $financialInstitutions,
        ]);
    }
    public function store(Request $request)
    {
        $bankTransferType = DisbursementType::where(
            'name',
            'Bank Transfer'
        )->first();
        $isBankTransfer = $bankTransferType &&
            (int) $request->input('disbursement_type_id') === $bankTransferType->id;
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:2000',
            ],
            'disbursement_type_id' => [
                'required',
                'exists:disbursement_types,id',
            ],
            'financial_institution_id' => [
                Rule::requiredIf($isBankTransfer),
                'nullable',
                'exists:financial_institutions,id',
            ],
            'account_number' => [
                Rule::requiredIf($isBankTransfer),
                'nullable',
                'string',
                'max:100',
            ],
            'account_name' => [
                Rule::requiredIf($isBankTransfer),
                'nullable',
                'string',
                'max:255',
            ],
            'purpose' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.description' => [
                'required',
                'string',
                'max:255',
            ],
            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'items.*.remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'items.*.image' => [
                'nullable',
                'image',
                'max:5120',
            ],
            'attachments' => [
                'nullable',
                'array',
            ],
            'attachments.*' => [
                'file',
                'max:10240',
            ],
        ]);
        DB::transaction(function () use ($request, $validated) {
            $purchaseRequest = PurchaseRequest::create([
                'title' => $validated['title'],
                'disbursement_type_id' =>
                    $validated['disbursement_type_id'],
                'financial_institution_id' =>
                    $validated['financial_institution_id'] ?? null,
                'account_number' =>
                    $validated['account_number'] ?? null,
                'account_name' =>
                    $validated['account_name'] ?? null,
                'purpose' =>
                    $validated['purpose'] ?? null,
            ]);
            foreach ($validated['items'] as $index => $item) {
                $imagePath = null;
                if ($request->hasFile("items.$index.image")) {
                    $imagePath = $request
                        ->file("items.$index.image")
                        ->store('purchase-items', 'public');
                }
                $purchaseRequest->items()->create([
                    'description' => $item['description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? null,
                    'image_path' => $imagePath,
                ]);
            }
        });
        return redirect()
            ->route('purchasing.request.index')
            ->with(
                'success',
                'Purchase request submitted successfully.'
            );
    }
}
