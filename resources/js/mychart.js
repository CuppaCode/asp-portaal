import Chart from 'chart.js/auto';
import { TempusDominus } from '@eonasdan/tempus-dominus';

$(document).ready(function () {

    // ─── Bail out if not on the analytics page ────────────────────────────────
    if (!document.getElementById('datetimepicker1')) {
        return;
    }

    // ─── Date picker config ───────────────────────────────────────────────────
    const dpConfig = {
        display: {
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-arrow-up',
                down: 'fa fa-arrow-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check',
                clear: 'fa fa-trash',
                close: 'fa fa-times',
            },
            buttons: { today: true, clear: false, close: true },
            components: { clock: false },
        },
        useCurrent: true,
        localization: {
            locale: 'nl',
            dateFormats: {
                LTS: 'h:mm:ss T', LT: 'h:mm T',
                L: 'dd-MM-yyyy', LL: 'MMMM d, yyyy',
                LLL: 'MMMM d, yyyy h:mm T', LLLL: 'dddd, MMMM d, yyyy h:mm T',
            },
            ordinal: (n) => n,
            format: 'L',
        },
    };

    new TempusDominus(document.getElementById('datetimepicker1'), dpConfig);
    new TempusDominus(document.getElementById('datetimepicker2'), dpConfig);

    // ─── Colours ──────────────────────────────────────────────────────────────
    const BLUE    = 'rgba(52, 74, 155, 0.85)';
    const GREEN   = 'rgba(40, 161, 81, 0.85)';
    const ORANGE  = 'rgba(255, 159, 64, 0.85)';
    const RED     = 'rgba(220, 53, 69, 0.85)';
    const PURPLE  = 'rgba(111, 66, 193, 0.85)';
    const TEAL    = 'rgba(32, 201, 151, 0.85)';
    const YELLOW  = 'rgba(255, 205, 86, 0.85)';
    const GREY    = 'rgba(100, 100, 100, 0.85)';
    const PALETTE = [BLUE, GREEN, ORANGE, RED, PURPLE, TEAL, YELLOW, GREY,
                     'rgba(23,162,184,.85)', 'rgba(253,126,20,.85)',
                     'rgba(102,16,242,.85)', 'rgba(52,58,64,.85)',
                     'rgba(232,62,140,.85)', 'rgba(0,123,255,.85)', 'rgba(40,167,69,.85)'];

    // ─── Chart registry (so we can destroy before redrawing) ─────────────────
    const charts = {};

    function makeOrReplace(id, config) {
        if (charts[id]) {
            charts[id].destroy();
        }
        charts[id] = new Chart(document.getElementById(id), config);
    }

    // ─── Utility: euro formatter ──────────────────────────────────────────────
    function euro(val) {
        return '€ ' + parseFloat(val || 0).toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ─── Utility: build a simple HTML table ──────────────────────────────────
    function buildTable(headers, rows) {
        let html = '<table class="table table-sm table-bordered table-hover mb-0"><thead class="thead-light"><tr>';
        headers.forEach(h => { html += `<th>${h}</th>`; });
        html += '</tr></thead><tbody>';
        if (rows.length === 0) {
            html += `<tr><td colspan="${headers.length}" class="text-center text-muted">Geen gegevens</td></tr>`;
        } else {
            rows.forEach(r => {
                html += '<tr>';
                r.forEach(cell => { html += `<td>${cell}</td>`; });
                html += '</tr>';
            });
        }
        html += '</tbody></table>';
        return html;
    }

    // ─── Period preset buttons ────────────────────────────────────────────────
    function setPresetDates(period) {
        const now   = new Date();
        const year  = now.getFullYear();
        const pad   = n => String(n).padStart(2, '0');
        const fmt   = d => `${pad(d.getDate())}-${pad(d.getMonth() + 1)}-${d.getFullYear()}`;

        let start, end;
        end = fmt(now);

        switch (period) {
            case 'quarterly': {
                const q = Math.floor(now.getMonth() / 3);
                start = fmt(new Date(year, q * 3, 1));
                break;
            }
            case 'halfyearly':
                start = fmt(new Date(year, now.getMonth() < 6 ? 0 : 6, 1));
                break;
            case 'yearly':
                start = fmt(new Date(year, 0, 1));
                break;
            default: // monthly
                start = fmt(new Date(year, now.getMonth(), 1));
        }

        $('#datetimepicker1Input').val(start);
        $('#datetimepicker2Input').val(end);
    }

    // Set default dates for the initial "monthly" preset
    setPresetDates('monthly');

    $('#period-presets').on('click', '.period-btn', function () {
        $('#period-presets .period-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        const period = $(this).data('period');
        $('#selected_period').val(period);
        setPresetDates(period);
    });

    // ─── View toggle (charts ↔ tables) ───────────────────────────────────────
    $('#view-toggle').on('click', 'button', function () {
        $('#view-toggle button').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');

        const view = $(this).data('view');
        if (view === 'charts') {
            $('.chart-area').removeClass('d-none');
            $('.table-area').addClass('d-none');
        } else {
            $('.chart-area').addClass('d-none');
            $('.table-area').removeClass('d-none');
        }
    });

    // ─── Build export URL ─────────────────────────────────────────────────────
    function updateExportLink(company, sdate, edate, period) {
        const params = new URLSearchParams({ company, startdate: sdate, enddate: edate, period });
        $('#btn-export-csv').attr('href', '/admin/analytics/export?' + params.toString()).removeClass('d-none');
    }

    // ─── Main load button ─────────────────────────────────────────────────────
    $('#btn-load-stats').on('click', function () {
        const company = $('#a_company_id').val();
        const sdate   = $('#datetimepicker1Input').val();
        const edate   = $('#datetimepicker2Input').val();
        const period  = $('#selected_period').val();

        if (!company || company === '') {
            alert('Selecteer eerst een bedrijf.');
            return;
        }
        if (!sdate || !edate) {
            alert('Selecteer een geldig datumbereik.');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Laden...');

        $.post('/admin/analytics/get-data', {
            company, startdate: sdate, enddate: edate, period,
            _token: $('meta[name="csrf-token"]').attr('content'),
        })
        .done(function (res) {
            $('#analytics-area').removeClass('d-none');
            $('#view-toggle-bar').removeClass('d-none');

            // Reset to chart view
            $('#view-toggle button[data-view="charts"]').click();

            renderDamagePeriod(res.damage_costs);
            renderSavingsPeriod(res.saved_costs);
            renderKind(res.kind_data);
            renderActivity(res.activity_data);
            renderDriver(res.driver_stats);
            renderVehicle(res.vehicle_stats);
            renderStatus(res.status_data);
            renderRecoverable(res.recoverable_data);
            renderVerwijtbaar(res.verwijtbaar_data);
            renderInjury(res.injury_data);
            renderOpposite(res.opposite_data);
            renderCostBreakdown(res.cost_breakdown_data);
            renderResolutionTime(res.avg_resolution);

            updateExportLink(company, sdate, edate, period);
        })
        .fail(function () {
            alert('Er is een fout opgetreden bij het ophalen van de statistieken.');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fa fa-bar-chart"></i> Toon statistieken');
        });
    });

    // ─── 1. Schadebedrag per periode ─────────────────────────────────────────
    function renderDamagePeriod(data) {
        const labels = data.map(r => r.period_label);
        const values = data.map(r => parseFloat(r.damage_costs || 0));

        makeOrReplace('chart_damage_period', {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Schadebedrag (€)', data: values, backgroundColor: BLUE, borderColor: BLUE, borderWidth: 1 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } } },
            },
        });

        const rows = data.map(r => [r.period_label, r.claim_count, euro(r.damage_costs)]);
        $('#table_damage_period').html(buildTable(['Periode', 'Aantal claims', 'Schadebedrag'], rows));
    }

    // ─── 2. Besparing per periode ─────────────────────────────────────────────
    function renderSavingsPeriod(data) {
        const labels = data.map(r => r.period_label);
        const values = data.map(r => parseFloat(r.saved_costs || 0));

        makeOrReplace('chart_savings_period', {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Besparing (€)', data: values, backgroundColor: GREEN, borderColor: GREEN, borderWidth: 1 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } } },
            },
        });

        const rows = data.map(r => [r.period_label, euro(r.saved_costs)]);
        $('#table_savings_period').html(buildTable(['Periode', 'Besparing'], rows));
    }

    // ─── 3. Schade per soort ──────────────────────────────────────────────────
    function renderKind(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.claim_count);
        const costs  = data.map(r => parseFloat(r.damage_costs || 0));

        makeOrReplace('chart_kind', {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Aantal claims', data: counts, backgroundColor: BLUE, yAxisID: 'y' },
                    { label: 'Schadebedrag (€)', data: costs, backgroundColor: ORANGE, yAxisID: 'y1' },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Aantal' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } },
                },
            },
        });

        const rows = data.map(r => [r.label, r.claim_count, euro(r.damage_costs)]);
        $('#table_kind').html(buildTable(['Soort', 'Aantal claims', 'Schadebedrag'], rows));
    }

    // ─── 4. Activiteit (transport only) ───────────────────────────────────────
    function renderActivity(data) {
        if (!data || data.length === 0) {
            $('#chart_activity').closest('.card-body').html('<p class="text-muted text-center mt-4">Geen transportschade-data beschikbaar voor deze periode.</p>');
            return;
        }
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.claim_count);
        const costs  = data.map(r => parseFloat(r.damage_costs || 0));

        makeOrReplace('chart_activity', {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Aantal claims', data: counts, backgroundColor: TEAL, yAxisID: 'y' },
                    { label: 'Schadebedrag (€)', data: costs, backgroundColor: ORANGE, yAxisID: 'y1' },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero: true },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } },
                },
            },
        });

        const rows = data.map(r => [r.label, r.claim_count, euro(r.damage_costs)]);
        $('#table_activity').html(buildTable(['Activiteit', 'Aantal claims', 'Schadebedrag'], rows));
    }

    // ─── 5. Per medewerker (driver) ───────────────────────────────────────────
    function renderDriver(data) {
        if (!data || data.length === 0) {
            $('#chart_driver').closest('.card-body').html('<p class="text-muted text-center mt-4">Geen medewerkerdata beschikbaar voor deze periode.</p>');
            return;
        }
        const labels = data.map(r => r.driver_name);
        const counts = data.map(r => r.claim_count);
        const costs  = data.map(r => parseFloat(r.damage_costs || 0));

        makeOrReplace('chart_driver', {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Aantal claims', data: counts, backgroundColor: PURPLE, yAxisID: 'y' },
                    { label: 'Schadebedrag (€)', data: costs, backgroundColor: RED, yAxisID: 'y1' },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero: true },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } },
                },
            },
        });

        const rows = data.map(r => [r.driver_name, r.claim_count, euro(r.damage_costs)]);
        $('#table_driver').html(buildTable(['Medewerker', 'Aantal claims', 'Schadebedrag'], rows));
    }

    // ─── 6. Per voertuig ──────────────────────────────────────────────────────
    function renderVehicle(data) {
        if (!data || data.length === 0) {
            $('#chart_vehicle').closest('.card-body').html('<p class="text-muted text-center mt-4">Geen voertuigdata beschikbaar voor deze periode.</p>');
            return;
        }
        const labels = data.map(r => r.vehicle_name);
        const counts = data.map(r => r.claim_count);
        const costs  = data.map(r => parseFloat(r.damage_costs || 0));

        makeOrReplace('chart_vehicle', {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Aantal claims', data: counts, backgroundColor: TEAL, yAxisID: 'y' },
                    { label: 'Schadebedrag (€)', data: costs, backgroundColor: BLUE, yAxisID: 'y1' },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero: true },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } },
                },
            },
        });

        const rows = data.map(r => [r.vehicle_name, r.claim_count, euro(r.damage_costs)]);
        $('#table_vehicle').html(buildTable(['Voertuig', 'Aantal claims', 'Schadebedrag'], rows));
    }

    // ─── 7. Claims status ─────────────────────────────────────────────────────
    function renderStatus(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.count);

        makeOrReplace('chart_status', {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: counts, backgroundColor: [BLUE, GREEN, RED], hoverOffset: 6 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const rows = data.map(r => [r.label, r.count]);
        $('#table_status').html(buildTable(['Status', 'Aantal'], rows));
    }

    // ─── 8. Verhaalbaarheid ───────────────────────────────────────────────────
    function renderRecoverable(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.count);

        makeOrReplace('chart_recoverable', {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: counts, backgroundColor: [GREEN, YELLOW, RED, GREY], hoverOffset: 6 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const rows = data.map(r => [r.label, r.count]);
        $('#table_recoverable').html(buildTable(['Categorie', 'Aantal'], rows));
    }

    // ─── 9. Verwijtbaarheid ───────────────────────────────────────────────────
    function renderVerwijtbaar(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.count);

        makeOrReplace('chart_verwijtbaar', {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: counts, backgroundColor: [RED, GREEN], hoverOffset: 6 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const rows = data.map(r => [r.label, r.count]);
        $('#table_verwijtbaar').html(buildTable(['Categorie', 'Aantal'], rows));
    }

    // ─── 10. Letselclaims ─────────────────────────────────────────────────────
    function renderInjury(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.count);

        makeOrReplace('chart_injury', {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: counts, backgroundColor: [RED, BLUE, GREY], hoverOffset: 6 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const rows = data.map(r => [r.label, r.count]);
        $('#table_injury').html(buildTable(['Categorie', 'Aantal'], rows));
    }

    // ─── 11. Tegenpartij type ─────────────────────────────────────────────────
    function renderOpposite(data) {
        const labels = data.map(r => r.label);
        const counts = data.map(r => r.count);

        makeOrReplace('chart_opposite', {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: counts, backgroundColor: PALETTE, hoverOffset: 6 }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const rows = data.map(r => [r.label, r.count]);
        $('#table_opposite').html(buildTable(['Type', 'Aantal'], rows));
    }

    // ─── 12. Kostensamenstelling ──────────────────────────────────────────────
    function renderCostBreakdown(data) {
        const labels = data.map(r => r.label);
        const amounts = data.map(r => parseFloat(r.amount || 0));

        makeOrReplace('chart_cost_breakdown', {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Bedrag (€)',
                    data: amounts,
                    backgroundColor: PALETTE,
                    borderColor: PALETTE,
                    borderWidth: 1,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { callback: v => '€ ' + v.toLocaleString('nl-NL') } },
                },
            },
        });

        const rows = data.map(r => [r.label, euro(r.amount)]);
        $('#table_cost_breakdown').html(buildTable(['Kostenpost', 'Bedrag'], rows));
    }

    // ─── 13. Gemiddelde afhandelingstijd ──────────────────────────────────────
    function renderResolutionTime(data) {
        if (!data || data.length === 0) {
            $('#chart_resolution_time').closest('.card-body').html('<p class="text-muted text-center mt-4">Geen afgesloten claims in deze periode.</p>');
            return;
        }
        const labels = data.map(r => r.period_label);
        const days   = data.map(r => parseFloat(r.avg_days || 0));
        const counts = data.map(r => r.claim_count);

        makeOrReplace('chart_resolution_time', {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Gem. dagen', data: days, backgroundColor: PURPLE, borderColor: PURPLE, borderWidth: 1, yAxisID: 'y' },
                    { label: 'Aantal afgesloten', data: counts, type: 'line', backgroundColor: GREEN, borderColor: GREEN, pointRadius: 4, yAxisID: 'y1' },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Gem. dagen' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Aantal' } },
                },
            },
        });

        const rows = data.map(r => [r.period_label, r.avg_days + ' dagen', r.claim_count]);
        $('#table_resolution_time').html(buildTable(['Periode', 'Gemiddelde (dagen)', 'Afgesloten claims'], rows));
    }


});
