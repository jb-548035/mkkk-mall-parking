@props(['route', 'defaultRange' => 'today'])

<div class="bg-white rounded-xl p-16 border border-gray-100 mb-24">
    <div class="flex flex-wrap items-center gap-12">
        <div class="flex items-center gap-8">
            <i class="fas fa-calendar-alt text-gray-400"></i>
            <span class="text-sm font-medium text-gray-700">Date Range:</span>
        </div>
        
        <div class="flex flex-wrap gap-8">
            <button onclick="setDateRange('today', '{{ $route }}')" class="px-12 py-6 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition date-btn" data-range="today">
                Today
            </button>
            <button onclick="setDateRange('week', '{{ $route }}')" class="px-12 py-6 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition date-btn" data-range="week">
                Last 7 Days
            </button>
            <button onclick="setDateRange('month', '{{ $route }}')" class="px-12 py-6 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition date-btn" data-range="month">
                This Month
            </button>
            <div class="flex items-center gap-8">
                <input type="date" id="start_date" class="px-12 py-6 border border-gray-300 rounded-lg text-sm">
                <span class="text-gray-400">to</span>
                <input type="date" id="end_date" class="px-12 py-6 border border-gray-300 rounded-lg text-sm">
                <button onclick="applyCustomRange('{{ $route }}')" class="px-16 py-6 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function setDateRange(range, route) {
    let start = '', end = '';
    const today = new Date();
    
    if (range === 'today') {
        start = end = today.toISOString().split('T')[0];
    } else if (range === 'week') {
        end = today.toISOString().split('T')[0];
        start = new Date(today.setDate(today.getDate() - 7)).toISOString().split('T')[0];
    } else if (range === 'month') {
        end = today.toISOString().split('T')[0];
        start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
    }
    
    window.location.href = `${route}?start_date=${start}&end_date=${end}`;
}

function applyCustomRange(route) {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    if (start && end) {
        window.location.href = `${route}?start_date=${start}&end_date=${end}`;
    }
}

// Highlight active filter
document.querySelectorAll('.date-btn').forEach(btn => {
    const urlParams = new URLSearchParams(window.location.search);
    const start = urlParams.get('start_date');
    const end = urlParams.get('end_date');
    const today = new Date().toISOString().split('T')[0];
    
    if (btn.dataset.range === 'today' && !start && !end) {
        btn.classList.add('bg-blue-600', 'text-white');
    }
});
</script>