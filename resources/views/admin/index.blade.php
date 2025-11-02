@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Admin Panel — Manage Events</h1>

    <!-- Event Form -->
    <form id="eventForm" class="mb-6 bg-white p-4 rounded shadow relative">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <!-- Title -->
            <div class="relative">
                <input type="text" name="title" id="title" placeholder="Event Title"
                    class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <div id="titleError" class="hidden mt-1 text-red-600 text-sm flex items-center gap-2">
                    <i class="fas fa-circle-exclamation"></i>
                    <span></span>
                </div>
            </div>

            <!-- Date -->
            <div class="relative">
                <input type="date" name="date" id="date"
                    class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <div id="dateError" class="hidden mt-1 text-red-600 text-sm flex items-center gap-2">
                    <i class="fas fa-circle-exclamation"></i>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Time -->
        <div class="mt-3 relative">
            <input type="time" name="time" id="time"
                class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <!-- Location -->
        <div class="mt-3 relative">
            <input type="text" name="location" id="location" placeholder="Location"
                class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>

        <!-- Description -->
        <div class="mt-3 relative">
            <textarea name="description" id="description" placeholder="Description"
                class="w-full mt-2 p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition"></textarea>
        </div>

        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Add Event
        </button>
    </form>

    <!-- Events Table -->
    <table id="eventsTable" class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">Title</th>
                <th class="p-2 text-left">Date</th>
                <th class="p-2 text-left">Time</th>
                <th class="p-2 text-left">Location</th>
                <th class="p-2 text-left">Category</th>
                <th class="p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody id="eventBody">
            <tr>
                <td colspan="5" class="text-center p-3 text-gray-500">Loading events...</td>
            </tr>
        </tbody>
    </table>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        document.addEventListener("DOMContentLoaded", () => {
            const eventForm = document.getElementById("eventForm");
            const eventBody = document.getElementById("eventBody");

            // Fetch all events
            const loadEvents = async () => {
                eventBody.innerHTML =
                    `<tr><td colspan="6" class="text-center p-3 text-gray-500">Loading events...</td></tr>`;

                try {
                    const response = await axios.get("/api/events");
                    const {
                        today,
                        future,
                        past
                    } = response.data;

                    // Merge all categories together for admin listing
                    const allEvents = [
                        ...today.map(e => ({
                            ...e,
                            category: "Today"
                        })),
                        ...future.map(e => ({
                            ...e,
                            category: "Future"
                        })),
                        ...past.map(e => ({
                            ...e,
                            category: "Past"
                        })),
                    ];

                    if (!allEvents.length) {
                        eventBody.innerHTML =
                            `<tr><td colspan="6" class="text-center p-3 text-gray-500">No events found.</td></tr>`;
                        return;
                    }

                    eventBody.innerHTML = "";
                    allEvents.forEach(event => {
                        eventBody.innerHTML += `
                        <tr class="border-t">
                            <td class="p-2 font-semibold">${event.title}</td>
                            <td class="p-2">${event.date}</td>
                            <td class="p-2">${event.time ?? '-'}</td>
                            <td class="p-2">${event.location ?? '-'}</td>
                            <td class="p-2">${event.category ?? '-'}</td>
                            <td class="p-2 space-x-3">
                                <a href="/events/${event.id}/edit" class="text-blue-600 hover:underline">Edit</a>
                                <button onclick="deleteEvent(${event.id})" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>`;
                    });
                } catch (error) {
                    eventBody.innerHTML =
                        `<tr><td colspan="6" class="text-center p-3 text-red-500">Failed to load events.</td></tr>`;
                }
            };

            const form = document.getElementById("eventForm");
            const title = document.getElementById("title");
            const date = document.getElementById("date");
            const titleError = document.getElementById("titleError");
            const dateError = document.getElementById("dateError");
            const submitBtn = form.querySelector('button[type="submit"]');

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

            // Validate date
            function validateDate() {
                const value = date.value.trim();

                if (!value) {
                    showError(date, dateError, "This field is required.");
                    return false;
                }

                try {
                    const parsedDate = dateFns.parse(value, "yyyy-mm-dd", new Date());
                    const year = parsedDate.getFullYear();
                    if (year < 1950) {
                        date.value = "1950-01-01";
                        showError(date, dateError, "Year cannot be earlier than 1950. Defaulted to 1950-01-01.");
                        return false;
                    }

                    hideError(date, dateError);
                    return true;
                } catch (err) {
                    showError(date, dateError, "Invalid date format. Use YYYY-MM-DD.");
                    return false;
                }
            }

            // Real-time validation
            title.addEventListener("input", validateTitle);
            date.addEventListener("focusout", validateDate);

            // Form submit
            form.addEventListener("submit", async (e) => {
                e.preventDefault();

                const isTitleValid = validateTitle();
                const isDateValid = validateDate();

                if (!isTitleValid || !isDateValid) {
                    submitBtn.textContent = "Add Event"; // reset text
                    submitBtn.disabled = false;
                    return;
                }

                // Disable button and show loading text
                submitBtn.textContent = "Adding...";
                submitBtn.disabled = true;

                const data = {
                    title: title.value.trim(),
                    description: document.getElementById("description").value.trim(),
                    date: date.value,
                    time: document.getElementById("time").value,
                    location: document.getElementById("location").value,
                };

                try {
                    await axios.post("/api/events", data);

                    Swal.fire("✅ Success!", "Event added successfully!", "success");

                    form.reset();
                    hideError(title, titleError);
                    hideError(date, dateError);

                    // Optionally reload event list if function exists
                    if (typeof loadEvents === "function") loadEvents();

                } catch (error) {
                    Swal.fire("❌ Error", "Failed to add event.", "error");
                } finally {
                    // Re-enable button after success/error
                    submitBtn.textContent = "Add Event";
                    submitBtn.disabled = false;
                }
            });

            // Delete Event
            window.deleteEvent = async (id) => {
                Swal.fire({
                    title: "Are you sure?",
                    text: "This event will be permanently deleted.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`/api/events/${id}`);
                            Swal.fire("Deleted!", "Event has been deleted.", "success");
                            loadEvents(); // reload after delete
                        } catch (error) {
                            Swal.fire("Error", "Failed to delete event.", "error");
                        }
                    }
                });
            };

            loadEvents(); // Load events on page load
        });
    </script>
@endsection
