<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers Eligibility and Profiling System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <script type="module" src="index.tsx"></script> -->

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #f8fafc; /* bg-slate-50 */
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div id="app">
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="text-xl font-bold text-slate-900">SupplierFlow</span>
                </div>
                <a href="#eligibility-form" class="hidden md:inline-block bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                    Get Started
                </a>
            </nav>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="bg-white">
                <div class="container mx-auto px-6 py-20 text-center">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 leading-tight">
                        Streamline Your Supplier Journey
                    </h1>
                    <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
                        Join our network of trusted suppliers. Complete our streamlined eligibility and profiling process to unlock new opportunities.
                    </p>
                    <a href="#eligibility-form" class="mt-8 inline-block bg-blue-600 text-white font-bold px-8 py-4 rounded-lg text-lg hover:bg-blue-700 transition-transform duration-300 hover:scale-105">
                        Check Your Eligibility Now
                    </a>
                </div>
            </section>

            <!-- How It Works Section -->
            <section class="py-20">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-12">
                         <h2 class="text-3xl font-bold text-slate-900">A Simple, Transparent Process</h2>
                         <p class="mt-2 text-md text-slate-500">Get verified in three easy steps.</p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-10 text-center">
                        <div class="bg-white p-8 rounded-xl shadow-sm">
                            <div class="bg-blue-100 text-blue-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">1. Submit Profile</h3>
                            <p class="mt-2 text-slate-600">Fill out our straightforward eligibility form with your company's details.</p>
                        </div>
                        <div class="bg-white p-8 rounded-xl shadow-sm">
                            <div class="bg-blue-100 text-blue-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">2. Verification</h3>
                            <p class="mt-2 text-slate-600">Our team reviews your submission against our criteria for quality and compliance.</p>
                        </div>
                        <div class="bg-white p-8 rounded-xl shadow-sm">
                            <div class="bg-blue-100 text-blue-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">3. Get Approved</h3>
                            <p class="mt-2 text-slate-600">Once approved, your profile is activated, making you visible for new projects.</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Eligibility Form Section -->
            <section id="eligibility-form" class="py-20 bg-white">
                <div class="container mx-auto px-6">
                    <div class="max-w-3xl mx-auto">
                        <div class="text-center mb-12">
                             <h2 class="text-3xl font-bold text-slate-900">Supplier Eligibility & Profiling Form</h2>
                             <p class="mt-2 text-md text-slate-500">Complete the form below to begin the process.</p>
                        </div>

                        <form id="supplier-form" class="space-y-6 bg-slate-50 p-8 rounded-lg border border-slate-200">
                            <div>
                                <label for="companyName" class="block text-sm font-medium text-slate-700">Company Name</label>
                                <input type="text" id="companyName" name="companyName" required class="mt-1 block w-full px-4 py-2 bg-white border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="registrationNumber" class="block text-sm font-medium text-slate-700">Business Registration Number</label>
                                <input type="text" id="registrationNumber" name="registrationNumber" required class="mt-1 block w-full px-4 py-2 bg-white border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="yearsInBusiness" class="block text-sm font-medium text-slate-700">Years in Business</label>
                                <input type="number" id="yearsInBusiness" name="yearsInBusiness" min="0" required class="mt-1 block w-full px-4 py-2 bg-white border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="industry" class="block text-sm font-medium text-slate-700">Industry / Category</label>
                                <select id="industry" name="industry" required class="mt-1 block w-full px-4 py-2 bg-white border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select an industry</option>
                                    <option value="tech">Technology</option>
                                    <option value="manufacturing">Manufacturing</option>
                                    <option value="logistics">Logistics</option>
                                    <option value="consulting">Consulting</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" id="submit-button" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                                    Submit for Review
                                </button>
                            </div>
                        </form>
                        <div id="success-message" class="hidden mt-6 p-4 bg-green-100 text-green-800 border border-green-200 rounded-lg text-center">
                            <p class="font-semibold">Thank you for your submission!</p>
                            <p>Your profile has been received. Our team will review it and get back to you within 5-7 business days.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-800 text-slate-400">
            <div class="container mx-auto px-6 py-8 text-center">
                <p>&copy; 2024 SupplierFlow. All Rights Reserved.</p>
                <p class="text-sm mt-1">A modern solution for supplier management.</p>
            </div>
        </footer>
    </div>

</body>
</html>
