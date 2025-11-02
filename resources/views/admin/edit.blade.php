@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Event</h1>

    <!-- Success message container -->
    <div id="alertContainer" class="mb-4"></div>

    <form id="updateForm" class="bg-white p-6 rounded shadow">
        @csrf
        @method('put')

        <div class="grid grid-cols-2 gap-4">
            <!-- Title -->
            <div class="relative">
                <input type="text" name="title" id="title"
                    class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <div id="titleError" class="hidden mt-1 text-red-600 text-sm flex items-center gap-2">
                    <i class="fas fa-circle-exclamation"></i>
                    <span></span>
                </div>
            </div>

            <!-- Date -->
            <div class="relative">
                <input type="date" name="date" id="date" placeholder="dd-mm-YYYY"
                    class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <div id="dateError" class="hidden mt-1 text-red-600 text-sm flex items-center gap-2">
                    <i class="fas fa-circle-exclamation"></i>
                    <span></span>
                </div>
            </div>

            <!-- Time -->
            <div>
                <input type="time" name="time" class="w-full p-2 border rounded">
            </div>

            <!-- Location -->
            <div>
                <input type="text" name="location" class="w-full p-2 border rounded">
            </div>
        </div>

        <!-- Description -->
        <div class="mt-4">
            <textarea name="description" class="w-full p-2 border rounded"></textarea>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Event</button>
            <a href="/admin" class="ml-3 bg-gray-400 text-white px-4 py-2 rounded">Cancel</a>
        </div>
    </form>

    <script>
        // Add CSRF token for Laravel
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        const id = "{{ request()->route('id') }}";

        // Get elements
        const form = document.getElementById("updateForm");
        const title = document.getElementById("title");
        const date = document.getElementById("date");
        const titleError = document.getElementById("titleError");
        const dateError = document.getElementById("dateError");

        // Show error
        function showError(field, errorElement, message) {
            field.classList.add("border-red-500");
            errorElement.classList.remove("hidden");
            errorElement.querySelector("span").textContent = message;
        }

        // Hide error
        function hideError(field, errorElement) {
            field.classList.remove("border-red-500");
            errorElement.classList.add("hidden");
        }

        // Validate title
        function validateTitle() {
            const value = title.value.trim();
            if (!value) {
                showError(title, titleError, "Event title field is required.");
                return false;
            } else if (value.length < 3) {
                showError(title, titleError, "Please enter a valid event title. Minimum 3 characters.");
                return false;
            }
            hideError(title, titleError);
            return true;
        }

        // Validate date using date-fns
        function validateDate() {
            const value = date.value.trim();

            if (!value) {
                showError(date, dateError, "This field is required.");
                return false;
            }

            try {
                const parsedDate = dateFns.parseISO(value);
                if (!dateFns.isValid(parsedDate)) {
                    showError(date, dateError, "Please enter a valid date in YYYY-MM-DD format.");
                    return false;
                }

                const year = parsedDate.getFullYear();
                if (year < 1950) {
                    const correctedDate = "1950-01-01";
                    date.value = correctedDate;
                    showError(date, dateError, "Year cannot be earlier than 1950. Defaulted to 1950-01-01.");
                    return false;
                }

                hideError(date, dateError);
                return true;
            } catch {
                showError(date, dateError, "Invalid date format.");
                return false;
            }
        }

        // Real-time validation
        title.addEventListener("input", validateTitle);
        date.addEventListener("input", validateDate);

        // Fetch event details from API
        async function loadEvent() {
            try {
                const res = await axios.get(`/api/events/${id}`);
                const event = res.data;

                title.value = event.title;
                document.querySelector('[name="description"]').value = event.description || '';
                date.value = event.date;
                document.querySelector('[name="time"]').value = event.time || '';
                document.querySelector('[name="location"]').value = event.location || '';
            } catch (error) {
                console.error(error);
                showAlert('danger', 'Failed to load event data.');
            }
        }

        // Handle form submission
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            // Validate before sending
            const validTitle = validateTitle();
            const validDate = validateDate();

            if (!validTitle || !validDate) {
                return;
            }

            const data = {
                title: title.value,
                description: document.querySelector('[name="description"]').value,
                date: date.value,
                time: document.querySelector('[name="time"]').value,
                location: document.querySelector('[name="location"]').value,
            };

            try {
                await axios.put(`/api/events/${id}`, data);
                showAlert('success', 'Event updated successfully! 🎉');
                setTimeout(() => window.location.href = '/admin', 1500);
            } catch (error) {
                console.error(error);
                showAlert('danger', 'Failed to update event.');
            }
        });

        // Bootstrap-style alert
        function showAlert(type, message) {
            const alertHTML = `
                <div class="alert alert-${type} d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="${type === 'success' ? 'Success' : 'Error'}">
                        <use xlink:href="#${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"/>
                    </svg>
                    <div>${message}</div>
                </div>`;
            document.getElementById('alertContainer').innerHTML = alertHTML;
        }

        // Load event data on page load
        loadEvent();
    </script>
@endsection
