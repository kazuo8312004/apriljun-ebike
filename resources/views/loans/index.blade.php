<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Loans') }}
            </h2>
            <a href="{{ route('loans.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                New Loan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1                                <textarea name="notes" id="notes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        Complete Sale
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemCounter = 0;
        let products = @json($products);
        let availableUnits = @json($availableUnits);

        function addSaleItem() {
            itemCounter++;
            const itemsContainer = document.getElementById('sale-items');
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'border border-gray-200 rounded-lg p-4 mb-4';
            itemDiv.id = `item-${itemCounter}`;
            
            itemDiv.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-900">Item ${itemCounter}</h4>
                    <button type="button" onclick="removeSaleItem(${itemCounter})" class="text-red-600 hover:text-red-800">
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product*</label>
                        <select name="items[${itemCounter}][product_id]" onchange="updateProductInfo(${itemCounter})" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Product</option>
                            ${products.map(product => `<option value="${product.id}" data-price="${product.price}" data-type="${product.type}">${product.name}</option>`).join('')}
                        </select>
                    </div>
                    
                    <div id="unit-select-${itemCounter}" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700">Unit*</label>
                        <select name="items[${itemCounter}][nwow_unit_id]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Unit</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity*</label>
                        <input type="number" name="items[${itemCounter}][quantity]" value="1" min="1" required
                               onchange="calculateItemTotal(${itemCounter})"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Price*</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="items[${itemCounter}][unit_price]" step="0.01" min="0" required
                                   onchange="calculateItemTotal(${itemCounter})"
                                   class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="text-right">
                        <span class="text-lg font-medium">Item Total: ₱<span id="item-total-${itemCounter}">0.00</span></span>
                    </div>
                </div>
            `;
            
            itemsContainer.appendChild(itemDiv);
        }

        function removeSaleItem(itemId) {
            const itemDiv = document.getElementById(`item-${itemId}`);
            if (itemDiv) {
                itemDiv.remove();
                calculateTotal();
            }
        }

        function updateProductInfo(itemId) {
            const productSelect = document.querySelector(`select[name="items[${itemId}][product_id]"]`);
            const unitSelect = document.getElementById(`unit-select-${itemId}`);
            const unitSelectInput = document.querySelector(`select[name="items[${itemId}][nwow_unit_id]"]`);
            const priceInput = document.querySelector(`input[name="items[${itemId}][unit_price]"]`);
            const quantityInput = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
            
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            
            if (selectedOption.value) {
                const price = selectedOption.dataset.price;
                const type = selectedOption.dataset.type;
                
                priceInput.value = price;
                
                if (type === 'unit') {
                    // Show unit selector for e-bikes
                    unitSelect.style.display = 'block';
                    quantityInput.value = 1;
                    quantityInput.readOnly = true;
                    
                    // Populate available units for this product
                    const productUnits = availableUnits.filter(unit => unit.product_id == selectedOption.value);
                    unitSelectInput.innerHTML = '<option value="">Select Unit</option>';
                    productUnits.forEach(unit => {
                        unitSelectInput.innerHTML += `<option value="${unit.id}">${unit.chassis_no} (${unit.color || 'No color'})</option>`;
                    });
                    
                    unitSelectInput.required = true;
                } else {
                    // Hide unit selector for parts/accessories
                    unitSelect.style.display = 'none';
                    quantityInput.readOnly = false;
                    unitSelectInput.required = false;
                }
                
                calculateItemTotal(itemId);
            } else {
                unitSelect.style.display = 'none';
                priceInput.value = '';
                unitSelectInput.required = false;
            }
        }

        function calculateItemTotal(itemId) {
            const quantity = parseFloat(document.querySelector(`input[name="items[${itemId}][quantity]"]`).value) || 0;
            const price = parseFloat(document.querySelector(`input[name="items[${itemId}][unit_price]"]`).value) || 0;
            const total = quantity * price;
            
            document.getElementById(`item-total-${itemId}`).textContent = total.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            
            // Sum all item totals
            document.querySelectorAll('[id^="item-total-"]').forEach(element => {
                subtotal += parseFloat(element.textContent) || 0;
            });
            
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const total = Math.max(0, subtotal - discount);
            
            document.getElementById('total-amount').textContent = `₱${total.toFixed(2)}`;
        }

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addSaleItem();
        });
    </script>
    @endpush
</x-app-layout>