@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">All Events</h1>

    <!-- Refresh Button -->
    <button onclick="loadEvents(this)" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
        🔄 Refresh
    </button>

    <!-- Event Sections -->
    <div id="eventSections" class="mt-6"></div>

    <script>
        window.eventData = {}; // Stores all event data

        async function loadEvents(e) {
            if (e) $(e).text('🔄 Loading...').attr('disabled', true);

            try {
                const res = await axios.get('/api/events');
                const {
                    today,
                    future,
                    past
                } = res.data;

                const render = (title, events, id) => {
                    if (!events.length) {
                        return `
                        <div class="bg-white p-4 rounded shadow mb-6">
                            <button class="w-full text-left font-bold text-xl mb-3 flex justify-between items-center"
                                onclick="togglePanel('${id}')">
                                ${title}
                                <span id="${id}-icon">➕</span>
                            </button>

                            <div id="${id}" class="hidden">
                                <div class="flex items-center justify-center bg-gray-50 border border-dashed border-gray-300 rounded-lg py-6">
                                    <div class="text-center text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="mx-auto mb-2" width="36" height="36" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="font-medium">No ${title.toLowerCase()} available</p>
                                        <p class="text-sm">Stay tuned — new events will be added soon!</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }

                    const firstThree = events.slice(0, 3);
                    const remaining = events.slice(3);

                    // Store data
                    window.eventData[id] = {
                        firstThree,
                        remaining
                    };

                    const eventHTML = (data) =>
                        data.map(e => `
                        <div class="border-b mb-2 pb-2">
                            <h3 class="font-semibold">${e.title}</h3>
                            <p>${e.description || ''}</p>
                            <small>${e.date} ${e.time || ''} — ${e.location || ''}</small>
                        </div>
                    `).join('');

                    return `
                    <div class="bg-white p-4 rounded shadow mb-6">
                        <button class="w-full text-left font-bold text-xl mb-3 flex justify-between items-center"
                            onclick="togglePanel('${id}')">
                            ${title}
                            <span id="${id}-icon">➕</span>
                        </button>

                        <div id="${id}" class="hidden transition-all duration-300 ease-in-out overflow-hidden">
                            <div id="${id}-list">
                                ${eventHTML(firstThree)}
                            </div>
                            ${remaining.length ? `
                                        <button id="${id}-more" class="text-blue-600 mt-2 hover:underline"
                                            onclick="showMore('${id}')">
                                            Read More
                                        </button>` : ''}
                        </div>
                    </div>`;
                };

                // Render all sections
                document.getElementById('eventSections').innerHTML =
                    render("📅 Today's Events", today, "today") +
                    render("🚀 Future Events", future, "future") +
                    render("⏳ Past Events", past, "past");

                if (e) $(e).text('🔄 Refresh').attr('disabled', false);
            } catch (error) {
                console.error("Error loading events:", error);
                document.getElementById('eventSections').innerHTML =
                    `<p class="text-red-500">Failed to load events. Please try again.</p>`;
            }
        }

        // Toggle open/close for one panel at a time
        function togglePanel(id) {
            const allPanels = document.querySelectorAll('#eventSections > div > div[id]');
            const allIcons = document.querySelectorAll('#eventSections span[id$="-icon"]');

            const panel = document.getElementById(id);
            const icon = document.getElementById(`${id}-icon`);
            const list = document.getElementById(`${id}-list`);

            const isHidden = panel.classList.contains('hidden');

            // If open → close it
            if (!isHidden) {
                panel.classList.add('hidden');
                icon.textContent = '➕';
                return;
            }

            // Close all others first
            allPanels.forEach(p => p.classList.add('hidden'));
            allIcons.forEach(ic => ic.textContent = '➕');

            // Open this one
            panel.classList.remove('hidden');
            icon.textContent = '➖';

            // Always reset to first 3 records when opened
            const data = window.eventData[id];
            if (!data) return;

            const eventHTML = (items) =>
                items.map(e => `
                <div class="border-b mb-2 pb-2">
                    <h3 class="font-semibold">${e.title}</h3>
                    <p>${e.description || ''}</p>
                    <small>${e.date} ${e.time || ''} — ${e.location || ''}</small>
                </div>
            `).join('');

            list.innerHTML = eventHTML(data.firstThree);

            // Recreate the "Read More" button if needed
            const existingBtn = document.getElementById(`${id}-more`);
            if (existingBtn) existingBtn.remove();
            if (data.remaining.length) {
                const newBtn = document.createElement('button');
                newBtn.id = `${id}-more`;
                newBtn.textContent = 'Read More';
                newBtn.className = 'text-blue-600 mt-2 hover:underline';
                newBtn.onclick = () => showMore(id);
                panel.appendChild(newBtn);
            }
        }

        // Show all records on "Read More"
        function showMore(id) {
            const data = window.eventData[id];
            const list = document.getElementById(`${id}-list`);
            const moreBtn = document.getElementById(`${id}-more`);

            if (!data) return;

            const eventHTML = (items) =>
                items.map(e => `
                <div class="border-b mb-2 pb-2">
                    <h3 class="font-semibold">${e.title}</h3>
                    <p>${e.description || ''}</p>
                    <small>${e.date} ${e.time || ''} — ${e.location || ''}</small>
                </div>
            `).join('');

            list.insertAdjacentHTML('beforeend', eventHTML(data.remaining));
            if (moreBtn) moreBtn.remove();
        }

        // Initial load
        loadEvents();
    </script>
@endsection
