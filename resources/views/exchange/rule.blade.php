@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Exchange Rules') }}</h1>

        <div class=" p-6 rounded-xl  space-y-6 text-gray-700 leading-relaxed">

            <p>{{ __('To ensure a safe and transparent environment for the badminton community, we have established the following rules for all users engaging in product exchanges.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('1. Eligibility') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Users must register an account to post or initiate an exchange.') }}</li>
                <li>{{ __('Only genuine badminton-related items are allowed for exchange (rackets, shoes, bags, accessories).') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('2. Listing Requirements') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Provide accurate and honest descriptions of product condition.') }}</li>
                <li>{{ __('Upload real images of the item being offered.') }}</li>
                <li>{{ __('State clearly whether you are looking to exchange, sell, or both.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('3. Communication') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Use the in-platform chat to communicate with other users.') }}</li>
                <li>{{ __('Respectful and courteous communication is mandatory.') }}</li>
                <li>{{ __('Do not share personal information such as bank account or ID outside secure channels.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('4. Exchange Process') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Meet in public or well-known badminton courts when possible.') }}</li>
                <li>{{ __('Inspect the product thoroughly before confirming the exchange.') }}</li>
                <li>{{ __('Badminton Exchange is not responsible for disputes arising from off-platform transactions.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('5. Prohibited Items and Behavior') }}</h2>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('Counterfeit or fake products are strictly prohibited.') }}</li>
                <li>{{ __('Do not post unrelated items (electronics, pets, etc.).') }}</li>
                <li>{{ __('Harassment, spam, and fraudulent activities will result in a permanent ban.') }}</li>
            </ul>

            <h2 class="text-xl font-semibold">{{ __('6. Reporting and Moderation') }}</h2>
            <p>{{ __('Users can report suspicious behavior or content. Our team will review and take appropriate action.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('7. Updates to Rules') }}</h2>
            <p>{{ __('These rules may be updated at any time to adapt to the community’s needs. We recommend users check this page periodically.') }}</p>

            <h2 class="text-xl font-semibold">{{ __('8. Contact Support') }}</h2>
            <p>{{ __('If you have questions about these rules or need assistance, please contact us at') }}
                <a href="mailto:support@badmintonexchange.com" class="text-blue-600 underline">support@badmintonexchange.com</a>.
            </p>
        </div>
    </div>
@endsection
