<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentType::firstOrCreate([
            'title' => 'Financial Documents',
            'description' => 'Covers records such as budgets, invoices, receipts, statements, and reports that track and support financial transactions and accountability.',
        ]);

        $eligibility = DocumentType::firstOrCreate([
            'title' => 'Eligibility Documents',
            'description' => 'Covers credentials, certifications, licenses, and other official records that verify an individual’s or organization’s qualifications to meet specified requirements.',
        ]);

        DocumentType::firstOrCreate([
            'title' => 'Legal Documents',
            'description' => 'Covers contracts, agreements, permits, licenses, and other official records required for legal and regulatory compliance.',
        ]);

        $eligibility->documents()->firstOrCreate([
            'title' => 'Business Permit',
            'description' => 'An official authorization issued by the local government that allows an individual or organization to legally operate a business within its jurisdiction.',
            'procurement_type' => ["goods", "infrastructure", "consulting services"],
            'is_required' => true
        ]);

        $eligibility->documents()->firstOrCreate([
            'title' => 'PhilGEPS Registration',
            'description' => 'A mandatory registration for suppliers, contractors, and consultants who wish to participate in government procurement activities in the Philippines.',
            'procurement_type' => ["goods", "infrastructure", "consulting services"],
            'is_required' => true
        ]);

        $eligibility->documents()->firstOrCreate([
            'title' => 'Income Tax Return (ITR)',
            'description' => 'A document that reports an individual’s or organization’s income, expenses, and other relevant financial information to the tax authorities for the purpose of calculating and paying taxes owed.',
            'procurement_type' => ["goods", "infrastructure", "consulting services"],
            'is_required' => true
        ]);

        $eligibility->documents()->firstOrCreate([
            'title' => 'Omnibus Sworn Statement',
            'description' => 'A legal document in which an individual or organization makes a sworn declaration regarding various statements, facts, or commitments, often used in procurement processes to affirm compliance with requirements and regulations.',
            'procurement_type' => ["goods", "infrastructure", "consulting services"],
            'is_required' => true
        ]);

        $eligibility->documents()->firstOrCreate([
            'title' => 'PCAB License',
            'description' => 'A license issued by the Philippine Contractors Accreditation Board (PCAB) that certifies a construction company or contractor to legally engage in construction activities within the Philippines.',
            'procurement_type' => ["infrastructure"],
            'is_required' => true
        ]);
    }
}
