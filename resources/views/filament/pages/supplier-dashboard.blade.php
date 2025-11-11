<div class="p-8 space-y-8">

    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
        <h1 class="text-2xl font-semibold text-gray-800">
            Welcome, {{ request()->user()->name ?? 'Supplier' }}!
        </h1>
        <p class="text-gray-600 mt-2">
            We’re glad to have you onboard. This panel allows you to manage your company information,
            submit bid proposals, and stay updated with procurement activities of the Provincial Government.
        </p>
    </div>

    <!-- Reminders Section -->
    <div class="bg-pink-50 rounded-2xl border border-pink-100 p-6">
        <h2 class="text-lg font-semibold text-pink-700 mb-3">Getting Started</h2>
        <ul class="space-y-3 text-gray-700">
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-600 mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Complete your <a href="/supplier/business-profile" class="text-pink-600 hover:underline">Business Profile</a> to ensure your company information is accurate and up-to-date.</span>
            </li>
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-600 mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0l-4 4m4-4l4 4" />
                </svg>
                <span>Upload required <a href="/supplier/business-profile" class="text-pink-600 hover:underline">Business Documents</a> such as Mayor’s Permit, DTI/SEC registration, and tax clearance.</span>
            </li>
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-600 mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405M19 13V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h8" />
                </svg>
                <span>Regularly check for <span class="text-pink-600">Procurement Opportunities</span> and participate in bidding events.</span>
            </li>
        </ul>
    </div>

    <!-- Notification Section -->
    <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Important Reminder</h2>
        <p class="text-gray-700 leading-relaxed">
            Please ensure all your business information and uploaded documents remain valid and up to date.
            Expired permits or incomplete profiles may prevent you from joining procurement opportunities.
        </p>
    </div>

    <!-- Support Section -->
    <div class="bg-gray-100 rounded-2xl border border-gray-200 p-6 text-center">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Need Help?</h2>
        <p class="text-gray-600 mb-3">If you encounter issues or have questions, you may contact the Procurement Management Office.</p>
        <a href="mailto:procurement.support@davaodelsur.gov.ph" class="inline-block px-5 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
            Contact Support
        </a>
    </div>

</div>
