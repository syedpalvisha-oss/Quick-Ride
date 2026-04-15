<x-layouts.app title="Book Ride">

<div class="p-10">

    <!-- Progress indicator -->
    <div class="max-w-xl mb-8 flex justify-between items-center text-sm font-medium text-gray-400">
        <div id="indicator-1" class="text-green-400">1. Pickup</div>
        <div class="flex-1 border-t border-gray-600 mx-4"></div>
        <div id="indicator-2">2. Drop</div>
        <div class="flex-1 border-t border-gray-600 mx-4"></div>
        <div id="indicator-3">3. Review</div>
    </div>

    <h1 class="text-3xl font-bold mb-8 transition-colors duration-300" id="step_title">
        Enter Pickup
    </h1>

    <div class="max-w-2xl mx-auto">
        <!-- Form Side -->
        <div class="w-full">
            <form method="POST" action="/book-ride" id="book_ride_form" class="h-full">
                @csrf
    
                <!-- STEP 1 -->
                <div id="step-1" class="space-y-6">
                    <div>
                        <label for="pickup" class="block text-sm font-medium text-gray-300 mb-2">Original Pickup Location</label>
                        <input type="text" name="pickup" id="pickup" value="{{ old('pickup', request('pickup')) }}" required class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-base p-4" placeholder="e.g. 123 Main St">
                    </div>
                    
                    <div>
                        <label for="pickup_radius" class="block text-sm font-medium text-gray-300 mb-2">Pickup Radius</label>
                        <select name="pickup_radius" id="pickup_radius" required class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-base p-4">
                            <option value="" disabled selected>Select a radius</option>
                            <option value="100m" {{ old('pickup_radius') == '100m' ? 'selected' : '' }}>100m</option>
                            <option value="200m" {{ old('pickup_radius') == '200m' ? 'selected' : '' }}>200m</option>
                            <option value="300m" {{ old('pickup_radius') == '300m' ? 'selected' : '' }}>300m</option>
                        </select>
                    </div>
    
                    <div id="dynamic_pickup_container" class="hidden bg-gray-800/50 p-5 rounded-lg border border-gray-700">
                        <label class="block text-sm font-medium text-green-400 mb-3">Suggested Pickup Points</label>
                        <div id="suggested_points_list" class="space-y-3">
                            <!-- JS will populate -->
                        </div>
                        @error('final_pickup_point')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="pt-4 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="bg-green-500 text-white px-6 py-3 rounded text-center font-bold hover:bg-green-600 transition">
                            Next: Enter Drop
                        </button>
                    </div>
                </div>
    
                <!-- STEP 2 -->
                <div id="step-2" class="hidden space-y-6">
                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-300 mb-2">Destination</label>
                        <input type="text" name="destination" id="destination" value="{{ old('destination', request('destination')) }}" required class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-base p-4" placeholder="e.g. 456 Elm St">
                    </div>
    
                    @php $oldStops = old('stops', request('stops', [])); @endphp
                    <div id="stops_container" class="space-y-4">
                        @foreach($oldStops as $index => $stop)
                            <div class="relative animate-fade-in bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                                <label class="block text-sm font-medium text-orange-400 mb-2">Stop {{ $index + 1 }}</label>
                                <div class="flex items-center gap-3">
                                    <input type="text" name="stops[]" value="{{ $stop }}" required class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-base p-3" placeholder="Enter stop location...">
                                    <button type="button" onclick="removeStop(this)" class="text-red-500 hover:text-red-400 font-bold p-2 text-2xl" title="Remove Stop">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
    
                    <div class="text-right">
                        <button type="button" id="add_stop_btn" onclick="addStop()" class="text-sm text-green-400 hover:text-green-300 font-semibold transition inline-flex items-center {{ count($oldStops) >= 3 ? 'hidden' : '' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Intermediate Stop
                        </button>
                    </div>
    
                    <div class="pt-4 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="bg-gray-700 text-white px-6 py-3 rounded text-center font-bold hover:bg-gray-600 transition">
                            Back
                        </button>
                        <button type="button" onclick="nextStep(3)" class="bg-green-500 text-white px-6 py-3 rounded text-center font-bold hover:bg-green-600 transition">
                            Next: Review Details
                        </button>
                    </div>
                </div>
    
                <!-- STEP 3 -->
                <div id="step-3" class="hidden space-y-6">
                
                    <div class="bg-gray-800/80 p-5 rounded-lg border border-gray-700 space-y-3">
                        <h3 class="text-green-400 font-bold text-lg border-b border-gray-700 pb-2 mb-3">Ride Summary</h3>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="text-gray-400">Pickup Area:</span>
                            <span class="text-white font-medium" id="summary_pickup">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="text-gray-400">Exact Point:</span>
                            <span class="text-white font-medium" id="summary_final_pickup">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="text-gray-400">Destination:</span>
                            <span class="text-white font-medium" id="summary_drop">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between" id="summary_stops_container">
                            <span class="text-gray-400">Stops:</span>
                            <span class="text-white font-medium" id="summary_stops">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between pt-2 border-t border-gray-700">
                            <span class="text-gray-400">Ride Type:</span>
                            <span class="text-white font-bold" id="summary_type">-</span>
                        </div>
                    </div>
    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Select Ride Type</label>
                        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                            <label class="flex-1 flex items-center p-4 border border-gray-600 rounded-lg cursor-pointer hover:bg-gray-800 transition">
                                <input type="radio" name="ride_type" value="instant" checked class="form-radio text-green-500 bg-gray-800 border-gray-600 focus:ring-green-500 mr-3" onchange="toggleSchedule()"> 
                                <span class="font-medium">Book Now</span>
                            </label>
                            <label class="flex-1 flex items-center p-4 border border-gray-600 rounded-lg cursor-pointer hover:bg-gray-800 transition">
                                <input type="radio" name="ride_type" value="scheduled" {{ old('ride_type') == 'scheduled' ? 'checked' : '' }} class="form-radio text-green-500 bg-gray-800 border-gray-600 focus:ring-green-500 mr-3" onchange="toggleSchedule()"> 
                                <span class="font-medium">Schedule Ride</span>
                            </label>
                        </div>
                        @error('ride_type')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>
    
                    <div class="hidden animate-fade-in" id="scheduled_time_container">
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-300 mb-2">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time') }}" class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-base p-4" style="color-scheme: dark;">
                        @error('scheduled_time')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>
    
                    <div class="pt-4 flex flex-col sm:flex-row justify-between gap-4">
                        <button type="button" onclick="prevStep(2)" class="w-full sm:w-auto bg-gray-700 text-white px-6 py-4 rounded text-center font-bold hover:bg-gray-600 transition order-2 sm:order-1">
                            Back
                        </button>
                        <button type="submit" class="w-full sm:flex-1 bg-green-500 px-6 py-4 rounded text-center font-bold hover:bg-green-600 transition text-white order-1 sm:order-2">
                            Confirm Booking
                        </button>
                    </div>
                    
                    <a href="/home" class="block mt-4 text-center text-gray-400 hover:text-white transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
            function toggleSchedule() {
                const isScheduled = document.querySelector('input[name="ride_type"]:checked').value === 'scheduled';
                const container = document.getElementById('scheduled_time_container');
                const input = document.getElementById('scheduled_time');
                if (isScheduled) {
                    container.classList.remove('hidden');
                    input.required = true;
                } else {
                    container.classList.add('hidden');
                    input.required = false;
                    input.value = ''; // clear value
                }
                
                // Update summary continuously if ride type changes when on step 3
                updateSummary();
            }

            const pickupInput = document.getElementById('pickup');
            const radiusInput = document.getElementById('pickup_radius');
            const dynamicContainer = document.getElementById('dynamic_pickup_container');
            const pointsList = document.getElementById('suggested_points_list');

            function generateSuggestedPoints() {
                const pickupValue = pickupInput.value.trim();
                const radiusValue = radiusInput.value;
                const oldFinalPoint = "{!! old('final_pickup_point') !!}";

                if (pickupValue === '' || radiusValue === '') {
                    dynamicContainer.classList.add('hidden');
                    return;
                }

                // Generate points
                const points = [
                    `Main Road near ${pickupValue}`,
                    `Landmark near ${pickupValue}`,
                    `Corner Point of ${pickupValue}`
                ];

                let html = '';
                points.forEach((point) => {
                    const checked = oldFinalPoint === point ? 'checked' : '';
                    html += `
                    <label class="flex items-start text-gray-300 cursor-pointer bg-gray-900/50 p-4 rounded-md hover:bg-gray-800 transition border border-gray-700">
                        <input type="radio" name="final_pickup_point" value="${point}" required ${checked} class="mt-1 form-radio text-green-500 bg-gray-800 border-gray-600 focus:ring-green-500 mr-3"> 
                        <span class="flex-1 whitespace-normal">${point} <span class="text-sm text-gray-500 block mt-1">Within ${radiusValue}</span></span>
                    </label>`;
                });

                pointsList.innerHTML = html;
                dynamicContainer.classList.remove('hidden');
            }

            pickupInput.addEventListener('input', generateSuggestedPoints);
            radiusInput.addEventListener('change', generateSuggestedPoints);

            let stopCount = {{ count($oldStops) }};
            function addStop() {
                if (stopCount >= 3) return;
                stopCount++;
                
                const container = document.getElementById('stops_container');
                const div = document.createElement('div');
                div.className = "relative animate-fade-in bg-gray-800/30 p-4 rounded-lg border border-gray-700";
                div.innerHTML = `
                    <label class="block text-sm font-medium text-orange-400 mb-2">Stop ${stopCount}</label>
                    <div class="flex items-center gap-3">
                        <input type="text" name="stops[]" required class="block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-base p-3" placeholder="Enter stop location...">
                        <button type="button" onclick="removeStop(this)" class="text-red-500 hover:text-red-400 font-bold p-2 text-2xl" title="Remove Stop">&times;</button>
                    </div>
                `;
                container.appendChild(div);
                
                if (stopCount >= 3) {
                    document.getElementById('add_stop_btn').classList.add('hidden');
                }
            }
            
            function removeStop(btn) {
                btn.parentElement.parentElement.remove();
                stopCount--;
                document.getElementById('add_stop_btn').classList.remove('hidden');
                
                // Renumber tags
                const labels = document.querySelectorAll('#stops_container label');
                labels.forEach((label, index) => {
                    label.innerText = 'Stop ' + (index + 1);
                });
            }

            // --- Multi-step Logic ---
            let currentStep = 1;
            const titles = {
                1: 'Enter Pickup',
                2: 'Enter Drop',
                3: 'Review Details'
            };

            function nextStep(step) {
                // Basic validation before typical next step
                if (step === 2) {
                    if (!pickupInput.checkValidity() || !radiusInput.checkValidity()) {
                        document.getElementById('book_ride_form').reportValidity();
                        return;
                    }
                    const finalPoint = document.querySelector('input[name="final_pickup_point"]:checked');
                    if (!finalPoint) {
                        alert('Please select a suggested pickup point before proceeding.');
                        return;
                    }
                }
                if (step === 3) {
                    if (!document.getElementById('destination').checkValidity()) {
                        document.getElementById('book_ride_form').reportValidity();
                        return;
                    }
                    // check stops
                    const stopsElems = document.querySelectorAll('input[name="stops[]"]');
                    for (let s of stopsElems) {
                        if (!s.checkValidity()) {
                            document.getElementById('book_ride_form').reportValidity();
                            return;
                        }
                    }
                    updateSummary();
                }

                showStep(step);
            }

            function prevStep(step) {
                showStep(step);
            }

            function showStep(step) {
                document.getElementById(`step-${currentStep}`).classList.add('hidden');
                document.getElementById(`indicator-${currentStep}`).classList.remove('text-green-400');
                
                currentStep = step;
                
                document.getElementById(`step-${currentStep}`).classList.remove('hidden');
                
                // Make previous and current indicators green
                for(let i=1; i<=3; i++) {
                    if (i <= currentStep) {
                        document.getElementById(`indicator-${i}`).classList.add('text-green-400');
                    } else {
                        document.getElementById(`indicator-${i}`).classList.remove('text-green-400');
                    }
                }
                
                document.getElementById('step_title').innerText = titles[currentStep];
            }

            function updateSummary() {
                // values
                const pickup = document.getElementById('pickup').value;
                const finalPointElem = document.querySelector('input[name="final_pickup_point"]:checked');
                const finalPoint = finalPointElem ? finalPointElem.value : '-';
                const dest = document.getElementById('destination').value;
                
                const typeRadio = document.querySelector('input[name="ride_type"]:checked');
                const isScheduled = typeRadio && typeRadio.value === 'scheduled';
                const typeStr = isScheduled ? 'Scheduled' : 'Instant (Book Now)';

                const stopsInputs = document.querySelectorAll('input[name="stops[]"]');
                let stopsArr = [];
                stopsInputs.forEach(i => { if(i.value.trim() !== '') stopsArr.push(i.value.trim()); });
                
                document.getElementById('summary_pickup').innerText = pickup;
                document.getElementById('summary_final_pickup').innerText = finalPoint;
                document.getElementById('summary_drop').innerText = dest;
                document.getElementById('summary_type').innerText = typeStr;

                if (stopsArr.length > 0) {
                    document.getElementById('summary_stops_container').classList.remove('hidden');
                    document.getElementById('summary_stops').innerText = stopsArr.join(', ');
                } else {
                    document.getElementById('summary_stops_container').classList.add('hidden');
                }
            }

            // On Load
            document.addEventListener('DOMContentLoaded', function() {
                toggleSchedule();
                generateSuggestedPoints();
                
                // If there are validation errors, we might want to stay on the step with the error
                @if($errors->has('destination') || $errors->has('stops.*'))
                    showStep(2);
                @elseif($errors->has('ride_type') || $errors->has('scheduled_time'))
                    updateSummary();
                    showStep(3);
                @else
                    showStep(1); // Default
                @endif
            });
            
            // Listen to ride_type changes to update summary dynamically
            document.querySelectorAll('input[name="ride_type"]').forEach(radio => {
                radio.addEventListener('change', updateSummary);
            });

        </script>
    </div>

</div>

</x-layouts.app>