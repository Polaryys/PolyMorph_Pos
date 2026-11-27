import './bootstrap';

import Alpine from 'alpinejs';
import { ventasCarrito } from './ventas';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('ventasCarrito', ventasCarrito);

Alpine.start();

function inicializarGraficoMetodosPago() {
    const canvas = document.getElementById('graficoMetodosPago');

    if (!canvas) return;

    // Evitar instancias duplicadas
    Chart.getChart(canvas)?.destroy();

    const labels = JSON.parse(canvas.dataset.labels);
    const valores = JSON.parse(canvas.dataset.valores);

    const estilos = getComputedStyle(canvas);
    const superficie = estilos.getPropertyValue('--app-surface').trim();
    const textoSecundario = estilos.getPropertyValue('--app-text-secondary').trim();

    new Chart(canvas, {
        type: 'doughnut',

        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: [
                    '#4f46e5',
                    '#0ea5e9',
                    '#10b981',
                    '#f59e0b',
                    '#ec4899',
                    '#8b5cf6',
                    '#64748b',
                ],
                borderWidth: 2,
                borderColor: superficie || '#ffffff',
                hoverOffset: 6,
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 18,
                        color: textoSecundario || '#64748b',
                        font: {
                            family: 'Figtree',
                            size: 12,
                        },
                    }
                },

                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const total = context.dataset.data
                                .reduce((a, b) => a + Number(b), 0);

                            const valor = Number(context.raw);
                            const porcentaje = total > 0
                                ? (valor / total * 100).toFixed(1)
                                : 0;

                            return ` Q ${valor.toFixed(2)} (${porcentaje}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Primera carga
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarGraficoMetodosPago);
} else {
    inicializarGraficoMetodosPago();
}

// Navegaciones posteriores con wire:navigate
document.addEventListener('livewire:navigated', inicializarGraficoMetodosPago);

function inicializarGraficoResumenSemanal() {
    const canvas = document.getElementById('graficoResumenSemanal');

    if (!canvas) return;

    // Evitar duplicar instancias al navegar.
    Chart.getChart(canvas)?.destroy();

    const labels = JSON.parse(canvas.dataset.labels);
    const ventas = JSON.parse(canvas.dataset.ventas);
    const utilidad = JSON.parse(canvas.dataset.utilidad);

    const estilos = getComputedStyle(document.documentElement);

    const texto = estilos.getPropertyValue('--app-text-secondary').trim();
    const borde = estilos.getPropertyValue('--app-border').trim();

    const formatoMoneda = new Intl.NumberFormat('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    new Chart(canvas, {
        type: 'line',

        data: {
            labels,

            datasets: [
                {
                    label: 'Ventas netas',
                    data: ventas,
                    borderColor: '#6366f1',
                    backgroundColor: '#6366f1',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Utilidad bruta',
                    data: utilidad,
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false,
            },

            plugins: {
                legend: {
                    position: 'bottom',

                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        color: texto || '#64748b',

                        font: {
                            family: 'Figtree',
                            size: 12,
                        },
                    },
                },

                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return `${context.dataset.label}: Q ${formatoMoneda.format(context.parsed.y)}`;
                        },
                    },
                },
            },

            scales: {
                x: {
                    grid: {
                        display: false,
                    },

                    ticks: {
                        color: texto || '#64748b',
                    },
                },

                y: {
                    beginAtZero: true,

                    grid: {
                        color: borde || '#e2e8f0',
                    },

                    ticks: {
                        color: texto || '#64748b',

                        callback: (valor) => {
                            return `Q ${formatoMoneda.format(valor)}`;
                        },
                    },
                },
            },
        },
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarGraficoResumenSemanal);
} else {
    inicializarGraficoResumenSemanal();
}

// Navegaciones posteriores.
document.addEventListener('livewire:navigated', inicializarGraficoResumenSemanal);