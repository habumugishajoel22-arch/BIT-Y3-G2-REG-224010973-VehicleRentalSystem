// General front-end behaviour: delete confirmations, live search (Fetch + JSON), cost preview
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ---- 1. Confirm dialogs (works for rows built later by JS too) ----
    document.addEventListener('submit', (event) => {
        const message = event.target.dataset ? event.target.dataset.confirm : null;
        if (message && !window.confirm(message)) event.preventDefault();
    });

    // ---- 2. Live search using the JSON API ----
    const search = document.getElementById('live-search');
    const tbody  = document.getElementById('results');
    if (search && tbody && search.dataset.api) {
        const cols = search.dataset.cols.split(',').map((c) => {
            const [key, label] = c.split(':');
            return { key, label };
        });
        const filter = search.dataset.filter ? document.querySelector(search.dataset.filter) : null;
        const colCount = cols.length + 1;
        let timer = null;
        let controller = null;

        const message = (text) => {
            tbody.innerHTML = '';
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = colCount;
            td.className = 'muted';
            td.textContent = text;
            tr.appendChild(td);
            tbody.appendChild(tr);
        };

        const buildRow = (item) => {
            const tr = document.createElement('tr');
            cols.forEach(({ key, label }) => {
                const td = document.createElement('td');
                td.dataset.label = label;
                if (key === 'status') {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-' + item.status;
                    badge.textContent = item.status;       // textContent = safe against XSS
                    td.appendChild(badge);
                } else if (key === 'daily_rate') {
                    td.textContent = Number(item[key]).toLocaleString('en-US',
                        { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    td.textContent = item[key] ?? '';
                }
                tr.appendChild(td);
            });

            const actions = document.createElement('td');
            actions.className = 'actions';
            const id = item[search.dataset.idField];

            const edit = document.createElement('a');
            edit.textContent = 'Edit';
            edit.href = search.dataset.edit + '&id=' + encodeURIComponent(id);
            actions.appendChild(edit);

            const form = document.createElement('form');
            form.method = 'post';
            form.action = search.dataset.delete;
            form.className = 'inline';
            form.dataset.confirm = 'Delete this ' + search.dataset.noun + '?';
            [['csrf_token', csrf], ['id', id]].forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });
            const btn = document.createElement('button');
            btn.type = 'submit';
            btn.className = 'link-btn danger';
            btn.textContent = 'Delete';
            form.appendChild(btn);
            actions.appendChild(form);

            tr.appendChild(actions);
            return tr;
        };

        const runSearch = async () => {
            if (controller) controller.abort();           // cancel the previous request
            controller = new AbortController();

            const params = new URLSearchParams({ q: search.value.trim() });
            if (filter) params.set('status', filter.value);

            try {
                const response = await fetch(search.dataset.api + '&' + params.toString(),
                    { signal: controller.signal, headers: { 'Accept': 'application/json' } });
                if (response.status === 401) { message('Session expired. Please log in again.'); return; }
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const json = await response.json();

                tbody.innerHTML = '';
                if (!json.data.length) { message('No results found.'); return; }
                json.data.forEach((item) => tbody.appendChild(buildRow(item)));
            } catch (err) {
                if (err.name !== 'AbortError') message('Search failed. Please try again.');
            }
        };

        const schedule = () => { clearTimeout(timer); timer = setTimeout(runSearch, 250); };
        search.addEventListener('input', schedule);
        if (filter) filter.addEventListener('change', runSearch);
    }

    // ---- 3. Rental cost preview ----
    const rentalForm = document.getElementById('rental-form');
    if (rentalForm) {
        const vehicle  = document.getElementById('vehicle_id');
        const start    = document.getElementById('start_date');
        const end      = document.getElementById('end_date');
        const estimate = document.getElementById('estimate');

        const update = () => {
            let rate = parseFloat(rentalForm.dataset.rate || '');
            if (vehicle && vehicle.selectedOptions[0]) rate = parseFloat(vehicle.selectedOptions[0].dataset.rate);
            const s = new Date(start.value), e = new Date(end.value);
            if (!rate || isNaN(s) || isNaN(e) || e < s) { estimate.textContent = '-'; return; }
            const days = Math.max(1, Math.round((e - s) / 86400000));
            estimate.textContent = (rate * days).toLocaleString('en-US',
                { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (' + days + ' day' + (days > 1 ? 's' : '') + ')';
        };
        [vehicle, start, end].forEach((el) => el && el.addEventListener('change', update));
        update();
    }
});
