<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Loan') }}
            </h2>
            <a href="{{ route('loans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Loans
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <form action="{{ route('loans.store') }}" method="POST" class="p-6">
                    @csrf

                    @if(session('error'))
                        <p class="mb-4 text-red-600 font-medium">{{ session('error') }}</p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Select Unit -->
                        <div>
                            <label for="nwow_unit_id" class="block text-sm font-medium text-gray-700">Select Unit*</label>
                            <select name="nwow_unit_id" id="nwow_unit_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Unit</option>
                                @foreach($availableUnits as $unit)
                                    <option value="{{ $unit->id }}" {{ old('nwow_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->product->name }} - {{ $unit->chassis_no }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nwow_unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower Name -->
                        <div>
                            <label for="borrower_name" class="block text-sm font-medium text-gray-700">Borrower Name*</label>
                            <input type="text" name="borrower_name" id="borrower_name" value="{{ old('borrower_name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('borrower_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower Phone -->
                        <div>
                            <label for="borrower_phone" class="block text-sm font-medium text-gray-700">Borrower Phone*</label>
                            <input type="text" name="borrower_phone" id="borrower_phone" value="{{ old('borrower_phone') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('borrower_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower Address -->
                        <div>
                            <label for="borrower_address" class="block text-sm font-medium text-gray-700">Borrower Address*</label>
                            <input type="text" name="borrower_address" id="borrower_address" value="{{ old('borrower_address') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('borrower_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower ID Type -->
                        <div>
                            <label for="borrower_id_type" class="block text-sm font-medium text-gray-700">ID Type*</label>
                            <input type="text" name="borrower_id_type" id="borrower_id_type" value="{{ old('borrower_id_type') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('borrower_id_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower ID Number -->
                        <div>
                            <label for="borrower_id_number" class="block text-sm font-medium text-gray-700">ID Number*</label>
                            <input type="text" name="borrower_id_number" id="borrower_id_number" value="{{ old('borrower_id_number') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('borrower_id_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Loan Date -->
                        <div>
                            <label for="loan_date" class="block text-sm font-medium text-gray-700">Loan Date*</label>
                            <input type="date" name="loan_date" id="loan_date" value="{{ old('loan_date', now()->toDateString()) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('loan_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expected Return Date -->
                        <div>
                            <label for="expected_return_date" class="block text-sm font-medium text-gray-700">Expected Return Date*</label>
                            <input type="date" name="expected_return_date" id="expected_return_date" value="{{ old('expected_return_date') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('expected_return_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Collateral Amount -->
                        <div>
                            <label for="collateral_amount" class="block text-sm font-medium text-gray-700">Collateral Amount*</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="collateral_amount" id="collateral_amount" value="{{ old('collateral_amount') }}" required step="0.01" min="0"
                                       class="pl-8 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            @error('collateral_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Loan Purpose -->
                        <div>
                            <label for="loan_purpose" class="block text-sm font-medium text-gray-700">Loan Purpose</label>
                            <input type="text" name="loan_purpose" id="loan_purpose" value="{{ old('loan_purpose') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('loan_purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Create Loan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
