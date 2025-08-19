<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Transfer') }}
            </h2>
            <a href="{{ route('transfers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Transfers
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">
            <form action="{{ route('transfers.store') }}" method="POST" id="transferForm">
                @csrf
                
                <!-- Transfer Information -->
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Transfer Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="from_branch_id" class="block text-sm font-medium text-gray-700">From Branch*</label>
                                <select name="from_branch_id" id="from_branch_id" required onchange="loadBranchItems()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @if(auth()->user()->isAdmin())
                                        <option value="">Select Source Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $userBranch }}" selected>
                                            {{ auth()->user()->branch->name }}
                                        </option>
                                    @endif
                                </select>
                                @error('from_branch_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="to_branch_id" class="block text-sm font-medium text-gray-700">To Branch*</label>
                                <select name="to_branch_id" id="to_branch_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Destination Branch</option>
                                    @foreach($branches as $branch)
                                        @if(auth()->user()->isAdmin() || $branch->id != $userBranch)
                                            <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('to_branch_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer Items -->
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Items to Transfer</h3>
                            <button type="button" onclick="addTransferItem()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Add Item
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <div id="transfer-items">
                            <!-- Items will be added here dynamically -->
                        </div>
                        
                        @error('items')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        Create Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemCounter = 0;
        let availableItems = {};

        function loadBranchItems() {
            const branchId = document.getElementById('from_branch_id').value;
            if (!branchId) return;

            fetch(`/api/branch/${branchId}/items`)
                .then(response => response.json())
                .then(data => {
                    availableItems = data;
                    // Clear existing items
                    document.getElementById('transfer-items').innerHTML = '';
                    itemCounter = 0;
                })
                .catch(error => {
                    console.error('Error loading branch items:', error);
                    alert('Error loading branch inventory.');
                });
        }

        function addTransferItem() {
            if (!availableItems.units && !availableItems.parts) {
                alert('Please select a source branch first.');
                return;
            }

            itemCounter++;
            const itemsContainer = document.getElementById('transfer-items');
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'border border-gray-200 rounded-lg p-4 mb-4';
            itemDiv.id = `item-${itemCounter}`;
            
            let unitsOptions = '';
            if (availableItems.units) {
                availableItems.units.forEach(unit => {
                    unitsOptions += `<option value="unit-${unit.id}" data-type="unit" data-max="1">${unit.product.name} - ${unit.chassis_no}</option>`;
                });
            }

            let partsOptions = '';
            if (availableItems.parts) {
                availableItems.parts.forEach(part => {
                    partsOptions += `<option value="part-${part.product.id}" data-type="part" data-max="${part.stock_quantity}">${part.product.name} (Available: ${part.stock_quantity})</option>`;
                });
            }
            
            itemDiv.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-900">Item ${itemCounter}</h4>
                    <button type="button" onclick="removeTransferItem(${itemCounter})" class="text-red-600 hover:text-red-800">
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Item*</label>
                        <select name="items[${itemCounter}][item_select]" onchange="updateItemInfo(${itemCounter})" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Item to Transfer</option>
                            <optgroup label="Units">
                                ${unitsOptions}
                            </optgroup>
                            <optgroup label="Parts/Accessories">
                                ${partsOptions}
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity*</label>
                        <input type="number" name="items[${itemCounter}][quantity]" min="1" required
                               onchange="validateQuantity(${itemCounter})"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                
                <input type="hidden" name="items[${itemCounter}][product_id]">
                <input type="hidden" name="items[${itemCounter}][nwow_unit_id]">
            `;
            
            itemsContainer.appendChild(itemDiv);
        }

        function removeTransferItem(itemId) {
            const itemDiv = document.getElementById(`item-${itemId}`);
            if (itemDiv) {
                itemDiv.remove();
            }
        }

        function updateItemInfo(itemId) {
            const selectElement = document.querySelector(`select[name="items[${itemId}][item_select]"]`);
            const quantityInput = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
            const productIdInput = document.querySelector(`input[name="items[${itemId}][product_id]"]`);
            const unitIdInput = document.querySelector(`input[name="items[${itemId}][nwow_unit_id]"]`);
            
            const selectedValue = selectElement.value;
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            if (selectedValue) {
                const type = selectedOption.dataset.type;
                const maxQuantity = parseInt(selectedOption.dataset.max);
                
                quantityInput.max = maxQuantity;
                
                if (type === 'unit') {
                    const unitId = selectedValue.replace('unit-', '');
                    unitIdInput.value = unitId;
                    productIdInput.value = availableItems.units.find(u => u.id == unitId).product_id;
                    quantityInput.value = 1;
                    quantityInput.readOnly = true;
                } else {
                    const productId = selectedValue.replace('part-', '');
                    productIdInput.value = productId;
                    unitIdInput.value = '';
                    quantityInput.value = 1;
                    quantityInput.readOnly = false;
                }
            } else {
                productIdInput.value = '';
                unitIdInput.value = '';
                quantityInput.readOnly = false;
            }
        }

        function validateQuantity(itemId) {
            const quantityInput = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
            const maxQuantity = parseInt(quantityInput.max);
            const currentQuantity = parseInt(quantityInput.value);
            
            if (currentQuantity > maxQuantity) {
                alert(`Maximum available quantity is ${maxQuantity}`);
                quantityInput.value = maxQuantity;
            }
        }

        // Load items when page loads if source branch is preselected
        document.addEventListener('DOMContentLoaded', function() {
            @if(!auth()->user()->isAdmin())
                loadBranchItems();
            @endif
        });
    </script>
    @endpush
</x-app-layout>