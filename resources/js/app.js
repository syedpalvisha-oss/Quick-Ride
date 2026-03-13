import './bootstrap';

document.addEventListener('alpine:init', () => {
    const COUNTRY_PHONE_CODES = [
        { name: 'Australia', iso2: 'AU', dialCode: '+61' },
        { name: 'Bangladesh', iso2: 'BD', dialCode: '+880' },
        { name: 'Belgium', iso2: 'BE', dialCode: '+32' },
        { name: 'Brazil', iso2: 'BR', dialCode: '+55' },
        { name: 'Canada', iso2: 'CA', dialCode: '+1' },
        { name: 'China', iso2: 'CN', dialCode: '+86' },
        { name: 'France', iso2: 'FR', dialCode: '+33' },
        { name: 'Germany', iso2: 'DE', dialCode: '+49' },
        { name: 'Hong Kong', iso2: 'HK', dialCode: '+852' },
        { name: 'India', iso2: 'IN', dialCode: '+91' },
        { name: 'Indonesia', iso2: 'ID', dialCode: '+62' },
        { name: 'Ireland', iso2: 'IE', dialCode: '+353' },
        { name: 'Italy', iso2: 'IT', dialCode: '+39' },
        { name: 'Japan', iso2: 'JP', dialCode: '+81' },
        { name: 'Malaysia', iso2: 'MY', dialCode: '+60' },
        { name: 'Mexico', iso2: 'MX', dialCode: '+52' },
        { name: 'Netherlands', iso2: 'NL', dialCode: '+31' },
        { name: 'New Zealand', iso2: 'NZ', dialCode: '+64' },
        { name: 'Nigeria', iso2: 'NG', dialCode: '+234' },
        { name: 'Pakistan', iso2: 'PK', dialCode: '+92' },
        { name: 'Philippines', iso2: 'PH', dialCode: '+63' },
        { name: 'Saudi Arabia', iso2: 'SA', dialCode: '+966' },
        { name: 'Singapore', iso2: 'SG', dialCode: '+65' },
        { name: 'South Africa', iso2: 'ZA', dialCode: '+27' },
        { name: 'South Korea', iso2: 'KR', dialCode: '+82' },
        { name: 'Spain', iso2: 'ES', dialCode: '+34' },
        { name: 'Sweden', iso2: 'SE', dialCode: '+46' },
        { name: 'Switzerland', iso2: 'CH', dialCode: '+41' },
        { name: 'Thailand', iso2: 'TH', dialCode: '+66' },
        { name: 'Turkey', iso2: 'TR', dialCode: '+90' },
        { name: 'United Arab Emirates', iso2: 'AE', dialCode: '+971' },
        { name: 'United Kingdom', iso2: 'GB', dialCode: '+44' },
        { name: 'United States', iso2: 'US', dialCode: '+1' },
        { name: 'Vietnam', iso2: 'VN', dialCode: '+84' },
    ];

    const createCountryPhoneFieldState = () => ({
        countryCode: '+62',
        countryIso2: 'ID',
        countrySearch: '',
        countryPickerOpen: false,
        countryOptions: COUNTRY_PHONE_CODES,

        filteredCountryOptions() {
            const query = String(this.countrySearch ?? '').trim().toLowerCase();
            if (!query) {
                return this.countryOptions;
            }

            const queryDigits = query.replace(/\D/g, '');

            return this.countryOptions.filter((country) => country.name.toLowerCase().includes(query)
                || country.iso2.toLowerCase().includes(query)
                || country.dialCode.includes(query)
                || (queryDigits && country.dialCode.replace(/\D/g, '').includes(queryDigits)));
        },

        selectedCountryOption() {
            return this.countryOptions.find((country) => country.iso2 === this.countryIso2)
                ?? this.countryOptions.find((country) => country.dialCode === this.countryCode)
                ?? null;
        },

        selectedCountryLabel() {
            const selectedCountry = this.selectedCountryOption();

            if (!selectedCountry) {
                return this.countryCode;
            }

            return `${selectedCountry.dialCode} (${selectedCountry.iso2})`;
        },

        selectCountry(country) {
            this.countryCode = country.dialCode;
            this.countryIso2 = country.iso2;
            this.countrySearch = '';
            this.countryPickerOpen = false;
        },

        openCountryPicker() {
            this.countryPickerOpen = true;

            this.$nextTick(() => {
                this.$refs.countrySearchInput?.focus();
            });
        },

        closeCountryPicker() {
            this.countryPickerOpen = false;
            this.countrySearch = '';
        },
    });

    const formatPhoneForApi = (countryCode, phoneNumber) => {
        const rawPhone = String(phoneNumber ?? '').trim();
        if (!rawPhone) {
            return '';
        }

        const normalizedDigits = rawPhone.replace(/\D/g, '');
        if (!normalizedDigits) {
            return '';
        }

        if (rawPhone.startsWith('+')) {
            return `+${normalizedDigits}`;
        }

        const normalizedCountryCode = String(countryCode ?? '').replace(/\D/g, '');

        return normalizedCountryCode
            ? `+${normalizedCountryCode}${normalizedDigits}`
            : `+${normalizedDigits}`;
    };

    const parsePhoneFromApi = (phoneNumber, countryOptions, fallbackIso2 = 'ID') => {
        const defaultCountry = countryOptions.find((country) => country.iso2 === fallbackIso2)
            ?? countryOptions[0]
            ?? { dialCode: '+62', iso2: fallbackIso2 };
        const rawPhone = String(phoneNumber ?? '').trim();
        const normalizedDigits = rawPhone.replace(/\D/g, '');

        if (!rawPhone || !normalizedDigits) {
            return {
                countryCode: defaultCountry.dialCode,
                countryIso2: defaultCountry.iso2,
                localPhone: '',
            };
        }

        if (!rawPhone.startsWith('+')) {
            return {
                countryCode: defaultCountry.dialCode,
                countryIso2: defaultCountry.iso2,
                localPhone: normalizedDigits,
            };
        }

        const countryMatch = countryOptions
            .map((country) => ({
                ...country,
                dialDigits: country.dialCode.replace(/\D/g, ''),
            }))
            .filter((country) => normalizedDigits.startsWith(country.dialDigits))
            .sort((firstCountry, secondCountry) => secondCountry.dialDigits.length - firstCountry.dialDigits.length)[0];

        if (!countryMatch) {
            return {
                countryCode: defaultCountry.dialCode,
                countryIso2: defaultCountry.iso2,
                localPhone: normalizedDigits,
            };
        }

        return {
            countryCode: countryMatch.dialCode,
            countryIso2: countryMatch.iso2,
            localPhone: normalizedDigits.slice(countryMatch.dialDigits.length),
        };
    };

    // ── Login Form ───────────────────────────────────────
    Alpine.data('loginForm', () => Object.assign(createCountryPhoneFieldState(), {
        phone: '',
        email: '',
        password: '',
        useEmail: false,
        errors: {},
        loading: false,

        async submit() {
            this.loading = true;
            this.errors = {};

            try {
                const formattedPhone = formatPhoneForApi(this.countryCode, this.phone);
                const payload = this.useEmail
                    ? { email: this.email, password: this.password }
                    : { phone: formattedPhone, password: this.password };

                const { data } = await axios.post('/api/personal-access-tokens', payload);
                localStorage.setItem('openjek_token', data.data.token);
                window.location.href = '/home';
            } catch (e) {
                if (e.response?.data?.errors) {
                    this.errors = e.response.data.errors;
                } else {
                    this.errors = { general: [e.response?.data?.message || 'Login failed. Please try again.'] };
                }
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Register Form ────────────────────────────────────
    Alpine.data('registerForm', () => Object.assign(createCountryPhoneFieldState(), {
        name: '',
        phone: '',
        email: '',
        password: '',
        password_confirmation: '',
        errors: {},
        loading: false,

        async submit() {
            this.loading = true;
            this.errors = {};

            try {
                const formattedPhone = formatPhoneForApi(this.countryCode, this.phone);

                await axios.post('/api/users', {
                    name: this.name,
                    phone: formattedPhone,
                    email: this.email || undefined,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                // Auto-login
                const { data } = await axios.post('/api/personal-access-tokens', {
                    phone: formattedPhone,
                    password: this.password,
                });
                localStorage.setItem('openjek_token', data.data.token);
                window.location.href = '/home';
            } catch (e) {
                if (e.response?.data?.errors) {
                    this.errors = e.response.data.errors;
                } else {
                    this.errors = { general: [e.response?.data?.message || 'Registration failed. Please try again.'] };
                }
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Profile Form ─────────────────────────────────────
    Alpine.data('profileForm', () => Object.assign(createCountryPhoneFieldState(), {
        token: null,
        user: null,
        name: '',
        phone: '',
        email: '',
        errors: {},
        successMessage: '',
        loading: true,
        saving: false,
        switchModeLoading: false,
        stripeOnboardingLoading: false,
        showVehicleOnboarding: false,
        vehicleLoading: false,
        vehicleErrors: {},
        vehicleForm: {
            code: '',
            vehicle_type: 0,
        },

        async init() {
            this.token = localStorage.getItem('openjek_token');
            if (!this.token) {
                window.location.href = '/login';

                return;
            }

            axios.defaults.headers.common.Authorization = `Bearer ${this.token}`;

            try {
                const { data } = await axios.get('/api/user');
                this.user = data.data;
                this.name = this.user?.name ?? '';
                this.email = this.user?.email ?? '';

                const parsedPhone = parsePhoneFromApi(this.user?.phone, this.countryOptions, this.countryIso2);
                this.countryCode = parsedPhone.countryCode;
                this.countryIso2 = parsedPhone.countryIso2;
                this.phone = parsedPhone.localPhone;
            } catch {
                localStorage.removeItem('openjek_token');
                window.location.href = '/login';

                return;
            } finally {
                this.loading = false;
            }
        },

        isDriverMode() {
            return Boolean(this.user?.vehicle_id);
        },

        vehicleTypeLabel(vehicleType) {
            return Number(vehicleType) === 1 ? 'Car' : 'Motorbike';
        },

        async submit() {
            if (this.loading || this.saving) {
                return;
            }

            this.saving = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const formattedPhone = formatPhoneForApi(this.countryCode, this.phone);
                const { data } = await axios.put('/api/users', {
                    name: this.name,
                    phone: formattedPhone,
                    email: this.email || null,
                });

                this.user = data.data;
                this.name = this.user?.name ?? '';
                this.email = this.user?.email ?? '';

                const parsedPhone = parsePhoneFromApi(this.user?.phone, this.countryOptions, this.countryIso2);
                this.countryCode = parsedPhone.countryCode;
                this.countryIso2 = parsedPhone.countryIso2;
                this.phone = parsedPhone.localPhone;
                this.successMessage = 'Profile updated successfully.';
            } catch (e) {
                if (e.response?.data?.errors) {
                    this.errors = e.response.data.errors;
                } else {
                    this.errors = { general: [e.response?.data?.message || 'Failed to update profile.'] };
                }
            } finally {
                this.saving = false;
            }
        },

        async setActiveVehicle(vehicleId) {
            if (!this.user || !vehicleId || this.switchModeLoading || this.user.vehicle_id === vehicleId) {
                return;
            }

            await this.persistVehicle(vehicleId);
        },

        async switchToRider() {
            if (!this.user || !this.isDriverMode() || this.switchModeLoading) {
                return;
            }

            await this.persistVehicle(null);
        },

        async persistVehicle(vehicleId) {
            this.switchModeLoading = true;
            this.errors = {};
            this.successMessage = '';

            try {
                const { data } = await axios.put('/api/users/mode', { vehicle_id: vehicleId });
                this.user = data.data;
                this.successMessage = vehicleId === null
                    ? 'Switched to rider mode.'
                    : 'Active vehicle updated.';
            } catch (e) {
                this.errors = {
                    general: [e.response?.data?.message || 'Failed to update active vehicle.'],
                };
            } finally {
                this.switchModeLoading = false;
            }
        },

        openVehicleOnboarding() {
            if (this.vehicleLoading) {
                return;
            }

            this.vehicleErrors = {};
            this.vehicleForm = {
                code: '',
                vehicle_type: 0,
            };
            this.showVehicleOnboarding = true;
        },

        closeVehicleOnboarding() {
            if (this.vehicleLoading) {
                return;
            }

            this.showVehicleOnboarding = false;
            this.vehicleErrors = {};
        },

        async submitVehicleOnboarding() {
            if (this.vehicleLoading) {
                return;
            }

            this.vehicleLoading = true;
            this.vehicleErrors = {};
            this.errors = {};
            this.successMessage = '';

            try {
                const { data: response } = await axios.post('/api/vehicles', this.vehicleForm);
                const createdVehicle = response?.data ?? null;
                this.showVehicleOnboarding = false;
                this.vehicleForm = {
                    code: '',
                    vehicle_type: 0,
                };

                const existingVehicles = Array.isArray(this.user?.vehicles)
                    ? this.user.vehicles
                    : [];

                const updatedVehicles = createdVehicle
                    ? [
                        createdVehicle,
                        ...existingVehicles.filter((vehicle) => vehicle.id !== createdVehicle.id),
                    ]
                    : existingVehicles;

                this.user = {
                    ...this.user,
                    vehicles_count: (this.user?.vehicles_count ?? 0) + 1,
                    can_switch_to_driver_mode: true,
                    vehicles: updatedVehicles,
                };

                this.successMessage = 'Vehicle added successfully.';
            } catch (e) {
                if (e.response?.data?.errors) {
                    this.vehicleErrors = e.response.data.errors;
                } else {
                    this.vehicleErrors = {
                        general: [e.response?.data?.message || 'Failed to save vehicle.'],
                    };
                }
            } finally {
                this.vehicleLoading = false;
            }
        },

        async startStripeOnboarding() {
            if (this.stripeOnboardingLoading) {
                return;
            }

            this.stripeOnboardingLoading = true;
            this.errors = {};

            try {
                const { data } = await axios.post('/api/driver/stripe/onboarding-link');
                window.location.href = data.data.url;
            } catch (e) {
                const stripeMessage = e.response?.data?.errors?.stripe?.[0]
                    || e.response?.data?.errors?.vehicles?.[0]
                    || e.response?.data?.message
                    || 'Failed to start Stripe onboarding.';

                this.errors = {
                    general: [stripeMessage],
                };
            } finally {
                this.stripeOnboardingLoading = false;
            }
        },

        async logout() {
            if (!window.confirm('Are you sure you want to log out?')) {
                return;
            }

            try {
                await axios.delete('/api/personal-access-token');
            } catch {}

            localStorage.removeItem('openjek_token');
            window.location.href = '/';
        },
    }));

    // ── Dashboard ────────────────────────────────────────
    Alpine.data('dashboard', () => ({
        // Auth
        token: null,
        user: null,
        loading: true,
        switchModeLoading: false,
        showVehiclePicker: false,
        showVehicleOnboarding: false,
        vehicleLoading: false,
        vehiclePickerVehicleId: null,
        vehicleErrors: {},
        vehicleForm: {
            code: '',
            vehicle_type: 0,
        },
        driverOrders: [],
        incomingOrders: [],
        driverOrdersLoading: false,
        incomingOrdersLoading: false,
        matchingOrderUuid: null,

        // Booking state
        step: 'idle', // idle, selectPickup, selectDropoff, selectVehicle, searching, active, completed
        pickupSearch: '',
        dropoffSearch: '',
        searchResults: [],
        searchDebounce: null,
        showResults: false,

        // Locations
        pickup: { lat: null, lng: null, address: '' },
        dropoff: { lat: null, lng: null, address: '' },

        // Center pin geocoding
        centerAddress: '',
        geocodeTimer: null,
        mapMoving: false,

        // Vehicle & fare
        vehicleType: null,
        fareEstimates: [],
        fareLoading: false,

        // Active ride
        activeOrder: null,
        pollTimer: null,
        searchTimer: null,
        searchTimedOut: false,
        searchElapsed: 0,
        searchCountdown: null,

        // Map references
        map: null,
        pickupMarker: null,
        dropoffMarker: null,
        routeLine: null,

        async init() {
            this.token = localStorage.getItem('openjek_token');
            if (!this.token) {
                window.location.href = '/login';
                return;
            }

            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

            try {
                const { data } = await axios.get('/api/user');
                this.user = data.data;
            } catch {
                localStorage.removeItem('openjek_token');
                window.location.href = '/login';
                return;
            }

            this.loading = false;

            this.$nextTick(() => {
                this.initMap();
                this.checkActiveOrders();
            });
        },

        async switchToRider() {
            if (!this.user || !this.isDriverMode() || this.switchModeLoading) {
                return;
            }

            await this.persistVehicle(null);
        },

        async switchToDriver() {
            if (!this.user || this.switchModeLoading) {
                return;
            }

            if (!this.user.can_switch_to_driver_mode) {
                this.openVehicleOnboarding();
                return;
            }

            const fallbackVehicleId = this.user?.vehicles?.[0]?.id;
            if (!fallbackVehicleId) {
                this.openVehicleOnboarding();
                return;
            }

            this.openVehiclePicker();
        },

        async persistVehicle(vehicleId) {
            this.switchModeLoading = true;
            try {
                const { data } = await axios.put('/api/users/mode', { vehicle_id: vehicleId });
                this.user = data.data;

                if (vehicleId !== null) {
                    this.cancelBooking();
                    await this.refreshDriverOrderFeeds();
                } else {
                    await this.checkActiveOrders();
                }
            } catch (e) {
                alert(e.response?.data?.message || 'Failed to update active vehicle.');
            } finally {
                this.switchModeLoading = false;
            }
        },

        openVehiclePicker() {
            if (!this.user || this.switchModeLoading) {
                return;
            }

            const fallbackVehicleId = this.user?.vehicles?.[0]?.id ?? null;
            this.vehiclePickerVehicleId = this.user?.vehicle_id ?? fallbackVehicleId;
            this.showVehiclePicker = true;
        },

        closeVehiclePicker() {
            if (this.switchModeLoading) {
                return;
            }

            this.showVehiclePicker = false;
        },

        async confirmVehiclePicker() {
            if (!this.vehiclePickerVehicleId || this.switchModeLoading) {
                return;
            }

            if (this.isDriverMode() && this.user?.vehicle_id === this.vehiclePickerVehicleId) {
                this.closeVehiclePicker();

                return;
            }

            await this.persistVehicle(this.vehiclePickerVehicleId);
            this.closeVehiclePicker();
        },

        openVehicleOnboarding() {
            if (this.vehicleLoading || this.switchModeLoading) {
                return;
            }

            this.vehicleErrors = {};
            this.vehicleForm = {
                code: '',
                vehicle_type: 0,
            };
            this.showVehicleOnboarding = true;
        },

        closeVehicleOnboarding() {
            if (this.vehicleLoading || this.switchModeLoading) {
                return;
            }

            this.showVehicleOnboarding = false;
            this.vehicleErrors = {};
        },

        async submitVehicleOnboarding() {
            if (this.vehicleLoading || this.switchModeLoading) {
                return;
            }

            this.vehicleLoading = true;
            this.vehicleErrors = {};

            try {
                const { data: response } = await axios.post('/api/vehicles', this.vehicleForm);
                const createdVehicle = response?.data ?? null;

                if (!createdVehicle?.id) {
                    throw new Error('Failed to save vehicle.');
                }

                this.showVehicleOnboarding = false;
                this.vehicleForm = {
                    code: '',
                    vehicle_type: 0,
                };

                await this.persistVehicle(createdVehicle.id);
            } catch (e) {
                if (e.response?.data?.errors) {
                    this.vehicleErrors = e.response.data.errors;
                } else {
                    this.vehicleErrors = {
                        general: [e.response?.data?.message || 'Failed to save vehicle.'],
                    };
                }
            } finally {
                this.vehicleLoading = false;
            }
        },

        initMap() {
            const mapEl = document.getElementById('map');
            if (!mapEl || !window.L) return;

            this.map = L.map(mapEl, {
                center: [-6.2088, 106.8456],
                zoom: 13,
                zoomControl: false,
            });

            L.control.zoom({ position: 'topright' }).addTo(this.map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
                maxZoom: 19,
            }).addTo(this.map);

            // Try user geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => this.map.setView([pos.coords.latitude, pos.coords.longitude], 15),
                    () => {},
                );
            }

            // Geocode map center as user drags
            this.map.on('movestart', () => { this.mapMoving = true; });
            this.map.on('moveend', () => {
                this.mapMoving = false;
                if (this.step === 'selectPickup' || this.step === 'selectDropoff') {
                    this.geocodeMapCenter();
                }
            });
        },

        geocodeMapCenter() {
            clearTimeout(this.geocodeTimer);
            this.centerAddress = 'Loading...';
            this.geocodeTimer = setTimeout(async () => {
                const center = this.map.getCenter();
                this.centerAddress = await this.reverseGeocode(center.lat, center.lng);
            }, 400);
        },

        startPickupSelection() {
            this.step = 'selectPickup';
            this.centerAddress = '';
            this.geocodeMapCenter();
        },

        confirmPickup() {
            const center = this.map.getCenter();
            this.pickup = { lat: center.lat, lng: center.lng, address: this.centerAddress || 'Selected location' };

            // Place permanent marker
            if (this.pickupMarker) this.map.removeLayer(this.pickupMarker);
            this.pickupMarker = L.marker([center.lat, center.lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div class="marker-dot pickup"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                }),
            }).addTo(this.map);

            this.step = 'selectDropoff';
            this.centerAddress = '';
            this.geocodeMapCenter();
        },

        confirmDropoff() {
            const center = this.map.getCenter();
            this.dropoff = { lat: center.lat, lng: center.lng, address: this.centerAddress || 'Selected location' };

            // Place permanent marker
            if (this.dropoffMarker) this.map.removeLayer(this.dropoffMarker);
            this.dropoffMarker = L.marker([center.lat, center.lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div class="marker-dot dropoff"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                }),
            }).addTo(this.map);

            this.centerAddress = '';
            this.drawRoute();
            this.step = 'selectVehicle';
            this.fetchFares();
        },

        drawRoute() {
            if (this.routeLine) this.map.removeLayer(this.routeLine);
            if (this.pickup.lat && this.dropoff.lat) {
                this.routeLine = L.polyline(
                    [[this.pickup.lat, this.pickup.lng], [this.dropoff.lat, this.dropoff.lng]],
                    { color: '#c8ff00', weight: 3, dashArray: '8, 12', opacity: 0.6 },
                ).addTo(this.map);
                this.map.fitBounds(this.routeLine.getBounds(), { padding: [80, 80] });
            }
        },

        async reverseGeocode(lat, lng) {
            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=17`,
                );
                const data = await res.json();
                return data.display_name?.split(',').slice(0, 3).join(',').trim() || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            } catch {
                return `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            }
        },

        onSearchInput(query) {
            clearTimeout(this.searchDebounce);
            if (!query || query.trim().length < 3) {
                this.searchResults = [];
                this.showResults = false;
                return;
            }
            this.searchDebounce = setTimeout(() => this.fetchSearchResults(query.trim()), 350);
        },

        async fetchSearchResults(query) {
            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=5&addressdetails=1`,
                );
                this.searchResults = await res.json();
                this.showResults = this.searchResults.length > 0;
            } catch {
                this.searchResults = [];
                this.showResults = false;
            }
        },

        selectSearchResult(result) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            const address = result.display_name?.split(',').slice(0, 3).join(',').trim() || result.display_name;

            if (this.step === 'idle' || this.step === 'selectPickup') {
                this.pickup = { lat, lng, address };
                this.pickupSearch = address;

                if (this.pickupMarker) this.map.removeLayer(this.pickupMarker);
                this.pickupMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="marker-dot pickup"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                    }),
                }).addTo(this.map);

                this.map.setView([lat, lng], 16);
                this.step = 'selectDropoff';
                this.centerAddress = '';
                this.$nextTick(() => this.geocodeMapCenter());
            } else if (this.step === 'selectDropoff') {
                this.dropoff = { lat, lng, address };
                this.dropoffSearch = address;

                if (this.dropoffMarker) this.map.removeLayer(this.dropoffMarker);
                this.dropoffMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="marker-dot dropoff"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                    }),
                }).addTo(this.map);

                this.drawRoute();
                this.step = 'selectVehicle';
                this.fetchFares();
            }

            this.searchResults = [];
            this.showResults = false;
        },

        clearSearch() {
            this.searchResults = [];
            this.showResults = false;
            clearTimeout(this.searchDebounce);
        },

        async fetchFares() {
            this.fareLoading = true;
            try {
                const params = new URLSearchParams({
                    'pickup_location[0]': this.pickup.lat,
                    'pickup_location[1]': this.pickup.lng,
                    'dropoff_location[0]': this.dropoff.lat,
                    'dropoff_location[1]': this.dropoff.lng,
                    currency_id: 'IDR',
                });
                const { data } = await axios.get(`/api/calculate-fare?${params}`);
                this.fareEstimates = data.data || [];
            } catch {
                this.fareEstimates = [];
            } finally {
                this.fareLoading = false;
            }
        },

        selectVehicle(type) {
            this.vehicleType = type;
        },

        async bookRide() {
            this.step = 'searching';
            this.searchTimedOut = false;
            this.searchElapsed = 0;
            try {
                const { data } = await axios.post('/api/orders', {
                    vehicle_type: this.vehicleType,
                    pickup_location: [this.pickup.lat, this.pickup.lng],
                    dropoff_location: [this.dropoff.lat, this.dropoff.lng],
                });
                this.activeOrder = data.data;
                this.step = 'active';
                this.startPolling();
                this.startSearchTimeout();
            } catch (e) {
                alert(e.response?.data?.message || 'Failed to book ride. Please try again.');
                this.step = 'selectVehicle';
            }
        },

        startSearchTimeout() {
            this.stopSearchTimeout();
            const TIMEOUT_SECONDS = 120;
            this.searchElapsed = 0;
            this.searchTimedOut = false;

            this.searchCountdown = setInterval(() => {
                this.searchElapsed++;
                if (this.searchElapsed >= TIMEOUT_SECONDS && !this.activeOrder?.matched_at) {
                    this.searchTimedOut = true;
                    this.stopSearchTimeout();
                }
            }, 1000);
        },

        stopSearchTimeout() {
            if (this.searchCountdown) {
                clearInterval(this.searchCountdown);
                this.searchCountdown = null;
            }
        },

        keepWaiting() {
            this.searchTimedOut = false;
            this.startSearchTimeout();
        },

        async cancelRide() {
            if (!this.activeOrder) return;

            if (!window.confirm('Are you sure you want to cancel this ride?')) {
                return;
            }

            try {
                await axios.post(`/api/orders/${this.activeOrder.uuid}/cancel`);
                this.activeOrder = { ...this.activeOrder, cancelled_at: new Date().toISOString() };
                this.step = 'completed';
                this.stopPolling();
            } catch (e) {
                alert(e.response?.data?.message || 'Failed to cancel ride.');
            }
        },

        startPolling() {
            this.stopPolling();
            this.pollTimer = setInterval(() => this.pollOrderStatus(), 5000);
        },

        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },

        async pollOrderStatus() {
            if (!this.activeOrder) return;
            try {
                const { data } = await axios.get(`/api/orders/${this.activeOrder.uuid}`);
                this.activeOrder = data.data;

                if (this.activeOrder.matched_at) {
                    this.searchTimedOut = false;
                    this.stopSearchTimeout();
                }

                if (this.activeOrder.completed_at || this.activeOrder.cancelled_at || this.activeOrder.driver_cancelled_at) {
                    this.step = 'completed';
                    this.stopPolling();
                    this.stopSearchTimeout();
                }
            } catch {
                // silent fail
            }
        },

        isDriverMode() {
            return Boolean(this.user?.vehicle_id);
        },

        isOrderFinished(order) {
            return Boolean(order?.completed_at || order?.cancelled_at || order?.driver_cancelled_at);
        },

        isOrderActive(order) {
            return Boolean(order && !this.isOrderFinished(order));
        },

        driverOrderStatus(order) {
            if (order?.completed_at) {
                return 'Completed';
            }

            if (order?.cancelled_at) {
                return 'Cancelled by rider';
            }

            if (order?.driver_cancelled_at) {
                return 'Cancelled by driver';
            }

            if (order?.pickup_at) {
                return 'In trip';
            }

            if (order?.matched_at) {
                return 'Matched';
            }

            return 'Incoming';
        },

        driverOrderStatusClass(order) {
            if (order?.completed_at) {
                return 'bg-emerald-400/10 text-emerald-300 border-emerald-400/20';
            }

            if (order?.cancelled_at || order?.driver_cancelled_at) {
                return 'bg-red-400/10 text-red-300 border-red-400/20';
            }

            if (order?.pickup_at || order?.matched_at) {
                return 'bg-neon-400/10 text-neon-400 border-neon-400/20';
            }

            return 'bg-void-700/50 text-void-200 border-void-600';
        },

        orderVehicleLabel(order) {
            return Number(order?.vehicle_type) === 1 ? 'Car' : 'Motorbike';
        },

        vehicleTypeLabel(vehicleType) {
            return Number(vehicleType) === 1 ? 'Car' : 'Motorbike';
        },

        shortOrderUuid(order) {
            const uuid = String(order?.uuid ?? '');

            return uuid ? `${uuid.slice(0, 8)}...` : '';
        },

        async refreshDriverOrderFeeds() {
            if (!this.isDriverMode()) {
                this.driverOrders = [];
                this.incomingOrders = [];

                return;
            }

            await Promise.all([
                this.fetchDriverOrders(),
                this.fetchIncomingOrders(),
            ]);
        },

        async fetchDriverOrders() {
            this.driverOrdersLoading = true;

            try {
                const { data } = await axios.get('/api/orders', {
                    params: { role: 'driver' },
                });

                this.driverOrders = data.data || [];
            } catch {
                this.driverOrders = [];
            } finally {
                this.driverOrdersLoading = false;
            }
        },

        async fetchIncomingOrders() {
            this.incomingOrdersLoading = true;

            try {
                const { data } = await axios.get('/api/orders', {
                    params: { role: 'driver_incoming' },
                });

                this.incomingOrders = data.data || [];
            } catch {
                this.incomingOrders = [];
            } finally {
                this.incomingOrdersLoading = false;
            }
        },

        async matchIncomingOrder(order) {
            if (!order?.uuid || this.matchingOrderUuid) {
                return;
            }

            this.matchingOrderUuid = order.uuid;

            try {
                await axios.post(`/api/orders/${order.uuid}/match`);
                await this.refreshDriverOrderFeeds();
            } catch (e) {
                alert(e.response?.data?.message || 'Failed to match order.');
            } finally {
                this.matchingOrderUuid = null;
            }
        },

        async focusOrderOnMap(order) {
            if (!order?.pickup_location || !order?.dropoff_location) {
                return;
            }

            this.pickup = {
                lat: order.pickup_location[0],
                lng: order.pickup_location[1],
                address: '',
            };
            this.dropoff = {
                lat: order.dropoff_location[0],
                lng: order.dropoff_location[1],
                address: '',
            };

            this.setMarkerFromCoords('pickup', this.pickup.lat, this.pickup.lng);
            this.setMarkerFromCoords('dropoff', this.dropoff.lat, this.dropoff.lng);
            this.drawRoute();

            this.pickup.address = await this.reverseGeocode(this.pickup.lat, this.pickup.lng);
            this.dropoff.address = await this.reverseGeocode(this.dropoff.lat, this.dropoff.lng);
        },

        async checkActiveOrders() {
            if (this.isDriverMode()) {
                await this.refreshDriverOrderFeeds();

                return;
            }

            try {
                const { data } = await axios.get('/api/orders');
                const orders = data.data || [];
                const active = orders.find(
                    (o) => !o.completed_at && !o.cancelled_at && !o.driver_cancelled_at,
                );
                if (active) {
                    this.activeOrder = active;
                    this.step = 'active';

                    if (active.pickup_location) {
                        this.pickup = { lat: active.pickup_location[0], lng: active.pickup_location[1], address: '' };
                        this.setMarkerFromCoords('pickup', this.pickup.lat, this.pickup.lng);
                        this.pickup.address = await this.reverseGeocode(this.pickup.lat, this.pickup.lng);
                    }
                    if (active.dropoff_location) {
                        this.dropoff = { lat: active.dropoff_location[0], lng: active.dropoff_location[1], address: '' };
                        this.setMarkerFromCoords('dropoff', this.dropoff.lat, this.dropoff.lng);
                        this.dropoff.address = await this.reverseGeocode(this.dropoff.lat, this.dropoff.lng);
                    }
                    this.drawRoute();
                    this.startPolling();
                }
            } catch {
                // silent fail
            }
        },

        setMarkerFromCoords(type, lat, lng) {
            if (!this.map) return;
            const isPickup = type === 'pickup';
            const markerKey = isPickup ? 'pickupMarker' : 'dropoffMarker';

            if (this[markerKey]) this.map.removeLayer(this[markerKey]);
            this[markerKey] = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div class="marker-dot ${isPickup ? 'pickup' : 'dropoff'}"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                }),
            }).addTo(this.map);
        },

        cancelBooking() {
            this.step = 'idle';
            this.pickup = { lat: null, lng: null, address: '' };
            this.dropoff = { lat: null, lng: null, address: '' };
            this.vehicleType = null;
            this.fareEstimates = [];
            this.activeOrder = null;
            this.pickupSearch = '';
            this.dropoffSearch = '';
            this.centerAddress = '';
            this.searchResults = [];
            this.showResults = false;
            this.searchTimedOut = false;
            this.searchElapsed = 0;
            this.matchingOrderUuid = null;
            this.stopPolling();
            this.stopSearchTimeout();
            clearTimeout(this.geocodeTimer);

            if (this.pickupMarker) { this.map.removeLayer(this.pickupMarker); this.pickupMarker = null; }
            if (this.dropoffMarker) { this.map.removeLayer(this.dropoffMarker); this.dropoffMarker = null; }
            if (this.routeLine) { this.map.removeLayer(this.routeLine); this.routeLine = null; }
        },

        getStatusText() {
            if (!this.activeOrder) return '';
            if (this.activeOrder.pickup_at) return 'Enjoy your ride!';
            if (this.activeOrder.matched_at) return 'Driver on the way!';
            return 'Looking for a driver...';
        },

        async logout() {
            if (!window.confirm('Are you sure you want to log out?')) {
                return;
            }

            try { await axios.delete('/api/personal-access-token'); } catch {}
            localStorage.removeItem('openjek_token');
            window.location.href = '/';
        },

        formatCurrency(amount, currency) {
            try {
                const locale = currency === 'IDR' ? 'id-ID' : 'en-US';
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency || 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                }).format(amount);
            } catch {
                return `${currency} ${amount}`;
            }
        },
    }));
});
