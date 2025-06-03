@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Privacy Policy') }}</h1>

        <div class=" p-6 rounded-xl space-y-6 text-gray-700 leading-relaxed">

            <p>{{ __('At Badminton Exchange, we are committed to protecting the privacy and security of our users. This Privacy Policy outlines how we collect, use, and disclose your personal information.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('1. Information We Collect') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Personal identification information: name, email address, phone number, location.') }}</li>
                <li>{{ __('Transaction and listing data: product information, purchase history, messages.') }}</li>
                <li>{{ __('Device and browser data: IP address, browser type, operating system.') }}</li>
                <li>{{ __('Usage data: interactions, page visits, and behavioral metrics.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('2. How We Use Your Information') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('To manage your account and listings effectively.') }}</li>
                <li>{{ __('To enable smooth communication between buyers and sellers.') }}</li>
                <li>{{ __('To enhance user experience through personalization and recommendations.') }}</li>
                <li>{{ __('To process transactions and support payment services.') }}</li>
                <li>{{ __('To ensure safety and prevent fraudulent activities.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('3. Data Sharing and Disclosure') }}</h2>
            <p>{{ __('We do not sell your personal information to third parties. However, your data may be shared in the following scenarios:') }}</p>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('With legal authorities if required by law or regulation.') }}</li>
                <li>{{ __('With payment gateways or logistics providers for order fulfillment.') }}</li>
                <li>{{ __('With affiliated partners under strict confidentiality agreements.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('4. Cookies and Tracking Technologies') }}</h2>
            <p>{{ __('We use cookies and similar technologies to recognize your browser, improve site performance, and deliver relevant advertisements. You can adjust your browser settings to disable cookies.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('5. Data Retention') }}</h2>
            <p>{{ __('Your data is stored securely for as long as your account remains active, or as needed to fulfill our legal obligations and resolve disputes.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('6. Your Rights') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Access your personal information.') }}</li>
                <li>{{ __('Request correction or deletion of your data.') }}</li>
                <li>{{ __('Withdraw consent or object to certain uses of your data.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('7. Data Protection') }}</h2>
            <p>{{ __('We implement strict security measures, including SSL encryption, access controls, and regular security audits to protect your information from unauthorized access.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('8. Changes to This Policy') }}</h2>
            <p>{{ __('We may update this Privacy Policy periodically. Changes will be notified via email or displayed prominently on our website.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('9. Contact Us') }}</h2>
            <p>
                {{ __('If you have any questions or concerns about our Privacy Policy, please contact us at') }}
                <a href="mailto:support@badmintonexchange.com" class="text-blue-600 underline">support@badmintonexchange.com</a>.
            </p>
        </div>
    </div>
@endsection
